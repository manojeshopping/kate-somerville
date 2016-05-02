<?php
/**
 * Ordergroove module rewrite for the check / money order
 * payment method. This class will disable this payment
 * method based on the logic in defined in the system
 * configuration.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Rewrite_Payment_Method_Checkmo extends Mage_Payment_Model_Method_Checkmo{
	/**
	 * Return whether the payment method is available.
	 * 
	 * @param Mage_Sales_Model_Quote $quote
	 * @return boolean
	 */
	public function isAvailable($quote = null){
		if(!Mage::helper('ordergroove/config')->functionalityCheckCheckMoneyOrder()){
			return FALSE;
		}
		return parent::isAvailable($quote);
	}
}
