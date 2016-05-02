<?php

class FME_Geoipultimatelock_Block_Adminhtml_Onlineip_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('onlineipGrid');
        $this->setDefaultSort('last_activity');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('log/visitor_online')
                ->prepare()
                ->getCollection(); //echo '<pre>';print_r($collection->getData());echo '</pre>';exit;
        $collection->addCustomerData();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns() {
        $this->addColumn('customer_id', array(
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '40px',
            'align' => 'right',
            'type' => 'number',
            'default' => Mage::helper('customer')->__('n/a'),
            'index' => 'customer_id'
        ));

        $this->addColumn('firstname', array(
            'header' => Mage::helper('customer')->__('First Name'),
            'default' => Mage::helper('customer')->__('Guest'),
            'index' => 'customer_firstname'
        ));

        $this->addColumn('lastname', array(
            'header' => Mage::helper('customer')->__('Last Name'),
            'default' => Mage::helper('customer')->__('n/a'),
            'index' => 'customer_lastname'
        ));

        $this->addColumn('countryname', array(
            'header' => Mage::helper('geoipultimatelock')->__('Country'),
            'default' => Mage::helper('geoipultimatelock')->__('n/a'),
            'index' => 'country_name',
            'renderer' => new FME_Geoipultimatelock_Block_Adminhtml_Onlineip_Renderer_Flag(),
            'filter' => false,
            'sort' => false
        ));
        
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'default' => Mage::helper('customer')->__('n/a'),
            'index' => 'customer_email'
        ));

        $this->addColumn('ip_address', array(
            'header' => Mage::helper('customer')->__('IP Address'),
            'default' => Mage::helper('customer')->__('n/a'),
            'index' => 'remote_addr',
            'renderer' => 'adminhtml/customer_online_grid_renderer_ip',
            'filter' => false,
            'sort' => false
        ));

        $this->addColumn('session_start_time', array(
            'header' => Mage::helper('customer')->__('Session Start Time'),
            'align' => 'left',
            'width' => '200px',
            'type' => 'datetime',
            'default' => Mage::helper('customer')->__('n/a'),
            'index' => 'first_visit_at'
        ));

        $this->addColumn('last_activity', array(
            'header' => Mage::helper('customer')->__('Last Activity'),
            'align' => 'left',
            'width' => '200px',
            'type' => 'datetime',
            'default' => Mage::helper('customer')->__('n/a'),
            'index' => 'last_visit_at'
        ));

        $typeOptions = array(
            Mage_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER => Mage::helper('customer')->__('Customer'),
            Mage_Log_Model_Visitor::VISITOR_TYPE_VISITOR => Mage::helper('customer')->__('Visitor'),
        );

        $this->addColumn('type', array(
            'header' => Mage::helper('customer')->__('Type'),
            'index' => 'type',
            'type' => 'options',
            'options' => $typeOptions,
//            'renderer'  => 'adminhtml/customer_online_grid_renderer_type',
            'index' => 'visitor_type'
        ));

        $this->addColumn('last_url', array(
            'header' => Mage::helper('customer')->__('Last URL'),
            'type' => 'wrapline',
            'lineLength' => '60',
            'default' => Mage::helper('customer')->__('n/a'),
            'renderer' => 'adminhtml/customer_online_grid_renderer_url',
            'index' => 'last_url'
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('geoipultimatelock')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('geoipultimatelock')->__('Block'),
                    'url' => array('base' => '*/*/blockIp'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('geoipultimatelock_id');
        $this->getMassactionBlock()->setFormFieldName('geoipultimatelock');

        $statuses = array(
            2   => Mage::helper('geoipultimatelock')->__('Block')
        );

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('geoipultimatelock')->__('Change status'),
            'url' => $this->getUrl('*/*/massBlockOnline', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('geoipultimatelock')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        
        return $this;
    }

    public function getRowUrl($row) {
        return (Mage::getSingleton('admin/session')->isAllowed('customer/manage') && $row->getCustomerId()) ?
                $this->getUrl('*/customer/edit', array('id' => $row->getCustomerId())) : '';
    }

}
