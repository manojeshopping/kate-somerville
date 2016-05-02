<?php
class Alliance_Quiz_Block_Adminhtml_Quiz extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_quiz';
    $this->_blockGroup = 'quiz';
    $this->_headerText = Mage::helper('quiz')->__('Applicant Manager');
    $this->_addButtonLabel = Mage::helper('quiz')->__('Add Applicant');
    parent::__construct();
  }
}