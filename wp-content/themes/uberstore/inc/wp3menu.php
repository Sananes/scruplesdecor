<?php
add_theme_support('nav-menus');
add_action('init','register_my_menus');

function register_my_menus() {
	register_nav_menus(
		array(
			'nav-menu' => __( 'Navigation Bar Menu',THB_THEME_NAME )
		)
	);
}

/* CUSTOM NAV WALKER */
class UberStoreNavDropdown extends Walker_Nav_Menu
{
	
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $display_depth = ($depth + 1);
        if($display_depth == '1'){$class_names = 'dropdown';}
        else {$class_names = 'dropdown-column';}
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<div class=".$class_names."><ul>\n";
    }

    function end_lvl( &$output, $depth = 1, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul></div>\n";
    }

    function start_el ( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

	    global $wp_query;
	    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
	    $class_names = $value = '';
	
	    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
	    $classes[] = 'menu-item-' . $item->ID;
	
	    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
	    $class_names = ' class="' . esc_attr( $class_names ) . '"';
	
	    $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
	    $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
	
	    $output .= $indent . '<li' . $id . $value . $class_names .'>';
	
	    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
	    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
	    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
	    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
	
			$heading = '';
	    if ( $depth == 0 ) {
	        if(strpos($class_names,'heading') !== false && $item->description !== ''){$heading = '<h6>'.$item->description.'</h6>';}
	    }
	
	    $description = '';
	    if(strpos($class_names,'image') !== false){$description = '<div class="dropdown-column"><img src="'.$item->description.'" alt=" "/></div>';}
	

		    $item_output = $args->before;
		    $item_output .= '<a'. $attributes .'>';
		    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		    $item_output .= $heading.$description;
		    $item_output .= '</a>';
		    $item_output .= $args->after;
	
	    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  	} 

}
?>