<?php 
	class WeFlex_Entity_Lang_Helper{
		
		
		
		public static function ToLangArrayFormat( $source ){
			
			$rtn = array();
			$rtn['langInfos'] = array();
			$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
			
			foreach( $langs as $lang ){
				$rtn['langInfos'][$lang] = array();
			}
			
			foreach( $source as $key => $value ){
				
				if( self::IsLangFieldName( $key ) ){
					$lang = self::GetLangFieldLang( $key );
					$fileName = self::GetLangFieldName( $key );
					$rtn['langInfos'][$lang][$fileName] = $value;
				}else{
					$rtn[$key] = $value;
				}
			}
			
			return $rtn;
			
			
		}
		
		
		public static function IsLangFieldName( $filedName ){
			$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
			foreach( $langs as $lang ){
				$isInLang = strstr( $filedName , $lang );
				if( $isInLang ){
					return true;	
				}	
			}
			return false;
		}
		
		public static function GetLangFieldName( $filedName ){
			
			$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
			foreach( $langs as $lang ){
				$isInLang = strstr( $filedName , $lang );
				if( $isInLang ){
					return str_replace( '_'.$lang , '' , $filedName );	
				}	
			}
			return false;
			
		}
		
		public static function GetLangFieldLang( $filedName ){
			
			$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
			foreach( $langs as $lang ){
				$isInLang = strstr( $filedName , $lang );
				if( $isInLang ){
					return $lang;
				}	
			}
			return false;
			
		}

		
	}
?>