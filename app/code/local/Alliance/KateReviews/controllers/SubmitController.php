<?php

class Alliance_KateReviews_SubmitController extends Mage_Core_Controller_Front_Action
{
    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();
        if ($postData) {
            $model = Mage::getModel('alliance_katereviews/review');
            $customer = Mage::getModel('customer/customer')->load($postData['review-customer-id']);
            $product = Mage::getModel('catalog/product')->load($postData['review-product-id']);
            $customer_email = $customer->getEmail();
			$customer_name = $customer->getName();
            $product_sku = $product->getSku();
            $product_name = $product->getName();
            $model->setData(array(
                'customer_id' => $postData['review-customer-id'],
                'product_id'  => $postData['review-product-id'],
                'store_id'    => $store = Mage::app()->getStore()->getId(),
                'customer_email' => $customer_email,
                'product_sku' => $product_sku,
                'product_name' => $product_name,
                'star_rating' => $postData['review-stars'],
                'recommended' => $postData['review-recommended'],
                'review_headline' => $postData['review-headline'],
                'review_text' => $postData['review-comments'],
                'purchased_at' => $postData['review-purchase-location'],
                'skin_concern' => $postData['review-skin-concern'],
                'age_range' => $postData['review-age-range'],
                'owned_for' => $postData['review-long-owned'],
                'often_used' => $postData['review-how-often'],
                'member_status' => $postData['review-rewards-member'],
                'location' => $postData['review-city-state'],
                'notify'   => $postData['review-notify'],
                //'recommended_products' => $postData[''],
				'customer_name' => $customer_name,
            ));
            $model->save();
            $this->_redirect('*/*/success/id/'.$postData['review-product-id']);
        }
        else {
            $this->_redirect('/');
        }
    }

    public function successAction()
    {
        $this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Katesomerville Reviews'));
        $this->renderLayout();
    }

    public function helpyesAction()
    {
        $customer_id = $this->getRequest()->getParam('customerid');
        $review_id = $this->getRequest()->getParam('reviewid');
        $logged_in = Mage::getModel('customer/session')->isLoggedIn();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $true_customer_id = $customer->getId();
        $authentic = $true_customer_id == $customer_id ? TRUE : FALSE;

        $collection = Mage::getResourceModel('alliance_katereviews/helpful_collection');
        $collection->addFieldToFilter('review_id', array(
            'eq' => $review_id,
        ));
        $collection->addFieldToFilter('customer_id', array(
            'eq' => $customer_id,
        ));
        $already_voted = $collection->getSize() > 0 ? TRUE : FALSE;
        if (!$already_voted && $logged_in && $authentic) {
            $model = Mage::getModel('alliance_katereviews/helpful');
            $model->setCustomerId($customer_id);
            $model->setReviewId($review_id);
            $model->setHelpful(1);
            if ($model->save()) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function helpnoAction()
    {
        $customer_id = $this->getRequest()->getParam('customerid');
        $review_id = $this->getRequest()->getParam('reviewid');
        $logged_in = Mage::getModel('customer/session')->isLoggedIn();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $true_customer_id = $customer->getId();
        $authentic = $true_customer_id == $customer_id ? TRUE : FALSE;

        $collection = Mage::getResourceModel('alliance_katereviews/helpful_collection');
        $collection->addFieldToFilter('review_id', array(
            'eq' => $review_id,
        ));
        $collection->addFieldToFilter('customer_id', array(
            'eq' => $customer_id,
        ));
        $already_voted = $collection->getSize() > 0 ? TRUE : FALSE;
        if (!$already_voted && $logged_in && $authentic) {
            $model = Mage::getModel('alliance_katereviews/helpful');
            $model->setCustomerId($customer_id);
            $model->setReviewId($review_id);
            $model->setHelpful(0);
            if ($model->save()) {
                return TRUE;
            }
        }
        return FALSE;
    }
}