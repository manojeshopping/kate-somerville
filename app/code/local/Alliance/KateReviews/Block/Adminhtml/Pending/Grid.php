<?php

class Alliance_KateReviews_Block_Adminhtml_Pending_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();

		$this->setDefaultSort('date');
		$this->setId('alliance_katereviews_pending_grid');
		$this->setDefaultDir('desc');
		$this->setSaveParametersInSession(true);
	}

	protected function _getCollectionClass()
	{
		return 'alliance_katereviews/review_collection';
	}

	protected function _prepareCollection()
	{

		$collection = Mage::getResourceModel($this->_getCollectionClass());
		$collection->addFieldToFilter('status', array(
			'eq' => 'Pending',
		));
		$this->setCollection($collection);

		return parent::_prepareCollection();

	}

	protected function _prepareColumns()
	{
		$this->addColumn('id',
			array(
				'header' => $this->__('ID'),
				'index' => 'id',
				'width' => '50px',
				'align' => 'right',
			)
		);
		$this->addColumn('date',
			array(
				'header' => $this->__('Date'),
				'index' => 'date',
				'width' => '50px',
				'type' => 'date',
			)
		);

		$this->addColumn('star_rating',
			array(
				'header' => $this->__('Star Rating'),
				'index' => 'star_rating',
				'width' => '50px',
				'align' => 'right',
			)
		);

		$this->addColumn('product_sku',
			array(
				'header' => $this->__('Product SKU'),
				'width' => '50px',
				'index' => 'product_sku',
			)
		);

		$this->addColumn('product_name',
			array(
				'header' => $this->__('Product Name'),
				'width' => '160px',
				'index' => 'product_name',
			)
		);

		$this->addColumn('customer_name',
			array(
				'header' => $this->__('Customer Name'),
				'width' => '50px',
				'index' => 'customer_name',
			)
		);

		$this->addColumn('customer_email',
			array(
				'header' => $this->__('Customer Email'),
				'width' => '50px',
				'index' => 'customer_email',
			)
		);

		$this->addColumn('review_headline',
			array(
				'header' => $this->__('Headline'),
				'index' => 'review_headline',
				'width' => '160px',
				'type' => 'text',
			)
		);

		$this->addColumn('review_text',
			array(
				'header' => $this->__('Review Text'),
				'index' => 'review_text',
				'type' => 'text',
			)
		);

		$this->addColumn('status',
			array(
				'header' => $this->__('Status'),
				'index' => 'status',
				'width' => '50px',
			)
		);

		$this->addColumn('action_approve', array(
			'header' => $this->helper('alliance_katereviews')->__('Approve'),
			'width' => 15,
			'sortable' => false,
			'filter' => false,
			'type' => 'action',
			'getter' => 'getId',
			'actions' => array(
				array(
					'url' => array(
						'base' => '*/*/approve',
						'params' => array('store' => $this->getRequest()->getParam('store')),
					),
					'field' => 'id',
					'caption' => $this->helper('alliance_katereviews')->__('Approve'),
				),
			)
		));

		$this->addColumn('action_deny', array(
			'header' => $this->helper('alliance_katereviews')->__('Deny'),
			'width' => 15,
			'sortable' => false,
			'filter' => false,
			'type' => 'action',
			'getter' => 'getId',
			'actions' => array(
				array(
					'url' => array(
						'base' => '*/*/deny',
						'params' => array('store' => $this->getRequest()->getParam('store')),
					),
					'field' => 'id',
					'caption' => $this->helper('alliance_katereviews')->__('Deny'),
				),
			)
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('review_ids');
		$this->getMassactionBlock()->addItem('approve', array(
			'label' => Mage::helper('alliance_katereviews')->__('Approve'),
			'url' => $this->getUrl('*/*/massPendingApprove', array('' => '')),
		));
		$this->getMassactionBlock()->addItem('deny', array(
			'label' => Mage::helper('alliance_katereviews')->__('Deny'),
			'url' => $this->getUrl('*/*/massPendingDeny', array('' => '')),
		));
		$this->getMassactionBlock()->addItem('delete', array(
			'label' => Mage::helper('alliance_katereviews')->__('Delete'),
			'url' => $this->getUrl('*/*/massPendingDelete', array('' => '')),
			'confirm' => Mage::helper('alliance_katereviews')->__('Are you sure? The selected reviews will be permanently deleted. This cannot be undone.')
		));
		return $this;
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'referer' => Mage::helper('core')->urlEncode(Mage::helper('core/url')->getCurrentUrl())));
	}
}