<?php
	/*
	 *	WooCommerce product category: Custom "Categories Grid" description field
	 */
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	
	/* Product category - Add: Include "Categories Grid" description field */
	function nm_product_category_add_description_field() {
		?>
        <div class="form-field term-description-wrap">
            <label for="nm_categories_description"><?php esc_html_e( 'Categories Grid Description', 'nm-framework' ); ?></label>
            <textarea cols="40" rows="5" name="nm_categories_description" id="nm-categories-description"></textarea>
            <p><?php esc_html_e( 'The description used for the "Categories Grid" element.','nm-framework' ); ?></p>
        </div>
	<?php
	}
	add_action( 'product_cat_add_form_fields', 'nm_product_category_add_description_field', 10, 2 );
	
	
	/* Product category - Edit: Include "Categories Grid" description field */
	function nm_product_category_add_edit_description_field( $term ) {
		// Get custom field's saved data
		$saved_data = get_option( 'nm_taxonomy_product_cat_' . $term->term_id . '_description' ); ?>
        
        <tr class="form-field term-description-wrap">
			<th scope="row"><label for="nm_categories_description"><?php esc_html_e( 'Categories Grid Description', 'nm-framework' ); ?></label></th>
			<td>
            	<textarea cols="50" rows="5" name="nm_categories_description" id="nm-categories-description" class="large-text"><?php echo ( $saved_data ) ? esc_attr( $saved_data ) : '' ;?></textarea>
				<p class="description"><?php esc_html_e( 'The description used for the "Categories Grid" element.','nm-framework' ); ?></p>
			</td>
		</tr>
	<?php
	}
	add_action( 'product_cat_edit_form_fields', 'nm_product_category_add_edit_description_field', 10, 2 );
	
	
	/* Product category - Save: Save "Categories Grid" description field data */
	function nm_product_categories_save_description_field( $term_id ) {
		if ( isset( $_POST['nm_categories_description'] ) ) {
			// Save custom field data
			update_option( 'nm_taxonomy_product_cat_' . $term_id . '_description', $_POST['nm_categories_description'] );
		}
	}
	add_action( 'create_product_cat', 'nm_product_categories_save_description_field', 10, 2 );
	add_action( 'edited_product_cat', 'nm_product_categories_save_description_field', 10, 2 );
	