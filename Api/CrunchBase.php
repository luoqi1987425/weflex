<?php 
	class WeFlex_Api_CrunchBase{
		
		
		public function getCompany( $permalink ){
			
			$api = "http://api.crunchbase.com/v/1/company/".$permalink.".js";
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
		}
		
		public function getService( $permalink ){
			
			$api = "http://api.crunchbase.com/v/1/product/".$permalink.".js";
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
			
		}
		
		public function getPeople( $permalink ){
			
			$api = "http://api.crunchbase.com/v/1/people/".$permalink.".js";
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
			
		}
		
		public function getFinancialOrganization( $permalink ){
			
			$api = "http://api.crunchbase.com/v/1/financial-organization/".$permalink.".js";
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
			
		}  
		
		public function getServiceProvider( $permalink ){
			
			$api = "http://api.crunchbase.com/v/1/service-provider/".$permalink.".js";
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
			
		}
		
		public function getCompanies(){
			
			$api = "http://api.crunchbase.com/v/1/companies.js";
			
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
		}
		
		public function getServices(){
			
			$api = "http://api.crunchbase.com/v/1/products.js";
			
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
			
		}
		
		public function getPeoples( ){
			
			$api = "http://api.crunchbase.com/v/1/people.js";
			
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
			
		}
		
		public function getFinancialOrganizations( ){
			
			$api = "http://api.crunchbase.com/v/1/financial-organizations.js";
			
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
			
		}  
		
		public function getServiceProviders( ){
			
			$api = "http://api.crunchbase.com/v/1/service-providers.js";
			
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
			
		}
		
		public function search( $keyword , $pageNo = null ){
			
			
			$api = "http://api.crunchbase.com/v/1/search.js?query=".$keyword;
			if( $pageNo ){
				$api .= '&page='.$pageNo;
			}
			
			$data = $this->_getUrlContents( $api );
			$data = Zend_Json::decode($data);
			return $data;
			
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
		
	}
?>