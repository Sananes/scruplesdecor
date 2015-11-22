<?php
/** @var $editor Vc_Frontend_Editor */
global $menu, $submenu, $parent_file, $post_ID, $post, $post_type;
$post_ID = $editor->post_id;
$post = $editor->post;
$post_type = $post->post_type;
$post_title = trim( $post->post_title );
$nonce_action = $nonce_action = 'update-post_' . $editor->post_id;
$user_ID = isset( $editor->current_user ) && isset( $editor->current_user->ID ) ? (int) $editor->current_user->ID : 0;
$form_action = 'editpost';
$menu = array();
add_thickbox();
wp_enqueue_media( array( 'post' => $editor->post_id ) );
require_once( $editor->adminFile( 'admin-header.php' ) );
vc_include_settings_preset_class();
?>
	<div id="vc_preloader"></div>
	<script type="text/javascript">
		document.getElementById( 'vc_preloader' ).style.height = window.screen.availHeight;
		var vc_mode = '<?php echo vc_mode() ?>',
			vc_iframe_src = '<?php echo esc_attr( $editor->url ); ?>';
	</script>
	<input type="hidden" name="vc_post_title" id="vc_title-saved" value="<?php echo esc_attr( $post_title ); ?>"/>
	<input type="hidden" name="vc_post_id" id="vc_post-id" value="<?php echo esc_attr( $editor->post_id ); ?>"/>
<?php
require_once vc_path_dir( 'EDITORS_DIR', 'navbar/class-vc-navbar-frontend.php' );
$nav_bar = new Vc_NavBar_Frontend( $post );
$nav_bar->render();
?>
	<div id="vc_inline-frame-wrapper"></div>
<?php
// Add element popup
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-add-element-box.php' );
$add_element_box = new Vc_Add_Element_Box( $editor );
$add_element_box->render();

// Edit form for mapped shortcode.
visual_composer()->editForm()->render();

// Templates manager old panel @deprecated and will be removed
visual_composer()->templatesEditor()->render();
// Templates manager new panel
// visual_composer()->templatesPanelEditor()->render();
visual_composer()->templatesPanelEditor()->renderUITemplate();

require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-post-settings.php' );
$post_settings = new Vc_Post_Settings( $editor );
$post_settings->renderUITemplate();
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-edit-layout.php' );
$edit_layout = new Vc_Edit_Layout();
$edit_layout->renderUITemplate();
vc_include_template( 'editors/partials/frontend_controls.tpl.php' );
?>
	<input type="hidden" name="vc_post_custom_css" id="vc_post-custom-css"
	       value="<?php echo esc_attr( $editor->post_custom_css ); ?>" autocomplete="off"/>
	<script type="text/javascript">
		var vc_user_mapper = <?php echo json_encode(WPBMap::getUserShortCodes()) ?>,
			vc_mapper = <?php echo json_encode(WPBMap::getShortCodes()) ?>,
			vc_settings_presets = <?php echo json_encode(Vc_Settings_Preset::listDefaultSettingsPresets()) ?>,
			vc_roles = <?php echo json_encode( array_merge( array( 'current_user' => $editor->current_user->roles ), (array) vc_settings()->get( 'groups_access_rules' ) ) ) ?>;
	</script>

	<script type="text/html" id="vc_settings-image-block">
		<li class="added">
			<div class="inner" style="width: 80px; height: 80px; overflow: hidden;text-align: center;">
				<img rel="<%= id %>" src="<%= url %>"/>
			</div>
			<a href="#" class="icon-remove"></a>
		</li>
	</script>
	<div style="height: 1px; visibility: hidden; overflow: hidden;">
		<?php

		// Disable notice in edit-form-advanced.php
		$is_IE = false;

		require_once ABSPATH . 'wp-admin/edit-form-advanced.php';

		// Fix: WP 4.0
		wp_dequeue_script( 'editor-expand' );

		do_action( 'vc_frontend_editor_render_template' );

		?>
	</div>
<?php require_once( $editor->adminFile( 'admin-footer.php' ) ); ?>