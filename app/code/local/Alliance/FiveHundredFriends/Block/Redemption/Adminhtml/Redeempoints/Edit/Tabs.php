<?php


class Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('alliance_fivehundredfriends_redeempoints_form_tabs');
		$this->setDestElementId('alliance_fivehundredfriends_redeem_form');
		$this->setTitle(Mage::helper('core')->__('Reedem Information'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('information', array(
			'label'     => Mage::helper('core')->__('Reedem Information'),
			'title'     => Mage::helper('core')->__('Reedem Information'),
			'content'   => $this->getLayout()->createBlock('alliance_fivehundredfriends/redemption_adminhtml_redeempoints_edit_tab_form')->toHtml(),
		));
		$this->addTab('items', array(
			'label'     => Mage::helper('core')->__('Redeem Items'),
			'class'     => 'ajax',
			'url'       => $this->getUrl('*/*/items', array('_current' => true)),
		));

		return parent::_beforeToHtml();
	}
}
