<?php

function rcp_discounts_page()
{
	global $rcp_options, $rcp_discounts_db_name, $wpdb;
	$page = admin_url( '/admin.php?page=rcp-discounts' );
	?>
	<div class="wrap">
		<?php if( isset( $_GET['edit_discount'] ) ) :
			include('edit-discount.php');
		else : ?>
			<h2><?php _e( 'Discount Codes', 'rcp' ); ?></h2>

			<table class="wp-list-table widefat posts">
				<thead>
					<tr>
						<th scope="col" class="rcp-discounts-name-col column-primary" ><?php _e( 'Name', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-desc-col"><?php _e( 'Description', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-code-col" ><?php _e( 'Code', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-subscription-col" ><?php _e( 'Subscription', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-amount-col"><?php _e( 'Amount', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-type-col"><?php _e( 'Type', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-status-col"><?php _e( 'Status', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-uses-col"><?php _e( 'Uses', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-uses-left-col"><?php _e( 'Uses Left', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-expir-col" ><?php _e( 'Expiration', 'rcp' ); ?></th>
						<?php do_action( 'rcp_discounts_page_table_header' ); ?>
					</tr>
				</thead>
				<tbody>
				<?php $codes = rcp_get_discounts(); ?>
				<?php
				if($codes) :
					$i = 1;
					foreach( $codes as $key => $code) : ?>
						<tr class="rcp_row <?php if( rcp_is_odd( $i ) ) { echo 'alternate'; } ?>">
							<td class="column-primary has-row-actions" data-colname="<?php _e( 'Name', 'rcp' ); ?>">
								<strong><a href="<?php echo esc_url( add_query_arg( 'edit_discount', $code->id, $page ) ); ?>"><?php echo stripslashes( $code->name ); ?></a></strong>
								<div class="row-actions">
									<?php if( current_user_can( 'rcp_manage_discounts' ) ) : ?>
										<span class="id"><?php echo __( 'ID:', 'rcp' ) . ' ' . $code->id; ?></span>
										<a href="<?php echo esc_url( add_query_arg( 'edit_discount', $code->id, $page ) ); ?>"><?php _e( 'Edit', 'rcp' ); ?></a> |
										<?php if(rcp_get_discount_status($code->id) == 'active') { ?>
											<a href="<?php echo esc_url( add_query_arg( 'deactivate_discount', $code->id, $page ) ); ?>"><?php _e( 'Deactivate', 'rcp' ); ?></a> |
										<?php } else { ?>
											<a href="<?php echo esc_url( add_query_arg( 'activate_discount', $code->id, $page ) ); ?>"><?php _e( 'Activate', 'rcp' ); ?></a> |
										<?php } ?>
										<a href="<?php echo esc_url( add_query_arg( 'delete_discount', $code->id, $page ) ); ?>" class="rcp_delete_discount"><?php _e( 'Delete', 'rcp' ); ?></a>
									<?php endif; ?>
								</div>
								<button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e( 'Show more details', 'rcp' ); ?></span></button>
							</td>
							<td data-colname="<?php _e( 'Description', 'rcp' ); ?>"><?php echo stripslashes( $code->description ); ?></td>
							<td data-colname="<?php _e( 'Code', 'rcp' ); ?>"><?php echo $code->code; ?></td>
							<td>
								<?php
								if ( $code->subscription_id > 0 ) {
									echo rcp_get_subscription_name( $code->subscription_id );
								} else {
									echo __( 'All Levels', 'rcp' );
								}
								?>
							</td>
							<td data-colname="<?php _e( 'Amount', 'rcp' ); ?>"><?php echo rcp_discount_sign_filter( $code->amount, $code->unit ); ?></td>
							<td data-colname="<?php _e( 'Type', 'rcp' ); ?>"><?php echo $code->unit == '%' ? __( 'Percentage', 'rcp' ) : __( 'Flat', 'rcp' ); ?></td>
							<td data-colname="<?php _e( 'Status', 'rcp' ); ?>">
								<?php
									if(rcp_is_discount_not_expired( $code->id ) ) {
										echo rcp_get_discount_status( $code->id ) == 'active' ? __( 'active', 'rcp' ) : __( 'disabled', 'rcp' );
									} else {
										_e( 'expired', 'rcp' );
									}
								?>
							</td>
							<td data-colname="<?php _e( 'Uses', 'rcp' ); ?>"><?php if( $code->max_uses > 0 ) { echo rcp_count_discount_code_uses( $code->code ) . '/' . $code->max_uses; } else { echo rcp_count_discount_code_uses( $code->code ); }?></td>
							<td data-colname="<?php _e( 'Uses Left', 'rcp' ); ?>"><?php echo rcp_discount_has_uses_left( $code->id ) ? 'yes' : 'no'; ?></td>
							<td data-colname="<?php _e( 'Expiration', 'rcp' ); ?>"><?php echo $code->expiration == '' ? __( 'none', 'rcp' ) : date_i18n( 'Y-m-d', strtotime( $code->expiration, current_time( 'timestamp' ) ) ); ?></td>
							<?php do_action('rcp_discounts_page_table_column', $code->id); ?>
						</tr>
					<?php
					$i++;
					endforeach;
				else : ?>
					<tr><td colspan="11"><?php _e( 'No discount codes added yet.', 'rcp' ); ?></td></tr>
				<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="col" class="rcp-discounts-name-col column-primary" ><?php _e( 'Name', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-desc-col"><?php _e( 'Description', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-code-col" ><?php _e( 'Code', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-subscription-col" ><?php _e( 'Subscription', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-amount-col"><?php _e( 'Amount', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-type-col"><?php _e( 'Type', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-status-col"><?php _e( 'Status', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-uses-col"><?php _e( 'Uses', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-uses-left-col"><?php _e( 'Uses Left', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-expir-col" ><?php _e( 'Expiration', 'rcp' ); ?></th>
						<?php do_action( 'rcp_discounts_page_table_header' ); ?>
					</tr>
				</tfoot>
			</table>
			<?php do_action( 'rcp_discounts_below_table' ); ?>
			<?php if( current_user_can( 'rcp_manage_levels' ) ) : ?>
				<h3><?php _e( 'Add New Discount', 'rcp' ); ?></h3>
				<form id="rcp-discounts" action="" method="POST">
					<table class="form-table">
						<tbody>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-name"><?php _e( 'Name', 'rcp' ); ?></label>
								</th>
								<td>
									<input name="name" id="rcp-name" type="text" value="" style="width: 300px;"/>
									<p class="description"><?php _e( 'The name of this discount', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-description"><?php _e( 'Description', 'rcp' ); ?></label>
								</th>
								<td>
									<textarea name="description" id="rcp-description" style="width: 300px;"></textarea>
									<p class="description"><?php _e( 'The description of this discount code', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-code"><?php _e( 'Code', 'rcp' ); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-code" name="code" value="" style="width: 300px;"/>
									<p class="description"><?php _e( 'Enter a code for this discount, such as 10PERCENT', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-unit"><?php _e( 'Type', 'rcp' ); ?></label>
								</th>
								<td>
									<select name="unit" id="rcp-duration-unit">
										<option value="%"><?php _e( 'Percentage', 'rcp' ); ?></option>
										<option value="flat"><?php _e( 'Flat amount', 'rcp' ); ?></option>
									</select>
									<p class="description"><?php _e( 'The kind of discount to apply for this discount.', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-amount"><?php _e( 'Amount', 'rcp' ); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-amount" name="amount" value="" style="width: 40px;"/>
									<p class="description"><?php _e( 'The amount of this discount code.', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-subscription"><?php _e( 'Subscription', 'rcp' ); ?></label>
								</th>
								<td>
									<?php
									$levels = rcp_get_subscription_levels('all', false);
									if( $levels ) : ?>
										<select name="subscription" id="rcp-subscription">
											<option value="0"><?php _e( 'All Levels', 'rcp' ); ?></option>
											<?php
												foreach( $levels as $level ) :
													echo '<option value="' . $level->id . '">' . $level->name . '</option>';
												endforeach;
											?>
										</select>
									<?php endif; ?>
									<p class="description"><?php _e( 'The subscription levels this discount code can be used for.', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-expiration"><?php _e( 'Expiration date', 'rcp' ); ?></label>
								</th>
								<td>
									<input name="expiration" id="rcp-expiration" type="text" style="width: 120px;" class="rcp-datepicker"/>
									<p class="description"><?php _e( 'Enter the expiration date for this discount code in the format of yyyy-mm-dd. For no expiration, leave blank', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-max-uses"><?php _e( 'Max Uses', 'rcp' ); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-max-uses" name="max" value="" style="width: 40px;"/>
									<p class="description"><?php _e( 'The maximum number of times this discount can be used. Leave blank for unlimited.', 'rcp' ); ?></p>
								</td>
							</tr>
							<?php do_action( 'rcp_add_discount_form' ); ?>
						</tbody>
					</table>
					<p class="submit">
						<input type="hidden" name="rcp-action" value="add-discount"/>
						<input type="submit" value="<?php _e( 'Add Discount Code', 'rcp' ); ?>" class="button-primary"/>
					</p>
					<?php wp_nonce_field( 'rcp_add_discount_nonce', 'rcp_add_discount_nonce' ); ?>
				</form>
			<?php endif; ?>
		<?php endif; ?>
	</div><!--end wrap-->

	<?php
}