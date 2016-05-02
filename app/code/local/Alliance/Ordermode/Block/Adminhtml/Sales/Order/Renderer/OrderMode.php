<?php
class Alliance_Ordermode_Block_Adminhtml_Sales_Order_Renderer_OrderMode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		return $row->getOrderMode();
	}
}