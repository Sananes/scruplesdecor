<?php

	/*
	 * NM - Post meta common
	 */
	
	/* Post meta: Verify save action */
	function nm_verify_save_action( $post_id, $meta_box_nonce_name ) {
		// NM: WP code - https://codex.wordpress.org/Function_Reference/add_meta_box
		
		/* We need to verify this came from our screen and with proper authorization, because the save_post action can be triggered at other times. */
	
		// Check if our nonce is set.
		if ( ! isset( $_POST[$meta_box_nonce_name] ) ) {
			return false;
		}
	
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST[$meta_box_nonce_name], 'nm-framework' ) ) {
			return false;
		}
	
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
	
		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return false;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return false;
			}
		}
		
		/* OK, it's safe for us to save the data now. */
		return true;
		
		// /NM: WP code
	}
	