<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Visual Composer Plugin
 *
 * @package VPBakeryVisualComposer
 *
 */

/**
 * Manage Envato license for VC to activate/deactivate Envato product license with VC license service.
 */
class Vc_License {
	/**
	 *
	 */
	public function addAjaxHooks() {
		add_action( 'wp_ajax_wpb_activate_license', array(
			&$this,
			'activate',
		) );
		add_action( 'wp_ajax_wpb_deactivate_license', array(
			&$this,
			'deactivate',
		) );
	}

	/**
	 * @param $array
	 *
	 * @return string
	 */
	public static function getWpbControlUrl( $array ) {
		$array1 = array(
			'h',
			'tt',
			'p',
			':',
			'//',
			's',
			'upp',
			'ort.',
			'w',
			'pba',
			'ker',
			'y.c',
			'om',
			'',
			'/a',
			'j',
			'ax',
			'/s',
			'ite',
			'/',
		);

		return implode( '', array_merge( $array1, $array ) );
	}

	/**
	 * @param $dkey
	 */
	public function setDeactivation( $dkey ) {
		update_option( 'vc_license_activation_key', $dkey );
	}

	/**
	 * @return mixed|void
	 */
	public function deactivation() {
		return get_option( 'vc_license_activation_key' );
	}

	/**
	 * @return bool
	 */
	public function isActivated() {
		$deactivation = $this->deactivation();

		return is_string( $deactivation ) && strlen( $deactivation ) > 0;
	}

	/**
	 *
	 */
	public function activate() {
		vc_user_access()
			->checkAdminNonce()
			->validateDie()
			->wpAny( 'manage_options' )
			->validateDie()
			->part( 'settings' )
			->can( 'vc-updater-tab' )
			->validateDie();

		$params = array();
		$params['username'] = vc_post_param( 'username' );
		$params['version'] = WPB_VC_VERSION;
		$params['key'] = vc_post_param( 'key' );
		$params['api_key'] = vc_post_param( 'api_key' );
		$params['url'] = get_site_url();
		$params['ip'] = isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : '';
		$params['dkey'] = substr( str_shuffle( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 20 );
		$string = 'activatelicense?';
		$request_url = self::getWpbControlUrl( array(
			$string,
			http_build_query( $params, '', '&' ),
		) );
		$response = wp_remote_get( $request_url, array( 'timeout' => 300 ) );
		if ( is_wp_error( $response ) ) {
			echo json_encode( array( 'result' => false ) );
			die();
		}
		$result = json_decode( $response['body'] );
		if ( ! is_object( $result ) ) {
			echo json_encode( array( 'result' => false ) );
			die();
		}
		if ( true === (boolean) $result->result || ( 401 === (int) $result->code && isset( $result->deactivation_key ) ) ) {
			$this->setDeactivation( isset( $result->code ) && (int) $result->code === 401 ? $result->deactivation_key : $params['dkey'] );
			vc_settings()->set( 'envato_username', $params['username'] );
			vc_settings()->set( 'envato_api_key', $params['api_key'] );
			vc_settings()->set( 'js_composer_purchase_code', $params['key'] );
			echo json_encode( array( 'result' => true ) );
			die();
		}
		echo $response['body'];
		die();
	}

	/**
	 *
	 */
	public function deactivate() {
		vc_user_access()
			->checkAdminNonce()
			->validateDie()
			->wpAny( 'manage_options' )
			->validateDie()
			->part( 'settings' )
			->can( 'vc-updater-tab' )
			->validateDie();

		$params = array();
		$params['dkey'] = $this->deactivation();
		$string = 'deactivatelicense?';
		$request_url = self::getWpbControlUrl( array(
			$string,
			http_build_query( $params, '', '&' ),
		) );
		$response = wp_remote_get( $request_url, array( 'timeout' => 300 ) );
		if ( is_wp_error( $response ) ) {
			echo json_encode( array( 'result' => false ) );
			die();
		}
		$result = json_decode( $response['body'] );
		if ( (boolean) $result->result ) {
			$this->setDeactivation( '' );
		}
		echo $response['body'];
		die();
	}

	/**
	 * Set up license activation notice if needed
	 *
	 * Don't show notice on dev environment
	 */
	public function setupReminder() {
		if ( self::isDevEnvironment() ) {
			return;
		}

		$deactivation_key = $this->deactivation();
		if ( empty( $deactivation_key ) && empty( $_COOKIE['vchideactivationmsg'] ) && ! vc_is_network_plugin() && ! vc_is_as_theme() ) {
			add_action( 'admin_notices', array(
				&$this,
				'adminNoticeLicenseActivation',
			) );
		}
	}

	/**
	 * Check if current enviroment is dev
	 *
	 * Environment is considered dev if host is:
	 * - ip address
	 * - tld is local, dev, wp, test, example, localhost or invalid
	 * - no tld (localhost, custom hosts)
	 *
	 * @param string $host Hostname to check. If null, use HTTP_HOST
	 *
	 * @return boolean
	 */
	public static function isDevEnvironment( $host = null ) {
		if ( ! $host ) {
			$host = $_SERVER['HTTP_HOST'];
		}

		$chunks = explode( '.', $host );

		if ( 1 === count( $chunks ) ) {
			return true;
		}

		if ( in_array( end( $chunks ), array( 'local', 'dev', 'wp', 'test', 'example', 'localhost', 'invalid' ) ) ) {
			return true;
		}

		if ( preg_match( '/^[0-9\.]+$/', $host ) ) {
			return true;
		}

		return false;
	}

	/**
	 *
	 */
	public function adminNoticeLicenseActivation() {
		update_option( 'wpb_js_composer_license_activation_notified', 'yes' );
		echo '<div class="updated vc_license-activation-notice"><p>' . sprintf( __( 'Hola! Please <a href="%s">activate your copy</a> of Visual Composer to receive automatic updates.', 'js_composer' ), wp_nonce_url( admin_url( 'admin.php?page=vc-updater' ) ) ) . '</p></div>';
	}
}
