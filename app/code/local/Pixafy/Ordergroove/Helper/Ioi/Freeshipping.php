<?php
/**
 * OrderGroove module Initial Order Incentive free shipping helper.
 * Determines whether or not when and how to apply free shipping 
 * based on the IOI cookies.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Helper_Ioi_Freeshipping extends Mage_Core_Helper_Abstract{

	const REGISTRY_KEY_TOTALS_COLLECTED	=	'ordergroove_totals_collected';
	/**
	 * Returns whether or not to apply free shipping
	 * based on a provided quote.
	 * 
	 * @param Mage_Sales_Model_Quote $quote
	 * @return boolean
	 */
	public function shouldApplyIoiFreeShipping(Mage_Sales_Model_Quote $quote){
		if($this->_getSession()->shouldApplyIoiFreeShipping()){
			if($this->_calculateQuoteSubtotal($quote) >= $this->getConfig()->getIoiFreeshippingThreshold()){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	
	/**
	 * Return the Ordergroove session class
	 * 
	 * @return Pixafy_Ordergroove_Model_Session
	 */
	protected function _getSession(){
		return Mage::getSingleton('ordergroove/session');
	}
	
	/**
	 * Return the configuration helper class
	 * 
	 * @return Pixafy_Ordergroove_Helper_Config
	 */
	public function getConfig(){
		return Mage::helper('ordergroove/config');
	}
	
	/**
	 * Determine the subtotal of the cart by looping
	 * over the items and summing their prices
	 * 
	 * @param Mage_Sales_Model_Quote $quote
	 * @return float
	 */
	protected function _calculateQuoteSubtotal($quote){
		$subtotal	=	0;
		foreach($quote->getAllItems() as $item){
			$subtotal+=$item->getPrice();
		}
		return $subtotal;
	}
	
}
?>