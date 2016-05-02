<?php

class Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	* Init class
	*/
	public function __construct()
	{
		$this->_blockGroup = 'alliance_fivehundredfriends';
		$this->_controller = 'redemption_adminhtml_redeempoints';

		parent::__construct();

		$base64_referer_url = $this->getRequest()->getParam('referer');
		Mage::getSingleton('core/session')->setCustomreviewsReferer($base64_referer_url);

		$this->_removeButton('save');
		$this->_removeButton('delete');
		$this->_removeButton('reset');
		$this->_updateButton('back', 'onclick', 'setLocation(\'' . Mage::helper('core')->urlDecode($base64_referer_url) . '\')');
	}

	/**
	* Get Header text
	*
	* @return string
	*/
	public function getHeaderText()
	{
		if(Mage::registry('alliance_fivehundredfriends_redemption')->getId()) {
			return $this->__('View Review');
		}
	}
}