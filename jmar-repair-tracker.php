<?php
/*
Plugin Name: JMAR Repair Tracker
Author:      Rosie Leung
Description: Adds the shortcode [jmar_repair_tracker] which allows users to fetch repair updates for a given vehicle registration number.
Version:     1.0.1
Author URI:  https://rosieleung.com/
*/

defined( 'ABSPATH' ) || exit;

define( 'JMRT_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'JMRT_PATH', dirname( __FILE__ ) );
define( 'JMRT_VERSION', '1.0.1' );

function jmrt_init_plugin() {
	include_once( JMRT_PATH . '/includes/jmrt.php' );
	include_once( JMRT_PATH . '/includes/jmrt-dashboard.php' );
	include_once( JMRT_PATH . '/includes/enqueue.php' );
}

add_action( 'plugins_loaded', 'jmrt_init_plugin' );