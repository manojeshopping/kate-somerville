<?php

class Alliance_KateReviews_Block_Preview extends Mage_Core_Block_Template
{
    public $product_id;
    public $product;
    public $customer_id;
    public $postData;

    public function __construct()
    {
        $this->postData = $this->getRequest()->getPost();
        if ($this->postData) {
            $this->product_id = $this->postData['review-product-id'];
            $this->product = Mage::getModel('catalog/product')->load($this->product_id);
            $this->customer_id = $this->postData['review-customer-id'];
        }
    }

    public function getProductName()
    {
        return $this->product->getName();
    }

    public function getProductImageUrl()
    {
        return $this->product->getImageUrl();
    }

    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }

    public function isLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function getCustomerId()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getId();
    }
}