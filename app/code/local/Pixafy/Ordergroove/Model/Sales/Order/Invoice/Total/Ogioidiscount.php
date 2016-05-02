<?php
/**
 * Handles all IOI discount logic for invoices.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Sales_Order_Invoice_Total_Ogioidiscount extends Mage_Sales_Model_Order_Invoice_Total_Abstract{
	public function collect(Mage_Sales_Model_Order_Invoice $invoice){
		$order = $invoice->getOrder();
		$amount = $order->getData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ORDER_DISCOUNT);
		if($amount){
			$invoice->setGrandTotal($invoice->getGrandTotal() - $amount);
			$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $amount);
		}
		return $this;
	}
}
?>