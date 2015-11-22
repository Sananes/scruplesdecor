<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $wp_query, $current_user;

$account_page_id    = wc_get_page_id('myaccount');
$account_url        = get_permalink($account_page_id);
$logout_url         = wp_logout_url($account_url);

$active_item = 'my-orders'; # my-orders | my-addresses | my-downloads | edit-account

if(isset($wp_query->query['edit-account']))
	$active_item = 'edit-account';
else
if(isset($wp_query->query['edit-address']))
	$active_item = 'my-addresses';

wc_print_notices();
?>

<?php do_action( 'woocommerce_before_my_account' ); ?>

<div class="row my-account">
	<div class="col-lg-3 col-md-3 col-sm-4 my-account-tabs-env">
		<div class="my-account-tabs">
			<div class="user-profile">
				<a class="image pull-left">
					<?php echo get_avatar($current_user->ID, 128); ?>
				</a>
				<a class="name" href="<?php echo the_author_meta('user_url', $current_user->ID); ?>"><?php echo $current_user->display_name; ?></a>
				<a class="logout" href="<?php echo $logout_url; ?>"><?php _e('Log Out', 'woocommerce'); ?></a>
			</div>
			<ul>
				<li<?php echo $active_item == 'my-orders' ? ' class="active"' : ''; ?>><a href="<?php echo $account_url; ?>#my-orders"><?php _e('Orders', 'woocommerce'); ?></a></li>
				<li<?php echo $active_item == 'my-addresses' ? ' class="active"' : ''; ?>><a href="<?php echo $account_url; ?>#my-addresses"><?php _e('My Addresses', 'woocommerce'); ?></a></li>
				<li<?php echo $active_item == 'my-downloads' ? ' class="active"' : ''; ?>><a href="<?php echo $account_url; ?>#my-downloads"><?php _e('My Downloads', TD); ?></a></li>
				<li<?php echo $active_item == 'edit-account' ? ' class="active"' : ''; ?>><a href="<?php echo wc_customer_edit_account_url(); ?>"><?php _e('Edit Account', TD); ?></a></li>
				<li><a href="<?php echo $logout_url; ?>"><?php _e('Log Out', TD); ?></a></li>
			</ul>
		</div>
	</div>
	<div class="col-lg-9 col-md-9 col-sm-8">

		<div class="my-account-content">

