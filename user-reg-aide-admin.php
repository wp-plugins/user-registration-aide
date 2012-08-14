<?php

/*
 * User Registration Aide - Plugin Main Administration Page
 * Plugin URI: http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/
 * Plugin Description: Forces new users to register additional fields with the option to add additional fields other than those 
 * supplied with the default Wordpress Installation. We have kept it simple in this version for those of you whom aren't familiar with 
 * handling multiple users or websites. We also are currently working on expanding this project with a paid version which will contain 
 * alot more features and options for those of you who wish to get more control over users and user access to your site.
 * Version: 1.1.0
 * Author: Brian Novotny
 * Author URI: http://creative-software-design-solutions.com/
*/

//For Debugging and Testing Purposes ------------

error_reporting(E_ALL);
ini_set('display_errors', '1');
//$ebitd = ini_get('error_reporting');
//error_reporting($ebits ^ E_NOTICE);

// ----------------------------------------------

/**
 * Fills array of known fields
 *
 * @since 1.0.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_fill_known_fields(){
	
	if(!empty($csds_userRegAide_knownFields) && !empty($csds_userRegAide)){
		$csds_userRegAide_knownFields = array();
		$csds_userRegAideFields = array();
	}
	$csds_userRegAide_knownFields = array(
	"first_name"	=> "First Name",
	"last_name"		=> "Last Name",
	"nickname"		=> "Nickname",
	"aim"			=> "AIM",
	"yim"			=> "Yahoo IM",
	"jabber"		=> "Jabber / Google Talk",
	"description"   => "Biographical Info"
	);
	
	update_option("csds_userRegAideFields", $csds_userRegAide_knownFields);
	update_option("csds_userRegAide_knownFields", $csds_userRegAide_knownFields);
	if(!empty($csds_userRegAide_NewFields)){
		foreach($csds_userRegAideFields as $key1 => $field1){
			foreach($csds_userRegAide_NewFields as $key => $field){
				if(!$key1 == $key){
					$csds_userRegAideFields[$key] = $field;
				}
			}
		}
	}
	
	// Updates the field order set to default by order entered into program
	
	if(empty($csds_userRegAide_fieldOrder) && !empty($csds_userRegAide_NewFields)){
		csds_userRegAide_update_field_order();
	}
	
	if(empty($csds_userRegAide_registrationFields)){
		csds_userRegAide_updateRegistrationFields();
	}
	
	/*
	* Checks to see if db version exists, if not it creates a database version for future
	* upgrades to make for easier changes to old options and settings
	*/
	
	if(empty($csds_userRegAide_dbVersion)){
		
		$csds_userRegAide_dbVersion = '1.1.0';
		update_option("csds_userRegAide_dbVersion", $csds_userRegAide_dbVersion);
	
	}
	// Redirects back to admin page after fields are loaded or reloaded
	
	csds_userRegAide_myOptionsSubpanel();
	
}

/**
 * Updates the registration fields array and storage method in options db upgrade in 1.1.0
 *
 * @since 1.1.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_updateRegistrationFields(){

	global $csds_userRegAide_knownFields, $csds_userRegAide_getOptions, $csds_userRegAide_NewFields, $csds_userRegAide_fieldOrder;

	$csds_userRegAide_fieldOrder = get_option('csds_userRegAide_fieldOrder');
	$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	$csds_userRegAideFields = get_option('csds_userRegAideFields');
	$csds_userRegAide_registrationFields = get_option('csds_userRegAide_registrationFields');
	$csds_userRegAide_getOptions = get_option('csds_userRegAide');
	
	// Checks to see if older version of additional fields exists and if so transfers them to new option
	
	if(!empty($csds_userRegAide_getOptions["additionalFields"])){
		foreach($csds_userRegAide_getOptions["additionalFields"] as $key => $value){
			foreach($csds_userRegAide_knownFields as $key1 => $value1){
				if($value == $key1){
					$csds_userRegAide_registrationFields[$key1] = $value1;
					$csds_userRegAide_registrationFields = $csds_userRegAide_registrationFields;
					update_option("csds_userRegAide_registrationFields", $csds_userRegAide_registrationFields);
				}
			}
			foreach($csds_userRegAide_NewFields as $key2 => $value2){
				if($value == $key2){
					$csds_userRegAide_registrationFields[$key2] = $value2;
					$csds_userRegAide_registrationFields = $csds_userRegAide_registrationFields;
					update_option("csds_userRegAide_registrationFields", $csds_userRegAide_registrationFields);
				}
				// Testing echo '<div id="message" class="updated fade"><p>'. __('Test key:'.$key.' value: '.$value.'Key 1: '.$key1.'Value: '.$value1.'Key 2: '.$key2.'Value 2: '.$value2.' end test', 'csds_userRegAide') .'</p></div>';
			}
		}
	$csds_userRegAide = array();
	update_option("csds_userRegAide", $csds_userRegAide);
	delete_option($csds_userRegAide);
	}
}

/**
 * Fills and arranges the order of new fields based on order of creation initially
 *
 * @since 1.1.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_update_field_order(){
	$csds_userRegAide_fieldOrder = get_option('csds_userRegAide_fieldOrder');
	$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	$csds_userRegAideFields = get_option('csds_userRegAideFields');
	if(empty($csds_userRegAide_fieldOrder)){
		if(!empty($csds_userRegAide_NewFields)){
			$i = 1;
			foreach($csds_userRegAide_NewFields as $key => $field){
				$csds_userRegAide_fieldOrder[$key] = $i;
				
				$i++;
			}
		}
		$csds_userRegAideFields = array();
		$csds_userRegAideFields = $csds_userRegAide_knownFields + $csds_userRegAide_NewFields;
	}
	
	update_option("csds_userRegAide_fieldOrder", $csds_userRegAide_fieldOrder);
}

/**
 * Loads and displays the User Registration Aide administration page
 *
 * @since 1.0.0
 * @updated 1.1.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_myOptionsSubpanel(){

global $csds_userRegAide_knownFields, $csds_mainMenuErrors, $csds_userRegAide_registrationFields, $csds_userRegAide_NewFields, $csds_userRegAideFields_getOptions, $csds_userRegAide_fieldOrder;
	
	if(empty($csds_userRegAide_registrationFields)){
		csds_userRegAide_updateRegistrationFields();
	}
	
	// Declaring - Defining Variables
	
	$regFields = '';
	$seperator = '';
	$csds_userRegAide_newFieldKey = '';
	$csds_userRegAide_newField = '';
	$csds_userRegMod_fields_missing = '';
	$csds_userRegMod_fields_missing1 = '';
	$csds_userRegMod_fields_missing2 = '';
	$key = '';
	$key1 = '';
	$key2 = '';
	$value = '';
	$value1 = '';
	$value2 = '';
	$cnt = '';
	$field = '';
	$new_field = '';
	$results2 = array();
	//$additionalFields = array();
	// Updating Arrays from options db
	
	$csds_userRegAideFields = get_option('csds_userRegAideFields');
	$csds_userRegAide_registrationFields = get_option('csds_userRegAide_registrationFields');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	$csds_userRegAide_knownFields_count = count($csds_userRegAide_knownFields);
	$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
	$csds_userRegAide_fieldOrder = get_option('csds_userRegAide_fieldOrder');
	$csds_userRegAide_support = get_option('csds_userRegAide_support');
		
		// Handles adding fields to registration form
		
		if (isset($_POST['info_update']))
		{
			$csds_userRegAide_registrationFields = array();
			$csds_current_user = wp_get_current_user();
				if($csds_current_user->has_cap('edit_plugins')){
					if(!empty($_POST['additionalFields'])){
						$results2 =  $_POST['additionalFields'];
							if(!empty($results2)){
								foreach($results2 as $key => $value){
								
									foreach($csds_userRegAide_knownFields as $key1 => $value1){
										if($value == $key1){
											$csds_userRegAide_registrationFields[$key1] = $value1;
											$csds_userRegAide_registrationFields = $csds_userRegAide_registrationFields;
											update_option("csds_userRegAide_registrationFields", $csds_userRegAide_registrationFields);
										}															
									}
									foreach($csds_userRegAide_NewFields as $key2 => $value2){
										if($value == $key2){
											$csds_userRegAide_registrationFields[$key2] = $value2;
											$csds_userRegAide_registrationFields = $csds_userRegAide_registrationFields;
											update_option("csds_userRegAide_registrationFields", $csds_userRegAide_registrationFields);
										}		
									}
								}
							}else{
								$selected = '';
								$csds_userRegAide_registrationFields = array();
								update_option("csds_userRegAide_registrationFields", $csds_userRegAide_registrationFields);
							}
					}else{
						$selected = '';
						$csds_userRegAide_registrationFields = array();
						update_option("csds_userRegAide_registrationFields", $csds_userRegAide_registrationFields);
					}
					echo '<div id="message" class="updated fade"><p>'. __('Registration Form New Required Field Options updated successfully.', 'csds_userRegAide') .'</p></div>';					//Report to the user that the data has been updated successfully
				}else{
					echo '<div id="message" class="updated fade"><p>'. __('You do not have adequate permissions to edit this plugin! Please check with Administrator to get additional permissions.', 'csds_userRegAide') .'</p></div>';
					exit();
				}
				
		// Handles adding new fields to database for user profiles and registration
		
		}elseif (isset($_POST['field_update'])){
		
			// Checking for blank fields
		
			if($_POST['csds_userRegAide_newFieldKey'] == '' || $_POST['csds_userRegAide_newField'] == ''){
				if($_POST['csds_userRegAide_newFieldKey'] == '') {
					$csds_userRegMod_fields_missing1 = " New Field Key ";
				}elseif($_POST['csds_userRegAide_newField'] == ''){
					$csds_userRegMod_fields_missing2 = " New Field Name ";
				}elseif($_POST['csds_userRegAide_newFieldKey'] == '' && $_POST['csds_userRegAide_newField'] == ''){
					$csds_userRegMod_fields_missing1 = " New Field Key ";
					$csds_userRegMod_fields_missing2 = " New Field Name ";
					$seperator = " & ";
				}
				else{
					$seperator = " ";
				}
			$csds_userRegMod_fields_missing = $csds_userRegMod_fields_missing1 . $seperator . $csds_userRegMod_fields_missing2;
			echo '<div id="message" class="updated fade"><p>'. __('Options not updated successfully. Missing following: '.$csds_userRegMod_fields_missing,'csds_userRegAide') .'</p></div>';
			exit();
			}else{
				$results = esc_attr(stripslashes($_POST['csds_userRegAide_newFieldKey']));
				$new_field = esc_attr(stripslashes($_POST['csds_userRegAide_newField']));
				
					// Checking for duplicate field in new fields
				
					if(!empty($csds_userRegAide_NewFields)){
						foreach($csds_userRegAide_NewFields as $key => $field){
							if($key == $results){
								echo '<div id="message" class="updated fade"><p>'. __('Cannot add duplicate fields, '. $results . ' is already included in the extra fields!', 'csds_userRegAide') .'</p></div>';
								$csds_mainMenuErrors = TRUE;
								exit();
							}elseif($field == $new_field){
								echo '<div id="message" class="updated fade"><p>'. __('Cannot add duplicate fields, '. $new_field . ' is already included in the extra fields!', 'csds_userRegAide') .'</p></div>';
								$csds_mainMenuErrors = TRUE;
								exit();
							}else{
								$csds_mainMenuErrors = FALSE;
						    }
						}	
					}else{
					
						// Checking for duplicate fields in known fields
					
						foreach($csds_userRegAide_knownFields as $key => $field){
							if($key == $results){
								echo '<div id="message" class="updated fade"><p>'. __('Cannot add duplicate fields, '. $results . ' is already included in the extra fields!', 'csds_userRegAide') .'</p></div>';
								$csds_mainMenuErrors = TRUE;
								exit();
							}elseif($field == $new_field){
								echo '<div id="message" class="updated fade"><p>'. __('Cannot add duplicate fields, '. $new_field . ' is already included in the extra fields!', 'csds_userRegAide') .'</p></div>';
								$csds_mainMenuErrors = TRUE;
								exit();
							}else{
								$csds_mainMenuErrors = FALSE;
							}
						}
					}
					
				// Updating arrays and database options
					
				$csds_current_user = wp_get_current_user();
					if($csds_current_user->has_cap('edit_plugins')){
						csds_add_field_to_users_meta($results);
						$csds_userRegAide_NewFields[$results] = $new_field;
						$csds_userRegAideFields[$results] = $new_field;
						update_option("csds_userRegAide_NewFields", $csds_userRegAide_NewFields);
						update_option("csds_userRegAideFields", $csds_userRegAideFields);
						$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
						$cnt = count($csds_userRegAide_NewFields);
						$csds_userRegAide_fieldOrder[$results] = $cnt;
						update_option("csds_userRegAide_fieldOrder", $csds_userRegAide_fieldOrder);
						echo '<div id="message" class="updated fade"><p>Add New Fields Options updated successfully! </p></div>';
						// Displaying results of operation, whether successful or in error
						
						}else{
						echo '<div id="message" class="updated fade"><p>'?><?php _e('You do not have adequate permissions to edit this plugin! Please check with Administrator to get additional permissions.', 'csds_userRegAide') .'</p></div>';
						exit();
					}
				
				//if($csds_mainMenuErrors = FALSE){
					
				//}
				
			}
			
		// Handles showing support for plugin
		
		}elseif (isset($_POST['csds_userRegAide_support_submit'])){
			$csds_userRegAide_support = esc_attr(stripslashes($_POST['csds_userRegAide_support']));
			$csds_display_link = 'http://creative-software-design-solutions.com';
			$csds_display_name = 'Powered By Creative Software Design Solutions';
			update_option('csds_userRegAide_support', $csds_userRegAide_support );
			update_option("csds_display_link", $csds_display_link );
			update_option("csds_display_name", $csds_display_name );
			echo '<div id="message" class="updated fade"><p>'. __('Support Options updated successfully.', 'csds_userRegAide') .'</p></div>'; //Report to the user that the data has been updated successfully
		}else{
		
			// Just some stuff I put here for S&G not too much to worry about
			
			$csds_userRegAide_getOptions = get_option('csds_userRegAide');
			$csds_userRegAideFields_getOptions = get_option('csds_userRegAideFields');
			
		}
	
	// Makes sure that if upgrading, the new known fields are added
	
	if($csds_userRegAide_knownFields_count < 7){
		csds_userRegAide_fill_known_fields();
	}

	// Makes sure that the field order is updated before showing administration page
	
	
		if(!empty($csds_userRegAide_NewFields)){
			if(empty($csds_userRegAide_fieldOrder)){
				csds_userRegAide_update_field_order();
			}
		}
	
// Making sure all arrays for page are fully loaded from options before page loads
	
$csds_userRegAide_getOptions = get_option('csds_userRegAide');
$csds_userRegAideFields_getOptions = get_option('csds_userRegAideFields');
$csds_userRegAide_registrationFields = get_option('csds_userRegAide_registrationFields');
$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
$csds_userRegAide_fieldOrder = get_option('csds_userRegAide_fieldOrder');

if(!empty($csds_userRegAide_knownFields)){

// Shows Aministration Page 
		
echo '<div id="wpbody">';
	echo '<div class=wrap>';
		echo '<form method="post" name="csds_userRegAide">';
            echo '<h2>'. __('User Registration Aide: Force New User Registration Fields', 'csds_userRegAide') .'</h2>';
            echo '<div id="poststuff">';
			
				//Form for selecting fields to add to registration form
				
                echo '<div class="stuffbox">';
                echo '<h3>'. __('Add Additional Fields to Registration Form', 'csds_userRegAide') .'</h3>';
                    echo '<div class="inside">';
						echo '<p>' . __('By default, Wordpress will only require an email address and username to register an account. Here, you can select additional fields required for registration.', 'csds_userRegAide') .'</p>';
						echo '<select name="additionalFields[]" id="csds_userRegMod_Select" size="8" multiple style="height:100px">';
						$csds_userRegAide_registrationFields = get_option('csds_userRegAide_registrationFields');
						$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
						$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
						$regFields = $csds_userRegAide_registrationFields;
						if(!empty($csds_userRegAide_registrationFields)){
							if(is_array($regFields)){
								foreach($csds_userRegAide_knownFields as $key1 => $value1){
									if(in_array("$value1",$regFields)){
										$selected = "selected=\"selected\"";
									}else{
										$selected = NULL;
									}
								echo "<option value=\"$key1\" $selected >$value1</option>";
								}
								
								foreach($csds_userRegAide_NewFields as $key2 => $value2){
									if(in_array("$value2",$regFields)){
										$selected = "selected=\"selected\"";
									}else{
										$selected = NULL;
									}
								echo "<option value=\"$key2\" $selected >$value2</option>";
								}
							}else{
								exit();
							}
						}else{
							foreach($csds_userRegAide_knownFields as $key1 => $value1){
								if(in_array("$value1",$regFields)){
									$selected = "selected=\"selected\"";
								}else{
									$selected = NULL;
								}
							echo "<option value=\"$key1\" $selected >$value1</option>";
							}
							
							foreach($csds_userRegAide_NewFields as $key2 => $value2){
								if(in_array("$value2",$regFields)){
									$selected = "selected=\"selected\"";
								}else{
									$selected = NULL;
								}
							echo "<option value=\"$key2\" $selected >$value2</option>";
							}
						}
							                        
						echo '</select>';
						echo '<p>'. __('Hold "Ctrl" to select multiple options', 'csds_userRegAide') .'</p>';
						echo '<p>';
						?>
						<input type="button" name="selectNone" value="<?php _e('Select None', 'csds_userRegAide'); ?>" onClick="document.getElementById('csds_userRegMod_Select').options.selectedIndex=-1;"/>
						</p>	
						<div class="submit"><input type="submit" name="info_update" value="<?php _e('Update Options', 'csds_userRegAide'); ?>"  /></div>
					</div>
				</div>
				
<?php			// Form for adding new fields for users profile and registration 

				echo '<div class="stuffbox">'; ?>
				<h3><?php _e('Add New Field', 'csds_userRegAide');?></h3>
				<?php
					echo '<div class="inside">';
					echo '<br/>';
					echo '<table border="5" cellpadding="5" cellspacing="5">';
					echo '<tr>';
					echo '<td>'. __('Field Key Name : ', 'csds_userRegAide') .'<input  style="width: 150px;" type="text" title="Enter the database name for your field here, like dob for Date of Birth or full_name, use lower case letters and _ (underscores) ONLY! Keep it short and simple and relative to the field you are creating!" value="';
					echo $csds_userRegAide_newFieldKey . '" name="csds_userRegAide_newFieldKey" id="csds_userRegAide_newFieldKey" /></td>';
					echo '<td>'. __('Field Name : ', 'csds_userRegAide').'<input  style="width: 150px;" type="text" title="Enter the user friendly name for your field here, like Date of Birth for dob, ect. Keep it short & simple and relative to the field you are creating!" value="';
					echo $csds_userRegAide_newField . '" name="csds_userRegAide_newField" id="csds_userRegAide_newField" /></td>';
					echo '</tr>';
					echo '</table>';
					echo '<br/>';
					echo '<input type="submit" name="field_update" value="'. __('Add Field', 'csds_userRegAide').'" />';
					echo '</div>';
				echo '</div>';
				
				//show support section
				
				echo '<div class="stuffbox">';
				echo '<h3><a href="http://creative-software-design-solutions.com" target="_blank">Creative Software Design Solutions</a></h3>';
					echo '<div class="inside">';
						
						?>
						<p><?php _e('Show Plugin Support: ', 'csds_userRegAide'); ?><input type="radio" id="csds_userRegAide_support" name="csds_userRegAide_support"  value="1" <?php
							if ($csds_userRegAide_support == 1) echo 'checked' ;?> /> Yes
							<input type="radio" id="csds_userRegAide_support" name="csds_userRegAide_support"  value="2" <?php
							if ($csds_userRegAide_support == 2) echo 'checked' ; ?> /> No
						<br/>
						<br/>

						<?php
						echo '<input name="csds_userRegAide_support_submit" id="csds_userRegAide_support_submit" lang="publish" class="button-primary" value="Update" type="Submit" />';
									
						?>
						<h2><?php _e('Plugin Configuration Help', 'csds_userRegAide'); ?></h2>
						<ul>
							<li><a href="http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/" target="_blank">Plugin Page & Screenshots</a></li>
						</ul>
						<h2><?php _e('Check Official Website', 'csds_userRegAide'); ?></h2>
						<ul>
							<li><a href="http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/" target="_blank">Check official website for live demo</a></li>
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
								<input type="hidden" name="cmd" value="_s-xclick">
								<input type="hidden" name="hosted_button_id" value="6BCZESUXLS9NN">
								<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
								<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
								</form>
						</ul>
						<?php
						
					echo '</div>';
				echo '</div>';
			echo '</div>';   
		echo '</form>';
	echo '</div>';
echo '</div>';
	 
}else{
	csds_userRegAide_fill_known_fields();
}
}

?>