<?php 

if(!defined('ABSPATH')) die('Access Denied');

class TW_Shortcode
{
    private $shortcode_tag = 'tw_cities';
    private $latitude_name = 'cu_latitude';
    private $longitude_name = 'cu_longitude';
    private $retreived_date_metakey = '_cu_weatherdata_date_saved';
    private $weatherdata_metakey = '_cu_weatherdata';

    public function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    public function init(){
        wp_register_script('tw-shortcode-script', TWURL . '/assets/shortcode.js',['jquery'], null, []);
        wp_register_style('tw-shortcode', TWURL . '/assets/shortcode.css', [], null);
        wp_register_style('fontawesome-6', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css', [], '6.6.0');

        add_shortcode($this->shortcode_tag, [$this, 'callback']);
        
    }


    /**
     * Callback function for the shortcode
     * 
     * @return string HTML Output
     */
    public function callback(){

        wp_enqueue_script('jquery');
        wp_enqueue_script('tw-shortcode-script');
        wp_enqueue_style('tw-shortcode');
        wp_enqueue_style('fontawesome-6');

        $cities = $this->get_cities();
        
        ob_start();
        ?>
        <div class="tw-main">
            <div class="tw-section tw-search">
                <form id="tw-citysearch" action="" method="POST" onsubmit="return false;">
                    <div class="cu-formrow">
                        <?php wp_nonce_field( 'tw_search', 'tw_wpnonce' ) ?>
                        <input type="hidden" name="action" value="tw_search">
                        <input type="text" name="s" id="tw-searchinput"> <button type="submit" id="tw-citysearch-submit">Search</button>
                    </div>
                </form>
            </div>
            <div class="tw-section tw-citytablelist">
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
            </div>
        </div>
        <?php 
        return ob_get_clean();
    }

    /**
     * Get the list of Cities
     * 
     * @param string $s
     * 
     * @return array Array of Cities
     */
    public function get_cities($s = ''){
        global $wpdb;

        $sql = "SELECT a.ID, a.post_title, a.post_name, a.post_type, b.meta_value '{$this->latitude_name}', c.meta_value '{$this->longitude_name}' FROM {$wpdb->posts} a
                LEFT JOIN $wpdb->postmeta b on a.ID = b.post_id AND b.meta_key = '{$this->latitude_name}'
                LEFT JOIN $wpdb->postmeta c on a.ID = c.post_id AND c.meta_key = '{$this->longitude_name}'
                WHERE a.post_status = 'publish' AND a.post_type = '". TW_PT ."'";

        if(!empty($s)) {
            $sql .= " a.post_title like '$s'";
        }

        $query = $wpdb->prepare($sql);
        return $wpdb->get_results($query);
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

        // return 'Test'; // testing

        if( !metadata_exists( 'post', $post_id, $this->weatherdata_metakey ) ) {
            $get_weather = OWM_API::get_api($lat, $lon, true);
            $this->save_weatherdata($post_id, $get_weather);
            return $get_weather;
        }else {
            $date_retreived = get_post_meta($post_id, $this->retreived_date_metakey, true);
            if(empty($date_retreived)) {
                $get_weather = OWM_API::get_api($lat, $lon, true);
                $this->save_weatherdata($post_id, $get_weather);
                return $get_weather;
            }else{
                return get_post_meta($post_id, $this->weatherdata_metakey, true);
            }
        }
    }

    /**
     * Save Weatherdata
     * 
     * @param int $post_id
     * @param mixed $weatherdata
     */
    public function save_weatherdata( $post_id = 0, $weatherdata ){
        if($post_id == 0 || $weatherdata === false || empty( $weatherdata )) {
            return false;
        }

        add_post_meta($post_id, $this->weatherdata_metakey, $weatherdata);
        add_post_meta($post_id, $this->retreived_date_metakey, date('Y-m-d H:i:s'));
    }
}

new TW_Shortcode();