<?php

/**
 * Plugin Name: Memberpress Coding Challenge 
 * Plugin URI:  
 * Description: Memberpress Coding Challenge For Jerry Dai
 * Version:     1.0.0
 * Author:      SOD
 * Text Domain: memberpress-sod-cha
 *
 * @package 
 */

define('MEMBERPRESS_SOD_CHA_VERSION', '1.0.0');
define('MEMBERPRESS_SOD_CHA_PATH', plugin_dir_path(__FILE__));
define('MEMBERPRESS_SOD_CHA_URL', plugins_url('', __FILE__));

function memberpress_sod_challenge()
{
	require_once MEMBERPRESS_SOD_CHA_PATH . '/includes/init.php';
}

memberpress_sod_challenge();
