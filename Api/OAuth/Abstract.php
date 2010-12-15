<?php 
	abstract class WeFlex_Api_OAuth_Abstract{
		
		
		const SESSION_REQUEST_TOKEN = 'weflex-request-token';
		const SESSION_ACCESS_TOKEN	= 'weflex-access-token';
		
		protected $_config;
		
		/**
		 * @var Zend_Oauth_Consumer
		 */
		protected $_consumer;
		
		/**
		 * @var Zend_Oauth_Token_Access
		 */
		protected $_accessToken;
		
		/**
		 * @var Zend_Oauth_Client
		 */
		protected $_client;
		
		protected $_requestScheme   = Zend_Oauth::REQUEST_SCHEME_HEADER;
		
		protected $_version 		= '1.0';

		protected $_signatureMethod = 'HMAC-SHA1';
		
		protected $_requestTokenUrl;
		
		protected $_authorizeUrl;
		
		protected $_accessTokenUrl;
		
		
		function __construct( $consumerKey , $consumerSecret , $callback , $accessToken = null ){
			
			$this->_initConfig( $consumerKey , $consumerSecret , $callback );
			$this->_consumer = new Zend_Oauth_Consumer($this->_config);
			$this->_accessToken = $accessToken;
			
		}
		
		
		/**
		 * request a token
		 *
		 */	
		public function requestAuthority(){
				 
			
			$token = $this->_consumer->getRequestToken();
			
		    WeFlex_Session::Set( self::SESSION_REQUEST_TOKEN , serialize( $token ) );
		    $this->_consumer->redirect(); 
			
		}
		
		/**
		 * if user accept, it will request a access token
		 *
		 */
		public function afterAuthority(){
			
	
			$this->_accessToken = $this->_consumer->getAccessToken($_GET, unserialize(WeFlex_Session::Get(self::SESSION_REQUEST_TOKEN)));
			WeFlex_Session::Set(self::SESSION_ACCESS_TOKEN , serialize($this->_accessToken));
			return $this->_accessToken;
		}
		
		protected function _initConfig( $consumerKey , $consumerSecret , $callback ){
			
			if( !$consumerKey || 
			    !$consumerSecret ||
			    !$callback ||
			    !$this->_requestTokenUrl ||
			    !$this->_authorizeUrl ||
			    !$this->_accessTokenUrl ){
			    	
			    	throw new Exception( 'oauth config is not full' );
			    }
			
			$config = array();
			$config['requestScheme'] = $this->_requestScheme;
			$config['version']		 = $this->_version;
			$config['signatureMethod'] = $this->_signatureMethod;
			$config['callbackUrl']	   = $callback;
			
			
			
			$config[ 'requestTokenUrl' ] = $this->_requestTokenUrl;
			$config[ 'authorizeUrl' ] 	 = $this->_authorizeUrl;
			$config[ 'accessTokenUrl' ]  = $this->_accessTokenUrl;
			$config[ 'consumerKey' ] 	 =  $consumerKey;
			$config[ 'consumerSecret' ]  = $consumerSecret;
			
			$this->_config = $config;
		}
		
		/**
		 * 
		 * @return Zend_Oauth_Client
		 *
		 */
		protected function _getClient(){
			
			if( !$this->_client ){
				
				$accessToken = $this->_getAccessToken();
				$this->_client = $accessToken->getHttpClient( $this->_config );
			}
			
			return $this->_client;
			
		}
		
		/**
		 * @return Zend_Oauth_Token_Access
		 */
		protected function _getAccessToken(){
			
			if( WeFlex_Session::Get(self::SESSION_ACCESS_TOKEN) && !$this->_accessToken ){
				$this->_accessToken = unserialize( WeFlex_Session::Get(self::SESSION_ACCESS_TOKEN) );
			}
			
			
			return $this->_accessToken;
		}
		
		protected function _getUriContent( $uri , $method = null ){
			
			if( !$method ){
				$method = Zend_Http_Client::GET;
			}
			
			$client = $this->_getClient();
			
			$client->setUri($uri);
			$client->setMethod($method);
			$response = $client->request()->getBody();
			$data = new SimpleXMLElement($response);
			$responseArray = $this->_simplexml2array($data);
			
			$this->_checkResponseExpiration( $responseArray );
			
			return $responseArray;
			
		}
		
		/**
		 * Convert SimpleXMLElement object to array
		 * Copyright Daniel FAIVRE 2005 - www.geomaticien.com
		 * Copyleft GPL license
		 */
		protected function _simplexml2array($xml) {
		   if ( @get_class($xml) == 'SimpleXMLElement') {
		       $attributes = $xml->attributes();
		       foreach($attributes as $k=>$v) {
		           if ($v) $a[$k] = (string) $v;
		       }
		       $x = $xml;
		       $xml = get_object_vars($xml);
		   }
		   if (is_array($xml)) {
		       if (count($xml) == 0) return (string) $x; // for CDATA
		       foreach($xml as $key=>$value) {
		           $r[$key] = $this->_simplexml2array($value);
		       }
		       if (isset($a)) $r['@'] = $a;    // Attributes
		       return $r;
		   }
		   return (string) $xml;
		}
		
		/**
		 * phrase the response to check if the access token is expiration 
		 * each external website may have different check method
		 *
		 * @param String $responseBody
		 * @return boolean
		 */
		abstract protected function _checkResponseExpiration( $responseBody ); 
		
		
		
	}
?>