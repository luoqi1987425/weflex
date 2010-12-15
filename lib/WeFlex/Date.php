<?php 

/**
 * Todo timezone set
 */

/**
 * This module is Data format module , We can build a instance of Date Class , 
 * we can config this instance year , month , 
 * and this instance supplies us a cool api to get date information such as year month and it��s timestamp. 
 * If u want to get now time instance , so u should set option null.
 *
 */
	class WeFlex_Date
	{
		
		const YEAR	=	'year';
		const MONTH =	'month';
		const DATE	=	'date';
		const HOUR	=	'hour';
		const MINUTE =	'minute'; 
		const SECOND = 	'second';
		
		private $_year;
		
		private $_month;
		
		private $_date;
		
		private $_hour;
		
		private $_minute;
		
		private $_second;
		
		
		/*
		 *	when new the  WeFlex_Date instance , you can set a configure option in construct method,
		 *  this option param contains the year month data .. information , if you set this option is null , 
		 *  it't will use now as the time.  if the option is a integer , we will see it as timestamp.
		 *  Example:
		   $options = array(
				WeFlex_Date::YEAR => 2010 , 
				WeFlex_Date::MONTH => 3 ,
				WeFlex_Date::DATE => 11,
				WeFlex_Date::HOUR => 7 , 
				WeFlex_Date::MINUTE => 0 ,
				WeFlex_Date::SECOND => 0 
			);
			$date = new WeFlex_Date($options);
			echo $date->getYear();  //2010
			echo $data->getTimeStamp();
		 * 
		 * 	
		 */
		function __construct( $options = null ){
			$this->set( $options );
		}
		
		
		public function set( $options = null ){
			
			//if is array
			//if is int
			//if is null
			//else throw exception( 'config option is not supported' );
			
			if( is_array( $options ) ){
				//TODO if options does not contains all how to do 
				$this->_setDate( 
					$options[WeFlex_Date::YEAR] ,
					$options[WeFlex_Date::MONTH] ,
					$options[WeFlex_Date::DATE] ,
					$options[WeFlex_Date::HOUR] ,
					$options[WeFlex_Date::MINUTE] ,
					$options[WeFlex_Date::SECOND] 
				);
			}
			else if( $options ){
				$this->_setTimeStamp( $options );
			}
			else if( !$options ){
				$this->_setTimeStamp( time() );
			}else{
				throw new Exception( 'config option is not supported' ); 
			}
			
			
			
			
				
		}
		
		public function getYear(){
			return $this->_year;	
		}
		
		public function getMonth(){
			return $this->_month;
		}
		
		public function getHour(){
			return $this->_hour;
		}
		
		public function getMinute(){
			return $this->_minute;
		}
		
		public function getSecond(){
			return $this->_second;
		}
		
		public function getDate(){
			return $this->_date;
		}
		
		Public function getTimeStamp(){
			return mktime($this->_hour,
						  $this->_minute,
						  $this->_second,
						  $this->_month,
						  $this->_date,
						  $this->_year);
		}
		
		
		private function _setDate( $year , $month , $date , $hour , $minute , $second ){
			
			$this->_year	=	$year;
			$this->_month	=	$month;
			$this->_date	=	$date;
			$this->_hour	=	$hour;
			$this->_minute	=	$minute;
			$this->_second	=	$second;
			
		}
		
		private function _setTimeStamp( $timeStamp ){
			
			//see more for http://php.net/manual/en/function.date.php
			
			$this->_year	=	intval( date("Y",$timeStamp) );
			$this->_month	=	intval( date("n",$timeStamp) );
			$this->_date	=	intval( date("d",$timeStamp) );
			$this->_hour	=	intval( date("G",$timeStamp) );
			$this->_minute	=	intval( date("i",$timeStamp) );
			$this->_second	=	intval( date("s",$timeStamp) );
			
		}
		

	}
?>