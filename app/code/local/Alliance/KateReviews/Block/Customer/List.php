<?php

class Alliance_KateReviews_Block_Customer_List extends Mage_Core_Block_Template
{
    protected $_collection;
    protected $_customer;

    public $customer_id;
    public $review_count;
    public $star_average;
    public $product_name;
    public $write_link;

    public function __construct()
    {
        parent::__construct();
        $this->_loadCollection();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function customerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    protected function _loadCollection()
    {
        $customer_id = Mage::getSingleton('customer/session')->getId();
        $collection = Mage::getResourceModel('alliance_katereviews/review_collection');
        $collection->addFieldToFilter('customer_id', array(
            'eq' => $customer_id,
        ));
        $collection->setOrder('date', 'DESC');

        $this->setCollection($collection);
        $this->_collection = $collection;
    }

    public function getProductName($product_id)
    {
        $model = Mage::getModel('catalog/product')->load($product_id);
        return $model->getName();
    }

    public function getProductUrl($product_id)
    {
        $model = Mage::getModel('catalog/product')->load($product_id);
        return $model->getProductUrl();
    }
}