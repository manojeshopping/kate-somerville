<?php
     
class Alliance_Giftreports_Block_Adminhtml_Giftreports extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_giftreports';
    $this->_blockGroup = 'giftreports';
    $this->_headerText = Mage::helper('giftreports')->__('Gift Card Reports');
    parent::__construct();
	$this->_removeButton('add');
    
  }
}
