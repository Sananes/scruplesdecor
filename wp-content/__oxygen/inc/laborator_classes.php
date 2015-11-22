<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */



class Main_Menu_Walker extends Walker_Nav_Menu {
	
	
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element )
			return;

		//Add indicators for top level menu items with submenus
		$id_field = $this->db_fields['id'];
		
		if ( !empty( $children_elements[ $element->$id_field ] ) ) {
			if($depth == 0)
				$element->classes[] = 'has-sub';
			else
				$element->classes[] = 'has-sub-sub';
		}
		
		Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}