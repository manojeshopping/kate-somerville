<?php
/**
* Used to check if already there
*
* @category    Alliance    
* @package     Alliance_Quiz
*/	   
class Alliance_Quiz_Model_Container_Quiz extends Enterprise_PageCache_Model_Container_Abstract
{ 
	
    

	 
	/**
	* Get cache identifier
	*
	* @return string
	*/
	protected function _getCacheId()
	{
		
		return 'CART_MESSAGE' . md5($this->_placeholder->getAttribute('cache_id').rand());
	}
	 
	/**
	* Render block content
	*
	* @return string
	*/
	protected function _renderBlock()
	{
	
		$blockClass = $this->_placeholder->getAttribute('block');

		$template = $this->_placeholder->getAttribute('template');
		 
		$block = new $blockClass;
		$block->setTemplate($template);
		return $block->toHtml();
	}
	
	
	
}