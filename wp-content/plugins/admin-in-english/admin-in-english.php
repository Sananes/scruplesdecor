<?php
/*
Plugin Name: Admin in English
Plugin URI: http://wordpress.org/extend/plugins/admin-in-english/
Description: Lets you have your backend administration panel in English, even if the rest of your blog is translated into another language.
Version: 1.2.1
Author: Nikolay Bachiyski
Author URI: http://nikolay.bg/
Tags: translation, translations, i18n, admin, english, localization, backend
*/

function admin_in_english_add_hooks() {
	add_filter( 'locale', 'admin_in_english_locale' );
}
add_action( 'plugins_loaded', 'admin_in_english_add_hooks' );

function admin_in_english_locale( $locale ) {
	if ( admin_in_english_should_use_english() ) {
		return 'en_US';
	}
	return $locale;
}

function admin_in_english_should_use_english() {
	// frontend AJAX calls are mistakend for admin calls, because the endpoint is wp-admin/admin-ajax.php
	return admin_in_english_is_admin() && !admin_in_english_is_frontend_ajax();
}

function admin_in_english_is_admin() {
	return
		is_admin() || admin_in_english_is_tiny_mce() || admin_in_english_is_login_page();
}

function admin_in_english_is_frontend_ajax() {
	return defined( 'DOING_AJAX' ) && DOING_AJAX && false === strpos( wp_get_referer(), '/wp-admin/' );
}

function admin_in_english_is_tiny_mce() {
	return false !== strpos( $_SERVER['REQUEST_URI'], '/wp-includes/js/tinymce/');
}

function admin_in_english_is_login_page() {
	return false !== strpos( $_SERVER['REQUEST_URI'], '/wp-login.php' );
}
