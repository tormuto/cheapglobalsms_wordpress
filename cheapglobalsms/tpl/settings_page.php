<?php if (!defined('ABSPATH')) die('Cannot be accessed directly!'); ?>
	<style type='text/css'>
		.custom-nav-tabs{
			margin: 0px;
			padding: 0px;
			list-style: none;
		}
		.custom-nav-tabs .nav-tab{
			background: none;
			color: #222;
			display: inline-block;
			padding: 10px 15px;
			cursor: pointer;
		}

		.custom-nav-tabs .nav-tab.active{background:#ffffff;color:#222;}
		.custom-nav-tabs .nav-tab{text-decoration:none;}
		.custom-nav-pane{display: none;background: #ededed;}
		.custom-nav-pane.active{display: inherit;}
		.custom-nav-panes{clear:both !important;}
	</style>
	
	<div class="wrap">
		<h2><?php _e('CheapGlobalSMS Settings', 'cheapglobalsms'); ?></h2>
		
		<div class='custom-nav-tabs' id='settings_tabs'>
			<a class='active nav-tab' href='#baseTab'>
				<?php _e('General settings', 'cheapglobalsms'); ?>
			</a>
			<a class='nav-tab' href='#smsPortalTab'>
				<?php _e('SMS Portal', 'cheapglobalsms'); ?>
			</a>
			<a class='nav-tab' href='#buildShortcodeTab'>
				<?php _e('Build Shortcode', 'cheapglobalsms'); ?>
			</a>
			<a class='nav-tab' href='#securityTab'>
				<?php _e('Security / 2fa', 'cheapglobalsms'); ?>
			</a>
		</div>
		<div class='custom-nav-panes'>			
			<form method="post" action="options.php">
			<?php settings_fields('cheapglobalsms'); ?>
			
			<?php do_settings_sections('cheapglobalsms'); ?>
            <div class='custom-nav-pane active' id='baseTab'>
                <p>
                    <?php $link = [':link' => '<a href="https://cheapglobalsms.com/sub_accounts" target="_blank"><strong>CheapGlobalSMS here</strong></a>']; ?>
                    <?= strtr(__('Please enter your sub-account details below. You find create and manage your sub-account credentials from :link.', 'cheapglobalsms'), $link); ?>
                </p>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Sub-account', 'cheapglobalsms'); ?></th>
                        <td><input type="text" name="cgsms_sub_account" value="<?php echo esc_attr(get_option('cgsms_sub_account')); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Sub-account Password', 'cheapglobalsms'); ?></th>
                        <td><input type="text" name="cgsms_sub_account_pass"
                                   value="<?php echo esc_attr(get_option('cgsms_sub_account_pass')); ?>" size="64"/></td>
                    </tr>
					<tr valign="top">
                        <th scope="row"><?php _e('Default sender', 'cheapglobalsms'); ?></th>
                        <td>
                            <label>
                                <input type="text" maxlength="15" name="cgsms_default_sender"
                                       value="<?= esc_attr(get_option('cgsms_default_sender')); ?>">
                            </label>
                            <p class="help-block description">
                                <?php _e('Must consist of at most 11 characters or 15 digits.', 'cheapglobalsms'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Enable sending UI', 'cheapglobalsms'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox"
                                       name="cgsms_enable_ui" <?= get_option('cgsms_enable_ui') ? 'checked' : ''; ?>>
                                <?php _e('Yes, enable the SMS sending UI', 'cheapglobalsms'); ?>
                            </label>
                            <p class="help-block description">
                                <?php _e('Enabling this adds a new admin menu for sending SMSs and listing sent messages, as well as managing contacts.', 'cheapglobalsms'); ?>
                            </p>
                        </td>
                    </tr>
					<tr valign="top">
                        <th scope="row"><?php _e('Enable security-tab', 'cheapglobalsms'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox"
                                       name="cgsms_security_enable" <?= get_option('cgsms_security_enable') ? 'checked' : ''; ?>
                                       id="cgsmsSecurityEnable"
                                       value="1">
                                <?php _e('Yes, enable the security settings tab', 'cheapglobalsms'); ?>
                            </label>
                            <p class="help-block description">
                                <?php _e('Used to enable two-factor security for logging into your WordPress backend.', 'cheapglobalsms'); ?>
                                <i class="info has-tooltip"
                                   title="<?= esc_attr(__('Enabling two-factor forces users with certain roles, to connect their cellphone with their user account. Afterwards an additional step is added after the user/password-prompt, which asks for a one-time code. This code is immediately sent via SMS to the users cellphone. It is possible to remember a device for an extended period of time, so the user will not have to reauthorize all the time.', 'cheapglobalsms')) ?>"></i>
                            </p>
                        </td>
                    </tr>
                   
                </table>
				
				<hr>
				<?php submit_button(); ?>
            </div>
			
			<div class='custom-nav-pane' id='securityTab'>
                <p>
                    <?= __('The two-factor login system is based solely on SMS, so your users will not need any apps, thus making it compatible with any mobile phone. All you will ever pay, is the cost of the text messages sent as part of the login process, while getting the greatly added security of two-factor security.', 'cheapglobalsms'); ?>
                </p>

                <table class="form-table">
                    <?php if (get_option('cgsms_security_enable')): ?>
                        <tr valign="top">
                            <th scope="row"><?php _e('Emergency bypass URL', 'cheapglobalsms'); ?></th>
                            <?php
                            $login_bypass_url = wp_login_url();
                            $login_bypass_url .= (strpos($login_bypass_url, '?') === false) ? '?' : '&';
                            $login_bypass_url .= 'action=gwb2fa&c='.CgsmsSecurityTwoFactor::getBypassCode();
                            ?>
                            <td>
                                <input type="hidden" name="cgsms_security_bypass_code" value="<?= CgsmsSecurityTwoFactor::getBypassCode(); ?>" />
                                <input type="text" size="85" readonly value="<?= $login_bypass_url; ?>" placeholder="<?php _e('New code is generated on save', 'cheapglobalsms'); ?>" /> <button id="cgsmsSecurityBypassCodeReset" type="button" class="button button-secondary"><?php _e('Reset', 'cheapglobalsms'); ?></button>
                                <p class="help-block description">
                                    <strong style="color: blue"><?php _e('This URL should be copied to a safe place!', 'cheapglobalsms'); ?></strong> <?php _e('Use it to bypass all two-factor security measures when logging in.', 'cheapglobalsms'); ?>
                                    <i class="info has-tooltip"
                                       title="<?= esc_attr(__('This could rescue you from a site lockout, in case your CheapGlobalSMS-account ran out of credit (you should enable auto-charge to avoid this) or if you forgot to update your profile when you got a new number. You should not share this URL, but keep it as a recovery measure for you as an administrator.', 'cheapglobalsms')) ?>"></i>
                                </p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('User roles with two-factor', 'cheapglobalsms'); ?></th>
                            <td>
                                <?php $roles = CgsmsSecurityTwoFactor::getRoles(); ?>
                                <select name="cgsms_security_required_roles[]" multiple size="<?= min(count($roles), 6); ?>" style="min-width: 250px;">
                                    <?php foreach($roles as $role_key => $role_opt): ?>
                                        <option value="<?= $role_key ?>" <?= $role_opt[1]?'selected':'' ?>><?= $role_opt[0]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="help-block description">
                                    <?php _e('All roles selected will be forced to upgrade to two-factor on their next login. We recommend that all roles above subscriber-level are selected.', 'cheapglobalsms'); ?>
                                    <br>
                                    <?php _e('Hold down CTRL (PC) / CMD (Mac) to select multiple roles.', 'cheapglobalsms'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Two-factor cookie lifetime', 'cheapglobalsms'); ?></th>
                            <td>
                                <select name="cgsms_security_cookie_lifetime">
                                    <?php $options = [
                                        0 => __('Re-authorize with every login', 'cheapglobalsms'),
                                        1 => __('Remember for up to 1 day', 'cheapglobalsms'),
                                        7 => __('Remember for up to 1 week', 'cheapglobalsms'),
                                        14 => __('Remember for up to 2 weeks', 'cheapglobalsms'),
                                        30 => __('Remember for up to 1 month', 'cheapglobalsms')
                                    ]; ?>
                                    <?php foreach ($options as $days => $text): ?>
                                        <option <?= get_option('cgsms_security_cookie_lifetime') == $days ? 'selected' : ''; ?> value="<?= $days; ?>"><?= $text; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="help-block description">
                                    <?php _e('How often must the user be forced to re-authorize via two-factor, when visiting from the same web browser?', 'cheapglobalsms'); ?>
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
				
				<hr>
				<p class="submit"><input type="submit" name="submit"  class="button button-primary" value="Save Changes"  /></p>
            </div>
			</form>
			
            <div class='custom-nav-pane' id='smsPortalTab'>
				<?php 
					$temp_token='';
					$cgsms_sub_account=get_option('cgsms_sub_account');
					$cgsms_sub_account_pass=get_option('cgsms_sub_account_pass');
					if(!empty($cgsms_sub_account)&&!empty($cgsms_sub_account_pass))$temp_token="?token=".md5($cgsms_sub_account.':'.$cgsms_sub_account_pass);
				?>
				<iframe 
					src='//cheapglobalsms.com/widget<?php echo $temp_token; ?>' 
					style='width:100%;height:560px;border:1px solid #bbb;border-radius:3px;'>
					</iframe>
			</div>
			
            <div class='custom-nav-pane' id='buildShortcodeTab'>
				<div>
					<h2 style='margin-top:5px;'><i class='fa fa-code'></i> Shortcode Generator</h2>
					<hr/>
					<p>Generate and copy short-code here, which you can then copy into any of your article, for it to be automatically replaced with the corresponding CheapGlobalSMS interface</p>
					<div class='clearfix'></div>
					<div id='cgsms_shortcode_generator' >
						<div class='form-group'>
							<label>Sub-account</label>
							<input type='text' name='temp_sub_account' id='temp_sub_account' />
						</div>
						<div class='form-group'>
							<label>Sub-account Password</label>
							<input type='password' name='temp_sub_account_pass' id='temp_sub_account_pass' />
						</div>
						
						<div class='form-group'>
							<label>
								<input type='checkbox' value='1' name='no_tabs' />
								Hide Tabs/Menu Bar
							</label>
							<label>
								<input type='checkbox' value='1' name='no_translate' />
								Hide Language Switch
							</label>
							<label>
								<input type='checkbox' value='1' name='disable_login' />
								Disable Login Page <small>(only access through token)</small>
							</label>
						</div>

						<?php
							$all_tabs_list=array('account'=>1,'send_sms'=>1,'sms_log'=>1,'sms_batches'=>1,'contacts'=>1,'sub_transactions'=>1,'coverage_list'=>1,'gateway_api'=>1);
						?>	
						<div class='form-group'>
							<label>Tabs Menu</label>
							<select class='form-control input-sm' id='cgsms_shortcode_multiple_option' name='tabs_list[]' multiple>
								<option value=''>ALL</option>
								<?php foreach($all_tabs_list as $tab=>$tab_flag){ ?>
									<option value='<?php echo $tab; ?>'><?php echo ucwords(str_replace('_',' ',$tab)); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class='clearfix'></div>
					<h4 id='generated_shortcode_div'></h4>
					<div>Shortcodes generated here can also be used directly inside template source code with php function, like: <code>&lt;?php echo do_shortcode("[cgsms]"); ?></code></div>
				</div>
			</div>
		</div>
	</div>

	<script type='text/javascript'>
		jQuery(function($){
			$('#cgsms_shortcode_generator [name]').on('change keyup',function(){
				var str="[cgsms";
				$('#cgsms_shortcode_generator input[type=checkbox]').each(function(){
					var temp_val=$(this).is(':checked')?1:0;
					var temp_name=$(this).attr('name');
					str+=" "+temp_name+"='"+temp_val+"'";
				});
				
				var temp_tab_list=[];
				
				if($('#cgsms_shortcode_multiple_option option:selected').length){
					if(!$("#cgsms_shortcode_multiple_option option[value='']").is(':selected')){
						$('#cgsms_shortcode_multiple_option option:selected').each(function(){
							var temp_val=$(this).attr('value');
							if(temp_val!='')temp_tab_list.push(temp_val);
						});
					}
					else {
						$('#cgsms_shortcode_multiple_option option').each(function(){
							var temp_val=$(this).attr('value');
							if(temp_val!='')temp_tab_list.push(temp_val);
						});
					}
				}
				
				if(temp_tab_list.length){
					str+=" tabs_list='"+(temp_tab_list.join(','))+"' ";
				}
				
				var temp_name=$('#temp_sub_account').val();
				var temp_pass=$('#temp_sub_account_pass').val();
				if(temp_name!=''||temp_pass!=''){
					var token_str=base64_encode(temp_name+':'+temp_pass);
					str+=" token='"+token_str+"' ";
				}
				
				str+=" ]";
				
				$('#generated_shortcode_div').html("<strong>Generated Shortcode:</strong> <code>"+str+"</code>");
			});
			
			$('#cgsms_shortcode_multiple_option').trigger('change');
			
			$('.custom-nav-tabs .nav-tab').on('click',function(evt){
				evt.preventDefault();
				var tab_id = $(this).attr('href');
				$('.custom-nav-tabs .nav-tab').removeClass('active');
				$('.custom-nav-pane').removeClass('active');

				$(this).addClass('active');
				$(tab_id).addClass('active');
			})
		});

		var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
		function base64_encode(str){ return Base64.encode(str); }
		function base64_decode(str){ Base64.decode(str); }		
	</script>

