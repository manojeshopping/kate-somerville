<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Colorswatches
 * @version    1.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Colorswatches_Block_Adminhtml_Swatch_Edit_Tab_Swatch extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{


    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                 'method'  => 'post',
                 'enctype' => 'multipart/form-data'
            )
        );

        $helper = Mage::helper('awcolorswatches');
        $_fieldset = $form->addFieldset('swatch_form', array('legend' => $helper->__('Swatch settings')));

        $_data = Mage::getSingleton('awcolorswatches/swatchattribute')->getSwatchattribute();

        $_fieldset->addField(
            'swatch_status', 'select',
            array(
                 'name'   => 'aw[swatch_status]',
                 'label'  => $this->__('Enable swatcher for current Attribute'),
                 'title'  => $this->__('Enable swatcher for current Attribute'),
                 'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            )
        );

        $_fieldset->addField(
            'display_popup', 'select',
            array(
                 'name'   => 'aw[display_popup]',
                 'label'  => $this->__('Display Images in pop-up on Mouse Hovering'),
                 'title'  => $this->__('Display Images in pop-up on Mouse Hovering'),
                 'values' => Mage::getModel('adminhtml/system_config_source_enabledisable')->toOptionArray(),
            )
        );

        $_fieldset2 = $form->addFieldset('swatch_table', array('legend' => $helper->__('Images')));
        $renderer = new AW_Colorswatches_Block_Adminhtml_Renderer_Attributetable;
        $renderer->setSwatchattribute($_data);
        $_fieldset2->addField(
            'attributetable', 'text',
            array('name' => 'attributetable')
        )->setRenderer($renderer);

        $form->setValues($_data->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }


    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('awcolorswatches')->__('Images for Attribute');
    }

    public function getTabTitle()
    {
        return Mage::helper('awcolorswatches')->__('Images for Attribute');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

}