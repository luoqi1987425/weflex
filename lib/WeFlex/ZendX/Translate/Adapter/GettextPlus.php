<?php

class WeFlex_ZendX_Translate_Adapter_GettextPlus extends Zend_Translate_Adapter_Gettext
{
	function addData(array $data){
		foreach($data as $lang => $value)
			$this->_translate[$lang] = array_merge(@(array)$this->_translate[$lang], $value);
	}
}