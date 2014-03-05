<?php

# What to do if the class is being called directly and not being included in a script via PHP
# This allows the class/script to be called via other methods like JavaScript
if(basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])){
	$return_array = array();
	
	if($_GET['address_to_verify'] == '' || !isset($_GET['address_to_verify'])){
		$return_array['error'] 				= 1;
		$return_array['message'] 			= 'No email address was submitted for verification';
		$return_array['domain_verified'] 	= 0;
		$return_array['format_verified'] 	= 0;
	}else{
		$verify = new EmailVerify();
		
		if($verify->verify_formatting($_GET['address_to_verify'])){
			$return_array['format_verified'] 	= 1;
			
			if($verify->verify_domain($_GET['address_to_verify'])){
				$return_array['error'] 				= 0;
				$return_array['domain_verified'] 	= 1;
				$return_array['message'] 			= 'Formatting and domain have been verified';
			}else{
				$return_array['error'] 				= 1;
				$return_array['domain_verified'] 	= 0;
				$return_array['message'] 			= 'Formatting was verified, but verification of the domain has failed';
			}
		}else{
			$return_array['error'] 				= 1;
			$return_array['domain_verified'] 	= 0;
			$return_array['format_verified'] 	= 0;
			$return_array['message'] 			= 'Email was not formatted correctly';
		}
	}
	
	echo json_encode($return_array);
	
	exit();
}

class EmailVerify {
	public function __construct(){
		
	}
	
	public function verify_domain($address_to_verify){
		// an optional sender  
		$record = 'MX';
		list($user, $domain) = explode('@', $address_to_verify);
		return checkdnsrr($domain, $record);
	}
	
	public function verify_formatting($address_to_verify){
		if(strstr($address_to_verify, "@") == FALSE){
			return false;
		}else{
			list($user, $domain) = explode('@', $address_to_verify);
			
			if(strstr($domain, '.') == FALSE){
				return false;
			}else{
				return true;
			}
		}
	}
}
?>
