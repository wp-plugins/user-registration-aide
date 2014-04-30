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
		$field1 = array();
		$field2 = array();
		$field3 = array();
		$field4 = array();
		$field5 = array();
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
		$field1 = array();
		$field2 = array();
		$field3 = array();
		$field4 = array();
		$field5 = array();
		$ucnt = (int) 0;
		$count = (int) 0;
		$logo = IMAGES_PATH."csds-dash_logo.png";
		
		$cols = $this->selected_dw_fields_array();
		$options = get_option('csds_userRegAide_Options');		
		$count = count($users);
		$col_count = (int) 1;
		foreach($users as $nuser){
			$uid[$ucnt] = $nuser->user_ID;
				foreach($cols as $ukey => $uvalue){
					if($ukey != 'roles'){
						if($col_count == 1){
							$field1[$ucnt] = $nuser->$ukey;
						}elseif($col_count == 2){
							$field2[$ucnt] = $nuser->$ukey;
						}elseif($col_count == 3){
							$field3[$ucnt] = $nuser->$ukey;
						}elseif($col_count == 4){
							$field4[$ucnt] = $nuser->$ukey;
						}elseif($col_count == 5){
							$field5[$ucnt] = $nuser->$ukey;
						}
					}elseif($ukey == 'roles'){
						$roles = $nuser->$ukey;
						$userrole = array_shift($roles);
						$field5[$ucnt] = $userrole;
					}
				$col_count ++;	
				}
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
				if($key == 'user_email'){
					$width = ' width="22%"';
				}elseif($key == 'role'){
					$width = '  width="23%"';
				}elseif($key == 'user_nicename'){
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
						echo '<td width="15%">'.$field1[$index].'</td>';
						echo '<td width="40%">'.$field2[$index].'</td>';
						echo '<td width="15%">'.$field3[$index].'</td>';
						echo '<td width="15%">'.$field4[$index].'</td>';
						echo '<td width="15%">'.$field5[$index].'</td>';
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
	 * Displays all the fields user can choose from to display on his dashboard widget
	 * @since 1.3.6
	 * @access public
	 * @returns array
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	 *
	*/
	
	function fields_array(){
		
		$existing_fields = array(
			'user_login'	=>	__('Login', 'csds_userRegAide'),
			'user_nicename'	=>	__('Username', 'csds_userRegAide'),
			'user_email'	=>	__('Email', 'csds_userRegAide'),
			'user_url'		=>	__('Website', 'csds_userRegAide'),
			'display_name'	=>	__('Display Name', 'csds_userRegAide'),
			'first_name'	=>	__('First Name', 'csds_userRegAide'),
			'last_name'		=>	__('Last Name', 'csds_userRegAide'),
			'nickname'		=>	__('Nickname', 'csds_userRegAide'),
			'roles'			=>	__('Role', 'csds_userRegAide')
		);
		
		$new_fields = array();
		$new_fields = get_option('csds_userRegAide_NewFields');
		$all_fields = array();
		if(!empty($new_fields)){
			$all_fields = array_merge($existing_fields, $new_fields);
		}else{
			$all_fields = $existing_fields;
		}
		return $all_fields;		
			
	}
	
	/**
	 * Creates and returns an array of all the chosen dashboard widget fields
	 * @since 1.3.6
	 * @access public
	 * @returns array
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	 *
	*/
	
	function selected_dw_fields_array(){
		
		$options = get_option('csds_userRegAide_Options');
		
		$selected_fields = array();
		
		if(!empty($options['dwf1_key']) && !empty($options['dwf1'])){
			$selected_fields[$options['dwf1_key']]	=	__($options['dwf1'], 'csds_userRegAide');
		}
		if(!empty($options['dwf2_key']) && !empty($options['dwf2'])){
			$selected_fields[$options['dwf2_key']]	=	__($options['dwf2'], 'csds_userRegAide');
		}
		if(!empty($options['dwf3_key']) && !empty($options['dwf3'])){
			$selected_fields[$options['dwf3_key']]	=	__($options['dwf3'], 'csds_userRegAide');
		}
		if(!empty($options['dwf4_key']) && !empty($options['dwf4'])){
			$selected_fields[$options['dwf4_key']]	=	__($options['dwf4'], 'csds_userRegAide');
		}
		if(!empty($options['dwf5_key']) && !empty($options['dwf5'])){
			$selected_fields[$options['dwf5_key']]	=	__($options['dwf5'], 'csds_userRegAide');
		}
			
		
		
		return $selected_fields;		
			
	}
	
	/**
	 * Displays options for dashboard widget on URA admin page
	 * @since 1.3.6
	 * @handles action 'admin_print_styles' line 233 user-registration-aide.php
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function dashboard_widget_options(){
		
		$options = get_option('csds_userRegAide_Options');
		$all_fields = $this->fields_array();
		$sel_fields = $this->selected_dw_fields_array();
		?>
		<table class="adminPage_Dash">
			<tr colspan="2">
				<th colspan="3"><?php _e('Dashboard Widget Display Options', 'csds_userRegAide');?> </th>
			</tr>
			<tr>
				<td><?php _e('Choose to display or not display the dashboard widget on the WordPress Admin page: ', 'csds_userRegAide');?>
				<span title="<?php _e('Select this option to show the Creative Software Design Solutions Users table Dashboard Widget', 'csds_userRegAide');?>">
				<input type="radio" name="csds_dashWidgetDisplay" id="csds_dashWidgetDisplay" value="1" <?php
				if ($options['show_dashboard_widget'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide');?></span>
				<span title="<?php _e('Select this option NOT to show the Creative Software Design Solutions Users table Dashboard Widget',  'csds_userRegAide');?>">
				<input type="radio" name="csds_dashWidgetDisplay" id="csds_dashWidgetDisplay" value="2" <?php
				if ($options['show_dashboard_widget'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?></span></td>
				<?php // new adding option to show user selected fields in dashboard widget ?>
				<td><?php _e('Choose to display custom fields in the dashboard widget on the WordPress Admin page: ', 'csds_userRegAide');?>
				<span title="<?php _e('Select this option to show the Creative Software Design Solutions Users table with your own chosen fields in the Dashboard Widget', 'csds_userRegAide');?>">
				<input type="radio" name="csds_dashWidgetFields" id="csds_dashWidgetFields" value="1" <?php
				if ($options['custom_dashboard_widget_fields'] == 1) echo 'checked' ;?> /> <?php _e('Yes', 'csds_userRegAide');?></span>
				<span title="<?php _e('Select this option to show your own chosen fields in the Creative Software Design Solutions Users table Dashboard Widget',  'csds_userRegAide');?>"></span>
				<input type="radio" name="csds_dashWidgetFields" id="csds_dashWidgetFields" value="2" <?php
				if ($options['custom_dashboard_widget_fields'] == 2) echo 'checked' ;?> /> <?php _e('No', 'csds_userRegAide'); ?><span title="<?php _e('Select this option to NOT show your own chosen fields in the Creative Software Design Solutions Users table Dashboard Widget',  'csds_userRegAide');?>"></span></td>
				<td><?php
				echo '<p>' . __('Here, you can select your own fields to show on the User Registration Aide Dashboard Widget.', 'csds_userRegAide') .'</p>';
				echo '<p><b>' . __('Note: You can only select a maximum of 5 fields due to the space constraints in the WordPress Dashboard!', 'csds_userRegAide') .'</b></p>';
				echo '<p class="adminPage"><select name="selectedFields[]" id="csds_userRegMod_Select" title="'.__('You can only select up to 5 fields due to space limitations, just hold down the control key while selecting multiple fields.', 'csds_userRegAide').'" size="8" multiple style="height:100px">';
				foreach($all_fields as $key1 => $value1){
					if(!empty($sel_fields)){
						if(in_array("$value1",$sel_fields)){
							$selected = "selected=\"selected\"";
						}else{
							$selected = NULL;
						}
					}else{
					$selected = NULL;
					}
					
				echo "<option value=\"$key1\" $selected >$value1</option>";
					
				}?>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<div class="submit"><input type="submit" class="button-primary" name="dash_widget_update" value="<?php _e('Update Dashboard Widget Options', 'csds_userRegAide');?>"/></div>
				</td>
			</tr>
		</table> <?php
	}
	
	/**
	 * Updates options for dashboard widget on URA admin page
	 * @since 1.3.6
	 * @handles action 'admin_print_styles' line 233 user-registration-aide.php
	 * @access private
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function update_dashboard_widget_options(){
		$update = array();
		$update = get_option('csds_userRegAide_Options');
		$update['show_dashboard_widget'] = $_POST['csds_dashWidgetDisplay'];
		update_option("csds_userRegAide_Options", $update);
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