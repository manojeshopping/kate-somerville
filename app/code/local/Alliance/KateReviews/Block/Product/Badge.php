<?php

class Alliance_KateReviews_Block_Product_Badge extends Mage_Core_Block_Template
{
    protected $_collection;

    public $review_count;
    public $star_average;
    public $product_id;
    public $write_link;

    public function __construct()
    {
        $this->_loadCollection();
        $this->_getProductId();
        $this->_getReviewCount();
        $this->_getStarAverage();
        $this->_getWriteLink();
    }

    protected function _loadCollection()
    {
        $product_id = $this->getRequest()->getParam('id');
        $child_product_ids = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product_id);
        $accepted_product_ids = array_keys($child_product_ids[0]);
        $accepted_product_ids[] = $product_id;
        $collection = Mage::getResourceModel('alliance_katereviews/review_collection');
        $collection->addFieldToFilter('product_id', array(
            'in' => $accepted_product_ids,
        ));
        $collection->addFieldToFilter('status', array(
            'eq' => 'Approved',
        ));
        $collection->setOrder('date', 'DESC');

        $customers = array();
        $excluded_review_ids = array();
        foreach ($collection as $review) {
            $customer_id = $review->getCustomerId();
            if (!in_array($customer_id, $customers)) {
                $customers[] = $customer_id;
            } else {
                $excluded_review_ids[] = $review->getId();
            }
        }

        $final_collection = Mage::getResourceModel('alliance_katereviews/review_collection');
        $final_collection->addFieldToFilter('product_id', array(
                    'in' => $accepted_product_ids,
                    ));
        if (!empty($excluded_review_ids)) {
            $final_collection->addFieldToFilter('id', array(
                        'nin' => $excluded_review_ids,
                        ));
        }
        $final_collection->addFieldToFilter('status', array(
                    'eq' => 'Approved',
                    ));
        $final_collection->setOrder('date', 'DESC');

        $this->_collection = $final_collection;
    }

    protected function _getProductId()
    {
        $this->product_id = $this->getRequest()->getParam('id');
    }

    protected function _getReviewCount()
    {
        $this->review_count = $this->_collection->getSize();
    }

    protected function _getStarAverage()
    {
        $number_reviews = 0;
        $total_stars = 0;
        foreach ($this->_collection as $review) {
            $number_reviews++;
            $total_stars += $review->getStarRating();
        }
        if ($number_reviews) {
            $this->star_average = round($total_stars / $number_reviews, 1, PHP_ROUND_HALF_UP);
        }
        else {
            $this->star_average = 0;
        }
    }

    protected function _getWriteLink()
    {
        $link = '/katereviews/write/index/id/';
        $link .= $this->product_id;
        $this->write_link = $link;
    }
}
