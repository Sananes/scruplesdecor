<?php
	
	/* Helper: Get Contact Form 7 forms */
	function nm_get_cf7_forms() {
		$cf7_forms = get_posts( 'post_type="wpcf7_contact_form"&numberposts=-1' );
		
		$forms = array();
		
		if ( $cf7_forms ) {
			foreach ( $cf7_forms as $form )
				$forms[$form->post_title] = $form->ID;
		} else {
			$forms[__( 'No contact forms found', 'nm-framework-admin' )] = 0;
		}
		
		return $forms;
	}
	
	
	// VC element: nm_contact_form_7
	vc_map( array(
		'name' 			=> __( 'Contact Form 7', 'nm-framework-admin' ),
		'category' 		=> __( 'Content', 'nm-framework-admin' ),
		'description'	=> __( 'Include "Contact Form 7" form', 'nm-framework-admin' ),
		'base' 			=> 'nm_contact_form_7',
		'icon' 			=> 'nm_contact_form_7',
		'params' 		=> array(
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Form title', 'nm-framework-admin' ),
				'param_name'	=> 'title',
				'admin_label'	=> true,
				'description'	=> __( 'Form title (leave blank if no title is needed).', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Select form', 'nm-framework-admin' ),
				'param_name' 	=> 'id',
				'description'	=> __( 'Select a previously created contact-form from the list.', 'nm-framework-admin' ),
				'value' 		=> nm_get_cf7_forms()
			)
		)
	) );