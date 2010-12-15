<?php 
	class WeFlex_File{
		
		protected $_regularPath;
		
		protected $_regularUrl;
		
		
		/**
		 * This interface purpose is to save the $_FILE temp file into formal directory
		 * 
		 * @param String|Integer $index
		 * @param $_FILE $file
		 * @return unknown
		 */
		public function regular( $index , $file ){
			
			
			$regularDir  = $this->_regularPath . '/' .  $index . '/' ;
			$regularImagePath = $regularDir . $file['name'];
			$regularImageUrl  = $this->_regularUrl . '/' . $index . '/' . $file['name'];
			
			$this->_mkDir($regularDir);
			$this->_copy ($file['tmp_name'] , $regularImagePath );
			
			return $regularImageUrl;
			
		}
		
		protected function _getImageFileName( $imageUrl ){
			$fileName = substr($imageUrl,strrpos($imageUrl,'/'));
			$fileName = substr($fileName,1);
			return $fileName;
		}
		
		protected function _mkDir( $dirPath ){			
			if( !is_dir( $dirPath ) ){
				mkdir($dirPath,0777,true);
        		@chmod($dirPath,0777);
			}
			
		}
		
		protected function _copy( $orginalFile , $toFile ){
			copy($orginalFile,$toFile);
	        @chmod($toFile,0777);
		}
		
	}
?>