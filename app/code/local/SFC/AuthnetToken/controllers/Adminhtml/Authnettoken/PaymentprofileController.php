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

class SFC_AuthnetToken_Adminhtml_Authnettoken_PaymentprofileController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Set admin breadcrumbs
     */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('customers/authnettoken');
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Payment Profile  Manager'),
            Mage::helper('adminhtml')->__('Payment Profile Manager'));

        return $this;
    }

    /**
     * Set default page title
     */
    public function indexAction()
    {
        $this->_title($this->__('Customer'));
        $this->_title($this->__('Payment Profiles'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Edit existing CIM payment profile
     */
    public function editAction()
    {
        $this->_title($this->__('AuthnetToken'));
        $this->_title($this->__('Customer'));
        $this->_title($this->__('Edit Item'));

        $profileId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('authnettoken/cim_payment_profile')->load($profileId);
        if ($model->getId()) {

            // Retreive extra data fields from Authorize.Net CIM API
            try {
                $model->retrieveCimProfileData();
            }
            catch (SFC_AuthnetToken_Helper_Cim_Exception $eCim) {
                Mage::getSingleton('adminhtml/session')->addError('Failed to retrieve saved credit card info from Authorize.Net CIM!');
                if ($eCim->getResponse() != null) {
                    Mage::getSingleton('adminhtml/session')->addError('CIM Result Code: ' . $eCim->getResponse()->getResultCode());
                    Mage::getSingleton('adminhtml/session')->addError('CIM Message Code: ' . $eCim->getResponse()->getMessageCode());
                    Mage::getSingleton('adminhtml/session')->addError('CIM Message Text: ' . $eCim->getResponse()->getMessageText());
                }
                // Send customer back to saved credit cards grid
                $this->_redirect('adminhtml/customer/edit/tab/customer_info_tabs_paymentprofile',
                    array('id' => $model->getCustomerId()));

                return;
            }
            // Save profile in registry
            Mage::register('paymentprofile_data', $model);
            Mage::register('customer_id', $model->getCustomerId());

            $this->loadLayout();
            $this->_setActiveMenu('authnettoken/paymentprofile');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Payment Profile'), Mage::helper('adminhtml')->__('Payment Profile'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Payment Profile'), Mage::helper('adminhtml')->__('Information'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('authnettoken/adminhtml_customer_paymentprofiles_edit'));
            $this->_addLeft($this->getLayout()->createBlock('authnettoken/adminhtml_customer_paymentprofiles_edit_tabs'));
            $this->renderLayout();
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('authnettoken')->__('Failed to find saved credit card.'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Create new CIM payment profile
     */
    public function newAction()
    {
        $this->_title($this->__('AuthnetToken'));
        $this->_title($this->__('Payment Profile'));
        $this->_title($this->__('New Payment Profile'));

        // Create new model for editing & init with customer fields
        $model = Mage::getModel('authnettoken/cim_payment_profile');
        $model->initCimProfileWithCustomerDefault($this->getRequest()->getParam('customerid'));

        // Save profile in registry
        Mage::register('paymentprofile_data', $model);
        Mage::register('customer_id', $model->getCustomerId());

        $this->loadLayout();
        $this->_setActiveMenu('authnettoken/paymentprofile');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Payment Profile Manager'), Mage::helper('adminhtml')
            ->__('Payment Profile Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Info'), Mage::helper('adminhtml')->__('info'));


        $this->_addContent($this->getLayout()->createBlock('authnettoken/adminhtml_customer_paymentprofiles_edit'));
        $this->_addLeft($this->getLayout()->createBlock('authnettoken/adminhtml_customer_paymentprofiles_edit_tabs'));

        $this->renderLayout();

    }

    /**
     * Save data to existing payment profile
     */
    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();

        if ($profileId = $this->getRequest()->getParam('id')) {
            $model = Mage::getModel('authnettoken/cim_payment_profile')->load($profileId);
        }
        else {
            $model = Mage::getModel('authnettoken/cim_payment_profile');
        }

        if ($postData) {

            try {
                try {
                    // Save post data to model
                    $model->addData($postData);
                    // Now try to save payment profile to Auth.net CIM
                    $model->saveCimProfileData(true);
                }
                catch (SFC_AuthnetToken_Helper_Cim_Exception $eCim) {
                    Mage::getSingleton('adminhtml/session')->addError('Failed to save credit card to Authorize.Net CIM!');
                    if ($eCim->getResponse() != null) {
                        Mage::getSingleton('adminhtml/session')->addError('CIM Result Code: ' . $eCim->getResponse()->getResultCode());
                        Mage::getSingleton('adminhtml/session')->addError('CIM Message Code: ' .
                        $eCim->getResponse()->getMessageCode());
                        Mage::getSingleton('adminhtml/session')->addError('CIM Message Text: ' .
                        $eCim->getResponse()->getMessageText());
                    }
                    // Send customer back to saved credit cards grid
                    $this->_redirect('adminhtml/customer/edit/tab/customer_info_tabs_paymentprofile',
                        array('id' => $postData['customer_id']));

                    return;
                }

                // Now save model
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Saved credit card ' . $model->getCustomerCardnumber() . '.'));
                Mage::getSingleton('adminhtml/session')->setCustomerData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));

                    return;
                }
                $this->_redirect('adminhtml/customer/edit/tab/customer_info_tabs_paymentprofile',
                    array('id' => $model->getCustomerId()));

                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError('Failed to save credit card!');
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // Send customer back to saved credit cards grid
                $this->_redirect('adminhtml/customer/edit/tab/customer_info_tabs_paymentprofile',
                    array('id' => $postData['customer_id']));
            }
        }

        // Send customer back to saved credit cards grid
        $this->_redirect('adminhtml/customer/edit/tab/customer_info_tabs_paymentprofile', array('id' => $postData['customer_id']));
    }

    /**
     * Delete CIM payment profile
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('authnettoken/cim_payment_profile')->load($this->getRequest()->getParam('id'));
                $customerId = $model->getData('customer_id');
                // Delete Auth.net CIM payment profile
                try {
                    $model->deleteCimProfile();
                }
                catch (SFC_AuthnetToken_Helper_Cim_Exception $eCim) {
                    Mage::getSingleton('adminhtml/session')->addError('Failed to delete CIM profile at Authorize.Net!');
                    if ($eCim->getResponse() != null) {
                        Mage::getSingleton('adminhtml/session')->addError('CIM Result Code: ' . $eCim->getResponse()->getResultCode());
                        Mage::getSingleton('adminhtml/session')->addError('CIM Message Code: ' .
                        $eCim->getResponse()->getMessageCode());
                        Mage::getSingleton('adminhtml/session')->addError('CIM Message Text: ' .
                        $eCim->getResponse()->getMessageText());
                    }
                    // Send back to saved credit cards grid
                    $this->_redirect('adminhtml/customer/edit/tab/customer_info_tabs_paymentprofile', array('id' => $customerId));

                    return;
                }
                // Now delete Magento model
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Saved credit card ' . $model->getCustomerCardnumber() . ' deleted.'));
                // Send back to saved credit cards grid
                $this->_redirect('adminhtml/customer/edit/tab/customer_info_tabs_paymentprofile', array('id' => $customerId));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError('Failed to delete saved credit card!');
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // Send admin back to manage customers
                $this->_redirect('adminhtml/customer/index');
            }
        }
    }

    /**
     * Delete multiple items
     */
    public function massRemoveAction()
    {
        try {
            $profileIds = $this->getRequest()->getPost('ids', array());
            $customerId = Mage::getModel('authnettoken/cim_payment_profile')->load($profileIds[0])->getCustomerId();
            foreach ($profileIds as $profileId) {
                $model = Mage::getModel('authnettoken/cim_payment_profile')->load($profileId);
                try {
                    try {
                        // Delete Auth.net CIM payment profile
                        $model->deleteCimProfile();
                    }
                    catch (SFC_AuthnetToken_Helper_Cim_Exception $eCim) {
                        $message =
                            'Failed to delete CIM profile at Authorize.Net for card: ' . $model->getCustomerCardnumber();
                        if ($eCim->getResponse() != null) {
                            $message .=
                                ' (Code: ' . $eCim->getResponse()->getMessageCode() . ' Message: ' .
                                $eCim->getResponse()->getMessageText() . ')';
                        }
                        Mage::getSingleton('adminhtml/session')->addError($message);
                    }
                    // Now delete Magento model
                    $model->delete();
                }
                catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deleted saved credit card(s).'));
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError('Failed to delete saved credit card!');
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('adminhtml/customer/edit/tab/customer_info_tabs_paymentprofile', array('id' => $customerId));
    }

    /**
     * Profile grid for AJAX request
     */
    public function gridAction()
    {
        $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
        Mage::register('current_customer', $customer);
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('authnettoken/adminhtml_customer_paymentprofiles_paymentprofile')->toHtml()
        );
    }

}
