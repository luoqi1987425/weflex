<?php 
require_once 'Functions.php';

	class WeFlex_Application
	{
		
		private static $_instance;
		
		
		/**
		 * @return WeFlex_Application
		 */
		public static function GetInstance(){
			if( !self::$_instance ){
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		
		/**
		 * @var current evn
		 */
		private $_environment;
		
		/**
		 * store config for all
		 * application.db....
		 * project.email....
		 *
		 * @var Zend_Config
		 */
		public $configAll;
		
		/**
		 * only store config for application
		 * 
		 * config.db...
		 * config.db...
		 *
		 * @var Zend_Config
		 */
		public $config;
		
	
		
		
		public function start( $evn , $configFilePath = null){
			//check enviroment 
			//check create file dir that require
			
			$this->_environment = $evn;	
			$this->_configSystem( $configFilePath );
			$this->_initTimeZone();
			
		}
		
		
		/**
		 * get system support langs
		 *
		 */
		public function getLangs(){
			
			if( $this->config->lang ){
				$langs = $this->config->lang->toArray();
				return $langs;
			}else{
				return array();
			}
		}
		
		public function getDefaultLang(){
			
			if( $this->config->lang ){
				$langs = $this->config->lang->toArray();
				foreach( $langs as $key => $lang ){
					return $key;
				}
			}else{
				return null;
			}	
		}
		
		public function getPublicPath(){
			
			return $this->config->public_path;
			
		}
	
		
		
		
		private function _configSystem( $configFilePath = null ){
			
			$options['nestSeparator'] = '.';
			
			if( Zend_Registry::isRegistered('cache')){
				
				$config = Zend_Registry::get('cache')->load('WeFlex_Config');
				
				if( !$config ){
					
					$config = new Zend_Config_Ini($configFilePath,
	                              $this->_environment,
	                              $options);
	                              
					Zend_Registry::get('cache')->save($config, 'WeFlex_Config');
				}
				
				
			}else{
				$config = new Zend_Config_Ini($configFilePath,
	                              $this->_environment,
	                              $options);
			}
				
                              
            $this->configAll = $config;
            $this->config    = $config->application;
			
		}
		
		
		private function _initTimeZone(){
			if( $this->config->timezone ){
				date_default_timezone_set( $this->config->timezone );
			}
		}
		
		
		
	}
?>