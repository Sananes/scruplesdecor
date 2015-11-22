<table class="variations-table" cellspacing="0">
	<tbody>
		<?php
		$loop = 0;
		foreach ( $picker->attributes as $name => $options ) : $loop++;
			$st_name = sanitize_title( $name );
			$hashed_name = md5( $st_name );
			$lookup_name = '';
			if ( isset( $picker->swatch_type_options[$hashed_name] ) ) {
				$lookup_name = $hashed_name;
			} elseif ( isset( $picker->swatch_type_options[$st_name] ) ) {
				$lookup_name = $st_name;
			}
			?>
			<tr>
				<td class="label"><label for="<?php echo $st_name; ?>"><?php echo WC_Swatches_Compatibility::wc_attribute_label( $name ); ?></label></td>
				<td>
					<?php
					if ( isset( $picker->swatch_type_options[$lookup_name] ) ) {
						$picker_type = $picker->swatch_type_options[$lookup_name]['type'];
						if ( $picker_type == 'default' ) {
							$picker->render_default( $st_name, $options );
						} else {
							$picker->render_picker( $st_name, $options, $name );
						}
					} else {
						$picker->render_default( $st_name, $options );
					}
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
