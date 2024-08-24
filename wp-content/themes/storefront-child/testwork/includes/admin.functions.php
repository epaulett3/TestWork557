<?php 

if(!defined('ABSPATH')) die('Access Denied');

class TWAdmin
{
    private $posttype = [
        'singular' => 'City',
        'plural' => 'Cities',
        'name' => 'city'
    ];

    private $tax = [
        'singular' => 'Country',
        'plural' => 'Countries',
        'name' => 'cu_countries'
    ];
    

    public function __construct()
    {
        add_action('init', [$this, 'create_customposttype'], 0);
        add_action('init', [$this, 'create_customtaxonomy'], 0);
    }
    
    /**
     * Create Custom Post Type
     * 
     * @return [type]
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
            'all_items'          => __( 'All ' . $this->posttype['singular'], TWTXTDOMAIN ),
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
     * @return [type]
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

    


}

new TWAdmin();