<?php 
require_once 'Zend/Form/Element/Select.php';

	class WeFlex_ZendX_Form_Element_SourceSelect extends Zend_Form_Element_Select
	{
		
		public $dataSource;

		public function init(){
			
			if( $this->dataSource ){
				$dataSource = explode('|', $this->dataSource);
				if($dataSource[2]){
					$options = call_user_func_array(array($dataSource[0], $dataSource[1]), $dataSource[2]);
				}else{
					$options = call_user_func_array(array($dataSource[0], $dataSource[1]) , array());
				}
			
			}
			
			$this->setMultiOptions( $options );

		}
	
	
		public function setDataSource($dataSource) {
			$this->dataSource = $dataSource;
		}

		
		
	}
?>