<?php 

if(!defined('ABSPATH')) die('Access Denied');

class OWM_API
{
    private static $_apikey = 'eb481724ea1648946307d95610c88a1e';
    private static $_api_url = 'https://api.openweathermap.org/data/2.5/weather';

    public static function get_api( $apikey = '',$api_url = '', $lat = '', $lon = ''){
        if(empty($lat) && empty($lon)) return false;
        if(empty($apikey)) $api_key = self::$_apikey;
        if(empty($api_url)) $api_url = self::$_api_url;

        try {
            $args = [
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ];
            $response = wp_remote_get($api_url, $args);
            if( !is_wp_error($response) && 200 === wp_remote_retrieve_response_code( $response ) ) {
                $responseBody = json_decode($response['body']);
                if(json_last_error() == JSON_ERROR_NONE){
                    $return = $responseBody;
                } 
            }
        } catch ( Exception $error ){
            return false;
        }

    }
}
