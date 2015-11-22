<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $use_uploaded_logo, $custom_logo_image, $custom_logo_max_width;

$use_uploaded_logo		= get_data('use_uploaded_logo');
$custom_logo_image      = get_data('custom_logo_image');
$custom_logo_max_width  = absint(get_data('custom_logo_max_width'));

$use_uploaded_logo_light = get_field('use_uploaded_logo_light');
$custom_logo_image_light = get_data('custom_logo_image_light');


# Whether using Image or Text logo
$use_image_logo         = false;
$logo_url               = '';
$logo_size              = array(0,0);
$max_width              = 0;

if($use_uploaded_logo && $custom_logo_image)
{
	$logo_path = ABSPATH . substr($custom_logo_image, strpos($custom_logo_image, 'wp-content/uploads'));

	if(file_exists($logo_path))
	{
		$use_image_logo   = true;
		$logo_url         = $custom_logo_image;
		$logo_size        = getimagesize($logo_path);
	}
}

if($use_image_logo)
{
	$max_width = $custom_logo_max_width ? $custom_logo_max_width : $logo_size[0];
}

if(isset($max_width) && $max_width != $logo_size[0])
{
	$scale = $logo_size[1] / $logo_size[0];
	$logo_height = $scale * $max_width;
}

?>
<div class="logo<?php echo ! $use_image_logo ? ' text-logo' : ''; ?>">

	<a href="<?php echo home_url(); ?>">
	<?php if($use_image_logo): ?>
		<style>
			.logo-dimensions {
				min-width: <?php echo $max_width; ?>px;
				width: <?php echo $max_width; ?>px;
			}
		</style>
		<img src="<?php echo $logo_url; ?>" class="logo-dimensions normal-logo" id="site-logo" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" width="<?php echo $max_width; ?>"<?php if(isset($logo_height)): ?> height="<?php echo $logo_height; ?>"<?php endif; ?> />

		<?php if(has_transparent_header() && $custom_logo_image_light): ?>
		<img src="<?php echo $custom_logo_image_light; ?>" class="logo-dimensions light-logo" id="site-logo-light" alt="<?php echo esc_attr(get_bloginfo('name')); ?>-light" width="<?php echo $max_width; ?>"<?php if(isset($logo_height)): ?> height="<?php echo $logo_height; ?>"<?php endif; ?> />
		<?php endif; ?>
	<?php else: ?>
		<?php echo get_data('logo_text'); ?>
	<?php endif; ?>
	</a>

</div>