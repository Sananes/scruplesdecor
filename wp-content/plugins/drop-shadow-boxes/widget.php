<?php
add_action( 'widgets_init', 'dropshadowboxes_register_widget' );

if(!function_exists("dropshadowboxes_register_widget")){
function dropshadowboxes_register_widget() {
    register_widget( 'DropShadowBoxesWidget' );
	
}
}

if(!class_exists("DropShadowBoxesWidget")){
	class DropShadowBoxesWidget extends WP_Widget {

		function __construct() {

			$description = esc_html__( 'Drop Shadow Box Widget', 'gravityforms' );

			WP_Widget::__construct(
				'dropshadowboxes_widget',
				__( 'Drop Shadow Box', 'dropshadowboxes' ),
				array( 'classname' => 'dropshadowboxes_widget', 'description' => $description ),
				array( 'width' => 200, 'height' => 250, 'id_base' => 'dropshadowboxes_widget' )
			);

		}

		function widget( $args, $instance ) {

			extract( $args );
			echo $before_widget;
			$title = apply_filters('widget_title', $instance['title'] );
			
			
			if ( $title )
				echo $before_title . $title . $after_title;
			
			$align = $instance["align"];
			$width_number = $instance["width_number"];
			$height = $instance["height_number"] == "auto" ? "" : $instance["height_number"] . "px";
			

			$box_width_units = $instance["box_width_units"];
			$width = $width_number . $box_width_units;
			
			$background_color = $instance["background_color"];
			
			$border_width = $instance["border_width"];
			$border_color = $instance["border_color"];
			$rounded_corners = $instance["rounded_corners"];
			$rounded_corners = strtolower($rounded_corners) == "1" ? "true" : "false";
			
			$inside_shadow = $instance["inside_shadow"];
			$inside_shadow = strtolower($inside_shadow) == "1" ? "true" : "false";
			
			$outside_shadow = $instance["outside_shadow"];
			$outside_shadow = strtolower($outside_shadow) == "1" ? "true" : "false";
			
			$effect = $instance["effect"];
			$box_content = $instance["box_content"];
			
			$shortcode = "[dropshadowbox align=\"{$align}\" width=\"{$width}\" height=\"{$height}\" border_width=\"{$border_width}\" border_color=\"{$border_color}\" rounded_corners=\"{$rounded_corners}\" inside_shadow=\"{$inside_shadow}\" outside_shadow=\"{$outside_shadow}\" effect=\"{$effect}\" background_color=\"{$background_color}\"  ]{$box_content}[/dropshadowbox]";

			echo do_shortcode($shortcode);

			echo $after_widget;
			return;
		}

		function update( $new_instance, $old_instance ) {

			$instance = $old_instance;
			$instance["title"] = $new_instance["title"];
			$instance["align"] = $new_instance["align"];
			$instance["width_number"] = $new_instance["width_number"];
			$instance["height_number"] = $new_instance["height_number"];
			$instance["box_width_units"] = $new_instance["box_width_units"];
			$instance["background_color"] = $new_instance["background_color"];
			$instance["border_width"] = $new_instance["border_width"];
			$instance["border_color"] = $new_instance["border_color"];
			$instance["effect"] = $new_instance["effect"];
			$instance["rounded_corners"] = empty( $new_instance["rounded_corners"] ) ? "0" : $new_instance["rounded_corners"];
			$instance["inside_shadow"] = empty( $new_instance["inside_shadow"] ) ? "0" : $new_instance["inside_shadow"];
			$instance["outside_shadow"] = empty( $new_instance["outside_shadow"] ) ? "0" : $new_instance["outside_shadow"];

			$instance["box_content"] = $new_instance["box_content"];
			return $instance;
		}

		function form( $instance ) {
		
			$instance = wp_parse_args( (array) $instance, array(
				'title' => __("Title", "drop-shadow-boxes"),
				'align' => 'none',
				'width_number' => '100',
				'height_number' => 'auto',
				'box_width_units' => '%',
				'border_width' => '1',
				'background_color' => '#FFF',
				'border_color' => '#DDD',
				'rounded_corners' => '1',
				'inside_shadow' => '1',
				'outside_shadow' => '1',
				'effect' => 'lifted-both',
				'box_content' =>  __("Enter your content here.", "drop-shadow-boxes")
						
				) );
			$border_color = $instance['border_color'];
			$background_color = $instance['background_color'];
			?>
			<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready(function()
				{
					// colorpicker field
					jQuery('.dropshadoboxes-color-picker').each(function(){
						var $this = jQuery(this),
							id = $this.attr('rel');
						
							$this.farbtastic('#' + id);
							
					});
					
					jQuery('.dropshadoboxes-color-picker').hide();
					
					
					 
				});

				
				
			//]]>   
			</script>		
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e("Title:", "drop-shadow-boxes"); ?></label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'effect' ); ?>"><?php _e("Effect:", "drop-shadow-boxes"); ?></label>
				<select id="<?php echo $this->get_field_id( 'effect' ); ?>" name="<?php echo $this->get_field_name( 'effect' ); ?>" style="width:90%;">
					<option value="lifted-both" <?php echo $instance['effect'] == "lifted-both" ? 'selected="selected"' : '' ; ?>><?php _e("Lifted (Both)", "drop-shadow-boxes"); ?> </option>
					<option value="lifted-bottom-left" <?php echo $instance['effect'] == "lifted-bottom-left" ? 'selected="selected"' : '' ; ?>><?php _e("Lifted (Left)", "drop-shadow-boxes"); ?> </option>
					<option value="lifted-bottom-right" <?php echo $instance['effect'] == "lifted-bottom-right" ? 'selected="selected"' : '' ; ?>><?php _e("Lifted (Right)", "drop-shadow-boxes"); ?> </option>
					
				   <option value="curled" <?php echo $instance['effect'] == "curled" ? 'selected="selected"' : '' ; ?>><?php _e("Curled", "drop-shadow-boxes"); ?> </option>
				   <option value="perspective-left" <?php echo $instance['effect'] == "perspective-left" ? 'selected="selected"' : '' ; ?>><?php _e("Perspective (Left)", "drop-shadow-boxes"); ?> </option>
				   <option value="perspective-right" <?php echo $instance['effect'] == "perspective-right" ? 'selected="selected"' : '' ; ?>><?php _e("Perspective (Right)", "drop-shadow-boxes"); ?> </option>
				   <option value="raised" <?php echo $instance['effect'] == "raised" ? 'selected="selected"' : '' ; ?>><?php _e("Raised", "drop-shadow-boxes"); ?> </option>
				   <option value="vertical-curve-left" <?php echo $instance['effect'] == "vertical-curve-left" ? 'selected="selected"' : '' ; ?>><?php _e("Vertical Curve (Left)", "drop-shadow-boxes"); ?> </option>
				   <option value="vertical-curve-both" <?php echo $instance['effect'] == "vertical-curve-both" ? 'selected="selected"' : '' ; ?>><?php _e("Vertical Curve (Both)", "drop-shadow-boxes"); ?> </option>
				   <option value="horizontal-curve-bottom" <?php echo $instance['effect'] == "horizontal-curve-bottom" ? 'selected="selected"' : '' ; ?>><?php _e("Horizontal Curve (Bottom)", "drop-shadow-boxes"); ?> </option>
				   <option value="horizontal-curve-both" <?php echo $instance['effect'] == "horizontal-curve-both" ? 'selected="selected"' : '' ; ?>><?php _e("Horizontal Curve (Both)", "drop-shadow-boxes"); ?> </option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'align' ); ?>"><?php _e("Alignment:", "drop-shadow-boxes"); ?></label>
				<select id="<?php echo $this->get_field_id( 'align' ); ?>" name="<?php echo $this->get_field_name( 'align' ); ?>" style="width:90%;">
					<option value="none" <?php echo $instance['align'] == "none" ? 'selected="selected"' : '' ; ?>><?php _e("None", "drop-shadow-boxes"); ?> </option>
					<option value="left" <?php echo $instance['align'] == "left" ? 'selected="selected"' : '' ; ?>><?php _e("Left", "drop-shadow-boxes"); ?> </option>
					<option value="right" <?php echo $instance['align'] == "right" ? 'selected="selected"' : '' ; ?>><?php _e("Right", "drop-shadow-boxes"); ?> </option>
					<option value="center" <?php echo $instance['align'] == "center" ? 'selected="selected"' : '' ; ?>><?php _e("Center", "drop-shadow-boxes"); ?> </option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'width_number' ); ?>"><?php _e("Width:", "drop-shadow-boxes"); ?></label><br/ >
				<input id="<?php echo $this->get_field_id( 'width_number' ); ?>" name="<?php echo $this->get_field_name( 'width_number' ); ?>" value="<?php echo $instance['width_number']; ?>" class="small-text"/>
				<select id="<?php echo $this->get_field_id( 'box_width_units' ); ?>" name="<?php echo $this->get_field_name( 'box_width_units' ); ?>">
					<option value="px" <?php echo $instance['box_width_units'] == "px" ? 'selected="selected"' : '' ; ?>><?php _e("pixels", "drop-shadow-boxes"); ?> </option>
					<option value="%" <?php echo $instance['box_width_units'] == "%" ? 'selected="selected"' : '' ; ?>><?php _e("%", "drop-shadow-boxes"); ?> </option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'height_number' ); ?>"><?php _e("Height:", "drop-shadow-boxes"); ?></label><br/ >
				<input id="<?php echo $this->get_field_id( 'height_number' ); ?>" name="<?php echo $this->get_field_name( 'height_number' ); ?>" value="<?php echo $instance['height_number']; ?>" class="small-text"/>
				<?php _e("pixels", "drop-shadow-boxes"); ?>
			</p>
			<p>

				<label for="<?php echo $this->get_field_id( 'border_width' ); ?>"><?php _e("Border:", "drop-shadow-boxes"); ?></label><br/ >
				<input id="<?php echo $this->get_field_id( 'border_width' ); ?>" name="<?php echo $this->get_field_name( 'border_width' ); ?>" value="<?php echo $instance['border_width']; ?>" class="small-text"/><?php _e("pixels", "drop-shadow-boxes"); ?>
				
				
				<input class="small-text" id="<?php echo $this->get_field_id('border_color'); ?>" name="<?php echo $this->get_field_name('border_color'); ?>" type="text" value="<?php if($border_color) { echo $border_color; } else { echo '#DDD';  }?>"  onclick="var $this = jQuery(this);id = $this.attr('id');var obj = jQuery('.dropshadoboxes-color-picker[rel=' + id + ']'); if(!obj.is(':visible')) {var a = obj.show('slow');} else {var a = obj.hide('slow');}" />
				<div class="dropshadoboxes-color-picker" rel="<?php echo $this->get_field_id('border_color'); ?>"></div>
			</p>
			<p>

				<label for="<?php echo $this->get_field_id( 'background_color' ); ?>"><?php _e("Background:", "drop-shadow-boxes"); ?></label><br/ >
				
				
		 
				  <input class="widefat" id="<?php echo $this->get_field_id('background_color'); ?>" name="<?php echo $this->get_field_name('background_color'); ?>" type="text" value="<?php if($background_color) { echo $background_color; } else { echo '#fff';  }?>" onclick="var $this = jQuery(this);id = $this.attr('id');var obj = jQuery('.dropshadoboxes-color-picker[rel=' + id + ']'); if(!obj.is(':visible')) {var a = obj.show('slow');} else {var a = obj.hide('slow');}" />
				<div class="dropshadoboxes-color-picker" rel="<?php echo $this->get_field_id('background_color'); ?>"></div>
				</p>
	 
			</p>
			<p>		
				<input type="checkbox" name="<?php echo $this->get_field_name( 'rounded_corners' ); ?>" id="<?php echo $this->get_field_id( 'rounded_corners' ); ?>" <?php checked($instance['rounded_corners']); ?> value="1" /> <label for="<?php echo $this->get_field_id( 'rounded_corners' ); ?>"><?php _e("Rounded corners", "drop-shadow-boxes"); ?></label> <br />
				<input type="checkbox" name="<?php echo $this->get_field_name( 'inside_shadow' ); ?>" id="<?php echo $this->get_field_id( 'inside_shadow' ); ?>" <?php checked($instance['inside_shadow']); ?> value="1" /> <label for="<?php echo $this->get_field_id( 'inside_shadow' ); ?>"><?php _e("Inside shadow", "drop-shadow-boxes"); ?></label> <br />
				<input type="checkbox" name="<?php echo $this->get_field_name( 'outside_shadow' ); ?>" id="<?php echo $this->get_field_id( 'outside_shadow' ); ?>" <?php checked($instance['outside_shadow']); ?> value="1" /> <label for="<?php echo $this->get_field_id( 'outside_shadow' ); ?>"><?php _e("Outside shadow", "drop-shadow-boxes"); ?></label> <br />
			</p>
			<p>	
				<textarea style="width:100%" name="<?php echo $this->get_field_name( 'box_content' ); ?>" id="<?php echo $this->get_field_id( 'box_content' ); ?>" ><?php echo $instance['box_content']; ?></textarea> 
			</p>

		<?php
		}
	}
}