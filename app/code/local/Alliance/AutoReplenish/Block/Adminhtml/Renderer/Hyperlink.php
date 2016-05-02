<?php
class Alliance_AutoReplenish_Block_Adminhtml_Renderer_Hyperlink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$column_index_value = $this->getColumn()->getIndex();
		$column_value =  $row->getData($this->getColumn()->getIndex());
		
		switch ($column_index_value) {
			case "customer_id" :
				$url = Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/id/".$column_value."/");
			break;
			case "customer_email" :
				$customer_id = $row->getData('customer_id');
				$url = Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/id/".$customer_id."/");
			break;
			case "order_id" :
			//	$url = Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/view/order_id/".$column_value."/");
				$order = Mage::getModel('sales/order')->loadByIncrementId($column_value);
				$url = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id'=> $order->getId()));
			break;
			default:
		}
		$customer_admin_url = Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/id/".$customer_id."/");
		//Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit/id/”.$customer_id."/");
		return '<a href='.$url.'>'.$column_value.'</a>';
		
	}
}