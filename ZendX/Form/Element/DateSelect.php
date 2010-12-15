<?php 
require_once 'Zend/Form/Element/Select.php';

	class WeFlex_ZendX_Form_Element_DateSelect extends Zend_Form_Element
	{

		
		public $options = array( 
			'startYear' => 1900,
			'endYear'	=> 2035
		 );
	
		
		public $helper = 'formDateSelect';
	
		
		public function setEndYear($endYear) {
			$this->options['endYear'] = $endYear;
		}
		

		public function setStartYear($startYear) {
			$this->options['startYear'] = $startYear;
		}

		
		
	}
?>