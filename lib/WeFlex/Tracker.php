<?php 
	class WeFlex_Tracker
	{
		const BASE_URL = '://www.pivotaltracker.com/services/v3/';
		private $_token;
		private $_forcessl;
		
		public function getFunction(){
			return $this->_token;
		}
		
		function __construct(){
			$this->_token = '' ;
			$this->_forcessl = false ;
		}
		
		public function setToken( $token )
		{
			$this->_token = $token;
		}
		
		/**
		 * use the user_name and user_password login tracker
		 *
		 * @param string $name
		 * @param string $password
		 */
		public  function login( $name , $password ) {
	        $auth = array(
				'username' => $name,
				'password' => $password,
			);

			$function = 'tokens/active';
			$token_arr = $this->_execute( $function, null, 'GET', $auth , 'https' );
			if ( $token_arr )
			{
				$this->_setSession( array( 'token'=>$token_arr->guid , 'uid'=>$token_arr->id ) );
			}
			else
			{
				throw new Exception( "Login Fail" , 1 );
			} 
    	}
    	
    	/**
    	 * get project by project_id
    	 *
    	 * @param unknown_type $project_id
    	 * @param unknown_type $token
    	 * @return unknown
    	 */
    	public function getProject( $project_id ){
    		$function = 'projects/'.$project_id;
    		$arr = $this->_execute( $function, null, 'GET', '' , 'http' );
    		return $arr;
    	}
    	
    	public function getStoryByFilter( $project_id , $conditions = NULL ){
    		$function = 'projects/'.$project_id.'/stories?filter=';
    		if ( $conditions )
    		{
    			foreach ( $conditions as $key => $value )
    			{
    				$filter[] = $key.":".$value;	
    			}
    			$filter = @implode(" ",$filter);
    			$function .= urlencode($filter);
    		}
    		
    		$arr = $this->_execute( $function, '' , 'GET', '' , 'http' );
 			return $arr;
    	}

		public function addStory($project_id , $datas )
		{
			if ( !is_array( $datas ) ) return false;
			$function = 'projects/'.$project_id.'/stories?';
			
			foreach ( $datas as $key => $value )
			{
				$conditions[]  =  urlencode("story[".$key."]=".$value);
			}
			$conditions = @implode("&",$conditions);
			$function .= $conditions;
			$arr = $this->_execute($function,'','POST','http');
			return $arr;
		}
		
		public function updateStory( $project_id , $story_id , $datas  )
		{
			if ( !is_array( $datas ) ) return false;
			$function = 'projects/'.$project_id.'/stories/'.$story_id.'?';
			
			foreach ( $datas as $key => $value )
			{
				$conditions[]  =  urlencode("story[".$key."]=".$value);
			}
			$conditions = @implode("&",$conditions);
			$function .= $conditions;
			$arr = $this->_execute($function,'','PUT','http');
			return $arr;
		}
		
		public function deleteStory( $project_id , $story_id )
		{
			$function = 'projects/'.$project_id.'/stories/'.$story_id;
			$arr = $this->_execute($function,'','DELETE','http');
			return $arr;
		}
		
		public function addStoryNote($project_id , $story_id , $note){
			
			$function = 'projects/'.$project_id.'/stories/'.$story_id . '/notes';
			$note = '<note><text>'.$note.'</text></note>';
			$arr = $this->_execute($function,$note,'POST', null , 'http' , array('Content-type: application/xml'));
			return $arr;
			
		}
		
    	private function _execute($function, $vars=null, $method='GET', $auth=null  , $http='http' , $headers = array() ) {
			$xml = $this->_curl($function, $vars, $method, $auth , $http  ,$headers);
			$arr = $this->_xmlToArray($xml);
			return $arr;
		}
		
		private function _curl($function, $vars=null, $method='GET', $auth=null , $http='http' , $headers = array() ) {
		
			$url = $http.self::BASE_URL.$function;

			$fields = (is_array($vars)) ? http_build_query($vars) : $vars;
			
			$ch = curl_init($url);

			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			switch ($method) {
				case 'GET':
				curl_setopt($ch, CURLOPT_HTTPGET, 1);
				break;
				case 'POST':
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				$headers [] = 'Content-Length: ' . strlen($fields);
				break;
				case 'PUT':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				$headers [] = 'Content-Length: ' . strlen($fields);
				break;
				case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				$headers [] = 'Content-Length: ' . strlen($fields);
				break;
				default:
				break;
			}

			//set headers
			if(!empty($this->_token)) {
				$headers [] = 'X-TrackerToken: ' . $this->_token;
			}
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			
			$do_auth = !empty($auth) && is_array($auth) && !empty($auth['username']) && !empty($auth['password']);
			
			if($do_auth) {
				curl_setopt($ch, CURLOPT_USERPWD, $auth['username'].':'.$auth['password']);
			}

			if($this->_forcessl && $do_auth) {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			} else {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			}
			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			$output = curl_exec($ch);
			
			$response = curl_getinfo($ch);
			

			if (curl_errno($ch))
				return curl_error($ch);
			else
				curl_close($ch);
			
			return $output;
		}
		
		private function _xmlToArray( $xml ){
				
			$xml = @simplexml_load_string($xml);
			if ( $xml )
			{
				$axml = new WeFlex_Axml();
				$data = $axml->obj2array( $xml , 10 );
				if ( !$data['story'][0]  )
				{
					$rtn = $data['story'];
					unset($data['story']);
					$data['story'][] = $rtn;
				}
			}
			return $data;
		}
	}

?>