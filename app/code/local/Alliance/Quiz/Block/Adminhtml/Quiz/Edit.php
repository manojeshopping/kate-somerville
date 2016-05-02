<?php

class Alliance_Quiz_Block_Adminhtml_Quiz_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'quiz';
        $this->_controller = 'adminhtml_quiz';
        
        $this->_updateButton('save', 'label', Mage::helper('quiz')->__('Save Applicant'));
        $this->_updateButton('delete', 'label', Mage::helper('quiz')->__('Delete Applicant'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('quiz_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'quiz_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'quiz_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('quiz_data') && Mage::registry('quiz_data')->getId() ) {
            return Mage::helper('quiz')->__("Edit Applicant '%s'", $this->htmlEscape(Mage::registry('quiz_data')->getTitle()));
        } else {
            return Mage::helper('quiz')->__('Add Applicant');
        }
    }
}