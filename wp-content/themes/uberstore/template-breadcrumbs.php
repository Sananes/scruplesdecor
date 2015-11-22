<?php global $woocommerce;
			$id = $wp_query->get_queried_object_id();
			$display_breadcrumbs = get_post_meta($id, 'display_breadcrumbs', true); 
			$postspage_id = get_option('page_for_posts'); ?>
<?php if(!is_singular('portfolio') && !is_singular('product') && ($display_breadcrumbs != 'off') && !is_404()) { ?>
<!-- Start Breadcrumbs -->
<div id="breadcrumbs">
<div class="row">
	<div class="twelve columns">
		<div class="main-header">
			<div class="row">
				<div class="six columns">
					<h1>
						<?php if($woocommerce && is_woocommerce()) {
										woocommerce_page_title(); 
									} else if (is_archive()|| is_search()){
										echo thb_title(array('title' => thb_which_archive())); 
									} else if (is_singular('post')) {
										echo thb_title(false,$postspage_id); 
									} else {
										echo thb_title(); 
									}
						?>
					
					</h1>
				</div>
				<div class="six columns hide-for-small">
				
						<?php if($woocommerce && is_woocommerce()) {
										$defaults = array(
										    'delimiter'  => '<span>/</span>',
										    'wrap_before'  => '<aside class="breadcrumb">',
										    'wrap_after' => '</aside>',
										    'before'   => '',
										    'after'   => '',
										    'home'    => __("Home", THB_THEME_NAME )
										);
										$args = wp_parse_args($defaults);
										woocommerce_get_template( 'global/breadcrumb.php', $args );
									} else {
										echo thb_breadcrumb();
									}
						?>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<!-- End Breadcrumbs -->
<?php } ?>