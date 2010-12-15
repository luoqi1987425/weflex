<?php 
/**
 * 这个class 主要用来 假装一个 Zend_Translator 不过没有任何作用
 * 主要会注入到 Zend_Helper 里边去
 */
	class WeFlex_ZendX_FakeTranslator extends Zend_View_Helper_Abstract{
		
		
		public function translate( $messageId, $locale = null )
          {
             return $messageId;
          }
		
		public function _( $messageId , $locale = null){
			return $messageId;
		}
		
	  	public function __call($method, array $options)
	    {
	       return;
	    }
		
	}

?>