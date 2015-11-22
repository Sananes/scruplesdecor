<?php
/**
 *	The template for displaying the shop header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $nm_theme_options, $nm_globals, $nm_page_includes;

$header_class = ' ';

// Categories setup
if ( $nm_theme_options['shop_categories'] ) {
	$nm_page_includes['shop_categories'] = true;
	$show_categories = true;
} else {
	$show_categories = false;
	$header_class .= 'no-categories ';
}

// Filters setup
if ( $nm_theme_options['shop_filters'] ) {
	$nm_page_includes['shop_filters'] = true;
	$show_filters = true;
} else {
	$show_filters = false;
	$header_class .= 'no-filters ';
}

// Search
if ( ! $nm_globals['shop_search'] ) {
	$header_class .= 'no-search';
}
?>
    <div class="nm-shop-header<?php echo esc_attr( $header_class ); ?>">
        <div class="nm-shop-menu">
            <div class="nm-row">
                <div class="col-xs-12">
                    <ul id="nm-shop-filter-menu" class="nm-shop-filter-menu">
                        <?php if ( $show_categories ) : ?>
                        <li class="nm-shop-categories-btn-wrap" data-panel="cat">
                            <a href="#categories" class="invert-color"><?php esc_html_e( 'Categories', 'nm-framework' ); ?></a>
							<em id="nm-categories-count" class="count">&nbsp;</em>
                        </li>
                        <?php 
							endif;
							
							if ( $show_filters ) :
						?>
                        <li data-panel="filter">
                            <a href="#filter" class="invert-color"><?php esc_html_e( 'Filter', 'nm-framework' ); ?></a>
						</li>
                        <?php 
							endif;
							
							if ( $nm_globals['shop_search'] ) :
						?>
                        <li class="nm-shop-search-btn-wrap" data-panel="search">
                            <span>&frasl;</span>
                            <a href="#search" id="nm-shop-search-btn" class="invert-color"><?php esc_html_e( 'Search', 'nm-framework' ); ?></a>
                            <i class="nm-font nm-font-search-alt flip"></i>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <?php if ( $show_categories ) : ?>
                    <ul id="nm-shop-categories" class="nm-shop-categories <?php echo esc_attr( $nm_theme_options['shop_categories_layout'] ); ?>">
                        <?php nm_category_menu(); ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if ( $show_filters ) : ?>
        <div id="nm-shop-sidebar" class="nm-shop-sidebar">
            <div class="nm-shop-sidebar-inner">
                <div class="nm-row">
                    <div class="col-xs-12">
                        <ul id="nm-shop-widgets-ul" class="small-block-grid-<?php echo esc_attr( $nm_theme_options['shop_filters_columns'] ); ?>">
                            <?php
                                if ( is_active_sidebar( 'widgets-shop' ) ) {
                                    dynamic_sidebar( 'widgets-shop' );
								}
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div id="nm-shop-sidebar-layout-indicator"></div> <!-- Don't remove (used for testing sidebar/filters layout in JavaScript) -->
        </div>
        <?php endif; ?>
        
        <?php 
			// Search-form
			if ( $nm_globals['shop_search'] ) {
				wc_get_template( 'product-searchform_nm.php' );
			}
		?>
    </div>
