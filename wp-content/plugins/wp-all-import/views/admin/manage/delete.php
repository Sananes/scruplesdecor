<h2><?php _e('Delete Import', 'wp_all_import_plugin') ?></h2>

<form method="post">
	<p><?php printf(__('Are you sure you want to delete <strong>%s</strong> import?', 'wp_all_import_plugin'), $item->name) ?></p>
	<div class="input">
		<input type="checkbox" id="is_delete_posts" name="is_delete_posts" class="switcher"/> <label for="is_delete_posts"><?php _e('Delete associated posts as well','wp_all_import_plugin');?> </label>
		<div class="switcher-target-is_delete_posts" style="padding: 5px 17px;">
			<div class="input">
				<input type="hidden" name="is_delete_images" value="no"/>
				<input type="checkbox" id="is_delete_images" name="is_delete_images" value="yes" />
				<label for="is_delete_images"><?php _e('Delete associated images from media gallery', 'wp_all_import_plugin') ?></label>			
			</div>
			<div class="input">
				<input type="hidden" name="is_delete_attachments" value="no"/>
				<input type="checkbox" id="is_delete_attachments" name="is_delete_attachments" value="yes" />
				<label for="is_delete_attachments"><?php _e('Delete associated files from media gallery', 'wp_all_import_plugin') ?></label>			
			</div>
		</div>
		<?php if ( ! empty($item->options['deligate']) and $item->options['deligate'] == 'wpallexport' and class_exists('PMXE_Plugin')): ?>
			<?php
				$export = new PMXE_Export_Record();
				$export->getById($item->options['export_id']);
				if ( ! $export->isEmpty() ){
					printf(__('<p class="wpallimport-delete-posts-warning"><strong>Important</strong>: this import was created automatically by WP All Export. All posts exported by the "%s" export job have been automatically associated with this import.</p>', 'wp_all_export_plugin'), $export->friendly_name );
				}
			?>
		<?php endif; ?>
	</div>
	<p class="submit">
		<?php wp_nonce_field('delete-import', '_wpnonce_delete-import') ?>
		<input type="hidden" name="is_confirmed" value="1" />
		<input type="submit" class="button-primary" value="Delete" />
	</p>
	
</form>