<?php

	class WeFlex_ZendX_Form extends Zend_Form{
		
		public $urlAction;
		public $urlController;
		public $urlModule;
		public $urlParam;
		public $langFields = array();
		
		
		public function __construct( $options = null ){
			
			$this->perInit();
			parent::__construct( $options );
			$this->postInit();
			
		}
		
		public function perInit(){
			
			//custom element
			$this->addPrefixPath('WeFlex_ZendX_Form_Element', 'WeFlex/ZendX/Form/Element', 'element');
		
			//custom validator
			$this->addElementPrefixPath('WeFlex_ZendX_Validate', 'WeFlex/ZendX/Validate', 'validate');
			
			//element decorator
			$this->addElementPrefixPath( 'WeFlex_ZendX_Form_Decorator' , 'WeFlex/ZendX/Form/Decorator' , 'decorator' );
			
			//element decorator
			$this->setElementDecorators( array('Standard') );
			
		}
		
		public function postInit(){
			
			//manipulate langs fields
			$langs = WeFlex_Application::GetInstance()->getLangs();
			$formElements = $formOriginalElements = $this->getElements();
			foreach($formOriginalElements as $key => $object)
			{
				if(in_array($key, $this->langFields )){
					
					
					foreach(array_keys($langs) as $lang){
						
						//$newElement = $this->getElement();
						$newElement = clone $object;
						
						$newElement->setName( $newElement->getName() . '_' . $lang );
						$newElement->setLabel( $newElement->getLabel() );
						$temp[] = $newElement;
					}
					array_splice($formElements, $this->_arrayFindPostion($formElements, $key), 1, $temp);
					$this->_elementsRest($formElements);
				}
			}
			$this->setElements($formElements);
			
			foreach(array_keys($langs) as $lang){
				$groupArray = array();
				foreach( $this->langFields as $filed ){
					$groupArray []= $filed . '_' . $lang;
					$this->addDisplayGroup( $groupArray , $this->getName() . '_' . $lang );
				}
			}
			
			//group decorator
     		$this->setDisplayGroupDecorators(array(
							'FormElements',
							'Fieldset'
     		));
			
			
			
			
		}
		
		
		
		public function init(){
			
			//action
			$view = $this->getView();
			
			$baseArray =  array( 'action' => $this->urlAction , 'controller' => $this->urlController , 'module' => $this->urlModule);
			
			if( $this->urlParam ){
				foreach( $this->urlParam as $key => $value ){
					$baseArray[$key]  = $value; 
				}
			}
			
			$url = $view->url( $baseArray , 'default', true);
			$this->setAction( $url );
			//validate
			
			//filter
			
			
			//default decorator
			
			$this->addDecorators( array(
				array( 'FormElements' ),
				array( 'Fieldset' ),
				array( 'Form' )
			) );
		}
		
		public function setEntity( WeFlex_Entity $entity  ){
			
			foreach($this->_elements as $element ){
				if( !$this->_isFileNameInLangs( $element->getName() ) ){
					if( $element->getType() != 'Zend_Form_Element_Submit' ){
						
						if( $element->getType() == 'WeFlex_ZendX_Form_Element_DateSelect'){
							$element->setValue( date('Y-m-d' , $entity[$element->getName()]) );
						}else if($element->getType() == 'WeFlex_ZendX_Form_Element_CkeEditor'){
							$element->setValue( htmlspecialchars_decode(stripslashes($entity[$element->getName()]) ));
						}else{
							$element->setValue( $entity[$element->getName()] );
						}
					}
				}
			}
			
			foreach( $this->langFields as $langField ){
				
				$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
				
				foreach( $langs as $lang ){
					
					$element = $this->getElement( $langField . '_' . $lang );
					
					if( $element ){
						if( $element->getType() == 'WeFlex_ZendX_Form_Element_DateSelect'){
							$element->setValue( date('Y-m-d' , $entity['langInfos'][$lang][$langField]) );
						}else if($element->getType() == 'WeFlex_ZendX_Form_Element_CkeEditor'){
							$element->setValue( htmlspecialchars_decode(stripslashes($entity['langInfos'][$lang][$langField]) ));
						}else{
							$element->setValue( $entity['langInfos'][$lang][$langField] );
						}
					}
					
				}
			
			}
			
		}
		
		public function generDataForEntity( $postData , $fileData = null ){
			
			$entity = array();
			
			//gener un lang data
			foreach($this->_elements as $element ){
				if( !$this->_isFileNameInLangs( $element->getName() ) ){
					if($element->getType() != 'Zend_Form_Element_Submit'){
						if( $element->getType() == 'WeFlex_ZendX_Form_Element_DateSelect'){	
							$entity[$element->getName()] = mktime( 0 , 0 , 0 , $postData[ $element->getName() . '_month' ] , $postData[ $element->getName() . '_date' ] , $postData[ $element->getName() . '_year' ] );
						}else{
							if( isset($postData[$element->getName()]) ){
								$entity[$element->getName()] = $postData[$element->getName()];
							}else if( $fileData[$element->getName()] ){
								$entity[$element->getName()] = $fileData[$element->getName()];
							}
						}		
					}
				}
			}
			
			//gener lang entity
			if( count( $this->langFields ) > 0 ){
			$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
			
			$entity['langInfos'] = array();
			
			foreach( $langs as $lang ){
				$entity['langInfos'][$lang] = array();
				foreach( $this->langFields as $langField ){
					$entity['langInfos'][$lang][$langField] = $postData[$langField.'_'.$lang];
				}
			}

			}
			
			return $entity;
			
			
		}
	
		
		
		
		
		public function setUrlAction($urlAction) {
			$this->urlAction = $urlAction;
		}
		
		
		public function setUrlController($urlController) {
			$this->urlController = $urlController;
		}
		
		
		public function setUrlModule($urlModule) {
			$this->urlModule = $urlModule;
		}
	
	
		public function setUrlParam($urlParam) {
			$this->urlParam = $urlParam;
		}
		
		public function setEntityClass( $className ){
			
//			$entity = new $className(array());
//			
//			//if lang entity   initial lang fields
//			if( $entity instanceof WeFlex_Entity_Lang  ){
//				$this->langFields = $entity->getLangFields();
//			}
			
		}
		
		public function setLangFields( $langFields ){
			$this->langFields = explode('|', $langFields);
		}
		
		
		private function _elementsRest(array &$array){
			foreach($array as $key => $element)
				$array_new[$element->getName()] = $element;
			$array = $array_new;
		}
		
		private function _arrayFindPostion(array $array, $key){
			foreach(array_keys($array) as $postion => $k)
				if($k == $key)
					return $postion;
		}
		
		private function _isFileNameInLangs( $filedName ){
			
			$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
			foreach( $langs as $lang ){
				
				$isInLang = strstr( $filedName , $lang );
				if( $isInLang ){
					return true;	
				}	
			}
			return false;
		}


		
	}
?>