<?php

class Alliance_Quiz_Block_Adminhtml_Quiz_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('quiz_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('quiz')->__('Applicant Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('quiz')->__('Applicant Information'),
          'title'     => Mage::helper('quiz')->__('Applicant Information'),
          'content'   => $this->getLayout()->createBlock('quiz/adminhtml_quiz_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}