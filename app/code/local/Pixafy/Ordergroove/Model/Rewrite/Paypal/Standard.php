<?php
/**
 * Ordergroove module rewrite for the PayPal Standard
 * payment method. This class will disable this payment
 * method based on the logic in defined in the system
 * configuration.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Rewrite_Paypal_Standard extends Mage_Paypal_Model_Standard{
	
	/**
	 * Run parent constructor
	 */
	public function __construct($params = array()){
		parent::__construct($params);
	}
	
	/**
	 * Check whether payment method can be used
	 * based off of the OrderGroove og_autoship
	 * cookie settings.
	 * 
	 * @param Mage_Sales_Model_Quote
	 * @return bool
	 */
	public function isAvailable($quote = null){
		if(!Mage::helper('ordergroove/config')->functionalityCheckPayPalStandard()){
			return FALSE;
		}
		return parent::isAvailable($quote);
	}
}
?>