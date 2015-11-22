<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

if( ! class_exists('WC_Wishlists_User'))
	return;
?>
<?php do_action('woocommerce_wishlists_before_wrapper'); ?>
<div id="wl-wrapper" class="woocommerce">
    
    <h4 class="with-divider"><?php echo apply_filters( 'woocommerce_my_account_my_wishlists_title', __( 'Wishlists', 'wc_wishlist' ) ); ?></h4>
    
    <table class="shop_table cart wl-table wl-manage my-lists-table my-lists-table-account" cellspacing="0">
        <thead>
            <tr>
                <th class="product-name"><?php _e('List Name', 'wc_wishlist'); ?></th>
                <th class="wl-date-added"><?php _e('Date Added', 'wc_wishlist'); ?></th>
                <th class="wl-privacy-col"><?php _e('Privacy Settings', 'wc_wishlist'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $lists = WC_Wishlists_User::get_wishlists(); ?>
            <?php if ($lists && count($lists)) : ?>
                <?php foreach ($lists as $list) : ?>
                    <?php $sharing = $list->get_wishlist_sharing(); ?>
                    <tr class="cart_table_item <?php echo WC_Wishlists_Request_Handler::last_updated_class($list->id); ?>">
                        <td class="product-name">
                            <a href="<?php $list->the_url_edit(); ?>"><?php $list->the_title(); ?></a>
                            <div class="row-actions"></div>
                            <?php if ($sharing == 'Public' || $sharing == 'Shared') : ?>
                                <?php woocommerce_wishlists_get_template('wishlist-sharing-menu.php', array('id' => $list->id)); ?>
                            <?php endif; ?>
                        </td>
                        <td class="wl-date-added up"><?php echo date(get_option('date_format'), strtotime($list->post->post_date)); ?></td>
                        <td class="wl-privacy-col up">
                            <?php echo $list->get_wishlist_sharing(true); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>

                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div><!-- /wishlist-wrapper -->
<?php do_action('woocommerce_wishlists_after_wrapper'); ?>
