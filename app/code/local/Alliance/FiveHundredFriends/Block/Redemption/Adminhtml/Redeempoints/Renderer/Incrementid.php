<?php

/**
 * Class Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints_Renderer_Incrementid
 *
 * Redemption model
 */

class Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints_Renderer_Incrementid
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$orderId =  $row->getData($this->getColumn()->getIndex());
		
		$order = Mage::getModel('sales/order')->load($orderId);
        return '<a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $orderId)).'">'.$order->getIncrementId().'</a>';
	}
}
?>