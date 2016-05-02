<?php

class Alliance_KateReviews_Block_Success extends Mage_Core_Block_Template
{
    protected $product;
    protected $product_id;
    public $product_url;


    public function __construct()
    {
        $this->_loadProductId();
        $this->_loadProduct();
        $this->_loadProductUrl();
    }

    protected function _loadProductId()
    {
        $this->product_id = $this->getRequest()->getParam('id');
    }

    protected function _loadProduct()
    {
        $this->product = Mage::getModel('catalog/product')->load($this->product_id);
    }

    protected function _loadProductUrl()
    {
        $this->product_url = $this->product->getProductUrl();
    }
}