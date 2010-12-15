<?php

require_once 'Zend/View/Helper/FormElement.php';


class WeFlex_ZendX_View_Helper_FormImageUploadCrop extends Zend_View_Helper_FormElement
{

    public function formImageUploadCrop( $name , $value = null, $attribs = null , $options = null)
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
    	
        
        /**
         * generate crop url
         */
       	$cropUrlArray = array( 'action' => $options['cropAction'] , 'controller' =>$options['cropController'] , 'module' => $options['cropModule']);
        if( $options['cropParam'] ){
        	foreach( $options['cropParam'] as $key => $uploadParam ){
        		$cropUrlArray[$key] = $uploadParam;
        	}
        }
        $cropUrl = $view->url( $cropUrlArray , 'default', true); 
        
        if( $value && $this->_isExternalUrl( $value )){
        	$cropImageUrl = $value;
        }elseif( $value ){
        	$cropImageUrl = $view->baseUrl(). $value;
        }else{
        	$cropImageUrl = $view->baseUrl(). '/images/_blank.png';
        }
        
		$xhtml = '
		<span id="spanButtonPlaceHolder_'.$name.'"></span>
		<input type="button" id="btnCancel_'.$name.'" style="display:none;" />
		<div id="fsUploadProgress_'.$name.'"></div>
		
		<input type="hidden" name="'.$name.'" id="imageName_'.$name.'"/>
		<input type="hidden" name="image_x1_'.$name.'" id="image_x1_'.$name.'" value="" />
		<input type="hidden" name="image_y1_'.$name.'" id="image_y2_'.$name.'" value="" />
		<input type="hidden" name="image_x2_'.$name.'" id="image_x2_'.$name.'" value="" />
		<input type="hidden" name="image_y2_'.$name.'" id="image_y2_'.$name.'" value="" />
		
		<div id="cropDialog_'.$name.'" title="Crop Image Dialog">
				<input type="button" id="cropButton_'.$name.'" value="Submit"><br/>
				<input type="hidden" id="uploadImageUrl_'.$name.'" />
				<img id="uploadImage_'.$name.'"></img>
		</div>
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
	
			var imageAreaSel_'.$name.';
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
		
		/*
		 * cropDialog init
		 */
		$("#cropDialog_'.$name.'").dialog(
				{
				autoOpen : false,
				bgiframe: true,
				width:"auto",
				height: "auto",
				resizable : false,
				position : ["left","top"],
				modal: true
				}
			);
		
		/*
		 * crop Button
		 */
		$("#cropButton_'.$name.'").click(function(){
			$.ajax({
				type: "POST",
				data: "cropImage="+$( "#uploadImageUrl_'.$name.'" ).val()+"&x1="+$("input[name=image_x1_'.$name.']").val()+"&x2="+$("input[name=image_x2_'.$name.']").val()+"&y1="+$("input[name=image_y1_'.$name.']").val()+"&y2="+$("input[name=image_y2_'.$name.']").val(),
				url: "'.$cropUrl.'",
				complete : onCompleteCropImage_'.$name.'
			});
		});
		
		
		/**
		 * handlers.js
		 */
		
		function onCompleteUpload_'.$name.'(rst){
			if( rst.error ){
				alert( rst.error );
				return;
			}
			
			
			var imgCtl = document.getElementById( "uploadImage_'.$name.'" );
			imgCtl.src = "'.$view->baseUrl().'" + rst.imageUrl;
			$( "#uploadImageUrl_'.$name.'" ).val(rst.imageUrl);
			
			$("#cropDialog_'.$name.'").dialog("open");
				imageAreaSel_'.$name.' = $("#uploadImage_'.$name.'").imgAreaSelect({
					instance : true,
					hide : false,
					zIndex : 9999,
					aspectRatio: "1:'.($options['cropHeight']/$options['cropWidth']).'",
					x1: 0, 
					y1: 0, 
					x2: '.$options['cropWidth'].', 
					y2: '.$options['cropHeight'].',
			        onSelectEnd: function (img, selection) {
			            $("input[name=image_x1_'.$name.']").val(selection.x1);
			            $("input[name=image_y1_'.$name.']").val(selection.y1);
			            $("input[name=image_x2_'.$name.']").val(selection.x2);
			            $("input[name=image_y2_'.$name.']").val(selection.y2);
			        }
			    });
			    imageAreaSel_'.$name.'.update();
			
			
		}
		
		function onCompleteCropImage_'.$name.'(response){
			var rst = $.weflex.ajax.prase(response);
			if( rst.status ){
				imageAreaSel_'.$name.'.setOptions({hide : true }); 
				$( "#cropImage_'.$name.'" ).attr( "src" , "'.$view->baseUrl().'" + rst.info.imageUrl );
				$( "#imageName_'.$name.'" ).val(rst.info.imageUrl);
				$( "#cropDialog_'.$name.'").dialog("close");
				
			}
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
