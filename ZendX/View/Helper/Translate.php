<?php 

	 
      class WeFlex_ZendX_View_Helper_Translate extends Zend_View_Helper_Abstract
      {
      	
      	private $_translate;
      	
      	  function __construct( $translate ){
      	  	
      	  	$this->_translate = $translate;	
      	  	
      	  }
      	 
      	
          public function translate( $messageId, $locale = null )
          {
             return $this->_core($messageId , $locale);
          }
          
          public function _( $messageId, $locale = null ){
          	 return $this->_core( $messageId , $locale );
          }
          
          private function _core( $messageId, $locale = null ){
          	return $this->_translate->_( $messageId , $locale );
     
          }
      }

?>