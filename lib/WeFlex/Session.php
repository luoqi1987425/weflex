<?php
require_once 'Zend/Session/Namespace.php';
/**
 * We can save and get Session data for customer using module.  
 * Session data is saved in server for temporary.
 *
 */
	class WeFlex_Session
	{
		
		
		/**
		 * Set the $object into session using the namespace, 
		 * Then save it into session.
		 * Example:
		 * WeFlex_Session::Set( 'rocky' , new Object() );
		 *
		 * @param String $namespace
		 * @param Object $object
		 * @throw Exception( 'Session could not save object , please serialize it' );
		 */
		public static function Set( $namespace , $object ){
			
			
			
			if( is_object( $object ) ){
				throw new Exception( 'Session could not save object , please serialize it'  );
			}
			
			$zendSession = new Zend_Session_Namespace( $namespace );
			$zendSession->default = $object;
			
			
		}
		

		
		
		/**
		 * Enter description here...
		 *
		 * @param String $namespace
		 * @return int | String
		 */
		public static function Get( $namespace ){
			$zendSession = new Zend_Session_Namespace( $namespace );
			if( isset( $zendSession->default ) ){
				$object = $zendSession->default;
			}else{
				$object = null;
			}
			
			return $object;
			
		}
		
		public static function Remove( $namespace ){
			
			$zendSession = new Zend_Session_Namespace( $namespace );
			@$zendSession->__unset();
			
		}
		

		/**
		 * Enter description here...
		 *
		 * @param String $namespace
		 * @return boolean
		 */
		public static function IsExist( $namespace ){
			
			$zendSession = new Zend_Session_Namespace( $namespace );
			
			if( isset( $zendSession->default ) ){
				return true;
			}else{
				return false;
			}
			
		}
		
		
		
	}
?>