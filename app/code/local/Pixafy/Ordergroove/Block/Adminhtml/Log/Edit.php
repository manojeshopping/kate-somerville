<?php
/**
 * Log entry edit block
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Adminhtml_Log_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	
	public function __construct()
	{
		$this->_objectId 	=	'id';
		$this->_controller 	=	'log';
		parent::__construct();
	}

	/**
	 * Return header text
	 * 
	 * @return string
	 */
	public function getHeaderText()
	{
		return Mage::helper('ordergroove')->__('OrderGroove Log Entry (ID '.$this->getLogEntry()->getId().')');
	}

	protected function _prepareLayout()
	{
		/**
		 * Remove unneeded save
		 * and reset buttons
		 */
		$this->_removeButton('save');
		$this->_removeButton('reset');
		
		/**
		 * Update template
		 */
		$this->setTemplate('ordergroove/log/edit.phtml');

		return parent::_prepareLayout();
	}
	
	/**
	 * Return the current log entry
	 * 
	 * @return Pixafy_Ordergroove_Model_Log
	 */
	public function getLogEntry(){
		return Mage::registry(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_CURRENT_LOG);
	}
	
	/**
	 * Return a website name from a given website id
	 * 
	 * @param int $websiteId
	 * @return string
	 */
	public function getWebsiteFromId($websiteId){
		return Mage::getModel('core/website')->load($websiteId)->getName();
	}
}
