<?php
/**
 * Plugin Name: Sod Track
 * Plugin URI:  
 * Description: Track User Search Queries
 * Version:     1.0.0
 * Author:      SOD 
 * Text Domain: sod-track-search
 *
 * @package 
 */

define( 'SOD_TRACK_VERSION', '1.0.0' );
define( 'SOD_TRACK_PATH', plugin_dir_path( __FILE__ ) );
define( 'SOD_TRACK_URL', plugins_url( '', __FILE__ ) );
define( 'SOD_TRACK_PLUGIN_BASE', plugin_basename( __FILE__ ) );

/**
 * Activate Plugin
 */
function sod_track_activation() {
	require_once SOD_TRACK_PATH . '/includes/activate.php';
	new SodTrack\Activation();
}
register_activation_hook( __FILE__, 'sod_track_activation' );

// Includes files
require_once SOD_TRACK_PATH . '/includes/init.php';


function sodtrack() {
    return SodTrack\SodTrack::getInstance();
}

sodtrack();