<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */


$wishlist = new WC_Wishlists_Wishlist($id);
$wishlist_items = WC_Wishlists_Wishlist_Item_Collection::get_items($id);

if ($wishlist_items && count($wishlist_items)) :
	$pinterest_img_url = false;
	$size = 'full';
	foreach ($wishlist_items as $item) {
		$_product = $item['data'];
		if ($_product->exists()) {
			if (has_post_thumbnail($_product->id)) {
				$image = wp_get_attachment_image_src(get_post_thumbnail_id($_product->id), $size);
			} elseif (( $parent_id = wp_get_post_parent_id($_product->id) ) && has_post_thumbnail($parent_id)) {
				$image = wp_get_attachment_image_src(get_post_thumbnail_id($parent_id), $size);
			} else {
				$image = false;
			}

			if ($image) {
				$pinterest_img_url = $image[0];
				break;
			}
		}
	}

	$is_users_list = $wishlist->get_wishlist_owner() == WC_Wishlists_User::get_wishlist_key();
	$twitter_message = $is_users_list ? __('Check out my wishlist at ', 'wc_wishlist') . get_bloginfo('name') . ' ' : __('Found an interesting list of products at ', 'wc_wishlist') . get_bloginfo('name') . ' ';
	$facebook_url = 'http://www.facebook.com/sharer.php?u=' . $wishlist->get_the_url_view($id, true) . '&t=' . $twitter_message;
	$twitter_url = 'http://twitter.com/home?status=' . $twitter_message . $wishlist->get_the_url_view($id, true);
	$pinterest_url = '#';

	$e_facebook = WC_Wishlists_Settings::get_setting('wc_wishlists_sharing_facebook', 'yes') == 'yes';
	$e_twitter = WC_Wishlists_Settings::get_setting('wc_wishlists_sharing_twitter', 'yes') == 'yes';
	$e_email = WC_Wishlists_Settings::get_setting('wc_wishlists_sharing_email', 'yes') == 'yes';
	$e_pinterest = WC_Wishlists_Settings::get_setting('wc_wishlists_sharing_pinterest', 'yes') == 'yes';


	$title = urlencode($twitter_message);
	$url = urlencode($wishlist->get_the_url_view($id, true));
	$summary = urlencode(get_the_title($wishlist->id));
	$image = urlencode($pinterest_img_url);
	
	?>
	<?php if (strstr($wishlist->get_wishlist_sharing(), 'Private') === false && ($e_email | $e_facebook | $e_twitter | $e_pinterest)) : ?>
		<ul class="lab-wl-share-links">
			<li class="wl-text"><?php _e('Share with Friends: ', 'wc_wishlist'); ?></li>
		<?php if ($e_email) : ?>
				<li>
					<a class="lab-wl-button" href="#share-via-email-<?php echo $wishlist->id; ?>">
						<i class="entypo-mail"></i>
					</a>
				</li>
		<?php endif; ?>
			<?php if ($e_facebook) : ?>
				<li class="wl-facebook">
					<a onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=<?php echo $title; ?>&amp;p[summary]=<?php echo $summary; ?>&amp;p[url]=<?php echo $url; ?>&amp;p[images][0]=<?php echo $image; ?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)">
						<i class="entypo-facebook"></i>
					</a>
				</li>
			<?php endif; ?>
			<?php if ($e_twitter) : ?>
				<li class="wl-twitter">
					<a target="_blank" href="<?php echo $twitter_url; ?>">
						<i class="entypo-twitter"></i>
					</a>
				</li>
			<?php endif; ?>
			<?php if ($e_pinterest && $pinterest_img_url) : ?>
				<li class="wl-pinterest">
					<a href="http://pinterest.com/pin/create/button/?url=<?php $wishlist->the_url_view(true); ?>&media=<?php echo $pinterest_img_url; ?>&amp;description=<?php $wishlist->the_title(); ?>" class="hide-text" target="_blank">
						<i class="pinterest"></i>
					</a>
				</li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>
	<?php endif; ?>