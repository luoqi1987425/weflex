<?php 
	class WeFlex_ZendX_Form_Decorator_Submit extends Zend_Form_Decorator_Abstract{
		
	    
	 
	    public function buildInput()
	    {
	        $element = $this->getElement();
	        $helper  = $element->helper;
	        return $element->getView()->$helper(
	            $element->getName(),
	            $element->getValue(),
	            $element->getAttribs(),
	            $element->options
	        );
	    }
	 
	    
	 
	    public function render($content)
	    {
	    
	        $element = $this->getElement();
	        if (!$element instanceof Zend_Form_Element) {
	            return $content;
	        }
	        if (null === $element->getView()) {
	            return $content;
	        }
	 
	        $separator = $this->getSeparator();
	        $placement = $this->getPlacement();
	        $input     = $this->buildInput();
	        
	 		
	        if( $this->getOption( 'class' ) ){
	        	$output = '<p class="'.$this->getOption( 'class' ).'">';
	        }else{
	        	$output = '<p>';
	        }
	        $output = $output
	                . $input
	                . '</p>';
	 
	        switch ($placement) {
	            case (self::PREPEND):
	                return $output . $separator . $content;
	            case (self::APPEND):
	            default:
	                return $content . $separator . $output;
	        }
	    }
		
	}
?>