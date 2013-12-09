<?php
/**
 * User Registration Aide - Dashboard Widget for WordPres Admin Page
 * Plugin URI: http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/
 * Version: 1.3.1
 * Since Version 1.3.0
 * Author: Brian Novotny
 * Author URI: http://creative-software-design-solutions.com/
*/

//For Debugging and Testing Purposes ------------



// ----------------------------------------------

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once ("user-reg-aide-admin.php");
require_once (URA_PLUGIN_PATH."user-registration-aide.php");
require_once ("user-reg-aide-newFields.php");
require_once ("user-reg-aide-regForm.php");
//require_once (ABSPATH . 'wp-admin/includes/class-wp-users-list-table.php');

/**
 * Class added for better functionality
 *
 * @category Class
 * @since 1.3.0
 * @updated 1.3.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/


class URA_DASHBOARD_WIDGET
{
	
	public static $instance;
	
	public function __construct() {
		$this->URA_DASHBOARD_WIDGET();
		$this->index = (int) 0;
	}
		
	function URA_DASHBOARD_WIDGET() { //constructor
		global $wp_version;
		self::$instance = $this;
	}

	/**
	 * Adds dashboard widget to wp admin page for news & update information
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles action 'wp_dashboard_setup' line 128 user-registration-aide.php
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function ura_dashboard_widget_action(){
		$options = get_option('csds_userRegAide_Options');
		if($options['show_dashboard_widget'] == 1){
			wp_add_dashboard_widget('csds_ura_dash_widget', '<a class="ura-dash-widget" href="http://creative-software-design-solutions.com">Creative Software Design Solutions</a><b>: User Registration Aide</b>', array(&$this, 'ura_dashboard_widget_display'));
		}
	}
	
	/**
	 * Adds dashboard widget to wp admin page for news & update information
	 * @since 1.3.0
	 * @updated 1.3.0
	 * @handles action line 60 &$this for 'wp_add_dashboard_widget'
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function ura_dashboard_widget_display(){
		
		global $wpdb, $role;
		$index = (int) 0;
		$cnt = (int) 0;
		$page = (int) 1;
		$load = (int) 0;
		$id = array();
		$uid = array();
		$uname = array();
		$email = array();
		$fname = array();
		$lname = array();
		$urole = array();
		$ucnt = (int) 0;
		$count = (int) 0;
		
		
		$user_args = array(
			'fields' 	=>	'all_with_meta'
		);
		$users = get_users($user_args);
		
		if($index == "0"){
			$this->display_users($users);
		}
				
	}
	
	/**
	 * Displays dashboard widget users for wp admin page for news & update information
	 * @since 1.3.0
	 * @handles display users line 97 &$this
	 * @params $users array of wordpress site users
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function display_users($users){
		
		global $wpdb, $role;
		$index = (int) 0;
		$cnt = (int) 0;
		$id = array();
		$uid = array();
		$uname = array();
		$email = array();
		$fname = array();
		$lname = array();
		$urole = array();
		$ucnt = (int) 0;
		$count = (int) 0;
		$logo = IMAGES_PATH."csds-dash_logo.png";
		
		$cols = array(
			'username' => __( 'Username', 'csds_userRegAide'),
			'email'     => __( 'E-Mail', 'csds_userRegAide' ),
			'first_name'    => __( 'First Name', 'csds_userRegAide' ),
			'last_name'     => __( 'Last Name', 'csds_userRegAide' ),
			'role'	   => __( 'Role', 'csds_userRegAide' )
		);
		
		$count = count($users);
		foreach($users as $nuser){
			$uid[$ucnt] = $nuser->user_ID;
			$uname[$ucnt] = $nuser->user_nicename;
			$email[$ucnt] = $nuser->user_email;
			$fname[$ucnt] = $nuser->first_name;
			$lname[$ucnt] = $nuser->last_name;
			$roles = $nuser->roles;
			$userrole = array_shift($roles);
			$urole[$ucnt] = $userrole;
			$ucnt ++;
		}
		
		echo '<div class="csds-dash-widget" id="csds-dash-widget">';
		
			echo '<table class="admin-dash">';
			echo '<tr>';
			echo '<th colspan="5" class="main_title">';
			echo __('Current Site Users:', 'csds_userRegAide');
			echo '</th>';
			echo '</tr>';
			echo '<tr>';
			foreach($cols as $key => $name){
				if($key == 'email'){
					$width = ' width="22%"';
				}elseif($key == 'role'){
					$width = '  width="23%"';
				}elseif($key == 'username'){
					$width = '  width="18%"';
				}elseif($key == 'first_name'){
					$width = '  width="19%"';
				}elseif($key == 'last_name'){
					$width = '  width="19%"';
				}
				echo '<th class="col_titles" '.$width.'>'. __($name, 'csds_userRegAide').'</th>';
			}
			echo '</tr>';
			
			foreach($users as $user){ 
				foreach($uid as $key => $value){
					if($value = $user->ID){
						echo '<tr>';
						echo '<td width="15%">'.$uname[$index].'</td>';
						echo '<td width="40%">'.$email[$index].'</td>';
						echo '<td width="15%">'.$fname[$index].'</td>';
						echo '<td width="15%">'.$lname[$index].'</td>';
						echo '<td width="15%">'.$urole[$index].'</td>';
						echo '</tr>';
						$cnt ++;
						$index ++;
						
						if($index == $count){
							break;
						}
					}
				}
			
				if($index == $count){
					break;
				}
			}
			echo '</table>';
			echo '<br />';
		echo '<a href="http://creative-software-design-solutions.com" target="_blank" rel="follow"><img src="'.$logo.'" /></a>';
		echo '</div>';
				
	}
	
	/**
	 * Adds stylesheets for dashboard widget to wp admin page for news & update information
	 * @since 1.3.0
	 * @handles action 'admin_print_styles' line 233 user-registration-aide.php
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function csds_dashboard_widget_style(){
		wp_register_style('user_regAide_admin_dash_style', CSS_PATH.'/admin-dash.css', false);
		wp_enqueue_style('user_regAide_admin_dash_style');
	}
	
	
} // end class