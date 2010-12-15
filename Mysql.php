<?php 
	class WeFlex_Mysql{
		
		private $_bin;
		private $_username;
		private $_password;
		
		
		function __construct( $bin , $username , $password ){
			
			$this->_bin 	 = $bin;
			$this->_username = $username;
			$this->_password = $password;
			
		}
		
		public function mysqldump( $folder , $database ){
			
			$time 	  = date('ymd', time());
			$filename = $database.'_'.$time.'.gz';
			
			$command = $this->_bin . '/mysqldump -u'.$this->_username.' -p'.$this->_password.' --default-character-set=utf8 --opt --extended-insert=false \--triggers -R --hex-blob -x '.$database.' | gzip > '.$folder.'/'.$filename;
			
			shell_exec($command);
			
			return $filename;
		}
		
		
	}
?>