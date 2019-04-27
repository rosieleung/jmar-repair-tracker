<?php

defined( 'ABSPATH' ) || exit;

// adds a settings link to the plugins list
function jmrt_add_settings_link( $links ) {
	array_unshift( $links, '<a href="options-general.php?page=jmrt-settings">Settings</a>' );
	
	return $links;
}

add_filter( "plugin_action_links_jm-repair-tracker/jmar-repair-tracker.php", 'jmrt_add_settings_link' );


// adds the settings page to the admin dashboard
function jmrt_register_options_page() {
	add_options_page(
		'Repair Tracker Settings',
		'Repair Tracker',
		'manage_options',
		'jmrt-settings',
		'jmrt_options_page'
	);
}

add_action( 'admin_menu', 'jmrt_register_options_page' );


// register the setting
function jmrt_register_settings() {
	register_setting(
		'jmrt_options_group',
		'jmrt_password',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	add_settings_section(
		'jmrt_settings',
		'',
		'jmrt_settings_section_fun',
		'options-general.php?page=jmrt-settings'
	);
	add_settings_field(
		'jmrt_password',
		'Password for API Access',
		'jmrt_settings_field_fun',
		'options-general.php?page=jmrt-settings',
		'jmrt_settings'
	);
}

add_action( 'admin_init', 'jmrt_register_settings' );

function jmrt_options_page() {
	?>
	<h1>Repair Tracker Settings</h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'jmrt_options_group' ); ?>
		<?php do_settings_sections( 'options-general.php?page=jmrt-settings' ); ?>
		<?php submit_button(); ?>
	</form>
	<?php
}

function jmrt_settings_field_fun() {
	echo "<input name='jmrt_password' size='20' type='text' value='" . get_option( 'jmrt_password', '' ) . "' />";
}

function jmrt_settings_section_fun() {
	?>
	<p><strong>Plugin usage:</strong> Fill out the password below and add the shortcode [jmar_repair_tracker] to a page.</p>
	<?php
	
}