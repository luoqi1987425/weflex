<?php

require_once 'Zend/Form/Element.php';


class WeFlex_ZendX_Form_Element_ImageUploadCrop extends Zend_Form_Element
{
	
	public $cropModule;
	public $uploadModule;

	
	public $options = array();

    
    public $helper = 'formImageUploadCrop';
	
	
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
	
	/**
	 * @return unknown
	 */
	public function getCropAction() {
		return $this->options['cropAction'];
	}
	
	/**
	 * @return unknown
	 */
	public function getCropController() {
		return $this->options['cropController'];
	}
	
	/**
	 * @return unknown
	 */
	public function getCropHeight() {
		return $this->options['cropHeight'];
	}
	
	/**
	 * @return unknown
	 */
	public function getCropWidth() {
		return $this->options['cropWidth'];
	}
	
	public function getCropParam(){
		return $this->options['cropParam'];
	}
	
	/**
	 * @param unknown_type $cropAction
	 */
	public function setCropAction($cropAction) {
		$this->options['cropAction'] = $cropAction;
	}
	
	/**
	 * @param unknown_type $cropController
	 */
	public function setCropController($cropController) {
		$this->options['cropController'] = $cropController;
	}
	
	/**
	 * @param unknown_type $cropHeight
	 */
	public function setCropHeight($cropHeight) {
		$this->options['cropHeight'] = $cropHeight;
	}
	
	/**
	 * @param unknown_type $cropWidth
	 */
	public function setCropWidth($cropWidth) {
		$this->options['cropWidth'] = $cropWidth;
	}
	
	
	
	
	public function setCropModule($cropModule) {
		$this->options['cropModule'] = $cropModule;
	}
	
	
	public function setUploadModule($uploadModule) {
		$this->options['uploadModule'] = $uploadModule;
	}
	
	public function setCropParam( $param ){
		$this->options['cropParam']	=	$param;
	}



}
