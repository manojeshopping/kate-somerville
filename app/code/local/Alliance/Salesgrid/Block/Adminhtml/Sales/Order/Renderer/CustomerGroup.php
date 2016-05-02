<?php
class Alliance_Salesgrid_Block_Adminhtml_Sales_Order_Renderer_CustomerGroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$Group = Mage::getResourceModel('customer/group_collection')
			 ->addFieldToFilter('customer_group_id', $row->getCustomerGroupId())
			 ->load()
			 ->toOptionHash();
		$val = array_values($Group);
		return $val[0];
	}
}