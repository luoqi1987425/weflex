<?php 
	class WeFlex_Entity_Lang_DbHelper{
		
		public static function Create( WeFlex_Entity_Lang $entity , $table ){
			
			
			$baseModel = self::_generModel( $table );
			$langModel = self::_generModel( $table.'_lang' );
			
			$baseInfo = $entity->getBaseInfo();
			$langInfos = $entity->getLangInfos();
			
			unset( $baseInfo['id'] );
			
			$id = $baseModel->insert($baseInfo);
			

			foreach( $langInfos as $lang => $values ){
				
				$values['parent'] = $id;
				$values['lang']   = $lang;
				
				$langModel->insert( $values );
				
			}
			
			return $id;
	
		}
		
		public static function GetAll( $entityName , $table , $conditions = null , $order = null , $pageNo = null , $pageSize = null ){

			$entity = null;
			eval( '$entity = new '.$entityName.'(array());' );
			$langFields = $entity->getLangFields();
			
			$dataDbs = self::_getAllByConditions( $table , $langFields , $conditions  , $order , $pageNo  , $pageSize );
			
			
			$datas = array();
			foreach( $dataDbs as $dataDb ){
				$datas []= self::_generDataFromDb( $dataDb , $langFields );
			}
			

			$rtn = array();
			foreach( $datas as $data ){
				eval( '$entity = new '.$entityName.'($data);' );
				if( Zend_Registry::get( 'lang' ) ){
					$entity->set_lang( Zend_Registry::get( 'lang' ) );
				}
				$rtn []= $entity;
			}
			
			return $rtn;
		}
		
		public static function GetAllCount( $entityName , $table , $conditions = null ){
			$entity = null;
			eval( '$entity = new '.$entityName.'(array());' );
			$langFields = $entity->getLangFields();
			return self::_getCountByConditions( $table , $langFields , $conditions );
		}
		
		public static function Get( $id , $entityName , $table ){
			
			$datas = self::GetAll( $entityName , $table , array( $table.'.id' => $id ) );
			return $datas[0];
			
		}
		
		public static function Update( WeFlex_Entity_Lang $entity , $table ){
			
			$baseModel = self::_generModel( $table );
			$langModel = self::_generModel( $table.'_lang' );
			
			$id = $entity['id'];
			
			$baseInfo = $entity->getBaseInfo();
			$langInfos = $entity->getLangInfos();
			
			unset( $baseInfo['id'] );
			
			$baseModel->update( $baseInfo , array( 'id' => $id ) );
			
			
			foreach( $langInfos as $lang => $values ){
				
				$langModel->update( $values , array( 'parent' => $id , 'lang' => $lang ) );
			}
			
			return $id;
			
			
		}
		
		public static function Delete(  WeFlex_Entity_Lang $entity , $table  ){
			
			$baseModel = self::_generModel( $table );
			$langModel = self::_generModel( $table.'_lang' );
			
			$id = $entity['id'];
			
			$baseModel->delete( array( 'id' => $id ) );
			$langModel->delete( array( 'parent' => $id ) );
			
		}
		
		
		
		/**
		 * @param unknown_type $table
		 * @return WeFlex_Db_Model
		 */
		private static function _generModel( $table ){
			
			$options = array(
		   			WeFlex_Db::ADAPTER   => WeFlex_Application::GetInstance()->config->db->adapter , 
		   			WeFlex_Db::DATABASE  => WeFlex_Application::GetInstance()->config->db->database , 
		   			WeFlex_Db::HOST 	 => WeFlex_Application::GetInstance()->config->db->host ,
		   			WeFlex_Db::USER 	 => WeFlex_Application::GetInstance()->config->db->user ,
		   			WeFlex_Db::PWD		 =>	WeFlex_Application::GetInstance()->config->db->pwd  ,
		   			WeFlex_Db::TABLE	 => $table
   			);
   			
   			
   			return new WeFlex_Db_Model( $options );
		}
		
		
		private static function _generDataFromDb( $data , $langFields ){
			
			$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
			$data['langInfos'] = array();
			foreach( $langs as $lang ){
				$data['langInfos'][$lang] =  array();
				foreach( $langFields as $langField ){
					$temp = $data[$langField . '_' . $lang];
					$data['langInfos'][$lang][$langField] = $temp;
					unset( $data[$langField . '_' . $lang] );
				}
			}
			
			return $data;
			
		}
		
	
		
		private static function _getAllByConditions( $table , $langFields , $conditions = null , $order = null , $pageNo = null , $pageSize = null   ){
		
			$baseModel = self::_generModel( $table );
			$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
			
			$columns = array();
			
			foreach( $langs as $lang ){
				$table_lang = $table .'_lang'.$lang;
				$baseModel->joinLeft( array( $table_lang => $table .'_lang' ) , $table_lang.'.parent = '.$table.'.id' );
				$baseModel->where( array( $table_lang.'.lang' => $lang ) );
				foreach( $langFields as $langField ){
					$columns []= array( $langField .'_'.$lang => $table_lang .'.'.$langField );
				}
			}
			
			$columns []= $table . '.*';
			
			$baseModel->columns($columns);
			
			if( $conditions ){
				$baseModel->where($conditions);
			}
			
			if( $order ){
				$baseModel->order( $order );
			}
			
			if( $pageNo && $pageSize ){
				$baseModel->limitPage( $pageNo , $pageSize );
			}
			
//			dump($baseModel->assemble());
//			die();
			
			return $baseModel->fetchAll();
		}
		
		private static function _getCountByConditions( $table , $langFields , $conditions = null  ){
		
			$baseModel = self::_generModel( $table );
			$langs = array_keys( WeFlex_Application::GetInstance()->getLangs() );
			
			$columns = array();
			
			foreach( $langs as $lang ){
				$table_lang = $table .'_lang'.$lang;
				$baseModel->joinLeft( array( $table_lang => $table .'_lang' ) , $table_lang.'.parent = '.$table.'.id' );
				$baseModel->where( array( $table_lang.'.lang' => $lang ) );
				foreach( $langFields as $langField ){
					$columns []= array( $langField .'_'.$lang => $table_lang .'.'.$langField );
				}
			}
			
			$columns []= $table . '.*';
			
			$baseModel->columns($columns);
			
			if( $conditions ){
				$baseModel->where($conditions);
			}
			
		
			
			return $baseModel->count();
		}
		
	}
?>