<?php

class Alliance_Gp_Block_Adminhtml_Gpfiles extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_gpfiles';
		$this->_blockGroup = 'gp';
		$this->_headerText = Mage::helper('gp')->__('Batches Created');
		
		parent::__construct();
		
		$this->_removeButton('add');
	}
}

