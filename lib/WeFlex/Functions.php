<?php 
	function dump($var, $echo=true,$label=null, $strict=true)
	{
	    $label = ($label===null) ? '' : rtrim($label) . ' ';
	    if(!$strict) {
	        if (ini_get('html_errors')) {
	            $output = print_r($var, true);
	            $output = "<pre>".$label.htmlspecialchars($output,ENT_QUOTES,C('OUTPUT_CHARSET'))."</pre>";
	        } else {
	            $output = $label . " : " . print_r($var, true);
	        }
	    }else {
	        ob_start();
	        var_dump($var);
	        $output = ob_get_clean();
	        if(!extension_loaded('xdebug')) {
	            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
	            $output = '<pre>'
	                    . $label
	                    . htmlspecialchars($output, ENT_QUOTES,C('OUTPUT_CHARSET'))
	                    . '</pre>';
	        }
	    }
	    if ($echo) {
	        echo($output);
	        return null;
	    }else {
	        return $output;
	    }
	}
	
	function C($name='',$value=null) 
	{
	    static $_config = array();
	    if(!is_null($value)) {
	        if(strpos($name,'.')) {
	            $array   =  explode('.',strtolower($name));
	            $_config[$array[0]][$array[1]] =   $value;
	        }else{
	            $_config[strtolower($name)] =   $value;
	        }
	        return ;
	    }
	    if(empty($name)) {
	        return $_config;
	    }
	
	    if(is_array($name)) {
	        $_config = array_merge($_config,array_change_key_case($name));
	        return $_config;
	    }
	    if(strpos($name,'.')) {
	        $array   =  explode('.',strtolower($name));
	        return $_config[$array[0]][$array[1]];
	    }elseif(isset($_config[strtolower($name)])) {
	        return $_config[strtolower($name)];
	    }else{
	        return NULL;
	    }
	}
?>