<?php 
require_once 'Validate/EmailAddress.php';
/**
 * Data validator , To use this Module , 
 * We can check this data is formatted correctly , 
 * for example , we can check if ¡°luoqi1987425@ddd¡± is a email format data , 
 * of course this is not , so it will be give us a false response.
 *
 */
	class WeFlex_Validate
	{
		
		const EmailAddress	=	'email_address';
		
		
		/**
		 * Validate the object is the type
		 * Example :
		 * //will return true
		 * WeFlex_Validate::IsValid( WeFlex_Validate::EmailAddress , 'rocky@gmail.com' );
		 * 
		 *
		 * @param String $type
		 * @param Object $object
		 * @return boolean
		 */
		public static function IsValid( $type , $object ){
			
			$validator = self::GetValidator( $type );
			return $validator->isValid( $object );
			
		}
		
		/**
		 * according to the type , Generate this type's validator.
		 *
		 * @param String $type
		 * @return WeFlex_Validate_Interface
		 */
		public static function GetValidator( $type ){
			
			switch( $type ){
				case self::EmailAddress :
					return new WeFlex_Validate_EmailAddress();
				default:
					throw new Exception( 'not exist validator' );
			}
			
		}
		
	}
?>