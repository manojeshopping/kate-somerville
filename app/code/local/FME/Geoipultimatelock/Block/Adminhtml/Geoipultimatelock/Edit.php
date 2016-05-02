<?php

class FME_Geoipultimatelock_Block_Adminhtml_Geoipultimatelock_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'geoipultimatelock';
        $this->_controller = 'adminhtml_geoipultimatelock';

        $this->_updateButton('save', 'label', Mage::helper('geoipultimatelock')->__('Save ACL'));
        $this->_updateButton('delete', 'label', Mage::helper('geoipultimatelock')->__('Delete ACL'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('geoipultimatelock_content') == null) {
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

    protected function _prepareLayout() {
        parent::_prepareLayout();

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    public function getHeaderText() {
        if (Mage::registry('geoipultimatelock_data') && Mage::registry('geoipultimatelock_data')->getId()) {
            return Mage::helper('geoipultimatelock')->__("Edit ACL '%s'", $this->htmlEscape(Mage::registry('geoipultimatelock_data')->getTitle()));
        } else {
            return Mage::helper('geoipultimatelock')->__('Add ACL');
        }
    }

}