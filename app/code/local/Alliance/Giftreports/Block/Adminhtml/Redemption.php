<?php
        
class Alliance_Giftreports_Block_Adminhtml_Redemption extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
	
    $this->_controller = 'adminhtml_redemption';
    $this->_blockGroup = 'giftreports';
    $this->_headerText = Mage::helper('giftreports')->__('Gift Card Redemption Reports');
    parent::__construct();
	$this->_removeButton('add');
    
  }
}
