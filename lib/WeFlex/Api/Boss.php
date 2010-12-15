<?php 
	class WeFlex_Api_Boss{
		
		
		const IMAGES = 'images';
		const NEWS	 = 'news';
		const WEB	 = 'web';
		
		const SITES     = 'sites';
		const PAGE_NO   = 'pageno';
		const PAGE_SIZE = 'pagesize';
		const LANG		=	'lang';
		
		/** like us en jp */
		const REGION	=	'region';
		
		private $_appId;
		
		function __construct( $appId ){
			$this->_appId = $appId;
		}
		
		public function getResults( $section , $keyword , $params = null ){
			
			$url = sprintf(
				"http://boss.yahooapis.com/ysearch/%s/v1/%s?appid=%s&style=raw&strictlang=1&filter=-porn&format=json%s",
				$section,
				urlencode(utf8_encode("\"".$keyword."\"")),
				$this->_appId,
				$this->_parseParam( $params )
			);
			
			
			$json = $this->_getUrlContents($url);
			$array = Zend_Json::decode($json);
			
			$resultsetName = "resultset_".$section;
			
			if (!isset($array["ysearchresponse"][$resultsetName])) {
				return false; // no results
			} else {
				return $array["ysearchresponse"][$resultsetName];
			}
			
		}
		
		public function getWebResults( $key , $params = null ){
			
			return $this->getResults( self::WEB , $key , $params );
			
			
		}
		
		public function getImageResults( $key , $params = null ){
			
			return $this->getResults( self::IMAGES , $key , $params );
			
		}
		
		public function getNewsResults( $key , $params = null ){
			
			return $this->getResults( self::NEWS , $key , $params );
			
		}
		
		private function _getUrlContents($url, $timeout = 0) {
			$c = curl_init();
		
			curl_setopt($c, CURLOPT_URL, $url);
			curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($c, CURLOPT_MAXREDIRS, 5);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($c);
			curl_close($c);
			
			return $data;
		}
		
		private function _parseParam( $params ){
			
			$rtn = '';
			
			if( !$params ){
				return $rtn;
			}
			
			foreach( $params as $key => $value ){
				
				if( $key == self::PAGE_NO ){
					$start = ( intval( $value ) - 1 ) * intval( $params[self::PAGE_SIZE] );
					$rtn .= '&start='.$start;
				}else if( $key == self::PAGE_SIZE ){
					$rtn .= '&count='.$value;
				}
				else{
					$rtn .= '&'.$key.'='.$value;
				}
			}
			
			return $rtn;

			
		}
		
		
	}
?>