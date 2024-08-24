<?php 

if(!defined('ABSPATH')) die('Access Denied');

class TW_Shortcode
{
    private $shortcode_tag = 'tw_cities';

    public function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    public function init(){
        add_shortcode($this->shortcode_tag, [$this, 'callback']);
    }


    public function callback(){

        ob_start();
        ?>
        <div class="tw-main">
            <div class="tw-section tw-search"></div>
            <div class="tw-section tw-citytable">
                
            </div>
        </div>
        <?php 
        return ob_get_clean();
    }
}

new TW_Shortcode();