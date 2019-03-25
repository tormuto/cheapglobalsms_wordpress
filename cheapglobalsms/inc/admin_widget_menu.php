<?php if (!defined('ABSPATH')) die('Cannot be accessed directly!'); ?>
<?php
	function cheapglobalsms_widget_page(){
		$cgsms_sub_account=get_option('cgsms_sub_account');
		$cgsms_sub_account_pass=get_option('cgsms_sub_account_pass');
		
		$temp_token='';
		if(!empty($cgsms_sub_account)&&!empty($cgsms_sub_account_pass))$temp_token="&token=".base64_encode($cgsms_sub_account.':'.$cgsms_sub_account_pass);
		
		$temp_page=empty($_GET['page'])?'':ltrim(str_replace('cheapglobalsms-widget','',$_GET['page']),'-');
		if(!empty($temp_page))$temp_token.="&a=$temp_page";
		
		echo "<iframe  src='//cheapglobalsms.com/widget?{$temp_token}' style='width:100%;min-height:650px;border:1px solid #bbb;border-radius:3px;'></iframe>";
	}
	
	function cgsms_widget_menu(){
	  $page_title = 'CheapGlobalSMS Widget';
	  $menu_title = 'CheapGlobalSMS';
	  $capability = 'manage_options';
	  $menu_slug  = 'cheapglobalsms-widget';
	  $function   = 'cheapglobalsms_widget_page';
	  $icon_url   = 'dashicons-email';
	  $position   = 4;

	  add_menu_page( $page_title,
					 $menu_title, 
					 $capability, 
					 $menu_slug, 
					 $function, 
					 $icon_url, 
					 $position );
		



	   //add sub-menus
	   $sub_menus=array(
			'send_sms'=>'Send SMS',
			'sms_log'=>'SMS Delivery Reports',
			'sms_batches'=>'SMS Batches Overview',
			'contacts'=>'Manage SMS Contacts',
			'sub_transactions'=>'SMS Sub-transaction Log',
			'coverage_list'=>'SMS Coverage & Pricing',
			//'gateway_api'=>null
	   );
		
		add_submenu_page($menu_slug, 'SMS Balance & Info', 'SMS Balance & Info',$capability,$menu_slug,$function);
	   foreach($sub_menus as $sub_menu=>$sub_menu_title){
		   $sub_menu_slug="$menu_slug-$sub_menu";
		   $sub_menu_title=ucwords(str_replace('_',' ',$sub_menu));
		   $sub_page_title="$sub_menu_title - CheapGlobalSMS";
		   
			add_submenu_page(
				$menu_slug,
				$sub_page_title,
				$sub_menu_title,
				$capability,
				$sub_menu_slug,
				'cheapglobalsms_widget_page'
			);
	   }
	}
