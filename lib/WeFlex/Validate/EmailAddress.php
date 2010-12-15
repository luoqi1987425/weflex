<?php 
require_once 'Interface.php';

	class WeFlex_Validate_EmailAddress implements WeFlex_Validate_Interface 
	{
	
		/**
		 * @see WeFlex_Validate_Interface::isValid()
		 *
		 * @param unknown_type $value
		 */
		public function isValid($value) {
			
			if (preg_match('/^[a-zA-Z0-9_-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/', $value)) 
			{
	            return true;
	        }
	        return false;
		}

	}
?>