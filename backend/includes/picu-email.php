<?php
/**
 * Template for picu HTML Emails
 *
 * @since 0.7.3
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function picu_get_email_template( $mail_content ) {

	if ( ! isset( $mail_content['footer_notice'] ) ) {
		$mail_content['footer_notice'] = '';
	}

	if ( ! isset( $mail_content['button_text'] ) ) {
		$mail_content['button_text'] = __( 'Click here', 'Default button text in picu emails', 'picu' );
	}

	if ( ! isset( $mail_content['title'] ) ) {
		$mail_content['title'] = __( 'Untitled', 'picu' );
	}

	// Filter to change the logo
	// TODO: Test this in different mail clients
	$logo = '';
	$logo_src = apply_filters( 'picu_logo', $logo );
	if ( ! empty( $logo_src ) ) {
		$logo = '<p style="margin-bottom: 20px; text-align: center;"><a href="' . esc_url( home_url( '/' ) ) . '"><img style="height: 45px; width: auto;" class="logo" src="' . $logo_src . '" /></a></p>';
	}

	// TODO: Fix this!
	if ( ! isset( $mail_content['message_text'] ) ) {
		$mail_content['message_text'] = '';
	}
	$mail_content['message_text'] = apply_filters( 'picu_email_message_text', $mail_content['message_text'] );

	if ( ! isset( $mail_content['mail_html'] ) ) {
		$mail_content['message_html'] = str_replace( "\n", '<br>', $mail_content['message_text'] );

		$mail_content['message_html'] = apply_filters( 'picu_email_message_html', $mail_content['message_html'] );
	}

	if ( ! isset( $mail_content['url'] ) ) {
		$mail_content['url'] = '';
	} else {
		// Add URL only to textmails
		$mail_content['message_text'] .= "\n\n" . $mail_content['url'];
	}

	// Check if collection is password protected
	$password = '';
	if ( isset( $_POST['post_password'] ) AND ! empty( $_POST['post_password'] ) ) {
		$password = __( 'Password:', 'picu' ) . ' ' . stripslashes( sanitize_text_field( $_POST['post_password'] ) );
	}

	// Load picu settings
	$options = get_option( 'picu_settings' );

	// Check Setting for HTMl Mails
	if ( isset( $options['send_html_mails'] ) AND 'on' != $options['send_html_mails'] ) {
		$mail_template = $mail_content['message_text'];

		if ( isset( $password ) AND ! empty( $password ) ) {
			$mail_template .= "\n\n" . $password;
		}
	} else {

		// Load the template file according to theme setting
		if ( isset( $options['theme'] ) AND $options['theme'] == 'light' ) {
			$mail_template = file_get_contents( PICU_PATH . '/backend/includes/email-templates/email-template-light-inline.html' );

			$picu_logo_uri = PICU_URL . '/backend/images/picu_logo_dark.png';
			$primary_base_color = '#2f92a7';
			$primary_color = '#2f92a7';
		}
		else {
			$mail_template = file_get_contents( PICU_PATH . '/backend/includes/email-templates/email-template-dark-inline.html' );

			$picu_logo_uri = PICU_URL . '/backend/images/picu_logo_light.png';
			$primary_base_color = '#7ad03a';
			$primary_color = '#7ad03a';
		}

		// Filter to change the primary color
		$primary_color = apply_filters( 'picu_primary_color', $primary_color );

		if ( isset( $options['picu_love'] ) AND 'on' == $options['picu_love'] ) {
			$picu_love = '<p>powered by</p><a href="https://picu.io/"><img src="' . $picu_logo_uri . '" width="80" style="width: 80px; height: auto;" alt="picu Logo"></a>';
		} else {
			$picu_love = '';
		}

		// Replace placeholders in template with actual strings
		$mail_template = str_replace(
			array(
				'{footer_notice}',
				'{button_text}',
				'{title}',
				'{logo}',
				'{message_text}',
				'{message_html}',
				'{url}',
				'{password}',
				'{picu_love}',
				$primary_base_color
			),
			array(
				$mail_content['footer_notice'],
				$mail_content['button_text'],
				$mail_content['title'],
				$logo,
				$mail_content['message_text'],
				$mail_content['message_html'],
				$mail_content['url'],
				$password,
				$picu_love,
				$primary_color
			),
			$mail_template
		);
	}

	// Return the content
	return $mail_template;

}
