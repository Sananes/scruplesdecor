<?php
/**
 * Class to create a custom layout control
 */
class Header_Layout_Picker_Storefront_Control extends WP_Customize_Control {

	/**
	* Render the content on the theme customizer page
	*/
	public function render_content() {
		?>
		<div style="overflow: hidden; zoom: 1;">
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

			<label style="width: 48%; float: left; margin-right: 3.8%; text-align: center; margin-bottom: 1.618em;">
				<img src="<?php echo plugins_url( '../assets/img/admin/compact-header.png', __FILE__ ); ?>" alt="Compact Header" style="display: block; width: 100%; margin-bottom: .618em" />
				<input type="radio" value="compact" style="margin: 5px 0 0 0;"name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); checked( $this->value(), 'compact' ); ?> />
				<br/>
			</label>
			<label style="width: 48%; float: right; text-align: center; margin-bottom: 1.618em;">
				<img src="<?php echo plugins_url( '../assets/img/admin/expanded-header.png', __FILE__ ); ?>" alt="Expanded Header" style="display: block; width: 100%; margin-bottom: .618em" />
				<input type="radio" value="expanded" style="margin: 5px 0 0 0;"name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); checked( $this->value(), 'expanded' ); ?> />
				<br/>
			</label>
			<label style="width: 48%; float: left; text-align: center; clear: both; margin-bottom: 1.618em;">
				<img src="<?php echo plugins_url( '../assets/img/admin/central-header.png', __FILE__ ); ?>" alt="Central Header" style="display: block; width: 100%; margin-bottom: .618em" />
				<input type="radio" value="central" style="margin: 5px 0 0 0;"name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); checked( $this->value(), 'central' ); ?> />
				<br/>
			</label>
			<label style="width: 48%; float: right; text-align: center; margin-bottom: 1.618em;">
				<img src="<?php echo plugins_url( '../assets/img/admin/inline-header.png', __FILE__ ); ?>" alt="Inline Header" style="display: block; width: 100%; margin-bottom: .618em" />
				<input type="radio" value="inline" style="margin: 5px 0 0 0;"name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); checked( $this->value(), 'inline' ); ?> />
				<br/>
			</label>
		</div>
		<?php
	}
}