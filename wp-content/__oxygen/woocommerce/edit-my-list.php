<?php $wishlist = new WC_Wishlists_Wishlist($_GET['wlid']); ?>

<?php
$current_owner_key = WC_Wishlists_User::get_wishlist_key();
$sharing = $wishlist->get_wishlist_sharing();
$sharing_key = $wishlist->get_wishlist_sharing_key();
$wl_owner = $wishlist->get_wishlist_owner();

$wishlist_items = WC_Wishlists_Wishlist_Item_Collection::get_items($wishlist->id, true);

$treat_as_registry = false;
?>

<?php
if ($wl_owner != WC_Wishlists_User::get_wishlist_key() && !current_user_can('manage_woocommerce')) :

	die();

endif;


# start: modified by Arlind Nushi
wp_enqueue_script(array('icheck'));
wp_enqueue_style(array('icheck'));

do_action('laborator_woocommerce_before_wrapper');
# end: modified by Arlind Nushi

?>

<?php if (function_exists('wc_print_messages')) : ?>
	<?php wc_print_messages(); ?>
<?php else : ?>
	<?php WC_Wishlist_Compatibility::wc_print_notices(); ?>
<?php endif; ?>

<?php do_action('woocommerce_wishlists_before_wrapper'); ?>
<div id="wl-wrapper" class="product woocommerce"> <!-- product class so woocommerce stuff gets applied in tabs -->

	
	<div class="wl-intro">
		<div class="row">
			<div class="col-lg-12">
				<div class="white-block block-pad log-in">
					<h1><?php $wishlist->the_title(); ?></h1>
					
					<div class="wl-intro-desc">
						<?php $wishlist->the_content(); ?>
					</div>
					
					<?php if ($sharing == 'Public' || $sharing == 'Shared') : ?>
						<div class="wl-share-url">
							<strong><?php _e('Wishlist URL:', 'wc_wishlist'); ?> </strong>
							<?php echo $wishlist->the_url_view($sharing == 'Shared'); ?>
						</div>
					<?php endif; ?>
					
					<div class="clear"></div>
					
					
					<?php if ($sharing == 'Public' || $sharing == 'Shared') : ?>
						<?php if ($wishlist_items && count($wishlist_items)) : ?>
							<div class="wl-meta-share">
								<?php woocommerce_wishlists_get_template('wishlist-sharing-menu.php', array('id' => $wishlist->id)); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
								
					<p class="wlbuttons-list">
						<a class="wlconfirm btn btn-default" data-message="<?php _e('Are you sure you want to delete this list?', 'wc_wishlist'); ?>" href="<?php $wishlist->the_url_delete(); ?>"><?php _e('Delete list', 'wc_wishlist'); ?></a>
						
						<?php if (($sharing == 'Public' || $sharing == 'Shared') && count($wishlist_items)) : ?>
							<a href="<?php $wishlist->the_url_view(); ?>&preview=true" class="btn btn-black"><?php _e('Preview List', 'wc_wishlist'); ?></a>
						<?php endif; ?>
					</p>
					
					
					<div class="clearfix"></div>
					
				</div>
			</div>
		</div>
	</div>

	
	<div class="row">
		<div class="col-md-12">
			
			<div class="wl-tab-wrap woocommerce-tabs">

				<ul class="wl-tabs tabs">
					<li class="wl-items-tab"><a href="#tab-wl-items"><?php _e('List Items', 'wc_wishlist'); ?></a></li>
					<li class="wl-settings-tab"><a href="#tab-wl-settings"><?php _e('Settings', 'wc_wishlist'); ?></a></li>
				</ul>
		
				<div class="wl-panel panel" id="tab-wl-items">
				
					<div class="row wl-edit-list-row">
						
						<div class="col-md-12">
						
							<?php if (sizeof($wishlist_items) > 0) : ?>
								<form action="<?php $wishlist->the_url_edit(); ?>" method="post" class="wl-form" id="wl-items-form">
									<input type="hidden" name="wlid" value="<?php echo $wishlist->id; ?>" />
									<?php WC_Wishlists_Plugin::nonce_field('manage-list'); ?>
									<?php echo WC_Wishlists_Plugin::action_field('manage-list'); ?>
									<input type="hidden" name="wlmovetarget" id="wlmovetarget" value="0" />
				
									<div class="wl-row">
										<table width="100%" cellpadding="0" cellspacing="0" class="wl-actions-table">
											<tbody>
												<tr>
													<td class="oxy-list-env">
														<select class="wl-sel move-list-sel oxy-list" name="wlupdateaction" id="wleditaction1">
															<option selected="selected"><?php _e('Actions', 'wc_wishlist'); ?></option>
															<option value="add-to-cart"><?php _e('Add to Cart', 'wc_wishlist'); ?></option>
															<option value="quantity"><?php _e('Update Quantities', 'wc_wishlist'); ?></option>
															<option value="remove"><?php _e('Remove from List', 'wc_wishlist'); ?></option>
															<optgroup label="<?php _e('Move to another List', 'wc_wishlist'); ?>">
																<?php $lists = WC_Wishlists_User::get_wishlists(); ?>
																<?php if ($lists && count($lists)) : ?>
																	<?php foreach ($lists as $list) : ?>
																		<?php if ($list->id != $wishlist->id) : ?>
																			<option value="<?php echo $list->id; ?>"><?php $list->the_title(); ?> ( <?php echo $wishlist->get_wishlist_sharing(true); ?> )</option>
																		<?php endif; ?>
																	<?php endforeach; ?>
																<?php endif; ?>
																<option value="create"><?php _e('+ Create A New List', 'wc_wishlist'); ?></option>
															</optgroup>
														</select>
													<td>
														<button class="button small wl-but wl-add-to btn-apply btn btn-default up"><?php _e('Apply Action', 'wc_wishlist'); ?></button>
													</td>
												</tr>
											</tbody>
										</table>
									</div><!-- wl-row wl-clear -->
				
									<table class="shop_table cart wl-table manage" cellspacing="0">
										<thead>
											<tr>
												<th class="check-column"><input type="checkbox" name="" value="" /></th>
												<th class="product-remove">&nbsp;</th>
												<th class="product-thumbnail"><?php _e('Image', 'wc_wishlist'); ?></th>
												<th class="product-name"><?php _e('Product', 'wc_wishlist'); ?></th>
												<th class="product-price"><?php _e('Price', 'wc_wishlist'); ?></th>
												<th class="product-quantity ctr"><?php _e('Quantity', 'wc_wishlist'); ?></th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($wishlist_items as $wishlist_item_key => $item) {
												$_product = $item['data'];
												if ($_product->exists() && $item['quantity'] > 0) {
													?>
													<tr class="cart_table_item">
														<td class="check-column" >
															<input type="checkbox" name="wlitem[]" value="<?php echo $wishlist_item_key; ?>" />
														</td>
														<td class="product-remove">
															<a href="<?php echo woocommerce_wishlist_url_item_remove($wishlist->id, $wishlist_item_key); ?>" class="remove wlconfirm" title="<?php _e('Remove this item from your wishlist', 'wc_wishlist'); ?>" data-message="<?php esc_attr(_e('Are you sure you would like to remove this item from your list?', 'wc_wishlist')); ?>">&times;</a>
														</td>
				
														<!-- The thumbnail -->
														<td class="product-thumbnail">
															<?php
															# start: modified by Arlind Nushi
															if(has_post_thumbnail($_product->id))
															{
																$cart_img = laborator_show_img($_product->id, 'shop-thumb-2'); 
															}
															else
															{
																$attachment_ids = $_product->get_gallery_attachment_ids();
																
																if(count($attachment_ids))
																{
																	$first_img = reset($attachment_ids);
																	$first_img_link = wp_get_attachment_url( $first_img );
																	
																	$cart_img = laborator_show_img($first_img_link, 'shop-thumb-2');
																}
																else
																{
																	$cart_img = laborator_show_img(wc_placeholder_img_src(), 'shop-thumb-2');
																}
															}
															# end: modified by Arlind Nushi
															printf('<a href="%s">%s</a>', esc_url(get_permalink(apply_filters('woocommerce_in_cart_product_id', $item['product_id']))), $cart_img);
															?>
														</td>
				
														<td class="product-name">
															<?php
															if (WC_Wishlist_Compatibility::is_wc_version_gte_2_1()) {
																if (!$_product->is_visible()) {
																	echo apply_filters('woocommerce_cart_item_name', $_product->get_title(), $item, $wishlist_item_key);
																} else {
																	echo apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', ( is_array($item['variation']) ? esc_url(add_query_arg($item['variation'], $_product->get_permalink())) : $_product->get_permalink()), $_product->get_title()), $item, $wishlist_item_key);
																}
															} else {
																printf('<a href="%s">%s</a>', esc_url(get_permalink(apply_filters('woocommerce_in_cart_product_id', $item['product_id']))), apply_filters('woocommerce_in_wishlist_product_title', $_product->get_title(), $_product, $wishlist_item_key));
															}
				
															// Meta data
															echo $woocommerce->cart->get_item_data($item);
				
															// Availability
															$availability = $_product->get_availability();
				
															if ($availability['availability']) :
																echo apply_filters('woocommerce_stock_html', '<p class="stock text-small ' . esc_attr($availability['class']) . '">' . esc_html($availability['availability']) . '</p>', $availability['availability']);
															endif;
															?>
															
															<?php
															# Rating			
															if ( get_option( 'woocommerce_enable_review_rating' ) != 'no' && $_product->get_rating_count() ):
															?>
															
															<div class="rating filled-<?php echo intval($_product->get_average_rating()); echo $_product->get_average_rating() - intval($_product->get_average_rating()) > .49 ? ' and-half' : ''; ?>">
																<span class="glyphicon glyphicon-star star-1"></span>
																<span class="glyphicon glyphicon-star star-2"></span>
																<span class="glyphicon glyphicon-star star-3"></span>
																<span class="glyphicon glyphicon-star star-4"></span>
																<span class="glyphicon glyphicon-star star-5"></span>
															</div>
															<?php endif; ?>
				
															<?php do_action('woocommerce_wishlist_after_list_item_name', $item, $wishlist); ?>
														</td>
				
														<!-- Product price -->
														<td class="product-price">
															<?php
															if (WC_Wishlist_Compatibility::is_wc_version_gte_2_1()) {
																$price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $item, $wishlist_item_key);
															} else {
																$product_price = ( get_option('woocommerce_display_cart_prices_excluding_tax') == 'yes' ) ? $_product->get_price_excluding_tax() : $_product->get_price();
																$price = apply_filters('woocommerce_cart_item_price_html', woocommerce_price($product_price), $item, $wishlist_item_key);
															}
															?>
				
															<?php echo apply_filters('woocommerce_wishlist_list_item_price', $price, $item, $wishlist); ?>
														</td>
				
														<!-- Quantity inputs -->
														<td class="product-quantity">
															<?php
															
															if ($_product->is_sold_individually()) {
																$product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $wishlist_item_key);
															} else {
																$product_quantity_value = apply_filters('woocommerce_wishlist_list_item_quantity_value', $item['quantity'], $item, $wishlist);
																
																$step = apply_filters('woocommerce_quantity_input_step', '1', $_product);
																$min = apply_filters('woocommerce_quantity_input_min', '', $_product);
																$max = apply_filters('woocommerce_quantity_input_max', $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(), $_product);
																
																$product_quantity = sprintf('<div class="quantity"><input type="number" name="cart[%s][qty]" step="%s" min="%s" max="%s" value="%s" size="4" title="' . _x('Qty', 'Product quantity input tooltip', 'woocommerce') . '" class="input-text qty text" maxlength="12" /></div>', $wishlist_item_key, $step, $min, $max, esc_attr($product_quantity_value));
															}				
															?>
				
															<?php echo $product_quantity; ?>
				
														</td>
														<?php if ($treat_as_registry) : ?>
															<td class="product-quantity">
																<?php $quantity_purchased = apply_filters('woocommerce_wishlist_item_purchased_quantity', isset($item['quantity_purchased']) ? $item['quantity_purchased'] : 0, $wishlist_item_key); ?>
																<?php
																$quantity_remaining = (int) $item['quantity'] - (int) $quantity_purchased;
																$quantity_remaining = $quantity_remaining > 0 ? absint($quantity_remaining) : 0;
																?>
																<?php echo apply_filters('woocommerce_wishlist_item_needs_quantity', $quantity_remaining, $wishlist_item_key); ?>
															</td>
														<?php endif; ?>
														<td class="product-purchase">
															<?php if ($_product->is_in_stock() && apply_filters('woocommerce_wishlist_user_can_purcahse', true, $_product)) : ?>
																<a href="<?php echo woocommerce_wishlist_url_item_add_to_cart($wishlist->id, $wishlist_item_key, $wishlist->get_wishlist_sharing() == 'Shared' ? $wishlist->get_wishlist_sharing_key() : false, 'edit'); ?>" class="button alt btn btn-default up"><?php _e('Add to Cart', 'wc_wishlist'); ?></a>
															<?php endif; ?>
														</td>
													</tr>
													<?php
												}
											}
											?>
										</tbody>
									</table>
									<div class="wl-row">
										<table width="100%" cellpadding="0" cellspacing="0" class="wl-actions-table">
											<tbody>
												<tr>
													<td class="oxy-list-env">
														<select class="wl-sel move-list-sel oxy-list" name="wleditaction2" id="wleditaction2">
															<option selected="selected"><?php _e('Actions', 'wc_wishlist'); ?></option>
															<option value="quantity"><?php _e('Update Quantities', 'wc_wishlist'); ?></option>
															<option value="add-to-cart"><?php _e('Add to Cart', 'wc_wishlist'); ?></option>
															<option value="remove"><?php _e('Remove from List', 'wc_wishlist'); ?></option>
															<optgroup label="<?php _e('Move to another list', 'wc_wishlist'); ?>">
																<?php $lists = WC_Wishlists_User::get_wishlists(); ?>
																<?php if ($lists && count($lists)) : ?>
																	<?php foreach ($lists as $list) : ?>
																		<?php if ($list->id != $wishlist->id) : ?>
																			<option value="<?php echo $list->id; ?>"><?php $list->the_title(); ?> ( <?php echo $wishlist->get_wishlist_sharing(true); ?> )</option>
																		<?php endif; ?>
																	<?php endforeach; ?>
																<?php endif; ?>
																<option value="create"><?php _e('+ Create A New List', 'wc_wishlist'); ?></option>
															</optgroup>
														</select>
													</td>
													<td>
														<button class="button small wl-but wl-add-to btn-apply btn btn-default up"><?php _e('Apply Action', 'wc_wishlist'); ?></button>
													</td>
												</tr>
											</tbody>
										</table>
				
										<div class="wl-clear"></div>
									</div><!-- wl-row wl-clear -->
								</form>
				
							<?php else : ?>
								<?php $shop_url = get_permalink(woocommerce_get_page_id('shop')); ?>
								<p><?php _e('You do not have anything in this list.', 'wc_wishlist'); ?> <a href="<?php echo $shop_url; ?>"><?php _e('Go Shopping', 'wc_wishlist'); ?></a>.</p>
				
							<?php endif; ?>
						
						</div>
					
					</div>
					
		
		
				</div><!-- /tab-wl-items -->
		
		
		
		
		
				<div class="wl-panel panel" id="tab-wl-settings">
					<div class="wl-form white-block block-pad no-margin">
						
						<h4 class="with-divider"><?php _e('Edit Wishlist', 'oxygen'); ?></h4>
						
						<form  action="" enctype="multipart/form-data" method="post">
							<input type="hidden" name="wlid" value="<?php echo $wishlist->id; ?>" />
							<?php echo WC_Wishlists_Plugin::action_field('edit-list'); ?>
							<?php echo WC_Wishlists_Plugin::nonce_field('edit-list'); ?>
		
		<!-- <p class="form-row">
		<strong>What kind of list is this?<abbr class="required" title="required">*</abbr></strong>
					<table class="wl-rad-table">
							<tr>
									<td><input type="radio" name="wishlist_type" id="rad_wishlist" value="wishlist" checked="checked"></td>
									<td><label for="rad_wishlist">Wishlist <span class="wl-small">- Allows you to add products to a list for later.</span></label></td>
							</tr>
							<tr>
									<td><input type="radio" name="wishlist_type" id="rad_reg" value="registry"></td>
									<td><label for="rad_reg">Registry <span class="wl-small">- Registries allow you to request a specific number of items and users can purchase items which will update the list for others to know what has been purchased.</span></label></td>
							</tr>
					</table>
		</p> -->
							<p class="form-row form-row-wide">
								<label for="wishlist_title" class="form-label"><?php _e('Name your list', 'wc_wishlist'); ?> <abbr class="required" title="required">*</abbr></label>
								<input type="text" name="wishlist_title" id="wishlist_title" class="input-text form-control" value="<?php echo esc_attr($wishlist->post->post_title); ?>" />
							</p>
							<p class="form-row form-row-wide">
								<label for="wishlist_description" class="form-label"><?php _e('Describe your list', 'wc_wishlist'); ?></label>
								<textarea name="wishlist_description" id="wishlist_description" class="autogrow"><?php echo esc_textarea($wishlist->post->post_content); ?></textarea>
							</p>
							
							<br />
							
							<p class="form-row">
								<strong class="form-label"><?php _e('Privacy Settings', 'wc_wishlist'); ?> <abbr class="required" title="required">*</abbr></strong>
								<table class="wl-rad-table icheck-top">
									<tr>
										<td><input type="radio" name="wishlist_sharing" id="rad_pub" value="Public" <?php checked('Public', $sharing); ?>></td>
										<td><label for="rad_pub"><?php _e('Public', 'wc_wishlist'); ?> <span class="wl-small">- <?php _e('Anyone can search for and see this list. You can also share using a link.', 'wc_wishlist'); ?></span></label></td>
									</tr>
									<tr>
										<td><input type="radio" name="wishlist_sharing" id="rad_shared" value="Shared" <?php checked('Shared', $sharing); ?>></td>
										<td><label for="rad_shared"><?php _e('Shared', 'wc_wishlist'); ?> <span class="wl-small">- <?php _e('Only people with the link can see this list. It will not appear in public search results.', 'wc_wishlist'); ?></span></label></td>
									</tr>
									<tr>
										<td><input type="radio" name="wishlist_sharing" id="rad_priv" value="Private" <?php checked('Private', $sharing); ?>></td>
										<td><label for="rad_priv"><?php _e('Private', 'wc_wishlist'); ?> <span class="wl-small">- <?php _e('Only you can see this list.', 'wc_wishlist'); ?></span></label></td>
									</tr>
								</table>
							</p>
							
							<br />
							
							<p><?php _e('Enter a name you would like associated with this list.  If your list is public, users can find it by searching for this name.', 'wc_wishlist'); ?></p>
							
							<div class="row spread-2">
							
								<div class="col-sm-4">
									
									<p class="form-row form-row-first">
										<label for="wishlist_first_name"><?php _e('First Name', 'wc_wishlist'); ?></label>
										<input type="text" name="wishlist_first_name" id="wishlist_first_name" value="<?php echo esc_attr(get_post_meta($wishlist->id, '_wishlist_first_name', true)); ?>" class="input-text form-control" />
									</p>
									
								</div>
							
								<div class="col-sm-4">
									
									<p class="form-row form-row-last">
										<label for="wishlist_first_name"><?php _e('Last Name', 'wc_wishlist'); ?></label>
										<input type="text" name="wishlist_last_name" id="wishlist_last_name" value="<?php echo esc_attr(get_post_meta($wishlist->id, '_wishlist_last_name', true)); ?>" class="input-text form-control" />
									</p>
									
								</div>
							
								<div class="col-sm-4">
									
									<p class="form-row form-row-first">
										<label for="wishlist_owner_email"><?php _e('Email Associated with the List', 'wc_wishlist'); ?></label>
										<input type="text" name="wishlist_owner_email" id="wishlist_owner_email" value="<?php echo esc_attr(get_post_meta($wishlist->id, '_wishlist_email', true)); ?>" class="input-text form-control" />
									</p>
									
								</div>
							
							</div>
								
							
							<div class="wl-clear"></div>
		
							<p class="form-row no-margin">
								<input type="submit" class="button alt btn btn-default up" name="update_wishlist" value="<?php _e('Save Changes', 'wc_wishlist'); ?>">
							</p>
						</form>
						<div class="wl-clear"></div>
					</div><!-- /wl form -->
		
				</div><!-- /tab-wl-settings panel -->
			</div><!-- /wishlist-wrapper -->
		
			<?php woocommerce_wishlists_get_template('wishlist-email-form.php', array('wishlist' => $wishlist)); ?>
			
		</div>
	</div>
	
	
</div>

<script type="text/javascript">
jQuery(document).ready(function($)
{
	$('.shop_table thead th input[type="checkbox"]').on('ifToggled', function()
	{		
		$(this).closest('table').find(':checkbox').attr('checked', this.checked).iCheck('update');
	});
});

</script>
<?php do_action('woocommerce_wishlists_after_wrapper'); ?>


<?php

# start: modified by Arlind Nushi
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi

?>