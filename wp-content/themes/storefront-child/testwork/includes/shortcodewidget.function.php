<?php 

if(!defined('ABSPATH')) die('Access Denied');

/**
 * Shortcode to list the Cities in a table with search
 * Whats Included:
 * - Register Shortcode
 * - Callback function for the Shortcode
 * - AJAX Callback function for the Search Form using AJAX
 * - Get Weather data
 * - Save Weather data to DB
 */
class TW_Shortcode
{
    private $shortcode_tag = 'tw_cities';
    private $latitude_name = 'cu_latitude';
    private $longitude_name = 'cu_longitude';
    private $retreived_date_metakey = '_cu_weatherdata_date_saved';
    private $weatherdata_metakey = '_cu_weatherdata';

    public function __construct()
    {
        // Add function to the init action hook
        add_action('init', [$this, 'init']);

        // ajax callback function
        add_action('wp_ajax_tw_search', [$this, 'tw_search_callback']);
        add_action('wp_ajax_nopriv_tw_search', [$this, 'tw_search_callback']);
    }

    /**
     * Initialize
     * 
     * @return [type]
     */
    public function init(){

        // Register Scripts and styles to be called later.
        wp_register_script('tw-shortcode-script', TWURL . '/assets/shortcode.js',['jquery'], null, []);
        wp_register_style('tw-shortcode', TWURL . '/assets/shortcode.css', [], null);
        wp_register_style('fontawesome-6', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css', [], '6.6.0');

        // Register the shortcode
        add_shortcode($this->shortcode_tag, [$this, 'callback']);
        
    }


    /**
     * Callback function for the shortcode
     * 
     * @return string HTML Output
     */
    public function callback(){

        // Enqueue scripts and styles only when the shortcode is used.
        wp_enqueue_script('jquery');
        wp_enqueue_script('tw-shortcode-script');
        wp_add_inline_script('tw-shortcode-script', 'const twjs = ' . json_encode( [
            'ajax_url' => admin_url('admin-ajax.php'),
        ] ), 'before');
        wp_enqueue_style('tw-shortcode');
        wp_enqueue_style('fontawesome-6');

        // get the cities
        $cities = $this->get_cities();
        
        // Start HTML Output
        ob_start();
        ?>
        <div class="tw-main">
            <div class="tw-section tw-search">
                <form id="tw-citysearch" action="" method="POST" onsubmit="return false;">
                    <div class="cu-formrow">
                        <?php wp_nonce_field( 'tw_search', 'tw_wpnonce' ) ?>
                        <input type="hidden" id="tw_action" name="action" value="tw_search">
                        <span class="cu-forminput"><input type="text" name="s" id="tw-searchinput"><button id="tw-inputclear" style="display: none;" title="Clear"><i class="fa-solid fa-xmark"></i></button></span> <button type="submit" id="tw-citysearch-submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                    </div>
                </form>
            </div>
            <div class="tw-section tw-citytablelist">
                <?php do_action('tw_citytable_before') ?>
                <table class="tw-table">
                    <thead>
                        <tr>
                            <th>Cities</th>
                            <th>Country</th>
                            <th>Coordinates (Latitude, Longitude) </th>
                            <th>Temperature</th>
                        </tr>
                    </thead>
                    <tbody id="tw-citylist">
                        <?php foreach($cities as $city): 
                            $country = get_the_terms($city->ID, TW_TAX);
                            ?>
                            <tr>
                                <td><?php echo $city->post_title ?></td>
                                <td><?php echo $country[0]->name ?></td>
                                <td><?php echo $city->cu_latitude . ', ' . $city->cu_longitude ?></td>
                                <td><?php $weather = $this->get_weather($city->ID, $city->cu_latitude, $city->cu_longitude); echo $weather['main']['temp'] . TW_TEMP_UNIT ?></td>
                            </tr>
                                
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php do_action('tw_citytable_after') ?>
            </div>
        </div>
        <?php 
        return ob_get_clean();
    }

    /**
     * Get the list of Cities. Query using global variable $wpdb
     * 
     * @param string $s
     * @param string $output Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
     * 
     * @return array Array of Cities
     */
    public function get_cities($s = '', $output = OBJECT){
        global $wpdb;

        $sql = "SELECT a.ID, a.post_title, a.post_name, a.post_type, b.meta_value '{$this->latitude_name}', c.meta_value '{$this->longitude_name}' FROM {$wpdb->posts} a
                LEFT JOIN $wpdb->postmeta b on a.ID = b.post_id AND b.meta_key = '{$this->latitude_name}'
                LEFT JOIN $wpdb->postmeta c on a.ID = c.post_id AND c.meta_key = '{$this->longitude_name}'
                WHERE a.post_status = 'publish' AND a.post_type = '". TW_PT ."'";

        if(!empty($s)) {
            $sql .= " AND a.post_title like '%{$s}%'";
        }

        $query = $wpdb->prepare($sql);
        return $wpdb->get_results($query, $output);
    }

    /**
     * Retreive the Weather Information including the Temperature
     * 
     * @param int $post_id
     * @param string $lat
     * @param string $lon
     * 
     * @return array Array of weather info including temperature
     */
    public function get_weather($post_id = 0, $lat = '', $lon = '') {
        if($post_id == 0 || empty($lat) || empty($lon) ) return false;

        if( !metadata_exists( 'post', $post_id, $this->weatherdata_metakey ) ) {
            $get_weather = OWM_API::get_api($lat, $lon, true);
            $this->save_weatherdata($post_id, $get_weather);
            return $get_weather;
        }else {
            $date_retreived = get_post_meta($post_id, $this->retreived_date_metakey, true);
            if(empty($date_retreived) || $this->check_weatherdata_last_saved($date_retreived) !== false) {
                $get_weather = OWM_API::get_api($lat, $lon, true);
                $this->save_weatherdata($post_id, $get_weather);
                return $get_weather;
            }else{
                return get_post_meta($post_id, $this->weatherdata_metakey, true);
            }
        }
    }

    /**
     * Save the Weather data to the DB. This will help reduce the API calls. default interval for next API call is 1 hour. Please check the check_weatherdata_last_saved() function below
     * 
     * @param int $post_id
     * @param mixed $weatherdata
     */
    public function save_weatherdata( $post_id = 0, $weatherdata ){
        if($post_id == 0 || $weatherdata === false || empty( $weatherdata )) {
            return false;
        }

        $metadata = [$this->weatherdata_metakey => $weatherdata, $this->retreived_date_metakey => date('Y-m-d H:i:s')];

        foreach($metadata as $metakey => $metavalue) {
            if(!metadata_exists('post', $post_id, $metakey)) {
                add_post_meta($post_id, $metakey, $metavalue);
            } else {
                update_post_meta($post_id, $metakey, $metavalue);
            }
        }
    }

    /**
     * Callback function for the AJAX. This is the callback function for the search form
     * 
     * @return json results in json format
     */
    public function tw_search_callback(){
        if( !isset($_REQUEST['wpnonce']) || !wp_verify_nonce( $_REQUEST['wpnonce'], 'tw_search' )  ) {
            return wp_send_json_error( ['error' => true, 'error_msg' => 'Invalid nonce'] );
        }

        $s = $_REQUEST['s'];
        if( !isset($s) || empty($s) ) {
            $city_result = $this->get_cities( '', ARRAY_A );
        }else{
            $city_result = $this->get_cities( sanitize_text_field($s), ARRAY_A );
        }


        for ($i=0; $i < count($city_result); $i++) {
            $city = $city_result[$i];
            $country = get_the_terms($city['ID'], TW_TAX);
            $city_result[$i]['country'] = $country[0]->name;
            $weather = $this->get_weather($city['ID'], $city['cu_latitude'], $city['cu_longitude']);
            if(!empty($weather) || $weather !== false) {
                $city_result[$i]['temp'] = $weather['main']['temp'] . TW_TEMP_UNIT;
            }
        }

        return wp_send_json_success($city_result);
    }

    /**
     * Check when is the weather data last saved on DB. This will check if the data should be updated or not. Default Interval is 1 Hour
     * 
     * @param string $date_last_retreived
     * @param int $interval 
     * 
     * @return bool 
     */
    public function check_weatherdata_last_saved( $date_last_retreived = '', $interval = 3600 ){
        if(empty($date_last_retreived)) return false;
        $checkdate = strtotime($date_last_retreived);
        $datenow = time();
        if( (($datenow - $checkdate) / $interval) >= 1 ) {
            return true;
        }else {
            return false;
        }

    }
}

new TW_Shortcode();