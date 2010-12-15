<?php

require_once 'Zend/View/Helper/FormElement.php';


class WeFlex_ZendX_View_Helper_FormCkeEditor extends Zend_View_Helper_FormElement
{

    public function formCkeEditor( $name , $value = null, $attribs = null , $options = null)
    {

		$xhtml = '<textarea name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($name) . '"'
                . $this->_htmlAttribs($attribs) . '>'
                . $this->view->escape($value) . '</textarea>';
                
      	$xhtml .= '
      	<script type="text/javascript">
      		
      		$(function (){
				/*
				init ckeditor
				*/
				 var editor = CKEDITOR.replace("'.$this->view->escape($name).'");
			     CKFinder.SetupCKEditor( editor, "'.$this->view->baseUrl().'/js/ckeditor/ckfinder" );
			});
      	</script>';
       

        return $xhtml;
    }
}
