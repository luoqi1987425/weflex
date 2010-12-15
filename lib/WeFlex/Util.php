<?php 
	class WeFlex_Util
	{
		
		/**
		 * get a image by it's size
		 * if url is entire url we will use it. if not we will use view baseUrl
		 * 
		 * @return String
		 */
		public static function GetImageUrlBySize($surl,$width = null ,$height = null) {
	        
			$isExternal = self::_IsExternalUrl( $surl );
			
			if( $isExternal ){
				$s3 		= Zend_Registry::get( 'magzine-s3' );
				$bucket 	= WeFlex_Application::GetInstance()->config->api->amazon->s3->bucket->issue;	 
				$lifetime 	= WeFlex_Application::GetInstance()->config->api->amazon->s3->lifetime;
				
				if( $width ){
					$durl = substr($surl,0,strrpos($surl,'.')).'_'.$width . 'x' . $height . substr($surl,strrpos($surl,'.'));
				}else{
					$durl = $surl;
				}
				//$durl = $s3->getAuthenticatedURL( $bucket, $durl, $lifetime , false , true );
				$durl = "http://" .  $bucket . ".s3.amazonaws.com/" . $durl ;
			}else{
				$view = Zend_Registry::get( 'view' );
				if( $width ){
					$durl = substr($surl,0,strrpos($surl,'.')).'_'.$width . 'x' . $height . substr($surl,strrpos($surl,'.'));
				}else{
					$durl = $surl;
				}
				
				$durl = $view->baseUrl() . $durl;
			}
			
	        return $durl;
    	}
    	
    	/**
    	 * only gener the size picture no intellgcy detect
    	 * 
    	 * @param unknown_type $surl
    	 * @param unknown_type $width
    	 * @param unknown_type $height
    	 */
    	public static function GenerImageUrlBySize($surl,$width ,$height){
    		$durl = substr($surl,0,strrpos($surl,'.')).'_'.$width . 'x' . $height . substr($surl,strrpos($surl,'.'));
    		return $durl;
    	}
    	
    	public static function GetFileName( $url ){
    		
    		$fileName = substr($url,strrpos($url,'/'));
			$fileName = substr($fileName,1);
			return $fileName;
    		
    	}
    	
		public static function GetFileFormat( $url ){
    		
    		$fileName = substr($url,strrpos($url,'.'));
			$fileName = substr($fileName,1);
			return $fileName;
    		
    	}
    	
    	
    	public static function GetIp(){
    		
    		if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) 
			{ 
				$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]; 
			} 
			elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) 
			{ 
				$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"]; 
			}
			elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]) 
			{ 
				$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"]; 
			} 
			elseif (getenv("HTTP_X_FORWARDED_FOR")) 
			{ 
				$ip = getenv("HTTP_X_FORWARDED_FOR"); 
			} 
			elseif (getenv("HTTP_CLIENT_IP")) 
			{ 
				$ip = getenv("HTTP_CLIENT_IP"); 
			} 
			elseif (getenv("REMOTE_ADDR"))
			{ 
				$ip = getenv("REMOTE_ADDR"); 
			} 
			else 
			{ 
				$ip = "Unknown"; 
			}

			return $ip;
    		
    	}
    	
    	public static function GetEurPriceFormat( $price ){
    		
    		if(strstr($price , '.'  )){
    			$rtn = str_replace(".", ",", $price);
    			$length = strlen(substr(strrchr( $rtn, "," ) , 1));
    			if( $length < 2 ){
    				
    				$rtn .= '0';
    			}elseif( $length > 2 ){
    				$rtn = substr($rtn, 0, -( $length -2 ));
    			}
    		}
    		else{
    			$rtn = $price . ',00';
    		}
    		
    		return $rtn;
    		
    		
    	}
    	
		public static function ConvertPrice( $price ){
    		
    		$rtn = str_replace(",", ".", $price);
    	
    		return $rtn;
    		
    		
    	}
    	
    	public static function GetDomain(){
    		
    		return 'http://' . $_SERVER['SERVER_NAME'];
    	}
    	
    	public static function GetFullUrl( $param , $router = 'default' ){
    		
    		$view 	 = Zend_Registry::get( "view" );
    		$domain  = self::GetDomain();
    		$url 	 = $view->url( $param , $router , true);
    		
    		return $domain . $url;
    		
    	}
    	
    	public static function GetFullBaseUrl(){
    		
    		$view 	 = Zend_Registry::get( "view" );
    		return 'http://' . $_SERVER['SERVER_NAME']  . $view->baseUrl();
    		
    	}
    	
    	public static function GenerNameForSEO( $name ){
    		
    		$output = preg_replace('/[^A-Za-z0-9\s]/','', $name);
    		$output = trim( $output );
    		$output = preg_replace('/[\s]+/','-', $output);
			 
			return $output;
    		
    	}

		public static function GenerNameForCacheKey( $name ){
    		
    		$output = preg_replace('/[^A-Za-z0-9\s_]/','', $name);
    		$output = trim( $output );
    		$output = preg_replace('/[\s]+/','_', $output);
			 
			return $output;
    		
    	}
    	
    	
		public static function MkDir( $dirPath ){			
			if( !is_dir( $dirPath ) ){
				mkdir($dirPath,0777,true);
        		@chmod($dirPath,0777);
			}
		}
		
		public static function Copy( $orginalFile , $toFile ){
			copy($orginalFile,$toFile);
	        @chmod($toFile,0777);
		}
		
		/**
		 * 将字符串包含 地址信息转换成 A
		 *
		 * @param unknown_type $str
		 * @return unknown
		 */
		public static function ConvertStrUrlLink( $str ){
			$str = preg_replace_callback("|http://[^\s]+|" , "url_preg_replace_callback", $str);
			$str = preg_replace_callback("|https://[^\s]+|", "url_preg_replace_callback", $str);
			return $str;
		}
		
	  private static function _IsExternalUrl( $url ){
    	
	  	//hack
    	if(preg_match("/^\/upload\//" ,$url)){
			return false;
		}else{
			return true;
		}
    	
    }
		
	}
	
function url_preg_replace_callback($matches){
	
	return '<a target="_blank" href="'.$matches[0].'">'.$matches[0].'</a>';
	
}

?>