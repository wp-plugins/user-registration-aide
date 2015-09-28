<?php
/**
 * User Registration Aide - Math Functions
 * Plugin URI: http://creative-software-design-solutions.com/wordpress-user-registration-aide-force-add-new-user-fields-on-registration-form/
 * Version: 1.5.0.1
 * Since Version 1.3.6
 * Author: Brian Novotny
 * Author URI: http://creative-software-design-solutions.com/
*/


/**
 * URA MATH FUNCTIONS Class for handling Anti-Spammer Math Problems
 *
 * @category Class
 * @since 1.3.6
 * @updated 1.3.6
 * @access private
 * @author Brian Novotny
 * @website http://creative-software-design-solutions.com
*/

class URA_MATH_FUNCTIONS{

	
	
	

	public function __construct() {
		$this->URA_MATH_FUNCTIONS();
	}
	
	public function URA_MATH_FUNCTIONS(){
	
		
		
	}
	
	/**
	 * Creates random numbers for helping to minimize spammers on registration page
	 * @since 1.3.0
	 * @handles anti-spam on registration form if user checks anti-spammer option (line 230 &$this (add_fields))
	 * @returns array $ran_numbs
	 * @access public
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function random_numbers(){
		
		$options = array();
		$options = get_option('csds_userRegAide_Options');
		$cnt = (int) 0;
		$div = (boolean) false;
		$mult = (boolean) false;
		$subt = (boolean) false;
		$add = (boolean) false;
	
		// addition operator check
		if($options['addition_anti_spam'] == 1){
			$cnt ++;
			$add = true;
		}else{
			$add = false;
		}
		// subtraction operator check
		if($options['minus_anti_spam'] == 1){
			$cnt ++;
			$subt = true;
		}else{
			$subt = false;
		}
		// multiplication operator check
		if($options['multiply_anti_spam'] == 1){
			$cnt ++;
			$mult = true;
		}else{
			$mult = false;
		}
		//division operator check
		if($options['division_anti_spam'] == 1){
			$cnt ++;
			$div = true;
		}else{
			$div = false;
		}
		
		//random number variable range array
		$ran_numbs = array();
		$a1 = rand(1,50);
		$a2 = rand(1,50);
		$d1 = rand(20,50);
		$d2 = rand(2, 10);
		$s1 = rand(1,50);
		$s2 = rand(1,50);
		$m1 = rand(1,20);
		$m2 = rand(1,20);
		$o = rand(1,$cnt);
		
		if($o == 1 && $add == true){
			$ran_numbs = $this->addition($a1, $a2);
		}elseif($o == 1 && $add == false && $subt == true){
			$ran_numbs = $this->subtraction($s1, $s2);
		}elseif($o == 1 && $add == false && $subt == false && $mult == true){
			$ran_numbs = $this->multiplication($m1, $m2);
		}elseif($o == 1 && $add == false && $subt == false && $mult == false && $div == true){
			$ran_numbs = $this->division($d1, $d2);
		}elseif($o == 2 && $subt == true){
			$ran_numbs = $this->subtraction($s1, $s2);
		}elseif($o == 2 && $subt == false && $mult == true){
			$ran_numbs = $this->multiplication($m1, $m2);
		}elseif($o == 2 && $subt == false && $mult == false && $div == true){
			$ran_numbs = $this->division($d1, $d2);
		}elseif($o == 3 && $mult == true){
			$ran_numbs = $this->multiplication($m1, $m2);
		}elseif($o == 3 && $mult == false && $div == true){
			$ran_numbs = $this->division($d1, $d2);
		}elseif($o == 4 && $div == true){
			$ran_numbs = $this->division($d1, $d2);
		}
		unset( $options );
		// $temp_answer = $ran_numbs['first'] .' ' .$ran_numbs['operator'].' ' .$ran_numbs['second'];
		// $math_answer = round($temp_answer, 1);
		return $ran_numbs;
		
	}
	
	/**
	 * Handles addition problems for anti-spam math problem
	 *
	 * @category Class
	 * @since 1.3.6
	 * @updated 1.3.6
	 * @access public
	 * @accepts int $a1, $a2
	 * returns array $ran_numbs includes operator, first and second numbers  ($a1, $a2)
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function addition($a1, $a2){
		$ran_numbs = array();
		$ran_numbs['operator'] = '+';
		$ran_numbs['first'] = $a1;
		$ran_numbs['second'] = $a2;
		return $ran_numbs;
	}
	
	/**
	 * Handles subtraction problems for anti-spam math problem
	 *
	 * @category Class
	 * @since 1.3.6
	 * @updated 1.3.6
	 * @access public
	 * @accepts int $s1, $s2
	 * returns array $ran_numbs includes operator, first and second numbers ($s1, $s2)
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function subtraction($s1, $s2){
		$ran_numbs = array();
		$ran_numbs['operator'] = '-';
		if($s2 < $s1){
			$ran_numbs['first'] = $s1;
			$ran_numbs['second'] = $s2;
		}else{
			$ran_numbs['first'] = $s2;
			$ran_numbs['second'] = $s1;
		}
		return $ran_numbs;
	}
	
	/**
	 * Handles multiplication problems for anti-spam math problem
	 *
	 * @category Class
	 * @since 1.3.6
	 * @updated 1.3.6
	 * @access public
	 * @accepts int $m1, $m2
	 * returns array $ran_numbs includes operator, first and second numbers ($m1, $m2)
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function multiplication($m1, $m2){
		$ran_numbs = array();
		$ran_numbs['operator'] = '*';
		$ran_numbs['first'] = $m1;
		$ran_numbs['second'] = $m2;
		return $ran_numbs;
	}
	
	/**
	 * Handles division problems for anti-spam math problem
	 *
	 * @category Class
	 * @since 1.3.6
	 * @updated 1.3.6
	 * @access public
	 * @accepts int $d1, $d2
	 * returns array $ran_numbs includes operator, first and second numbers ($d1, $d2)
	 * @author Brian Novotny
	 * @website http://creative-software-design-solutions.com
	*/
	
	function division($d1, $d2){
		$ran_numbs = array();
		$ran_numbs['operator'] = '/';
		$ran_numbs['first'] = $d1;
		$ran_numbs['second'] = $d2;
		return $ran_numbs;
	}
} // end class