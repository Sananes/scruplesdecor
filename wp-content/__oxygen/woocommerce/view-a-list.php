<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */


$wishlist = new WC_Wishlists_Wishlist($_GET['wlid']);
$wishlist_items = WC_Wishlists_Wishlist_Item_Collection::get_items($wishlist->id);
$wishlist_item_categories = WC_Wishlists_Wishlist_Item_Collection::get_items_categories($wishlist->id);
?>

<?php
$current_owner_key = WC_Wishlists_User::get_wishlist_key();
$sharing = $wishlist->get_wishlist_sharing();
$sharing_key = $wishlist->get_wishlist_sharing_key();
$wl_owner = $wishlist->get_wishlist_owner();


$is_visible = true;

if (!current_user_can('manage_woocommerce')) :
	if ($sharing == 'Shared' && ($current_owner_key != $wl_owner)) :
		if (!isset($_GET['wlkey']) || $_GET['wlkey'] != $sharing_key) :
			$is_visible = false;
		endif;
	elseif ($sharing == 'Private' && $current_owner_key != $wl_owner) :
		if (!isset($_GET['wlkey']) || $_GET['wlkey'] != $sharing_key || $current_owner_key != $wl_owner) :
			$is_visible = false;
		endif;
	endif;
endif;

$wlitemsort = isset($_GET['wlitemsort']) ? $_GET['wlitemsort'] : 'date';
$wlitemcat = isset($_GET['wlitemcat']) ? $_GET['wlitemcat'] : 0;


# start: modified by Arlind Nushi
do_action('laborator_woocommerce_before_wrapper');
# end: modified by Arlind Nushi

?>
<?php if (function_exists('wc_print_messages')) : ?>
	<?php wc_print_messages(); ?>
<?php else : ?>
	<?php WC_Wishlist_Compatibility::wc_print_notices(); ?>
<?php endif; ?>

<?php do_action('woocommerce_wishlists_before_wrapper'); ?>
<?php if (!$is_visible) : ?>
	<div id="wl-wrapper" class="woocommerce">

		<div class="row">
			<div class="col-md-12">
				<ul class="woocommerce_error woocommerce-errror">
					<li>
						<?php _e('Unable to locate the requested list', 'wc_wishlist'); ?>
					</li>
				</ul>
			</div>
		</div>

	</div>
<?php else: ?>
	<div id="wl-wrapper" class="product woocommerce">
	
		<?php if ($wishlist_items && count($wishlist_items)) : ?>
			<?php if (isset($_GET['preview']) && $_GET['preview']) : ?>
				<div class="row">
					<div class="col-md-12">
						<div class="woocommerce-info woocommerce_info">
							<a href="<?php echo $wishlist->the_url_edit(); ?>" class="button btn btn-white left-btn"><?php _e('Return to your view', 'wc_wishlist'); ?></a> <?php _e('This is how other people will see your Wish List', 'wc_wishlist'); ?>
						</div>
					</div>
				</div>
				<br />
			<?php endif; ?>
			
			
			<div class="wl-intro">
				<div class="row">
					<div class="col-lg-12">
						<div class="white-block block-pad log-in">
							<h1><?php $wishlist->the_title(); ?></h1>
							
							<div class="wl-intro-desc">
								<?php $wishlist->the_content(); ?>
							</div>
							
							
							<?php if ($sharing == 'Public' || $sharing == 'Shared') : ?>
								<?php if ($wishlist_items && count($wishlist_items)) : ?>
									<div class="wl-meta-share pull-left">
										<?php woocommerce_wishlists_get_template('wishlist-sharing-menu.php', array('id' => $wishlist->id)); ?>
									</div>
								<?php endif; ?>
							<?php endif; ?>
							
							<div class="clearfix"></div>
							
						</div>
					</div>
				</div>
			</div>

			

			
			<div class="row">
				
				<div class="col-md-12">
				
				
					<div class="wl-row wl-clear">
						<form method="GET" action="<?php $wishlist->the_url_view(); ?>">
							<input type="hidden" name="wlid" value="<?php echo $wishlist->id; ?>" />
							<table width="100%" cellpadding="0" cellspacing="0" class="wl-actions-table wl-right sorting-options">
		
		
								<tbody>
									<tr>
										<td><label for="sort-dropdown"><?php _e('Sort by:', 'wc_wishlist'); ?></label></td>
										<td><label for="sort-dropdown"><?php _e('In Category:', 'wc_wishlist'); ?></label></td>
										<td></td>
									</tr>
		
									<tr>
										<td class="sel-env">
											<select class="wl-sel oxy-list" name="wlitemsort" id="sort-dropdown">
												<option value="date" <?php selected($wlitemsort, 'date'); ?>><?php _e('Date Added', 'wc_wishlist'); ?></option>
												<option value='pasc' <?php selected($wlitemsort, 'pasc'); ?>><?php _e('Price (High to Low)', 'wc_wishlist'); ?></option>
												<option value="pdesc" <?php selected($wlitemsort, 'pdesc'); ?>><?php _e('Price (Low to High)', 'wc_wishlist'); ?></option>
											</select>
										</td>
										<td class="sel-env">
											<?php wp_dropdown_categories(array('taxonomy' => 'product_cat', 'class' => 'wl-sel oxy-list', 'name' => 'wlitemcat', 'show_option_all' => __('All', 'wc_wishlist'), 'selected' => $wlitemcat, 'hierarchical' => true, 'include' => array_keys($wishlist_item_categories))); ?>
										</td>
										<td>
											<input type="submit" class="button small wl-but btn btn-default" value="<?php _e('Go', 'wc_wishlist'); ?>" />
										</td>
		
									</tr>
								</tbody>
		
							</table>
						</form>
					</div>
					
					
					<table class="shop_table cart wl-table view my-lists-table view-a-list" cellspacing="0">
						<thead>
		
							<tr>
								<th class="product-thumbnail"><?php _e('Image', 'wc_wishlist'); ?></th>
								<th class="product-name"><?php _e('Product', 'wc_wishlist'); ?></th>
								<th class="product-price"><?php _e('Price', 'wc_wishlist'); ?></th>
								<th class="product-quantity ctr"><?php _e('Quantity', 'wc_wishlist'); ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
							if ($wlitemcat) {
								$wishlist_items = _woocommerce_wishlist_filter_item_collection_category($wishlist_items, (int) $wlitemcat);
							}
		
							if ($wlitemsort == 'date') {
								uasort($wishlist_items, '_woocommerce_wishlist_sort_item_collection_date');
							} elseif ($wlitemsort == 'pasc') {
								uasort($wishlist_items, '_woocommerce_wishlist_sort_item_collection_price_asc');
							} elseif ($wlitemsort == 'pdesc') {
								uasort($wishlist_items, '_woocommerce_wishlist_sort_item_collection_price_desc');
							}
		
		
							if (sizeof($wishlist_items) > 0) :
								foreach ($wishlist_items as $wishlist_item_key => $item) :
									$_product = $item['data'];
									if ($_product->exists() && $item['quantity'] > 0) :
										?>
										<tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item cart_table_item', $item, $wishlist_item_key)); ?>">
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
													echo apply_filters('woocommerce_stock_html', '<p class="stock ' . esc_attr($availability['class']) . '">' . esc_html($availability['availability']) . '</p>', $availability['availability']);
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
											<td class="product-price">
												<?php
												$price = '';
												if (WC_Wishlist_Compatibility::is_wc_version_gte_2_1()) {
													$price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $item, $wishlist_item_key);
												} else {
													$product_price = ( get_option('woocommerce_display_cart_prices_excluding_tax') == 'yes' ) ? $_product->get_price_excluding_tax() : $_product->get_price();
													$price = apply_filters('woocommerce_cart_item_price_html', woocommerce_price($product_price), $item, $wishlist_item_key);
												}
												?>
		
												<?php echo apply_filters('woocommerce_wishlist_list_item_price', $price, $item, $wishlist); ?>
		
											</td>
											<td class="product-quantity">
												<?php echo apply_filters('woocommerce_wishlist_list_item_quantity_value', $item['quantity'], $item, $wishlist); ?>
											</td>
											<td class="product-purchase">
												<?php if ($_product->is_in_stock() && apply_filters('woocommerce_wishlist_user_can_purcahse', true, $_product)) : ?>
													<a href="<?php echo woocommerce_wishlist_url_item_add_to_cart($wishlist->id, $wishlist_item_key, $wishlist->get_wishlist_sharing() == 'Shared' ? $wishlist->get_wishlist_sharing_key() : false ); ?>" class="button btn btn-default btn-block up"><?php _e('Add to Cart', 'wc_wishlist'); ?></a>
												<?php endif; ?>
		
											</td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
								<tr><!-- the tds need to be individual for woothemes responsive mode, since they target the first td specifically and hide it. WIth colspan this causes issues once in responsive mode -->
									<td class="product-thumbnail">&nbsp;</td>
									<td class="product-name">&nbsp;</td>
									<td class="product-price">&nbsp;</td>
									<td class="product-quantity">&nbsp;</td>
									<td class="product-purchase"><a href="<?php echo woocommerce_wishlist_url_add_all_to_cart($wishlist->id, $wishlist->get_wishlist_sharing() == 'Shared' ? $wishlist->get_wishlist_sharing_key() : false ); ?>" class="button alt wl-add-all btn btn-black btn-block up"><?php _e('Add All To Cart', 'wc_wishlist'); ?></a></td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
					
					
				</div>
				
			</div>


		<?php else : ?>
			<?php $shop_url = get_permalink(woocommerce_get_page_id('shop')); ?>
			
			<div class="row">
				<div class="col-md-12">
					<div class="woocommerce-info woocommerce_info"> 
						<?php _e('This list currently contains no items.', 'wc_wishlist'); ?> <a href="<?php echo WC_Wishlists_Pages::get_url_for('find-a-list'); ?>"><?php _e('Back to find a list', 'wc_wishlist'); ?></a>
					</div>
				</div>
			</div>
			
			<?php endif; ?>

	</div><!-- /wishlist-wrapper -->
<?php endif; ?>
<?php do_action('woocommerce_wishlists_after_wrapper'); ?>

<?php woocommerce_wishlists_get_template('wishlist-email-form.php', array('wishlist' => $wishlist)); ?>


<?php
# start: modified by Arlind Nushi
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi
?>