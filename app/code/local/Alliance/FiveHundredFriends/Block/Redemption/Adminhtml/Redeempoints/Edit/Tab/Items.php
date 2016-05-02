<?php


class Alliance_FiveHundredFriends_Block_Redemption_Adminhtml_Redeempoints_Edit_Tab_Items extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('alliance_fivehundredfriends_redeempoints_items_grid');
		$this->setDefaultSort('entity_id', 'desc');
		$this->setUseAjax(true);
	}
	
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('alliance_fivehundredfriends/redemption_item_collection')
            ->addFieldToFilter('redeem_id', Mage::registry('alliance_fivehundredfriends_redemption')->getId())
		;
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		$store = Mage::app()->getStore($storeId); 
		
		$this->addColumn('entity_id', array(
			'header'    => Mage::helper('customer')->__('ID'),
			'width'     => '50',
			'index'     => 'entity_id',
			'align' 	=> 'right',
		));
		
		$this->addColumn('reward_id', array(
			'header'    => Mage::helper('customer')->__('Reward ID'),
			'width'     => '100',
			'index'     => 'reward_id',
			'align' 	=> 'right',
		));
		
		$this->addColumn('redeem_points', array(
			'header'    => Mage::helper('customer')->__('Points Redeemed'),
			'width'     => '100',
			'index'     => 'redeem_points',
			'align' 	=> 'right',
		));
		
		$this->addColumn('reward_type', array(
			'header'    => Mage::helper('customer')->__('Reward Type'),
			'index'     => 'reward_type',
			'width'     => '100',
            'type'      => 'options',
			'options' => array(
				'disabled' => 'Disabled',
				'discount' => 'Discount',
				'offers' => 'Complementary Offer',
			),
		));
		
		$this->addColumn('discount_amount', array(
			'header'    => Mage::helper('customer')->__('Discount Amount'),
			'width'     => '100',
			'index'     => 'discount_amount',
            'type'      => 'currency',
			'currency_code'     => $store->getBaseCurrency()->getCode(),
		));
		
		$this->addColumn('status', array(
			'header'    => Mage::helper('customer')->__('Status'),
			'width'     => '100',
			'index'		=> 'status',
			'type' 		=> 'options',
			'options' => array(
				'pending' => 'Pending',
				'redeemed' => 'Redeemed',
				'rejected' => 'Rejected',
			),
		));
		
		return parent::_prepareColumns();
	}
}