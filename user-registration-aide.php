<?php
/**
Plugin Name: User Registration Aide
Plugin URI: http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/
Description: Forces new users to register additional fields with the option to add additional fields other than those supplied with the default Wordpress Installation. We have kept it simple in this version for those of you whom aren't familiar with handling multiple users or websites. We also are currently working on expanding this project with a paid version which will contain alot more features and options for those of you who wish to get more control over users and user access to your site.

Version: 1.3.7.3
Author: Brian Novotny
Author URI: http://creative-software-design-solutions.com/
Text Domain: csds_userRegAide

User Registration Aide Requires & Adds More Fields to User Registration & Profile - Forces new users to register additional fields on registration form, and gives you the option to add additional fields at your discretion. Gives you more control over who registers, and allows you to manage users easier!

Copyright ©  2012 - 2014 Brian Novotny

Users Registration Helper - Forces new users to register additional fields

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

define('URA_PLUGIN_PATH', WP_PLUGIN_DIR.'/user-registration-aide/');
define('JS_PATH', plugin_dir_url(__FILE__).'js/');
define('CSS_PATH', plugin_dir_url(__FILE__).'css/');
define('IMAGES_PATH', plugin_dir_url(__FILE__).'images/');
define('SCREENSHOTS_PATH', plugin_dir_url(__FILE__).'screenshots/');
define('CLASSES_PATH', WP_PLUGIN_DIR.'/user-registration-aide/classes/');

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once (CLASSES_PATH."user-reg-aide-admin.php");  // Handles main options page
require_once (CLASSES_PATH."user-reg-aide-newFields.php"); // Handles new fields options page
require_once (CLASSES_PATH."user-reg-aide-options.php"); // Behind the scenes options functions & default options
require_once (CLASSES_PATH."user-reg-aide-regForm.php"); // Handles registration form main options page
require_once (CLASSES_PATH."user-reg-aide-regFormCSSMessages.php"); // Handles registration form css and messages options ** New 1.3.0
//require_once (CLASSES_PATH."user-reg-aide-multisite.php"); // Handles multisite functions ** New 1.3.0 Skipping multisite for now until next update due to lack of documentation and support and issues
//require_once (CLASSES_PATH."user-reg-aide-mailer.php"); // Handles mailing functions ** New 1.3.0
require_once (CLASSES_PATH."user-reg-aide-customCSS.php"); // Handles custom css for front end registration forms ** New 1.3.0
require_once (CLASSES_PATH."user-reg-aide-userProfile.php"); // Handles user profile extra fields functions ** New 1.3.0
require_once (CLASSES_PATH."user-reg-aide-registrationForm.php"); // Handles registration form fields functions, adding, checking and updating new user registrations ** New 1.3.0
require_once (CLASSES_PATH."user-reg-aide-dashWidget.php"); // Handles the dashboard widget ** New 1.3.0
require_once (CLASSES_PATH."user-reg-aide-actions.php"); // Handles recurring custom actions to eliminate redundancy

//For Debugging and Testing Purposes ------------


// ----------------------------------------------

/**
 * Creates Class CSDS_USER_REG_AIDE & Adds Actions and Hooks to register
 *
 * @category Class CSDS_USER_REG_AIDE
 * @since 1.2.0
 * @updated 1.3.6
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

class CSDS_USER_REG_AIDE
{

	public static $instance;
	protected $retrieve_password_for   = '';
	public    $during_user_creation    = false; // hack
	
	/**
	* Constructor
	*/
		
	public function __construct() {
		$this->CSDS_USER_REG_AIDE();
	}
		
	function CSDS_USER_REG_AIDE() { //constructor
	
		global $wp_version, $pagenow;
		self::$instance = $this;
		$this->plugin_dir = dirname(__FILE__);
		$this->plugin_url = trailingslashit(get_option('siteurl')) . 'wp-content/plugins/' . basename(dirname(__FILE__)) .'/';
		$this->ref = explode('?',$_SERVER['REQUEST_URI']);
		$this->ref = $this->ref[0];
		//$multi_site = new CSDS_URA_MULTISITE(); Skipping multisite for now until next update due to lack of documentation and support and issues
		//$mailer = new CSDS_USER_REG_AIDE_MAILER(); Skipping multisite for now until next update due to lack of documentation and support and issues mailer only for multisite//
		$ura_options = new URA_OPTIONS();
		$admin = new CSDS_URA_ADMIN_SETTINGS();
		$customCSS = new URA_CUSTOM_CSS();
		$userProfile = new URA_USER_PROFILE();
		$regForm = new URA_REGISTRATION_FORM();
		$dashWidget = new URA_DASHBOARD_WIDGET();
		$actions = new CSDS_URA_ACTIONS();
		
		$settings_file = 'user-reg-aide-admin.php';
		$options = get_option('csds_userRegAide_Options');
		
		if(empty($options['updated'])){
			$ura_options->csds_userRegAide_updateOptions();
		}elseif($options['updated'] == 2){
			$ura_options->csds_userRegAide_updateOptions();
		}
		// defines
		
		if ( ! defined( 'WP_CONTENT_URL' ) )
			define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
		if ( ! defined( 'WP_CONTENT_DIR' ) )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ( ! defined( 'WP_PLUGIN_URL' ) )
			define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
		if ( ! defined( 'WP_PLUGIN_DIR' ) )
			define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
		if ( ! defined( 'WPMU_PLUGIN_URL' ) )
			define( 'WPMU_PLUGIN_URL', WP_CONTENT_URL. '/mu-plugins' );
		if ( ! defined( 'WPMU_PLUGIN_DIR' ) )
			define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' );
		
		// Gets plugin directory
		$plugins_dir = dirname(__FILE__);
		
		// Actions and Filters
		
		// Adds widget to wp admin page dashboard
		add_action('wp_dashboard_setup', array(&$dashWidget, 'ura_dashboard_widget_action'));  // Line 59 user-reg-aide-dashWidget.php
		
		// Checks to make sure options are up to date
		add_action( 'admin_init',  array(&$ura_options, 'check_options_table' )); // Line 
		
		// Filling existing WordPress profile fields into options db			
		if( isset($_GET['page']) && $_GET['page'] == 'user-reg-aide-admin' ){
			add_action( 'init',  array(&$ura_options, 'csds_userRegAide_fill_known_fields' )); // Line 229 user-reg-aide-options.php
		}
		
		// styling enqueues and registering for css styling on plugin admin pages
		if( isset($_GET['page']) && $_GET['page'] == 'user-registration-aide' ){
			add_action('admin_print_styles', array(&$this, 'add_admin_settings_css')); // Line 748 &$this
		}
		
		if( isset($_GET['page']) && $_GET['page'] == 'edit-new-fields' ){
			add_action('admin_print_styles', array(&$this, 'add_admin_settings_css')); // Line 748 &$this
		}
		
		if( isset($_GET['page']) && $_GET['page'] == 'registration-form-options' ){
			add_action('admin_print_styles', array(&$this, 'add_admin_settings_css')); // Line 748 &$this
		}
		
		if( isset($_GET['page']) && $_GET['page'] == 'registration-form-css-options' ){
			add_action('admin_print_styles', array(&$this, 'add_admin_settings_css')); // Line 748 &$this
		}
		
		// Filling default User Registration Aide options db
		if(isset($_GET['action']) && $_GET['action'] == 'admin_init'){
			if(!empty($options)){
				add_action( 'init', array(&$ura_options, 'csds_userRegAide_DefaultOptions' )); // Line 59 user-reg-aide-options.php
			}
		}
		
		// Customize Registration & Login Forms
		if(!is_multisite()){
			add_action( 'login_head', array(&$customCSS, 'csds_userRegAide_Password_Header')); // Line 988 &$this
		}else{
			//add_action( 'signup_header', array(&$multi_site, 'multisite_Password_Header')); // Line 1187 user-reg-aide-multisite.php
		}
		add_filter('login_headerurl', array(&$customCSS, 'csds_userRegAide_CustomLoginLink')); // Line 151 &$customCSS
		add_filter('login_headertitle', array(&$customCSS, 'csds_userRegAide_Logo_Title_Color')); // Line 56 &$customCSS
		add_action( 'login_head', array(&$customCSS, 'csds_userRegAide_Logo_Head')); // Line 100 &$customCSS
		
		// Adding custom stylesheets
		add_action('wp_enqueue_scripts', array(&$this, 'csds_userRegAide_stylesheet')); // Line 779 &$this
		add_action('admin_init', array(&$this, 'csds_userRegAide_stylesheet')); // Line 779 &$this
								
		// Changes the Login & Register form messages for this site at top of forms
		add_filter('login_message', array(&$this, 'ura_login_message')); // Line 502 &$this
		add_filter('login_messages', array(&$this, 'my_login_messages')); // Line 578 &$this
		
		
		
		if(isset($_GET['action']) && $_GET['action'] == 'register'){
			add_action('login_enqueue_scripts', array(&$this, 'add_lostpassword_css')); // Line 762 &$this
		}

		if(isset($_GET['action']) && $_GET['action'] == 'rp'){
			add_action('login_enqueue_scripts', array(&$this, 'add_lostpassword_css')); // Line 762 &$this
		}						
		
		//Multisite Specific Actions & Filters
		// Skipping multisite for now until next update due to lack of documentation and support and issues
		// if(is_multisite()){ 
			// //add_action( 'signup_header', array(&$multi_site, 'multisite_Password_Header')); // Line 1187 user-reg-aide-multisite.php csds_userRegAide_Password_Header
			
			// add_action('signup_header', array(&$multi_site, 'ms_actions')); // Line 40 user-reg-aide-multisite.php
			// add_filter('login_headerurl', array(&$customCSS, 'csds_userRegAide_CustomLoginLink')); // Line 151 &$customCSS
			// add_filter('login_headertitle', array(&$customCSS, 'csds_userRegAide_Logo_Title_Color')); // Line 56 &$customCSS
			// add_action( 'signup_header', array(&$customCSS, 'csds_userRegAide_Logo_Head')); // Line 100 &$customCSS
		// }
		
		// Handles new fields for registration form
		if(isset($_GET['action']) && $_GET['action'] == 'register'){
			add_action('register_form', array(&$regForm, 'csds_userRegAide_addFields')); // Line 57 &$regForm
			add_action('user_register', array(&$regForm, 'csds_userRegAide_updateFields'), 1, 1); // Line 283 &$regForm (Params: int $user_id)
			
			add_filter('registration_errors', array(&$regForm, 'csds_userRegAide_checkFields'), 1, 3 ); // Line 219 &$regForm (Params: array $errors, str $username, str $email)
			add_filter('registration_redirect', array(&$this, 'ura_registration_redirect'), 1, 1); // Line 417 &$this (Params: string $redirect_to)
		}
					
		// Filter to modify redirect of successful user login
		add_filter('login_redirect', array(&$this, 'ura_login_redirect'), 1, 1); // Line 449 &$this (Params: string $redirect_to)
		

		// Adds settings page to wordpress plugins page & css
		add_filter('plugin_action_links', array(&$this, 'add_plugins_settings_link'), 10, 2); // Line 621 &$this (Params: array $links, str $file)
		add_action('admin_print_styles', array(&$this, 'add_settings_css')); // Line 734 &$this
		
		
		// Adds stylesheet to admin dashboard widget
		add_action('admin_print_styles', array(&$dashWidget, 'csds_dashboard_widget_style')); // Line 655 &$dashWidget
					
		// Sets new password if user can enter own password on registration
		add_filter('random_password',  array(&$this, 'csds_userRegAide_createNewPassword'), 0, 1); // Line 874 &$this (Params: str $password)
		
		// Administration Pages
		
		if(!is_multisite()){
			// Single Site Administration Menus
			add_action('admin_menu', array(&$this, 'csds_userRegAide_optionsPage')); // Line 646 &$this
			add_action('admin_menu', array(&$this, 'csds_userRegAide_editNewFields_optionsPage')); // Line 712 &$this
			add_action('admin_menu', array(&$this, 'csds_userRegAide_regFormOptionsPage')); // Line 669 &$this
			add_action('admin_menu', array(&$this, 'csds_userRegAide_regFormCSSOptionsPage')); // Line 690 &$this
		}else{
			// Multisite Admin Menus incase needs changed to mu plugin (must use) but not using in this instance
			//add_action('admin_menu', array(&$this, 'csds_userRegAide_optionsPage')); // Line 646 &$this
			//add_action('admin_menu', array(&$this, 'csds_userRegAide_editNewFields_optionsPage')); // Line 712 &$this
			//add_action('admin_menu', array(&$this, 'csds_userRegAide_regFormOptionsPage')); // Line 669 &$this
			//add_action('admin_menu', array(&$this, 'csds_userRegAide_regFormCSSOptionsPage')); // Line 690 &$this
		}
		
				// custom actions for this plugin
		add_action('create_tabs', array(&$actions, 'options_tabs_page'), 10, 1); // Line 105 &$actions Accepts $current_page - (current options menu page)
		add_action('show_support', array(&$actions, 'show_support_section')); // Line 141 user-reg-aide-actions.php
		add_action('update_field_order', array($ura_options, 'csds_userRegAide_update_field_order')); // Line 331 &$ura_options
		add_action('delete_usermeta_field', array(&$this, 'csds_delete_field_from_users_meta'), 10, 1); // Line 1206 &$this
		add_action('update_options', array($ura_options, 'csds_userRegAide_updateOptions')); // Line 402 &$ura_options
		add_action('display_dw_options', array($dashWidget, 'dashboard_widget_options')); // Displays dashboard widget options on URA admin page, $admin line	364	
		add_action('update_dw_display_options', array($dashWidget, 'update_dashboard_widget_options')); // Update dashboard widget options from admin page, $admin line 479
		add_action('update_dw_field_options', array($dashWidget, 'update_dashboard_widget_fields')); // Update dashboard widget options from admin page, $admin line 497
		add_action('update_dw_field_order', array($dashWidget, 'update_dashboard_widget_field_order')); // Update dashboard widget options from admin page, $admin line 600
		
				// Handles user profiles and extra fields
		add_action('show_user_profile', array(&$userProfile, 'csds_show_user_profile'), 0, 1); // Line 59 &$userProfile (Params: str $user)
		add_action('edit_user_profile', array(&$userProfile, 'csds_show_user_profile'), 0, 1); // Line 59 &$userProfile (Params: str $user)
		add_action('personal_options_update', array(&$userProfile, 'csds_update_user_profile'), 0, 1); // Line 138 &$userProfile (Params: int $user_id)
		add_action('edit_user_profile_update', array(&$userProfile, 'csds_update_user_profile'), 0, 1); // Line 138 &$userProfile (Params: int $user_id)
		add_action('profile_update', array(&$userProfile, 'csds_update_user_profile'), 0, 1); // Line 138 &$userProfile (Params: int $user_id)
		add_action('load_extra_fields', array(&$this, 'create_new_user_extra_fields'), 0, 0); // Line 970 &$this My custom action for user-new.php
		add_action('update_new_user_fields', array(&$this, 'create_new_user_extra_fields_updates'), 0, 1); // Line 1072 &$this My custom action for user-new.php
		
		// Add new column to the user list
		if(!is_multisite()){
			add_filter( 'manage_users_columns', array(&$this, 'csds_userRegAide_addUserFields')) ; // Line 814 &$this 
			add_filter( 'manage_users_custom_column', array(&$this, 'csds_userRegAide_fillUserFields'), 0, 3); // Line 850 &$this (Params: str $value, str $column_name, int $user_id)
		}else{
			//add_filter( 'wpmu_users_columns', array(&$this, 'csds_userRegAide_addUserFields')); // Line 814 &$this 
			//add_filter( 'manage_users_columns', array(&$this, 'csds_userRegAide_addUserFields')) ; // Line 814 &$this 
			//add_filter( 'manage_users_custom_column', array(&$this, 'csds_userRegAide_fillUserFields'), 0, 3); // Line 850 &$this (Params: str $value, str $column_name, int $user_id)
		}
		
		// Translation File - Still in Works
		add_action('init', array(&$this, 'csds_userRegAide_translationFile')); // Line 385 &$this
		
		// Filters for user input into extra and new fields
		add_filter('pre_user_first_name', 'esc_html'); // Wordpress Filters
		add_filter('pre_user_first_name', 'strip_tags'); // Wordpress Filters
		add_filter('pre_user_first_name', 'trim'); // Wordpress Filters
		add_filter('pre_user_first_name', 'wp_filter_kses'); // Wordpress Filters
		add_filter('pre_user_last_name', 'esc_html'); // Wordpress Filters
		add_filter('pre_user_last_name', 'strip_tags'); // Wordpress Filters
		add_filter('pre_user_last_name', 'trim'); // Wordpress Filters
		add_filter('pre_user_last_name', 'wp_filter_kses'); // Wordpress Filters
		add_filter('pre_user_nickname', 'esc_html'); // Wordpress Filters
		add_filter('pre_user_nickname', 'strip_tags'); // Wordpress Filters
		add_filter('pre_user_nickname', 'trim'); // Wordpress Filters
		add_filter('pre_user_nickname', 'wp_filter_kses'); // Wordpress Filters
		add_filter('pre_user_url', 'esc_url'); // Wordpress Filters
		add_filter('pre_user_url', 'strip_tags'); // Wordpress Filters
		add_filter('pre_user_url', 'trim'); // Wordpress Filters
		add_filter('pre_user_url', 'wp_filter_kses'); // Wordpress Filters
		add_filter('pre_user_description', 'esc_html'); // Wordpress Filters
		add_filter('pre_user_description', 'strip_tags'); // Wordpress Filters
		add_filter('pre_user_description', 'trim'); // Wordpress Filters
		add_filter('pre_user_description', 'wp_filter_kses'); // Wordpress Filters
		
		// Deactivation hook for deactivation of User Registration Aide Plugin
		register_deactivation_hook(__FILE__, array(&$this, 'csds_userRegAide_deactivation')); // Line 1427 &$this
		
		// Multisite Actions & Filters ---------------------------------------------------------------------------
		// if(is_multisite()){ Skipping multisite for now until next update due to lack of documentation and support and issues
		
			// // add engine for hidden extra fields to 2nd stage user's registration
			// add_action('signup_hidden_fields', array(&$multi_site, 'ms_wpmu_signup_stage_2'), 1, 0); // Line 73 user-reg-aide-multisite.php

			// // add filter to modify signup URL for WordPress MU where plug-in is installed per blog
			// add_filter('wp_signup_location', array(&$multi_site, 'confirm_mu_signup_location')); // Line 508 user-reg-aide-multisite.php

			// // // add extra fields to registration form
			// add_action('signup_extra_fields', array(&$multi_site, 'addFields_MultisiteErrors'), 1, 1); // Line 983 user-reg-aide-multisite.php (Params: array $errors)

			// // // add checks for extra fields in the registration form
			// //add_action('validate_user_signup', array(&$multi_site, 'ms_validate_user_signup'), 0, 0); // !!!!!!!!!!!!!!!!! NOT USED !!!!!!!!!!!!!!!!!!!!
			// add_filter('wpmu_validate_user_signup', array(&$multi_site, 'ms_filter_validate_user_signup'), 0, 1); // Line 119 user-reg-aide-multisite.php (Params: array $results)

			// // Adds custom messages to signup finished page 			
			// add_action('signup_finished', array(&$multi_site, 'ms_signup_finished')); // Line 233 user-reg-aide-multisite.php

			// // add extra fields to wp_signups waiting for confirmation
			// add_filter('add_signup_meta', array(&$multi_site, 'signup_meta_extra_fields'), 0, 1); // Line 532 user-reg-aide-multisite.php (Params: array $meta)
			// add_action('wpmu_signup_user', array(&$multi_site, 'ms_wpmu_signup_user'), 0, 3); // Line 267 user-reg-aide-multisite.php (Params: string $user, string $user_email,  array $meta)
						
			// // // add update engine for extra fields to user's registration (user only)
			// add_action('wpmu_activate_user', array(&$multi_site, 'activate_mu_user'), 0, 3);  // Line 610 user-reg-aide-multisite.php (Params: int $user_id, string $password, array $meta1)
			
			// // Adds my own actions for mutlisite signups since WordPress multisite actions don't seem to be working correctly
			// add_action('ms_activate_user', array(&$multi_site, 'activate_mu_user'), 0, 3);  // Line 610 user-reg-aide-multisite.php (Params: int $user_id, string $password, array $meta1)
			// add_action('ms_instant_activation', array(&$multi_site, 'multisite_activateSignup'), 0, 1);	// Line 893 user-reg-aide-multisite.php (Params: string $key)			
			
			// // filters for adding fields to user email notification
			// add_filter('wpmu_signup_user_notification_email', array(&$mailer, 'signup_user_notification_email'), 0, 4 ); // Line 279 user-reg-aide-mailer.php (Params: string $link, string $user, string $user_email, string $key
			
			// add_filter('wpmu_signup_user_notification_subject', array(&$mailer, 'ms_signup_user_notification_subject'), 0, 4); // Line 38 user-reg-aide-mailer.php (Params: string $user, string $user_email, string $key, array $meta) !!!!!!!!!!!!!!!!!!!!!!CHECK ON PARAMS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			
			// add_filter('update_welcome_user_subject', array(&$mailer, 'ms_welcome_user_notification_subject'), 0, 1); // Line 63 user-reg-aide-mailer.php (Params: string $subject)
			
			// add_filter('update_welcome_user_email', array(&$mailer, 'ms_welcome_user_notice'), 0, 4); // Line 86  user-reg-aide-mailer.php (Params: string $welcome_email, int $user_id, string $password, array $meta)
			
		// }
		
		// Checking options have been loaded
		add_action('init' , array(&$this, 'check_default_options' )); // Line 400 &$this
	}
		
	// ----------------------------------------     Installation - Setup Functions     ----------------------------------------
	
	/**
	 * Installs options and default fields to database for plugin
	 *
	 * @since 1.0.0
	 * @updated 1.2.0
	 * @handles register_activation_hook line 1305 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function csds_userRegAide_install(){
		$ura_options = new URA_OPTIONS();
		if(function_exists('csds_userRegAide_fill_known_fields')){
			$ura_options->csds_userRegAide_fill_known_fields(); // Line 229 user-reg-aide-options.php
		}
		
		if(function_exists('csds_userRegAide_DefaultOptions')){
			$ura_options->csds_userRegAide_DefaultOptions(); // Line 59 user-reg-aide-options.php
		}
		
	}
	
	/**
	 * Adds the translation directory to the plugin folder
	 * @since 1.1.0
	 * @handles 'init' line 279 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function csds_userRegAide_translationFile(){
		
		$plugin_path = plugin_basename(dirname( __FILE__) .'/languages');
		load_plugin_textdomain('csds_userRegAide', false, $plugin_path );
	}
	
	/**
	 * Checks and verifies options have been loaded
	 * @since 1.3.0
	 * @handles 'init' line 348 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function check_default_options(){
		$options = get_option('csds_userRegAide_Options');
		if(empty($options)){
			$ura_options = new URA_OPTIONS();
			$ura_options->csds_userRegAide_DefaultOptions();  // Line 59  &$ura_options
		}
	}
	
	// ----------------------------------------     Login-Registration Redirects Functions     ----------------------------------------
		
	/** Redirects after successful new user registration
	 *
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles filter 'registration_redirect' line 220 &$this
	 * @access private
	 * @accepts string $redirect_to (url)
	 * @returns $redirect_to New url to redirect to after successful registration if user chooses option or same url if option not chosen
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	 *
	*/
		
	function ura_registration_redirect($redirect_to){
		
		$options = get_option('csds_userRegAide_Options');
		if($options['redirect_registration'] == "1"){
			$redirect_to = $options['registration_redirect_url'];
		}else{
			$redirect_to = $redirect_to;
		}
		
		return $redirect_to;
		
	}
	
	/** Redirects after successful user login
	 *
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles filter 'registration_redirect' line 224 &$this 
	 * @access private
	 * @accepts string $redirect_to (url)
	 * @returns $redirect_to New url to redirect to after successful login if user chooses option
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	 *
	*/
	
	function ura_login_redirect($redirect_to){
		
		$options = get_option('csds_userRegAide_Options');
		if($options['redirect_login'] == "1"){
			$redirect_to = $options['login_redirect_url'];
		}else{
			$redirect_to = $redirect_to;
		}
		
		return $redirect_to;
		
	}
	
	// ----------------------------------------     Wordpress Message Filters Functions     ----------------------------------------
	
	/** Changes the default WordPress registration page message 
	 * Sets the text color and logo links & shadows in registration/login pages
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @Filters 'login_message' line 134 &$this
	 * @access private
	 * @returns $message (registration form top message)
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function ura_registration_top_message(){
		$options = get_option('csds_userRegAide_Options');
		$message = (string)'';
		$show_message = $options['show_login_message'];
		
		if( $show_message == "1"){
			if($options['add_security_question'] == "2"){
				$message = '<p class="message register">'.$options['reg_top_message'].'</p>';
			}else{
				$message = '<p class="message register" style="width: 425px;">'.$options['reg_top_message'].'</p>';
			}
		}
		
		return $message;
	}
		
	/** Changes the default WordPress login page messages
	 * Sets the <p class="message"> to predefined custom message
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @Filters 'login_message' line 171 &$this
	 * @access private
	 * @returns $message (wp-login.php form messages)
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function ura_login_message(){
		$options = get_option('csds_userRegAide_Options');
		$message = '';
		$page = site_url(). $_SERVER['REQUEST_URI'];
		$show_message = $options['show_login_message'];
		$errors = new WP_Error();
		if( $show_message == 1){
			if($page == wp_login_url()){
				$message = '<p class="message">'.$options['login_message'].'</p>';
			}
			
			if( isset($_GET['action']) && $_GET['action'] == 'register'){
				if($options['add_security_question'] == "2"){
					$message = '<p class="message register">'.$options['reg_top_message'].'</p>';
				}else{
					$message = '<p class="message register" style="width: 450px;">'.$options['reg_top_message'].'</p>';
				}
			}
			
			if( isset($_GET['action']) && $_GET['action'] == 'lostpassword'){
				$message = '<p class="message register">'.$options['login_messages_lost_password'].'</p>'; 
			}
			
			if(isset($_GET['action']) && $_GET['action'] == 'resetpass' ){ //after successful reset password
				if($options['add_security_question'] == "1"){
					$message = $options['reset_password_success_security']; 
				}else{
					$message = $options['reset_password_success_normal']; 
				}
			}
			
			if(isset($_GET['action']) && $_GET['action'] == 'rp'){  // Reset Password Page
				if($options['add_security_question'] == "1"){
					$message = $options['reset_password_messages_security']; 
				}else{
					$message = $options['reset_password_messages_normal']; 
				}
			}
		
			if(!empty($_GET['login'])){
				if(!empty($_GET['key'])){
					$action = $_GET['action'];
					if($action == 'rp'){
						if($options['add_security_question'] == "1"){
							$message = '<p class="message reset-pass">' . __($options['reset_password_messages_security'], 'csds_userRegAide').'</p>'; 
						}else{
							$message = '<p class="message reset-pass">' . __($options['reset_password_messages_normal'], 'csds_userRegAide').'</p>'; 
						}
					}elseif($action == 'resetpass'){
						if($options['add_security_question'] == "1"){
							$message = '<p class="message reset-pass">' . __($options['reset_password_success_security'], 'csds_userRegAide').'</p>';
						}else{
							$message = '<p class="message reset-pass">' . __($options['reset_password_success_normal'], 'csds_userRegAide').'</p>'; 
						}
					}
					
				}
			}
		}
		return $message;
	}
				
	/** Sets the <p> class="messages" to custom message (2nd message on login form)
	 * 
	 *
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @Filters 'login_messages' line 172 &$this 
	 *
	 * @param string $messages
	 * @returns string $messages (customized messages)
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function my_login_messages($messages){
		$options = get_option('csds_userRegAide_Options');
		if($options['show_login_message'] == "1"){
		
			if( isset($_GET['loggedout']) && true == $_GET['loggedout'] ){
				$messages = __($options['login_messages_logged_out'],'csds_userRegAide');
			}elseif( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] ){
				$messages = __($options['login_messages_registered'],'csds_userRegAide');
			}elseif( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] ){
				$messages = __($options['reset_password_confirm'],'csds_userRegAide');
			}elseif(isset($_GET['action']) && $_GET['action'] == 'resetpass'){
				if($options['add_security_question'] == "1"){
					$messages = __($options['reset_password_success_security'],'csds_userRegAide'); 
				}else{
					$messages = __($options['reset_password_success_normal'],'csds_userRegAide'); 
				}
			}elseif(isset($_GET['action']) && $_GET['action'] == 'rp'){
				if($options['add_security_question'] == "1"){
					$messages = __($options['reset_password_messages_security'],'csds_userRegAide'); 
				}else{
					$messages = __($options['reset_password_messages_normal'],'csds_userRegAide'); 
				}
			}
		}
		return $messages;
	}
	
	// ----------------------------------------     Admin Menu Pages & Links Functions     ----------------------------------------
		
	/** Adds a setting page option to the plugins page
	 * 
	 *
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @Filters 'plugin_action_links' line 228 &$this (Priority: 10 - Params: 2)
	 *
	 * @param array $links Array of links for admin plugins page links for all plugins
	 * @param $file This plugin filename
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
		
	function add_plugins_settings_link($links, $file){
		$admin = new CSDS_URA_ADMIN_SETTINGS();
		$this_file = plugin_basename(__FILE__);
		if($file == $this_file){
			$plugin_settings = '<ul class="settings_menu"><li><a href="#">Settings</a>';
			$plugin_settings .= '<ul><li><a href="admin.php?page=user-registration-aide">'.__('Registration Fields', 'csds_userRegAide').'</a></li>';
			$plugin_settings .= '<li><a href="admin.php?page=edit-new-fields">'.__('Edit New Fields', 'csds_userRegAide').'</a></li>';
			$plugin_settings .= '<li><a href="admin.php?page=registration-form-css-options">'.__('Registration Form Messages & CSS Options', 'csds_userRegAide').'</a></li>';
			$plugin_settings .= '<li><a href="admin.php?page=registration-form-options">'.__('Registration Form Options', 'csds_userRegAide').'</a></li></ul></li></ul>';
			array_unshift($links, $plugin_settings);
		}
		return $links;
	}
	
	/**
	 * Add the management page to the admin side panel
	 *
	 * @since 1.0.0
	 * @updated 1.3.0
	 * @handles action 'admin_menu' line 242 &$this and line 248 'admin_menu' for multisite (maybe not need for multisite?)
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function csds_userRegAide_optionsPage(){
		
		$admin_settings = new CSDS_URA_ADMIN_SETTINGS();
		if(function_exists('add_menu_page')){
			//if(!is_multisite()){
			$page = add_menu_page(__('User Registration Aide', 'csds_userRegAide'), __('User Registration Aide', 'csds_userRegAide'), 'manage_options','user-registration-aide', array(&$admin_settings, 'csds_userRegAide_myOptionsSubpanel'), '', 71 ) ; // line 71 &$admin_settings
		}
		
		add_action('admin_print_styles-'.$page, array(&$this, 'csds_userRegAide_enqueueMyStyles'));
			
	}

	/**
	 * Add options page for the Registration Form options
	 *
	 * @since 1.2.0
	 * @updated 1.3.0
	 * @handles action 'admin_menu' line 244 &$this and handles action 'admin_menu' for multisite line 250 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function csds_userRegAide_regFormOptionsPage(){
		$rfo = new URA_REG_FORM_OPTIONS();
		if(function_exists('add_submenu_page')){
			$page = add_submenu_page('user-registration-aide', __('Registration Form Options', 'csds_userRegAide'), __('Registration Form Options', 'csds_userRegAide'), 'manage_options', 'registration-form-options', array(&$rfo, 'csds_userRegAide_regFormOptions' )); // line 69 &$rfo
		}
		
		add_action('admin_print_styles-'.$page, array(&$this, 'csds_userRegAide_enqueueMyStyles'));
			
	}
	
	/**
	 * Add options page for the Registration Form CSS Options
	 *
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles action 'admin_menu' line 245 &$this and handles action 'admin_menu' for multisite line 251 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function csds_userRegAide_regFormCSSOptionsPage(){
		
		$rfco = new URA_REG_FORM_CSS_OPTIONS();
		if(function_exists('add_submenu_page')){
			$page = add_submenu_page('user-registration-aide', __('Registration Form Messages & CSS Options', 'csds_userRegAide'), __('Registration Form Messages & CSS Options', 'csds_userRegAide'), 'manage_options', 'registration-form-css-options', array(&$rfco, 'csds_userRegAide_regFormCSSOptions' )); // line 67 &$rfco
		}
		
		add_action('admin_print_styles-'.$page, array(&$this, 'csds_userRegAide_enqueueMyStyles'));
			
	}

	/**
	 * Add the Add-Edit New Fields management page to the user settings bar
	 *
	 * @since 1.1.0
	 * @updated 1.3.0
	 * @handles action 'admin_menu' line 243 &$this and action 'admin_menu' for multisite line 249 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function csds_userRegAide_editNewFields_optionsPage(){
		$new_fields = new URA_NEW_FIELDS();
		if(function_exists('add_submenu_page')){
			$page = add_submenu_page('user-registration-aide',__('Edit New Fields', 'csds_userRegAide'), __('Edit New Fields', 'csds_userRegAide'), 'manage_options', 'edit-new-fields', array( &$new_fields, 'csds_userRegAide_editNewFields' ));  // Line 46 &$new_fields
		}
		
		add_action('admin_print_styles-'.$page, array(&$this, 'csds_userRegAide_enqueueMyStyles')); // Line 795 &$this
		
	}
	
	// ----------------------------------------     WordPress Enqueue Scripts & Styles Functions     ----------------------------------------
	
	/** Enqueues CSS stylesheet for settings menu on all plugins admin page
	 * 
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles action 'admin_print_styles' line 229 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
		
	function add_settings_css(){
		wp_enqueue_style('user_regAide_menu_style', plugins_url('css/wp-admin-menu-stylesheet.css', __FILE__));
	}
	
	/** Enqueues CSS stylesheet for my menu settings on my pages
	 * 
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles action 'admin_print_styles' line 140, 144, 148, 152 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
		
	function add_admin_settings_css(){
		wp_enqueue_style('user_regAide_admin_style', plugins_url('css/csds_ura_only_stylesheet.css', __FILE__));
	}
	
	/** Enqueues CSS stylesheet for lost password form
	 * 
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles action 'login_enqueue_scripts' line 191, 193, 197, 201 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function add_lostpassword_css(){
		$options = get_option('csds_userRegAide_Options');
		if($options['add_security_question'] == "1"){
			wp_enqueue_style('user_regAide_lost_pass_style', plugins_url('css/regist_login_stylesheet.css', __FILE__));
		}
	}
	
	/** Registers the main admin pages stylesheet for the plugin
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles action 'wp_enqueue_scripts' line 167 &$this
	 * @handles action 'admin_init' line 168 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
		
	function csds_userRegAide_stylesheet(){
		wp_register_style('user_regAide_style', plugins_url('css/user-reg-aide-style.css', __FILE__));
		wp_register_style('user_regAide_menu_style', plugins_url('css/wp-admin-menu-stylesheet.css', __FILE__));
		wp_register_style('user_regAide_admin_style', plugins_url('css/csds_ura_only_stylesheet.css', __FILE__));
		wp_register_style('user_regAide_lost_pass_style', plugins_url('css/regist_login_stylesheet.css', __FILE__));
	}
	
	/** Enqueues the stylesheet for the plugin
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles action 'admin_print_styles' -> line 654, 675, 697, 718 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function csds_userRegAide_enqueueMyStyles(){
		wp_enqueue_style('user_regAide_style');
	}
	
	// ----------------------------------------     Admin User Table Functions     ----------------------------------------
		
	/** Adds the extra fields to the default WordPress User Fields Screen 
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @Filters 'manage_users_columns' line 270 &$this (Priority: 0 - Params: 1)
	 * @Filters 'wpmu_users_columns' for multisite line 273 &$this (Priority: 0 - Params: 1)
	 * @Filters 'manage_users_columns' for multisite line 274 &$this (Priority: 0 - Params: 1)
	 * @access private
	 * @param array $columns Columns for admin user table view
	 * @returns array $columns Returns extra columns not included in original admin user table view
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
		
	function csds_userRegAide_addUserFields($columns){
				
		global $wpdb;
		$fields = get_option('csds_userRegAideFields');
		$new_fields = get_option('csds_userRegAide_NewFields');
		
		if(!empty($fields)){		
			foreach($fields as $key => $value){
				if($key != "user_pass"){
					$columns[$key] = __($value, 'csds_userRegAide');
				}
			}
		}
		if(!empty($new_fields)){
			foreach($new_fields as $key1 => $value1){
				$columns[$key1] = __($value1, 'csds_userRegAide');
			}
		}
		
		return $columns;
	}
	
	/** Fills in the extra fields to the default WordPress User Fields Screen 
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @Filters  'manage_users_custom_column' line 271 &$this (Priority: 0 - Params: 3)
	 * @Filters  'manage_users_custom_column' for multisite line 275 &$this (Priority: 0 - Params: 3)
	 * @access private
	 * @param string $value(column value), $column_name, $user_id
	 * @param string $column_name Name of new column to add to admin users table view
	 * @param int $user_id Unique user id given by WordPress
	 * @returns $data User data for specified column ($column_name)
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function csds_userRegAide_fillUserFields($value, $column_name, $user_id ){
		
		global $wpdb;
				
		$data = get_user_option($column_name, $user_id, false);
				
		return $data;
	}
	
	// ----------------------------------------     Password Functions     ----------------------------------------
	
	/**
	 * Filter for the default WordPress create password function
	 * Inserts password entered by user if option chosen to let users enter own password instead of using default random password emailing
	 * @since 1.2.0
	 * @updated 1.2.5
	 * @Filters 'random_password' line 236 &$this (Priority: 0 - Params: 1)
	 * @access private
	 * @param string $password 
	 * @returns $password
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function csds_userRegAide_createNewPassword($password){
		$options = get_option('csds_userRegAide_Options');
		$password1 = '';
		$data_meta = array();
		if (!is_multisite()) {
			if (isset($_POST["pass1"]))
				$password = $_POST["pass1"];
		}
		else {
			if (!empty($_GET['key']))
				$key = $_GET['key'];
			else if (!empty($_POST['key']))
				$key = $_POST['key'];

			if (!empty($key)) {
				// seems useless since this code cannot be reached with a bad key anyway you never know
				$key = $wpdb->escape($key);

				$sql = "SELECT active, meta FROM ".$wpdb->signups." WHERE activation_key='".$key."'";
				$data = $wpdb->get_results($sql);

				// checking to make sure data is not empty
				if (isset($data[0])) {
					// if account not active
					if (!$data[0]->active) {
						$meta = maybe_unserialize($data[1]->meta);

						if (!empty($meta['pass1'])) {
							$password = $meta['pass1'];
						}else{
							$password = $password;
						}
					}
				}
			}
		}
		return $password;
		
	}

	/**
	 * Remove default password message if user entered own password
	 *
	 * @since 1.0.0
	 * @updated 1.2.7
	 * @access private
	 * @handles $ura->remove_default_password_nag($user_id) line 319 user-reg-aide-registrationForm.php
	 * @param int $user_id
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function remove_default_password_nag($user_id) {
		global $user_id;
		$options = get_option('csds_userRegAide_Options');
		if($options['user_password'] == "1"){
			delete_user_setting('default_password_nag', $user_id);
			update_user_option($user_id, 'default_password_nag', false, true);
		}else{
			update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.
		}
		return;
	}
	
	// ----------------------------------------     Admin Page Create New User Additional Fields Functions     ----------------------------------------

	/**
	 * -- Not For Novices!!! Requires Hacking WordPress Core Files Which Isn't Recommended!! --
	 *
	 * Show the additional fields added on the User-New.php admin page
	 *
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles custom action 'load_extra_fields' line 265 &$this
	 * Some users wanted this even with the core hacks so I added it for them.
	 *
	 * To work and have the extra fields you created show up on the admin page for create new 
	 * users, you must add the following code to Line 380 of user-new.php in the wp-admin folder 
	 * of the WordPress core files.
	 *
	 * IMPORTANT!!!! If Wordpress updates, the code inside the Wordpress Core will be gone
	 * too so you will have to re-enter it as well, and if the file user-new.php changes at all, it may have 
	 * to go in a different place, so either wait for me to inform you where or contact me to make sure
	 * it is okay, hopefully they will add actions and filters soon so we won't have to do this anymore!
	 *
	 * Code to add to line 428 user-new.php just above the </table> tag replacing the <?php } ?> 
	 <?php } do_action('load_extra_fields');?>
	 * @handles custom action load_extra_fields line 265 &$this
	 * 
	 * @access private
	 * 
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function create_new_user_extra_fields(){
	 global $current_user;
		$options = get_option('csds_userRegAide_Options');
		$field_names = 'fieldNames[]';
		$fieldKey = '';
		$fieldName = '';
		$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
		$no_fields = (int) 2;
		$current_user = wp_get_current_user();
		if(current_user_can('create_users', $current_user->ID)){
			if(!empty($csds_userRegAide_NewFields)){
				echo '<tr>';
				echo '<th colspan="2"><b>'. __('User Registration Aide Additional Fields', 'csds_userRegAide').'</b></th>';
				echo '</tr>';
				foreach($csds_userRegAide_NewFields as $fieldKey => $fieldName){ 
					echo '<tr>';
					echo '<th><label for="'.$fieldKey.'">'.$fieldName.'</label></th>';
					echo '<td><input type="text" name="'.$fieldKey.'" id="'.$fieldKey.'" value="" class="regular-text" /></td>';
					echo '</tr>';
				}
			}else{
				$no_fields = 1;
			}
									
			if($options['show_support'] == "1"){
				echo '<tr>';
				echo '<td><a target="_blank" href="'.$options['support_display_link'].'">' . $options['support_display_name'] . '</a></td>';
				echo '</tr>';
			}
			echo wp_nonce_field('userRegAideNewUserForm', 'userRegAideNewUserNonce');
		}else{
			exit(__('Naughty, Naughty! You do not have permissions to do this!', 'csds_userRegAide'));
		}
			
	} // end function
	
	 /**
	 * -- Not For Novices!!! Requires Hacking WordPress Core Files Which Isn't Recommended!! --
	 *
	 * Updates the additional fields added on the User-New.php admin page
	 *
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles custom action 'update_new_user_fields' line 266 &$this
	 * Some users wanted this even with the core hacks so I added it for them.
	 *
	 * To work and have the extra fields you created show up on the admin page for create new 
	 * users, you must add the following code to Line 102 to 104 of user-new.php in the wp-admin folder 
	 * of the WordPress core files.
	 *
	 * IMPORTANT!!!! If Wordpress updates, the code inside the Wordpress Core will be gone
	 * too so you will have to re-enter it as well, and if the file user-new.php changes at all, it may have 
	 * to go in a different place, so either wait for me to inform you where or contact me to make sure
	 * it is okay, hopefully they will add actions and filters soon so we won't have to do this anymore!
	 *
	 * Code to add to line 102 user-new.php (Dont add line numbers!!!)
	 102 if(!is_wp_error( $user_id ) ) {
	 103	do_action('update_new_user_fields', $user_id);
	 104 }
	 *
	 *
	 * @handles custom action load_extra_fields line 265 &$this
	 * @accepts $user_id
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	 
	 function create_new_user_extra_fields_updates($user_id){
	 
		global $wpdb, $current_user, $pagename;
		$options = get_option('csds_userRegAide_Options');
		$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
		$fieldKey = '';
		$fieldName = '';
		$newValue = '';
		$newValue1 = (string) '';
		$newValue2 = (string) '';
		$extraFields = array();
		$current_user = wp_get_current_user();
		if(!empty($user_id)){
			
			$userID = $user_id;
			
		}else{
			exit('No User ID');
		}
		if(!empty($csds_userRegAide_NewFields)){
			
			if(!is_multisite()){
				if(current_user_can('edit_user', $current_user->ID)  || current_user_can('create_users', $current_user->ID)){
					if(wp_verify_nonce($_POST["userRegAideNewUserNonce"], 'userRegAideNewUserForm')){
						foreach($_POST as $key => $value){
							foreach($csds_userRegAide_NewFields as $fieldKey => $fieldName){
								if($key == $fieldKey){
									$newValue = esc_attr(stripslashes($_POST[$fieldKey]));
									if(!empty($newValue)){
										update_user_meta($userID, $fieldKey, $newValue);
									}else{
										//exit(__('New Value empty!'));
									}
								}
							}
						}	
					}else{
						exit(__('Failed Security Check', 'csds_userRegAide'));
					}
				}else{
					exit(__('You do not have sufficient permissions to edit this user, contact a network administrator if this is an error!', 'csds_userRegAide'));
				}
			}else{
				if(current_user_can('edit_user', $current_user->ID)  || current_user_can('create_users', $current_user->ID)){
					if(wp_verify_nonce($_POST["userRegAideNewUserNonce"], 'userRegAideNewUserForm')){
							foreach($csds_userRegAide_NewFields as $fieldKey => $fieldName){
								$newValue = $_POST[$fieldKey];
								if(!empty($newValue)){
									update_user_meta($userID, $fieldKey, $_POST[$fieldKey]);
								}
								else{
									//exit(__('New Value empty!'));
								}
							}
					}else{
						exit(__('Failed Security Check', 'csds_userRegAide'));
					}
				}else{
					exit(__('You do not have sufficient permissions to edit this user, contact a network administrator if this is an error!', 'csds_userRegAide'));
				}
			} // end if multisite
		} // end if not empty new fields
		
	} // end function
	
	// ----------------------------------------     New Fields Add - Delete Functions     ----------------------------------------

	/**
	 * Adds all the additional fields created to existing users meta
	 *
	 * @since 1.0.0
	 * @handles csds_add_field_to_users_meta($results) line 279 &$admin
	 * @access private
	 * @accepts string $field
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
		
	function csds_add_field_to_users_meta($field){

		global $wpdb;
		//require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$count = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users;");
		$i = 1;
		while($i <= $count){
			$user_id = $i;
			update_user_meta( $user_id, $field, "");
			$i++;
		} // end while
	
	} // end function

	/**
	 * Deletes the additional fields from existing users meta
	 * 
	 * @since 1.1.0
	 * @handles csds_delete_field_from_users_meta($results1) line 106 &$URA_NEW_FIELDS
	 * @access private
	 * @accepts string $field
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
		
	function csds_delete_field_from_users_meta($field){

		global $wpdb;
		//require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$count = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users;");
		$i = 1;
		while($i <= $count){
			$user_id = $i;
			delete_user_meta( $user_id, $field, "");
			$i++;
		} // end while
		
	} // end function
	
	// ----------------------------------------     Deactivation Functions     ----------------------------------------

	/**
	 * Deactivation Function
	 * @since 1.0.0
	 * @handles 'register_deavtivation_hook' line 304 &$this
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/

	function csds_userRegAide_deactivation()
	{
		// Do nothing here in case user wants to reactivate at a later time Just included because I am fairly sure WordPress requires something like this
		// uninsttall.php handles plugin deletion
	}
			
} //end CSDS_USER_REG_AIDE class

// ----------------------------------------     Pluggable Functions     ----------------------------------------

/**
 * Emails registration confirmation to new user
 *
 * @since 1.0.0
 * @updated 1.3.0
 * @access private
 * @accepts $user_id, $plaintext_pass
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
if(!function_exists('wp_new_user_notification')){
	function wp_new_user_notification($user_id, $plaintext_pass = '') {
		
		$user = new WP_User($user_id);
		$options = get_option('csds_userRegAide_registrationFields');
		$user_login = stripslashes($user->user_login);
		$user_email = stripslashes($user->user_email);

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	 
		// we want to reverse this for the plain text arena of emails.
	 
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	 
		$message  = sprintf(__('A new user has registered on your site %s:'), $blogname) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";
	 
		@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration Alert'), $blogname), $message);
	 
		if ( empty($plaintext_pass) ){
			return;
		}
		$xwrd = 'User Entered';
		$message = sprintf(__('Thank you for registering with %s!'), $blogname) . "\r\n\n";
		$message .= sprintf(__('Here are your new login credentials for %s:'), $blogname) . "\r\n\n";
		//$message = sprintf(__($options['wp_user_notification_message'])); not using dont want to confuse users 
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n";
		if(in_array('Password', $options)){
			$message .= sprintf(__('Password: %s'), $xwrd) . "\r\n";
		}elseif(!in_array('Password', $options)){
			$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
		}
		$message .= wp_login_url() . "\r\n";
		 
		wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);
		 
	}
}

// ----------------------------------------     Plugin Initiate Functions     ----------------------------------------

/**
 * Activates & runs The Plugin!
 *
 * @since 1.0.0
 * @updated 1.3.0
 * @access private
 * @initiates csds_userRegAide_install() line 363 &$this
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/
# 
if( class_exists('CSDS_USER_REG_AIDE') ){
	$csds_userRegAide_Instance = new CSDS_USER_REG_AIDE();
	if(isset($csds_userRegAide_Instance)){
		// if(function_exists('csds_userRegAide_install')){
			register_activation_hook( __FILE__, array(  &$csds_userRegAide_Instance, 'csds_userRegAide_install' ) );
		// }
		
		//register_deactivation_hook( __FILE__, array(  &$csds_userRegAide_Instance, 'Uninstall' ) );
	}
}
?>