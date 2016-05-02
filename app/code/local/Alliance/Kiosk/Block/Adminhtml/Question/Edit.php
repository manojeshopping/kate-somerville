<?php

class Alliance_Kiosk_Block_Adminhtml_Question_Edit extends Mage_Adminhtml_Block_Widget_Form_Container 
{
  public function __construct() {
    $this->_blockGroup = 'kiosk';
    $this->_controller = 'adminhtml_question';
    
    $this->_headerText = Mage::helper('kiosk/question')->__('Edit Question');
    parent::__construct();
    
    $this->_updateButton('save', 'label', $this->__('Save Question'));
    $this->_updateButton('delete', 'label', $this->__('Delete Question'));
    }  
}
