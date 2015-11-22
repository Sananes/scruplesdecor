<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $locations;

$address_title      = get_field('address_title');
$address            = get_field('address');

$route_finder       = get_field('enable_route_finder');

?>
<!-- Address Block -->
<div class="row">

	<div class="col-lg-5">
		<div class="white-block block-pad contact-store">
			<h4><?php echo $address_title; ?></h4>
			
			<div class="address-content">
				<?php echo $address; ?>
			</div>
			
			<?php if($route_finder != 'hide'): ?>
			<div class="show-me-the-route">
			
				<a href="#">
					<i class="entypo-location"></i>
					<?php _e('Show me the route?', 'oxygen'); ?>
				</a>
				
				<div class="route-options-container">
					
					<?php 
					if(count($locations) > 1):
					
						?>
						<div class="select-address">
							
							<label><?php _e('Select Point/Destination', 'oxygen'); ?>:</label>
						
							<select name="current-address" id="current-contact-address">
								<optgroup label="<?php _e('Select point', 'oxygen'); ?>">
								<?php						
								foreach($locations as $i => $location):
									
									?>
									<option value="<?php echo $i; ?>"><?php echo isset($location['label']) ? $location['label'] : $location['address']; ?></option>
									<?php
									
								endforeach;							
								?>
								</optgroup>
							</select>
							
						</div>
						<?php
					else:
						?>
						<input type="hidden" id="current-contact-address" value="0" />
						<?php
					endif; 
					?>
				
					<?php if($route_finder == 'both' || $route_finder == 'location'): ?>
					<div class="route-options">
						<input type="radio" class="form-control" name="location" value="gps" id="gps-type" />
						<label for="gps-type"><?php _e('My current location', 'oxygen'); ?></label>
					</div>
					<?php endif; ?>
					
				
					<?php if($route_finder == 'both' || $route_finder == 'address'): ?>
					<div class="route-options">
						<input type="radio" class="form-control" name="location" value="address" id="address-type" />
						<label for="address-type"><?php _e('Specific address', 'oxygen'); ?></label>
						
						<div class="address-field<?php echo $route_finder == 'address' ? ' visible' : ''; ?>">
							<input type="text" class="form-control" placeholder="<?php _e('Enter your address...', 'oxygen'); ?>" />
							<span class="error"><?php _e('Cannot find this address!', 'oxygen'); ?></span>
						</div>
					</div>
					<?php endif; ?>
					
					<button type="button" class="btn btn-default to-uppercase" id="calc-route"><?php _e('Calculate route', 'oxygen'); ?></button>
					
					<div class="route-error"><?php _e('Unable to find the route path!', 'oxygen'); ?></div>
					
					<div class="route-details">
						<div class="route-detail distance">
							<span>0</span>
							<?php _e('km', 'oxygen'); ?>
						</div>
						
						
						<div class="route-detail time">
							<span>0</span>
							<?php _e('mins', 'oxygen'); ?>
						</div>
					</div>
					
					<a href="#" class="route-clear"><?php _e('Clear route', 'oxygen'); ?></a>
					
				</div>
			</div>
			<?php endif; ?>
			
		</div>
	</div>
	
</div>

<?php if($route_finder): ?>
<script type="text/javascript">
var contact_vars = contact_vars || {};
contact_vars.carPin = '<?php echo THEMEASSETS; ?>images/pin-car.png';
</script>
<?php endif; ?>