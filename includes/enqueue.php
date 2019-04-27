<?php

defined( 'ABSPATH' ) || exit;

function jmrt_enqueue_scripts() {
	wp_enqueue_style( 'jmar-repair-tracker', JMRT_URL . '/assets/jmar-repair-tracker.css', array(), JMRT_VERSION );
	wp_enqueue_script( 'jmar-repair-tracker', JMRT_URL . '/assets/jmar-repair-tracker.js', array( 'jquery' ), JMRT_VERSION, true );
	wp_localize_script( 'jmar-repair-tracker', 'jmar', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
	) );
}

add_action( 'wp_enqueue_scripts', 'jmrt_enqueue_scripts' );
