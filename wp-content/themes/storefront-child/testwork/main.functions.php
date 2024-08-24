<?php 

if(!defined('ABSPATH')) die('Access Denied');

define( 'TWPATH', get_stylesheet_directory() . '/testwork' );
define( 'TWTXTDOMAIN', 'testwork');
/**
 * Main Function
 */
class TestWork
{
    private static $instance;

    
    private function __construct(){
        
        // include all functions
        include_once( TWPATH . '/includes/openweathermapapi.functions.php' );
        include_once( TWPATH . '/includes/shortcodewidget.function.php' );

        include_once( TWPATH . '/includes/admin.functions.php' );
    }

    /**
     * get the class instance
     * 
     */
    public static function getInstance(){
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

TestWork::getInstance();