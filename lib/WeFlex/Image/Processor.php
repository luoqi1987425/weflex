<?php
/*---------------------------------------------------------------
 功能: 图片操作类
 作者: ice_berg16(寻梦的稻草人)
 -----------------------------------------------------------------*/

class WeFlex_Image_Processor
{

	public static function cutpic($sourFile, $newName, $x, $y, $width, $height)
	{
		$imageInfo = self::getInfo($sourFile);
        switch ($imageInfo["type"])
        {
            case 1: //gif
                $img = imagecreatefromgif($sourFile);
                break;
            case 2: //jpg
                $img = imagecreatefromjpeg($sourFile);
                break;
            case 3: //png
                $img = imagecreatefrompng($sourFile);
                break;
            default:
                return 0;
                break;
        }
        
        if (!$img)
        return 0;
        
        
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
        {
            $new = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($new, 255, 255, 255);
			imagefill($new, 0, 0, $white);
            ImageCopyResampled($new, $img, 0, 0, $x, $y, $width, $height, $width, $height);
        }
        else
        {
            $new = imagecreate($width, $height);
            ImageCopyResized($new, $img, 0, 0, $x, $y, $width, $height, $width, $height);
        }
        
        if (file_exists($newName)) unlink($newName);
        $maketype = strtolower(substr(strrchr($newName,"."),1));
        switch($maketype)
        {
            case "jpg": ImageJPEG($new, $newName);break;
            case "gif" : ImageGIF($new, $newName);break;
            case "png" : ImagePNG($new, $newName);break;
            case "wbmp" : ImageWBMP($new, $newName);break;
            default: ImageJPEG($new, $newName);
        }
        ImageDestroy($new);
        ImageDestroy($img);
        chmod($newName,0777);
        return $newName;
	}
	
    //==========================================
    // 函数: switchpic($sourFile,$width=128,$height=128)
    // 功能: 转换图片格式,输出指定大小.
    // 参数: $sourFile 图片源文件
    // 参数: $newName 生成的文件URL
    // 参数: $width 生成缩略图的宽度
    // 参数: $height 生成缩略图的高度
    //   $backcolor = array( 111,111,111 ) 三原色 
    // 返回: 0 失败 成功时返回生成的图片路径
    //==========================================
    public static function switchpic($sourFile , $newName , $width=128,$height=128 , $backcolor = null , $focus = null )
    {
    	$dst_width = $width;
    	$dst_height = $height;
        $imageInfo = self::getInfo($sourFile);
        switch ($imageInfo["type"])
        {
            case 1: //gif
                $img = imagecreatefromgif($sourFile);
                break;
            case 2: //jpg
                $img = imagecreatefromjpeg($sourFile);
                break;
            case 3: //png
                $img = imagecreatefrompng($sourFile);
                break;
            default:
                return 0;
                break;
        }
        if (!$img)
        return 0;

        //$width  = ($width > $imageInfo["width"]) ? $imageInfo["width"] : $width;
        //$height = ($height > $imageInfo["height"]) ? $imageInfo["height"] : $height;
        $srcW = $imageInfo["width"];
        $srcH = $imageInfo["height"];
        /*
        if ($srcW * $width > $srcH * $height)
        $height = round($srcH * $width / $srcW);
        else
        $width = round($srcW * $height / $srcH);
        */
    	if( $focus == 'width' ){
        	$height 	= round($srcH * $width / $srcW);
	        $dst_height = $height;
        	
        }elseif( $focus == 'height' ){
        	$width 	   = round($srcW * $height / $srcH);
	        $dst_width = $width;
        	
        }else{
        	if ($srcW / $width > $srcH / $height)
	        $height = round($srcH * $width / $srcW);
	        else
	        $width = round($srcW * $height / $srcH);
        }
       

        if (function_exists("imagecreatetruecolor")) //GD2.0.1
        {
            $new = imagecreatetruecolor($dst_width, $dst_height);
            $offset_x = ($dst_width-$width) / 2;
            $offset_y = ($dst_height-$height) / 2;
            $backcolor = self::_getbackcolor( $new , $backcolor);
			imagefill($new, 0, 0, $backcolor);
            ImageCopyResampled($new, $img, $offset_x, $offset_y, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
        }
        else
        {
            $new = imagecreate($dst_width, $dst_height);
            $offset_x = ($dst_width-$width) / 2;
            $offset_y = ($dst_height-$height) / 2;
            ImageCopyResized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
        }
        //*/
        if (file_exists($newName)) unlink($newName);
        $maketype = strtolower(substr(strrchr($newName,"."),1));
        switch($maketype)
        {
    
            case "jpg": ImageJPEG($new, $newName , 100);break;
            case "gif" : ImageGIF($new, $newName);break;
            case "png" : ImagePNG($new, $newName , 9);break;
            case "wbmp" : ImageWBMP($new, $newName);break;
            default: ImageJPEG($new, $newName);
        }
        ImageDestroy($new);
        ImageDestroy($img);
        chmod($newName,0777);
        return $newName;



    }
    
    //==========================================
    // 函数: waterMark($sourFile, $text)
    // 功能: 给图片加水印
    // 参数: $sourFile 图片文件名
    // 参数: $text 文本数组(包含二个字符串)
    // 返回: 1 成功 成功时返回生成的图片路径
    //==========================================
    public static function waterMark($sourFile, $text)
    {
        $fontName = "1900805.ttf";
        $imageInfo = self::getInfo($sourFile);
        switch ($imageInfo["type"])
        {
            case 1: //gif
                $img = imagecreatefromgif($sourFile);
                break;
            case 2: //jpg
                $img = imagecreatefromjpeg($sourFile);
                break;
            case 3: //png
                $img = imagecreatefrompng($sourFile);
                break;
            default:
                return 0;
                break;
        }
        if (!$img)
        return 0;

        $width  = $imageInfo["width"];
        $height = $imageInfo["height"];
        $srcW = $imageInfo["width"];
        $srcH = $imageInfo["height"];
        if ($srcW * $width > $srcH * $height)
        $height = round($srcH * $width / $srcW);
        else
        $width = round($srcW * $height / $srcH);
        if (function_exists("imagecreatetruecolor")) //GD2.0.1
        {
            $new = imagecreatetruecolor($width, $height);
            ImageCopyResampled($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
        }
        else
        {
            $new = imagecreate($width, $height);
            ImageCopyResized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
        }
        $white = imageColorAllocate($new, 255, 255, 255);
        $black = imageColorAllocate($new, 0, 0, 0);
        $alpha = imageColorAllocateAlpha($new, 230, 230, 230, 40);
        //$rectW = max(strlen($text[0]),strlen($text[1]))*7;
        ImageFilledRectangle($new, 0, $height-26, $width, $height, $alpha);
        ImageFilledRectangle($new, 13, $height-20, 15, $height-7, $black);
        //ImageTTFText($new, 4.9, 0, 20, $height-14, $black, $fontName, $text[0]);
        ImageTTFText($new, 4.9, 0, 20, $height-6, $black, $fontName, $text);
        //*/
        ImageJPEG($new, $sourFile);
        ImageDestroy($new);
        ImageDestroy($img);

    }

    public static function waterpic($logopic , $photopic , $maxWidth = 500 , $maxHeight = 300)
    {
        $imageInfo = self::getInfo($logopic);
        switch ($imageInfo["type"])
        {
            case 1: //gif
                $img = imagecreatefromgif($logopic);
                break;
            case 2: //jpg
                $img = imagecreatefromjpeg($logopic);
                break;
            case 3: //png
                $img = imagecreatefrompng($logopic);
                break;
            default:
                return 0;
                break;
        }
        if (!$img)
        return 0;

        $width = $srcW = $imageInfo["width"];
        $height = $srcH = $imageInfo["height"];
        $new = ImageCreateFromJpeg($photopic);
        ImageAlphaBlending($new, true);
        ImageCopy($new, $img, 0, 0, 0, 0, $width, $height);

        ImageJPEG($new, $photopic);
        ImageDestroy($new);
        ImageDestroy($img);
    }

    public static function getInfo($file)
    {
        $data = getimagesize($file);
        $imageInfo["width"] = $data[0];
        $imageInfo["height"]= $data[1];
        $imageInfo["type"] = $data[2];
        $imageInfo["name"] = basename($file);
        //$imageInfo["size"]  = filesize($file);
        return $imageInfo;
    }
    
    private static function _getbackcolor( $new , $backcolor = null ){
    	
    	$white = imagecolorallocate($new, 255, 255, 255);
        $black = imagecolorallocate($new, 0, 0, 0);
    	
    	if( !$backcolor ){
    		return $white;
    	}
    	
    	if( $backcolor == 'black' ){
    		return $black;
    	}
    	
    	return imagecolorallocate($new , $backcolor[0] , $backcolor[1] , $backcolor[2] );
    	  
    	
    }
}
?>
