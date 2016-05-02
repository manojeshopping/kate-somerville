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

include('Mage/Adminhtml/controllers/CustomerController.php');

class SFC_AuthnetToken_Override_Admin_CustomerController extends Mage_Adminhtml_CustomerController
{

    /**
     * Delete customer action
     */
    public function deleteAction()
    {
        $this->_initCustomer();
        $customer = Mage::registry('current_customer');
        // delete the customer profile from Authorize.Net
        if (strlen($customer->getData('cim_customer_profile_id')) > 0) {
            try {
                /** @var SFC_AuthnetToken_Helper_Cim $cimHelper */
                $cimHelper = Mage::helper('authnettoken/cim');
                $cimHelper->setConfigWebsite($customer->getWebsiteId());
                $this->deletePaymentProfiles($customer->getId());
                $cimHelper->deleteCustomerProfile($customer->getData('cim_customer_profile_id'));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        if ($customer->getId()) {
            try {
                $customer->load($customer->getId());
                $customer->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The customer has been deleted.'));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/customer');
    }

    /**
     * Mass Delete customer action
     */
    public function massDeleteAction()
    {
        $customerIds = $this->getRequest()->getParam('customer');
        foreach ($customerIds as $id) {
            $customer = Mage::getModel('customer/customer')->load($id);
            // delete all the customer profiles from Authorize.Net
            $this->deletePaymentProfiles($id);
            if ($customer && strlen($customer->getData('cim_customer_profile_id')) > 0) {
                try {
                    /** @var SFC_AuthnetToken_Helper_Cim $cimHelper */
                    $cimHelper = Mage::helper('authnettoken/cim');
                    $cimHelper->setConfigWebsite($customer->getWebsiteId());
                    $cimHelper->deleteCustomerProfile($customer->getData('cim_customer_profile_id'));
                }
                catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        // run parent method
        parent::massDeleteAction();
    }

    /**
     * Delete all payment profiles for each customer
     *
     * @param int $customerId
     */
    public function deletePaymentProfiles($customerId)
    {
        $collection = Mage::getModel('authnettoken/cim_payment_profile')->getCollection()->addfilter('customer_id', $customerId);
        foreach ($collection as $item) {
            try {
                $item->deleteCimProfile();
            }
            catch (Exception $e) {
            }
            try {
                $item->delete();
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }

}
