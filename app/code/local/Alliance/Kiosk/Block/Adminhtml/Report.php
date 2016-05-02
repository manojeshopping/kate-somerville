<?php
class Alliance_Kiosk_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        
        $this->_controller = 'adminhtml_report';
        $this->_blockGroup = 'kiosk';
        
        $this->_headerText = Mage::helper('kiosk/report')->__('Manage Reports');
        parent::__construct();
                $this->removeButton('add');
        
    }
}
