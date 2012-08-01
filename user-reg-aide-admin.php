<?php

/*
 * 	User Registration Aide
 * Plugin URI: http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/
 * Plugin Description: Forces new users to register additional fields with the option to add additional fields other than those 
 * supplied with the default Wordpress Installation. We have kept it simple in this version for those of you whom aren't familiar with 
 * handling multiple users or websites. We also are currently working on expanding this project with a paid version which will contain 
 * alot more features and options for those of you who wish to get more control over users and user access to your site.
 * Version: 1.0.1
 * Author: Brian Novotny
 * Author URI: http://creative-software-design-solutions.com/
*/

/**
 * Fills array of known fields
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function fill_known_fields()
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
	
	csds_userRegAide_myOptionsSubpanel();
	
}

/**
 * Loads and displays the User Registration Aide administration page
 *
 * @since 1.0.0
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

function csds_userRegAide_myOptionsSubpanel(){

global $csds_userRegAide_knownFields, $seperator, $csds_userRegAide_getOptions, $csds_userRegAide_NewFields, $csds_userRegAideFields_getOptions, $csds_userRegMod_fields_missing, $csds_userRegMod_fields_missing1, $csds_userRegMod_fields_missing2;
	
	$csds_userRegAideFields = get_option('csds_userRegAideFields');
	$csds_userRegAide_getOptions = get_option('csds_userRegAide');
	$csds_userRegAide_knownFields = get_option('csds_userRegAide_knownFields');
	$csds_userRegAide_NewFields = get_option('csds_userRegAide_NewFields');
	$csds_userRegAide_support = get_option('csds_userRegAide_support');
		if (isset($_POST['info_update']))
		{
			$results = array(	"additionalFields" => $_POST['additionalFields']);
			update_option("csds_userRegAide", $results);
			echo '<div id="message" class="updated fade"><p>'. __('Registration Form New Required Field Options updated successfully.', 'csds_userRegAide') .'</p></div>';					//Report to the user that the data has been updated successfully
		}
		
		else
		{
		
		}
		
		if (isset($_POST['field_update']))
		{
			if($_POST['csds_userRegAide_newFieldKey'] == '' || $_POST['csds_userRegAide_newField'] == ''){
				if($_POST['csds_userRegAide_newFieldKey'] == '') {
					$csds_userRegMod_fields_missing1 = " New Field Key ";
				}
				else{
					
				}
				if($_POST['csds_userRegAide_newField'] == ''){
					$csds_userRegMod_fields_missing2 = " New Field Name ";
				}
				else{
					
				}
				if($_POST['csds_userRegAide_newFieldKey'] == '' && $_POST['csds_userRegAide_newField'] == ''){
					$seperator = " & ";
				}
				else{
					$seperator = " ";
				}
				$csds_userRegMod_fields_missing = $csds_userRegMod_fields_missing1 . $seperator . $csds_userRegMod_fields_missing2;
			echo '<div id="message" class="updated fade"><p>'. __('Options not updated successfully. Missing following: '.$csds_userRegMod_fields_missing,'csds_userRegAide') .'</p></div>';
			}
			else{
				$results = esc_attr(stripslashes($_POST['csds_userRegAide_newFieldKey']));
				csds_add_field_to_users_meta($results);
				$new_field = esc_attr(stripslashes($_POST['csds_userRegAide_newField']));
				update_option("csds_userRegAide_newField", $csds_userRegAide_newField );
				$csds_userRegAide_knownFields[$results] = $new_field;
				$csds_userRegAide_NewFields[$results] = $new_field;
				update_option("csds_userRegAide_NewFields", $csds_userRegAide_NewFields);
				update_option("csds_userRegAideFields", $csds_userRegAide_knownFields);
				update_option("csds_userRegAide_knownFields", $csds_userRegAide_knownFields);
				echo '<div id="message" class="updated fade"><p>'. __('Add New Fields Options updated successfully.', 'csds_userRegAide') .'</p></div>';
			}
				
		
		}
		
		else
		{
		
		}
		
		if (isset($_POST['csds_userRegAide_support_submit']))
		{
			$csds_userRegAide_support = esc_attr(stripslashes($_POST['csds_userRegAide_support']));
			$csds_display_link = 'http://creative-software-design-solutions.com';
			$csds_display_name = 'Powered By Creative Software Design Solutions';
			
			update_option('csds_userRegAide_support', $csds_userRegAide_support );
			update_option("csds_display_link", $csds_display_link );
			update_option("csds_display_name", $csds_display_name );
			echo '<div id="message" class="updated fade"><p>'. __('Support Options updated successfully.', 'csds_userRegAide') .'</p></div>';					//Report to the user that the data has been updated successfully
		}
		
		else
		{
		
		}
		
	$csds_userRegAide_getOptions = get_option('csds_userRegAide');
	$csds_userRegAideFields_getOptions = get_option('csds_userRegAideFields');

	if(!empty($csds_userRegAide_knownFields)){
		
	
echo '<div id="wpbody">';
	echo '<div class=wrap>';
		echo '<form method="post" name="csds_userRegAide">';
            echo '<h2>User Registration Aide: Force New User Registration Fields</h2>';
            echo '<div id="poststuff">';       
                echo '<div class="stuffbox">';
                    echo '<h3>Add Additional Fields to Registration Form</h3>';
                    echo '<div class="inside">';
                        
                    echo'<p>By default, Wordpress will only require an email address and username to register an account. Here, you can select additional fields required for registration.</p>';
    
                        echo '<select name="additionalFields[]" id="csds_userRegMod_Select" size="8" multiple style="height:100px">';
                       
                            foreach($csds_userRegAide_knownFields as $key => $value){
                                if(in_array("$key", $csds_userRegAide_getOptions["additionalFields"])){
                                    $selected = "selected=\"selected\"";
                                }
                                else{
                                    $selected = NULL;
                                }
                                echo "<option value=\"$key\" $selected >$value</option>\r\n";
                            }
							                        
						echo '</select>';
						
                    echo '<p>Hold "Ctrl" to select multiple options</p>';
					echo '<br/>';
						echo '<p>';
						?>
						<input type="button" name="selectNone" value="<?php _e('Select None', 'csds_userRegAide'); ?>" onClick="document.getElementById('csds_userRegMod_Select').options.selectedIndex=-1;"/>
						<?php
						echo '</p>';	
					   	echo '<div class="submit"><input type="submit" name="info_update" value="Update Options",'.csds_userRegAide.'  /></div>';
						echo '</div>';
					echo '</div>';
						echo '<div class="stuffbox">';
						echo '<h3>Add New Field</h3>';
						echo '<div class="inside">';
						echo '<p>Field Key Name : <input  style="width: 150px;" type="text" title="Enter the database name for your field here, like dob for Date of Birth or full_name, use lower case letters and _ (underscores) ONLY! Keep it short and simple and relative to the field you are creating!" value="';
						echo $csds_userRegAide_newFieldKey . '" name="csds_userRegAide_newFieldKey" id="csds_userRegAide_newFieldKey" /></p>';
						echo '<p>Field Name : <input  style="width: 150px;" type="text" title="Enter the user friendly name for your field here, like Date of Birth for dob, ect. Keep it short & simple and relative to the field you are creating!" value="';
						echo $csds_userRegAide_newField . '" name="csds_userRegAide_newField" id="csds_userRegAide_newField" /></p>';
						echo '<input type="submit" name="field_update" value="Add Field",'.csds_userRegAide.'  />';
						echo '</div>';
						echo '</div>';
						
						echo '<div class="stuffbox">';
						echo '<h3><a href="http://creative-software-design-solutions.com" target="_blank">Creative Software Design Solutions</a></h3>';
						echo '<div class="inside">';
						
						?>
						<p>Show Plugin Support: <input type="radio" id="csds_userRegAide_support" name="csds_userRegAide_support"  value="1" <?php
						if ($csds_userRegAide_support == 1) echo 'checked' ;?> /> Yes
						<input type="radio" id="csds_userRegAide_support" name="csds_userRegAide_support"  value="2" <?php
						if ($csds_userRegAide_support == 2) echo 'checked' ; ?> /> No
						<br/>
						<br/>

							<?php
								echo '<input name="csds_userRegAide_support_submit" id="csds_userRegAide_support_submit" lang="publish" class="button-primary" value="Update" type="Submit" />';
								
								?>
								<h2>Plugin configuration help</h2>
								<ul>
									<li><a href="http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/" target="_blank">Plugin Page & Screenshots</a></li>
								</ul>
								<h2>Check official website</h2>
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
	 }
	else{
		fill_known_fields();
	}
}

?>