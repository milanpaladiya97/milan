<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      4.0
 *
 * @package    Email_Subscribers
 * @subpackage Email_Subscribers/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines Shortcode
 *
 * @package    Email_Subscribers
 * @subpackage Email_Subscribers/public
 * @author     Your Name <email@example.com>
 */
class ES_Shortcode {

	public function __construct() {
	}

	public static function render_es_subscription_shortcode( $atts ) {
		ob_start();

		$atts = shortcode_atts( array(
			'namefield' => '',
			'desc'      => '',
			'group'     => ''
		), $atts, 'email-subscribers' );

		$data['name_visible'] = $atts['namefield'];
		$data['list_visible'] = 'no';
		$data['lists']        = array();
		$data['form_id']      = 0;
		$data['list']         = $atts['group'];
		$data['desc']         = $atts['desc'];

		self::render_form( $data );

		return ob_get_clean();
	}


	public static function render_es_form( $atts ) {
		ob_start();

		$atts = shortcode_atts( array(
			'id' => ''
		), $atts, 'email-subscribers-form' );

		$id = $atts['id'];

		if ( ! empty( $id ) ) {
			$form = ES_DB_Forms::get_form_by_id( $id );

			if ( $form ) {
				$form_data = ES_Forms_Table::get_form_data_from_body( $form );

				self::render_form( $form_data );
			}
		}

		return ob_get_clean();

	}

	// Hanadle Email Subscribers Group Selector Shortcode
	// Backward Compatibility
	public static function render_es_advanced_form( $atts ) {
		ob_start();

		$atts = shortcode_atts( array(
			'id' => ''
		), $atts, 'email-subscribers-advanced-form' );

		$af_id = $atts['id'];

		if ( ! empty( $af_id ) ) {
			$form = ES_DB_Forms::get_form_by_af_id( $af_id );
			if ( $form ) {
				$form_data = ES_Forms_Table::get_form_data_from_body( $form );

				self::render_form( $form_data );
			}
		}

		return ob_get_clean();
	}

	public static function render_form( $data ) {

		/**
		 * - Show name? -> Prepare HTML for name
		 * - Show email? -> Prepare HTML for email // Always true
		 * - Show lists? -> Preapre HTML for Lists list_ids
		 * - Hidden Field -> form_id,
		 *      list,
		 *      es_email_page,
		 *      es_email_page_url,
		 *      es-subscribe,
		 *      honeypot field
		 */
		// Compatibility for GDPR
		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		$show_name         = ! empty( $data['name_visible'] ) ? strtolower( $data['name_visible'] ) : false;
		$required_name     = ! empty( $data['name_required'] ) ? $data['name_required'] : false;
		$name_place_holder = ! empty( $data['name_label'] ) ? $data['name_label'] : '';

		$show_last_name         = ! empty( $data['last_name_visible'] ) ? strtolower( $data['last_name_visible'] ) : false;
		$required_last_name     = ! empty( $data['last_name_required'] ) ? $data['last_name_required'] : false;
		$last_name_place_holder = ! empty( $data['last_name_label'] ) ? $data['last_name_label'] : '';

		$email_label       = ! empty( $data['email_label'] ) ? $data['email_label'] : '';
		$button_label      = ! empty( $data['button_label'] ) ? $data['button_label'] : __( 'Subscribe', 'email-subscribers' );
		$show_list         = ! empty( $data['list_visible'] ) ? $data['list_visible'] : false;
		$list_ids          = ! empty( $data['lists'] ) ? $data['lists'] : array();
		$form_id           = ! empty( $data['form_id'] ) ? $data['form_id'] : 0;
		$list              = ! empty( $data['list'] ) ? $data['list'] : 0;
		$desc              = ! empty( $data['desc'] ) ? $data['desc'] : '';
		//replace total contact 
		$total_contacts = ES_DB_Contacts::count_active_subscribers_by_list_id();
		$desc           = str_replace( "{{TOTAL-CONTACTS}}", $total_contacts, $desc );

		$current_page     = get_the_ID();
		$current_page_url = get_the_permalink( get_the_ID() );

		$unique_id = time();
		$hp_style  = "position:absolute;top:-99999px;" . ( is_rtl() ? 'right' : 'left' ) . ":-99999px;z-index:-99;";
		$nonce     = wp_create_nonce( 'es-subscribe' );

		// Name
		$name_html = '';
		$last_name_html = '';

		?>
		<div class="set_column_es">
		<?php
		if ( ! empty( $show_name ) && 'no' !== $show_name ) {
			$name_label = ( 'yes' === $required_name ) ? __( 'First Name', 'email-subscribers' ) . ' * ' : __( 'First Name', 'email-subscribers' );
			$required   = ( 'yes' === $required_name ) ? 'required' : '';
			$name_html  = '<div class="es-field-wrap"><input type="text" name="name" class="es_name" placeholder="First Name *" title="Please enter valid value or remove extra spaces" pattern="[a-zA-Z][a-zA-Z0-9\s]*" placeholder="' . $name_place_holder . '" value="" ' . $required . '/></div>';
		}

		
			$last_name_label = ( 'yes' === $required_last_name ) ? __( 'Last Name', 'email-subscribers' ) . ' * ' : __( 'Last Name', 'email-subscribers' ). ' * ';
			$required_last_name_s   = ( 'yes' === $required_last_name ) ? 'required' : '';
			$last_name_html  = '<div class="es-field-wrap"><input type="text" name="last_name" placeholder="Last Name *" class="es_lastname" pattern="[a-zA-Z][a-zA-Z0-9\s]*"  title="Please enter valid value or remove extra spaces" placeholder="' . $last_name_place_holder . '" value="" required/></div>';
		
		?>
		</div>
		<?php

		// Lists
		if ( ! empty( $list_ids ) && $show_list ) {
			$lists_id_name_map = ES_DB_Lists::get_list_id_name_map();
			$list_html         = self::prepare_lists_checkboxes( $lists_id_name_map, $list_ids );
		} elseif ( ! empty( $list_ids ) && ! $show_list ) {
			$list_html = '';
			foreach ( $list_ids as $id ) {
				$list_html .= '<input type="hidden" name="lists[]" value="' . $id . '" />';
			}
		} elseif ( is_numeric( $list ) ) {
			$list_html = '<input type="hidden" name="lists[]" value="' . $list . '" />';
		} else {
			$list_data = ES_DB_Lists::get_list_by_name( $list );
			if ( empty( $list_data ) ) {
				$list_id = ES_DB_Lists::add_list( $list );
			} else {
				$list_id = $list_data['id'];
			}

			$list_html = '<input type="hidden" name="lists[]" value="' . $list_id . '" />';
		}

		// Form html
		$form_html = '<input type="hidden" name="form_id" value="' . $form_id . '" />';

		$email_html = '<div class="es-field-wrap"><input class="es_required_field es_txt_email" placeholder="Email *" type="email" name="email" value="" placeholder="' . $email_label . '" required/></div>';
		?>

        <div class="emaillist">
            <form action="#" method="post" class="es_subscription_form es_shortcode_form" id="es_subscription_form_<?php echo $unique_id; ?>" data-source="ig-es">
				<?php if ( $desc != "" ) { ?>
                    <div class="es_caption"><?php echo $desc; ?></div>
				<?php } ?>
				<?php echo $name_html; ?>
				<?php echo $last_name_html; ?>
				<?php echo $email_html; ?>
				<?php echo $list_html; ?>
				<?php echo $form_html; ?>

                <input type="hidden" name="es_email_page" value="<?php echo $current_page; ?>"/>
                <input type="hidden" name="es_email_page_url" value="<?php echo $current_page_url; ?>"/>
                <input type="hidden" name="status" value="Unconfirmed"/>
                <input type="hidden" name="es-subscribe" id="es-subscribe" value="<?php echo $nonce; ?>"/>
                <label style="<?php echo $hp_style; ?>"><input type="text" name="es_hp_<?php echo wp_create_nonce( 'es_hp' ); ?>" class="es_required_field" tabindex="-1" autocomplete="-1"/></label>
				<?php do_action( 'es_after_form_fields' ) ?>
				<?php if ( ( in_array( 'gdpr/gdpr.php', $active_plugins ) || array_key_exists( 'gdpr/gdpr.php', $active_plugins ) ) ) {
					echo GDPR::consent_checkboxes();
				} ?>
                <input type="submit" name="submit" class="es_subscription_form_submit es_submit_button es_textbox_button" id="es_subscription_form_submit_<?php echo $unique_id; ?>" value="<?php echo $button_label; ?>"/>


				<?php $spinner_image_path = plugin_dir_url( ES_PLUGIN_BASE_NAME ) . 'public/images/spinner.gif'; ?>
                <span class="es_spinner_image" id="spinner-image"><img src="<?php echo $spinner_image_path; ?>"/></span>

            </form>

            <span class="es_subscription_message success" id="es_subscription_message_<?php echo $unique_id; ?>"></span>
        </div>

		<?php
	}

	public static function prepare_lists_checkboxes( $lists, $list_ids = array(), $columns = 3, $selected_lists = array(), $contact_id = 0 ) {
		$lists_html = '<div><table><tr>';
		$i          = 0;
		foreach ( $lists as $list_id => $list_name ) {
			if ( $i != 0 && ( $i % $columns ) === 0 ) {
				$lists_html .= "</tr><tr>";
			}
			$status_span = '';
			if ( in_array( $list_id, $list_ids ) ) {
				if ( in_array( $list_id, $selected_lists ) ) {
					if ( ! empty( $contact_id ) ) {
						$list_contact_status_map = ES_DB_Lists_Contacts::get_list_contact_status_map( $contact_id );
						$status_span             = '<span class="es_list_contact_status ' . $list_contact_status_map[ $list_id ] . '" title="' . ucwords( $list_contact_status_map[ $list_id ] ) . '">';
					}
					$lists_html .= '<td>' . $status_span . '<label><input type="checkbox" name="lists[]" checked="checked" value="' . $list_id . '" />' . $list_name . '</label></td>';
				} else {
					$lists_html .= '<td><label><input type="checkbox" name="lists[]" value="' . $list_id . '" />' . $list_name . '</label></td>';
				}
				$i ++;
			}
		}
		$lists_html .= '</tr></table></div>';

		return $lists_html;
	}


}


