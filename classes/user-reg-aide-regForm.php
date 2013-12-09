<?php

/**
 * User Registration Aide - Registration Form Options Settings Admin Page
 * Plugin URI: http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/
 * Version: 1.3.1
 * Author: Brian Novotny
 * Author URI: http://creative-software-design-solutions.com/
*/

/*For Debugging and Testing Purposes ------------


*/

// ----------------------------------------------

/**
 * Couple of includes for functionality
 *
 * @since 1.2.0
 * @updated 1.3.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once (URA_PLUGIN_PATH."user-registration-aide.php");
require_once ("user-reg-aide-options.php");
require_once ("user-reg-aide-newFields.php");
require_once ("user-reg-aide-admin.php");

/**
 * Class for better functionality for regForm options
  *
 * @category Class
 * @since 1.3.0
 * @updated 1.3.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

class URA_REG_FORM_OPTIONS
{

	public static $instance;

	public function __construct() {
		$this->URA_REG_FORM_OPTIONS();
	}
		
	function URA_REG_FORM_OPTIONS() { //constructor
	
		global $wp_version;
		
		self::$instance = $this;
	}
		
	/**
	 * Loads and displays the User Registration Aide administration page
	 * @handles action 'add_submenu_page' line 672 user-registration-aide.php
	 * @since 1.2.0
	 * @updated 1.3.0
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function csds_userRegAide_regFormOptions(){

		global $current_user;
		$ura_options = new URA_OPTIONS(); 
		$csds_userRegAide_Options = get_option('csds_userRegAide_Options');
		if($csds_userRegAide_Options['csds_userRegAide_db_Version'] != "1.3.4"){
			$ura_options->csds_userRegAide_updateOptions();
		}
			
		if(empty($csds_userRegAide_registrationFields)){
			$ura_options->csds_userRegAide_updateRegistrationFields();
		}
		
		// add code to handle new registration form message
		// if (isset($_POST['multi_site_options'])){
			// $update = array();
			// $both = false;
			// if($_POST['csds_select_activated_now'] == 1 && $_POST['ms_non_activation_now'] == 1){
				// $both = true;
			// }
			// $msg = __('You have selected the activate now feature which will override the non activation message, you cant use both!', 'csds_userRegAide') ;
			// $update = get_option('csds_userRegAide_Options');
			// $update['ms_activate_now'] = $_POST['csds_select_activated_now'];
			// $update['ms_user_activation_message'] = esc_attr(stripslashes($_POST['csds_ms_user_activation_message']));
			// if($both == true){
				// $update['ms_non_activation_now'] = 2;
				// $update['ms_non_activation_message'] = esc_attr(stripslashes($_POST['csds_ms_non_activate_now_message']));
			// }else{
				// $update['ms_non_activation_now'] = esc_attr(stripslashes($_POST['csds_ms_non_activate_now']));
				// $update['ms_non_activation_message'] = esc_attr(stripslashes($_POST['csds_ms_non_activate_now_message']));
			// }
			// $update['ms_non_activation_now'] = esc_attr(stripslashes($_POST['csds_ms_non_activate_now']));
			// $update['ms_non_activation_message'] = esc_attr(stripslashes($_POST['csds_ms_non_activate_now_message']));
			// update_option("csds_userRegAide_Options", $update);
			// if($both == true){
				// echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Multi Site Registration/Confirmation Form Message Options updated successfully, however You have selected the activate now feature which will override the non activation message, you cant use both!', 'csds_userRegAide') .'</p></div>';
			// }else{
				// echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Multi Site Registration/Confirmation Form Message Options updated successfully.', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
			// }
			
		// add code to handle new registration form message at top of reg form
		//}
		if(isset($_POST['reg_form_message_update'])){
			// add code to handle new registration form message
			$update = array();
			$ucnt = (int) 0;
			$update = get_option('csds_userRegAide_Options');
			if(!empty($_POST['csds_select_RegFormMessage'])){
				$update['select_pass_message'] = esc_attr(stripslashes($_POST['csds_select_RegFormMessage']));
			}else{
				$ucnt ++;
			}
			if(!empty($_POST['csds_RegForm_Message'])){
				$update['registration_form_message'] = esc_attr(stripslashes($_POST['csds_RegForm_Message']));
			}else{
				$ucnt ++;
			}
			if($ucnt == 0){
				update_option("csds_userRegAide_Options", $update);
				echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Bottom Registration Form Message Options updated successfully.', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
			}else{
				echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Bottom Registration Form Message Options empty, not updated successfully.', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
			}
			
			
		// add code to handle new registration & login form redirects
		}elseif(isset($_POST['redirects_update'])){
			$update = array();
			$ucnt = (int) 0;
			$field = (string) '';
			$update = get_option('csds_userRegAide_Options');
			if(!empty($_POST['csds_registration_redirect_option'])){
				$update['redirect_registration'] = esc_attr(stripslashes($_POST['csds_registration_redirect_option']));
			}else{
				$ucnt ++;
				$field = ' Registration Redirect URL Option ';
			}
			if(!empty($_POST['csds_registration_redirect_url'])){
				$update['registration_redirect_url'] = esc_url_raw(trim($_POST['csds_registration_redirect_url']));
			}else{
				$ucnt ++;
				$field .= ' Registration Redirect URL ';
			}
			if(!empty($_POST['csds_login_redirect_option'])){
				$update['redirect_login'] = esc_attr(stripslashes($_POST['csds_login_redirect_option']));
			}else{
				$ucnt ++;
				$field .= ' Login Redirect URL Option ';
			}
			if(!empty($_POST['csds_login_redirect_url'])){
				$update['login_redirect_url'] = esc_url_raw(trim($_POST['csds_login_redirect_url']));
			}else{
				$ucnt ++;
				$field .= ' Login Redirect URL ';
			}
			update_option("csds_userRegAide_Options", $update);
			if($ucnt == 0){
			echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Login & Registration Form Custom Redirects Options updated successfully.', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
			}else{
				echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Login & Registration Form Custom Redirects Options Field '.$field.' Empty!', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
			}
		
		// Updates registration form agreement message options
		}elseif (isset($_POST['reg_form_agreement_message_update'])){
			$update = array();
			$ucnt = (int) 0;
			$field = (string) '';
			$update = get_option('csds_userRegAide_Options');
			if(!empty($_POST['csds_userRegAide_agreement_link'])){
				$update['show_custom_agreement_link'] = esc_attr(stripslashes($_POST['csds_userRegAide_agreement_link']));
			}else{
				$ucnt ++;
				$field = ' Show User Custom Agreement Link Option ';
			}
			if(!empty($_POST['csds_userRegAide_newAgreementURL'])){
				$update['agreement_link'] = esc_url_raw(trim($_POST['csds_userRegAide_newAgreementURL']));
			}else{
				$ucnt ++;
				$field .= ' Custom Agreement Link ';
			}
			if(!empty($_POST['csds_userRegAide_newAgreementTitle'])){
				$update['agreement_title'] = esc_attr(stripslashes($_POST['csds_userRegAide_newAgreementTitle']));
			}else{
				$ucnt ++;
				$field .= ' Custom Agreement Title ';
			}
			if(!empty($_POST['csds_userRegAide_show_agreement_message'])){
				$update['show_custom_agreement_message'] = esc_attr(stripslashes($_POST['csds_userRegAide_show_agreement_message']));
			}else{
				$ucnt ++;
				$field .= ' Show Custom Agreement Message Confirmation Agreement Option ';
			}
			if(!empty($_POST['csds_userRegAide_agreement_checkbox'])){
				$update['show_custom_agreement_checkbox'] = esc_attr(stripslashes($_POST['csds_userRegAide_agreement_checkbox']));
			}else{
				$ucnt ++;
				$field .= ' Show Custom Agreement Checkbox Option ';
			}
			if(!empty($_POST['csds_RegForm_Agreement_Message'])){
				$update['agreement_message'] = esc_attr(stripslashes($_POST['csds_RegForm_Agreement_Message']));
			}else{
				$ucnt ++;
				$field .= ' Custom Agreement Message ';
			}
			update_option("csds_userRegAide_Options", $update);
			if($ucnt == 0){
				echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Registration Form Agreement Message Options updated successfully.', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
			}else{
				echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Registration Form Agreement Message Options Fields '.$field.' Empty!', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
			}
			
		// Upates lost password security question settings 
		}elseif (isset($_POST['anti-bot-spammer'])){
			$update = array();
			$update = get_option('csds_userRegAide_Options');
			$registrationFields = get_option('csds_userRegAide_registrationFields');
			$newFields = get_option('csds_userRegAide_NewFields');
			//$update['add_security_question'] = esc_attr(stripslashes($_POST['csds_select_SecurityQuestion']));
			$update['activate_anti_spam'] = esc_attr(stripslashes($_POST['csds_select_AntiBot']));
			update_option("csds_userRegAide_Options", $update);
			echo '<div id="message" class="updated fade"><p class="my_message">'. __('Anti-Bot-Spammer Math Problem Options updated successfully.', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
		
		// Upates and changes profile page extra fields title 
		}elseif (isset($_POST['update_profile_title'])){
			$update = array();
			$ucnt = (int) 0;
			$update = get_option('csds_userRegAide_Options');
			if(!empty($_POST['csds_change_profile_title'])){
				$update['change_profile_title'] = esc_attr(stripslashes($_POST['csds_change_profile_title']));
			}else{
				$ucnt ++;
				$field = ' Change Profile Plugin Title Option ';
			}
			if(!empty($_POST['csds_profile_title'])){
				$update['profile_title'] = esc_attr(stripslashes($_POST['csds_profile_title']));
			}else{
				$ucnt ++;
				$field = ' Profile Plugin Title Option ';
			}
			update_option("csds_userRegAide_Options", $update);
			if($ucnt == 0){
				echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Profile Page Extra Fields Title Updated Successfully.', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
			}else{
				echo '<div id="message" class="updated fade"><p class="my_message">'. __('New Profile Page Extra Fields Title Fields '.$field.' Empty!', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
			}
		
		// Support updates
		}elseif (isset($_POST['csds_userRegAide_support_submit'])){ // Handles showing support for plugin
			$update = array();
			$update = get_option('csds_userRegAide_Options');
			$update['show_support'] = esc_attr(stripslashes($_POST['csds_userRegAide_support']));
			update_option("csds_userRegAide_Options", $update);
			echo '<div id="message" class="updated fade"><p class="my_message">'. __('Support Options updated successfully.', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
		}
		
		// Loading options prior to loading registration form options page
		$csds_userRegAide_getOptions = get_option('csds_userRegAide');
		$csds_userRegAide_registrationFields = get_option('csds_userRegAide_registrationFields');
		$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
		$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
		$csds_userRegAide_fieldOrder = get_option('csds_userRegAide_fieldOrder');
		$csds_userRegAide_Options = get_option('csds_userRegAide_Options');
					
		$cnt1 = count($csds_userRegAide_knownFields);
		if($cnt1 < 8 ){
			$ura_options->csds_userRegAide_fill_known_fields(); // Line 229 user-reg-aide-options.php
		}
		
		// Just checking to make sure options are installed before loading admin page
		if(empty($csds_userRegAide_knownFields)){
			$ura_options->csds_userRegAide_fill_known_fields(); // Line 229 user-reg-aide-options.php
		}
		if(empty($csds_userRegAide_Options)){
			$ura_options->csds_userRegAide_DefaultOptions(); // Line 57 user-reg-aide-options.php
		}
		
		// Shows Aministration Page 
		$current_user = wp_get_current_user();
		if(current_user_can('manage_options', $current_user->ID)){				
		echo '<div id="wpbody">';
			echo '<div class=wrap>';
			do_action('create_tabs', 'registration_form_options'); // Line 255 user-registration-aide.php
				echo '<form method="post" name="csds_userRegAide">';
					echo '<h2 class="adminPage">'. __('User Registration Aide: Custom Registration Form Options', 'csds_userRegAide') .'</h2>';
					echo '<div id="poststuff">';
					
					if(is_multisite()){
					// Multi Site Options here -------------------------------------------------------------------------------------------------
					echo '<div class="stuffbox"><span class="regForm">'.__('Multi-Site Options:', 'csds_userRegAide').'</span>';
						//echo '<h3 class="regForm">'.__('Multi-Site Options', 'csds_userRegAide').'</h3>';
						echo '<div class="inside">';?>
							<table class="regForm" width="100%">
							<tr>
								<td width="60%"><?php _e('Choose to allow new users to be activated right away if new users can enter own password: ', 'csds_userRegAide');?>
								<span title="<?php _e('Select this option to allow users to be activated and log in right away', 'csds_userRegAide');?>">
								<input type="radio" name="csds_select_activated_now" id="csds_select_activated_now" value="1" <?php
									if ($csds_userRegAide_Options['ms_activate_now'] == 1) echo 'checked' ;?>/> <?php _e('Yes', 'csds_userRegAide');?></span>
								<span title="<?php _e('Select this option to send confirmation link to new user registrations to confirm their registration, or the default Wordpress Multisite signup method', 'csds_userRegAide');?>">
								<input type="radio" name="csds_select_activated_now" id="csds_select_activated_now" value="2" <?php
									if ($csds_userRegAide_Options['ms_activate_now'] == 2) echo 'checked' ;?>/> <?php _e('No', 'csds_userRegAide'); ?></span></td>
								<td width="40%"><?php _e('Enter new activation message below: (HINT: Leave Links Alone!!!)', 'csds_userRegAide');?></td></tr>
							<tr>
								<td colspan="2"><textarea name="csds_ms_user_activation_message" id="csds_ms_user_activation_message" class="regForm" wrap="soft" rows="2" 
								title="<?php _e('Enter a custom message here for activation if users can create their own password and account activated right away!', 'csds_userRegAide');?>"><?php _e(esc_textarea($csds_userRegAide_Options['ms_user_activation_message']),'csds_userRegAide');?></textarea>
								</td>
							</tr>
							<tr>
								<td  width="60%"><?php _e('Choose to add a special message to confirm form if new users can&#39;t enter own password and be activated right away: ', 'csds_userRegAide'); ?>
								<input type="radio" name="csds_ms_non_activate_now" id="csds_ms_non_activate_now" value="1" <?php
									if ($csds_userRegAide_Options['ms_non_activation_now'] == 1) echo 'checked' ;?>/> <?php _e('Yes', 'csds_userRegAide');?>
									<input type="radio" name="csds_ms_non_activate_now" id="csds_ms_non_activate_now" value="2" <?php
									if ($csds_userRegAide_Options['ms_non_activation_now'] == 2) echo 'checked' ;?>/> <?php _e('No', 'csds_userRegAide'); ?></td>
								<td width="40%"><?php _e('Enter new confirmation form new activation message below: ', 'csds_userRegAide');?></td></tr>
							<tr>
								<td colspan="2"><textarea name="csds_ms_non_activate_now_message" id="csds_ms_non_activate_now_message" class="regForm" wrap="soft" rows="3" 
								title="<?php _e('Enter a custom message here for confirmation form if users can&#39;t create their own password and account is not activated right away!', 'csds_userRegAide');?>"><?php _e(esc_textarea($csds_userRegAide_Options['ms_non_activation_message']),'csds_userRegAide');?></textarea>
								</td>
							</tr>
							</table>
							<div class="submit"><input type="submit" class="button-primary" name="multi_site_options" value="<?php _e('Update Multi-Site Registration Form Options', 'csds_userRegAide'); ?>"  /></div>
						</div>
					</div>
					<?php
					} // end if for multisite options
					
					//Form for adding different message to bottom of registration form	?>
						<div class="stuffbox"><span class="regForm"><?php _e('Add Your Own Message to Bottom of Registration Form:', 'csds_userRegAide');?></span>
							<div class="inside">
							<table class="regForm" width="100%">
							<tr>
								<td width="60%"><?php _e('Choose to add a special message to bottom of registration form: ', 'csds_userRegAide');?>
								<span title="<?php _e('Select this option to add your own custom message to the bottom of the registration form', 'csds_userRegAide');?>">
								<input type="radio" name="csds_select_RegFormMessage" id="csds_select_RegFormMessage" value="1" <?php
								if ($csds_userRegAide_Options['select_pass_message'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide');?></span>
								<span title="<?php _e('Select this option to use the default Wordpress message on the bottom of the registration form',  'csds_userRegAide');?>">
								<input type="radio" name="csds_select_RegFormMessage" id="csds_select_RegFormMessage" value="2" <?php
								if ($csds_userRegAide_Options['select_pass_message'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?></span></td>
														
								<td width="40%"><?php _e('Enter new Registration form message below: ', 'csds_userRegAide');?></td></tr>
							<tr>
								<td colspan="2"><textarea name="csds_RegForm_Message" id="csds_RegForm_Message" class="regForm" wrap="soft" rows="1" 
								title="<?php _e('Enter a custom message here for bottom of registration form if users can or even can&#39t create their own password!', 'csds_userRegAide');?>"><?php _e(esc_textarea(stripslashes($csds_userRegAide_Options['registration_form_message']),'csds_userRegAide'));?></textarea>
								</td>
							</tr>
							
							</table>
							<div class="submit"><input type="submit" class="button-primary" name="reg_form_message_update" value="<?php _e('Update Registration Form Message Options', 'csds_userRegAide'); ?>"  /></div>
						</div>
					</div>
					<?php
					//Form for adding redirects to registration and login pages ?>
						<div class="stuffbox"><span class="regForm"><?php _e('Add custom redirects after users login or a new user registers here:', 'csds_userRegAide');?></span>
							<div class="inside">
							<table class="regForm" width="100%">
							<tr>
								<td width="60%"><?php _e('Choose to redirect users to a different page than default WordPress page after successful registration: ', 'csds_userRegAide');?>
								<br/>
								<span title="<?php _e('Select this option to redirect new user to custom page after successsful new registration signup',  'csds_userRegAide');?>">
									<input type="radio" name="csds_registration_redirect_option" id="csds_registration_redirect_option" value="1" <?php
									if ($csds_userRegAide_Options['redirect_registration'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide');?></span>
								<span title="<?php _e('Select this option to redirect new user to default Wordpress page after successsful new user registration signup',  'csds_userRegAide');?>">
									<input type="radio" name="csds_registration_redirect_option" id="csds_registration_redirect_option" value="2" <?php
									if ($csds_userRegAide_Options['redirect_registration'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?></span></td>
														
								<td width="40%"><?php _e('Enter new successfull Registration form redirect url below: ', 'csds_userRegAide');?></td></tr>
							<tr>
								<td colspan="2"><input type="text" name="csds_registration_redirect_url" id="csds_registration_redirect_url" class="regFormRedirect" width="75%" title="<?php _e('Enter a new url here to redirect users to after a successful registration has been completed!', 'csds_userRegAide');?>" value="<?php _e(esc_url($csds_userRegAide_Options['registration_redirect_url']),'csds_userRegAide');?>" />
								</td>
							</tr>
							<tr>
								<td width="60%"><?php  _e('Choose to redirect users to a different page than default WordPress page after successful login: ', 'csds_userRegAide');?>
								<span title="<?php _e('Select this option to redirect new user to custom page after successsful login',  'csds_userRegAide');?>">
								<input type="radio" name="csds_login_redirect_option" id="csds_login_redirect_option" value="1" <?php
								if ($csds_userRegAide_Options['redirect_login'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide');?></span>
								<span title="<?php _e('Select this option to redirect new user to default Wordpress page after successsful login',  'csds_userRegAide');?>">
								<input type="radio" name="csds_login_redirect_option" id="csds_login_redirect_option" value="2" <?php
								if ($csds_userRegAide_Options['redirect_login'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?></td></span>
														
								<td width="40%"><?php _e('Enter new successful login redirect url below: ', 'csds_userRegAide');?></td></tr>
							<tr>
								<td colspan="2"><input type="text" name="csds_login_redirect_url" id="csds_login_redirect_url" class="regFormRedirect width="75%" title="<?php _e('Enter a new url here to redirect users to after a successful login has been completed!', 'csds_userRegAide');?>" value="<?php _e(esc_url($csds_userRegAide_Options['login_redirect_url']),'csds_userRegAide');?>" />
								</td>
							</tr>
							</table>
							<div class="submit"><input type="submit" class="button-primary" name="redirects_update" value="<?php _e('Update Redirects Options', 'csds_userRegAide'); ?>"  /></div>
						</div>
					</div>
					<?php
					
					// Form for adding additional agreement to signup for website ?>
				<div class="stuffbox"><span class="regForm"><?php _e('Add Your Own Agreement Message and Policy Link with Confirmation of Agreement to Bottom of Registration Form:', 'csds_userRegAide');?> </span>
					<div class="inside">
						<table class="regForm" width="100%">
						<tr>
							<td colspan="3"><?php _e('Choose to add a special message to bottom of registration form for new users requiring them to read and agree to terms and conditions of the website:', 'csds_userRegAide'); ?></td>
						</tr>
						<tr>
							<td width="25%"><?php _e('Show Custom Link for Agreement/Guidelines/Policy Page: ', 'csds_userRegAide');?><br/>
							<span title="<?php _e('Select this option to show link for custom agreement message page on registration page',  'csds_userRegAide');?>">
							<input type="radio" id="csds_userRegAide_agreement_link" name="csds_userRegAide_agreement_link" value="1" <?php
								if ($csds_userRegAide_Options['show_custom_agreement_link'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide'); ?></span>
							<span title="<?php _e('Select this option to NOT show link for custom agreement message page on registration page',  'csds_userRegAide');?>">
								<input type="radio" id="csds_userRegAide_agreement_link" name="csds_userRegAide_agreement_link"  value="2" <?php
								if ($csds_userRegAide_Options['show_custom_agreement_link'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?></span>
							</td> 
							<td width="25%"><?php _e('Enter a title to display for link to Agreement/Guidelines/Policies URL: ', 'csds_userRegAide');?>
							<input  style="width: 200px;" type="text" title="<?php _e('Enter the title for the URL where your agreement/guidelines/policy page is located.', 'csds_userRegAide');?>" value="<?php _e($csds_userRegAide_Options['agreement_title'], 'csds_userRegAide');?>" name="csds_userRegAide_newAgreementTitle" id="csds_userRegAide_newAgreementTitle" /></td>
							<td width="50%"><?php _e('Enter Link to Agreement/Guidelines/Policies URL: ', 'csds_userRegAide');?>
							<input  style="width: 350px;" type="text" title="<?php esc_url(_e('Enter the URL where your agreement/guidelines/policy page is located . Example: (http://mysite.com/agreement.php)', 'csds_userRegAide'));?>" value="<?php _e($csds_userRegAide_Options['agreement_link'], 'csds_userRegAide');?>" name="csds_userRegAide_newAgreementURL" id="csds_userRegAide_newAgreementURL" /></td></tr>
						<tr>
							<td width="25%"><?php _e('Show Message Confirming Agreement for Agreement/Guidelines/Policy Page: ', 'csds_userRegAide');?><br/>
							<span title="<?php _e('Select this option to show custom agreement message on registration page',  'csds_userRegAide');?>">
							<input type="radio" id="csds_userRegAide_show_agreement_message" name="csds_userRegAide_show_agreement_message" value="1" <?php
							if ($csds_userRegAide_Options['show_custom_agreement_message'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide'); ?></span>
							<span title="<?php _e('Select this option NOT to show custom agreement message on registration page',  'csds_userRegAide');?>">
							<input type="radio" id="csds_userRegAide_show_agreement_message" name="csds_userRegAide_show_agreement_message"  value="2" <?php
							if ($csds_userRegAide_Options['show_custom_agreement_message'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?></span>
							</td>
							<td width="25%"><?php _e('Show Checkbox Confirming Agreement for Agreement/Guidelines/Policy Page: ', 'csds_userRegAide');?><br/>
							<span title="<?php _e('Select this option to show checkbox for user to check stating they agree to the agreement terms and conditions',  'csds_userRegAide');?>">
							<input type="radio" id="csds_userRegAide_agreement_checkbox" name="csds_userRegAide_agreement_checkbox" value="1" <?php
								if ($csds_userRegAide_Options['show_custom_agreement_checkbox'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide'); ?></span>
							<span title="<?php _e('Select this option NOT to show checkbox for user to check stating they agree to the agreement terms and conditions',  'csds_userRegAide');?>">
							<input type="radio" id="csds_userRegAide_agreement_checkbox" name="csds_userRegAide_agreement_checkbox"  value="2" <?php
								if ($csds_userRegAide_Options['show_custom_agreement_checkbox'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?></span>
							</td>
							<td width="50%"><?php _e('Add your special message below to add to bottom of registration form if new users must agree to terms or policies:', 'csds_userRegAide');?></td>
						<tr>
							<td colspan="3"><textarea name="csds_RegForm_Agreement_Message" id="csds_RegForm_Agreement_Message" class="regForm" rows="1" 
							title="<?php _e('Enter a custom message here for bottom of registration form if users can create their own password or for other reasons!', 'csds_userRegAide');?>"><?php _e(esc_textarea(stripslashes($csds_userRegAide_Options['agreement_message']),'csds_userRegAide'))?></textarea>
							</td>
						</tr></table>
							<div class="submit"><input type="submit" class="button-primary" name="reg_form_agreement_message_update" value="<?php _e('Update Registration Form Agreement Options', 'csds_userRegAide');?>" /></div>
						</div>
					</div>
					<?php 	//Form for adding security math problem to registration form
					?>
					<div class="stuffbox"><span class="regForm"><?php _e('Add Anti-Bot-Spammer for Registration Form:', 'csds_userRegAide');?> </span>
						<div class="inside">
						<table class="regForm" width="100%">
							<tr><?php
							echo '<td width="60%">'. __('Choose to add a anti-spammer anti-bot math problem to the registration form to help reduce spammers and bots accessing your site and spamming it: ', 'csds_userRegAide');?>
							<span title="<?php _e('Select this option to activate the anti-spam math problem to registration form',  'csds_userRegAide');?>">
							<input type="radio" name="csds_select_AntiBot" id="csds_select_AntiBot" value="1" <?php
								if ($csds_userRegAide_Options['activate_anti_spam'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide');?></span>
							<span title="<?php _e('Select this option to de-activate the anti-spam math problem to registration form',  'csds_userRegAide');?>">
								<input type="radio" name="csds_select_AntiBot" id="csds_select_AntiBot" value="2" <?php
								if ($csds_userRegAide_Options['activate_anti_spam'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?></span></td>
							</tr>
						</table>
						<div class="submit"><input type="submit" class="button-primary" name="anti-bot-spammer" value="<?php _e('Update Anti-Bot-Spammer Math Problem Options', 'csds_userRegAide'); ?>"  /></div>
						</div>
					</div>
					<?php//Form for changing profile extra fields title ?>
					<div class="stuffbox"><span class="regForm"><?php _e('Change Title For User Profiles Page:', 'csds_userRegAide');?> </span>
					<?php
						echo '<div class="inside">';
						echo '<table class="regForm" width="100%">';
						echo '<tr>';?>
								<td width="40%"><?php _e('Choose to change the title for extra fields on the users profile page: ', 'csds_userRegAide'); ?>
								<br />
								<span title="<?php _e('Select this option to add your own special title to the extra fields portion of the users profile',  'csds_userRegAide');?>">
								<input type="radio" name="csds_change_profile_title" id="csds_change_profile_title" value="1" <?php
								if ($csds_userRegAide_Options['change_profile_title'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide');?></span>
								<span title="<?php _e('Select this option to keep the default title to the extra fields portion of the users profile',  'csds_userRegAide');?>">
								<input type="radio" name="csds_change_profile_title" id="csds_change_profile_title" value="2" <?php
								if ($csds_userRegAide_Options['change_profile_title'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?></span></td>
								<td width=60%><?php _e('Extra Fields Title: ', 'csds_userRegAide');?>
								<br />
								<input style="width: 90%;" type="text" title="<?php _e(esc_attr('Enter the new title that you would like to have for the extra fields portion on the users profile page here:'), 'csds_userRegAide');?>" value="<?php _e(esc_attr($csds_userRegAide_Options['profile_title']), 'csds_userRegAide');?>" name="csds_profile_title" id="csds_profile_title" /></td>
							</tr>
						</table>
						<div class="submit"><input type="submit" class="button-primary" name="update_profile_title" value="<?php _e('Update Profile Title Options', 'csds_userRegAide'); ?>"  /></div>
						</div>
					</div>
					<?php // Show support section here
					do_action('show_support'); // Line 256 user-registration-aide.php
				echo '</div>';   
				echo '</div>';   
			echo '</form>';
		echo '</div>';
	echo '</div>';
		}else{
			wp_die(__('You do not have permissions to manage options for this plugin, sorry, please check with the site administrators to resolve this issue please!'));
		}
	}
}
?>