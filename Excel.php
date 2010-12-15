<?php 
	class WeFlex_Excel
	{
         private $header = 
		"<?xml version=\"1.0\" encoding=\"UTF-8\"?\><Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
		
        private $footer = "</Workbook>";

        private $lines = array();

        private $sEncoding;
       
        private $bConvertTypes;
       
        private $sWorksheetTitle;

        public function __construct($sEncoding = 'UTF-8', $bConvertTypes = false, $sWorksheetTitle = 'Table1')
        {
                $this->bConvertTypes = $bConvertTypes;
        		$this->setEncoding($sEncoding);
        		$this->setWorksheetTitle($sWorksheetTitle);
        }

        public function setEncoding($sEncoding)
        {
        		$this->sEncoding = $sEncoding;
        }

        public function setWorksheetTitle ($title)
        {
                $title = preg_replace ("/[\\\|:|\/|\?|\*|\[|\]]/", "", $title);
                $title = substr ($title, 0, 31);
                $this->sWorksheetTitle = $title;
        }

        private function addRow ($array)
        {
       	 		$cells = "";
                foreach ($array as $k => $v):
                        $type = 'String';
                        /*
                        if ( preg_match ("/%$/", $v))
                        {
                        	$type = 'Number';
                        }
                        */
                        if (is_numeric($v))
                        {
                            $type = 'Number';
                        }
                        $v = htmlentities($v, ENT_COMPAT, $this->sEncoding);
                        $cells .= "<Cell><Data ss:Type=\"$type\">" . $v . "</Data></Cell>\n";
                endforeach;
                $this->lines[] = "<Row>\n" . $cells . "</Row>\n";
        }

        public function addArray ($array)
        {
                foreach ($array as $k => $v)
                   $this->addRow ($v);
        }


        public function generateXML ( $filename = 'excel-export')
        {
                //$filename = preg_replace('/[^aA-zZ0-9\_\-]/', '//', $filename);

                $data = stripslashes (sprintf($this->header, $this->sEncoding));
                
                $data .= "\n<Worksheet ss:Name=\"" . $this->sWorksheetTitle . "\">\n<Table>\n";
                
                foreach ($this->lines as $line)
                {
                	$data .=  $line;
                }
                
                $data .= "</Table>\n</Worksheet>\n";
                
                $data .= $this->footer;

                file_put_contents($filename , $data);
        }
		
        public function isGenerateXML ( $filename )
        {
		        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		        header("Content-Disposition: inline; filename=\"" . $filename . ".xls\"");
		
				
		        echo stripslashes ($this->header);
		        
		        echo "\n<Worksheet ss:Name=\"" . $this->sWorksheetTitle . "\">\n<Table>\n";
				
        		foreach ($this->lines as $line)
                {
                	echo $line;
                }
				
				echo "</Table>\n</Worksheet>\n";
		        echo $this->footer;
        }
	}

?>