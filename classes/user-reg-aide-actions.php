<?php

/**
 * User Registration Aide - Actions & Filters
 * Plugin URI: http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/
 * Version: 1.3.7.3
 * Since Version 1.3.0
 * Author: Brian Novotny
 * Author URI: http://creative-software-design-solutions.com/
*/

//For Debugging and Testing Purposes ------------



// ----------------------------------------------

/*
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
 * Class for better functionality
 *
 * @category Class
 * @since 1.3.0
 * @updated 1.3.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

class CSDS_URA_ACTIONS
{

	public static $instance;

	public function __construct() {
		$this->CSDS_URA_ACTIONS();
	}
		
	function CSDS_URA_ACTIONS() { //constructor
	
		global $wp_version;
		
		self::$instance = $this;
	}
	
	/**
	 * Creates array for menu tabs titles
	 *
     * @since 1.3.0
     * @updated 1.3.0
	 * @returns $tabs - array of menu tabs Titles for plugin
     * @access private
     * @author Brian Novotny
     * @website http://creative-software-design-solutions.com
    */
	
	function menu_tabs_array(){
		$tabs = array('registration_fields' => 'Registration Fields', 'edit_new_fields' => 'Edit New Fields', 'registration_form_options' => 'Registration Form Options', 'registration_form_css_options' => 'Registration Form Messages & CSS Options');
		
		return $tabs;
	}
	
	/**
	 * Creates array for menu tabs links
	 *
     * @since 1.3.0
     * @updated 1.3.0
	 * @returns $menu_links - array of links for menu tabs
     * @access private
     * @author Brian Novotny
     * @website http://creative-software-design-solutions.com
    */
	
	function menu_links_array(){
		$menu_links = array('registration_fields' => 'admin.php?page=user-registration-aide', 'edit_new_fields' => 'admin.php?page=edit-new-fields', 'registration_form_options' => 'admin.php?page=registration-form-options', 'registration_form_css_options' => 'admin.php?page=registration-form-css-options');
		
		return $menu_links;
	}
	
	/**
	 * Shows tabbes menu at top of options pages for easier user access -- wont work for different pages so i do it separetely on each page
	 * @handles action for menu tabs 'create tabs' $ura line 246
     * @since 1.3.0
     * @updated 1.3.0
	 * @accepts $current_page (current menu page)
     * @access private
     * @author Brian Novotny
     * @website http://creative-software-design-solutions.com
    */
	
	function options_tabs_page($current_page){
		
		$tabs = $this->menu_tabs_array();  // line 71 &$this
		$menu_links = $this->menu_links_array(); // line 88 &$this
		$tab_links = array();
		foreach($menu_links as $menu_key => $menu_name){
			foreach($tabs as $tab_key => $tab_name){
			
				if($menu_key == $tab_key && $tab_key == $current_page ){
					$tab_links[$tab_key] = '<a class="nav-tab nav-tab-active" href="'.admin_url($menu_name).'">'.$tab_name.'</a>';
				}elseif($menu_key == $tab_key){
					$tab_links[$tab_key] = '<a class="nav-tab" href="'.admin_url($menu_name).'">'.$tab_name.'</a>';
				}
			}
		}
		echo '<h2>';
		foreach($tab_links as $link_key => $link_name){
			echo $link_name;
		}
		echo '</h2>';
	
	}
	
	/**
	 * Shows support section for options pages
	 * @handles custom action for show support 'show_support' $ura line 240
     * @since 1.3.0
     * @updated 1.3.0
	 * @accepts $current_page (current menu page)
     * @access private
     * @author Brian Novotny
     * @website http://creative-software-design-solutions.com
    */
	
	function show_support_section(){ 
		$options = get_option('csds_userRegAide_Options');?>
		<div class="stuffbox"><span class="regForm"><?php _e('Plugin Support and Configuration Information:', 'csds_userRegAide');?> </span>
			<div class="inside">
			<table class="csds_support">
			<tr>
			<th class="csds_support_links" colspan="4"><a href="http://creative-software-design-solutions.com" target="_blank">Creative Software Design Solutions</a></th>
			</tr>
			<tr>
			<th class="csds_support_th" colspan="4"><?php _e('Please show your support & appreciation and help us out with a donation!', 'csds_userRegAide');?></th>
			</tr>
			<tr>
			<td>
			<p><?php _e('Show Plugin Support: ', 'csds_userRegAide');?><input type="radio" id="csds_userRegAide_support" name="csds_userRegAide_support"  value="1" <?php
				if ($options['show_support'] == 1) echo 'checked' ;?>/> Yes
				<input type="radio" id="csds_userRegAide_support" name="csds_userRegAide_support"  value="2"<?php
				if ($options['show_support'] == 2) echo 'checked' ;?>/> No
			
			<div class="submit"><input name="csds_userRegAide_support_submit" id="csds_userRegAide_support_submit" lang="publish" class="button-primary" value="Update" type="Submit" /></div>
			</td>
			<td>
			<h2 class="support"><?php _e('Plugin Configuration Help', 'csds_userRegAide');?></h2>
			<?php
			echo '<ul>';
			echo '<li><a href="http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/" target="_blank">Plugin Page & Screenshots</a></li>';
			echo '</ul>';
			echo '</td>';
			echo '<td>';
			echo '<h2 class="support">'.__('Check Official Website', 'csds_userRegAide').'</h2>';
			echo '<ul>';
			echo '<li><a href="http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/" target="_blank">Check official website for live demo</a></li></ul></td>';?>
			<td>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="6BCZESUXLS9NN">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"><?php
			
			echo '</td>';
			echo '</tr>';
			echo '</table>';
			echo '</div>';
		echo '</div>';
	}
} // end class