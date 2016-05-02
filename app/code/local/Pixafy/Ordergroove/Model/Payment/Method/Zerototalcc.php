<?php
/**
 * This payment method is displayed when the cart total is 0 
 * and the zero subtotal checkout is disabled.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Payment_Method_Zerototalcc extends Mage_Payment_Model_Method_Cc{
	
	protected $_code			=	'og_zerototalcc';

	protected $_canSaveCc		=	false;
	
	/**
	 * Check whether this payment method is allowed.
	 * We use the raw class name instead of getModel(classname)
	 * because we want the default logic associated with the
	 * free method. Basically, if the zero subtotal checkout
	 * method is "available", but disabled by the OrderGroove module,
	 * then we want the zero total credit card method to appear.
	 *
	 * @param Mage_Sales_Model_Quote|null $quote
	 * @return bool
	 */
	public function isAvailable($quote = null)
	{
		$freeClass	=	new Mage_Payment_Model_Method_Free();
		if($freeClass->isAvailable($quote)){
			if(!Mage::getModel('payment/method_free')->isAvailable()){
				if(Mage::helper('ordergroove/config')->getZerototalccEnabled()){
					return TRUE;
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * Placeholder function. Do not do anything here
	 * so that 
	 *
	 * @param  Mage_Payment_Model_Info $payment
	 * @param  decimal $amount
	 * @return Pixafy_Ordergroove_Model_Payment_Method_Zerototalcc
	 */
	public function authorize(Varien_Object $payment, $amount){
		return $this;
	}

	/**
	 * Send capture request to gateway
	 *
	 * @param Mage_Payment_Model_Info $payment
	 * @param decimal $amount
	 * @return Pixafy_Ordergroove_Model_Payment_Method_Zerototalcc
	 */
	public function capture(Varien_Object $payment, $amount){
		return $this;
	}

	/**
	 * Void the payment through gateway
	 *
	 * @param  Mage_Payment_Model_Info $payment
	 * @return Pixafy_Ordergroove_Model_Payment_Method_Zerototalcc
	 */
	public function void(Varien_Object $payment){
		return $this;
	}
	
	/**
	 * Return the payment method title
	 * 
	 * @return string
	 */
	public function getTitle(){
		return Mage::helper('ordergroove/config')->getZerototalccTitle();
	}
}
?>