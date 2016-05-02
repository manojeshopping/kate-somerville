<?php
/**
 * Handles all IOI discount logic for credit memo.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Sales_Order_Creditmemo_Total_Ogioidiscount extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo){
		$order = $creditmemo->getOrder();
		
		if($amount = $order->getData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ORDER_DISCOUNT)){
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $amount);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $amount);
		}
		return $this;
	}
}