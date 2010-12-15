<?php 
	
	class WeFlex_Sitemap{
		
		public static function Gener( $filePath , $urls ){
			
			$xml = '';
			$xml .= self::GenerHeader();
			$xml .= self::GenerItems( $urls );
			
			file_put_contents($filePath , $xml);
			
			return $xml;
			
		}
		
		public static function GenerHeader(){
			
			$rtn = '<?xml version="1.0" encoding="UTF-8"?>';
			
			return $rtn;
			
		}
		
		public static function GenerItems( $urls ){
			
			$rtn = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
			
			if( is_array( $urls ) ){
				
				foreach( $urls as $url ){
					$rtn .= '<url>
  								<loc>'.$url.'</loc>
							 </url>';
				}
			}
			
			$rtn.= '</urlset>';
			return $rtn;
			
		}
		
	}

?>