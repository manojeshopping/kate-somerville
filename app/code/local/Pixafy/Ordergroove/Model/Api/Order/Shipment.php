<?php
/**
 * Order API class that parses and returns the shipment method to be used for the order
 * 
 * @package     Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 * 
 * @method extract()
 */
class Pixafy_Ordergroove_Model_Api_Order_Shipment extends Pixafy_Ordergroove_Model_Api_Order{
	
	/**
	 * Return the carrier / service level to be used for the order.
	 * 
	 * @return string
	 */
	public function extract(){
		$helper	=	Mage::helper('ordergroove/installer');
		return Mage::getStoreConfig('ordergroove/order_api/shipping_method', Mage::app()->getStore()->getCode());
	}
}
?>
