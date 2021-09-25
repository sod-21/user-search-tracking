<?php

namespace memberpress\sod;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helper {

    /**
     * API URL
     */

    const API = "https://cspf-dev-challenge.herokuapp.com/";

    /**
     * Time Interval
     * maybe it's changable
     */
    const TimeInterval = 1;

    /**
     * transient key
     */
    const TRANSIENT_KEY = 'memberpress_sod_data';

    /**
     * get data
     */
    
    public static function get_data( $is_refresh = false ) {
        try {

            if ( !$is_refresh ) {
                $res = get_transient( self::TRANSIENT_KEY );

                if ( $res ) {
                    return $res;
                }
            }

            $response = wp_remote_get( self::API );

            if ( is_array( $response ) && ! is_wp_error( $response ) ) {

                $body = $response['body'];
                $res = json_decode( $body );

                set_transient( self::TRANSIENT_KEY, $res, self::TimeInterval * 3600 );            
            }

        } catch ( Exception $ex ) {
            $res = false;
        }
        
        return $res;
    }
}
