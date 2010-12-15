<?php 
	class WeFlex_JsCompress{
	

		/**
		 * $paths 是一个array 传入 需要压缩的文件夹或则文件， 按照先后循序进行压缩 
		 * @resultPath 设置压缩后的文件存放路径
		 */
		public static function compress( $paths , $resultPath ){
			
			$queue = self::_queuePaths( $paths );
			self::_compress( $queue , $resultPath);
			
		
		}
		
		private function _compress( $queues , $resultPath ){
			
			$merge_js_body  = '';
			
			foreach( $queues as  $queue){
				$merge_js_body .= file_get_contents( $queue );
			}
			
			file_put_contents( $resultPath , $merge_js_body );	
			
			
		}
		
		
		
		private static function _queuePaths($paths){
			
			$rtn = array();
			
			if( is_string( $paths ) ){
				$paths = array($paths);
			}
			
			foreach( $paths as $path ){
				
				if( !is_dir( $path ) && self::_isJs($path) ){
					$rtn []= $path;
				}elseif( is_dir( $path ) ){
					$dp=dir($path);
					while($file = $dp->read()){
			        	if($file!='.'&&$file!='..'&&$file!='.svn'){
			        		if( !is_dir( $file ) && self::_isJs($file) ){
			        			$rtn []= $path.'/'.$file;
			        		}elseif( is_dir( $path.'/'.$file ) ){
			        			$tmp_rtn = self::_queuePaths( $path.'/'.$file );
			        			$rtn = array_merge( $rtn , $tmp_rtn );
			        		}
			        	}
			        }
			        
			        $dp->close();
					
				}
				
			}
			
			return $rtn;
		}
		
		private static function _isJs( $file ){
			$fileName = substr($file,strrpos($file,'.'));
			$fileName = substr($fileName,1);
			
			if( $fileName != "js" ){
				return false;
			}else{
				return true;
			}
		}
	}

?>