<?php
class Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'alliance_fivehundredfriends';
		$this->_controller = 'redemption_adminhtml_redeempoints';
		$this->_headerText = $this->__('UKR Points Redeemed');

		parent::__construct();
	}
	
	
	protected function _prepareLayout() {
		$this->_removeButton('add');
		return parent::_prepareLayout();
	}
}
