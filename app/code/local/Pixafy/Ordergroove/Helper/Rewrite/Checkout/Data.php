<?php
/**
 * Checkout helper class rewrite. This class is rewritten for use
 * with determining to disable multi-ship checkout when the
 * og_autoship cookie is present.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Helper_Rewrite_Checkout_Data extends Mage_Checkout_Helper_Data{
	/**
	 * Check if multi shipping should be disabled based
	 * on settings on OrderGroove tab of the system
	 * configuration panel.
	 *
	 * @return bool
	 */
	public function isMultishippingCheckoutAvailable(){
		if(!Mage::helper('ordergroove/config')->functionalityCheckShipToMultipleAddresses()){
			return FALSE;
		}
		return parent::isMultishippingCheckoutAvailable();
	}
}