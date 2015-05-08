<table class="form-table visage">
	<tr>
		<th><label for="avatar"><?php _e( 'Personal Avatar', 'visage' ); ?></label></th>
		<td>
			<table>
				<tbody>
					<tr>
						<td><?php echo $avatar; ?></td>
						<td>

							<a href="#" class="button-primary button-small visage-upload"><?php _e( 'Change', 'visage' ); ?></a>
							<a href="#" class="button-secondary button-small visage-delete <?php if( $avatar_id == 0 ) { echo 'hidden'; } ?>"><?php _e( 'Delete', 'visage' ); ?></a>

							<select id="avatar" name="visage-rating"<?php if( $avatar_id == 0 ) { echo ' class="hidden"'; } ?>>
							<?php

								$ratings = array(
									'G' => __( 'G &#8212; Suitable for all audiences', 'visage' ),
									'PG' => __( 'PG &#8212; Possibly offensive, usually for audiences 13 and above', 'visage' ),
									'R' => __( 'R &#8212; Intended for adult audiences above 17', 'visage' ),
									'X' => __( 'X &#8212; Even more mature than above', 'visage' )
								);

								foreach( $ratings as $key => $rating ) :

									$selected = ( get_user_meta( $user->ID, 'visage_rating', true ) == $key ) ? ' selected' : '';
									echo '<option value="'. $key .'"'. $selected .'>'. $rating .'</option>';

								endforeach;

							?>
							</select>

						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>

<input type="hidden" name="visage-current" value="<?php echo $avatar_id; ?>">
<input type="hidden" name="visage-default" value="<?php echo $default_avatar; ?>">