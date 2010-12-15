<?php 
/**
 * WeFlex
 * 
 * This class is a proxy which can manipulate a table in database.
 * We can use this proxy class called Model to do some manipulation such as 
 * insert , select , update and delete. 
 * This class must to be extended , and the subclass will defind the database 
 * name and the table name.
 * The following is the interface.
 * 
 *
 */

require_once 'Zend/ZendAdapterFactory.php';


	class WeFlex_Db_Model
	{
		
		
		protected $_tableName;
		
		/**
		 * @var Zend_Db_Select
		 */
		protected $_selector;
		
		/**
		 * @var Zend_Db_Adapter_Abstract
		 */
		protected $_adapter;
		
		function __construct( $options = null ){
			
			
			if( !$options ){				
				$options = array(
		   			WeFlex_Db::ADAPTER   => WeFlex_Application::GetInstance()->config->db->adapter , 
		   			WeFlex_Db::DATABASE  => WeFlex_Application::GetInstance()->config->db->database , 
		   			WeFlex_Db::HOST 	 => WeFlex_Application::GetInstance()->config->db->host ,
		   			WeFlex_Db::USER 	 => WeFlex_Application::GetInstance()->config->db->user ,
		   			WeFlex_Db::PWD		 =>	WeFlex_Application::GetInstance()->config->db->pwd  ,
		   			WeFlex_Db::TABLE	 => $this->_tableName
   				);
			}
			
			if( !$options[WeFlex_Db::TABLE] ){
				throw new Exception( 'no table name specify' );
			}
			
			
			$this->setOptions( $options );
		}
		
		public function setOptions( $options ){
			$this->_adapter		=   WeFlex_Db_ZendAdapterFactory::factory($options);     
			$this->_selector	=	$this->_adapter->select();
			$this->_tableName 	=   $options[WeFlex_Db::TABLE];
			$this->_initSelector();
		}
		
		/**
		 * init the select
		 *
		 */
		private function _initSelector(){
			$this->_selector->reset();
			$this->_selector->from( $this->_tableName );
		}
		
		public function getTableName(){
			return $this->_tableName;
		}
		
		
		/**
		 * we can get many rows from table , you can set where condition , how to order , how many count , 
		 * where to start.
		 * Example:
		 * // SELECT * FROM round_table
		   // WHERE noble_title = "Sir" and title = 'Rocky'
		   // ORDER BY first_name
		   // LIMIT 10 OFFSET 20
			
			$where = array( 
						array( 'noble_title = ?' , 'Sir' ),
						array( 'title = ?' , 'Rocky' ) 
					 );
			$order = 'first_name';
			$count = 10;
			$offset = 20;
			
			$rows = $model->fetchAll( $where , $order , $count , $offset );
		 * 
		 *
		 * @param array | String  $where
		 * @param String $order
		 * @param int $count
		 * @param int $offset
		 */
		public function fetchAll(){
			$data	= $this->_adapter->fetchAll( $this->_selector );
			$this->_initSelector();
			return $data;
		}
		
		/**
		 * See as fetchAll , the difference is this method return only 1 row.
		 *
		 * @param unknown_type $where
		 * @param unknown_type $order
		 */
		public function fetch(){
			$data = $this->_adapter->fetchRow( $this->_selector );
			$this->_initSelector();
			return $data;
		}
		
		/**
		 * count
		 * @return int
		 */
		public function count(){
			$this->_selector->reset( Zend_Db_Select::COLUMNS );
			$this->_selector->columns( array( 'count' => 'count(*)' ) );
			$data = $this->_adapter->fetchRow( $this->_selector );
			$this->_initSelector();
			return intval( $data['count'] );
		}
		
		/**
		 * inner Join
		 * 
		 * @param array|String $name  
		 * @param unknown_type $cond
		 * @return WeFlex_Db_Model
		 */
		public function joinInner( $name , $cond ){
			$this->_selector->joinInner( $name , $cond );
			return $this;
		}
		
		/**
		 * left Join
		 *
		 * @param unknown_type $name
		 * @param unknown_type $cond
		 * @return WeFlex_Db_Model
		 */
		public function joinLeft($name, $cond){
			$this->_selector->joinLeft( $name , $cond );
			return $this;
		}
		
		/**
		 * Columns
		 *
		 * @param unknown_type $name
		 * @param unknown_type $cond
		 * @return WeFlex_Db_Model
		 */
		public function columns( $cols ){
			$this->_selector->reset( Zend_Db_Select::COLUMNS );
			foreach($cols as $col){
				$this->_selector->columns( $col , null );
			}
			return $this;
		}
		
		
		
		/**
	     * Updates existing rows.
	     * Don't use $model->where()->update($data);
	     *
	     * @param  array        $data  Column-value pairs.
	     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
	     * @return int          The number of rows updated.
	     * @throws new Exception( 'condition is null' );
	     */
		public function update( $data , $where){
			
			if( !isset( $where ) ){
				throw new Exception( 'condition is null' );
			}
			
			//no data need update
			if( count( $data ) == 0 ){
				return;
			}
			
			$where = $this->_translateToZendWhere( $where );
			$rtn 	= $this->_adapter->update( $this->_tableName , $data , $where );
			
			
			return $rtn;
			
		}
		
		
		/**
	     * Deletes existing rows.
	     *
	     * @param  array|string $where SQL WHERE clause(s).
	     * @return int          The number of rows deleted.
	     */
		public function delete($where){
			
			if( !isset( $where ) ){
				throw new Exception( 'condition is null' );
			}
			
			$where = $this->_translateToZendWhere( $where );
			return $this->_adapter->delete( $this->_tableName , $where );
		}
		
		/**
	     * Inserts a new row.
	     *
	     * @param  array  $data  Column-value pairs.
	     * @return mixed         The primary key of the row inserted.
	     */
		public function insert( $data ){
			$this->_adapter->insert( $this->_tableName , $data );
			$id = $this->_adapter->lastInsertId();
			return $id;
		}
		
		/**
		 * query sql 
		 *
		 * @param String $sql
		 * @return array
		 */
		public function query( $sql ){
			$result = $this->_adapter->query( $sql );
			return $result->fetchAll();
		}
		
		
		/**
		 * array( 'key' => 1 );
		 * array( 'key' => array( '!=' , 1 ) )
		 * array( 'key' => array( 'min' , 1 ) )
		 * array( 'key' => array( 'max' , 5 ) )
		 * array( 'key' => array( 'between' , 1 , 5 ) )
		 * array( 'key' => array( 'in'  , array( 1,2,3,4,5 ) ) )
		 * array( 'key' => array( 'match'  , 'rocky' ) ) 
		 * 
		 * @return WeFlex_Db_Model
		 */
		public function where( $conditions ){
			if( is_array( $conditions ) ){
				$conditions = $this->_translateToZendWhere( $conditions );
				$this->_selector->where( $conditions );
			}
			return $this;
			
		}
		
		/**
		 * see where
		 * @return WeFlex_Db_Model
		 */
		public function orWhere( $conditions ){
			if( is_array( $conditions ) ){
				$conditions = $this->_translateToZendWhere( $conditions );
			}
			$this->_selector->orWhere( $conditions );
			return $this;
		}
		
		/**
		 * @return WeFlex_Db_Model
		 */
		public function order( $order ){
			$this->_selector->order( $order );
			return $this;
		}
		
		/**
		 * @return WeFlex_Db_Model
		 */
		public function group( $group ){
			$this->_selector->group( $group );
			return $this;
		}
		
		/**
		 * @return WeFlex_Db_Model
		 */
		public function limit( $count = null , $offset = null ){
			$this->_selector->limit( $count , $offset  );
			return $this;
		}
		
		/**
		 * @return WeFlex_Db_Model
		 */
		public function limitPage( $page , $rowCount ){
			$this->_selector->limitPage( $page , $rowCount );
			return $this;
		}
		
		/**
		 * @return String
		 */
		public function assemble(){
			return $this->_selector->assemble();
		}
		
		/**
		 * quote a query
		 * see more in zend db
		 * @return String
		 */
		public function quoteInto($text, $value, $type = null, $count = null){
			return $this->_adapter->quoteInto($text, $value, $type, $count);
		}
		
		public function quote($value , $type = null){
			return $this->_adapter->quote( $value , $type );
		}
		
		/*
		 * quickly use of find one by conditions
		 * $model->getOneByCondtions( array( 'id' => 1 , 'name' => 2 , 'age' => array( 'min' , 18 ) ) )
		 */
		public function getOneByConditions( $conditions = null , $cloumns = null ){
			
			if( $conditions ){
				$this->where( $conditions );
			}
			
			if( is_array( $cloumns ) ){
				$this->columns( $cloumns );
			}
		
			return $this->fetch();
			
		}
		
		/**
		 * quickly use of find all by conditions
		 *
		 * @param unknown_type $column
		 * @param unknown_type $order
		 * @param unknown_type $pageNo
		 * @param unknown_type $pageSize
		 * 
		 * $model->getAllByConditions( array( 'age' => array( 'min' , 18 ) ) , 'name' , 2 , 5 );
		 * 
		 */
		public function getAllByConditions( $conditions = null , $order = null , $pageNo = null , $pageSize = null , $cloumns = null ){
			
			if( $conditions ){
				$this->where( $conditions );
			}
			
			if( $order ){
				$this->order( $order );
			}
			
			if( $pageNo && $pageSize ){
				$this->limitPage( $pageNo , $pageSize );
			}
			
			if( is_array( $cloumns ) ){
				$this->columns( $cloumns );
			}
			
			return $this->fetchAll();
		}
		
		
		protected function _translateToZendWhere( $conditions ){
			
			if( is_array( $conditions ) ){
				
				
				$rtn = $this->_adapter->quoteInto( ' 1 = ?' , 1 );
				
				
				foreach( $conditions as $key => $value ){
					
					$key = $this->_secureColumn($key);
					
					if( !is_array( $value ) ){
						$rtn .= $this->_adapter->quoteInto( ' AND '.$key.' = ?' , $value );
					}else{
						switch( $value[0] ){
							case 'max':
								$rtn .= $this->_adapter->quoteInto( ' AND '.$key.' < ?' , $value[1] );
								break;
							case 'min':
								$rtn .= $this->_adapter->quoteInto( ' AND '.$key.' > ?' , $value[1] );
								break;
							case 'between' :
								$rtn .= $this->_adapter->quoteInto( ' AND '.$key.' > ?' , $value[1] );
								$rtn .= $this->_adapter->quoteInto( ' AND '.$key.' < ?' , $value[2] );
								break;	
							case 'in'	   :
								$rtn .= $this->_adapter->quoteInto( ' AND '.$key.' in (?)' , $value[1] );
								break;
							case '!='	   :
								$rtn .= $this->_adapter->quoteInto( ' AND '.$key.' != ?' , $value[1] );
								break;	
							case 'match'   :
								$rtn .= $this->_adapter->quoteInto( ' AND match('.$key.') against (?)' , $value[1] );
						}
					}
				}
				return $rtn;
				
			}else{
				throw new Exception( 'conditions can not be parse' );
			}
		}
		
		private function _secureColumn( $column ){
			
			$items = explode('.' , $column);
			
			
			
			foreach( $items as $key => $value ){
				$items[$key] = '`'.$value.'`';
			}
			
			return implode(".", $items);
			
		}
		
	
	}
?>