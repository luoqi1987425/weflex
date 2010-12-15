<?php
class WeFlex_Pagination
{
	
	const DEFAULT_PAGENO_KEY = 'pageNo';
	const DEFAULT_CLASS_NAME = 'pagination';


    /**
     +----------------------------------------------------------
     * 列表每页显示行数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $listRows ;


    /**
     +----------------------------------------------------------
     * 分页总页面数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $totalPages  ;

    /**
     +----------------------------------------------------------
     * 总行数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $totalRows  ;

    /**
     +----------------------------------------------------------
     * 当前页数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $nowPage    ;
    
    /**
     * @var url key for pageNo like  ..../pageNo/1
     */
    protected $pageNoKey;
    
    /**
     * ul className
     */
    protected $className;
    
    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $zendRequest ;
    
    /**
     * @var Zend_View_Abstract
     */
    protected $zendView;
    
    
    protected $router;

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示的个数
     * @param array $request  zend request
     * @param array $view  	  zend view
     * @param array $className  ul 的class name
     * @param string pagenoKey  在 url 里边 page_no param
     +----------------------------------------------------------
     */
    public function __construct( $totalRows , $listRows , Zend_Controller_Request_Abstract $request , Zend_View_Abstract $view , $router = null , $className = '' , $pageNoKey = null)
    {
        $this->totalRows = $totalRows;
        $this->listRows  = $listRows;
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->zendRequest = $request;
        $this->zendView    = $view;
        $this->router	   = $router;
        
		
        /**
         * sepcify the page no key
         */
		if( !$pageNoKey ){
			$this->pageNoKey = self::DEFAULT_PAGENO_KEY;
		}else{
			$this->pageNoKey = $pageNoKey;
		}
		
		$this->_setClassName($className);
		
		/**
		 * get now page no
		 */
		$this->_getNowPage();
      
  
    }
    
    /**
     +----------------------------------------------------------
     * 分页显示
     * 用于在页面显示的分页栏的输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function show(){

        if(0 == $this->totalRows) return '';
        
        $upPage   = '';
        $downPage = '';
     
        //上下翻页字符串
        $upRow   = $this->_getNowPage() - 1;
        $downRow = $this->_getNowPage() + 1;
        if ($upRow>0){
            $upPage = '<li class="item prev_item"><a href="'.$this->_generUrl($upRow).'" title="'.$this->zendView->_('previous').'">'.$this->zendView->_('previous').'</a></li>';
        }

        if ($downRow <= $this->totalPages){
            $downPage='<li class="item next_item"><a href="'.$this->_generUrl($downRow).'" title="'.$this->zendView->_('next').'">'.$this->zendView->_('next').'</a></li>';
        }

        $pagecount = $this->totalPages;
        $curpage   = $this->_getNowPage();
        $linkPage='';
        
        //第一种情况:小于11页
        if($pagecount<11)
        {
            //echo('x1x');
            //显示全部的链接
            for($i=1;$i<=$pagecount;$i++)
            {
                $linkPage .= $this->makeItemHtml($i,$curpage);
            }
        }
        //第二种情况:大于11页,当前页在前5页
        if($pagecount>11 && $curpage<=6)
        {
            //echo('x2x');
            //前9页照常显示
            for($i=1;$i<=9;$i++)
            {
                $linkPage .= $this->makeItemHtml($i,$curpage);
            }
            //加...
            $linkPage.='<li>...</li>';
            //加后2页
            $linkPage .= $this->makeItemHtml(($pagecount-1),$curpage);
            $linkPage .= $this->makeItemHtml(($pagecount),$curpage);
        }
        //第三种情况:大于11页，当前页不在前后6页
        if($pagecount>11 && $curpage>6 && $curpage<=$pagecount-6)
        {
            //echo('x3x');
            //加前2页
            $linkPage .= $this->makeItemHtml(1,$curpage);
            $linkPage .= $this->makeItemHtml(2,$curpage);
            //加...
            $linkPage.='<li>...</li>';
            //加当前页前后各2页
            for($i=$curpage-2;$i<=$curpage+2;$i++)
            {
                $linkPage .= $this->makeItemHtml($i,$curpage);
            }
            //加...
            $linkPage.='<li>...</li>';
            //加后两页
            $linkPage .= $this->makeItemHtml(($pagecount-1),$curpage);
            $linkPage .= $this->makeItemHtml(($pagecount),$curpage);
        }
        //第四种情况:大于11页，且当前页在最后6页
        if($pagecount>11 && $curpage>$pagecount-6)
        {
            //echo('x4x');
            //加前2页
            $linkPage .= $this->makeItemHtml(1,$curpage);
            $linkPage .= $this->makeItemHtml(2,$curpage);
            //加...
            $linkPage.='<li>...</li>';
            //加最后九页
            for($i=$pagecount-9;$i<=$pagecount;$i++)
            {
                $linkPage .= $this->makeItemHtml($i,$curpage);
            }
        }
        
        $pageStr = '<div class="'.$this->className.'" ><ul>'.$upPage.$linkPage.$downPage.'</ul><div class="clear" ></div></div>';

        return $pageStr;
    }
    
	private function _getNowPage(){
    	
    	$nowPage = $this->zendRequest->getParam( $this->pageNoKey );
    	if( empty( $nowPage ) ){
    		$nowPage = 1;
    		$this->_setNowPage( $nowPage );
    	}else if( !empty($this->totalPages) && $nowPage > $this->totalPages ){
    		$nowPage = $this->totalPages;
    		$this->_setNowPage( $nowPage );
    	}
    	return intval( $nowPage );
    }
    
    private function _setNowPage( $nowPage ){
    	$this->zendRequest->setParam( $this->pageNoKey , $nowPage );
    }
    
    private function _generUrl( $pageNo = null ){
    	if( $pageNo && !$this->router ){
    		$requestParams = $this->zendRequest->getParams();
    		$requestParams[$this->pageNoKey] = $pageNo;
    		return $this->zendView->url($requestParams , null , true);
    	}else{
    		$requestParams = array();
    		$requestParams[$this->pageNoKey] = $pageNo;
    		return $this->zendView->url($requestParams , $this->router , true);
    	}
    	
    }
    
    private function _setClassName( $className = null ){
    	
    	if( !$className ){
    		$className = self::DEFAULT_CLASS_NAME;
    	}
    	$this->className = $className;
    	
    }
    
    
    private function makeItemHtml($i,$curpage)
    {
            if($i==$curpage)
            {
                $rtn='<li class="item current_item"><a href="'.$this->_generUrl( $i ).'" >'.$i.'</a></li>';
            }
            else
            {
                $rtn='<li class="item"><a href="'.$this->_generUrl( $i ).'" >'.$i.'</a></li>';
            }
            return $rtn;
    }

}//类定义结束
?>