<?php

class Alliance_KateReviews_WriteController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $session = Mage::getSingleton('customer/session');
        $session->setAfterAuthUrl(Mage::helper('core/url')->getCurrentUrl());
        $session->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Product KateReviews'));
        $this->renderLayout();
    }

    public function previewAction()
    {
        $postData = $this->getRequest()->getPost();
        if ($postData) {
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('KateReview Details'));
            $this->renderLayout();
        }
    }
}