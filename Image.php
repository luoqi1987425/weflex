<?php
require_once 'Image/Processor.php';
require_once 'Image/Uploader.php'; 
require_once 'Image/Thumbnail.php'; 

	class WeFlex_Image
	{
		
		protected $_tempFolder = 'temp';
		protected $_tempCropFolder = 'crop';
		protected $_uploadUrl;
		
		/**
		 * array(
		 * 	array( 111 , 222 ),
		 *  array( 333 , 444 ) 
		 * )
		 * 
		 *
		 * @var array
		 */
		protected $_regularImageStandards;
		
		
		/**
		 * get a image by it's size
		 * @return String
		 */
		public static function GetImageUrlBySize($surl,$width,$height) {
	        $durl = substr($surl,0,strrpos($surl,'.')).'_'.$width . 'x' . $height . substr($surl,strrpos($surl,'.'));
	        return $durl;
    	}
    	
    	/**
    	 * Image download
    	 * @return String
    	 *
    	 */
    	public static function Download( $url , $filePath ){
    		
    		if(!$url){
    			return false;
    		}
			
			ob_start();
			readfile( $url );
			$img = ob_get_contents();
			ob_end_clean();
			file_put_contents( $filePath , $img );
//			$fp2=@fopen($filePath, $img) ;
//			fclose($fp2) ;
			
			return $filePath;
 
    	}
    	
    	public function __construct( $uploadUrl = null , $resize = null ){
    		
    		$this->_uploadUrl = $uploadUrl;
    		$this->_regularImageStandards = $resize;
    		
    	}
		
		
		/**
		 * Image upload
		 *
		 * @return String tempUrl
		 */
		public function upload(){
			
	        $options = array();
	        $options['target_path'] = $this->_getTempPath();
	        $options['field_name'] = 'fileField';
	        $options['save_name'] = array('random');
	        $options['allow_type'] = array('jpeg','jpg','gif','bmp','png');
	        $upload = new WeFlex_Image_Uploader($options);
	        $rtn = $upload->upload();
	        
	        if(0!=$rtn){
	        	throw new Exception( "image upload error" );
	        }
	        
	        $file_name = $upload->get_file_name();
	        $rtn =  $this->_getTempUrl() . '/' .  $file_name;
	        return $rtn;
		}
		
		public function cropImage( $tempUrl , $cropParam ){
			
			$imageFileName = $this->_getImageFileName( $tempUrl );
			$tempImagePath = WeFlex_Application::GetInstance()->getPublicPath() . $tempUrl;
			$tempCropImageDir = $this->_getTempPath() . '/' . $this->_tempCropFolder . '/';
			$cropImageUrl = $this->_getTempUrl()	  . '/' . $this->_tempCropFolder . '/' . $imageFileName;
			
			$this->_mkDir($tempCropImageDir);
			//$this->_copy( $tempImagePath , $tempCropImagePath );
			$this->_cropImage( $tempImagePath , $cropParam['x1'] , $cropParam['x2'] , $cropParam['y1'] , $cropParam['y2'] , $tempCropImageDir );
			
			return $cropImageUrl;
		}
		
		/**
		 * change a temp image into a regular image;
		 *
		 * @param String || int $index (image belong to which index( product or else ))
		 * @param String $imageTempUrl
		 * @pram $cropParam array(
		 * 	x1,y1,x2,y2
		 * );
		 * @throws Exception( 'temp url is not exist' )
		 * 
		 */
		public function regular( $index , $tempUrl , $cropParam = null ){
			
			if( !$cropParam ){
				$regularImage = $this->_moveTempToRegular( $index , $tempUrl);
			}else{
				$regularImage = $this->_moveTempToRegularCrop( $index , $tempUrl , $cropParam );
			}
			
			$this->_generStandard($regularImage['path']);
			
			
			return $regularImage['url'];
		}
	
		/**
		 * @return array
		 */
		public function getRegularImageStandards() {
			return $this->_regularImageStandards;
		}
		
		/**
		 * @return unknown
		 */
		public function getRegularPath() {
			return $this->_getRegularPath();
		}
		
		/**
		 * @return unknown
		 */
		public function getRegularUrl() {
			return $this->_getRegularUrl();
		}
		
		/**
		 * @return unknown
		 */
		public function getTempCropDir() {
			return $this->_tempCropFolder;
		}
		
		/**
		 * @return unknown
		 */
		public function getTempPath() {
			return $this->_getTempPath();
		}
		
		/**
		 * @return unknown
		 */
		public function getTempUrl() {
			return $this->_getTempUrl();
		}

		
		
		
	
		
		protected function _moveTempToRegular( $index , $tempUrl ){
			
			$imageFileName = $this->_getImageFileName( $tempUrl );
			
			$tempImagePath = WeFlex_Application::GetInstance()->getPublicPath() . $tempUrl;
			$regularDir  = $this->_getRegularPath() . '/' .  $index . '/' ;
			$regularImagePath = $regularDir . $imageFileName;
			$regularImageUrl  = $this->_getRegularUrl() . '/' . $index . '/' . $imageFileName;
			
			$this->_mkDir($regularDir);
			$this->_copy ($tempImagePath , $regularImagePath );
			
			return array( 'path' => $regularImagePath , 'url' => $regularImageUrl );
			
		}
		
		protected function _moveTempToRegularCrop( $index , $tempUrl , $cropParam ){
			
			$imageFileName = $this->_getImageFileName( $tempUrl );
			$tempImagePath = WeFlex_Application::GetInstance()->getPublicPath() . $tempUrl;
			$regularDir  = $this->_getRegularPath() . '/'. $index . '/' ;
			$regularImagePath = $regularDir . $imageFileName;
			$regularImageUrl  = $this->_getRegularUrl() . '/' . $index . '/' . $imageFileName;
			$this->_mkDir($regularDir);
			
			$this->_cropImage( $tempImagePath , $cropParam['x1'] , $cropParam['x2'] , $cropParam['y1'] , $cropParam['y2'] , $regularDir );
			
			
			return array( 'path' => $regularImagePath , 'url' => $regularImageUrl );
		}
		
		protected function _cropImage( $originalImage , $x1 , $x2  ,$y1 , $y2 , $targetDir ){
			
			list($width, $height, $type, $attr) = getimagesize($originalImage);
			
			$left = $x1;
			$right = $width - $x2;
			$top = $y1;
			$bottom = $height - $y2;
			
			$thumbWidth		= $x2 - $x1;
			$thumbHeight 	= $y2 - $y1;
			
			
			$imageProc = new WeFlex_Image_Thumbnail();
			$imageProc -> Cropimage = array(1, 1, 
											(int)$left,(int)$right,(int)$top,(int)$bottom);
											
				
			$imageProc -> Thumbwidth  = (int)$thumbWidth;
			$imageProc -> Thumbheight  = (int)$thumbHeight;
			$imageProc -> Thumblocation = $targetDir;
			$imageProc -> Thumbprefix = '';
			$imageProc -> Createthumb($originalImage, 'file');
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
		
		protected function _getImageFileName( $imageUrl ){
			$fileName = substr($imageUrl,strrpos($imageUrl,'/'));
			$fileName = substr($fileName,1);
			return $fileName;
		}
		
		protected function _changeFileName( $fileName ){
			
			$fileNames = explode( '.' , $fileName );
			$newName = md5( time() . $fileNames[0] );
			
			return $newName . '.' . $fileNames[1];
			
		}
		
		protected function _generStandard( $regularPath ){
			foreach ( $this->_regularImageStandards as $standard ){
				$this->_generImageFile( $regularPath , $standard[0] , $standard[1] , $standard[2] , $standard[3]);
			}
		}
		
		protected function _generImageFile( $sFile , $width , $height , $backcolor = null , $focus = null ){
			$dFile = substr($sFile,0,strrpos($sFile,'.')).'_'.$width . 'x' . $height . substr($sFile,strrpos($sFile,'.'));
	        WeFlex_Image_Processor::switchpic( $sFile , $dFile , $width , $height , $backcolor , $focus );
	        return $dFile;
		}
		
		
		
		protected function _getTempPath(){
			$tempPath = WeFlex_Application::GetInstance()->config->public_path . $this->_uploadUrl . '/' . $this->_tempFolder;
			return $tempPath;
		}
		
		protected function _getTempUrl(){
			$tempUrl = $this->_uploadUrl . '/' . $this->_tempFolder;
			return $tempUrl;
		}
		
		protected function _getRegularPath(){
			$regularPath = WeFlex_Application::GetInstance()->config->public_path . $this->_uploadUrl;
			return $regularPath;
		}
		
		protected function _getRegularUrl(){
			$regularUrl = $this->_uploadUrl;
			return $regularUrl;
		}
	
	}
?>