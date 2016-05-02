<?php

/**
 * Class Alliance_KateReview_Block_Adminhtml_Review_Grid_Renderer_CustomerName
 */
class Alliance_KateReviews_Block_Adminhtml_Review_Grid_Renderer_CustomerName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row)
	{
		$customer_id = $row->getData($this->getColumn()->getIndex());
		$customer = Mage::getModel('customer/customer')->load($customer_id);
		return $customer->getName();
	}
}