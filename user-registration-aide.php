<?php
/*
Plugin Name: User Registration Aide
Plugin URI: http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/
Plugin Description: Forces new users to register additional fields with the option to add additional fields other than those 
supplied with the default Wordpress Installation. We have kept it simple in this version for those of you whom aren't familiar with 
handling multiple users or websites. We also are currently working on expanding this project with a paid version which will contain 
alot more features and options for those of you who wish to get more control over users and user access to your site.
Version: 1.1.1
Author: Brian Novotny
Author URI: http://creative-software-design-solutions.com/
Text Domain: user-registration-aide

User Registration Aide Requires & Adds More Fields to User Registration & Profile - Forces new users to register additional fields 
on registration form, and gives you the option to add additional fields at your discretion. Gives you more control over who registers, 
and allows you to manage users easier!
Copyright (c) 2012 Brian Novotny

Users Registration Helper - Forces new users to register additional fields
Copyright (c) 2012 Brian Novotny
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/


require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
include ("user-reg-aide-admin.php");
include ("user-reg-aide-newFields.php");

//For Debugging and Testing Purposes ------------

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//$ebitd = ini_get('error_reporting');
//error_reporting($ebits ^ E_NOTICE);

// ----------------------------------------------

/**
 * Actions and Hooks to register
 *
 * @since 1.0.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

register_activation_hook(__FILE__, 'csds_userRegAide_install');
add_action('register_form', 'csds_userRegAide_addFields');
add_action('register_post', 'csds_userRegAide_checkFields', 10, 3);
add_action('user_register', 'csds_userRegAide_updateFields', 10, 1);
add_action('admin_menu', 'csds_userRegAide_optionsPage');
add_action('admin_menu', 'csds_userRegAide_editNewFields_optionsPage');
add_action('show_user_profile', 'csds_show_user_profile');
add_action('edit_user_profile', 'csds_show_user_profile');
add_action('personal_options_update', 'csds_update_user_profile');
add_action('edit_user_profile_update', 'csds_update_user_profile');
add_action('profile_update', 'csds_update_user_profile');
add_action('init', 'csds_userRegAide_translationFile');
add_filter('pre_user_first_name','esc_html');
add_filter('pre_user_first_name','strip_tags');
add_filter('pre_user_first_name','trim');
add_filter('pre_user_first_name','wp_filter_kses');
add_filter('pre_user_last_name','esc_html');
add_filter('pre_user_last_name','strip_tags');
add_filter('pre_user_last_name','trim');
add_filter('pre_user_last_name','wp_filter_kses');
add_filter('pre_user_nickname','esc_html');
add_filter('pre_user_nickname','strip_tags');
add_filter('pre_user_nickname','trim');
add_filter('pre_user_nickname','wp_filter_kses');
add_filter('pre_user_url','clean_url');
add_filter('pre_user_url','strip_tags');
add_filter('pre_user_url','trim');
add_filter('pre_user_url','wp_filter_kses');
add_filter('pre_user_description','esc_html');
add_filter('pre_user_description','strip_tags');
add_filter('pre_user_description','trim');
add_filter('pre_user_description','wp_filter_kses');
register_deactivation_hook(__FILE__, 'csds_userRegAide_deactivation');
//register_uninstall_hook(__FILE__, 'uninstall.php');

/**
 * Adds the translation directory to the plugin folder
 *
 * @since 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_translationFile(){
	
	$plugin_path = plugin_basename( dirname(__FILE__).'/translations' );
	load_plugin_textdomain('user-registration-aide', '', $plugin_path );
}


/**
 * Add the management page to the user settings bar
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_optionsPage(){
	if(function_exists('add_menu_page')){
	
	add_menu_page('User Registration Aide','User Registration Aide', 'manage_options','user-registration-aide', 'csds_userRegAide_myOptionsSubpanel', '', 71 );
    
	}
}

/**
 * Add the Add-Edit New Fields management page to the user settings bar
 *
 * @since 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_editNewFields_optionsPage(){
	if(function_exists('add_submenu_page')){
	
	add_submenu_page('user-registration-aide','Edit New Fields', 'Edit New Fields', 'manage_options', 'edit-new-fields', 'csds_userRegAide_editNewFields' );
    
	}
}

/**
 * Installs plugin
 *
 * @since 1.0.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
	
function csds_userRegAide_install()
{
	
	$csds_userRegAide_knownFields = array(
	"first_name"	=> "First Name",
	"last_name"		=> "Last Name",
	"nickname"		=> "Nickname",
	"aim"			=> "AIM",
	"yim"			=> "Yahoo IM",
	"jabber"		=> "Jabber / Google Talk",
	"description"   => "Biographical Info"
	);
	
	// Creates a database version for future upgrades to make for easier changes to old options and settings
	
	$csds_userRegAide_dbVersion = '1.1.0';
	update_option("csds_userRegAide_dbVersion", $csds_userRegAide_dbVersion);
	
	// Updates the Fields and Know Fields options array
	
	update_option("csds_userRegAideFields", $csds_userRegAide_knownFields);
	update_option("csds_userRegAide_knownFields", $csds_userRegAide_knownFields);
	
}

/**
 * Add fields to the new user registration page that the user must fill out when they register
 *
 * @since 1.0.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_addFields(){

	global $csds_userRegAideFields, $csds_userRegAide_registrationFields;
	$fieldKey = '';
	$fieldName = '';
	$cnt = 2;
	$csds_userRegAide_support = get_option('csds_userRegAide_support');
	$csds_display_link = get_option('csds_display_link');
	$csds_display_name = get_option('csds_display_name');
	$csds_userRegAide_registrationFields = get_option('csds_userRegAide_registrationFields');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	$csds_userRegAideFields = get_option('csds_userRegAideFields');
	
	if(!empty($csds_userRegAide_registrationFields)){
		foreach($csds_userRegAide_registrationFields as $fieldKey => $fieldName){
			
			echo '<p>';?>
			<label><?php _e($csds_userRegAide_registrationFields[$fieldKey], 'csds_userRegAide') ?><br />
			
			<input type="text" name="<?php echo $fieldKey; ?>" id="<?php echo $fieldKey; ?>" class="input" value="" size="25" tabindex="<?php $cnt ?>" style="font-size: 20px; width: 97%;	padding: 3px; margin-right: 6px;" /></label>
		<?php // above value="" => <?php echo esc_attr(stripslashes($_POST[$fieldKey])); 
			//echo '<input type="text" name="'.$fieldKey.'" id="'.$fieldKey.'" class="input" value="'.esc_attr(stripslashes($_POST[$fieldKey])).'" size="25" tabindex="10" style="font-size: 20px; width: 97%;	padding: 3px; margin-right: 6px;" /></label>';
			echo '</p>';
		$cnt++;
		}
		wp_nonce_field("userRegAideRegForm", "userRegAideNonce");
		if($csds_userRegAide_support == "1"){
				echo '<a target="_blank" href="'.$csds_display_link.'">' . $csds_display_name . '</a><br/>';
				echo '<br/>';
		}
	}	
}

/**
 * Add the additional metadata into the database after the new user is created
 *
 * @since 1.0.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_updateFields($user_id){

	global $csds_addField, $csds_userRegAide_registrationFields, $wpdb, $table_prefix;
	
	$thisValue = '';
	$name = '';
	$newValue = '';
	$csds_userRegAide_registrationFields = get_option('csds_userRegAide_registrationFields');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	
	if (wp_verify_nonce($_POST["userRegAideNonce"], "userRegAideRegForm")){
		foreach($csds_userRegAide_registrationFields as $thisValue => $name){
			if($_POST[$thisValue] != ''){
				if($thisValue == "first_name"){
					$newValue = apply_filters('pre_user_first_name', $_POST[$thisValue]);
				}elseif($thisValue == "last_name"){
					$newValue = apply_filters('pre_user_last_name', $_POST[$thisValue]);
				}elseif($thisValue == "nickname"){
					$newValue = apply_filters('pre_user_nickname', $_POST[$thisValue]);
				}elseif($thisValue == "user_url"){
					$newValue = apply_filters('pre_user_url', $_POST[$thisValue]);
				}elseif($thisValue == "description"){
					$newValue = apply_filters('pre_user_description', $_POST[$thisValue]);
				}else{
					$newValue = apply_filters('pre_user_description', $_POST[$thisValue]);
				}
			update_user_meta( $user_id, $thisValue, $newValue);
			}else{
				// Havent figured out what to do here yet as nothing really need to be done
			}
		}
	}else{
		exit('Failed Security Verification');
	}
}

/**
 * Check the new user registration form for errors
 *
 * @since 1.0.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_checkFields($user_login, $user_email, $errors){

	global $csds_userRegAide_knownFields, $csds_userRegAide_registrationFields;
	$thisValue = '';
	$fieldName1 = '';
	$csds_userRegAide_registrationFields = get_option('csds_userRegAide_registrationFields');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');

	foreach($csds_userRegAide_registrationFields as $thisValue => $fieldName1){
		if ($_POST[$thisValue] == '') {
			$errors->add('empty_'.$thisValue , __("<strong>ERROR</strong>: Please type your ".$csds_userRegAide_registrationFields[$thisValue], 'csds_userRegAide'));
		}
	}
}

/**
 * Add the additional fields added to the user profile page
 *
 * @since 1.0.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
	
function csds_show_user_profile($user)
{
 
 global $csds_userRegAide_knownFields, $csds_userRegAide_registrationFields, $csds_userRegAide_NewFields;
	
	$user_id = $user->ID;
	$fieldKey = '';
	$fieldName = '';
	$csds_userRegAide_registrationFields = get_option('csds_userRegAide_registrationFields');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
	$csds_userRegAide_support = get_option('csds_userRegAide_support');
	$csds_display_link = get_option('csds_display_link');
	$csds_display_name = get_option('csds_display_name');
	
		echo '<h3>User Registration Aide Additional Fields</h3>';
		if($csds_userRegAide_NewFields != ''){
			foreach($csds_userRegAide_NewFields as $fieldKey => $fieldName){
			
				?>
				<table class="form-table">
				<tr>
				<th><label for="<?php echo $fieldKey ?>"><?php echo $fieldName ?></label></th>
				<td><input type="text" name="<?php $fieldKey ?>" id="<?php echo $fieldKey ?>" value="<?php echo esc_attr(get_user_meta($user_id, $fieldKey, TRUE)) ?>" class="regular-text" /></td>
				</tr>
				<?php
				
			}
				echo '</table>';
				
		wp_nonce_field("userRegAideProfileForm", "userRegAideProfileNonce");
				
		echo '<br/>';				
			if($csds_userRegAide_support == "1"){
				echo '<a target="_blank" href="'.$csds_display_link.'">' . $csds_display_name . '</a>';
				echo '<br/>';
			}
		}
			
}
 
 /**
 * Updates the additional fields data added to the user profile page
 * @since 1.0.0 
 * @updated  1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
 
 function csds_update_user_profile($user_id)
 {
	global $wpdb;
	global $csds_userRegAide_knownFields, $csds_userRegAide_registrationFields, $csds_userRegAide_NewFields;
	$userID = $user_id;
	$fieldKey = '';
	$fieldName = '';
	$csds_current_user = wp_get_current_user();
	$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
	if($csds_current_user->has_cap('edit_user')){
		if(wp_verify_nonce($_POST["userRegAideProfileNonce"], "userRegAideProfileForm")){
			foreach($csds_userRegAide_NewFields as $fieldKey => $fieldName){
				$newValue = $_POST[$fieldKey];
				if($newValue != ''){
				update_user_meta($userID, $fieldKey, $newValue);
				}
				else{
				
				}
			}
		}else{
			wp_die(__('Failed Security Check'));
		}
	}else{
		wp_die(__('You do not have sufficient permissions to edit this user, contact a network administrator if this is an error!'));
	}
}

/**
 * Adds all the additional fields created to existing users meta
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
	
function csds_add_field_to_users_meta($field){

	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$count = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users;");
	$i = 1;
		while($i <= $count)
			{
				$user_id = $i;
				update_user_meta( $user_id, $field, "");
				$i++;
			}
}

/**
 * Deletes the additional fields from existing users meta
 *
 * @since 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
	
function csds_delete_field_from_users_meta($field){

	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$count = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users;");
	$i = 1;
		while($i <= $count)
			{
				$user_id = $i;
				delete_user_meta( $user_id, $field, "");
				$i++;
			}
}



/**
 * Deactivation Function
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_deactivation()
{
	
}

/**
 * Uninstall Function - Deletes options from the wp-options table
 *
 * @since 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_Uninstall() {
	if(!defined('WP_UNINSTALL_PLUGIN')){
		exit('Something Bad Happened');
	}else{
		delete_option($csds_userRegAide);
		delete_option($csds_userRegAideFields);
		delete_option($csds_userRegAide_knownFields);
		delete_option($csds_userRegAide_NewFields);
		delete_option($csds_userRegAide_fieldOrder);
		delete_option($csds_userRegAide_registrationFields);
		delete_option($csds_userRegAide_newField);
		delete_option($csds_userRegAide_dbVersion);
	}
}
	
?>
