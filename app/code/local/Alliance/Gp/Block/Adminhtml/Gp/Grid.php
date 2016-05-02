<?php

class Alliance_Gp_Block_Adminhtml_Gp_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('gpGrid');
		
		// This is the primary key of the database
		$this->setDefaultSort('increment_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
 
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('gp/gp')->getCollection();
		$this->setCollection($collection);
		
		return parent::_prepareCollection();
	}
 
	protected function _prepareColumns()
	{
		$this->addColumn('increment_id', array(
			'header' 	=> Mage::helper('gp')->__('Increment Id'),
			'align' 	=>'right',
			'width' 	=> '150px',
			'index' 	=> 'increment_id',
		));

		$this->addColumn('customer_email', array(
			'header' 	=> Mage::helper('gp')->__('Email'),
			'align' 	=>'left',
			'index' 	=> 'customer_email',
		));

		$this->addColumn('file', array(
			'header' 	=> Mage::helper('gp')->__('File'),
			'align' 	=>'left',
			'index' 	=> 'file',
		));

		$this->addColumn('timestamp', array(
			'header' 	=> Mage::helper('gp')->__('Creation Time'),
			'align' 	=> 'left',
			'width' 	=> '120px',
			'type' 	=> 'date',
			'default' 	=> '--',
			'index' 	=> 'timestamp',
		));

		$this->addColumn('status', array(
			'header'    => Mage::helper('gp')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
				'exported' => 'Successful',
				'reimported' => 'Re-Imported',
			),
		));

		return parent::_prepareColumns();
	}
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('order_id');
		$this->getMassactionBlock()->setFormFieldName('order_id');
		
		$this->getMassactionBlock()->addItem('reimport', array(
			'label'=> Mage::helper('gp')->__('Re-Import Orders'),
			'url'  => $this->getUrl('*/*/reimport', array('' => '')),
			'confirm' => Mage::helper('gp')->__('The selected order(s) will be reimported into the next GP batch')
		));
	return $this;
	}
	
	public function getRowUrl($row)
	{
		return Mage::helper('adminhtml')->getUrl('backend/sales_order/view', array('order_id' => $row->getId()));
	}
	
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
 
 
}