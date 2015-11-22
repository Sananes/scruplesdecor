<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $top_menu_class;

if( ! get_data('header_top_links'))
	return;

$header_top_style       = get_data('header_top_style');
$header_top_links_left  = get_data('header_top_links_left');
$header_top_links_right = get_data('header_top_links_right');
?>
<div class="top-menu<?php echo $header_top_style ? " {$header_top_style}" : ''; echo $top_menu_class ? " {$top_menu_class}" : '' ?>">

	<div class="container">
		<div class="row">
			<div class="col-sm-6">
				<?php laborator_display_header_top_bar($header_top_links_left); ?>
			</div>
			<div class="col-sm-6 right-align">
				<?php laborator_display_header_top_bar($header_top_links_right); ?>
			</div>
		</div>
	</div>

</div>