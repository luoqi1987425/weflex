<?php
/*
 * Make Connection and Manipulate the database. 
 * this can create a model which is direct to Table in Database , 
 * and we can use this model instance to manipulate the database , 
 * such as insertData , del , update , search , or query some sql , 
 * and will get a data array for response.
 */


require_once 'Db/Model.php';

	class WeFlex_Db
	{
		
				
		const HOST 		= 'host';
		const ADAPTER	= 'adpter';
		const USER		= 'username';
		const PWD		= 'password';
		const DATABASE	= 'database';
		const TABLE		= 'table';
		
		const TABLE_PRIMARY_KEY	=	'primary_key';
		

		
		
		//ADPTER
		const ADAPTER_MYSQL = 'mysql';
		
		
		/**
		 * Create A Model
		 * Example:
		 $options = array(
   			WeFlex_Db::ADAPTER   => WeFlex_Db::ADAPTER_MYSQL , 
   			WeFlex_Db::DATABASE  => 'test' , 
   			WeFlex_Db::HOST 	 => '127.0.0.1' ,
   			WeFlex_Db::USER => 'jiyiri' ,
   			WeFlex_Db::PWD	=>	'jiyiri' ,
   			WeFlex_Db::TABLE		=>  'comment'
   		);
   		
   		$model = WeFlex_Db::CreateModel( $options );
   		$data = $model->find( 1 );
		 * 
		 *
		 * @param array $option
		 * @return WeFlex_Db_Model
		 */
		public static function CreateModel( $options ){
			return new WeFlex_Db_Model( $options );
		}
	}
?>