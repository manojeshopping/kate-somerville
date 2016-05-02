<?php

/**
 * Class Alliance_GlobalBanner_Block_Adminhtml_Banner_Edit_Form
 */
class Alliance_GlobalBanner_Block_Adminhtml_Banner_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('alliance_globalbanner_banner_form');
        $this->setTitle($this->__('Banner Details'));
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('alliance_globalbanner');

        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $fieldset = $form->addFieldset('alliance_globalbanner_banner', array(
            'legend' => Mage::helper('alliance_globalbanner')->__('Global Banner Details'),
            'class'  => 'fieldset-wide',
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }

        $fieldset->addField('image', 'image', array(
            'label'    => Mage::helper('alliance_globalbanner')->__('Upload Global Banner Image'),
            'required' => true,
            'name'     => 'image',
        ));

        $fieldset->addField('image_link', 'text', array(
            'name'     => 'image_link',
            'label'    => Mage::helper('alliance_globalbanner')->__('Global Banner Link'),
            'title'    => Mage::helper('alliance_globalbanner')->__('Global Banner Link'),
            'required' => false,
        ));

        $fieldset->addField('new_tab', 'select', array(
            'name'   => 'new_tab',
            'label'  => Mage::helper('alliance_bannerslider')->__('Link Opens In New Tab'),
            'title'  => Mage::helper('alliance_bannerslider')->__('Link Opens In New Tab'),
            'values' => array('No' => 'No', 'Yes' => 'Yes'),
        ));

        $fieldset->addField('image_alt', 'text', array(
            'name'  => 'image_alt',
            'label' => Mage::helper('alliance_globalbanner')->__('Global Banner Alt Text'),
            'title' => Mage::helper('alliance_globalbanner')->__('Global Banner Alt Text'),
        ));

        $fieldset->addField('stores', 'multiselect', array(
            'label'    => Mage::helper('alliance_globalbanner')->__('Stores'),
            'name'     => 'stores[]',
            'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            'required' => true,
        ));

        $fieldset->addField('pages', 'multiselect', array(
            'label'    => Mage::helper('alliance_globalbanner')->__('Pages'),
            'name'     => 'pages[]',
            'values'   => array(
                array(
                    'label' => 'Home Page',
                    'value' => '0',
                ),
                array(
                    'label' => 'Category Pages',
                    'value' => '1',
                ),
                array(
                    'label' => 'Product Pages',
                    'value' => '2',
                ),
                array(
                    'label' => 'Checkout Pages',
                    'value' => '3',
                ),
                array(
                    'label' => 'User Account Pages',
                    'value' => '4',
                ),
                array(
                    'label' => 'All Other CMS Pages',
                    'value' => '5',
                ),
            ),
            'required' => true,
        ));

        $fieldset->addField('logged_in_status', 'multiselect', array(
            'name'   => 'logged_in_status',
            'label'  => Mage::helper('alliance_globalbanner')->__('Customer Status'),
            'title'  => Mage::helper('alliance_globalbanner')->__('Customer Status'),
            'values' => array(
                array(
                    'label' => 'Logged In',
                    'value' => '1',
                ),
                array(
                    'label' => 'Not Logged In',
                    'value' => '0',
                ),
            ),
            'required' => true,
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'         => 'from_date',
            'label'        => Mage::helper('alliance_globalbanner')->__('From Date'),
            'title'        => Mage::helper('alliance_globalbanner')->__('From Date'),
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'         => 'to_date',
            'label'        => Mage::helper('alliance_globalbanner')->__('To Date'),
            'title'        => Mage::helper('alliance_globalbanner')->__('To Date'),
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
        ));

        $fieldset->addField('priority', 'text', array(
            'name'     => 'priority',
            'label'    => Mage::helper('alliance_globalbanner')->__('Priority'),
            'title'    => Mage::helper('alliance_globalbanner')->__('Priority'),
            'required' => false,
        ));

        $fieldset->addField('status', 'select', array(
            'name'   => 'status',
            'label'  => Mage::helper('alliance_globalbanner')->__('Status'),
            'title'  => Mage::helper('alliance_globalbanner')->__('Status'),
            'values' => array('Disabled' => 'Disabled', 'Enabled' => 'Enabled'),
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Gets all pages and returns them in the form of an options array for a form field
     *
     * @return array
     */
    protected function _getPageOptions()
    {

    }
}
