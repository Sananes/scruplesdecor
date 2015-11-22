<?php
/**
 * Product Loop End
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

global $parsed_from_vc;

if(SHOPSIDEBAR && ! is_product() && ! $parsed_from_vc):
?>

							<?php do_action( 'woocommerce_after_shop_loop' ); ?>

						</div>

					</div>

				</div>

			</section>

			<?php wc_get_template('loop/quick-view.php'); ?>

		</div>

	</div>

	<div class="col-md-3 sidebar-env">

		<div class="blog shop_sidebar">
			<?php dynamic_sidebar('shop_sidebar'); ?>
		</div>

	</div>

</div>

<?php else: ?>
			</div>

		</section>

		<?php wc_get_template('loop/quick-view.php'); ?>

	</div>

</div>

<?php endif; ?>

