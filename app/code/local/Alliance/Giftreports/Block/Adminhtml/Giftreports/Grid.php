<?php

class Alliance_Giftreports_Block_Adminhtml_Giftreports_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct() {
		parent::__construct();
        $this->setId('giftreportGrid');
        $this->setDefaultSort('giftcardaccount_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('giftreport_filter');
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('enterprise_giftcardaccount/giftcardaccount_collection');
        $collection->getSelect()->join( array('history'=>'enterprise_giftcardaccount_history'), 'main_table.giftcardaccount_id = history.giftcardaccount_id', array("REPLACE(REPLACE (history.additional_info,'Order #',''),'.','') AS order_number",'history.*'));
        $collection->addFieldtoFilter('action','0');
		$this->setCollection($collection);
		
       	parent::_prepareCollection();
		
        return $this;
    }

    protected function _prepareColumns() {
    
	$this->addColumn('code', array(
            'header' => Mage::helper('giftreports')->__('Gift Card #'),
            'index' => 'code'
        ));

	$this->addColumn('balance_amount', array(
            'header' => Mage::helper('giftreports')->__('Amount of Gift Card'),
            'index' => 'balance_amount',
			'type' => 'currency',
			'currency_code'     => $this->_getStore()->getCurrentCurrencyCode(),
			'rate'              => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),

        ));

		
        $this->addColumn('date_created',
            array(
                'header'=> Mage::helper('giftreports')->__('Date Created'),
                'width' => 120,
                'type'  => 'date',
                'index' => 'date_created',
        ));

        $this->addColumn('date_expires',
            array(
                'header'  => Mage::helper('giftreports')->__('Expiration Date'),
                'width'   => 120,
                'type'    => 'date',
                'index'   => 'date_expires',
                'default' => '--',
        ));

	  $this->addColumn('order_number',
            array(
                'header'  => Mage::helper('giftreports')->__('Order #'),
                'index'   => 'order_number',
                'default' => '--',
                'filter_condition_callback' => array($this, '_filterOrderNumber'),
        ));
		
        // $this->addExportType('*/*/exportCsv', Mage::helper('events')->__('CSV'));
        // $this->addExportType('*/*/exportXml', Mage::helper('events')->__('XML'));
		
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

	
	protected function _filterOrderNumber($collection, $column)
	{
		if(! $value = $column->getFilter()->getValue()) return $this;
		
		$this->getCollection()->getSelect()->where(
			"REPLACE(REPLACE(history.additional_info,'Order #',''),'.','') LIKE '%".$column->getFilter()->getValue()."%'"
		);
		
		return $this;
	}
}