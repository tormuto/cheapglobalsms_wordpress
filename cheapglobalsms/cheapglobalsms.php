<?php
/*
Plugin Name: Cheap Global SMS
Plugin URI:  https://github.com/tormuto/cheapglobalsms_wordpress
Description: Send or broadcast SMS through WordPress; Add SMS authentication.
Version:     1.0.0
Author:      Tormuto
Author URI:  http://tormuto.com
License:     MIT
License URI: https://opensource.org/licenses/MIT
Text Domain: cgsms
Domain Path: /languages
*/
if (!defined('ABSPATH')) die('Cannot be accessed directly!');
const CHEAPGLOBALSMS_VERSION = '1.0.0';
function _cgsms_dir(){ return __DIR__; }

function _cgsms_url(){
    static $dir;
    if ($dir) return $dir;
    $dir = plugin_dir_url(__FILE__);
    return $dir;
}

/*
	sms sending interface; available for use everywhere.
*/
function cgsms_send_sms($message, $recipients, $senderid='',$send_at=0, $flash=0, $unicode=null){
	if(is_array($recipients)){
		$contacts=array();
		$first=current($recipients);
		if(is_array($first)){
			$contacts=$recipients;
			$recipients=$first['phone'];
		}
		else $recipients=implode(',',$recipients);
	}
	$cgsms_sub_account=get_option('cgsms_sub_account');
	$cgsms_sub_account_pass=get_option('cgsms_sub_account_pass');
	$senderid=$senderid?:get_option('cgsms_default_sender');
	
	$default_unicode=get_option('cgsms_default_unicode',0);
    if($unicode===null)$unicode=$default_unicode;
    
	$post_data=array(
	'sub_account'=>$cgsms_sub_account,
	'sub_account_pass'=>$cgsms_sub_account_pass,
	'action'=>'send_sms',
	'sender_id'=>$senderid,
	'recipients'=>$recipients,
	'message'=>$message
	);
	if(!empty($contacts))$post_data['contacts']=$contacts;
	
	if(!empty($flash))$post_data['type']=$flash;
	if(!empty($send_at))$post_data['send_at']=date('Y-m-d H:i',$send_at);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,'http://cheapglobalsms.com/api_v1');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$response = curl_exec($ch);
	$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if($response_code != 200)$response=curl_error($ch);
	curl_close($ch);
	
	$resps=array('total'=>0);

	if($response_code != 200)$resps['error']="HTTP ERROR $response_code: $response";
	else {
		$json=@json_decode($response,true);
		
		if($json===null)$resps['error']="INVALID RESPONSE: $response"; 
		elseif(!empty($json['error']))$resps['error']=$json['error'];
		else {
			//$json['total'];
			return $json['batch_id'];
		}
	}
	
	return new WP_Error('CGSMS_FAIL', $resps['error']);
}

add_action('init', function () {
    $D = _cgsms_dir();
	
    if (get_option('cgsms_enable_ui')) {
        include "$D/inc/admin_widget_menu.php";
		add_action( 'admin_menu', 'cgsms_widget_menu' );
    }
	
	if (!is_admin()) {
		include "$D/inc/shortcode.php";
	}

	if (get_option('cgsms_security_enable')) {
        include "$D/inc/security_two_factor.php";
    }
	
    if (!current_user_can('edit_others_posts')) return;

	add_action('admin_menu', function () {
		if (!current_user_can('activate_plugins')) return;

		add_submenu_page('options-general.php', __('CheapGlobalSMS Settings', 'cheapglobalsms'), __('CheapGlobalSMS Settings', 'cheapglobalsms'), 'administrator', 'cheapglobalsms', function () {
			wp_enqueue_script('jquery-ui-tooltip');
			wp_enqueue_script('jquery-ui-sortable');
			include _cgsms_dir() . "/tpl/settings_page.php";
		});
	});

	add_action('admin_init', function () {
		register_setting('cheapglobalsms', 'cgsms_sub_account');
		register_setting('cheapglobalsms', 'cgsms_sub_account_pass');
		register_setting('cheapglobalsms', 'cgsms_default_sender');
		register_setting('cheapglobalsms', 'cgsms_default_unicode');
		register_setting('cheapglobalsms', 'cgsms_enable_ui');
		
		register_setting('cheapglobalsms', 'cgsms_security_enable');
		register_setting('cheapglobalsms', 'cgsms_security_required_roles');
		register_setting('cheapglobalsms', 'cgsms_security_cookie_lifetime');
		register_setting('cheapglobalsms', 'cgsms_security_bypass_code');
        
		register_setting('cheapglobalsms', 'cgsms_notif_wc-new');
		register_setting('cheapglobalsms', 'cgsms_notif_wc-payment');
        
        if(function_exists('wc_get_order_statuses')){
            $woo_statuses=wc_get_order_statuses();
            foreach($woo_statuses as $woo_status=>$woo_status_descr)
                register_setting('cheapglobalsms', "cgsms_notif_$woo_status");            
        }
	});
	
}, 9);

/*
add_action('plugins_loaded', function() {
    // load translations
    //load_plugin_textdomain('cheapglobalsms', false, 'cheapglobalsms/languages/');
});
*/

//https://demo.wp-sms-pro.com/wp-admin/admin.php?page=wp-sms-pro&tab=wc
function _cgsms_replace_placeholders($template,$order,array $more_values=array()){
    $values=array();
    $values['billing_first_name']=$order->get_billing_first_name();
    $values['billing_last_name']=$order->get_billing_last_name();
    $values['billing_company']=$order->get_billing_company();
    $values['billing_address']=$order->get_billing_address_1();
    $values['order_id']=$order->get_id(); //$order->order_id;
    $values['order_number']=$order->get_order_number();
    $values['order_total']=$order->get_formatted_order_total();
    $values['status']=$order->get_status();
    if(is_array($more_values)&&!empty($more_values))$values=array_merge($values,$more_values);
    
    $find=array(); $replace=array();
    foreach($values as $rk=>$rv){
        $find[]="%$rk%"; $replace[]=$rv;
    }
    return str_ireplace($find,$replace,$template);
}


function cgsms_woo_order_status_changed($order_id,$old_status,$new_status) {
    $order = wc_get_order($order_id);
    $recipient=$order->get_billing_phone('view');
    if(empty($recipient))return;    
    $message_template=trim(get_option("cgsms_notif_wc-$new_status"));
    if(empty($message_template))return;
    
    $message=_cgsms_replace_placeholders($message_template,$order);
    cgsms_send_sms($message,$recipient);
}
add_action('woocommerce_order_status_changed', 'cgsms_woo_order_status_changed', 10, 3);

add_action('woocommerce_new_order',function($order_id){
    $order = wc_get_order($order_id);
    $recipient=$order->get_billing_phone('view');
    if(empty($recipient))return;
    $message_template=trim(get_option("cgsms_notif_wc-new"));
    if(empty($message_template))return;
    $message=_cgsms_replace_placeholders($message_template,$order);
    cgsms_send_sms($message,$recipient);
});

add_action('woocommerce_payment_complete',function($order_id){
    $order = wc_get_order($order_id );
    $recipient=$order->get_billing_phone('view'); //or 'edit'
    //$recipient=$order->billing_phone;
    if(empty($recipient))return;    
    $message_template=trim(get_option("cgsms_notif_wc-payment"));
    if(empty($message_template))return;
    /*
    $user = $order->get_user();
    if($user ){} // do something with the user
    */
    $message=_cgsms_replace_placeholders($message_template,$order);
    cgsms_send_sms($message,$recipient);
});

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'cheapglobalsms_add_plugin_page_settings_link');
function cheapglobalsms_add_plugin_page_settings_link($links){
	$links[]='<a href="'.admin_url('options-general.php?page=cheapglobalsms').'">'.__('Settings').'</a>';
	return $links;
}