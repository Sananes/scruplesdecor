<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Backwards compat
 */
$active_plugins = get_option( 'active_plugins', array() );
foreach ( $active_plugins as $key => $active_plugin ) {
	if ( strstr( $active_plugin, '/product-csv-import.php' ) ) {
		$active_plugins[ $key ] = str_replace( '/product-csv-import.php', '/woocommerce-product-csv-import-suite.php', $active_plugin );
	}
}
update_option( 'active_plugins', $active_plugins );