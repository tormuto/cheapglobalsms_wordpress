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
function cgsms_send_sms($message, $recipients, $senderid='',$send_at=0, $flash=0, $unicode=0){
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
		register_setting('cheapglobalsms', 'cgsms_enable_ui');
		
		register_setting('cheapglobalsms', 'cgsms_security_enable');
		register_setting('cheapglobalsms', 'cgsms_security_required_roles');
		register_setting('cheapglobalsms', 'cgsms_security_cookie_lifetime');
		register_setting('cheapglobalsms', 'cgsms_security_bypass_code');
	});
	
}, 9);

/*
add_action('plugins_loaded', function() {
    // load translations
    //load_plugin_textdomain('cheapglobalsms', false, 'cheapglobalsms/languages/');
});
*/