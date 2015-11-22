<?php
/**
 *	NM Widget: Color Filter List
 *
 *	Note: This is a modified version of the "WooCommerce Layered Nav" widget - All custom code is placed within "//NM" comments
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Widget_Color_Filter extends WC_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'nm_widget nm_widget_color_filter woocommerce widget_layered_nav';
		$this->widget_description = __( 'Shows "color" attributes in a widget which lets you narrow down the list of products when viewing products. ', 'woocommerce' );
		$this->widget_id          = 'nm_woocommerce_color_filter';
		$this->widget_name        = __( 'WooCommerce Color Filter', 'woocommerce' );

		parent::__construct();
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		// NM
		/*$this->init_settings();
		return parent::update( $new_instance, $old_instance );*/
		$instance = $old_instance;

		if ( empty( $new_instance['title'] ) ) {
			$new_instance['title'] = __( 'Color', 'nm-framework' );
		}

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['columns'] = strip_tags( $new_instance['columns'] );
		//$instance['attribute'] = stripslashes( $new_instance['attribute'] );
		$instance['attribute'] = 'color';
		$instance['query_type'] = stripslashes( $new_instance['query_type'] );
		$instance['colors'] = $new_instance['colors'];

		return $instance;
		// /NM
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance ) {
		// NM
		/*$this->init_settings();
		parent::form( $instance );*/
		$defaults = array(
			'title' 		=> '',
			'columns' 		=> '1',
			'attribute' 	=> 'color',
			'query_type'	=> 'and',
			'colors' 		=> ''
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label>
				<?php esc_html_e( 'Title', 'nm-framework' ); ?><br />
				<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</label>
		</p>
        <p>
        	<label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Columns', 'nm-framework' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>">
				<option value="1" <?php selected( $instance['columns'], '1' ); ?>><?php echo '1'; ?></option>
				<option value="2" <?php selected( $instance['columns'], '2' ); ?>><?php echo '2'; ?></option>
                <option value="small-2" <?php selected( $instance['columns'], 'small-2' ); ?>><?php echo '2 - On smaller browser sizes'; ?></option>
			</select>
		</p>
        <?php
		/* NM: This can be used to add support for multiple product attributes (it would also require AJAX replacing the terms when the select changes)
		<p>
        	<label for="<?php echo $this->get_field_id( 'attribute' ); ?>"><?php esc_html_e( 'Attribute', 'nm-framework' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'attribute' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'attribute' ) ); ?>">
                <?php
					$attribute_taxonomies = wc_get_attribute_taxonomies();
					$options = '';
					
					if ( $attribute_taxonomies ) {
						foreach ( $attribute_taxonomies as $tax ) {
							if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
								$options .= '<option name="' . $tax->attribute_name . '"' . selected( $tax->attribute_name, $instance['attribute'], false ) . '">' . $tax->attribute_name . '</option>';
							}
						}
					}
				
					echo $options;
				?>
            </select>
        </p>*/
        ?>
		<p>
        	<label for="<?php echo esc_attr( $this->get_field_id( 'query_type' ) ); ?>"><?php esc_html_e( 'Query type', 'nm-framework' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'query_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'query_type' ) ); ?>">
				<option value="and" <?php selected( $instance['query_type'], 'and' ); ?>><?php esc_html_e( 'AND', 'nm-framework' ); ?></option>
				<option value="or" <?php selected( $instance['query_type'], 'or' ); ?>><?php esc_html_e( 'OR', 'nm-framework' ); ?></option>
			</select>
		</p>
		<div class="nm-widget-attributes-table">
			<?php
				$terms = get_terms( 'pa_' . $instance['attribute'], array( 'hide_empty' => '0' ) );
							
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$id = 'widget-' . $this->id . '-';
					$name = 'widget-' . $this->id_base . '[' . $this->number . ']';
					$values = $instance['colors'];
					
					$output = sprintf( '<table><tr><th>%s</th><th>%s</th></tr>', esc_html__( 'Term', 'nm-framework' ), esc_html__( 'Color', 'nm-framework' ) );
					
					
					foreach ( $terms as $term ) {
						$id = $id . $term->term_id;
						
						$output .= '<tr>
							<td><label for="' . esc_attr( $id ) . '">' . esc_attr( $term->name ) . ' </label></td>
							<td><input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '[colors][' . esc_attr( $term->term_id ) . ']" value="' . ( isset( $values[$term->term_id] ) ? esc_attr( $values[$term->term_id] ) : '' ) . '" size="3" class="nm-widget-color-picker" /></td>
						</tr>';
					}
		
					$output .= '</table>';
					$output .= '<input type="hidden" name="' . esc_attr( $name ) . '[labels]" value="" />';
				} else {
					$output = '<span>No product attribute saved with the <strong>"color"</strong> slug yet. <br />Click <a href="http://docs.nordicmade.com/savoy/#shop-color-widget" target="_blank">here</a> for more info.</span>';
				}
			
				echo $output;
			?>
		</div>

		<input type="hidden" name="widget_id" value="widget-<?php echo esc_attr( $this->id ); ?>-" />
		<input type="hidden" name="widget_name" value="widget-<?php echo esc_attr( $this->id_base ); ?>[<?php echo esc_attr( $this->number ); ?>]" />
        <?php
		// /NM
	}

	/**
	 * Init settings after post types are registered
	 */
	// NM
	/*public function init_settings() {
		// Removed: Using custom options
	}*/
	// /NM

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		global $_chosen_attributes;

		extract( $args );

		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		$current_term 	= is_tax() ? get_queried_object()->term_id : '';
		$current_tax 	= is_tax() ? get_queried_object()->taxonomy : '';
		$title 			= apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		// NM
		//$taxonomy 		= isset( $instance['attribute'] ) ? wc_attribute_taxonomy_name($instance['attribute']) : '';
		$taxonomy 		= wc_attribute_taxonomy_name( 'color' );
		// /NM
		$query_type 	= isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';
		// NM
		//$display_type 	= isset( $instance['display_type'] ) ? $instance['display_type'] : 'list';
		// /NM
		
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return;
		}

	    $get_terms_args = array( 'hide_empty' => '1' );

		$orderby = wc_attribute_orderby( $taxonomy );

		switch ( $orderby ) {
			case 'name' :
				$get_terms_args['orderby']    = 'name';
				$get_terms_args['menu_order'] = false;
			break;
			case 'id' :
				$get_terms_args['orderby']    = 'id';
				$get_terms_args['order']      = 'ASC';
				$get_terms_args['menu_order'] = false;
			break;
			case 'menu_order' :
				$get_terms_args['menu_order'] = 'ASC';
			break;
		}

		$terms = get_terms( $taxonomy, $get_terms_args );

		if ( count( $terms ) > 0 ) {

			ob_start();

			$found = false;

			echo $before_widget . $before_title . $title . $after_title;

			// Force found when option is selected - do not force found on taxonomy attributes
			if ( ! is_tax() && is_array( $_chosen_attributes ) && array_key_exists( $taxonomy, $_chosen_attributes ) ) {
				$found = true;
			}
				
			// NM: Removed "display_type" if statement
			
			// List display
			// NM
			//echo "<ul>";
			$columns_class = 'no-col';
			if ( isset( $instance['columns'] ) && $instance['columns'] !== '1' ) {
				$columns_class = ( $instance['columns'] === '2' ) ? 'small-block-grid-2 has-col' : 'small-block-grid-2 medium-block-grid-1 has-col';
			}
			echo '<ul class="' . $columns_class . '">';
			// /NM

			foreach ( $terms as $term ) {

				// Get count based on current view - uses transients
				$transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_taxonomy_id ) );

				if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

					$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

					set_transient( $transient_name, $_products_in_term );
				}

				$option_is_set = ( isset( $_chosen_attributes[ $taxonomy ] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) );

				// skip the term for the current archive
				if ( $current_term == $term->term_id ) {
					continue;
				}

				// If this is an AND query, only show options with count > 0
				if ( $query_type == 'and' ) {

					$count = sizeof( array_intersect( $_products_in_term, WC()->query->filtered_product_ids ) );

					if ( $count > 0 && $current_term !== $term->term_id ) {
						$found = true;
					}

					if ( $count == 0 && ! $option_is_set ) {
						continue;
					}

				// If this is an OR query, show all options so search can be expanded
				} else {

						$count = sizeof( array_intersect( $_products_in_term, WC()->query->unfiltered_product_ids ) );

						if ( $count > 0 ) {
							$found = true;
						}

				}

				$arg = 'filter_' . sanitize_title( $instance['attribute'] );

				$current_filter = ( isset( $_GET[ $arg ] ) ) ? explode( ',', $_GET[ $arg ] ) : array();

				if ( ! is_array( $current_filter ) ) {
					$current_filter = array();
				}

				$current_filter = array_map( 'esc_attr', $current_filter );

				if ( ! in_array( $term->term_id, $current_filter ) ) {
					$current_filter[] = $term->term_id;
				}

				// Base Link decided by current page
				if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
					$link = home_url();
				} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id('shop') ) ) {
					$link = get_post_type_archive_link( 'product' );
				} else {
					$link = get_term_link( get_query_var('term'), get_query_var('taxonomy') );
				}

				// All current filters
				if ( $_chosen_attributes ) {
					foreach ( $_chosen_attributes as $name => $data ) {
						if ( $name !== $taxonomy ) {

							// Exclude query arg for current term archive term
							while ( in_array( $current_term, $data['terms'] ) ) {
								$key = array_search( $current_term, $data );
								unset( $data['terms'][$key] );
							}

							// Remove pa_ and sanitize
							$filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

							if ( ! empty( $data['terms'] ) ) {
								$link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
							}

							if ( $data['query_type'] == 'or' ) {
								$link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
							}
						}
					}
				}

				// Min/Max
				if ( isset( $_GET['min_price'] ) ) {
					$link = add_query_arg( 'min_price', $_GET['min_price'], $link );
				}

				if ( isset( $_GET['max_price'] ) ) {
					$link = add_query_arg( 'max_price', $_GET['max_price'], $link );
				}

				// Orderby
				if ( isset( $_GET['orderby'] ) ) {
					$link = add_query_arg( 'orderby', $_GET['orderby'], $link );
				}

				// Current Filter = this widget
				if ( isset( $_chosen_attributes[ $taxonomy ] ) && is_array( $_chosen_attributes[ $taxonomy ]['terms'] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) ) {
					
					$class = 'class="chosen"';
					
					// Remove this term is $current_filter has more than 1 term filtered
					if ( sizeof( $current_filter ) > 1 ) {
						$current_filter_without_this = array_diff( $current_filter, array( $term->term_id ) );
						$link = add_query_arg( $arg, implode( ',', $current_filter_without_this ), $link );
					}

				} else {

					$class = '';
					$link = add_query_arg( $arg, implode( ',', $current_filter ), $link );

				}

				// Search Arg
				if ( get_search_query() ) {
					$link = add_query_arg( 's', get_search_query(), $link );
				}

				// Post Type Arg
				if ( isset( $_GET['post_type'] ) ) {
					$link = add_query_arg( 'post_type', $_GET['post_type'], $link );
				}

				// Query type Arg
				if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[ $taxonomy ]['terms'] ) && is_array( $_chosen_attributes[ $taxonomy ]['terms'] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) ) ) {
					$link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );
				}
				
				echo '<li ' . $class . '>';
					
				echo ( $count > 0 || $option_is_set ) ? '<a href="' . esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ) . '">' : '<span>';
				
				// NM
				$color_val = isset( $instance['colors'][$term->term_id] ) ? $instance['colors'][$term->term_id] : '#e0e0e0';
				
				echo '<i style="background-color:' . esc_attr( $color_val ) . ';" class="nm-filter-color nm-filter-color-' . esc_attr( strtolower( $term->name ) ) . '"></i>';
				// /NM
				
				echo esc_attr( $term->name );
				
				echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
				
				echo ' <small class="count">' . $count . '</small></li>';

			}

			echo "</ul>";

			echo $after_widget;

			if ( ! $found ) {
				ob_end_clean();
			} else {
				echo ob_get_clean();
			}
		}
	}
}
