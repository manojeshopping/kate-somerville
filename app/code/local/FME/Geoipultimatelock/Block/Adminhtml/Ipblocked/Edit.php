<?php

class FME_Geoipultimatelock_Block_Adminhtml_Ipblocked_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'geoipultimatelock';
        $this->_controller = 'adminhtml_ipblocked';

        $this->_updateButton('save', 'label', Mage::helper('geoipultimatelock')->__('Save '));
        //$this->_updateButton('delete', 'label', Mage::helper('geoipultimatelock')->__('Delete '));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('blocked_ip') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'notes');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'notes');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }


    public function getHeaderText() {
        if (Mage::registry('geoipblockedips_data') && Mage::registry('geoipblockedips_data')->getId()) {
            return Mage::helper('geoipultimatelock')->__("Edit '%s'", $this->htmlEscape(Mage::registry('geoipblockedips_data')->getTitle()));
        } else {
            return Mage::helper('geoipultimatelock')->__('Add');
        }
    }

}