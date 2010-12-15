<?php 

class WeFlex_Imagic{
	
	/**
	 * if num not exist will throw Exception
	 *
	 * @param unknown_type $originalPdfPath
	 * @param unknown_type $targetFolder
	 * @param unknown_type $format
	 * @param unknown_type $num
	 * @param unknown_type $width
	 * @param unknown_type $height
	 * @return unknown
	 */
	public static function ConvertPdf2Image( $originalPdfPath , $targetFolder , $format , $num = null , $width = null , $height = null ){
		
		//check if pdf
		
		//check format
		
		
		$originalPdfFile = WeFlex_Util::GetFileName( $originalPdfPath );
		$rtn = array();
		
		
		for( $i = 0 ; $i < $num ; $i ++ ){
			
			$originalPdfTemp = $originalPdfPath . '['.$i.']';
			$tempImagePath = $targetFolder . '/' .  substr($originalPdfFile,0,strrpos($originalPdfFile,'.')) . "_" .$i."." . $format;
			
			
			if( $width || $height ){
				$resize = " -resize ";
			}
			
			if( $width && !$height ){
				$resize .= $width . "x";
			}elseif( !$width && $height ){
				$resize .= "x" . $height;
			}elseif( $width && $height ){
				$resize .= $width . "x" . $height;
			}
			
			$cmd = 'convert -colorspace rgb  '.$resize . '  ' . $originalPdfTemp.' '.$tempImagePath;
			exec( $cmd );
			
			$rtn []= $tempImagePath;
			
		
		}
	
		
		return $rtn;
		
		
	}
	
	public function ConvertAllPdf2Image($originalPdfPath , $targetFolder , $format , $width = null , $height = null){
		
		
		$originalPdfFile 	= WeFlex_Util::GetFileName( $originalPdfPath );
		$tempImagePath 		= $targetFolder . '/' .  substr($originalPdfFile,0,strrpos($originalPdfFile,'.')) .".". $format;
		$tempImagePathPre 	= $targetFolder . '/' .  substr($originalPdfFile,0,strrpos($originalPdfFile,'.'));
		$originalPdfTemp 	= $originalPdfPath;
		
		$rtn = array();
		
		if( $width || $height ){
			$resize = " -resize ";
		}
			
		if( $width && !$height ){
			$resize .= $width . "x";
		}elseif( !$width && $height ){
			$resize .= "x" . $height;
		}elseif( $width && $height ){
			$resize .= $width . "x" . $height;
		}
		
		$cmd = 'convert -colorspace rgb  '.$resize . '  ' . $originalPdfTemp.' '.$tempImagePath;
		exec( $cmd );
		
		$i = 0;
		while( file_exists( $tempImagePathPre . '-' . $i . '.' . $format ) ){
			$rtn []= $tempImagePathPre . '-' . $i . '.' . $format;
			$i++;
		}
		
		return $rtn;
		
	}
	
}

?>