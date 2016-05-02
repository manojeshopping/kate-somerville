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
class FME_Geoipultimatelock_Block_Adminhtml_Ipblocked_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('geoipblockedips_tabs', array('legend' => Mage::helper('geoipultimatelock')->__('Block IP')));

        $fieldset->addField('IP', 'editor', array(
            'label' => Mage::helper('geoipultimatelock')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'blocked_ip',
            'after_element_html' => "<p><small>".Mage::helper('geoipultimatelock')->__('(Provide ip(s) separated by ",". Ip with wrong format will be ignored!)')."</small></p>"
        ));

        

        if (Mage::getSingleton('adminhtml/session')->getGeoipblockedipsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getGeoipblockedipsData());
            Mage::getSingleton('adminhtml/session')->getGeoipblockedipsData(null);
        } elseif (Mage::registry('geoipblockedips_data')) {
            $form->setValues(Mage::registry('geoipblockedips_data')->getData());
        }
        
        return parent::_prepareForm();
    }

}