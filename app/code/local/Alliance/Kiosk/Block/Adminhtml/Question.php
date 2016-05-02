<?php
class Alliance_Kiosk_Block_Adminhtml_Question extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_question';
        $this->_blockGroup = 'kiosk';
        $this->_headerText = Mage::helper('kiosk/question')->__('Manage Questions');
        $this->_addButtonLabel = Mage::helper('kiosk/question')->__('Add Question');
        parent::__construct();
    }
}
