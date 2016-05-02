<?php

class Alliance_Quiz_Block_Adminhtml_Quiz_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('quiz_form', array('legend'=>Mage::helper('quiz')->__('Applicant information')));
     
      $fieldset->addField('qname', 'text', array(
          'label'     => Mage::helper('quiz')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'qname',
      ));

      $fieldset->addField('qage', 'text', array(
          'label'     => Mage::helper('quiz')->__('Age'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'qage',
      ));
      $fieldset->addField('qgender', 'text', array(
          'label'     => Mage::helper('quiz')->__('Gender'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'qgender',
      ));

      $fieldset->addField('q1option', 'text', array(
          'label'     => Mage::helper('quiz')->__('What are your top two skin concerns?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q1option',
      ));
      $fieldset->addField('q2option', 'text', array(
          'label'     => Mage::helper('quiz')->__('How would you characterize your skin?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q2option',
      ));
      $fieldset->addField('q3option', 'text', array(
          'label'     => Mage::helper('quiz')->__('What is your skin color, without a suntan?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q3option',
      ));
      $fieldset->addField('q4option', 'text', array(
          'label'     => Mage::helper('quiz')->__('How sensitive is your skin?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q4option',
      ));
      $fieldset->addField('q5option', 'text', array(
          'label'     => Mage::helper('quiz')->__('What is your main concern around the eye area?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q5option',
      ));


      $fieldset->addField('q6option', 'text', array(
          'label'     => Mage::helper('quiz')->__('Where do you experience breakouts?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q6option',
      ));
      $fieldset->addField('q7option', 'text', array(
          'label'     => Mage::helper('quiz')->__('How often do you experience breakouts?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q7option',
      ));
      $fieldset->addField('q8option', 'text', array(
          'label'     => Mage::helper('quiz')->__('What kind of breakouts do you experience?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q8option',
      ));
      $fieldset->addField('q9option', 'text', array(
          'label'     => Mage::helper('quiz')->__('Describe your issue(s) with discoloration?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q9option',
      ));
      $fieldset->addField('q10option', 'text', array(
          'label'     => Mage::helper('quiz')->__('Where are you experiencing fine lines and wrinkles?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q10option',
      ));
      $fieldset->addField('q11option', 'text', array(
          'label'     => Mage::helper('quiz')->__('Where are you experiencing enlarged pores?'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'q11option',
      ));

/*		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('quiz')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('quiz')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('quiz')->__('Disabled'),
              ),
          ),
      ));
     
     */
      if ( Mage::getSingleton('adminhtml/session')->getQuizData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getQuizData());
          Mage::getSingleton('adminhtml/session')->setQuizData(null);
      } elseif ( Mage::registry('quiz_data') ) {
          $form->setValues(Mage::registry('quiz_data')->getData());
      }
      return parent::_prepareForm();
  }
}