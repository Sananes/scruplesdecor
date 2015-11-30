<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$tab = preg_replace( '/^vc\-/', '', $page->getSlug() );
$use_custom = get_option( vc_settings()->getFieldPrefix() . 'use_custom' );
$css = ( ( 'color' === $tab ) && $use_custom ) ? ' color_enabled' : '';
$dev_environment = Vc_License::isDevEnvironment();

$classes = 'vc_settings-tab-content vc_settings-tab-content-active ' . esc_attr( $css );
if ( 'updater' === $tab && $dev_environment ) {
	$classes .= ' hidden';
}
?>
<script type="text/javascript">
	var vcAdminNonce = '<?php echo vc_generate_nonce( 'vc-admin-nonce' ); ?>';
</script>
<?php if ( 'updater' === $tab && $dev_environment ) :  ?>
	<div class="tab_intro" data-vc-ui-element="updater-tab-notice">
		<p>
			<?php _e( 'It is optional to activate license on localhost development environment. You can still activate license on localhost to receive plugin updates.', 'js_composer' ) ?>
		</p>

		<button type="button"
		        class="button button-primary"
		        data-vc-ui-element="license-form-show-button"
		        data-vc-target="[data-vc-ui-element=settings-tab-updater]"
		        data-vc-container="[data-vc-ui-element=updater-tab-notice]"
			>
			<?php echo __( 'Show License Activation Form', 'js_composer' ) ?>
		</button>
	</div>
<?php else : ?>
	<?php ?>
<?php endif ?>
<form action="options.php"
      method="post"
      id="vc_settings-<?php echo $tab ?>"
      data-vc-ui-element="settings-tab-<?php echo $tab ?>"
      class="<?php echo $classes ?>"
	<?php echo apply_filters( 'vc_setting-tab-form-' . $tab, '' ) ?>
	>
	<?php settings_fields( vc_settings()->getOptionGroup() . '_' . $tab ) ?>
	<?php do_settings_sections( vc_settings()->page() . '_' . $tab ) ?>
	<?php if ( 'general' === $tab && vc_pointers_is_dismissed() ) :  ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Guide tours', 'js_composer' ) ?></th>
				<td>
					<a href="#" class="button vc_pointers-reset-button"
					   id="vc_settings-vc-pointers-reset"
					   data-vc-done-txt="<?php _e( 'Done', 'js_composer' ) ?>"><?php _e( 'Reset', 'js_composer' ) ?></a>

					<p class="description indicator-hint"><?php _e( 'Guide tours are shown in VC editors to help you to start working with editors. You can see them again by clicking button above.', 'js_composer' ) ?></p>
				</td>
			</tr>
		</table>
	<?php endif ?>
	<?php
	$submit_button_attributes = array();
	$submit_button_attributes = apply_filters( 'vc_settings-tab-submit-button-attributes', $submit_button_attributes, $tab );
	$submit_button_attributes = apply_filters( 'vc_settings-tab-submit-button-attributes-' . $tab, $submit_button_attributes, $tab );
	$license_activation_key = vc_license()->deactivation();
	if ( 'updater' === $tab && ! empty( $license_activation_key ) ) { $submit_button_attributes['disabled'] = 'true'; }
	?>
	<?php if ( 'updater' !== $tab ) :  ?>
		<?php submit_button( __( 'Save Changes', 'js_composer' ), 'primary', 'submit_btn', true, $submit_button_attributes ); ?>
	<?php endif ?>
	<input type="hidden" name="vc_action" value="vc_action-<?php echo $tab; ?>"
	       id="vc_settings-<?php echo $tab; ?>-action"/>
	<?php if ( 'color' === $tab ) :  ?>
		<a href="#" class="button vc_restore-button"
		   id="vc_settings-color-restore-default"><?php _e( 'Restore Default', 'js_composer' ) ?></a>
	<?php endif ?>
	<?php if ( 'updater' === $tab ) :  ?>
		<input type="hidden" id="vc_settings-license-status" name="vc_license_status"
		       value="<?php echo empty( $license_activation_key ) ? 'not_activated' : 'activated' ?>"/>
		<a href="#" class="button button-primary vc_activate-license-button"
		   id="vc_settings-activate-license"><?php empty( $license_activation_key ) ? _e( 'Activate License', 'js_composer' ) : _e( 'Deactivate License', 'js_composer' ) ?></a>
		<span class="vc_updater-spinner-wrapper" style="display: none;" id="vc_updater-spinner"><img
				src="<?php echo get_site_url() ?>/wp-admin/images/wpspin_light.gif"/></span>
	<?php endif ?>
</form>
