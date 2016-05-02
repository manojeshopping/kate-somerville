<?php
/**
 * OrderGroove rewrite of the Sales_Order_Invoice_Totals
 * block. This class is rewritten to add the custom
 * IOI discount total to the view.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Adminhtml_Sales_Order_Invoice_Totals extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals
{
	protected function _initTotals()
	{
		parent::_initTotals();
		if ($discount = $this->getOrder()->getData(Pixafy_Ordergroove_Helper_Constants::ORDER_COLUMN_OG_IOI_ORDER_DISCOUNT)) {
			$this->addTotal(new Varien_Object(array(
				'code'			=>	Pixafy_Ordergroove_Model_Sales_Quote_Address_Total_Ogioidiscount::OG_IOI_TOTAL_CODE,
				'value'			=>	-$discount,
				'base_value'	=>	-$discount,
				'label'			=>	Mage::helper('ordergroove/config')->getIoiDiscountLabel(),
			)));
		}
		return $this;
	}
 
}