<?php
/**
 * Show messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! $messages ) return;
?>

<aside class="notification-box success">
	<div class="icon"></div>
	<div class="content">
	<?php foreach ( $messages as $message ) : ?>
		<?php echo wp_kses_post( $message ); ?><br />
	<?php endforeach; ?>
	</div>
	<a href="#" class="close"></a>
</aside>