<?php
/**
 * Log entry grid class
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor. Init values that
	 * will be used to create grid
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('ogLogGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('desc');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('ordergroove_filter');

	}

	/**
	 * Load the log entries collection
	 */
	protected function _prepareCollection()
	{
		$collection	=	Mage::getModel('ordergroove/log')->getCollection();
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	/**
	 * Prepare the columns that will be
	 * displayed in the grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('entity_id',
			array(
				'header'=> Mage::helper('ordergroove')->__('ID'),
				'width' => '20px',
				'type'  => 'number',
				'index' => 'entity_id',
		));
		$this->addColumn('website_id',
			array(
				'header'=> Mage::helper('ordergroove')->__('Website'),
				'width' => '20px',
				'type'		=>	'options',
				'options'	=>	$this->getWebsiteOptions(),
				'index' => 'website_id',
		));
		$this->addColumn('activity',
			array(
				'header'=> Mage::helper('ordergroove')->__('Activity'),
				'width' => '150px',
				'type'  => 'text',
				'index' => 'activity',
		));    

		$this->addColumn('type',
			array(
				'header'	=>	Mage::helper('ordergroove')->__('Type'),
				'width' 	=>	'150px',
				'index'		=>	'type',
				'type'		=>	'options',
				'options'	=>	$this->getTypes()
		));
		$this->addColumn('message',
			array(
				'header'	=>	Mage::helper('ordergroove')->__('Message'),
				'width' 	=>	'150px',
				'index'		=>	'message',
				'type'		=>	'text',
		));
		$this->addColumn('log_date',
			array(
				'header'	=>	Mage::helper('ordergroove')->__('Date (Store Time Zone)'),
				'width' 	=>	'150px',
				'index'		=>	'log_date',
				'type'		=>	'text',
		));
		$this->addColumn('is_read',
			array(
				'header'	=>	Mage::helper('ordergroove')->__('Is Read'),
				'width' 	=>	'150px',
				'index'		=>	'is_read',
				'type'		=>	'options',
				'options'	=>	$this->getBooleanOptions()
		));
		
		$this->addColumn('action',
			array(
				'header'    => Mage::helper('ordergroove')->__('Action'),
				'width'     => '50px',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper('ordergroove')->__('View Log Entry'),
						'url'     => array(
							'base'=>'*/*/edit'
						),
						'field'   => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
		));
		return parent::_prepareColumns();
	}

	/**
	 * Add the mass delete function to the grid
	 */
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('og_logs');
		$this->getMassactionBlock()->addItem('delete', array(
			 'label'=> Mage::helper('ordergroove')->__('Delete'),
			 'url'  => $this->getUrl('*/*/massDelete'),
			 'confirm' => Mage::helper('ordergroove')->__('This will permanently delete the log message(s)')
		));

		return $this;
	}

	/**
	 * Return the ajax grid url
	 * 
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}

	/**
	 * Get the url for clicking on a
	 * single row.
	 * 
	 * @param Pixafy_Ordergroove_Model_Log
	 * @return string
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
	}
	
	/**
	 * Return the boolean yes / no array
	 * 
	 * @return array
	 */
	protected function getBooleanOptions(){
		return array(0=>$this->__('No'), 1=>$this->__('Yes'));
	}
	
	/**
	 * Return an array containing website data
	 * with format websiteId => websiteName
	 * 
	 * @return array
	 */
	public function getWebsiteOptions(){
		$websites	=	array();
		foreach(Mage::getModel('core/website')->getCollection() as $website){
			$websites[$website->getId()]	=	$website->getName();
		}
		return $websites;
	}
	
	/**
	 * Return the log types array
	 * 
	 * @return array
	 */
	public function getTypes(){
		return Pixafy_Ordergroove_Helper_Constants::getLogTypes();
	}
}
