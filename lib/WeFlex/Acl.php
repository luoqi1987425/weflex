<?php 
/**
 * if 单一情况的 database
 * id , role , resource , operation
 * else 
 * id , index , role , resource , operation
 *
 */

	class WeFlex_Acl{
		
		const OPERATION_GET 	= 'get';
		const OPERATION_CREATE 	= 'create';
		const OPERATION_DELETE	= 'delete';
		const OPERATION_POST	= 'post';
		
		/**
		 * @var whether this acl is store in database.
		 */
		protected $_isDb;
		
		protected $_index;
		
		/**
		 * @var WeFlex_Db_Model
		 */
		protected $_dbModel;
		
		/**
		 * array(
		 * 
		 * 	'resource' => array(
		 * 		
		 * 		'edit' => array( 'boss' , 'staff' ),
		 *      'get'  => array( 'boss' )
		 * 	
		 * 	)
		 * 
		 * )
		 *
		 * @var unknown_type
		 */
		private $_coll;
		
		
		/**
		 * construct acl
		 *
		 * @param array|string $options
		 */
		function __construct( $options , $index = null ){
			
			
			if( is_array( $options ) ){
				$this->_coll = $options;	
			}
			else if( is_string( $options ) ){

				$this->_isDb = true;
				$this->_index = $index;
				$this->_initModel( $options );
				$this->_initDbAcl();
				
			}
			
		}
		
		public function isAllowed( $roles , $resource , $operation ){
			
			try {
				
				if( is_array( $roles ) ){
					
					foreach( $roles as $role ){
						
						if( @in_array( $role , $this->_coll[$resource][$operation] ) ){
							return true;
						}else{
							return false;
						}
						
					}
					
				}else{
					if( @in_array( $roles , $this->_coll[$resource][$operation] ) ){
						return true;
					}else{
						return false;
					}
				}
				
				
				
				
				
			}catch( Exception $ex ){
				return false;
			}
			
		}
		
		public function allow( $role , $resource , $operations ){
			
			if( is_array( $operations ) ){
				foreach( $operations as $operation ){
					$this->_allow( $role , $resource , $operation );
				}
			}else{
				$this->_allow( $role , $resource , $operations );
			}
		}
		
		public function deny( $role , $resource , $operations ){
			
		}
		
		public function save(){
			
			if( $this->_isDb ){
				$this->_deleteOldData();
				$this->_createData();
			}
			
			
		}
		
		private function _allow( $role , $resource , $operation ){
			
				if( !$this->_coll[$resource] ){
					$this->_coll[$resource] = array();
				}
				if( !array_key_exists( $operation , $this->_coll[$resource] ) ){
					$this->_coll[$resource][$operation] = array();
				}
				
				if( is_array( $role ) ){
					foreach( $role as $roleCore ){
						if( !in_array( $roleCore , $this->_coll[$resource][$operation] ) ){
							$this->_coll[$resource][$operation] []= $roleCore;
						}
					}	
				}else{
					if( !in_array( $role , $this->_coll[$resource][$operation] ) ){
						$this->_coll[$resource][$operation] []= $role;
					}
				}
				
				
			
		}
		
		private function _deleteOldData(){
			
			if( $this->_index ){
				$this->_dbModel->delete( array( 'index' => $this->_index ) );
			}else{
				$this->_dbModel->delete( array() );
			}
			
		}
		
		private function _createData(){
			
			foreach( $this->_coll as $resource => $operations ){
				
				foreach( $operations as $operation => $roles ){
					
					foreach( $roles as $role ){
						
						if( $this->_index ){
							$this->_dbModel->insert( array( 'index' => $this->_index , 'resource' => $resource , 'operation' => $operation , 'role' => $role ) );
						}else{
							$this->_dbModel->insert( array( 'resource' => $resource , 'operation' => $operation , 'role' => $role ) );
						}
					}
				}
			}
		}
		
		private function _initDbAcl(){
			
			/* 如果acl不是针对单一的环境, 而是针对这一环境下边 不同id 的不同表现形式  */
			
			if( $this->_index ){
				$dbs = 	$this->_dbModel->getAllByConditions( array( 'index' => $this->_index ) );	
			}else{
				$dbs =  $this->_dbModel->getAllByConditions();
			}
			
			$coll = array();
			
			foreach( $dbs as $db ){
				
				if( !$coll[$db['resource']] ){
					$coll[$db['resource']] = array();
				}
				if( !$coll[$db['resource']][$db['operation']] ){
					$coll[$db['resource']][$db['operation']] = array();
				}
				
				$coll[$db['resource']][$db['operation']] []= $db['role'];
				
			}
			
			$this->_coll = $coll;
			
		}
		
		/**
		 * @param string $table
		 * @return WeFlex_Db_Model
		 */
		private function _initModel( $table ){
			
			$options = array(
		   			WeFlex_Db::ADAPTER   => WeFlex_Application::GetInstance()->config->db->adapter , 
		   			WeFlex_Db::DATABASE  => WeFlex_Application::GetInstance()->config->db->database , 
		   			WeFlex_Db::HOST 	 => WeFlex_Application::GetInstance()->config->db->host ,
		   			WeFlex_Db::USER 	 => WeFlex_Application::GetInstance()->config->db->user ,
		   			WeFlex_Db::PWD		 =>	WeFlex_Application::GetInstance()->config->db->pwd  ,
		   			WeFlex_Db::TABLE	 => $table
   			);
   			
   			
   			$this->_dbModel = new WeFlex_Db_Model( $options );
		}
		
		
		
	}
?>