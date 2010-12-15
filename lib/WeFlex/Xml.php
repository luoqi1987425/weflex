<?php 
/**
 * if key is rocky_0  the key will automatic into rocky
 *
 */
	class WeFlex_Xml
	{
		
		public static function arrayToXml($data, $rootNodeName = 'data', $xml=null)
		{
			// turn off compatibility mode as simple xml throws a wobbly if you don't.
			if (ini_get('zend.ze1_compatibility_mode') == 1){
				ini_set ('zend.ze1_compatibility_mode', 0);
			}
			
			if ($xml == null){
				$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
			}
			
			// loop through the data passed in.
			foreach($data as $key => $value){
				// no numeric keys in our xml please!
				if (is_numeric($key)){
					// make string key...
					$key = "unknownNode_". (string) $key;
				}
				
				if (self::_is_array_key($key)){
					// make string key...
					$key = self::_get_array_key($key);
				}
				
				// replace anything not alpha numeric
				//$key = preg_replace('/[^a-z]/i', '', $key);
				
				// if there is another array found recrusively call this function
				if (is_array($value)){
					$node = $xml->addChild($key);
					// recrusive call.
					self::arrayToXml($value, $rootNodeName, $node);
				}
				else
				{
				// add single node.
				//$value = htmlentities($value);
				$node = $xml->addChild($key);
				$node[0] = $value;
				}
			}
			// pass back as string. or simple xml object if you want!
			return $xml->asXML();
		}
		
		public static function xmlToArray( $xml ){
			
			$xml = simplexml_load_string($xml);
			
			return self::_xmlToArray( $xml );
			
			
		}
		
		private static function _xmlToArray( $xml ){
			
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
		           $r[$key] = self::_xmlToArray($value);
		       }
		       if (isset($a)) $r['@'] = $a;    // Attributes
		       return $r;
		   }
		   return (string) $xml;
				
		}
		
		private static function _is_array_key( $key ){
			
			$rtn = split( '_' , $key );
			
			if( count( $rtn ) == 2 ){
				if(preg_match("/^[\d]+/" , $rtn[1])){
					return true;
				}
			}
			
			return false;
			
			
		}
		
		private static function _get_array_key( $key ){
			
			$rtn = split( '_' , $key );
			return $rtn[0];
		}
		
		
		
		
	}


?>