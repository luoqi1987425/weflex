<?php 
	class WeFlex_Api_Yql{
		
		const BASE_URI = 'http://query.yahooapis.com/v1/public/yql';
		
		const TABLE_SEARCH_SUGGEST = 'search.suggest';
		
		const TABLE_WEATHER_FORCAST = 'weather.forcast';
		
		const TABLE_FLICKR_PHOTOS_SEARCH = 'flickr.photos.search';
		
		public function query( $yql ){
			
			$yql = urlencode(utf8_encode($yql));
			$url = self::BASE_URI . '?q=' . $yql . '&format=json&diagnostics=false';
			$data = $this->_getUrlContents( $url );
			$responseArray = Zend_Json::decode($data);
			
			return $responseArray['query']['results'];
			
		}
		
		public function fetch( $table , $where = null , $pageNo = null , $pageSize = null ){
			
			$yql = $this->_generYQL($table , $where , $pageNo , $pageSize );
			
			return $this->query( $yql );
			
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
		
		private function _generYQL( $table , $where = null , $pageNo = null , $pageSize = null ){
			
			$yql = 'select * from '.$table;
			
			if( $pageNo && $pageSize ){
				$start = $pageSize * ( $pageNo - 1 );
				$yql .= '('.$start.','.$pageSize.')';
			}
			
			if( $where ){
				$yql .= ' where ';
				foreach( $where as $key => $value ){
					$yql .= $key.'="'.$value.'"';
				}
			}
			
			return $yql;
			
		}
		
		
		
		
	}
?>