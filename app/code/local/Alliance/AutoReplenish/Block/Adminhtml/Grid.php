<?php

class Alliance_AutoReplenish_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
		parent::__construct();
		$this->setId('autoreplenish_id');
        $this->setDefaultLimit(1000);
		$this->setDefaultSort('order_create_date');
		$this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('autoreplenish/autoreplenish')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('order_id', array(
            'header'       => Mage::helper('autoreplenish')->__('Order ID'),
            'align'        => 'right',
            'filter_index' => 'order_id',
            'index'        => 'order_id',
            'type'         => 'text',
			'renderer'     => 'Alliance_AutoReplenish_Block_Adminhtml_Renderer_Hyperlink',
			'align'        => 'left',
        ));
		
		$this->addColumn('order_create_date', array(
            'header'       => Mage::helper('autoreplenish')->__('Order Creation	Date'),
            'align'        => 'right',
            'filter_index' => 'order_create_date',
            'index'        => 'order_create_date',
            'type'         => 'date',
			'align'        => 'left',
        ));
		
		$this->addColumn('next_order_date', array(
            'header'       => Mage::helper('autoreplenish')->__('Next Order Date'),
            'align'        => 'right',
            'filter_index' => 'next_order_date',
            'index'        => 'next_order_date',
            'type'         => 'date',
			'align'        => 'left',
        ));
		
		$this->addColumn('customer_email', array(
            'header'       => Mage::helper('autoreplenish')->__('Customer Email'),
            'align'        => 'right',
            'filter_index' => 'customer_email',
            'index'        => 'customer_email',
            'type'         => 'text',
			'renderer'     => 'Alliance_AutoReplenish_Block_Adminhtml_Renderer_Hyperlink',
			'align'        => 'left',
        ));
		
		$this->addColumn('customer_id', array(
            'header'       => Mage::helper('autoreplenish')->__('Customer ID'),
            'align'        => 'right',
            'filter_index' => 'customer_id',
            'index'        => 'customer_id',
            'type'         => 'text',
			'renderer'     => 'Alliance_AutoReplenish_Block_Adminhtml_Renderer_Hyperlink',
			'align'        => 'left',
        ));
		
		$this->addColumn('product_id', array(
            'header'       => Mage::helper('autoreplenish')->__('Product ID'),
            'align'        => 'right',
            'filter_index' => 'product_id',
            'index'        => 'product_id',
            'type'         => 'text',
			'align'        => 'left',
        ));
		
		$this->addColumn('sku', array(
            'header'       => Mage::helper('autoreplenish')->__('Product SKU'),
            'align'        => 'right',
            'filter_index' => 'sku',
            'index'        => 'sku',
            'type'         => 'text',
			'align'        => 'left',
        ));
		
		$this->addColumn('product_name', array(
            'header'       => Mage::helper('autoreplenish')->__('Product Name'),
            'align'        => 'right',
            'sortable'	   => false,
			'filter'	   => false,
            'index'        => 'product_id',
            'type'         => 'text',
			'renderer'     => 'Alliance_AutoReplenish_Block_Adminhtml_Renderer_Productname',
			'align'        => 'left',
        ));
		
		$this->addColumn('qty', array(
            'header'       => Mage::helper('autoreplenish')->__('Quantity'),
            'align'        => 'right',
            'filter_index' => 'qty',
            'index'        => 'qty',
            'type'         => 'text',
			'align'        => 'right',
        ));
		
		$this->addColumn('frequency', array(
            'header'       => Mage::helper('autoreplenish')->__('Frequency'),
            'align'        => 'right',
            'filter_index' => 'frequency',
            'index'        => 'frequency',
            'type'         => 'text',
			'align'        => 'right',
        ));
		
		$this->addColumn('status', array(
            'header'       => Mage::helper('autoreplenish')->__('Status'),
            'align'        => 'right',
            'filter_index' => 'status',
            'index'        => 'status',
            'type'         => 'options',
			'options'	   => array(1=>'Active',0=>'Inactive'),
			'align'        => 'right',
        ));
		
		$this->addColumn('loyalty_points', array(
            'header'       => Mage::helper('autoreplenish')->__('Loyalty Points'),
            'align'        => 'right',
            'filter_index' => 'loyalty_points',
            'index'        => 'loyalty_points',
            'type'         => 'options',
			'options'	   => array(0=>'No',1=>'Yes',2=>'No Account'),
			'align'        => 'right',
        ));
		
		$this->addExportType('*/*/exportCsv',
         Mage::helper('autoreplenish')->__('CSV'));
		
		return parent::_prepareColumns();
    }
}
