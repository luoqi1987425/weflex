<?php
require_once 'Zend/Db.php'; 
require_once 'Zend/Db/Profiler/Firebug.php';

	class WeFlex_Db_ZendAdapterFactory
	{
		
		/**
		 * Create a Zend_Db_Adapter_Abstract
		 *
		 * @param array $options
		 * @return Zend_Db_Adapter_Abstract
		 */
		public static function factory( $options ){
			
			
			$adapter 	= self::_getZendAdapterName( $options[WeFlex_Db::ADAPTER] );
			
			/**
			 * $TODO check the following validate
			 */
			$database	= $options[WeFlex_Db::DATABASE];
			$username	= $options[WeFlex_Db::USER];
			$password	= $options[WeFlex_Db::PWD];
			$host		= $options[WeFlex_Db::HOST];
			
			
			/**
			 * @todo if the adapter is exsit , we could not create it , and get it from registry
			 */
			
			$zendAdapter = Zend_Db::factory(
				$adapter, 
				array(
				    'host'     => $host,
				    'username' => $username,
				    'password' => $password,
				    'dbname'   => $database
				)
			);
			
			/**
			 * hack for debugger;
			 */
			if( WeFlex_Application::GetInstance()->config->db->usefirephp ){
				$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
				$profiler->setEnabled(true);
				$zendAdapter->setProfiler($profiler);
			}
			
			$zendAdapter->query("SET NAMES 'utf8'");
			
			
			return $zendAdapter;
		}
		
		private static function _getZendAdapterName( $adapter ){
			
			
			/**
			 * @todo 
			 * if is null , throw Exception( 'not sepcify the adapter' );
			 */
			
			switch( $adapter ){
				case WeFlex_Db::ADAPTER_MYSQL :
					return 'PDO_MYSQL';
					break;
				default :
					throw new Exception( 'The Adapter "'. $adapter .'" is not support by system , check if u have a correct adapter name' );
					break;
			}
			
		}
	}
?>