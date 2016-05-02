<?php
/**
 * Geoip Ultimate Lock extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Geoipultimatelock
 * @author     R.Rao <rafay.tahir@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
class FME_Geoipultimatelock_Block_Adminhtml_Geoipultimatelock_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('geoipultimatelock_form', array('legend' => Mage::helper('geoipultimatelock')->__('ACL')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('geoipultimatelock')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        $fieldset->addField('priority', 'text', array(
            'label' => Mage::helper('geoipultimatelock')->__('Priority'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'priority',
            'after_element_html' => "<p><small>".Mage::helper('geoipultimatelock')->__('(The group with higher priority will be selected. 0 has the higher priority!)')."</small></p>"
        ));

        $fieldset->addField('stores', 'multiselect', array(
            'label' => Mage::helper('geoipultimatelock')->__('Stores'),
            'required' => false,
            'name' => 'stores[]',
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'after_element_html' => "<p><small>".Mage::helper('geoipultimatelock')->__('(The access to the selected store(s) will be blocked!)')."</small></p>"
        ));

        $fieldset->addField('cms_pages', 'multiselect', array(
            'label' => Mage::helper('geoipultimatelock')->__('Cms Pages'),
            'required' => false,
            'name' => 'cms_pages',
            'values' => Mage::helper('geoipultimatelock')->allCmsPages(),
            'after_element_html' => "<p><small>".Mage::helper('geoipultimatelock')->__('(The access to the selected page(s) will be blocked!)')."</small></p>"
        ));

        $fieldset->addField('redirect_url', 'text', array(
            'label' => Mage::helper('geoipultimatelock')->__('Redirect Url'),
            'required' => false,
            'name' => 'redirect_url',
            'class' => 'validate-url',
            'after_element_html' => "<p><small>".Mage::helper('geoipultimatelock')->__('(Redirect will not work if rules are applied)')."</small></p>"
        ));
        
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('geoipultimatelock')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('geoipultimatelock')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('geoipultimatelock')->__('Disabled'),
                ),
            ),
        ));

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('tab_id' => 'form_section'));
        $wysiwygConfig["files_browser_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index');
        $wysiwygConfig["directives_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
        $wysiwygConfig["directives_url_quoted"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
        $wysiwygConfig["widget_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index');
        $wysiwygConfig["files_browser_window_width"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width');
        $wysiwygConfig["files_browser_window_height"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height');
        $plugins = $wysiwygConfig->getData("plugins");
        $plugins[0]["options"]["url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin');
        $plugins[0]["options"]["onclick"]["subject"] = "MagentovariablePlugin.loadChooser('" . Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin') . "', '{{html_id}}');";
        $plugins = $wysiwygConfig->setData("plugins", $plugins);

        $fieldset->addField('notes', 'editor', array(
            'name' => 'notes',
            'label' => Mage::helper('geoipultimatelock')->__('Notes'),
            'title' => Mage::helper('geoipultimatelock')->__('Notes'),
            'style' => 'width:700px; height:500px;',
            'required' => true,
            'config' => $wysiwygConfig
        ));
        
        $fieldset->addField('ips_exception', 'textarea', array(
            'name' => 'ips_exception',
            'label' => Mage::helper('geoipultimatelock')->__('Exceptions'),
            'title' => Mage::helper('geoipultimatelock')->__('Exceptions'),
            'style' => 'width:400px; height:300px;',
            'required' => false,
            'after_element_html' => "<p><small>".Mage::helper('geoipultimatelock')->__('(Provide ip\'s separated by ",". Wrong type of ips will be ignored!)')."</small></p>"
        ));

        if (Mage::getSingleton('adminhtml/session')->getGeoipultimatelockData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getGeoipultimatelockData());
            Mage::getSingleton('adminhtml/session')->setGeoipultimatelockData(null);
        } elseif (Mage::registry('geoipultimatelock_data')) {
            $form->setValues(Mage::registry('geoipultimatelock_data')->getData());
        }
        return parent::_prepareForm();
    }

}
