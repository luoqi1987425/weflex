<?php

require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * this helper is a assistance of return result for ajax callback
 * and can parse the request data of json format
 *
 */
class WeFlex_ZendX_Controller_Action_Helper_WfAjax extends Zend_Controller_Action_Helper_Abstract
{


	/**
	 * return for success result
	 *
	 * @param array | string $info
	 * { status : 1 , info : data }
	 */
	public function success( $info = null ){
		
		$successReturn = $this->_generReturn( true , $info );
		$encodeJson =    $this->_encodeJson($successReturn);
		$this->_sendJson( $encodeJson );
		
	}
	
	/**
	 * return for error result
	 *
	 * @param array | string $info
	 */
	public function error( $info = null ){
		$successReturn = $this->_generReturn( false , $info );
		$encodeJson =    $this->_encodeJson($successReturn);
		$this->_sendJson( $encodeJson );
	}
	
	/**
	 * parse JsonString to Array
	 *
	 * @param String $jsonStr
	 */
	public function parsePostJson( $jsonStr ){
		
	}
	
	
	/**
	 * gener a success or error array return
	 *
	 * @param boolean $isSuccess
	 * @param array | string $info
	 * @return array
	 */
	private function _generReturn( $isSuccess , $info = null ){
		
		$return = array();
		
		if( $isSuccess ){
			$return['status'] = 1;
		}else{
			$return['status'] = 0;
		}
		
		if( $info == null ){
			$info = array();
		}
		
		$return['info'] = $info;
		
		return $return;
		
	}
	
	/**
	 * echo array to json
	 * and set response type application/json
	 *
	 * @param unknown_type $return
	 */
	private function _encodeJson( $return ){
		
		/**
         * @see Zend_View_Helper_Json
         */
        require_once 'Zend/View/Helper/Json.php';
        $jsonHelper = new Zend_View_Helper_Json();
        $data = $jsonHelper->json($return);
        return $data;
		
	}
	
	/**
	 * send response
	 *
	 * @param jsonString $json
	 */
	private function _sendJson( $json ){
		$response = $this->getResponse();
		$response->setBody($json);
        $response->sendResponse();
        exit;
	}
	
}

