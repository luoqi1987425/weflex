<?php 
require_once 'Zend/Json.php';
/**
 * Json data format translator , We can translate the data formatted by xml and array to json format , 
 * and translate json format data to string. 
 *
 */
	class WeFlex_Json
	{
		
		
		/**
		 * Decode Json to Array
		 * Example:
		 * $json = '{ a:1 , b:2 , c:3 }';
		   $json_array = WeFlex_Json::Decode( $json );
		   $json_array is like array( 'a' => 1 , 'b' => 2 , 'c' => 3 );
		 *
		 * @param String $json
		 * @return array
		 */
		public static function Decode( $json ){
			
			return Zend_Json::decode( $json );
			
			
		}
		
		/**
		 * Encode Json From array
		 * 
		 * @param array $array
		 * @return String
		 */
		public static function EncodeArray( $array ){
			return Zend_Json::encode( $array );
		}
		
		/**
		 * Encode Json From xml
		 *
		 * @param String $xml
		 * @return String
		 */
		public static function EncodeXml( $xml ){
			return Zend_Json::fromXml( $xml );
		}
		
		
	}
?>