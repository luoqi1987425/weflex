<?php 
	class WeFlex_ZendX_Form_Decorator_Standard extends Zend_Form_Decorator_Abstract implements  Zend_Form_Decorator_Marker_File_Interface{
		
	    public function buildLabel()
	    {
	        $element = $this->getElement();
	        $label = $element->getLabel();
	        
	        /*user translate to translate the label*/
	        $translator = Zend_Registry::get( 'translate' );
	        if( $translator ){
	        	$label = $translator->_($label);
	        }
	        
	       
	        if ($element->isRequired()) {
	            $label .= '*';
	        }
	        $label .= ':';
	        return $element->getView()
	                       ->formLabel($element->getName(), $label);
	    }
	 
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
	 
	    public function buildErrors()
	    {
	        $element  = $this->getElement();
	        $messages = $element->getMessages();
	        if (empty($messages)) {
	            return '';
	        }
	        return '<div class="errors">' .
	               $element->getView()->formErrors($messages) . '</div>';
	    }
	 
	    public function buildDescription()
	    {
	        $element = $this->getElement();
	        $desc    = $element->getDescription();
	        if (empty($desc)) {
	            return '';
	        }
	        return '<div class="description">' . $desc . '</div>';
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
	        $label     = $this->buildLabel();
	        $input     = $this->buildInput();
	        $errors    = $this->buildErrors();
	        $desc      = $this->buildDescription();
	 		
	        if( $this->getOption( 'class' ) ){
	        	$output = '<p class="'.$this->getOption( 'class' ).'">';
	        }else{
	        	$output = '<p>';
	        }
	        $output = $output
	                . $label
	                . $input
	                . $errors
	                . $desc
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