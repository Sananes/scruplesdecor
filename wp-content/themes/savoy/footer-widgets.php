<?php
	global $nm_theme_options;
	
	// Layout classes
	$border_class = ( $nm_theme_options['footer_widgets_border'] ) ? ' has-border' : '';
	$row_class = ' nm-row-' . $nm_theme_options['footer_widgets_layout'];
	
	// Grid columns class
	$columns_medium = ( intval( $nm_theme_options['footer_widgets_columns'] ) < 2 ) ? '1' : '2';
	$columns_class = 'nm-footer-block-grid small-block-grid-1 medium-block-grid-' . $columns_medium . ' large-block-grid-' . $nm_theme_options['footer_widgets_columns'];
?>
	
	<div class="nm-footer-widgets<?php echo esc_attr( $border_class ); ?> clearfix">
    	<div class="nm-footer-widgets-inner">
            <div class="nm-row <?php echo esc_attr( $row_class ); ?>">
                <div class="col-xs-12">
                    <ul class="<?php echo esc_attr( $columns_class ); ?>">
                        <?php dynamic_sidebar( 'footer' ); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
