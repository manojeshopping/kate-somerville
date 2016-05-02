<?php

class Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();

		$this->setDefaultSort('entity_id', 'desc');
		$this->setId('alliance_fivehundredfriends_redeempoints_grid');
		$this->setSaveParametersInSession(true);
	}

	
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel($this->_getCollectionClass());
		
		$collection->getSelect()->joinLeft(
			array('order' => $collection->getTable('sales/order')), 
			'main_table.order_id = order.entity_id',
			array('increment_id' => 'increment_id', 'order_status_origin' => 'status')
		);
		
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('entity_id',
			array(
				'header' => $this->__('ID'),
				'index' => 'entity_id',
				'width' => '50px',
				'align' => 'right',
			)
		);

		$this->addColumn('customer_email',
			array(
				'header' => $this->__('Customer Email'),
				'width' => '50px',
				'index' => 'customer_email',
				'filter_index' => 'main_table.customer_email',
			)
		);

		$this->addColumn('redeem_points',
			array(
				'header' => $this->__('Points Redeemed'),
				'index' => 'redeem_points',
				'width' => '50px',
				'align' => 'right',
			)
		);

		$this->addColumn('status',
			array(
				'header' => $this->__('Status'),
				'index' => 'status',
				'filter_index' => 'main_table.status',
				'type'  => 'options',
				'width' => '50px',
				'options' => array(
					'pending' => 'Pending',
					'place_before' => 'Place Before',
					'redeemed' => 'Redeemed',
					'retured' => 'Returned',
				),
			)
		);

		$this->addColumn('increment_id',
			array(
				'header' => $this->__('Order #'),
				'index' => 'order_id',
				'width' => '50px',
				'align' => 'right',
				'renderer' =>  'Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints_Renderer_Incrementid',
			)
		);

		$this->addColumn('order_status_origin',
			array(
				'header' => $this->__('Order Status'),
				'index' => 'order_status_origin',
				'filter_index' => 'order.status',
				'type'  => 'options',
				'width' => '50px',
				'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
			)
		);
		
		$this->addColumn('order_date',
			array(
				'header' => $this->__('Order Date'),
				'index' => 'order_date',
				'type' => 'datetime',
				'width' => '50px',
			)
		);

		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'referer' => Mage::helper('core')->urlEncode(Mage::helper('core/url')->getCurrentUrl())));
	}

	
	protected function _getCollectionClass()
	{
		return 'alliance_fivehundredfriends/redemption_collection';
	}
}