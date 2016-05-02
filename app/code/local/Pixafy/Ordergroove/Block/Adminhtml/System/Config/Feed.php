<?php
/**
 * 	Block that renders the run product feed button in the system configuration panel
 * 
 * 	@package Pixafy_Ordergroove
 * 	@author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Adminhtml_System_Config_Feed extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/**
	 * Get the element html
	 * 
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string | html
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$this->setElement($element);
		return $this->_getAddRowButtonHtml($this->__('Run Product Feed'));
	}

	/**
	 * Generate and return the HTML
	 * 
	 * @param string $title - the button title
	 * @return string | html
	 */
	protected function _getAddRowButtonHtml($title) {
		$buttonBlock = $this->getElement()->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button');
		$_websiteCode = $buttonBlock->getRequest()->getParam('website');
		$params = array(
			'website' => $_websiteCode,
			'_store' => $_websiteCode ? $_websiteCode : Mage::app()->getDefaultStoreView()->getId()
		);

		$url = Mage::helper('adminhtml')->getUrl('adminhtml/ordergroove_product_feed/generate');
		return $this->getLayout()->createBlock('adminhtml/widget_button')
				->setType('button')
				->setLabel($this->__($title))
				->setOnClick("window.location.href='".$url."'")
				->toHtml();
	}



}
