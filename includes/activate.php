<?php
/**
 * Activate Plugin
 *
 */

namespace SodTrack;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {

	/**
	 * Initialize plugin class
	 */
	public function __construct() {	
		$this->add_tables();
	}
	
	

	/**
	 * Add tables
	 *
	 * @return void
	 */
	public function add_tables() {
		require_once SOD_TRACK_PATH . '/includes/track.php';
        Track::create_table();
	}
}
