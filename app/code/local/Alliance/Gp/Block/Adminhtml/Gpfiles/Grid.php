<?php
class Alliance_Gp_Block_Adminhtml_Gpfiles_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('gpGridfiles');
		
		// This is the primary key of the database
		$this->setDefaultSort('file_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
 
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('gp/gpfiles')->getCollection();
		$this->setCollection($collection);
		
		return parent::_prepareCollection();
	}
 
	protected function _prepareColumns()
	{
		$this->addColumn('file_id', array(
			'header' 	=> Mage::helper('gp')->__('File Id'),
			'align' 	=>'right',
			'width' 	=> '150px',
			'index' 	=> 'file_id',
		));

		$this->addColumn('file_name', array(
			'header' 	=> Mage::helper('gp')->__('File Name'),
			'align' 	=>'left',
			'index' 	=> 'file_name',
		));

		$this->addColumn('timestamp', array(
			'header' 	=> Mage::helper('gp')->__('Creation Time'),
			'align' 	=> 'left',
			'width' 	=> '120px',
			'type' 	=> 'date',
			'default' 	=> '--',
			'index' 	=> 'timestamp',
		));
		
		$this->addColumn('file_size', array(
			'header' 		=> Mage::helper('gp')->__('File Size'),
			'align' 		=>'right',
			'width' 		=> '150px',
			'index' 		=> 'file_size',
			'renderer' 	=> 'Alliance_Gp_Block_Adminhtml_Gpfiles_Renderer_Filesize'
		));
		
		$this->addColumn('orders', array(
			'header' 	=> Mage::helper('gp')->__('Orders Count'),
			'align' 	=>'right',
			'width' 	=> '150px',
			'index' 	=> 'orders',
		));

		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/download', array('file_id' => $row->getId()));
	}
	
	public function getGridUrl()
	{
		return $this->getUrl('*/*/gridfiles', array('_current'=>true));
	}
 
 
}