<?php
/**
 *	Laborator 1 Click Demo Content Importer
 *
 *	Developed by: Arlind Nushi
 *	URL: www.laborator.co
 */

?>
<style>
.btn-float-right {
	float: right;
	position: relative;
	top: 4px;
}

.clearfix:after {
	clear: both;
	content: "";
	display: block;
}

.demo-content-packs {
	list-style: none;
	margin-left: -15px;
	margin-right: -15px;
	margin-top: 25px;
	transition: all 500ms;
}

.demo-content-packs:after {
	clear: both;
	content: "";
	display: block;
}

.demo-content-packs li {
	display: block;
	float: left;
	padding: 0;
	margin: 0;
	margin-bottom: 30px;
	width: 25%;
}

.demo-content-packs .pack-entry {
	background: #fff;
	border: 1px solid #ccc;
	margin: 0 15px;
	transition: all 250ms;
}

.demo-content-packs .pack-entry:hover {
	background-color: #f5f5f5;
}

.demo-content-packs .pack-entry img {
	border-bottom: 1px solid #ccc;
	max-width: 100%;
	width: 100%;
}

.demo-content-packs .pack-entry .pack-details {
	padding: 10px 15px;
	padding-bottom: 15px;
}

.demo-content-packs .pack-entry .pack-details h3 {
	margin-top: 0;
	margin-bottom: 0;
}

.demo-content-packs .pack-entry .pack-details p {
	color: #888;
	font-size: 12px;
	margin-top: 10px;
}

.demo-content-packs .pack-entry .pack-details .button {
	display: block;
	text-transform: uppercase;
	padding: 2px 15px;
	height: auto;
	text-align: center;
}

#lab_demo_content_container .plugins-to-install {
	margin-top: 25px;
}

#lab_demo_content_container .plugins-to-install span {
	display: block;
	margin-top: 10px;
}
#lab_demo_content_container .plugins-to-install em {
	display: block;
	font-size: 11px;
	color: #888;
	font-style: normal;
}

#lab_demo_content_container .low-opacity {
	opacity: 0.25;
	pointer-events: none;
}

#media_downloads {
	display: inline-block;
	background: #2ea2cc;
	border: 1px solid #0074a2;
	border-radius: 2px;
	margin-top: 15px;
}

#media_downloads label {
	display: block;
	white-space: nowrap;
	padding: 6px 10px !important;
	color: #fff;
}
</style>

<div class="wrap" id="lab_demo_content_container">
	<h2 id="main-title">1-Click Demo Content Installer</h2>
	<p class="description">Choose the demo content pack to install in this copy of WordPress installation. We recommend to install only one demo content pack per WordPress installation. This process is irreversible!</p>

	<?php if($missing_plugins = lab_1cl_demo_installer_required_plugins_missing()): ?>
	<div class="plugins-to-install update error">
		<p>
		Before proceeding with demo content importing please install the following plugins:
		<br />

		<?php
		foreach($missing_plugins as $plugin_id => $plugin_name):

			?>
			<span>
				<strong><?php echo $plugin_name; ?></strong>
				<em><?php echo $plugin_id; ?></em>
			</span>
			<?php

		endforeach;
		?>
		</p>
	</div>
	<?php else: ?>
	<form method="post" id="media_downloads">
		<label for="lab_1cl_demo_installer_download_media">
			<input name="lab_1cl_demo_installer_download_media" type="checkbox" id="lab_1cl_demo_installer_download_media" value="1"<?php checked(true, get_option('lab_1cl_demo_installer_download_media')); ?> />
			Download Media Files
		</label>

		<input type="hidden" name="lab_change_media_status" value="1" />
	</form>

	<script type="text/javascript">
		jQuery("#media_downloads input").on('change', function()
		{
			jQuery(".demo-content-packs").addClass('low-opacity');

			setTimeout(function(){
				jQuery("#media_downloads").submit();
			}, 1000)
		});
	</script>
	<?php endif; ?>

	<ul class="demo-content-packs<?php echo count($missing_plugins) > 0 ? ' low-opacity' : ''; ?>">
	<?php
	foreach(lab_1cl_demo_installer_get_packs() as $pack):

		extract($pack);

		?>
		<li>
			<div class="pack-entry">
				<img src="<?php echo $lab_demo_content_url . $thumb; ?>" />

				<div class="pack-details">
					<h3><?php echo $name; ?></h3>

					<?php if($desc): ?>
					<p><?php echo nl2br($desc); ?></p>
					<?php endif; ?>

					<a href="<?php echo admin_url("admin.php?page=laborator_demo_content_installer&install-pack=" . sanitize_title($name)) . '&#038;TB_iframe=true&#038;width=780&#038;height=450'; ?>" title="Demo Content Pack &raquo; <?php echo esc_attr($name); ?> (import process will take a while)" class="button button-primary thickbox">Install Content Pack</a>
				</div>
			</div>
		</li>
		<?php

	endforeach;
	?>
	</ul>

	<hr />
	<div class="footer-copyrights">
		&copy; This plugin is developed by <a href="http://laborator.co">Laborator</a>
	</div>
</div>