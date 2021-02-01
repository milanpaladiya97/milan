<?php

add_filter( 'ig_es_registered_settings', 'ig_es_add_upsale', 10, 2 );

// Add additional tab in Audience > Sync
add_filter('ig_es_sync_users_tabs', 'ig_es_add_comments_tab', 11, 1);

add_action( 'ig_es_sync_users_tabs_comments', 'ig_es_add_comments_tab_settings' );

function ig_es_add_upsale( $fields ) {

	$active_plugins = (array) get_option( 'active_plugins', array() );
	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	if ( ! ( in_array( 'email-subscribers-premium/email-subscribers-premium.php', $active_plugins ) || array_key_exists( 'email-subscribers-premium/email-subscribers-premium.php', $active_plugins ) ) ) {

		// Security settings
		$field_security['es_upsale_security'] = array(
			'id'   => 'ig_es_blocked_domains',
			'type' => 'html',
			'name' => '',
			'html' => '<div class="es-upsale-image" style=""><a target="_blank" href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=es_security_settings&utm_campaign=es_upsale"><img src="' . EMAIL_SUBSCRIBERS_URL . '/admin/images/es-captcha-2.png' . '"/></a></div>'
		);
		$fields['security_settings']          = array_merge( $fields['security_settings'], $field_security );

		// SMTP settings
		$field_smtp['es_upsale_smtp'] = array(
			'id'   => 'ig_es_blocked_domains',
			'type' => 'html',
			'name' => '<div class="es-smtp-label" style=""><a target="_blank" href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=es_security_settings&utm_campaign=es_upsale"><img src="' . EMAIL_SUBSCRIBERS_URL . '/admin/images/es-smtp-label.png' . '"/></a></div>',
			'html' => '<div class="es-upsale-image es-smtp-image" style=""><a target="_blank" href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=es_security_settings&utm_campaign=es_upsale"><img src="' . EMAIL_SUBSCRIBERS_URL . '/admin/images/es-smtp.png' . '"/></a></div>'
		);
		$fields['email_sending']      = array_merge( $fields['email_sending'], $field_smtp );

	}

	return $fields;
}

function ig_es_add_comments_tab( $tabs ) {
	$tabs['comments'] = array(
		'name' => __('Comments', 'email-subscribers')
	);
	return $tabs;
}

function ig_es_add_comments_tab_settings() {
	?> 
	<a target="_blank" href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=es_comment_upsale&utm_campaign=es_upsale">
		<img  src=" <?php echo EMAIL_SUBSCRIBERS_URL . '/admin/images/es-comments.png' ?> "/>
	</a>
	<?php
}



