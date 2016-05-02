<?php

class Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	* Init class
	*/
	public function __construct()
	{
		parent::__construct();
		$this->setTitle($this->__('Review Information'));
	}

	/**
	* Setup form fields for inserts/updates
	*
	* return Mage_Adminhtml_Block_Widget_Form
	*/
	protected function _prepareForm()
	{
		$model = Mage::registry('alliance_fivehundredfriends_redemption');
		
		$form = new Varien_Data_Form(array(
			'id'     => 'alliance_fivehundredfriends_redeem_form',
			'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method' => 'post'
		));
		
		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
}