<?php 

if(!defined('ABSPATH')) die('Access Denied');

class TWAdmin
{
    // declare custom post type variables
    private $posttype = [
        'singular' => 'City',
        'plural' => 'Cities',
        'name' => TW_TAX
    ];

    // declare taxonomy variables
    private $tax = [
        'singular' => 'Country',
        'plural' => 'Countries',
        'name' => TW_TAX
    ];

    // declare custom field variables
    private $customfields = [
        [
            'name' => 'cu_latitude',
            'id' => 'cu-latitude',
            'title' => 'Latitude',
        ],
        [
            'name' => 'cu_longitude',
            'id' => 'cu-longitude',
            'title' => 'Longitude',
        ]
    ];
    

    public function __construct()
    {
        add_action('init', [$this, 'create_customposttype'], 0); // function for creating custom post type called
        add_action('init', [$this, 'create_customtaxonomy'], 0); // function for creating custom taxonomy
        add_action('init', [$this, 'init_metabox'], 0); // function for adding metaboxes to the custom post type edit screen

        // save customfields function
        add_action( 'save_post', [$this, 'save_customfields'] );
        add_action( 'new_to_publish', [$this, 'save_customfields'] );

        // add_action( 'wp_footer', [$this, 'testfunction']);
    }
    
    /**
     * Create Custom Post Type
     * 
     */
    public function create_customposttype(){
        $labels = array(
            'name'               => _x( $this->posttype['singular'], 'post type general name', TWTXTDOMAIN ),
            'singular_name'      => _x( $this->posttype['name'], 'post type singular name', TWTXTDOMAIN ),
            'menu_name'          => _x( $this->posttype['plural'], 'admin menu', TWTXTDOMAIN ),
            'name_admin_bar'     => _x( $this->posttype['singular'], 'add new on admin bar', TWTXTDOMAIN ),
            'add_new'            => _x( 'Add New', $this->posttype['name'], TWTXTDOMAIN ),
            'add_new_item'       => __( 'Add New '.$this->posttype['singular'], TWTXTDOMAIN ),
            'new_item'           => __( 'New ' . $this->posttype['singular'], TWTXTDOMAIN ),
            'edit_item'          => __( 'Edit ' . $this->posttype['singular'], TWTXTDOMAIN ),
            'view_item'          => __( 'View ' . $this->posttype['singular'], TWTXTDOMAIN ),
            'all_items'          => __( 'All ' . $this->posttype['plural'], TWTXTDOMAIN ),
            'search_items'       => __( 'Search ' . $this->posttype['singular'], TWTXTDOMAIN ),
            'not_found'          => __( 'No '. $this->posttype['singular'] .' found.', TWTXTDOMAIN ),
            'not_found_in_trash' => __( 'No '. $this->posttype['singular'] .' found in Trash.', TWTXTDOMAIN )
        );
           $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'Add New City on the country' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => $this->posttype['name'] ),
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 100,
            'menu_icon'          =>'dashicons-admin-site-alt3',
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail','comments','capabilities' ),
            'taxonomies'         => array( $this->tax['name'] )
        );
            register_post_type( $this->posttype['name'], $args );
    }
    
    /**
     * Create custom Taxonomy
     * 
     */
    public function create_customtaxonomy(){
        $singular = $this->tax['singular'];
        $plural = $this->tax['plural'];
        $labels = array(
            'name' => _x( $plural, TWTXTDOMAIN ),
            'singular_name' => _x( $singular, TWTXTDOMAIN ),
            'search_items' =>  __( 'Search ' . $plural ),
            'all_items' => __( 'All ' . $plural ),
            'parent_item' => __( 'Parent ' . $singular ),
            'parent_item_colon' => __( "Parent $singular:" ),
            'edit_item' => __( 'Edit ' . $singular ), 
            'update_item' => __( 'Update ' . $singular ),
            'add_new_item' => __( 'Add New ' . $singular ),
            'new_item_name' => __( "New $singular Name" ),
            'menu_name' => __( $plural ),
          );
              
          register_taxonomy( $this->tax['name'], array($this->posttype['name']), array(
              'hierarchical' => true,
              'labels' => $labels,
              'show_ui' => true,
              'show_admin_column' => true,
              'query_var' => true,
              'rewrite' => array( 'slug' => $this->tax['name'] ),
          ));

    }

    public function init_metabox() {
        add_action('add_meta_boxes', [$this, 'add_metabox']);
    }

    /**
     * Add Metabox on the City Custom post type edit screen
     * 
     */
    public function add_metabox(){
        add_meta_box(
            'latitude_longitude',
            'Latitude and Longitude', 
            [$this, 'metabox_display'],
            $this->posttype['name'],
            'normal',
            'high'
        );
    }

    /**
     * Callback function for the add_meta_box function
     */
    public function metabox_display(){
        global $wpdb, $post;
        $postid = $post->ID;

        ob_start();
        wp_nonce_field($this->posttype['name'] . '_customfield', 'cu_wpnonce');
        ?>
        <div class="cu-form-group">
            <?php foreach( $this->customfields as $cu ): 
                $value = !empty(get_post_meta($postid, $cu['name'], true)) ? get_post_meta($postid, $cu['name'], true) : '';
                ?>
            <div class="cu-formrow">
                <div class="cu-formcol"><label for="<?php echo $cu['id'] ?>"><?php echo $cu['title'] ?></label></div>
                <div class="cu-formcol"><input type="text" name="<?php echo $cu['name'] ?>" id="<?php echo $cu['id'] ?>" value="<?php echo $value ?>" data-postid="<?php echo $postid ?>" ></div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php 
        echo ob_get_clean();
        
    }

    /**
     * Save the Custom Fields
     * 
     * @param int $post_id
     * 
     */
    public function save_customfields( $post_id ){
        global $wpdb;

        // verify nonce
        if(!isset($_POST['cu_wpnonce']) || !wp_verify_nonce($_POST['cu_wpnonce'], $this->posttype['name'] . '_customfield') ) {
            return 'invalid nonce';
        }

        // check autosave
        if( wp_is_post_autosave($post_id) ) return;
        

        // check for permissions
        if( $_POST['post_type'] == $this->posttype['name'] ) {
            if( !current_user_can('edit_page', $post_id) ) return 'cannot edit page';
        } elseif ( !current_user_can('edit_post', $post_id) ) {
            return 'cannot edit post';
        }


        
        // Loop through each customfields
        foreach( $this->customfields as $cu ) {
            // sanitized values first
            // $sanitized_value = sanitize_text_field($_POST[$cu['name']]);
            $sanitized_value = $_POST[ $cu['name'] ];
            $a[] = $sanitized_value;
            
            // add or update customfield to database
            if( ! metadata_exists( 'post', $post_id, $cu['name'] ) ) {
                add_post_meta( $post_id, $cu['name'], $sanitized_value);
            } else {
                update_post_meta( $post_id, $cu['name'], $sanitized_value );
            }
            
        }
        
    }

    public function testfunction(){
        $city = get_post(19);

        $taxonomy = get_the_terms($city->ID, $this->tax['name']);

        $get_weather = OWM_API::get_api($city->cu_latitude, $city->cu_longitude);

        $city_info = [
            'city_name' => $city->post_title,
            'country' => $taxonomy[0]->name,
            'latitude' => $city->cu_latitude,
            'longitude' => $city->cu_longitude,
        ];
        ?>
        <pre><?php var_dump($get_weather) ?></pre>
        <?php 
    }

}

new TWAdmin();