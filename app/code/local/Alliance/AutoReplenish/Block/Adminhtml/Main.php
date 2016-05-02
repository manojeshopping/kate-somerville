<?php
class Alliance_AutoReplenish_Block_Adminhtml_Main extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
		$this->_blockGroup = 'autoreplenish';
        $this->_controller = 'adminhtml';
        parent::__construct();
	}
	
	public function getHeaderText()
    {
        return Mage::helper('salesrule')->__('Auto Replenish Dashboard');
    }
}
