;(function($) {
	// Gateway select change event
	$('.hide_class').hide();

    $('#cf7_sms_settings\\[sms_gateway\\]').on( 'change', function() {
		var self = $(this),
			value = self.val();
		$('.hide_class').hide();
		$('.'+value+'_wrapper').fadeIn();
	});

	// Trigger when a change occurs in gateway select box
    $('#cf7_sms_settings\\[sms_gateway\\]').trigger('change');

})(jQuery);
