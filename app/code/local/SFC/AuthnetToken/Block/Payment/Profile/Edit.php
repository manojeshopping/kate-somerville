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

class SFC_AuthnetToken_Block_Payment_Profile_Edit extends Mage_Adminhtml_Block_Template
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    public function getTitle()
    {
        if (strlen($this->getCimProfile()->getId())) {
            // Editing an existing profile
            return ' Edit Saved Credit Card ' . $this->getCimProfile()->getCustomerCardnumber();
        }
        else {
            // Creating a new profile
            return ' Enter New Credit Card ';
        }
    }

    public function getBackUrl()
    {
        return $this->getUrl('creditcards/index/', array('_secure' => true));
    }

    public function getSaveUrl()
    {
        if (strlen($this->getCimProfile()->getId())) {
            // Editing an existing profile
            return Mage::getUrl('creditcards/index/save', array('id' => $this->getCimProfile()->getId()));
        }
        else {
            // Creating a new profile
            return Mage::getUrl('creditcards/index/save', array('customerid' => $this->getRequest()->getParam('id')));
        }

    }

    /**
     * Retrieve payment configuration object
     *
     * @return Mage_Payment_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('payment/config');
    }

    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    public function getCcMonths()
    {
        $months = $this->getData('cc_months');
        $output = array();
        if (is_null($months)) {
            $months[0] = $this->__('Month');
            $months = array_merge($months, $this->_getConfig()->getMonths());
            foreach ($months as $k => $v) {
                if (strlen($k) == 1 && $k != 0) {
                    $value = '0' . $k;
                    $output[$value] = $v;
                }
                elseif ($v != 'Month') {
                    $output[$k] = $v;
                }
            }
            $this->setData('cc_months', $months);
        }

        return $output;
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    public function getCcYears()
    {
        $years = $this->getData('cc_years');
        if (is_null($years)) {
            $years = $this->_getConfig()->getYears();
            $this->setData('cc_years', $years);
        }

        return $years;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|Mage_Core_Model_Store $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'payment/' . $this->getCode() . '/' . $field;

        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * Retrieve available credit card types
     *
     * @return array
     */
    public function getCcAvailableTypes()
    {
        return Mage::getModel('authnettoken/source_cctype')->getCcAvailableTypes();
    }

}
