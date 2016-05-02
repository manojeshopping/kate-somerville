<?php
      
class Alliance_Giftreports_Block_Adminhtml_Redemption_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct() {
   	
	parent::__construct();
        $this->setId('giftreportsredemptionGrid');
        $this->setDefaultSort('giftcardaccount_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('giftreportsredemption_filter');
    }

    protected function _prepareCollection() {
        
        //$collection = Mage::getResourceModel('enterprise_giftcardaccount/giftcardaccount_collection');
		$collection = Mage::getResourceModel('enterprise_giftcardaccount/history_collection');
        $collection->getSelect()->join( array('history'=>'enterprise_giftcardaccount'), 'main_table.giftcardaccount_id = history.giftcardaccount_id', 
		//array("REPLACE(REPLACE (main_table.additional_info,'Customer #',''),'.','') AS customer_number",'history.*'));
				array("SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(concat_ws('|',
	REPLACE(REPLACE (main_table.additional_info,'Customer #',''),'.',''),
	(SELECT customer_id 
		FROM `sales_flat_order` 
		where increment_id=REPLACE(REPLACE(main_table.additional_info,'Order #',''),'.','')
	)
),'Order #',''), '|', 2), '|', -1) as customer_number",'history.*'));

		// $collection->addFieldtoFilter('action','3');
		$collection->addFieldToFilter('action', array('in' => array('1','3')));
		// $collection->getSelect()->group('main_table.giftcardaccount_id');
		// $collection->getSelect()->order('main_table.giftcardaccount_id');
		
		
		
//		echo $collection->getSelect()->__toString();
//		$collection->removeAllFieldsFromSelect();
	   $this->setCollection($collection);
       	parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns() {
		$collection = Mage::getResourceModel('enterprise_giftcardaccount/history_collection')->getFirstItem();
		
		$this->addColumn('code', array(
            'header' => Mage::helper('giftreports')->__('Gift Card #'),
            'index' => 'code'
        ));
		
		$this->addColumn('additional_info', array(
			'header' 		=> Mage::helper('giftreports')->__('Order #'),
			'align' 		=> 'right',
			'width' 		=> '150px',
			'index' 		=> 'additional_info',
			'renderer' 		=> 'Alliance_Giftreports_Block_Adminhtml_Redemption_Renderer_Orderid'
		));
		
		
		$this->addColumn('action', array(
            'header' => Mage::helper('giftreports')->__('Redemption Status'),
            'index' => 'action',
			'type'      => 'options',
			'options'   => array(0=>'Created', 1=>'Used', 2=> 'Sent', 3 =>'Redeemed', 4=> 'Expired', 5=> 'Updated' ),
        ));

		$this->addColumn('balance_delta', array(
            'header' => Mage::helper('giftreports')->__('Redemption Amount'),
            'index' => 'balance_delta',
			'type' => 'currency',
			'currency_code'     => $this->_getStore()->getCurrentCurrencyCode(),
			'rate'              => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),

        ));

         $this->addColumn('updated_at',
            array(
                'header'  => Mage::helper('giftreports')->__('Redemption Date'),
                'index'   => 'updated_at',
                'default' => '--',
        ));
		
         $this->addColumn('customer_number',
            array(
                'header'  => Mage::helper('giftreports')->__('Used by Customer #'),
                'index'   => 'customer_number',
                'default' => '--',
        ));
    
        //$this->addExportType('*/*/exportCsv', Mage::helper('events')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('events')->__('XML'));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('giftreports')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('giftreports')->__('XML'));

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row) {
        return false;
    }
	/**
     * Define row click callback
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
	/**
     * Return Current work store
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::app()->getStore();
    }
	
}