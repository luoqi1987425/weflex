<?php

class WeFlex_ZendX_Translate_Adapter_ArrayPlus extends Zend_Translate_Adapter_Array
{
	function getData(){
		return $this->_translate;
	}
}