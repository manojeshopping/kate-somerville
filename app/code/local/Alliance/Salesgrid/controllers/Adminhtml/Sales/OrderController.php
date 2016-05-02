<?php 
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';

class Alliance_Salesgrid_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
	/**
     * Export order grid to CSV format
     */
	public function exportCsvAction()
    {
		$fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_grid');
		$grid->addColumnAfter('order_mode', array(
            'header'    => Mage::helper('customer')->__('Order Mode'),
            'width'     => '200',
			'index' 	=> 'order_mode'
          ), 'shipping_name');
		 
			$groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
		$grid->addColumnAfter('customer_group_id', array(
            'header'    => Mage::helper('customer')->__('Customer Group'),
            'width'     => '200',
			'index'		=> 'customer_group_id',
			'type'    => 'options',
            'options' => $groups,
           ), 'created_at');
		
		$grid->addColumnAfter('customer_email', array(
            'header'    => Mage::helper('customer')->__('Customer Email'),
            'width'     => '200',
			'index'		=> 'customer_email',
           ), 'created_at');
		   
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_grid');
		$grid->addColumnAfter('order_mode', array(
            'header'    => Mage::helper('customer')->__('Order Mode'),
            'width'     => '200',
			'index' 	=> 'order_mode'
          ), 'shipping_name');
		 
			$groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
		$grid->addColumnAfter('customer_group_id', array(
            'header'    => Mage::helper('customer')->__('Customer Group'),
            'width'     => '200',
			'index'		=> 'customer_group_id',
			'type'    => 'options',
            'options' => $groups,
           ), 'created_at');
		
		$grid->addColumnAfter('customer_email', array(
            'header'    => Mage::helper('customer')->__('Customer Email'),
            'width'     => '200',
			'index'		=> 'customer_email',
           ), 'created_at');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

}