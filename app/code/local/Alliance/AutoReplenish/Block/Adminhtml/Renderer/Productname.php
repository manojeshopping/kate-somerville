<?php
class Alliance_AutoReplenish_Block_Adminhtml_Renderer_Productname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$productId =  $row->getData($this->getColumn()->getIndex());
		$productName = Mage::getModel('catalog/product')->load($productId)->getName();
		return $productName;
		
	}
}