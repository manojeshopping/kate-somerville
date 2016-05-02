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

class SFC_AuthnetToken_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Authenticate customer
     */
    public function preDispatch()
    {
        // Call parent implementation
        parent::preDispatch();
        // Get customer session
        /** @var Mage_Customer_Model_Session $customerSession */
        $customerSession = Mage::getSingleton('customer/session');
        // Require logged in customer / authenticate customer
        if (!$customerSession->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Authorize customer from session against the specified CIM profile.  This ensures customer is allowed to edit / update / delete profile.
     *
     * @param SFC_AuthnetToken_Model_Cim_Payment_Profile $cimProfile Profile to authorize against current customer
     * @throws SFC_AuthnetToken_Helper_Cim_Exception
     */
    protected function authorizeCustomerForProfile(SFC_AuthnetToken_Model_Cim_Payment_Profile $cimProfile)
    {
        // Get customer session
        /** @var Mage_Customer_Model_Session $customerSession */
        $customerSession = Mage::getSingleton('customer/session');
        // Authorize customer for this profile - in other words profile must belong to this customer
        if ($customerSession->getCustomerId() != $cimProfile->getData('customer_id')) {
            throw new SFC_AuthnetToken_Helper_Cim_Exception('Customer not authorized to edit this profile!');
        }
    }

    /**
     * Customer Dashboard, CIM payment profile grid
     */
    public function indexAction()
    {
        // Load layout
        $this->loadLayout();

        // Set page title
        /** @var Mage_Page_Block_Html_Head $headBlock */
        $headBlock = $this->getLayout()->getBlock('head');
        $headBlock->setTitle($this->__('My Credit Cards'));

        $this->renderLayout();
    }

    /**
     * New CIM payment profile
     */
    public function newAction()
    {
        // Get core session
        /** @var Mage_Core_Model_Session $coreSession */
        $coreSession = Mage::getSingleton('core/session');
        // Get customer session
        /** @var Mage_Customer_Model_Session $customerSession */
        $customerSession = Mage::getSingleton('customer/session');

        // Load layout
        $this->loadLayout();

        // Set page title
        /** @var Mage_Page_Block_Html_Head $headBlock */
        $headBlock = $this->getLayout()->getBlock('head');
        $headBlock->setTitle($this->__('Enter New Saved Credit Card'));

        try {
            // Create new CIM profile
            /** @var SFC_AuthnetToken_Model_Cim_Payment_Profile $cimProfile */
            $cimProfile = Mage::getModel('authnettoken/cim_payment_profile');
            // Init new cim profile with customer info
            $cimProfile->initCimProfileWithCustomerDefault($customerSession->getCustomerId());
            // Pass fields to view for rendering
            $this->getLayout()->getBlock('payment_profile_edit')->setData('cim_profile', $cimProfile);
        }
        catch (Exception $e) {
            $coreSession->addError('Failed to load new credit credit card page!');
            // Send customer back to grid        
            $this->_redirect('creditcards/*/');

            return;
        }

        // Render layout
        $this->renderLayout();
    }

    /**
     * Edit CIM payment profile
     */
    public function editAction()
    {
        // Get core session
        /** @var Mage_Core_Model_Session $coreSession */
        $coreSession = Mage::getSingleton('core/session');

        // Load layout
        $this->loadLayout();

        // Set page title
        /** @var Mage_Page_Block_Html_Head $headBlock */
        $headBlock = $this->getLayout()->getBlock('head');
        $headBlock->setTitle($this->__('Edit Saved Credit Card'));

        try {
            // Get CIM profile ID & load the CIM profile
            $profileId = $this->getRequest()->getParam('id');
            /** @var SFC_AuthnetToken_Model_Cim_Payment_Profile $cimProfile */
            $cimProfile = Mage::getModel('authnettoken/cim_payment_profile')->load($profileId);
            // Authorize customer
            $this->authorizeCustomerForProfile($cimProfile);
            // Get payment profile data from authorize.net
            $cimProfile->retrieveCimProfileData();
            // Pass fields to view for rendering
            $this->getLayout()->getBlock('payment_profile_edit')->setData('cim_profile', $cimProfile);
        }
        catch (SFC_AuthnetToken_Helper_Cim_Exception $eCim) {
            $coreSession->addError('Failed to retrieve credit card from gateway for edit!');
            // Send customer back to grid        
            $this->_redirect('creditcards/*/');

            return;
        }
        catch (Exception $e) {
            $coreSession->addError('Failed to retrieve credit card for edit!');
            // Send customer back to grid        
            $this->_redirect('creditcards/*/');

            return;
        }

        $this->renderLayout();
    }

    /**
     * Save a new CIM payment profile
     */
    public function saveAction()
    {
        // Get core session
        /** @var Mage_Core_Model_Session $coreSession */
        $coreSession = Mage::getSingleton('core/session');

        // Load layout
        $this->loadLayout();

        // Get post data
        $postData = $this->getRequest()->getPost();

        // Create profile model
        $cimProfile = null;
        if ($profileId = $this->getRequest()->getParam('id')) {
            // Load existing profile
            /** @var SFC_AuthnetToken_Model_Cim_Payment_Profile $cimProfile */
            $cimProfile = Mage::getModel('authnettoken/cim_payment_profile')->load($profileId);
        }
        else {
            // Create new profile
            /** @var SFC_AuthnetToken_Model_Cim_Payment_Profile $cimProfile */
            $cimProfile = Mage::getModel('authnettoken/cim_payment_profile');
        }

        // Adjust the exp fields to the proper format
        if ($postData['cc_exp_year'] == 'XXXX' || $postData['cc_exp_month'] == 'XXXX') {
            $postData['exp_date'] = 'XXXX';
        }
        else {
            $postData['exp_date'] = $postData['cc_exp_year'] . '-' . $postData['cc_exp_month'];
        }

        try {
            // Set attributes that can be saved in our DB & Authorize.Net CIM
            $cimProfile->addData($postData);
            // Authorize customer
            $this->authorizeCustomerForProfile($cimProfile);
            // Now try to save payment profile to Auth.net CIM
            $cimProfile->saveCimProfileData(true);
            // Save our DB model
            $cimProfile->save();

            $coreSession->addSuccess('Credit card was successfully saved');
        }
        catch (SFC_AuthnetToken_Helper_Cim_Exception $eCim) {
            $this->_addErrorFromCimException($eCim);
        }
        catch (Exception $e) {
            $coreSession->addError('Failed to save credit card!');
        }

        // Send customer back to grid
        $this->_redirect('creditcards/*/');
    }

    /**
     * Delete payment profile
     */
    public function deleteAction()
    {
        // Get core session
        /** @var Mage_Core_Model_Session $coreSession */
        $coreSession = Mage::getSingleton('core/session');

        // Get id of profile to delete
        $profileId = $this->getRequest()->getParam('id');
        try {
            // Load profile        
            /** @var SFC_AuthnetToken_Model_Cim_Payment_Profile $cimProfile */
            $cimProfile = Mage::getModel('authnettoken/cim_payment_profile')->load($profileId);
            // Authorize customer
            $this->authorizeCustomerForProfile($cimProfile);
            // Delete CIM profile
            $cimProfile->deleteCimProfile();
            // Delete Magento DB row
            $cimProfile->delete();

            $coreSession->addSuccess('Your credit card was successfully deleted.');
        }
        catch (SFC_AuthnetToken_Helper_Cim_Exception $eCim) {
            $coreSession->addError('Failed to connect to gateway to delete saved credit card!');
        }
        catch (Exception $e) {
            $coreSession->addError('Failed to delete saved credit card!');
        }

        // Send customer back to grid        
        $this->_redirect('creditcards/*/');
    }

    protected function _addErrorFromCimException(SFC_AuthnetToken_Helper_Cim_Exception $eCim)
    {
        // Get core session
        /** @var Mage_Core_Model_Session $coreSession */
        $coreSession = Mage::getSingleton('core/session');

        switch ($eCim->getResponse()->getMessageCode()) {
            case 'E00014':
                $coreSession->addError('A required field was not entered for credit card!');
                break;
            case 'E00015':
                $coreSession->addError($eCim->getResponse()->getMessageText());
                break;
            case 'E00039':
                $coreSession->addError('Credit card number is already saved in your account!');
                break;
            case 'E00042':
                $coreSession->addError('You have already saved the maximum number of credit cards!');
                break;
            default:
                $coreSession->addError('Failed to save credit card with gateway!');
                break;
        }
    }

}
