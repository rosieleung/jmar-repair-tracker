<?php
defined( 'ABSPATH' ) || exit;

// [jmar_repair_tracker]
function jmrt_repair_tracker_fun() {
	
	$password = get_option( 'jmrt_password', '' );
	
	if ( ! $password ) {
		return '<p>Error: Please enter the "password" parameter to access this API.</p>';
	}
	
	ob_start();
	?>
	<form action="#" method="POST" id="jmar-repair-tracker" class="jmar-repair-tracker">
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( "jmar_repair_tracker" ); ?>" />
		<input type="text" name="vehiclereg" placeholder="Vehicle Reg #" />
		<input type="submit" value="Track Repair" />
	</form>
	<?php
	return ob_get_clean();
}

add_shortcode( 'jmar_repair_tracker', 'jmrt_repair_tracker_fun' );


function jmrt_lookup_vehicle() {
	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		jmrt_error_and_die( "Invalid security token." );
	}
	
	check_ajax_referer( 'jmar_repair_tracker' );
	
	$password = get_option( 'jmrt_password', '' );
	if ( ! $password ) {
		jmrt_error_and_die( "Cannot access the API (error: missing password). Please notify the website administrator." );
	}
	
	$userpw = urlencode( $password );
	
	$vehicle = $_POST['vehicle'];
	
	$is_valid_vehicle = preg_match( '/(?<Current>^[A-Z]{2}[0-9]{2}[A-Z]{3}$)|(?<Prefix>^[A-Z][0-9]{1,3}[A-Z]{3}$)|(?<Suffix>^[A-Z]{3}[0-9]{1,3}[A-Z]$)|(?<DatelessLongNumberPrefix>^[0-9]{1,4}[A-Z]{1,2}$)|(?<DatelessShortNumberPrefix>^[0-9]{1,3}[A-Z]{1,3}$)|(?<DatelessLongNumberSuffix>^[A-Z]{1,2}[0-9]{1,4}$)|(?<DatelessShortNumberSufix>^[A-Z]{1,3}[0-9]{1,3}$)|(?<DatelessNorthernIreland>^[A-Z]{1,3}[0-9]{1,4}$)/', $vehicle );
	if ( ! $is_valid_vehicle ) {
		jmrt_error_and_die( "Invalid vehicle. Please double-check your registration number or contact the website administrator." );
	}
	
	$url = 'https://tcruk.uk/bodyshopsystems/Service/VehicleStatus/VehicleStatus.svc/GetVehicleStatus/100/' . $userpw . '/' . $vehicle;
	$str = file_get_contents( $url );
	
	if ( ! $str || ! strlen( $str ) ) {
		jmrt_error_and_die( "Could not load the vehicle information (error: could not contact API). Please double-check your registration number or contact the website administrator." );
	}
	
	$json = json_decode( $str, true );
	
	if ( ! $json ) {
		jmrt_error_and_die( "Could not load the vehicle information (error: invalid JSON). Please double-check your registration number or contact the website administrator." );
	}
	
	if ( $json["Exception"] ) {
		jmrt_error_and_die( $json["Exception"] );
	}
	
	if ( $ecd = $json['EcdDate'] ) {
		preg_match( '/(.+)( [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})/', $ecd, $matches );
		$ecd = $matches[1];
	}
	
	?>
	<h2>Vehicle Information</h2>
	<p>
		<span>Vehicle Make:</span> <?php echo $json['VehicleMake'] ?: 'N/A'; ?><br>
		<span>Vehicle Model:</span> <?php echo $json['VehicleModel'] ?: 'N/A'; ?><br>
		<span>Registration Number:</span> <?php echo $json['RegNumber'] ?: 'N/A'; ?>
	</p>
	<h2>Repair Status</h2>
	<p>
		<span>Status:</span> <?php echo $json['VehicleStatus'] ?: 'N/A'; ?><br>
		<span>Estimated Completion Date:</span> <?php echo $ecd ?: 'N/A'; ?><br>
		<span>Notes:</span> <?php echo $json['VehicleStatussNotes'] ?: 'N/A'; ?>
	</p>
	<?php
	
	die();
}

add_action( 'wp_ajax_jmrt_lookup_vehicle', 'jmrt_lookup_vehicle' );
add_action( 'wp_ajax_nopriv_jmrt_lookup_vehicle', 'jmrt_lookup_vehicle' );

function jmrt_error_and_die( $msg ) {
	echo '<h2>Error</h2>';
	echo '<p>' . $msg . '</p>';
	die();
}