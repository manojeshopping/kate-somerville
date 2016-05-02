<?php

class Alliance_KateReviews_Block_Form extends Mage_Core_Block_Template
{
    public $product;
    public $postData;
    public $postExists;

    public function __construct()
    {
        $this->product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
        $this->postData = $this->getRequest()->getPost();
        if ($this->postData) {
            $this->postExists = TRUE;
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