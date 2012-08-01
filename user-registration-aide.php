<?php
/*
Plugin Name: User Registration Aide
Plugin URI: http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/
Plugin Description: Forces new users to register additional fields with the option to add additional fields other than those 
supplied with the default Wordpress Installation. We have kept it simple in this version for those of you whom aren't familiar with 
handling multiple users or websites. We also are currently working on expanding this project with a paid version which will contain 
alot more features and options for those of you who wish to get more control over users and user access to your site.
Version: 1.0.0
Author: Brian Novotny
Author URI: http://creative-software-design-solutions.com/

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

/**
 * Actions and Hooks to register
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

register_activation_hook(__FILE__, 'csds_userRegAide_install');
add_action('register_form', 'csds_userRegAide_addFields');
add_action('register_post', 'csds_userRegAide_checkFields', 10, 3);
add_action('user_register', 'csds_userRegAide_updateFields', 10, 1);
add_action('admin_menu', 'csds_userRegAide_optionsPage');
add_action('show_user_profile', 'csds_show_user_profile');
add_action('edit_user_profile', 'csds_show_user_profile');
add_action('personal_options_update', 'csds_update_user_profile');
add_action('edit_user_profile_update', 'csds_update_user_profile');
register_deactivation_hook(__FILE__, 'csds_userRegAide_deactivation');

$csds_userRegAide_getOptions = get_option("csds_userRegAide");
$csds_userRegAide_NewFields_getOptions = get_option("csds_userRegAide_NewFields");


/**
 * Add the management page to the user settings bar
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_optionsPage(){
	if(function_exists('add_management_page')){
	
	add_users_page('User Registration Aide','User Registration Aide', 'manage_options','user-registration-aide', 'csds_userRegAide_myOptionsSubpanel' );
    
	}
}

/**
 * Installs plugin
 *
 * @since 1.0.0
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
	"jabber"		=> "Jabber / Google Talk"
	);
	
	update_option("csds_userRegAideFields", $csds_userRegAide_knownFields);
	update_option("csds_userRegAide_knownFields", $csds_userRegAide_knownFields);
	
}

/**
 * Add fields to the new user registration page that the user must fill out when they register
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_addFields(){

	global $csds_userRegAide_knownFields, $csds_userRegAide_getOptions;
	$csds_userRegAide_support = get_option('csds_userRegAide_support');
	$csds_display_link = get_option('csds_display_link');
	$csds_display_name = get_option('csds_display_name');
	$csds_userRegAide_getOptions = get_option('csds_userRegAide');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	if($csds_userRegAide_getOptions["additionalFields"]){
		foreach($csds_userRegAide_getOptions["additionalFields"] as $thisValue){
			
			echo '<p>';?>
			<label><?php _e($csds_userRegAide_knownFields[$thisValue], 'csds_userRegAide') ?><br />
			<?php
			echo '<input type="text" name="'.$thisValue.'" id="'.$thisValue.'" class="input" value="'.esc_attr(stripslashes($_POST[$thisValue])).'" size="25" tabindex="10" style="font-size: 20px; width: 97%;	padding: 3px; margin-right: 6px;" /></label>';
			echo '</p>';
			
		}
		
		if($csds_userRegAide_support == "1"){
				echo '<a target="_blank" href="'.$csds_display_link.'">' . $csds_display_name . '</a><br/>';
				echo '<br/>';
		}
	}	
}

/**
 * Add the additional metadata into the database after the user is created
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_updateFields($user_id){

	global $csds_addField, $csds_userRegAide_getOptions;
	
	$csds_userRegAide_getOptions = get_option('csds_userRegAide');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	
	foreach($csds_userRegAide_getOptions["additionalFields"] as $thisValue){
		if($_POST[$thisValue] != ''){
			update_user_meta( $user_id, $thisValue, esc_attr(stripslashes($_POST[$thisValue])));
		}
		else{
		
		}
	}
}

/**
 * Check the new user registration form for errors
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_checkFields($user_login, $user_email, $errors){

	global $csds_userRegAide_knownFields, $csds_userRegAide_getOptions;
	
	$csds_userRegAide_getOptions = get_option('csds_userRegAide');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');

	foreach($csds_userRegAide_getOptions["additionalFields"] as $thisValue){
		if ($_POST[$thisValue] == '') {
			$errors->add('empty_'.$thisValue , __("<strong>ERROR</strong>: Please type your ".$csds_userRegAide_knownFields[$thisValue].".",'csds_userRegAide'));
		}
	}
}

/**
 * Add the additional fields added to the user profile page
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
	
function csds_show_user_profile($user)
{
 
 global $csds_userRegAide_knownFields, $csds_userRegAide_getOptions, $csds_userRegAide_NewFields;
	
	$user_id = $user->ID;
	$csds_userRegAide_getOptions = get_option('csds_userRegAide');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
	$csds_userRegAide_support = get_option('csds_userRegAide_support');
	$csds_display_link = get_option('csds_display_link');
	$csds_display_name = get_option('csds_display_name');
	
		echo '<h3>User Registration Aide Additional Fields</h3>';
			foreach($csds_userRegAide_NewFields as $fieldKey => $fieldName){
			
				?>
				<table class="form-table">
				<tr>
				<th><label for="<?php echo $fieldKey ?>"><?php echo $fieldName ?></label></th>
				<td><input type="text" name="<?php echo $fieldKey ?>" id="<?php echo $fieldKey ?>" value="<?php echo esc_attr(get_user_meta($user_id, $fieldKey, TRUE)) ?>" class="regular-text" /></td>
				</tr>
				<?php
				}
				echo '</table>';
				
				echo '<br/>';				
				if($csds_userRegAide_support == "1"){
				echo '<a target="_blank" href="'.$csds_display_link.'">' . $csds_display_name . '</a>';
				echo '<br/>';
				
				}
						
}
 
 /**
 * Updates the additional fields data added to the user profile page
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
 
 function csds_update_user_profile($user_id)
 {
 
	$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
	
		foreach($csds_userRegAide_NewFields as $fieldKey => $fieldName){
		
			update_user_meta($user_id, $fieldKey, esc_attr(stripslashes($_POST[$thisValue])));
			
		}
}

/**
 * Adds all the additional fields created to existing users
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
	
function csds_add_field_to_users_meta($field){

	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users;");
	$i = 1;
		while($i <= $count)
			{
				$user_id = $i;
				update_user_meta( $user_id, $field, "");
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
	
?>
