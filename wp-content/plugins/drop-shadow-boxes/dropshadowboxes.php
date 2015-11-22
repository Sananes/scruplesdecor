<?php
/*
Plugin Name: Drop Shadow Boxes
Plugin URI: http://www.stevenhenty.com/products/wordpress-plugins/drop-shadow-boxes/
Description: Drop Shadow Boxes provides an easy way to highlight important content on your posts and pages. Includes a shortcode builder with a preview so you can test your box before adding it.
Version: 1.5.4
Author: Steven Henty
Contributors: stevehenty
Donate link: http://www.stevenhenty.com/products/wordpress-plugins/donate/
Author URI: http://www.stevenhenty.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: drop-shadow-boxes

------------------------------------------------------------------------
Copyright 2012-2014 Steven Henty

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/


//------------------------------------------


if (!defined("DSB_CURRENT_PAGE"))
    define("DSB_CURRENT_PAGE", basename($_SERVER['PHP_SELF']));
if (!defined("IS_ADMIN"))
    define("IS_ADMIN", is_admin());

add_action('init', array('DropShadowBoxes', 'init'));


//------------------------------------------

if (!class_exists('DropShadowBoxes')) {
    class DropShadowBoxes {
        public static $version = "1.5.3";

        //Plugin starting point. Will load appropriate files
        public static function init() {

            load_plugin_textdomain('drop-shadow-boxes', false, '/drop-shadow-boxes/languages');

            if (IS_ADMIN) {
                if (in_array(DSB_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php', 'widgets.php', 'admin.php'))) {

                    add_action('admin_enqueue_scripts', array('DropShadowBoxes', 'load_color_picker_style'));
                    add_action('admin_enqueue_scripts', array('DropShadowBoxes', 'load_color_picker_script'));
                }
                if (in_array(DSB_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php'))) {
                    //Adding "box" button
                    add_action('media_buttons', array('DropShadowBoxes', 'add_box_button'), 20);
                    add_action('admin_footer', array('DropShadowBoxes', 'add_mce_popup'));
                    add_action('admin_enqueue_scripts', array('DropShadowBoxes', 'load_styles'));
                }

                add_action('wp_ajax_dropshadowboxes_ajax_get_preview', array('DropShadowBoxes', 'dropshadowboxes_ajax_get_preview'));


            } else {

                add_filter('the_posts', array('DropShadowBoxes', 'conditionally_add_scripts_and_styles')); // the_posts gets triggered before wp_head

                if (is_active_widget('', '', 'dropshadowboxes_widget')) { // check if search widget is used
                    wp_enqueue_style('dropshadowboxes_css', plugins_url('css/dropshadowboxes.css', __FILE__), null, self::$version);
                }

            }

            add_shortcode('dropshadowbox', array('DropShadowBoxes', 'render_shortcode'));
            add_shortcode('dropshadowboxes', array('DropShadowBoxes', 'render_shortcode'));


        } //end function init

        public static function load_color_picker_script() {
            wp_enqueue_script('farbtastic');
        }

        public static function load_color_picker_style() {
            wp_enqueue_style('farbtastic');
        }


        public static function load_styles() {
            wp_enqueue_style('dropshadowboxes_css', plugins_url('css/dropshadowboxes.css', __FILE__), null, self::$version);
        }


        public static function dropshadowboxes_ajax_get_preview() {
            // TO DO - check ajax referrer
            $shortcode = $_POST['shortcode'];

            $outputarray = array();

            $outputarray["preview"] = do_shortcode(stripslashes($shortcode));
            $outputarray["status"]  = "ok";
            echo json_encode($outputarray);
            die();

        }


        public static function conditionally_add_scripts_and_styles($posts) {
            if (empty($posts))
                return $posts;

            $shortcode_found = false;
            foreach ($posts as $post) {
                if (stripos($post->post_content, '[dropshadowbox') !== false) {
                    $shortcode_found = true;
                    break;
                }
            }

            if ($shortcode_found) {
                wp_enqueue_style('dropshadowboxes_css', plugins_url('css/dropshadowboxes.css', __FILE__), null, self::$version);
            }

            return $posts;
        }

        public static function render_shortcode($attributes, $content = null) {

            extract(shortcode_atts(array(
                'align'               => "none",
                'width'               => "",
                'max_width'           => "",
                'min_width'           => "",
                'margin'              => "",
                'height'              => "",
                'background_color'    => "white",
                'border_width'        => "2",
                'border_color'        => "#DDD",
                'rounded_corners'     => true,
                'inside_shadow'       => true,
                'outside_shadow'      => true,
                'effect'              => "lifted-both",
                'inline_styles'       => false,
                'effect_shadow_color' => "",
                'padding'             => ""

            ), $attributes));

            $rounded_corners = strtolower($rounded_corners) == "false" ? false : true;
            $inside_shadow   = strtolower($inside_shadow) == "false" ? false : true;
            $outside_shadow  = strtolower($outside_shadow) == "false" ? false : true;
            $inline_styles   = strtolower($inline_styles) == "true" ? true : false;
            $effect          = strtolower($effect);

            $box_classes       = "";
            $box_style         = "";
            $container_classes = "";
            $container_style   = "";

            if ($align == "left") {
                $container_classes .= "dropshadowboxes-left ";
                if($width !== ''){
                    $container_style .= 'width:' . $width . ';';
                }

            } elseif ($align == "right") {
                $container_classes .= "dropshadowboxes-right ";
                if($width !== ''){
                    $container_style .= 'width:' . $width . ';';
                }
            } elseif ($align == "none") {
                if($width !== ''){
                    $container_style .= 'width:' . $width . ';';
                }
            } elseif ($align == "center") {
                $container_classes .= "dropshadowboxes-center ";
                $container_style .= 'width:100%;';
                if($width !== ''){
                    $box_style .= 'width:' . $width . ';';
                }
            }

            if ($rounded_corners === true)
                $box_classes .= "dropshadowboxes-rounded-corners ";


            if ($inside_shadow === true && $outside_shadow === true)
                $box_classes .= "dropshadowboxes-inside-and-outside-shadow ";
            elseif ($inside_shadow === true)
                $box_classes .= "dropshadowboxes-inside-shadow ";
            elseif ($outside_shadow === true)
                $box_classes .= "dropshadowboxes-outside-shadow ";


            if ($effect == "lifted")
                $box_classes .= "dropshadowboxes-lifted-both ";
            elseif ($effect == "lifted-both")
                $box_classes .= "dropshadowboxes-lifted-both ";
            elseif ($effect == "lifted-bottom-left")
                $box_classes .= "dropshadowboxes-lifted-bottom-left ";
            elseif ($effect == "lifted-bottom-right")
                $box_classes .= "dropshadowboxes-lifted-bottom-right ";
            elseif ($effect == "curled")
                $box_classes .= "dropshadowboxes-curled ";
            elseif ($effect == "perspective-left")
                $box_classes .= "dropshadowboxes-perspective-left ";
            elseif ($effect == "perspective-right")
                $box_classes .= "dropshadowboxes-perspective-right ";
            elseif ($effect == "raised") {

                if ($inside_shadow === false && $outside_shadow === false) {
                    $box_classes .= "dropshadowboxes-raised-no-inside-shadow-no-outside-shadow ";
                } elseif ($inside_shadow === true && $outside_shadow === false) {
                    $box_classes .= "dropshadowboxes-raised-with-inside-shadow-no-outside-shadow ";
                } elseif ($inside_shadow === false && $outside_shadow === true) {
                    $box_classes .= "dropshadowboxes-raised-no-inside-shadow-with-outside-shadow ";
                } elseif ($inside_shadow === true && $outside_shadow === true) {
                    $box_classes .= "dropshadowboxes-raised-with-inside-shadow-with-outside-shadow ";
                }

            } elseif ($effect == "vertical-curve-left")
                $box_classes .= "dropshadowboxes-curved ";
            elseif ($effect == "vertical-curve-both")
                $box_classes .= "dropshadowboxes-curved dropshadowboxes-curved-vertical-2 ";
            elseif ($effect == "horizontal-curve-bottom")
                $box_classes .= "dropshadowboxes-curved dropshadowboxes-curved dropshadowboxes-curved-horizontal-1 ";
            elseif ($effect == "horizontal-curve-both")
                $box_classes .= "dropshadowboxes-curved dropshadowboxes-curved dropshadowboxes-curved-horizontal-2 ";
            elseif ($effect == "none")
                $box_classes = "";


            if (!empty ($effect_shadow_color))
                $box_classes .= "dropshadowboxes-effect-" . $effect_shadow_color;
            elseif($effect != "none")
                $box_classes .= "dropshadowboxes-effect-default";

            if($padding !== ''){
                $padding = "padding:{$padding};";
            }

            if($max_width !== ''){
                $max_width = "max-width:{$max_width};";
            }

            if($min_width !== ''){
                $min_width = "min-width:{$min_width};";
            }

            if($margin !== ''){
                $margin = "margin:{$margin};";
            }

            $output = "";
            if ($inline_styles){
                $output = "<script>" . file_get_contents(plugins_url('css/dropshadowboxes.css', __FILE__)) . "</script>";
            }

            $content = do_shortcode($content);
            $output .= "<div class='dropshadowboxes-container {$container_classes}' style='$container_style'>
                            <div class='dropshadowboxes-drop-shadow {$box_classes}' style='{$box_style} border: {$border_width}px solid {$border_color}; height:{$height}; background-color:{$background_color}; {$padding} {$max_width} {$min_width} {$margin}'>
                            {$content}
                            </div>
                        </div>";

            return $output;

        } // end function gf_polls_poll_shortcode

        //Action target that adds the "Insert dropshadowbox" button to the post/page edit screen
        public static function add_box_button() {

            $is_post_edit_page = in_array(DSB_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php'));
            if (!$is_post_edit_page)
                return $context;

            // do a version check for the new 3.5 UI
            $version = get_bloginfo('version');

            if ($version < 3.5) {
                // show button for v 3.4 and below
                $image_btn = self::get_base_url() . "/images/box-button.png";
                $out       = '<a href="#TB_inline?&height=555&width=640&inlineId=add_dropshadowbox" class="thickbox" id="add_box" title="' . __("Add Drop-Shadow Box", 'drop-shadow-boxes') . '"><img src="' . $image_btn . '" alt="' . __("Add Drop-Shadow Box", 'drop-shadow-boxes') . '" /></a>';

            } else {
                // display button matching new UI
                $out = '<style>.dropshadowboxes_media_icon{
					background:url(' . self::get_base_url() . '/images/box-button.png) no-repeat top left;
					display: inline-block;
					height: 16px;
					margin: 0 2px 0 0;
					vertical-align: text-top;
					width: 16px;
					}
					.wp-core-ui a.dropshadowboxes_media_link{
					 padding-left: 0.4em;
					}
				 </style>
				  <a href="#TB_inline?&height=555&width=640&inlineId=add_dropshadowbox" class="thickbox button dropshadowboxes_media_link" id="add_box" title="' . __("Add Drop Shadow Box", 'drop-shadow-boxes') . '"><span class="dropshadowboxes_media_icon "></span> ' . __("Add Box", "drop-shadow-boxes") . '</a>';
            }

            echo $out;
        }

        //Action target that displays the popup to insert a form to a post/page
        public static function add_mce_popup() {
            ?>
            <script>
                function BuildDropShadowBoxShortcode() {
                    var box_alignment = jQuery("#box_alignment").val();
                    var box_effect = jQuery("#box_effect").val();
                    var box_width = jQuery("#box_width").val();
                    var box_height = jQuery("#box_height").val();
                    box_height = box_height == 'auto' ? '' : box_height + 'px';
                    var box_background_color = jQuery("#box_background_color").val();

                    var box_width_units = box_width == 'auto' ? '' : jQuery("#box_width_units").val();
                    var border_width = jQuery("#border_width").val();
                    var border_color = jQuery("#border_color").val();

                    var rounded_corners = jQuery("#rounded_corners").is(":checked");
                    var inside_shadow = jQuery("#inside_shadow").is(":checked");
                    var outside_shadow = jQuery("#outside_shadow").is(":checked");

                    var box_background_color_qs = "background_color=\"" + box_background_color + "\" ";
                    var box_alignment_qs = "align=\"" + box_alignment + "\" ";
                    var box_effect_qs = "effect=\"" + box_effect + "\" ";
                    var box_width_qs = "width=\"" + box_width + box_width_units + "\" ";
                    var border_width_qs = "border_width=\"" + border_width + "\" ";
                    var box_height_qs = "height=\"" + box_height + "\" ";
                    var border_color_qs = "border_color=\"" + border_color + "\" ";

                    var rounded_corners_qs = !rounded_corners ? "rounded_corners=\"false\" " : "";
                    var inside_shadow_qs = !inside_shadow ? "inside_shadow=\"false\" " : "";
                    var outside_shadow_qs = !outside_shadow ? "outside_shadow=\"false\" " : "";

                    var box_content = jQuery("#box_content").val();

                    return "[dropshadowbox " + box_alignment_qs + box_effect_qs + box_width_qs + box_height_qs + box_background_color_qs + border_width_qs + border_color_qs + rounded_corners_qs + inside_shadow_qs + outside_shadow_qs + "]" + box_content + "[/dropshadowbox]";
                }

                function SendDropShadowShortCodeToEditor() {
                    window.send_to_editor(BuildDropShadowBoxShortcode());
                }

                function RefreshPreview() {

                    jQuery.ajax({
                        url     : ajaxurl,
                        type    : 'POST',
                        dataType: 'json',
                        data    : 'action=dropshadowboxes_ajax_get_preview&shortcode=' + BuildDropShadowBoxShortcode(),
                        success : function (results) {
                            if (results === -1) {
                                //permission denied
                            }
                            else {

                                var ajaxresults = results;

                                jQuery("#dropshadowboxes_preview-placeholder").html(ajaxresults.preview);

                            }

                        }

                    });

                }

                jQuery(document).ready(function () {
                    // colorpicker field
                    jQuery('.dropshadowboxes-color-picker').each(function () {
                        var $this = jQuery(this),
                            id = $this.attr('rel');


                        $this.farbtastic('#' + id);

                    });

                    jQuery('.dropshadowboxes-color-picker').hide();


                });
                function DSB_open_color_picker(id) {

                    var input_position = jQuery('#' + id).position();
                    var picker = jQuery('.dropshadowboxes-color-picker[rel=' + id + ']');
                    if (!picker.is(':visible')) {
                        picker.css('left', input_position.left);
                        var a = picker.show('slow');
                    } else {
                        var a = picker.hide('slow');
                    }
                }
            </script>
            <div id="add_dropshadowbox" style="display:none;width:640px;overflow:auto">
                <div id="dropshadowbox_shortcode_builder_container" class="wrap">

                    <div style="padding:15px 15px 0 15px;">
                        <h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;margin-top:0"><?php _e("Insert a Drop-Shadow Box", "drop-shadow-boxes"); ?></h3>
								<span>
									<?php _e("Select the options below for your drop-shadow box.", "drop-shadow-boxes"); ?>
								</span>
                    </div>
                    <div style="padding:15px 15px 0 15px;"><?php _e("Effect:", "drop-shadow-boxes"); ?>
                        <select id="box_effect">
                            <option value="lifted-both"><?php _e("Lifted (Both)", "drop-shadow-boxes"); ?> </option>
                            <option
                                value="lifted-bottom-left"><?php _e("Lifted (Left)", "drop-shadow-boxes"); ?> </option>
                            <option
                                value="lifted-bottom-right"><?php _e("Lifted (Right)", "drop-shadow-boxes"); ?> </option>

                            <option value="curled"><?php _e("Curled", "drop-shadow-boxes"); ?> </option>
                            <option
                                value="perspective-left"><?php _e("Perspective (Left)", "drop-shadow-boxes"); ?> </option>
                            <option
                                value="perspective-right"><?php _e("Perspective (Right)", "drop-shadow-boxes"); ?> </option>
                            <option value="raised"><?php _e("Raised", "drop-shadow-boxes"); ?> </option>
                            <option
                                value="vertical-curve-left"><?php _e("Vertical Curve (Left)", "drop-shadow-boxes"); ?> </option>
                            <option
                                value="vertical-curve-both"><?php _e("Vertical Curve (Both)", "drop-shadow-boxes"); ?> </option>
                            <option
                                value="horizontal-curve-bottom"><?php _e("Horizontal Curve (Bottom)", "drop-shadow-boxes"); ?> </option>
                            <option
                                value="horizontal-curve-both"><?php _e("Horizontal Curve (Both)", "drop-shadow-boxes"); ?> </option>
                        </select>

                        <?php _e("Background:", "drop-shadow-boxes"); ?>
                        <input id="box_background_color" value="#ffffff" class="dropshadowboxes-color-input" type="text"
                               onclick="DSB_open_color_picker('box_background_color')"/>

                        <div class="dropshadowboxes-color-picker" rel="box_background_color"></div>

                        <?php _e("Alignment:", "drop-shadow-boxes"); ?>
                        <select id="box_alignment">
                            <option value="none"><?php _e("None", "drop-shadow-boxes"); ?> </option>
                            <option value="left"><?php _e("Left", "drop-shadow-boxes"); ?> </option>
                            <option value="right"><?php _e("Right", "drop-shadow-boxes"); ?> </option>
                            <option value="center"><?php _e("Center", "drop-shadow-boxes"); ?> </option>
                        </select>


                    </div>


                    <div style="padding:15px 15px 0 15px;">
                        <?php _e("Height:", "drop-shadow-boxes"); ?>
                        <input id="box_height" value="auto" class="small-text" type="text"/>

                        <?php _e("Width:", "drop-shadow-boxes"); ?>
                        <input id="box_width" value="auto" class="small-text" type="text"/>
                        <select id="box_width_units">
                            <option value="px"><?php _e("pixels", "drop-shadow-boxes"); ?> </option>
                            <option value="%"><?php _e("%", "drop-shadow-boxes"); ?> </option>
                        </select>

                        <?php _e("Border (pixels):", "drop-shadow-boxes"); ?><input id="border_width" value="1"
                                                                                  class="small-text" type="text"/>
                        <input id="border_color" value="#dddddd" class="dropshadowboxes-color-input" type="text"
                               onclick="DSB_open_color_picker('border_color')"/>

                        <div class="dropshadowboxes-color-picker" rel="border_color"></div>
                    </div>

                    <div style="padding:15px 15px 0 15px;">
                        <input type="checkbox" id="rounded_corners" checked='checked'/> <label
                            for="rounded_corners"><?php _e("Rounded corners", "drop-shadow-boxes"); ?></label> &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" id="inside_shadow" checked='checked'/> <label
                            for="inside_shadow"><?php _e("Inside shadow", "drop-shadow-boxes"); ?></label> &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" id="outside_shadow" checked='checked'/> <label
                            for="outside_shadow"><?php _e("Outside shadow", "drop-shadow-boxes"); ?></label> &nbsp;&nbsp;&nbsp;

                    </div>

                    <div style="padding:15px 15px 0 15px;">
                        <textarea style="width:100%"
                                  id="box_content"><?php _e("Enter your content here.", "drop-shadow-boxes"); ?></textarea>
                        &nbsp;&nbsp;&nbsp;

                    </div>
                    <div style="padding:0px 15px 15px 15px;">
                        <input type="button" class="button-primary"
                               value="<?php _e("Refresh Preview", "drop-shadow-boxes"); ?>" onclick="RefreshPreview();"/>&nbsp;&nbsp;&nbsp;
                        <input type="button" class="button-primary"
                               value="<?php _e("Insert Box", "drop-shadow-boxes"); ?>"
                               onclick="SendDropShadowShortCodeToEditor();"/>&nbsp;&nbsp;&nbsp;
                        <a class="button" style="color:#bbb;" href="#"
                           onclick="tb_remove(); return false;"><?php _e("Cancel", "drop-shadow-boxes"); ?></a>
                    </div>
                    <fieldset style="border: 4px dashed #DDDDDD;width:90%;margin:0 15px 0 15px;">
                        <legend
                            style="color:#CCC;font-weight:bold;font-family: Helvetica, Arial;font-size: 1.8em"><?php _e("Preview", "drop-shadow-boxes"); ?></legend>
                        <div id="dropshadowboxes_preview_box" style="height: 200px;padding:15px;overflow:auto;">
                            <div id="dropshadowboxes_preview_container" style="width:95%;">
                                Tellus vestibulum tempus tellus ullamcorper amet egestas varius sollicitudin ut tellus
                                ac sollicitudin dolor.
                                <span id="dropshadowboxes_preview-placeholder"></span>
                                Curabitur auctor dignissim dignissim tellus at ligula facilisis et varius sit
                                ullamcorper egestas sit hendrerit vestibulum in. Donec amet lorem amet id velit amet id
                                ut nec nulla dignissim. Tortor morbi varius iaculis lorem vestibulum amet dignissim
                                facilisis in.
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <script>
                jQuery(document).ready(function () {
                    RefreshPreview();
                });
            </script>
        <?php
        }


        //helper functions
        public static function get_base_url() {
            $folder = basename(dirname(__FILE__));

            return plugins_url($folder);
        }

        //Returns the physical path of the plugin's root folder
        public static function get_base_path() {
            $folder = basename(dirname(__FILE__));

            return WP_PLUGIN_DIR . "/" . $folder;
        }

        //used only for logging
        public static function _log($message) {
            if (WP_DEBUG === true) {
                if (is_array($message) || is_object($message)) {
                    error_log(print_r($message, true));
                } else {
                    error_log($message);
                }
            }
        }

    } //end class

    require_once(DropShadowBoxes::get_base_path() . "/widget.php");

} //end if

