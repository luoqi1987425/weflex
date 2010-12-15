<?php

require_once 'Zend/View/Helper/FormElement.php';


class WeFlex_ZendX_View_Helper_FormImageUpload extends Zend_View_Helper_FormElement
{

    public function formImageUpload( $name , $value = null, $attribs = null , $options = null)
    {
        $view = $this->view;
        
        /**
         * generate upload url
         */
        $uploadUrlArray = array( 'action' => $options['uploadAction'] , 'controller' =>$options['uploadController'] , 'module' => $options['uploadModule']);
        
        
        if( $options['uploadParam'] ){
        	foreach( $options['uploadParam'] as $key => $uploadParam ){
        		$uploadUrlArray[$key] = $uploadParam;
        	}
        }
        $uploadUrl = $view->url( $uploadUrlArray , 'default', true);
    	
        $cropImageUrl = WeFlex_Util::GetImageUrlBySize( $value );
//    	if( $value && $this->_isExternalUrl( $value )){
//        	$cropImageUrl = $value;
//        }elseif( $value ){
//        	$cropImageUrl = $view->baseUrl(). $value;
//        }else{
//        	$cropImageUrl = $view->baseUrl(). '/images/_blank.png';
//        }
       
        
		$xhtml = '
		<span id="spanButtonPlaceHolder_'.$name.'"></span>
		<input type="button" id="btnCancel_'.$name.'" style="display:none;" />
		<div id="fsUploadProgress_'.$name.'"></div>
		
		<input type="hidden" name="'.$name.'" id="imageName_'.$name.'"/>
		<p >
			<img id="cropImage_'.$name.'" src="'.$cropImageUrl .'"></img>
		</p>
		';
		
		$xhtml .= '
	<script type="text/javascript">
	
		$(function (){

   			var uploadUrl = "'.$uploadUrl.'";
			var flashUrl  = "'.$view->baseUrl() . $options['flashUrl'].'";
			var buttonImageUrl = "'.$view->baseUrl() . $options['buttonImageUrl'].'";
	
			'.$name.'_swfu = new SWFUpload(
			{
			upload_url	:	uploadUrl,
		    flash_url	:	flashUrl,
	
		    button_placeholder_id	: "spanButtonPlaceHolder_'.$name.'",
		    button_image_url	  	: buttonImageUrl,
		    button_width: "60",
		    button_height: "18",
		    
		
		    file_post_name:"fileField",
		    post_params: {"PHPSESSID" : "<php>echo(session_id());</php>"},
		    file_size_limit : "10 MB",
		    file_types : "*.jpg;*.jpeg;*.png;*.gif;*.bmp",
		    file_types_description : "Image Files",
		    file_upload_limit : 0,
		    file_queue_limit : 0,
		    custom_settings : {
			    progressTarget : "fsUploadProgress_'.$name.'",
			    cancelButtonId : "btnCancel_'.$name.'",
			    complete_handler : onCompleteUpload_'.$name.'
			},
	
	    	// The event handler functions are defined in handlers.js
	    	file_dialog_complete_handler : fileDialogComplete,
	    	upload_start_handler 		 : uploadStart,
	    	upload_progress_handler 	 : uploadProgress,
	    	upload_error_handler 	     : uploadError,
	    	upload_success_handler 		 : uploadSuccess,
	    	upload_complete_handler 	 : uploadComplete,
	    	debug:false
			}
		);
		
		
		
		/**
		 * handlers.js
		 */
		
		function onCompleteUpload_'.$name.'(rst){
			if( rst.error ){
				alert( rst.error );
				return;
			}
			
			
			$( "#cropImage_'.$name.'" ).attr( "src" , "'.$view->baseUrl().'" + rst.imageUrl );
			$( "#imageName_'.$name.'" ).val(rst.imageUrl);
			
			
		}
		
	   
	});
	</script>';
		
       

        return $xhtml;
    }
    
 	private function _isExternalUrl( $url ){
    	
    	if(preg_match("/^http/" ,$url)){
			return true;
		}else{
			return false;
		}
    	
    }
}
