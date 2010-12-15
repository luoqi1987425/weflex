<?php
class WeFlex_Api_IpInfoDb{
	
	protected $errors = array();
	protected $showTimezone = false;
	protected $service = 'api.ipinfodb.com';
	protected $version = 'v2';
	protected $apiKey = '';

	public function __construct(){}

	public function __destruct(){}

	public function setKey($key){
		if(!empty($key)) $this->apiKey = $key;
	}

	public function showTimezone(){
		$this->showTimezone = true;
	}

	public function getError(){
		return implode("\n", $this->errors);
	}

	public function getGeoLocation( $host = null ){
		
		if( $host ){
			$ip = @gethostbyname($host);
		}else{
			$ip = WeFlex_Util::GetIp();
		}
		

		if(preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ip)){
			$xml = @file_get_contents('http://' . $this->service . '/' . $this->version . '/' . 'ip_query.php?key=' . $this->apiKey . '&ip=' . $ip);

			try{
				$response = @new SimpleXMLElement($xml);

				foreach($response as $field=>$value){
					$result[(string)$field] = (string)$value;
				}
				return $result;
			}
			catch(Exception $e){
				$this->errors[] = $e->getMessage();
				return;
			}
		}

		$this->errors[] = '"' . $host . '" is not a valid IP address or hostname.';
		return;
	}
}
?>