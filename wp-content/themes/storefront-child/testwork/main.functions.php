<?php 

if(!defined('ABSPATH')) die('Access Denied');

define( 'TWPATH', __DIR__ );
define( 'TWURL', get_stylesheet_directory_uri() . '/testwork' );
define( 'TWTXTDOMAIN', 'testwork');
define('TW_TAX', 'cu_countries');
define( 'TW_PT', 'city');
define( 'TW_TEMP_UNIT', '&deg;C' );
/**
 * Main Function
 */
class TestWork
{
    private static $instance;

    
    private function __construct(){
        
        // include all functions
        include_once( TWPATH . '/includes/openweathermapapi.functions.php' );
        
        include_once( TWPATH . '/includes/admin.functions.php' );
        include_once( TWPATH . '/includes/shortcodewidget.function.php' );
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