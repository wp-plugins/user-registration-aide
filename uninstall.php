<?php

/* Plugin Name: User Registration Aide Uninstall File
* Added Version 1.1.0
* Brian Novotny
* Creative Software Design Solutions
* http://creative-software-design-solutions.com
*/

//For Debugging and Testing Purposes ------------

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//$ebitd = ini_get('error_reporting');
//error_reporting($ebits ^ E_NOTICE);

// ----------------------------------------------

	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')){
		exit('Something Bad Happened');
	}else{
		// $csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
		// foreach($csds_userRegAide_NewFields as $field => $value){
			
			// $count = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users;");
			// $i = 1;
				// while($i <= $count)
			// {
				// $user_id = $i;
				// delete_user_meta( $user_id, $field, "");
				// $i++;
			// }
		// }
	//$wpdb->delete($wpdb->options, array('csds_userRegAideFields' => $csds_userRegAideFields));
	//$wpdb->delete($wpdb->options, array($csds_userRegAideFields);
	// $wpdb->delete($wpdb->options, array('csds_userRegAide_knownFields' => $csds_userRegAide_knownFields);
	// $wpdb->delete($wpdb->options, array('csds_userRegAide_NewFields' => $csds_userRegAide_NewFields);
	// $wpdb->delete($wpdb->options, array('csds_userRegAide_fieldOrder' => $csds_userRegAide_fieldOrder);
	// $wpdb->delete($wpdb->options, array('csds_userRegAide_registrationFields' => $csds_userRegAide_registrationFields);
	// $wpdb->delete($wpdb->options, array('csds_userRegAide_newField' => $csds_userRegAide_newField);
	// $wpdb->delete($wpdb->options, array('csds_userRegAide_dbVersion' => $csds_userRegAide_dbVersion);
	// $wpdb->delete($wpdb->options, array('csds_userRegAide' => $csds_userRegAide);
	delete_option('csds_userRegAideFields');
	delete_option('csds_userRegAide_knownFields');
	delete_option('csds_userRegAide_fieldOrder');
	delete_option('csds_userRegAide_registrationFields');
	delete_option('csds_userRegAide_newField');
	delete_option('csds_userRegAide_dbVersion');
	delete_option('csds_userRegAide');
	$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
		foreach($csds_userRegAide_NewFields as $field => $value){
			
			$count = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users;");
			$i = 1;
				while($i <= $count)
			{
				$user_id = $i;
				delete_user_meta( $user_id, $field, "");
				$i++;
			}
		}
	delete_option('csds_userRegAide_NewFields');
	
	
	}
		
	
	?>