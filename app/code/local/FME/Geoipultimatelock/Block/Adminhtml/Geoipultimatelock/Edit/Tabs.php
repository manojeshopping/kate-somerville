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
class FME_Geoipultimatelock_Block_Adminhtml_Geoipultimatelock_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('geoipultimatelock_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('geoipultimatelock')->__('ACL Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('geoipultimatelock')->__('ACL Information'),
            'title' => Mage::helper('geoipultimatelock')->__('ACL Information'),
            'content' => $this->getLayout()->createBlock('geoipultimatelock/adminhtml_geoipultimatelock_edit_tab_form')->toHtml(),
        ));

        $this->addTab('conditions_section', array(
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
            'content' => $this->getLayout()->createBlock('geoipultimatelock/adminhtml_geoipultimatelock_edit_tab_conditions')->toHtml(),
        ));
        
        //if (Mage::helper('geoipultimatelock')->isTableExists('geoip_cl')) {
            $this->addTab('country_section', array(
                'label' => 'Add Country to ACL',
                'content' => $this->getLayout()->createBlock('adminhtml/template', 'countrytabs', array('template' => 'geoipultimatelock/blockcountryform.phtml'))->toHtml(),
            ));
        //}
        
        return parent::_beforeToHtml();
    }

}
