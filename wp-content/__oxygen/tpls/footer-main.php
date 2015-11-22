<?php
/**
 *	Oxygen WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $post;

$menu_locations = get_nav_menu_locations();
$supported_payments = get_supported_payments();

?>
<div class="footer-env">
	<div class="container">
		<?php

		if(get_data('footer_widgets'))
		{
			get_template_part('tpls/footer-widgets');
		}

		?>
		<footer class="footer-container">

			<div class="footer_main row">

				<div class="col-md-12 hidden-sm hidden-xs">
					<hr class="divider" />
				</div>

				<div class="clear"></div>

				<?php
				# Footer Menu
				if(isset($menu_locations['footer-menu']) && $menu_locations['footer-menu'] > 0):
				?>
				<div class="col-sm-12">

					<div class="footer-nav">
						<?php
						wp_nav_menu(array(
							'theme_location' => 'footer-menu',
							'container' => '',
							'depth' => 1
						));
						?>
					</div>

				</div>

				<div class="clear"></div>
				<?php endif; ?>

				<div class="col-sm-<?php echo count($supported_payments) ? 6 : 12; ?>">

					<div class="copyright_text">
						<?php echo do_shortcode( get_data('footer_text') ); ?>
					</div>

				</div>

				<?php if(count($supported_payments)): ?>
				<div class="clear-sm"></div>

				<div class="col-sm-6">

					<ul class="payment-methods">
					<?php
					foreach($supported_payments as $payment_method):

						$p_image_1  = $payment_method['p_image_1'];
						$p_image_2  = $payment_method['p_image_2'];
						$name       = $payment_method['name'];
						$link       = $payment_method['link'];
						$blank_page = $payment_method['blank_page'];

						if( ! isset($p_image_1['th']))
							continue;
						?>
						<li>
							<a<?php echo $link ? " href=\"{$link}\"" : ''; echo $link && $blank_page ? ' target="_blank"' : ''; ?> class="payment-slide<?php echo isset($p_image_2['original']) ? ' hover' : ''; ?>">
								<img src="<?php echo site_url($p_image_1['original']); ?>" class="normal-img" alt="" />

								<?php if(isset($p_image_2['th'])): ?>
								<img src="<?php echo site_url($p_image_2['original']); ?>" class="hover-img" alt="" />
								<?php endif; ?>
							</a>
						</li>
						<?php
					endforeach;
					?>
					</ul>

				</div>
				<?php endif; ?>
			</div>

		</footer>
	</div>
</div>