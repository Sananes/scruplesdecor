<?php
	global $nm_theme_options, $nm_globals;
	
	$searchClass = ( $nm_globals['header_shop_search'] ) ? ' nm-header-search' : 'nm-shop-search';
?>

<div id="nm-shop-search" class="nm-shop-search<?php echo esc_attr( $searchClass ); ?>">
    <div class="nm-row">
        <div class="col-xs-12">
            <div class="nm-shop-search-inner">
                <div class="nm-shop-search-input-wrap">
                    <a href="#" id="nm-shop-search-close"><i class="nm-font nm-font-close2"></i></a>
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <input type="text" id="nm-shop-search-input" autocomplete="off" value="" name="s" placeholder="<?php esc_html_e( 'Search store', 'nm-framework' ); ?>" />
                        <input type="hidden" name="post_type" value="product" />
                    </form>
                </div>
                
                <div id="nm-shop-search-notice"><span><?php wp_kses( _e( 'press <u>Enter</u> to search', 'nm-framework' ), array( 'u' => array() ) ); ?></span></div>
            </div>
        </div>
    </div>
</div>
