<?php

class FME_Geoipultimatelock_Block_Adminhtml_Ipblocked_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_status = null;

    public function __construct() {
        parent::__construct();
        $this->setId('ipblockedGrid');
        $this->setDefaultSort('geoipblockedips_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        try {
            $collection = Mage::getModel('geoipultimatelock/geoipblockedips')
                    ->getCollection();
            /* @var $collection Mage_Log_Model_Mysql4_Visitor_Online_Collection */
            //$collection->addCustomerData(); //echo (string) $collection->getSelect();exit;
            $this->setCollection($collection);
            parent::_prepareCollection();

            return $this;
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }
    }

    protected function _prepareColumns() {

        $this->addColumn('id', array(
            'header' => Mage::helper('geoipultimatelock')->__('ID'),
            'width' => '40px',
            'align' => 'right',
            'type' => 'number',
            'default' => Mage::helper('geoipultimatelock')->__('n/a'),
            'index' => 'geoipblockedips_id'
        ));

        $this->addColumn('customer_id', array(
            'header' => Mage::helper('customer')->__('Customer ID'),
            'width' => '40px',
            'align' => 'right',
            'type' => 'number',
            'default' => Mage::helper('customer')->__('n/a'),
            'index' => 'customer_id'
        ));

        $this->addColumn('firstname', array(
            'header' => Mage::helper('customer')->__('First Name'),
            'default' => Mage::helper('customer')->__('Guest'),
            'index' => 'customer_firstname',
            'filter' => false,
        ));

        $this->addColumn('countryname', array(
            'header' => Mage::helper('geoipultimatelock')->__('Country'),
            'default' => Mage::helper('geoipultimatelock')->__('n/a'),
            'index' => 'country_name',
            'renderer' => new FME_Geoipultimatelock_Block_Adminhtml_Onlineip_Renderer_Flag(),
            'filter' => false,
            'sort' => false
        ));

        $this->addColumn('blockedips', array(
            'header' => Mage::helper('customer')->__('IP Address'),
            'default' => Mage::helper('customer')->__('n/a'),
            'index' => 'blocked_ip',
            //'renderer' => 'adminhtml/customer_online_grid_renderer_ip',
            'filter' => false,
            'sort' => false
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
            'index' => 'type'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('geoipultimatelock')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Unblocked',
                2 => 'Blocked',
            ),
        ));

        //$status = $this->_getRowData($this->getRowId($row),'status');

        $this->addColumn('action', array(
            'header' => Mage::helper('geoipultimatelock')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('geoipultimatelock')->__("Block/Unblock"),
                    'url' => array('base' => '*/*/unblockIp'),
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

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('geoipultimatelock')->__('Delete'),
            'url' => $this->getUrl('*/*/allIpStatusDelete'),
            'confirm' => Mage::helper('geoipultimatelock')->__('Are you sure?')
        ));

        $statuses = array(
            1 => Mage::helper('geoipultimatelock')->__('Unblock'),
            2 => Mage::helper('geoipultimatelock')->__('Block')
        );

        array_unshift($statuses, array('label' => '', 'value' => '')); //echo '<pre>';print_r($statuses);echo '</pre>';
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('geoipultimatelock')->__('Change status'),
            'url' => $this->getUrl('*/*/massBlockStatus', array('_current' => true)),
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

}