<?php
require_once 'Abstract.php';

	class WeFlex_Api_OAuth_LinkedIn extends WeFlex_Api_OAuth_Abstract{
		
		const URI_USER_PROFILE = 'https://api.linkedin.com/v1/people/~';
		const URI_USER_CONNECTION = 'http://api.linkedin.com/v1/people/~/connections';
		
		
		protected $_requestTokenUrl = 'https://api.linkedin.com/uas/oauth/requestToken';
		
		protected $_authorizeUrl	= 'https://api.linkedin.com/uas/oauth/authorize';
		
		protected $_accessTokenUrl  = 'https://api.linkedin.com/uas/oauth/accessToken';
		
		
		public function getProfile( $fields = null ){
			$uri = $this->_addFields(self::URI_USER_PROFILE , $fields);
			return $this->_getUriContent( $uri );
			
		}
		
		public function getConnection( $fields = null ){
			$uri = $this->_addFields(self::URI_USER_CONNECTION , $fields);
			return $this->_getUriContent( $uri );
		}
		
		
	
	
		protected function _checkResponseExpiration($responseBody) {
		
			if( isset( $responseBody['error-code'] )  ){
				throw new Exception( $responseBody->message );
			}
			
			return true;
			
		}
		
		protected function _addFields( $uri , $fields ){
			
			if( $fields ){	
				if( is_string( $fields ) ){
					$fields = array( $fields );
				}
				
				$uri .= ':(';
				$uri .=	implode(",", $fields);
				$uri .= ')';
				
			}
			
			return $uri;
			
			
		}

		
		
		
		
		
	}
?>