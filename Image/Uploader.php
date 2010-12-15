<?php

/**
 * 文件上传处理类
 * @author daniel
 * 
 * @see http://oa.jiyiri.com/wiki/index.php?title=FileUploader%E5%B7%A5%E5%85%B7%E7%B1%BB
 * 
 * @desc 支持的Option极其说明如下
 	target_path 保存文件到哪个路径
    field_name 表单项名称
    allow_type 允许的扩展名
    allow_max_size 允许的最大文件大小，设为0为不care
    save_name 目标文件名的处理方式，可以为如下:array('random'),array('original'),array('assign','assign_name'); 
 */
class WeFlex_Image_Uploader
{
    private static $ERROR_INFOS = array(
        'UNKNOW'             => array(-1,'未知错误'),
        'INVAILD_TYPE'       => array(-2,'未允许类型'),
        'TOO_LARGE'          => array(-3,'文件太大'),
        'ERROR_FILE_NAME'    => array(-4,'产生文件名出错'),
        'UPLOAD_FAIL'        => array(-5,'上传失败'),
        'PATH_NOT_EXISTS'	 => array(-6,'目录不存在'),
        'FAIL_CREATE_PATH'	 => array(-7,'建立目录失败')
    );
    
    private static  $DEFAULT_OPTIONS = array(
            'allow_type'=>array(),
            'allow_max_size'=>0,
            'save_name'=>array('random')
    );
    
    private $_options;
    
    private $_err_code;
    private $_target_name;
    
    public function __construct($options)
    {
        $this->_init();
        $this->_set_options($options);
    }
    
    public function upload($field_name='',$options=array())
    {
        $this->_init_state();
        $this->_set_options($options);
        if($field_name)
        {
            $this->_options['field_name'] = $field_name;
        }
        
        $this->_check_valid();
        $this->_process_target_path();
        $this->_process_target_name();
        if ($this->_err_code < 0) return $this->_err_code;
        $this->_real_process();
        return $this->_err_code;
    }
    public function get_file_name()
    {
        return $this->_target_name;
    }
    public function get_errmsg($err_code=null)
    {
        if(null==$err_code)
        {
            $err_code = $this->_err_code;
        }
        
        foreach(self::$ERROR_INFOS as $error)
        {
            if($error[0]==$err_code)
            {
                return $error[1];
            }
        }
    }
    private function _init()
    {
        $this->_init_state();
        $this->_init_options();
    }
    private function _init_state()
    {
        $this->_err_code = 0;
    }
    private function _init_options()
    {
        
        $this->_options = self::$DEFAULT_OPTIONS;

    }
    private function _set_options($options)
    {
        $support_parameters = array('target_path','field_name','allow_type','allow_max_size','save_name');
        foreach($options as $option_key=>$option_val)
        {
            if(in_array($option_key,$support_parameters))
            {
                $this->_options[$option_key] = $option_val;
            }
            else
            {
                throw new Exception(sprintf('not support option key:%s',$option_key));
            }
        }
    }
    
    private function _set_err($name)
    {
        $error_infos = self::$ERROR_INFOS;
        $this->_err_code = $error_infos[$name][0];
    }
    

    
    private function _check_valid()
    {
        $is_valid = true;
        $field_name = $this->_options['field_name'];
        $allow_types = $this->_options['allow_type'];
        $allow_max_size = $this->_options['allow_max_size'];
        $the_file = $_FILES[$field_name];
        $the_file_name = $the_file['name'];
        $the_file_size = $the_file['size'];
        $the_file_type = $this->_get_file_type_name($the_file_name);
        
        if($allow_types && is_array($allow_types) && count($allow_types)>0)
        {
            if(!in_array($the_file_type,$allow_types))
            {
                $this->_set_err('INVAILD_TYPE');
            }
        }
        
        if($allow_max_size)
        {
            if($the_file_size > $allow_max_size)
            {
                $this->_set_err('TOO_LARGE');
            }

        }
        
        return $is_vaild;
    }
    
    private function _process_target_path()
    {
        $path = $this->_options['target_path'];
        if (!file_exists($path)) {
            $this->_mkdir($path);
        }
    }
    
    private function _process_target_name()
    {
        $file_name = '';
        $save_name = $this->_options['save_name'];
        switch($save_name[0])
        {
            case 'random': $file_name = substr(md5(microtime()),0,16) .'.'. $this->_get_file_type_name($_FILES[$this->_options['field_name']]['name']); ;break;
            case 'original':$file_name = $the_file = $_FILES[$this->_options['field_name']]['name'];break;
            case 'assign':$file_name = $save_name[1];break;
        }
        $file_name = $this->_sanitize_filename($file_name);
        if(!$file_name)
        {
            $this->_set_err('ERROR_FILE_NAME');
        }
        $this->_target_name = $file_name;
    }
    
    private function _real_process()
    {
        $file_path = $this->_options['target_path'];
        if ($file_path[strlen($file_path)-1] != '/') {
            $file_path .= '/'; 
        }
        $file_path .= $this->_target_name;
        $tmp_name = $_FILES[$this->_options['field_name']]['tmp_name'];
        if (!@move_uploaded_file($tmp_name, $file_path)) {
            $this->_set_err('UPLOAD_FAIL');
        }
        
        @chmod($file_path,0777);
        return $this->_err_code;
    }
    
    private function _get_file_type_name($file_name)
    {
        $extend = pathinfo($file_name);
        $extend = strtolower($extend["extension"]);
        return $extend;
    }
    
    private function _mkdir($newdir)
    {
        $dirary = explode("/", $newdir);
        $real_dir = '';
        foreach ($dirary as $d) {
            $real_dir = $real_dir . $d . "/";
            if (! is_dir($real_dir)) 
            {
                mkdir($real_dir, 0777);
                chmod($real_dir,0777);
            }
        }
        return $real_dir;
    }
    
    private function _sanitize_filename( $sNewFileName )
    {
    	$sNewFileName = stripslashes( $sNewFileName ) ;   
    	$sNewFileName = preg_replace( '/\\.(?![^.]*$)/', '_', $sNewFileName ) ;
    	$sNewFileName = preg_replace( '/\\\\|\\/|\\||\\:|\\?|\\*|"|<|>|[[:cntrl:]]/', '_', $sNewFileName ) ;
    
    	return $sNewFileName ;
    }
    
}
?>