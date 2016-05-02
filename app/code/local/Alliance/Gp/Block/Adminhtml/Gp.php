<?php

class Alliance_Gp_Block_Adminhtml_Gp extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_gp';
		$this->_blockGroup = 'gp';
		$this->_headerText = Mage::helper('gp')->__('Exported Orders');
		
		parent::__construct();
		
		$this->_removeButton('add');
	}
}

