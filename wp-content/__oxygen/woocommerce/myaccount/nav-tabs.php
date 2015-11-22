<?php
/**
 *	Nav Tabs
 *
 *	Laborator.co
 *	www.laborator.co
 */

?>
<ul class="myaccount-tabs">

	<?php if ( $downloads = WC()->customer->get_downloadable_products() ) : ?>
	<li<?php echo $active == 'my-downloads' ? ' class="active"' : ''; ?>>
		<a href="#my-downloads"><?php echo sprintf(__('My Downloads <span>(%d)</span>', 'oxygen'), WC()->customer->get_downloadable_products()); ?></a>
	</li>
	<?php endif; ?>

	<li<?php echo $active == 'my-orders' ? ' class="active"' : ''; ?>>
		<a href="#my-orders"><?php _e('Recent Orders', 'oxygen'); ?></a>
	</li>

	<?php if(is_wishlist_supported()): ?>
	<li<?php echo $active == 'wishlists' ? ' class="active"' : ''; ?>>
		<a href="#my-wishlists"><?php _e('Wishlists', 'oxygen'); ?></a>
	</li>
	<?php endif; ?>

	<?php if($active == 'my-address'): ?>
	<li class="active">
		<a href="<?php echo MYACCOUNTURL; ?>#my-address"><?php _e('My Address', 'oxygen'); ?></a>
	</li>
	<?php else: ?>
	<li>
		<a href="#my-address"><?php _e('My Address', 'oxygen'); ?></a>
	</li>
	<?php endif; ?>

	<?php if(function_exists('init_comission_king')): ?>
	<li>
		<a href="#comission-king"><?php _e('Commission King', 'oxygen'); ?></a>
	</li>
	<?php endif; ?>

	<li<?php echo $active == 'edit-account' ? ' class="active"' : ''; ?>>
		<a href="<?php echo wc_customer_edit_account_url(); ?>"><?php _e('Edit Account', 'oxygen'); ?></a>
	</li>

	<li>
		<a href="<?php echo laborator_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ); ?>"><?php _e('Logout', 'oxygen'); ?></a>
	</li>
</ul>