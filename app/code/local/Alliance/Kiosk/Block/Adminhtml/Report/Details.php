<?php

class Alliance_Kiosk_Block_Adminhtml_Report_Details extends Mage_Adminhtml_Block_Widget_Form_Container 
{
  public function __construct() {
    $this->_blockGroup = 'kiosk';
    $this->_controller = 'adminhtml_report';
    
    $this->_headerText = Mage::helper('kiosk/report')->__('Details');
    parent::__construct();

    }  
}
