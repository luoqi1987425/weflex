<?php

require_once 'Zend/View/Helper/FormElement.php';


class WeFlex_ZendX_View_Helper_FormDateSelect extends Zend_View_Helper_FormElement
{
	
	/**
	 * $value shoule be 2009-10-25
	 *
	 * @param unknown_type $name
	 * @param unknown_type $value
	 * @param unknown_type $attribs
	 * @param unknown_type $options
	 * @return unknown
	 */
    public function formDateSelect( $name , $value = null, $attribs = null , $options = null)
    {
    	$startYear = $options['startYear'];
    	$endYear = $options['endYear'];
    	$currentDate = split( '-' , $value );
        
        $yearSelect = $this->selectCode( $name.'_year' , 'Year' , $startYear , $endYear , $currentDate[0] );
        $monthSelect = $this->selectCode( $name.'_month' , 'Month' , 1 , 12 , $currentDate[1]) ;
        $dateSelect = $this->selectCode( $name.'_date' , 'Date' , 1 , 31 , $currentDate[2]) ;
    	
		$xhtml = $yearSelect . $monthSelect . $dateSelect;
       
        return $xhtml;
    }
    
    private function selectCode( $name , $type , $start , $end , $currentValue = null ){
    	
    	$html = '<select name="'.$name.'" id="'.$name.'">';
    	
    	$html .= '<option value=0>'.$type.'</option>';
    	
    	for( $i = $start ; $i <=  $end  ;  $i ++){
    		if( $i == $currentValue ){
    			$html .= '<option value='.$i.'   selected="selected">'.$i.'</option>';
    		}else{
    			$html .= '<option value='.$i.'>'.$i.'</option>';
    		}
    		
    	}
    	
    	
    	$html .= '</select>';
    	
    	return $html;
    	
    }
}
