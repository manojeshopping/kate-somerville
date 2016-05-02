<?php
/**
 * Block that renders the run product feed download link in the admin config
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Adminhtml_System_Config_Feed_Download extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/**
	 * Get the element html
	 * 
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string | html
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		if($webCode = Mage::app()->getRequest()->getParam('website')){
			$website = Mage::getModel('core/website')->load($webCode, 'code');
			$merchantId = Mage::helper('ordergroove/config')->getMerchantId($website->getDefaultStore());
		}
		else{
			$merchantId = Mage::helper('ordergroove/config')->getMerchantId();
		}
		Mage::log($merchantId);
		$this->_getHelper()->setCurrentMerchantId($merchantId);
		if(file_exists($this->_getHelper()->getCsvFilePath())){
			return '<a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/ordergroove_product_feed/download', array('file' => $this->_getHelper()->getCsvFileName())).'">'.Mage::helper('ordergroove')->__("Download feed").'</a>';
		}
		return Mage::helper('ordergroove')->__("Product feed does not exist");
	}
	
	protected function _getHelper(){
		return Mage::helper('ordergroove/product_feed');
	}
}
