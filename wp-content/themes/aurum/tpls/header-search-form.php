<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

?>
<form action="<?php echo home_url(); ?>" method="get" class="search-form<?php echo get('s') ? ' input-visible' : ''; ?>" enctype="application/x-www-form-urlencoded">

	<div class="search-input-env<?php echo trim(get('s')) ? ' visible' : ''; ?>">
		<input type="text" class="form-control search-input" name="s" placeholder="<?php _e('Search...', TD); ?>" value="<?php echo esc_attr(get('s')); ?>">
	</div>

</form>