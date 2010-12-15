<?php
require_once 'Entity/Lang/DbHelper.php';
require_once 'Entity/Lang/Helper.php';
 
	class WeFlex_Entity implements Iterator , ArrayAccess{
	
		 /**
	      * 保存对象的数组
	      *
	      * @var array
	      * @access private
	      */
	    protected $_coll;
	    
	    
	    protected $_is_valid;
	    
	    /**
	     * this list of offset will be ban set function
	     *
	     * @var unknown_type
	     */
	    protected $_banSetList = array();
		
	    /**
	     * the list of offset will ban get function
	     *
	     * @var unknown_type
	     */
	    protected $_banGetList = array();
		
	    /**
	     * the list will need to validate type
	     * array( 'email' => WeFlex_Validate::Email_Address )
	     */
		protected $_typeValidateList = array();
		
		/**
	     * the list will need to validate type
	     * array( 'email' , 'name' , 'age' )
	     */
		protected $_emptyValidateList = array();
		
		/**
		 * the list will need to validate the length
		 * array( 'password' => array( 5 , 12 ) )
		 */
		protected $_lengthValidateList = array();
	    
	    
		 /**
	      * 构造函数
	      *
	      * @param string $type 集合对象类型
	      */
	    function __construct( array $coll )
	    {
	        $this->_coll = $coll;
	    }
	    
	    function reConstruct( array $coll ){
	    	 $this->_coll = $coll;
	    }
	    
	    function toArray(){
	    	return $this->_coll;
	    }
	    
	    function addColumn( $key , $value ){
	    	$this->_coll[$key] = $value;
	    }
	    
		/**
	     * 检查指定索引的对象是否存在，实现 ArrayAccess 接口
	     *
	     * @code php
	     * echo isset($coll[1]);
	     * @endcode
	     *
	     * @param mixed $offset
	     *
	     * @return boolean
	     */
	    function offsetExists($offset)
	    {
	        return isset($this->_coll[$offset]);
	    }
	
	    /**
	     * 返回指定索引的对象，实现 ArrayAccess 接口
	     *
	     * @code php
	     * $item = $coll[1];
	     * @endcode
	     *
	     * @param mixed $offset
	     *
	     * @return mixed
	     */
	    function offsetGet($offset)
	    {
	        
	    	return $this->_getOffset( $offset );
	        
	    }
	
	    /**
	     * 设置指定索引的对象，实现 ArrayAccess 接口
	     *
	     * @code php
	     * $coll[1] = $item;
	     * @endcode
	     *
	     * @param mixed $offset
	     * @param mixed $value
	     */
	    function offsetSet( $offset , $value)
	    {
	        $this->_setOffset( $offset , $value  );
	    }
	
	    /**
	     * 注销指定索引的对象，实现 ArrayAccess 接口
	     *
	     * @code php
	     * unset($coll[1]);
	     * @endcode
	     *
	     * @param mixed $offset
	     */
	    function offsetUnset($offset)
	    {
	        unset($this->_coll[$offset]);
	    }
	    
	    
		/**
	     * 返回当前位置的对象，实现 Iterator 接口
	     *
	     * @return mixed
	     */
	    function current()
	    {
	        return current($this->_coll);
	    }
	
	    /**
	     * 返回遍历时的当前索引，实现 Iterator 接口
	     *
	     * @return mixed
	     */
	    function key()
	    {
	        return key($this->_coll);
	    }
	
	    /**
	     * 遍历下一个对象，实现 Iterator 接口
	     */
	    function next()
	    {
	        $this->_is_valid = (false !== next($this->_coll));
	    }
	
	    /**
	     * 重置遍历索引，实现 Iterator 接口
	     */
	    function rewind()
	    {
	        $this->_is_valid = (false !== reset($this->_coll));
	    }
	    
	    
		/**
	     * 判断是否是调用了 rewind() 或 next() 之后获得的有效对象，实现 Iterator 接口
	     *
	     * @return boolean
	     */
	    function valid()
	    {
	        return $this->_is_valid;
	    }
	  
	   	
		/**
		 * magic function
		 *
		 * gener set_name get_name function
		 * 
		 * @param string $method
		 * @param array $args
		 *
		 * @return mixed
		 */
		function __call($method, array $args)
		{
			$prefix = substr($method, 0, 3);
			if ($prefix == 'get')
			{
				$offset = substr($method, 4);
				return $this->_getOffset( $offset );
			} else if($prefix == 'set')
			{
				$offset = substr($method, 4);
				$this->_setOffset( $offset , $args[0] );
			}
		}
		
		
		
		protected function _getOffset( $offset ){
			
			$this->_checkGetBanList( $offset );
			
			//out put filter
			if( is_string( $this->_coll[$offset] ) ){
				return htmlspecialchars_decode(stripslashes($this->_coll[$offset]));
			}else{
				return $this->_coll[$offset];
			}
			
	   	}
	   	
	   	protected function _setOffset( $offset , $value ){
	   		
	   		$this->_checkSetBanList( $offset );
	   		$this->_checkType( $offset  , $value );
	   		$this->_checkEmpty( $offset , $value );
	   		$this->_checkLength( $offset , $value );

	   		if( isset($this->_coll[$offset] ) ){
				$this->_coll[$offset] = $value;	   				
	   		}
	   	}
	   	
	   	protected function _checkGetBanList( $offset ){
	   		
	   		$isInBanList = $this->_isColumnInGetBanList( $offset );
			if( $isInBanList ){
				throw new Exception( $offset . ' is in get ban list ' );
			}
	   	}
	   	
		protected function _checkSetBanList( $offset ){
	   		
			$isInSetBanList = $this->_isColumnInSetBanList( $offset );
	   		if( $isInSetBanList ){
	   			throw new Exception( $offset . ' is in set ban list ' ); 
	   		}
	   	}
	   	
	   	protected function _checkType( $offset , $value ){
	   		
	   		foreach( $this->_typeValidateList as $column => $type ){
	   			if( $offset == $column ){
	   				$isValid = WeFlex_Validate::IsValid( $type , $value );
	   				if( !$isValid ){
	   					throw new Exception( $offset . ' is not validate the type ' . $type ); 
	   				}
	   			}
	   		}
	   	}
	   	
	   	protected function _checkEmpty( $offset , $value ){
	   		
	   		foreach( $this->_emptyValidateList as $column ){
	   			if( $offset == $column ){
	   				
	   				if( is_int( $value ) ){
	   					return;
	   				}
	   				
	   				$value = trim(  $value );
	   				if( $value === '' ){
	   					throw new Exception( $offset . ' should not be empty '); 
	   				}
	   			}
	   		}
	   	}
	   	
	   	protected function _checkLength( $offset , $value ){
	   		
	   		foreach( $this->_lengthValidateList as $column => $lenthRegion ){
	   			
	   			if( $offset == $column ){
	   				
	   				$len=strlen($value);
	   				
	   				if( $len < $lenthRegion[0] || $len > $lenthRegion[1] ){
	   					throw new Exception( $offset . ' str length is not right ' ); 
	   				}
	   			}
	   		}
	   	}
	   	
	   	
	   	private function _isColumnInGetBanList( $column ){
	   		return in_array( $column , $this->_banGetList );
	   		//return WeFlex_Array::HasValue( $this->_banGetList , $column );
	   	}
	   	
	   	private function _isColumnInSetBanList( $column ){
	   		return in_array( $column , $this->_banSetList );
	   		//return WeFlex_Array::HasValue( $this->_banSetList , $column );
	   	}

		
	}
?>