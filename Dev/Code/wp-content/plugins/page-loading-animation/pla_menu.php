<?php

add_action( 'admin_menu', 'pla_menu_register' );

function pla_menu_register() {

	add_menu_page (
		'Preloader Animation',		  // Page Title
		'Preloader Animation',         // Menu Title
		'manage_options',                 // Capability
		'page-loading-animation-options', // Menu Slug
		'pla_all_fields_show',		  	  // Call back Function            
		'dashicons-image-rotate',         // Icon
		'8'                               // Positions
	);
}


// PLA Callback Function
function pla_all_fields() {


	//*****************  Animation Control Section  ********************

	add_settings_section(
		'pla_section0',					// ID of section
		'Animation Control',			// Title
		null,							// Call Back Function
		'pla-animation-ctrl-options'	// Page
	);

	add_settings_field(
		'pla_animation_choose',			// ID
		'Animation Choose',				// Title
		'pla_animation_choose',			// Call Back Function
		'pla-animation-ctrl-options',   // page
		'pla_section0'					// Section
	);

	add_settings_field(
		'pla_animation_upload',			// ID
		'',								// Title
		'pla_animation_upload',			// Call Back Function
		'pla-animation-ctrl-options',   // page
		'pla_section0'					// Section
	);


	register_setting('pla_section0','pla_animation_upload');
	register_setting('pla_section0','pla_animation_choose');


	//*****************  Animation Background Section  ********************

	add_settings_section(
		'pla_section1',					// ID of section
		'Animation Background (Only for Custom Image)',			// Title
		null,							// Call Back Function
		'pla-animation-bg-options'		// Page
	);

	add_settings_field(
		'pla_animation_bg_color',		// ID
		'Animation Background Color',	// Title
		'pla_animation_bg_color',		// Call Back Function
		'pla-animation-bg-options',     // page
		'pla_section1'					// Section
	);

	register_setting('pla_section1','pla_animation_bg_color');

}

add_action('admin_init','pla_all_fields');



//*****************  Section Call Back  ********************

function pla_all_fields_show() {
	?>
		
		<div id="tabs2">
			<ul>
				<li><a href="#tabs-1" title="">Animation Control</a></li>
				<li><a href="#tabs-2" title="">Background</a></li>
			</ul>
			<div id="tabs_container">
				<div id="tabs-1" style="height: 300px">
					<form action="options.php" method="post">
	                	<?php
	                		settings_fields('pla_section0');
	                		do_settings_sections('pla-animation-ctrl-options');
	                		submit_button();
	                	?>
	                </form>
				</div>
				<div id="tabs-2">
					<form action="options.php" method="post">
	                	<?php
	                		settings_fields('pla_section1');
	                		do_settings_sections('pla-animation-bg-options');
	                		submit_button();
	                	?>
	                </form>
				</div>
			</div><!--End tabs container-->			
		</div><!--End tabs2-->



		<head>
			<link rel="stylesheet" href="<?php echo plugins_url( 'assets/css/pla_menu_styles.css', __FILE__ ); ?>">
		</head>
		
		<script src="<?php echo plugins_url( 'assets/js/tabulous.js', __FILE__ ); ?>"></script>

		<script type="text/javascript">
			(function ($) {

				$('#tabs2').tabulous({
					effect: 'slideLeft'
				});


				$('select[name=pla_animation_choose]').on('change', function() {
				    if($("#show").is(":selected")){
				    	$('.show').show();
				    }else{
				    	$('.show').hide();
				    }
				 });


				jQuery(function(){
					jQuery("#pla_animation_upload").on("click", function(){
						var images = wp.media({
							title: "Upload Image",
							multiple: false
						}).open().on("select", function(){
							var html = '';
							var uploaded_images = images.state().get("selection");
							var fiels = uploaded_images.toJSON();
							jQuery.each(fiels, function (index, item){
								html += item.url +",";
							});

							jQuery("#pla_up_img").val(html);

							var uploaded_images = images.state().get("selection").first();
							var selectedImages = uploaded_images.toJSON();

							jQuery("#pla_animation_pic_select").attr("src",selectedImages.url);
						});
					});
				});

			})(jQuery);    
		</script>

	<?php
}

?>