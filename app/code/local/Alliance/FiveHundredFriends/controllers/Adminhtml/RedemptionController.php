<?php

class Alliance_FiveHundredFriends_Adminhtml_RedemptionController extends Mage_Adminhtml_Controller_Action
{
	public function redeempointsAction()
	{
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function editAction()
	{
		$this->_initReedem();
		
		// Add title
		$this->_title('Redeem points detail');
		
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('alliance_fivehundredfriends/redemption_adminhtml_redeempoints_edit'));
		$this->_addLeft($this->getLayout()->createBlock('alliance_fivehundredfriends/redemption_adminhtml_redeempoints_edit_tabs'));
		$this->renderLayout();
	}
	
	public function itemsAction()
	{
		$this->_initReedem();
		
        $this->loadLayout();
        $this->renderLayout();
	}
	
	
	protected function _initReedem()
	{
		// Get data.
		$id = $this->getRequest()->getParam('id');
		
		// Load redeem by id.
		$model = $this->_getRedemptionModel();
		$model->load($id);
		if(! $model->getId()) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('This redeem no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		
		// Register data.
		Mage::register('alliance_fivehundredfriends_redemption', $model);
		
		return $this;
	}
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('sales/alliance_fivehundredfriends')
			->_title($this->__('Sales'))
			->_title($this->__('Redeem points'))
			->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
			->_addBreadcrumb($this->__('Redeem points'), $this->__('Redeem points'))
		;

		return $this;
	}
	protected function _getRedemptionModel()
	{
		return Mage::getModel('alliance_fivehundredfriends/redemption');
	}
}
