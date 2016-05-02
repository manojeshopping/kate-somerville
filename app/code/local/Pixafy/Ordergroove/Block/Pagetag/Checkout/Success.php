<?php
/**
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Pagetag_Checkout_Success extends Pixafy_Ordergroove_Block_Pagetag_Abstract{
	public function _construct(){
		parent::_construct();
	}
	
	/**
	 * Get the order that the customer just placed from the registry
	 * 
	 * @return Mage_Sales_Model_Order
	 */
	protected function _getOrder(){
		return Mage::registry(Pixafy_Ordergroove_Helper_Constants::REGISTRY_KEY_ORDER_SUCCESS_ORDER);
	}
	
	/**
	 * Check and see if the order has an id.
	 * 
	 * @return boolean
	 */
	public function isValidOrder(){
		$isValid	=	FALSE;
		$_order	=	$this->_getOrder();
		if(is_object($_order)){
			if($_order->getId()){
				$isValid	=	TRUE;
			}
		}
		return $isValid;
	}
	
	/**
	 * Return whether or not this content can be shown
	 * 
	 * @return boolean
	 */
	protected function _canShow(){
		return $this->getConfig()->isCheckoutSuccessPagetagEnabled();
	}
}
?>
