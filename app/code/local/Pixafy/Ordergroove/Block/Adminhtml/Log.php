<?php
/**
 * Logging admin block
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Container{
	/**
	 * Prepare button and grid
	 *
	 * @return Mage_Adminhtml_Block_Catalog_Product
	 */
	protected function _prepareLayout()
	{
		$this->setChild('grid', $this->getLayout()->createBlock('ordergroove/adminhtml_log_grid', 'log.grid'));
		return parent::_prepareLayout();
	}
	/**
	 * Deprecated since 1.3.2
	 *
	 * @return string
	 */
	public function getAddNewButtonHtml()
	{
		return $this->getChildHtml('add_new_button');
	}

	/**
	 * Render grid
	 *
	 * @return string
	 */
	public function getGridHtml()
	{
		return $this->getChildHtml('grid');
	}

	/**
	 * Check whether it is single store mode
	 *
	 * @return bool
	 */
	public function isSingleStoreMode()
	{
		if (!Mage::app()->isSingleStoreMode()) {
			   return false;
		}
		return true;
	}
}
