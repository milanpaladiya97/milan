(function ($) {

	$(document).ready(
		function () {
			$(document).on('change', '.es_visible', function () {
				if ($('.es_visible:checked').length >= 1) {
					$('.es_required').prop('disabled', false);
					$('.es_name_label').removeAttr('disabled');
				} else {
					$('.es_required').prop('disabled', true);
					$('.es_name_label').attr('disabled', 'disabled');
				}
			});
			$('.es_visible').change();

			$('#es-settings-tabs').tabs().addClass('ui-tabs-vertical ui-helper-clearfix');

			var defaultHeight = $('div#es-settings-tabs div#menu-tab-content div#tabs-1').height() + 30;
			$('div#es-settings-tabs div#menu-tab-listing ul').height(defaultHeight);

			// Set Tab Height
			$('.ui-tabs-anchor').click(function () {
				var tab = $(this).attr('href');
				$('#email_tabs_form').attr('action', tab);
				var tabHight = $('div#es-settings-tabs div#menu-tab-content div' + tab).height() + 30;
				$('div#es-settings-tabs div#menu-tab-listing ul').height(tabHight);
			});

			if (jQuery('.statusesselect').length) {
				var statusselect = jQuery('.statusesselect')[0].outerHTML;
			}

			if (jQuery('.groupsselect').length) {
				var groupselect = jQuery('.groupsselect')[0].outerHTML;
			}

			jQuery(".es-audience-view .bulkactions #bulk-action-selector-top").after(statusselect);
			jQuery(".es-audience-view .bulkactions #bulk-action-selector-top").after(groupselect);

			//jQuery(".es-audience-view .bulkactions #bulk-action-selector-bottom").after(statusselect);
			// jQuery(".es-audience-view .bulkactions #bulk-action-selector-bottom").after(groupselect);

			jQuery("#bulk-action-selector-top").change(function () {
				if (jQuery('option:selected', this).attr('value') == 'bulk_list_update' || jQuery('option:selected', this).attr('value') == 'bulk_list_add') {
					jQuery('.groupsselect').eq(1).show();
					jQuery('.statusesselect').eq(1).hide();
				} else if (jQuery('option:selected', this).attr('value') == 'bulk_status_update') {
					jQuery('.statusesselect').eq(1).show();
					jQuery('.groupsselect').eq(1).hide();
				} else {
					jQuery('.statusesselect').hide();
					jQuery('.groupsselect').hide();
				}
			});

			jQuery('.es-audience-view .tablenav.bottom #bulk-action-selector-bottom').hide();
			jQuery('.es-audience-view .tablenav.bottom #doaction2').hide();
			jQuery(document).on('change', "#base_template_id", function () {
				var img = jQuery('option:selected', this).data('img')
				jQuery('.es-templ-img').html(img);
			});

			//send test emails
			$(document).on('click', '#es-send-test', function (e) {
				e.preventDefault();
				var test_email = $('#es-test-email').val();
				var params = {};
				params.es_test_email = test_email;
				params.action = 'es_send_test_email';
				if (test_email) {
					$('#es-send-test').next('#spinner-image').show();
					jQuery.ajax({
						method: 'POST',
						url: ajaxurl,
						data: params,
						dataType: 'json',
						success: function (response) {
							if (response && typeof response.status !== 'undefined' && response.status === "success") {
								$('#es-send-test').parent().find('.helper').html('<span style="color:green">' + response.message + '</span>');
							}

							$('#es-send-test').next('#spinner-image').hide();
						},

						error: function(err) {
							$('#es-send-test').next('#spinner-image').hide();
						}
					});
				} else {
					confirm('Add test email ');
				}

			});
		});


	// Get the element with id="defaultOpen" and click on it


})(jQuery);

function checkDelete() {
	return confirm('Are you sure?');
}
