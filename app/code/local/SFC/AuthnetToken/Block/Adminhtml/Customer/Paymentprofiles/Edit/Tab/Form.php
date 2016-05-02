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

class SFC_AuthnetToken_Block_Adminhtml_Customer_Paymentprofiles_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare edit form
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        // Create form
        $form = new Varien_Data_Form();
        // Create fieldset
        $fieldset =
            $form->addFieldset('authnettoken_form', array('legend' => Mage::helper('authnettoken')->__('Saved Credit Card Details')));

        // customer id
        $fieldset->addField('customer_id', 'hidden', array(
            'label' => Mage::helper('authnettoken')->__('Customer ID'),
            'class' => '',
            'required' => false,
            'name' => 'customer_id',
        ));

        // CIM payment profile id
        $fieldset->addField('cim_payment_profile_id', 'hidden', array(
            'label' => Mage::helper('authnettoken')->__('CIM Payment Profile ID'),
            'class' => '',
            'required' => false,
            'name' => 'cim_payment_profile_id',
        ));

        // customer id
        $fieldset->addField('customer_id_readonly', 'label', array(
            'label' => Mage::helper('authnettoken')->__('Customer ID'),
            'class' => '',
            'required' => false,
            'readonly' => true,
            'name' => 'customer_id_readonly',
        ));

        // CIM payment profile id
        $fieldset->addField('cim_payment_profile_id_readonly', 'label', array(
            'label' => Mage::helper('authnettoken')->__('CIM Payment Profile ID'),
            'class' => '',
            'required' => false,
            'readonly' => true,
            'name' => 'cim_payment_profile_id_readonly',
        ));

        // customer first name
        $fieldset->addField('customer_fname', 'text', array(
            'label' => Mage::helper('authnettoken')->__('First Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'customer_fname',
        ));

        // customer last name
        $fieldset->addField('customer_lname', 'text', array(
            'label' => Mage::helper('authnettoken')->__('Last Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'customer_lname',
        ));

        // company
        $fieldset->addField('company', 'text', array(
            'label' => Mage::helper('authnettoken')->__('Company'),
            'class' => '',
            'required' => false,
            'name' => 'company',
        ));

        // street
        $fieldset->addField('street', 'text', array(
            'label' => Mage::helper('authnettoken')->__('Address'),
            'class' => '',
            'required' => false,
            'name' => 'street',
        ));

        // country_id
        $fieldset->addField('country_id', 'select', array(
            'label' => Mage::helper('authnettoken')->__('Country'),
            'class' => '',
            'required' => false,
            'values' => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
            'name' => 'country_id',
        ));

        // city
        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('authnettoken')->__('City'),
            'class' => '',
            'required' => false,
            'name' => 'city',
        ));

        // region
        $fieldset->addField('region', 'text', array(
            'label' => Mage::helper('authnettoken')->__('State'),
            'class' => '',
            'required' => false,
            'name' => 'region',
        ));

        // postcode
        $fieldset->addField('postcode', 'text', array(
            'label' => Mage::helper('authnettoken')->__('Zip/Postal Code'),
            'class' => '',
            'required' => false,
            'name' => 'postcode',
        ));

        // phone number
        $fieldset->addField('telephone', 'text', array(
            'label' => Mage::helper('authnettoken')->__('Phone Number'),
            'class' => '',
            'required' => false,
            'name' => 'telephone',
        ));

        // fax number
        $fieldset->addField('fax', 'text', array(
            'label' => Mage::helper('authnettoken')->__('Fax'),
            'class' => '',
            'required' => false,
            'name' => 'fax',
        ));

        // card type
        $fieldset->addField('cc_type', 'select', array(
            'label' => Mage::helper('authnettoken')->__('Card Type'),
            'class' => '',
            'required' => false,
            'values' => Mage::getModel('authnettoken/source_cctype')->toOptionArray(),
            'name' => 'cc_type',
        ));

        // credit card
        $fieldset->addField('customer_cardnumber', 'text', array(
            'label' => Mage::helper('authnettoken')->__('Credit Card'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'customer_cardnumber',
        ));

        // exp date
        $fieldset->addField('exp_date', 'text', array(
            'label' => Mage::helper('authnettoken')->__('Expiration Date'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'exp_date',
            'after_element_html' => "<small style='color:red'>(yyyy-mm)</small>"
        ));

        // Get payment profile from registry
        $model = Mage::registry('paymentprofile_data');
        // Tweak model with extra fields
        $model->setData('customer_id_readonly', $model->getData('customer_id'));
        $model->setData('cim_payment_profile_id_readonly', $model->getData('cim_payment_profile_id'));
        // Set values for form
        $form->setValues($model->getData());
        // Set form on this widget
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
