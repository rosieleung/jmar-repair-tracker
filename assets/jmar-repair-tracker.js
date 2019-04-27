jQuery(function () {
	init_jmar_repair_tracker();
});


function init_jmar_repair_tracker() {
	let $jmrt = jQuery('#jmar-repair-tracker');
	if ( !$jmrt.length ) return;

	let $jmrt_results = jQuery('<div class="jmrt-results"></div>');
	$jmrt.after($jmrt_results);

	let $submit = $jmrt.find("input[type='submit']");

	$submit.click(function ( e ) {
		e.preventDefault();

		if ( jQuery(this).hasClass("sending") ) {
			return;
		}

		let nonce = $jmrt.find("input[name='nonce']").val();
		let vehicle = $jmrt.find("input[name='vehiclereg']").val().toUpperCase().trim();
		let pattern = new RegExp("(?<Current>^[A-Z]{2}[0-9]{2}[A-Z]{3}$)|(?<Prefix>^[A-Z][0-9]{1,3}[A-Z]{3}$)|(?<Suffix>^[A-Z]{3}[0-9]{1,3}[A-Z]$)|(?<DatelessLongNumberPrefix>^[0-9]{1,4}[A-Z]{1,2}$)|(?<DatelessShortNumberPrefix>^[0-9]{1,3}[A-Z]{1,3}$)|(?<DatelessLongNumberSuffix>^[A-Z]{1,2}[0-9]{1,4}$)|(?<DatelessShortNumberSufix>^[A-Z]{1,3}[0-9]{1,3}$)|(?<DatelessNorthernIreland>^[A-Z]{1,3}[0-9]{1,4}$)");

		if ( !vehicle.length ) {
			$jmrt_results.html("<p><strong>Error:</strong> Please enter your vehicle registration number.");
			return;
		}
		if ( !pattern.test(vehicle) ) {
			$jmrt_results.html("<p><strong>Error:</strong> Invalid vehicle registration number.");
			return;
		}
		if ( !nonce.length ) {
			$jmrt_results.html("<p><strong>Error:</strong> Nonce not entered. Please notify the site admin.");
			return;
		}

		jQuery.ajax({
			url: jmar.ajax_url,
			type: 'post',
			data: {
				action: 'jmrt_lookup_vehicle',
				_ajax_nonce: nonce,
				vehicle: vehicle
			},
			beforeSend: function () {
				$submit.addClass("sending").val("Searching…");
				$jmrt_results.html("");
			},
			success: function ( response ) {
				$submit.removeClass("sending").val("Track Repair");
				$jmrt_results.html(response);
			},
			error: function ( xhr, status, error ) {
				$submit.removeClass("sending").val("Track Repair");
				$jmrt_results.html(xhr.responseText);
			}
		});
	});
}
