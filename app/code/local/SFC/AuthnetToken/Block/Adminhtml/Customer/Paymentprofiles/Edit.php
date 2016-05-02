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

class SFC_AuthnetToken_Block_Adminhtml_Customer_Paymentprofiles_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {

        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'authnettoken';
        $this->_controller = 'adminhtml_customer_paymentprofiles';
        $this->_updateButton('save', 'label', Mage::helper('authnettoken')->__('Save Credit Card'));
        $this->_updateButton('delete', 'label', Mage::helper('authnettoken')->__('Delete Credit Card'));

    }

    public function getHeaderText()
    {
        if (Mage::registry('paymentprofile_data') && Mage::registry('paymentprofile_data')->getId()) {
            return Mage::helper('authnettoken')->__(
                "Edit Saved Credit Card '%s'",
                $this->htmlEscape(Mage::registry('paymentprofile_data')->getCustomerCardnumber()));
        }
        else {
            $customer = Mage::getModel('customer/customer')->load(Mage::registry('customer_id'));

            return Mage::helper('authnettoken')->__('New Saved Credit Card - ' . $customer->getName());
        }
    }

    /**
     * Return URL to customer edit page
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/customer/edit/tab/customer_info_tabs_paymentprofile',
            array('id' => Mage::registry('customer_id')));
    }

}