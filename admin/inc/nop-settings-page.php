<?php

/**
 * Add the settings page for nop plugin
 *
 * @return void
 */
function nop_add_admin_menu() {
	// add sub-page to "Settings"
	add_submenu_page( 'options-general.php', __( 'New Order Popup ', 'new-order-notification' ), __( 'New Order Popup ', 'new-order-notification' ), 'manage_options', 'nop-notification-settings', 'nop_show_admin_page_menu_settings' );
}
add_action( 'admin_menu', 'nop_add_admin_menu' );

/**
 * Settings page
 *
 * @return void
 */
function nop_show_admin_page_menu_settings() {

	global $woo_fs;
	$set_id_suffix = '_freemius';
	$disabled = true;
	$save_field_cb = false;
	if ( $woo_fs && $woo_fs->is__premium_only() ) {
		if ( $woo_fs->can_use_premium_code() ) {
			$set_id_suffix = '';
			$disabled = false;
			$save_field_cb = true;
		}
	}
	$up_msg = array(
			'tooltip-class' => '',
			'tooltip'       => '',
		);
	$up_msg_top = '';
	if( $disabled ) {
		$up_msg_cu = '';
		if( $woo_fs ) {
		$up_msg_cu =	'<a href="' . esc_url( $woo_fs->get_trial_url() ) . '">' .
				__('Start your free trial now!', 'new-order-notification') .
				'</a>';
		}
		$up_msg_top = "<h3>". __( 'All these awesome features are available in the premium plan. Try them with a free trial! ', 'new-order-notification' ). $up_msg_cu . "</h3>";
	}
	?>
	
	<style>
	table.form-table tr {
		padding: 1em;
		background: #fff;
		text-align: left;
		border: 1px solid #e9e9e9;
	}
	
	table.form-table tr th {
		vertical-align: middle;
		padding: 2.33em 1em;
	}
	
	table.form-table tr td input.regular-text.sound_file {
		color: transparent;
	}
	
	table.form-table tr .pbwoo_choose_file input {
		opacity: 0;
		position: absolute;
	}
	
	table.form-table tr .pbwoo_choose_file label {
		color: #2271b1;
		background: #f6f7f7;
		border: 1px solid #2271b1;
		max-width: 80%;
		font-size: 13px;
		font-weight: 400;
		text-overflow: ellipsis;
		white-space: nowrap;
		display: inline-block;
		overflow: hidden;
		padding: 0 10px;
		border-radius: 3px;
		line-height: 2.15384615;
		cursor: pointer !important;
		min-height: 30px;
	}
	
	table.form-table tr td span.description {
		color: #666;
		padding-top: 0.5em;
		float: left;
		width: 100%;
	}
	
	table.form-table tr.pbwoo_button {
		background: transparent !important;
		border: 0px !important;
	}
	
	table.form-table tr td {
		padding: 20px 10px;
	}
	</style>
	  <?php
	  if( $disabled ) { ?>
		<script>
			jQuery( function() {
				//  disable ALL descendants of the DIV
				jQuery( "form.cmb-form" ).children().prop('disabled',true);
				 jQuery("form.cmb-form :input").prop("disabled", true);
				jQuery( "form.cmb-form").fadeTo('500',.8).css( "cursor", 'not-allowed' );
			});
		</script>
		<?php
	  } ?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Woocommerce New Order Notification Settings', 'new-order-notification' ); ?></h1>
	</div>
		<?php
	// See if the user has posted us some information
	if ( $save_field_cb && isset( $_POST['_settings_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_settings_nonce'] ) ), 'nonce_settings_notification' ) ) {
		// Read their posted value and later we are saving them in an option
		$notification_page_elements = array();
		$notification_page_elements['pbwoo_notification_msg'] = isset( $_POST['pbwoo_notification_msg'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['pbwoo_notification_msg'] ) ) ) : '';
		
		$notification_page_elements['pbwoo_btn_text'] = isset( $_POST['pbwoo_btn_text'] ) ? str_replace( ' ', '', sanitize_text_field( wp_unslash( $_POST['pbwoo_btn_text'] ) ) ) : '';
	
		if( isset( $_FILES ) && !empty( $_FILES['pbwoo_popup_icon'] )) {
			$image_file = $_FILES['pbwoo_popup_icon'];
			// get the image file type ( e.g. gif, jpg etc. )
			$image_file_type = pathinfo( sanitize_file_name( wp_unslash( $image_file['name'] ) ), PATHINFO_EXTENSION );

			/**
			 * change the allowed image extensions that may be uploaded to the cases
			 *
			 * @param string[] with file extensions to allow
			 */
			$allowed_image_extension = apply_filters( 'pbwoo_popup_icon_allow_images_type', array( 'png', 'jpg', 'jpeg', 'gif' ) );
			$image_name = 'notification_image.'. $image_file_type;
			// Validate file input to check if is with valid extension
			if ( in_array( $image_file_type, $allowed_image_extension, true ) ) {
				$upload_dir = NOP_PLUGIN_DIR . '/assets/img/';
				$image_path = $upload_dir .  $image_name;
				$file_size = $image_file['size'];
				if ( $file_size < 10485760 ) {
					move_uploaded_file( $image_file['tmp_name'], $image_path );
				}
			}
		}
		
		if( isset( $_FILES ) && !empty( $_FILES['pbwoo_notification_sound_file'] )) {
			$sound_file = $_FILES['pbwoo_notification_sound_file'];
			$sound_file_type = pathinfo( sanitize_file_name( wp_unslash( $sound_file['name'] ) ), PATHINFO_EXTENSION );
		
			/**
			 * change the allowed image extensions that may be uploaded to the cases
			 *
			 * @param string[] with file extensions to allow
			 */
			$allowed_image_extension = apply_filters( 'pbwoo_popup_icon_allow_images_type', array( 'mp3', 'wma' ) );
			$image_name = 'notification_sound.'. $sound_file_type;
			// Validate file input to check if is with valid extension
			if ( in_array( $sound_file_type, $allowed_image_extension, true ) ) {
				$upload_dir = NOP_PLUGIN_DIR . '/assets/sound/';
				$sound_path = $upload_dir .  $image_name;
				$file_size = $sound_file['size'];
				if ( $file_size < 2485760 ) {
					move_uploaded_file( $sound_file['tmp_name'], $sound_path );
				}
			}
		}
				
		$notification_page_elements['pbwoo_popup_theme_color'] = isset( $_POST['pbwoo_popup_theme_color'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['pbwoo_popup_theme_color'] ) ) ) : '';
		
		$notification_page_elements['pbwoo_popup_font_color'] = isset( $_POST['pbwoo_popup_font_color'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['pbwoo_popup_font_color'] ) ) ) : '';
		
		$notification_page_elements['pbwoo_popup_icon'] = isset( $_POST['pbwoo_popup_icon'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['pbwoo_popup_icon'] ) ) ) : '';
		update_option( 'nop-notification-settings' , $notification_page_elements );

	}
		$setting_page_values = get_option( 'nop-notification-settings', array() ) ;
	?>
		<?php echo $up_msg_top; ?>
	<form method="post" action="" enctype="multipart/form-data" class="cmb-form">

		<?php wp_nonce_field( 'nonce_settings_notification'.$set_id_suffix, '_settings_nonce'.$set_id_suffix ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Primary message', 'new-order-notification' ); ?></th>
				<td>
					<?php
					$pboo_value = !empty( $setting_page_values['pbwoo_notification_msg'] ) ? $setting_page_values['pbwoo_notification_msg'] :__( 'You Got a New Order', 'new-order-notification' );?>
					<input type="text" name="pbwoo_notification_msg" aria-label="<?php esc_attr_e( 'You Got a New Order', 'new-order-notification' ); ?>" value="<?php echo esc_attr( $pboo_value ); ?>" class="regular-text">
					
				</td>
			</tr>
						
			<tr>
				<th scope="row"><?php esc_html_e( 'Notification Sound', 'new-order-notification' ); ?></th>
				<td class="pbwoo_choose_file">
					<?php
					$pboo_value = !empty( $setting_page_values['pbwoo_notification_sound_file'] ) ? $setting_page_values['pbwoo_notification_sound_file'] : ''; ?>
					<input type="file" name="pbwoo_notification_sound_file" aria-label="<?php esc_attr_e( 'Notification Sound', 'new-order-notification' ); ?>" value="<?php echo esc_attr( $pboo_value ); ?>" class="regular-text sound_file" accept="audio/mpeg, audio/ogg">
					<label><span><?php echo __( 'Add A Sound File', 'new-order-notification' ); ?></span></label>
					<p class="description"><?php echo __( 'Upload an audio file (Note: Only <strong>.mp3</strong> and <strong>.ogg</strong> files are allowed).', 'new-order-notification' ); ?></p>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><?php esc_html_e( 'Button Text', 'new-order-notification' ); ?></th>
				<td>
					<?php
					$pboo_value = !empty( $setting_page_values['pbwoo_btn_text'] ) ? $setting_page_values['pbwoo_btn_text'] : __( 'Ok', 'new-order-notification' ); ?>
					<input type="text" name="pbwoo_btn_text" aria-label="<?php esc_attr_e( 'Button Text', 'new-order-notification' ); ?>" value="<?php echo esc_attr( $pboo_value ); ?>" class="regular-text">
				</td>
			</tr>

			<tr class="default-sender-id-inside">
				<th scope="row"><?php esc_html_e( 'Popup Background Color', 'new-order-notification' ); ?></th>
				<td>
					<?php
					$pboo_value = !empty( $setting_page_values['pbwoo_popup_theme_color'] ) ? $setting_page_values['pbwoo_popup_theme_color'] : '#03ad1d'; ?>
					<input type="color" name="pbwoo_popup_theme_color" value="<?php echo esc_attr( $pboo_value ); ?>">
					<br>
					<span class="description"><?php echo  __( 'Choose the color of your choice ', 'new-order-notification' ); ?></span>
				</td>
			</tr>
			
			
			<tr class="default-sender-id-inside">
				<th scope="row"><?php esc_html_e( 'Text Color', 'new-order-notification' ); ?></th>
				<td>
					<?php
					$pboo_value = !empty( $setting_page_values['pbwoo_popup_font_color'] ) ? $setting_page_values['pbwoo_popup_font_color'] : '#f7f7f7'; ?>
					<input type="color" name="pbwoo_popup_font_color" value="<?php echo esc_attr( $pboo_value ); ?>">
					<br>
					
					<span class="description"><?php echo  __( 'Choose the color of your choice ', 'new-order-notification' ); ?></span>
				</td>
			</tr>
			
			<tr class="default-sender-id-inside">
				<th scope="row"><?php esc_html_e( 'Popup Image/Icon', 'new-order-notification' ); ?></th>
				<td class="pbwoo_choose_file">
					<?php
					$pboo_value = !empty( $setting_page_values['pbwoo_popup_icon'] ) ? $setting_page_values['pbwoo_popup_icon'] : ''; ?>
					<input type="file" name="pbwoo_popup_icon" value="<?php echo esc_attr( $pboo_value ); ?>" class="regular-text" accept="image/gif, image/jpeg, image/png">
					<label><span><?php echo __( 'Add Imange/Icon', 'new-order-notification' ); ?></span></label>
					<br>
					
					<span class="description"><?php echo __( 'Upload an image or enter an URL(Note: Only <strong>.png,.jpeg,.jpg</strong> and <strong>.gif</strong> files are allowed).', 'new-order-notification' ); ?></span>
				</td>
			</tr>
			
			<tr class="pbwoo_button">
				<td colspan="2">
					<p class="submit" style="clear:both">
						<input type="submit" name="save" class="button button-primary" value="<?php esc_attr_e( 'Save Settings', 'new-order-notification' ); ?>">
					</p>
				</td>
			</tr>
	</table>
	</form>

	<?php
}
?>