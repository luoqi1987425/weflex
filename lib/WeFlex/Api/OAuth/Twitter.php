<?php
require_once 'Abstract.php';

	class WeFlex_Api_OAuth_Twitter extends WeFlex_Api_OAuth_Abstract{
		
		const URI_USER_STATUS = 'http://api.twitter.com/1/statuses/user_timeline.xml';
		
		
		protected $_requestTokenUrl = 'https://api.twitter.com/oauth/request_token';
		
		protected $_authorizeUrl	= 'https://api.twitter.com/oauth/authorize';
		
		protected $_accessTokenUrl  = 'https://api.twitter.com/oauth/access_token';
		
		
		public function getUserStatus( $count ){
			return $this->_getUriContent( self::URI_USER_STATUS . '?count='.$count );	
		}
		
		public function search( $query , $params = null ){
			
		}
	
	
		protected function _checkResponseExpiration($responseBody) {
		
//			if( isset( $responseBody['error-code'] )  ){
//				throw new Exception( $responseBody->message );
//			}
			
			return true;
			
		}

		
		
		
		
		
	}
?>