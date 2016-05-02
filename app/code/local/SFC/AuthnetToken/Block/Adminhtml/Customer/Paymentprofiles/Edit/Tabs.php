<?php
/**
 * StoreFront Authorize.Net CIM Tokenized Payment Extension for Magento
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to commercial source code license of StoreFront Consulting, Inc.
 *
 * @category  SFC
 * @package   SFC_AuthnetToken
 * @author    Garth Brantley <garth@storefrontconsulting.com>
 * @copyright 2009-2013 StoreFront Consulting, Inc. All Rights Reserved.
 * @license   http://www.storefrontconsulting.com/media/downloads/ExtensionLicense.pdf StoreFront Consulting Commercial License
 * @link      http://www.storefrontconsulting.com/authorize-net-cim-saved-credit-cards-extension-for-magento/
 *
 */

class SFC_AuthnetToken_Block_Adminhtml_Customer_Paymentprofiles_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('paymentprofile_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('authnettoken')->__('Saved Credit Card'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => Mage::helper('authnettoken')->__('Information'),
            'title' => Mage::helper('authnettoken')->__('Information'),
            'content' => $this->getLayout()->createBlock('authnettoken/adminhtml_customer_paymentprofiles_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
