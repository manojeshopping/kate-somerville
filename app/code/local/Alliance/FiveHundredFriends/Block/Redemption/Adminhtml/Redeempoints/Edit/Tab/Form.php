<?php
class Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$model = Mage::registry('alliance_fivehundredfriends_redemption');
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend' => Mage::helper('alliance_fivehundredfriends')->__('Reedem Information'),
			'class'  => 'fieldset-wide',
		));

		if ($model->getId()) {
			$fieldset->addField('entity_id', 'hidden', array(
				'name' => 'entity_id',
				'after_element_html' =>
					'<tr><td class="label"><label>Customer Name</label></td>
					<td class="value">'.$this->_getCustomerName($model->getCustomerId()).'</td></tr>' .
					'<tr><td class="label"><label>Customer Email</label></td>
					<td class="value">'.$model->getCustomerEmail().'</td></tr>' .
					'<tr><td class="label"><label>Total Points</label></td>
					<td class="value">'.$model->getTotalPoints().'</td></tr>' .
					'<tr><td class="label"><label>Points Redeemed</label></td>
					<td class="value">'.$model->getRedeemPoints().'</td></tr>' .
					'<tr><td class="label"><label>Status</label></td>
					<td class="value">'.$this->_getStatusLabel($model->getStatus()).'</td></tr>' .
					'<tr><td class="label"><label>Order Increment Id</label></td>
					<td class="value">'.$this->_getOrderIncrementId($model->getOrderId()).'</td></tr>' .
					'<tr><td class="label"><label>Order Status</label></td>
					<td class="value">'.$this->_getOrderStatusOrigin($model->getOrderId()).'</td></tr>' .
					'<tr><td class="label"><label>Order Date</label></td>
					<td class="value">'.Mage::helper('core')->formatDate($model->getOrderDate(), Mage_Core_Model_Locale::FORMAT_TYPE_LONG, true).'</td></tr>',
			));
		}

		return parent::_prepareForm();
	}
	
	
	protected function _getCustomerName($customerId)
	{
        $customer = Mage::getModel('customer/customer')->load($customerId);
        return $customer->getName();
	}
	protected function _getOrderIncrementId($orderId)
	{
        $order = Mage::getModel('sales/order')->load($orderId);
        return '<a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $orderId)).'">'.$order->getIncrementId().'</a>';
	}
	protected function _getOrderStatusOrigin($orderId)
	{
        $order = Mage::getModel('sales/order')->load($orderId);
		$status = Mage::getModel('sales/order_status')->loadDefaultByState($order->getStatus());
		
        return $status->getStoreLabel();
	}
	protected function _getStatusLabel($label)
	{
		if($label == 'pending') return 'Pending';
		if($label == 'place_before') return 'Place Before';
		if($label == 'redeemed') return 'Redeemed';
		if($label == 'retured') return 'Retured';
	}
}
