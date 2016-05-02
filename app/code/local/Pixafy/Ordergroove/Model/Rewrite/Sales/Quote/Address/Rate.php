<?php
/**
 * Ordergroove module rewrite of the shipping rate class. Checks for free shipping
 * in the Initial Order Incentive, and applies based on the settings in Configuration 
 * panel.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Rewrite_Sales_Quote_Address_Rate extends Mage_Sales_Model_Quote_Address_Rate{
	
	public function _construct(){
		parent::_construct();
	}
	
	/**
	 * Return the price of the shipping rate. Check and see if the current
	 * rate is elgibile for IOI free shipping. If it is not return the 
	 * price already set on the rate. Otherwise change the price to 0
	 * and return that value.
	 */
	public function getPrice(){
		if(Mage::helper('ordergroove/ioi_freeshipping')->shouldApplyIoiFreeShipping(Mage::getSingleton('checkout/session')->getQuote())){
			if($this->_shouldApplyIoiFreeshippingToRate()){
				return 0;
			}
		}
		return $this->getData('price');
	}
	
	/**
	 * Given a shipping rate, checks and sees if free shipping
	 * should be applied to it based on the methods selected
	 * in the system configuration.
	 * 
	 * @param Mage_Sales_Model_Quote_Address_Rate
	 * @return boolean
	 */
	protected function _shouldApplyIoiFreeshippingToRate(){
		if(in_array($this->getCode(), $this->_getIoiFreeshippingMethods())){
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Return the shipping methods that are set to be eligible
	 * for IOI free shipping
	 * 
	 * @return array
	 */
	protected function _getIoiFreeshippingMethods(){
		if(!$this->_ioiShippingMethods){
			$this->_ioiShippingMethods	=	Mage::helper('ordergroove/config')->getIoiFreeshippingMethods();
		}
		return $this->_ioiShippingMethods;
	}
}