<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

# start: modified by Arlind Nushi
do_action('laborator_woocommerce_before_wrapper');
# end: modified by Arlind Nushi
?>

<?php global $wp_query; ?>

<?php if (function_exists('wc_print_messages')) : ?>
	<?php wc_print_messages(); ?>
<?php else : ?>
	<?php WC_Wishlist_Compatibility::wc_print_notices(); ?>
<?php endif; ?>

<?php do_action('woocommerce_wishlists_before_wrapper'); ?>
<div id="wl-wrapper" class="woocommerce">

	<div class="row">
		<div class="col-lg-12">
			<div class="white-block block-pad log-in">
				<h1><?php the_title(); ?></h1>
			</div>
		</div>
	</div>
	
	
	<div class="row">
		<div class="col-md-12">
			
			<form class="wl-search-form" method="get">
		        <div class="col col-text">
		        	<label for="f-list"><?php _e("Find Someone's List", 'wc_wishlist'); ?></label>
		        </div>
		        <div class="col col-input">
			        <input type="text" name="f-list" id="f-list" class="find-input form-control" value="<?php echo (isset($_GET['f-list']) ? esc_attr($_GET['f-list']) : ''); ?>"  placeholder="<?php _e('Enter name or email', 'wc_wishlist'); ?>" />	
		        </div>
		        <div class="col col-search-submit">
		        	<input type="submit" class="button btn btn-default up" value="<?php _e('Search', 'wc_wishlist'); ?>" />
		        </div>
		    </form>
				
		</div>
	</div>
	
    
    <br />

    <div class="row">
    	<div class="col-md-12">
    <?php if (have_posts()) : ?>
		    	
        <?php if (isset($_GET['f-list']) && $_GET['f-list']) : ?>
            <?php printf(__('<a class="wl-clear-results up pull-right btn btn-black btn-sm" href="%s">Clear Results</a>'), WC_Wishlists_Pages::get_url_for('find-a-list')); ?>
            <p class="wl-results-msg">
            	<?php printf(__("We've found %s lists matching <strong>%s</strong>"), $wp_query->found_posts, esc_html($_GET['f-list'])); ?>
            </p> 
        <?php endif; ?>
        <table class="shop_table cart wl-table wl-manage wl-find-table my-lists-table" cellspacing="0">
            <thead>
                <tr>
                    <th class="product-name"><?php _e('List Name', 'wc_wishlist'); ?></th>
                    <th class="wl-pers-name"><?php _e('Name', 'wc_wishlist'); ?></th>
                    <th class="wl-date-added"><?php _e('Date Added', 'wc_wishlist'); ?></th>
                </tr>
            </thead>

            <?php
            while (have_posts()) : the_post();
                $list = new WC_Wishlists_Wishlist(get_the_ID());
                ?>
                <tr>
                    <td><a href="<?php $list->the_url_view(); ?>"><?php $list->the_title(); ?></a></td>
                    <td><?php echo esc_attr(get_post_meta($list->id, '_wishlist_first_name', true)) . ' ' . esc_attr(get_post_meta($list->id, '_wishlist_last_name', true)); ?></td>
                    <td class="wl-date-added"><?php echo date(get_option('date_format'), strtotime($list->post->post_date)); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <?php woocommerce_wishlists_nav('nav-below'); ?>
	        

    <?php elseif (isset($_GET['f-list'])): ?>
        <!-- results go down here -->
        <p><?php _e("We're sorry, we couldn't find a list for that name. Please double check your entry and try again.", 'wc_wishlist'); ?></p>
        <h2 class="wl-search-result"><?php _e('We found 0 matching lists', 'wc_wishlist'); ?></h2>
    <?php endif; ?>
    
		</div>
	</div>
</div>
<?php do_action('woocommerce_wishlists_after_wrapper'); ?>

<?php
# start: modified by Arlind Nushi
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi
?>