<?php
/**
 * Ordergroove module rewrite for the zero subtotal
 * payment method. This class will disable this payment
 * method based on the logic in defined in the system
 * configuration.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Rewrite_Payment_Method_Free extends Mage_Payment_Model_Method_Free{
	/**
	 * Return whether the payment method is available.
	 * 
	 * @param Mage_Sales_Model_Quote $quote
	 * @return boolean
	 */
	public function isAvailable($quote = null){
		if(!Mage::helper('ordergroove/config')->functionalityCheckZeroSubtotal()){
			return FALSE;
		}
		if(Mage::registry(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_FORCE_ALLOW_FREE) == 1){
			return TRUE;
		}
		return parent::isAvailable($quote);
	}
}
