<?php 
global $order_notify_image_url, $order_notify_sound_url, $woo_fs;
$pbwoo_settings = get_option( 'nop-notification-settings' ) ;
$wpb_popupbgcolor = '#03ad1d';
$wpb_soundfile = $order_notify_sound_url ;
$wpb_font_color = '#fff';
$wpb_notif_msg = __( 'You Got a New Order', 'new-order-notification' );
$wpb_icon = $order_notify_image_url ;
$wpb_btn_text = __( 'Ok', 'new-order-notification' );
if ( $woo_fs && $woo_fs->is__premium_only() ) {
	if ( $woo_fs->can_use_premium_code() ) {
		if( $pbwoo_settings ) {
		   $wpb_popupbgcolor = ( $pbwoo_settings['pbwoo_popup_theme_color'] != '' || $pbwoo_settings['pbwoo_popup_theme_color'] !== false ) ? $pbwoo_settings['pbwoo_popup_theme_color'] : '#03ad1d';
		   $wpb_soundfile = $order_notify_sound_url ;
		   $wpb_font_color = ( $pbwoo_settings['pbwoo_popup_font_color'] != ''  || $pbwoo_settings['pbwoo_popup_font_color'] !== false )  ? $pbwoo_settings['pbwoo_popup_font_color'] : '#fff';
		   $wpb_notif_msg = ($pbwoo_settings['pbwoo_notification_msg'] != '' || $pbwoo_settings['pbwoo_notification_msg'] !== false  ) ? $pbwoo_settings['pbwoo_notification_msg'] : $wpb_notif_msg;
		   $wpb_icon = $order_notify_image_url ;
		   $wpb_btn_text = ( $pbwoo_settings['pbwoo_btn_text'] != '' || $pbwoo_settings['pbwoo_btn_text'] !== false ) ? $pbwoo_settings['pbwoo_btn_text'] : __( 'Ok', 'new-order-notification' ) ;
		}
	}
}
?>  
  <style>
	   .mfp-bg{
		   background:<?php echo esc_attr( $wpb_popupbgcolor );?>!important;
		   opacity:1!important;
		   z-index:9999!important;
	   }
	   .mfp-wrap {
		   justify-content:center!important;
		   align-items:center!important;
		   z-index:10000!important;
	   }
	   .btn-got-it{
		  background-color: red;
		  border: none;
		  color: white;
		  width: 270px;
		  padding: 24px 32px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 24px;
		  margin: 4px 2px;
	     font-weight:700;
		  border-radius:8px;
		  opacity:0.9;
		  cursor: pointer;
		}
	   .btn-got-it:hover{
		   opacity:1;
	   }
</style>
<script type="text/javascript">
jQuery(function($) {
	  
 function nopShowPopup( orderId ){
		let cuHtml = '';
		cuHtml = '<center><div id="test-modal" class=" white-popup-block">';
		cuHtml += '<img width="150px" height="150px" src="<?php echo esc_url( $wpb_icon );?>">';
		cuHtml += '<h1 style="color:<?php echo esc_attr( $wpb_font_color );?>;"><?php echo esc_html( $wpb_notif_msg );?> #'+ orderId + '</h1>';
		cuHtml += '<p style="color:<?php echo esc_attr( $wpb_font_color );?>; display:none;"><?php esc_html_e( 'You won\'t be able to dismiss this by usual means (escape or click button), but you can close it programatically based on user choices or actions.', 'new-order-notification'); ?></p>';
		cuHtml += '<form id="pbnew_form" method="post">'; 
		cuHtml += '<p style="color:white;"><button type="submit" class=" btn-got-it popup-modal-dismiss"  name="pbnew_confessed"><?php echo esc_html( $wpb_btn_text );?></button></p>';
		cuHtml += '</form>';
		cuHtml += '</div></center>';
  
	$.magnificPopup.open({
	 items: {
		src: cuHtml,
		type:'inline'
		},
	callbacks: {
		open: function() {
			
		},
		close: function() {
		 
		}
	},
	modal: true
	});
 }
	 //part -2
 var do_req = true;
<?php 
$wpb_soundfile1 = $order_notify_sound_url;
$wpb_font_color1 = '#fff';
$wpb_popupbgcolor1 = '#00FF00';
$pbwoo_settings1 = get_option( 'nop-notification-settings' ) ;
if( $pbwoo_settings1 ){
   $wpb_popupbgcolor1 = $pbwoo_settings1['pbwoo_popup_theme_color'] ?? 'green';
   $wpb_soundfile1 = $order_notify_sound_url;
   $wpb_font_color1 = $pbwoo_settings1['pbwoo_popup_font_color'] ?? '#fff';
}
?>
function nop_new_order_beep(){
	let loadSound = new Audio("<?php echo esc_url( $wpb_soundfile1 );?>");  	
	loadSound.addEventListener("canplaythrough", (event) => {
	  /* the audio is now playable; play it if permissions allow */
	  loadSound.play();
	});
}

function nopSendRequest(){
	jQuery.ajax({
	url: ajaxurl,
	type: 'POST',
	dataType: 'json',
	data: {action : 'check_new_order'},
	success: function(response) {
	   if(response.norder === 1){
		   do_req = false;
		   nopShowPopup( response.oid );
		   setInterval( function(){
			   nop_new_order_beep();
			   
		   },4000);
	   }
		else{
			do_req = true;
			return;
		}
	 }

	});
}
if( do_req === true ){
	 setInterval( function(){
		nopSendRequest();
	},30000);	
}
  });
</script>
<?php 
 if( isset( $_POST['pbnew_confessed']) ){
	 $wpb_confessed = array(
	 'new_order_id' => 0,
	 'nop_new_order_stat' => 0
	 );
	 update_option( 'nop_new_order', $wpb_confessed );
 }
?>