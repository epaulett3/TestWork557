<?php

// add custom php functions below
include( get_stylesheet_directory() . '/testwork/main.functions.php' );

// creates a custom action hook and hook the storefront_page_content
add_action('cu_storefront_page', 'storefront_page_content', 10);


function storefront_header_container() {
    echo '<div class="cu-branding">';
}

