<?php

/**
 * data structure
 * 
 * id
 * createTime
 * 'en' => array(
 * 		title => 'title_en_gb',
 * 		desc  => 'desc_en_gb'
 * ),
 * 'zn' => array(
 * 		title => 'title_zn',
 * 		desc  => 'desc_an'
 * )
 * 
 */
require_once 'WeFlex/Entity.php';

	class WeFlex_Entity_Lang extends WeFlex_Entity {
		
		/**
		 * current lang
		 */
		protected $_lang;
		
		/**
		 * array( 'title' , 'desc' )
		 * means these column support multiple lang
		 * 
		 */
		protected $_langColumnList = array();
		
		public function getLangFields(){
			return $this->_langColumnList;
		}
		
		
		
		public function set_lang( $lang ){
			$this->_lang = $lang;
		}
	
		public function get_lang() {
			return $this->_lang;
		}
		
		public function getBaseInfo(){
			
			$rtn = array();
			
			foreach( $this->_coll as $key => $value ){
				if( $key != 'langInfos' ){
					$rtn[$key] = $value ;	
				}
			}
			
			return $rtn;
			
			
		}
		
		public function getLangInfos(){
			return $this->_coll['langInfos'];
		}
		
	
		/**
		 * @see WeFlex_Entity::_getOffset()
		 *
		 * @param unknown_type $offset
		 * @return unknown
		 */
		protected function _getOffset( $offset ) {
			
			if( $this->_isColumnInLangList( $offset ) ){
				$this->_checkGetBanList( $offset );
				
				return $this->_coll['langInfos'][$this->_lang][$offset];
			}else{
				return parent::_getOffset( $offset );
			}
		
		}
		
		/**
		 * @see WeFlex_Entity::_setOffset()
		 *
		 * @param unknown_type $offset
		 * @param unknown_type $value
		 */
		protected function _setOffset($offset, $value) {
		
			if( $this->_isColumnInLangList( $offset ) ){
				
				$this->_checkSetBanList( $offset );
		   		$this->_checkType( $offset  , $value );
		   		$this->_checkEmpty( $offset , $value );
		   		$this->_checkLength( $offset , $value );
				
				$this->_coll['langInfos'][$this->_lang][$offset] = $value;	   				
		   		
			}else{
				parent::_setOffset( $offset , $value );
			}
			
		}
		
		private function _isColumnInLangList( $column ){
			
			foreach( $this->_langColumnList as $langColumn  ){
				if( $column == $langColumn ){
					
					return true;
				}	
			}
			return false;
		}
	


		
		
		
	}
?>