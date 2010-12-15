<?php 
	
	class WeFlex_Api_Paypal_Nvp{
		
		const API_ENDPOINT_DEVELOPMENT 	  = "https://api-3t.sandbox.paypal.com/nvp";
		const URL_PAYPAL_DEVELOPMENT	  = "https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=";
		
		
		const API_ENDPOINT_PRODUCTION = "https://api-3t.paypal.com/nvp";
		const URL_PAYPAL_PRODUCTION	  = "https://www.paypal.com/webscr&cmd=_express-checkout&token=";
		
		const VERION				  = "64.0";
		
		
		private $_username;
		
		private $_password;   
		
		private $_signature;

		private $_apiEndPoint;
		
		private $_urlPaypol;
		
		private $_isProxy;
		
		private $_subject;
		
		private $_token;
		
		public function __construct( $username , $password , $signature , $evn = "production" , $subject = '' ){
			
			$this->_username = $username;
			$this->_password = $password;
			$this->_signature = $signature;
			
			switch( $evn ){
				
				case "production":
					$this->_apiEndPoint = self::API_ENDPOINT_PRODUCTION;
					$this->_urlPaypol   = self::URL_PAYPAL_PRODUCTION;
					break;
				case "development":
					$this->_apiEndPoint = self::API_ENDPOINT_DEVELOPMENT;
					$this->_urlPaypol   = self::URL_PAYPAL_DEVELOPMENT;
					
					break;
				default:
					throw new Exception("Nvp no evn");
				
			}
			
			
			
		}
		
		public function setExpressCheckout( array $nvp ){
			
			$nvpstr = $this->_nvpToString($nvp);
			
			$rtn = $this->hashCall( "SetExpressCheckout" , $nvpstr );
			
			
			$ack = strtoupper($rtn["ACK"]);
			
			if($ack=="SUCCESS"){
					// Redirect to paypal.com here
					$token = urldecode($rtn["TOKEN"]);
					$payPalURL = $this->_urlPaypol.$token;
					header("Location: ".$payPalURL);
					exit();
			} else  {
					 throw new Exception($rtn['L_LONGMESSAGE0']);
			}
			
		}
		
		public function doExpressCheckoutPayment( $token , $payerId  , $paymentType , $currencyCodeType , $paymentAmount ){
			
			$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerId.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currencyCodeType.'&IPADDRESS='.urlencode($_SERVER['SERVER_NAME']) ;
			$rtn = $this->hashCall( "DoExpressCheckoutPayment" , $nvpstr );
		
			$ack = strtoupper($rtn["ACK"]);
			
			if($ack != 'SUCCESS' && $ack != 'SUCCESSWITHWARNING'){
				
				throw new Exception($rtn['L_LONGMESSAGE0']);
        	}
        	
        	return $rtn;
		}
		
		
		
		public function hashCall( $methodName , $nvpStr ){
			
			//add header.
			$nvpHeader = "&PWD=".urlencode($this->_password)."&USER=".urlencode($this->_username)."&SIGNATURE=".urlencode($this->_signature)."&SUBJECT=".urlencode($this->_subject);
			$nvpStr = $nvpHeader . $nvpStr;
			
			//declaring of global variables
			//global $API_Endpoint,$version,$API_UserName,$API_Password,$API_Signature,$nvp_Header, $subject;
		
			//setting the curl parameters.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$this->_apiEndPoint);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
			//turning off the server and peer verification(TrustManager Concept).
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POST, 1);
		    //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
		   //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
			if($this->_isProxy)
				curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT); 
				//check if version is included in $nvpStr else include the version.
				if(strlen(str_replace('VERSION=', '', strtoupper($nvpStr))) == strlen($nvpStr)) {
					$nvpStr = "&VERSION=" . urlencode(self::VERION) . $nvpStr;	
			}
			
			$nvpreq="METHOD=".urlencode($methodName).$nvpStr;
			
			
		
			
			//setting the nvpreq as POST FIELD to curl
			curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);
		
			//getting response from server
			$response = curl_exec($ch);
		
			//convrting NVPResponse to an Associative Array
			$nvpResArray=$this->_deformatNVP($response);
			$nvpReqArray=$this->_deformatNVP($nvpreq);
			$_SESSION['nvpReqArray']=$nvpReqArray;
		  
			if (curl_errno($ch)) {
				// moving to display page to display curl errors
//				  $_SESSION['curl_error_no']=curl_errno($ch) ;
//				  $_SESSION['curl_error_msg']=curl_error($ch);
//				  $location = "APIError.php";
//				  header("Location: $location");
				  throw new Exception(curl_error($ch));
			 } else {
				 //closing the curl
					curl_close($ch);
			  }
		
			return $nvpResArray;
			
		}
		
		private function _deformatNVP($nvpstr)
		{
		
			$intial=0;
		 	$nvpArray = array();
		
		
			while(strlen($nvpstr)){
				//postion of Key
				$keypos= strpos($nvpstr,'=');
				//position of value
				$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
		
				/*getting the Key and Value values and storing in a Associative Array*/
				$keyval=substr($nvpstr,$intial,$keypos);
				$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
				//decoding the respose
				$nvpArray[urldecode($keyval)] =urldecode( $valval);
				$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
		     }
			return $nvpArray;
		}
		
		private function _nvpToString( array $nvpArray ){
			
			$rtn = '';
			
			foreach( $nvpArray as $key => $value ){
				
				$rtn .= "&".$key.'='.$value;
				
			}
			
			return $rtn;
			
		}
		
	}

?>