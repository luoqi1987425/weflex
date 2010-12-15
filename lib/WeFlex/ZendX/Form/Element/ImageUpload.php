<?php

require_once 'Zend/Form/Element.php';


class WeFlex_ZendX_Form_Element_ImageUpload extends Zend_Form_Element
{
	

	
	public $options = array();

    
    public $helper = 'formImageUpload';
	
	
	public function getButtonImageUrl() {
		return $this->options['buttonImageUrl'];
	}
	
	
	public function getFlashUrl() {
		return $this->options['flashUrl'];
	}
	
	
	public function getUploadAction() {
		return $this->options['uploadAction'];
	}
	
	
	public function getUploadController() {
		return $this->options['uploadController'];
	}
	
	public function getUploadParam(){
		return $this->options['uploadParam'];
	}
	
	
	public function setButtonImageUrl($buttonImageUrl) {
		$this->options['buttonImageUrl'] = $buttonImageUrl;
	}
	
	
	public function setFlashUrl($flashUrl) {
		$this->options['flashUrl'] = $flashUrl;
	}
	
	
	public function setUploadAction($uploadAction) {
		$this->options['uploadAction'] = $uploadAction;
	}
	
	
	public function setUploadController($uploadController) {
		$this->options['uploadController'] = $uploadController;
	}
	
	public function setUploadParam( $param ){
		$this->options['uploadParam'] = $param;
	}
	
	
	
	public function setUploadModule($uploadModule) {
		$this->options['uploadModule'] = $uploadModule;
	}
	



}
