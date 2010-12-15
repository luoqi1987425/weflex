<?php 
	class WeFlex_Verify
	{
		/**
	     * 生成随即数码并生成图片
	     *
	     */
	    public function GetVerifyCode( $name )
	    {
			header ("Cache-Control: no-cache, must-revalidate"); 
	   		header ("Pragma: no-cache"); 
	        header("content-type: image/png");
	        $randnum=$this->_randomCode(5);
	        WeFlex_Session::Set( $name , $randnum );
	        $image = @imagecreate(55 , 20);
	        $background_color = imagecolorallocate($image , 250 , 250 , 250);
	        $gray_color  = imagecolorallocate($image , 100 , 100 , 100);
	        imagestring ($image, 18, 5, 5, $randnum,$gray_color);
	        imagepng($image);
	        imagedestroy($image);
	    }
		
	    /**
	     * 验证
	     *
	     * @param int $length
	     * @return string
	     */
	    public function VerifyCode( $name , $code )
	    {
		    $sessionCode = WeFlex_Session::Get( $name );
	    	if($code == $sessionCode)
	    	{
	    		return  true;
	    	}
	    	else
	    	{
	    		return false;
	    	}
	    }
	    
	    /**
	     * 生成随即数码
	     * $length为生成数码的长度
	     *
	     * @param int $length
	     * @return string
	     */
	    private function _randomCode($length)
	    {
	        $result = "";
	        $string = "1234567890QWERTYUIPASDFGHJKLZXCVBNM";
	        for ($i = 0 ; $i < $length ; $i++)
	        {
	            $result .= $string[mt_rand(0 , strlen($string) - 1)];
	        }
	        return $result;
	    }
		
	}

?>