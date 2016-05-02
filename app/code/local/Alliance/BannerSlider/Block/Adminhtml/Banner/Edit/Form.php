<?php

/**
 * Class Alliance_BannerSlider_Block_Adminhtml_Banner_Edit_Form
 */
class Alliance_BannerSlider_Block_Adminhtml_Banner_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('alliance_bannerslider_banner_form');
        $this->setTitle($this->__('Banner Details'));
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('alliance_bannerslider');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data',
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('alliance_bannerslider')->__('Banner Information'),
            'class'     => 'fieldset-wide',
        ));

        $fieldset->addField('image', 'image', array(
            'label'     => Mage::helper('alliance_bannerslider')->__('Upload Banner Image'),
            'required'  => true,
            'name'      => 'image',
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'  => 'id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('alliance_bannerslider')->__('Banner Title / Alt Text'),
            'title'     => Mage::helper('alliance_bannerslider')->__('Banner Title / Alt Text'),
            'required'  => true,
        ));

        $fieldset->addField('link', 'text', array(
            'name'      => 'link',
            'label'     => Mage::helper('alliance_bannerslider')->__('Banner Internal / External Link'),
            'title'     => Mage::helper('alliance_bannerslider')->__('Banner Internal / External Link'),
            'required'  => false,
        ));

        $fieldset->addField('new_tab', 'select', array(
            'name'      => 'new_tab',
            'label'     => Mage::helper('alliance_bannerslider')->__('Link Opens In New Tab'),
            'title'     => Mage::helper('alliance_bannerslider')->__('Link Opens In New Tab'),
            'values'    => array('No'=>'No','Yes' => 'Yes'),
            'required'  => false,
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'label'     => Mage::helper('alliance_bannerslider')->__('Banner Sort Order'),
            'title'     => Mage::helper('alliance_bannerslider')->__('Banner Sort Order'),
        ));

        $fieldset->addField('store_code', 'select', array(
            'name'      => 'store_code',
            'label'     => Mage::helper('alliance_bannerslider')->__('Store Code'),
            'title'     => Mage::helper('alliance_bannerslider')->__('Store Code'),
            'values'    => $this->_getStoreCodeOptions(),
        ));

        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => Mage::helper('alliance_bannerslider')->__('Status'),
            'title'     => Mage::helper('alliance_bannerslider')->__('Status'),
            'values'    => array('Disabled'=>'Disabled','Enabled' => 'Enabled'),
            'required'  => false,
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Gets all store codes and returns them in the form of an options array for a form field
     *
     * @return array
     */
    protected function _getStoreCodeOptions()
    {
        $stores = Mage::app()->getStores();
        $storecodes_sorted = array();
        $options_array = array();
        foreach ($stores as $store) {
            $storecodes_sorted[intval($store->getId())] = $store->getCode();
        }
        ksort($storecodes_sorted);
        foreach ($storecodes_sorted as $sorted_store_code) {
            $options_array[$sorted_store_code] = $sorted_store_code;
        }
        return $options_array;
    }
}