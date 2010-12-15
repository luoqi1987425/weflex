<?php 
/**
 * 
 * "Rocky Sky":[http://www.sina.com] => <a href="http://www.sina.com">Rocky Sky</a>
 * 
 * @author rocky
 *
 */
	class WeFlex_Textile{
		
		
		
		/*
		 * "Rocky Sky":[http://www.sina.com] => <a href="http://www.sina.com">Rocky Sky</a>
		 */
		public static function Parse( $text ){
			$rtn = preg_replace_callback( "/[\"][^\"]+[\"]:\[[^\]]+[\]]/" , 'weflex_textile_parse_item_callback' , $text );
			return $rtn;
		}
	
		
	}
	
	function weflex_textile_parse_item_callback( $matches ){
		
		preg_match( "/[^\"]+/" , $matches[0] , $out_content );
		preg_match( "/[\[][^\]]+[\]]/" , $matches[0] , $out_url );
		$content = $out_content[0];
		$url 	 = trim( $out_url[0] , "[]" );
		
		$rtn = '<a href="'.$url.'" target="_blank" title="'.$content.'">'.$content.'</a>';
		
		return $rtn;
	}
?>