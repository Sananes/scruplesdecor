<?php
/**
 *	Oxygen WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

?>

<div class="row">

	<div class="col-lg-9">

		<!--blog01-->
		<div class="blog">

			<?php
			while(have_posts()):

				the_post();

				get_template_part('tpls/blog-post-single');

			endwhile;
			?>

		</div>

	</div>

	<div class="col-lg-3">
		<?php get_template_part('tpls/blog-sidebar'); ?>
	</div>

</div>