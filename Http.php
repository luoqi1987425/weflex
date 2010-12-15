<?php 
	class WeFlex_Http{
		
		public static function Get( $url ){
			
			// create curl resource 
	        $ch = curl_init(); 
	        // set url 
	        curl_setopt($ch, CURLOPT_URL, $url); 
	        //return the transfer as a string 
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	        // $output contains the output string 
	        $output = curl_exec($ch); 
	        // close curl resource to free up system resources 
	        curl_close($ch);      
	        
	        return $output;
		}
		
	}
?>