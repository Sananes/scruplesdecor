<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $locations;

$map_location   = get_field('map_location');
$map_zoom_level = get_field('map_zoom_level');
$map_pin        = get_field('map_pin');
$map_pin_retina = get_field('map_pin_retina');
$fourty_five	= get_field('map_fourtyfive_degree');
$map_type		= get_field('map_type');
$map_panby		= get_field('map_panby');

$more_locations	= get_field('more_locations');
$location_label = get_field('location_label');

$enable_switcher 		= get_field('map_enable_type_switcher');
$allowed_map_types 		= get_field('map_allowed_map_types');
$street_view_heading	= get_field('street_view_heading');
$street_view_pitch 		= get_field('street_view_pitch');

$locations = array();

if( ! $map_pin)
	$map_pin = THEMEASSETS . 'images/pin-shop.png';

if($map_pin)
{
	try
	{
		$image_path = str_replace(site_url(), ABSPATH, $map_pin);
		
		if( file_exists( $image_path ) ) {
			
			$pin_dimensions = getimagesize( $image_path );
	
			if(is_array($pin_dimensions) && is_numeric($pin_dimensions[0]))
			{
				$pin_width = $pin_dimensions[0];
				$pin_height = $pin_dimensions[1];
			}
		}
		
	} catch(Exception $e){
		
	}
	
}

if( ! is_array($map_location) || ! isset($map_location['lat']) || ! $map_location['lat'])
	return;

if($location_label)
	$map_location['label'] = $location_label;

$locations = array($map_location);

if(is_array($more_locations) && count($more_locations))
{
	foreach($more_locations as $maploc)
	{
		$maploc['map_location']['label'] = $maploc['label'];
		$locations[] = $maploc['map_location'];
	}
}

switch($map_type)
{
	case 'roadmap':
		$map_type_id = 0;
		break;
		
	case 'hybrid':
		$map_type_id = 2;
		break;
		
	case 'street':
		$map_type_id = 3;
		break;
		
	default:
		$map_type_id = 1;
}

$pan_x = $pan_y = 0;

if(preg_match("/^([0-9\-]+)(,[0-9\-]+)?$/", trim($map_panby), $panby_matches))
{
	$pan_x = $panby_matches[1];
	
	if(isset($panby_matches[2]) && ($pan_y = $panby_matches[2]))
		$pan_y = str_replace(',', '', $pan_y);
}
?>

<div id="contact-map" class="contact-map-canvas"></div>

<?php if($enable_switcher): ?>
<div class="map-type-switcher<?php echo $map_type_id == 1 || $map_type_id == 2 ? ' satellite-view' : ''; ?>">

	<?php if($fourty_five): ?>
		<?php if(in_array('satellite', $allowed_map_types) || $map_type_id == 1 || $map_type_id == 2): ?>
		<a href="#" class="rotate-view"></a>
		<?php endif; ?>
	<?php endif; ?>
	
	<?php if(in_array('roadmap', $allowed_map_types)): ?>
	<a href="#" data-tooltip="<?php _e('Roadmap', 'oxygen'); ?>" data-type="roadmap" class="type-roadmap simptip-position-top simptip-fade<?php echo $map_type_id == 0 ? ' current' : ''; ?>"></a>
	<?php endif; ?>
	
	<?php if(in_array('satellite', $allowed_map_types)): ?>
	<a href="#" data-tooltip="<?php _e('Satellite', 'oxygen'); ?>" data-type="<?php echo $map_type_id == 2 ? 'hybrid' : 'satellite'; ?>" class="type-satellite simptip-position-top simptip-fade<?php echo $map_type_id == 2 ? ' type-hybrid' : ''; echo $map_type_id == 1 || $map_type_id == 2 ? ' current' : ''; ?>"></a>
	<?php endif; ?>
	
	<?php if(in_array('street', $allowed_map_types)): ?>
	<a href="#" data-tooltip="<?php _e('Street View', 'oxygen'); ?>" data-type="street" class="type-street simptip-position-top simptip-fade<?php echo $map_type_id == 3 ? ' current' : ''; ?>"></a>
	<?php endif; ?>
</div>
<?php endif; ?>

<a href="#" class="toggle-info-blocks" data-visible="<?php _e('Hide Contact Blocks', 'oxygen'); ?>" data-hidden="<?php _e('Show Contact Blocks', 'oxygen'); ?>"><?php _e('Hide Contact Blocks', 'oxygen'); ?></a>



<script type="text/javascript">
var map,
	map_types,
	position,
	panorama,
	pan_x = <?php echo -$pan_x; ?>,
	pan_y = <?php echo -$pan_y; ?>;


var contact_vars = contact_vars || {};
contact_vars.lat = <?php echo $map_location['lat']; ?>;
contact_vars.lng = <?php echo $map_location['lng']; ?>;
contact_vars.zoom = <?php echo intval($map_zoom_level); ?>;
contact_vars.map_type_id = <?php echo $map_type_id; ?>;
contact_vars.shopPin = '<?php echo $map_pin; ?>';
<?php if(isset($pin_width) && is_numeric($pin_width) && $pin_width): ?>
contact_vars.pinSize = [<?php echo $pin_width; ?>, <?php echo $pin_height; ?>];
<?php endif; ?>
contact_vars.fourtyFive = <?php echo $fourty_five ? 'true' : 'false'; ?>;
contact_vars.streetViewHeading = <?php echo intval($street_view_heading); ?>;
contact_vars.streetViewPitch = <?php echo intval($street_view_pitch); ?>;

contact_vars.locations = <?php echo json_encode($locations); ?>;
</script>
