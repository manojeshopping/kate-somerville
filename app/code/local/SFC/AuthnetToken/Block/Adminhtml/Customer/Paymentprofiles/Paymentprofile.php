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

class SFC_AuthnetToken_Block_Adminhtml_Customer_Paymentprofiles_Paymentprofile
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_chat = null;

    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('paymentprofile');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
     */
    protected function _prepareCollection()
    {
        // get collection filtered by the current customer id
        $collection = Mage::getModel('authnettoken/cim_payment_profile')->getCollection();
        $collection->addFieldToFilter('customer_id', $this->getCustomer());

        // set collection
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Create grid buttons
     */
    protected function _prepareLayout()
    {
        // get url to add new payment profile
        $urlString = 'authnettoken/adminhtml_paymentprofile/new';
        $url = $this->getUrl($urlString, array('customerid' => Mage::registry('current_customer')->getId()));
        // add new CIM payment profile
        $this->setChild('payment_profile_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Add Saved Credit Card'),
                    'onclick' => 'setLocation(\'' . $url . '\')',
                ))
        );

        // export to csv button
        $this->setChild('export_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Export'),
                    'onclick' => $this->getJsObjectName() . '.doExport()',
                    'class' => 'task'
                ))
        );

        // reset filter button
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Reset Filter'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                ))
        );

        // search button
        $this->setChild('search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Search'),
                    'onclick' => $this->getJsObjectName() . '.doFilter()',
                    'class' => 'task'
                ))
        );
        parent::_prepareLayout();
    }

    /**
     * Generate the html for out new payment profile button
     */
    public function  getSearchButtonHtml()
    {
        return parent::getSearchButtonHtml() . $this->getChildHtml('payment_profile_button');
    }

    /**
     * Generate grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns()
     */
    protected function _prepareColumns()
    {
        // payment profile id
        $this->addColumn('id', array(
            'header' => Mage::helper('authnettoken')->__('Id'),
            'align' => 'left',
            'width' => '30px',
            'type' => 'number',
            'index' => 'id',
            'filter' => false,
        ));

        // customer first name
        $this->addColumn('customer_fname', array(
            'header' => Mage::helper('authnettoken')->__('Customer First Name'),
            'index' => 'customer_fname',
            'type' => 'text'
        ));

        // customer last name
        $this->addColumn('customer_lname', array(
            'header' => Mage::helper('authnettoken')->__('Customer Last Name'),
            'index' => 'customer_lname',
            'type' => 'text'
        ));

        // CIM payment proile id
        $this->addColumn('cim_payment_profile_id', array(
            'header' => Mage::helper('authnettoken')->__('CIM Payment Profile Id'),
            'index' => 'cim_payment_profile_id',
            'type' => 'text'
        ));

        // card number
        $this->addColumn('customer_cardnumber', array(
            'header' => Mage::helper('authnettoken')->__('Card Number'),
            'index' => 'customer_cardnumber',
            'type' => 'text'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get URL to view the payment profile
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/authnettoken_paymentprofile/edit', array('id' => $row->getId()));
    }

    /**
     * Get the url for this grid. Used for ajax calls.
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/authnettoken_paymentprofile/grid', array('_current' => true));
    }

    /**
     * Delete multiple items from grid
     *
     * @return SFC_AuthnetToken_Block_Adminhtml_Paymentprofile_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_profile', array(
            'label' => Mage::helper('authnettoken')->__('Delete Saved Credit Card'),
            'url' => $this->getUrl('adminhtml/authnettoken_paymentprofile/massRemove'),
            'confirm' => Mage::helper('authnettoken')->__('Are you sure?')
        ));

        return $this;
    }

    /**
     * ######################## TAB settings #################################
     */

    // tab label
    public function getTabLabel()
    {
        return $this->__('Saved Credit Cards (CIM)');
    }

    // tab title
    public function getTabTitle()
    {
        return $this->__('Saved Credit Cards (Authorize.Net CIM)');
    }

    // tab display
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');

        return (bool)$customer->getId();
    }

    // tab hide
    public function isHidden()
    {
        return false;
    }

    // get customer id
    public function getCustomer()
    {
        return Mage::registry('current_customer')->getId();
    }

}