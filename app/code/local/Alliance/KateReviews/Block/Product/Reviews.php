<?php

class Alliance_KateReviews_Block_Product_Reviews extends Mage_Core_Block_Template
{
    protected $_collection;
    protected $_product;
    protected $_customer;

    public $customer_id;
    public $star_average;
    public $product_id;
    public $product_name;
    public $write_link;
    public $total_count;

    public function __construct()
    {
        $this->_loadCollection();
        $this->_loadTotalCount();
        $this->_loadProductId();
        $this->_loadCustomer();
        $this->_loadProduct();
        $this->_loadProductName();
        $this->_loadStarAverage();
        $this->_loadWriteLink();
    }

    public function getTopContributorText($customer_id)
    {
        $topcontributor = Mage::getModel('alliance_katereviews/topcontributor');
        $topcontributor->loadByCustomerId($customer_id);
        if ($topcontributor->getId()) {
            $rank = $topcontributor->getRank();
            $text = 'Top 100 Contributor';
            if ($rank < 76) $text = 'Top 75 Contributor';
            if ($rank < 51) $text = 'Top 50 Contributor';
            if ($rank < 26) $text = 'Top 25 Contributor';
            return $text;
        }
        return FALSE;
    }

    public function customerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
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
        $final_collection->getSelect()->limit(5);

        $this->_collection = $final_collection;
    }

    protected function _loadTotalCount()
    {
        $this->total_count = $this->_collection->getSize();
    }

    protected function _loadProduct()
    {
        $product = Mage::getModel('catalog/product');
        $product->load($this->product_id);
        $this->_product = $product;
    }

    protected function _loadCustomer()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->_customer = $customer;
            $this->customer_id = $customer->getId();
        }
    }

    protected function _loadStarAverage()
    {
        $number_reviews = 0;
        $total_stars = 0;
        $customers = array();
        foreach ($this->_collection as $review) {
            if (!in_array($review->getCustomerId(), $customers)) {
                $customers[] = $review->getCustomerId();
                $number_reviews++;
                $total_stars += $review->getStarRating();
            }
        }
        if ($number_reviews) {
            $this->star_average = round($total_stars / $number_reviews, 1, PHP_ROUND_HALF_UP);
        }
        else {
            $this->star_average = 0;
        }
    }

    protected function _loadWriteLink()
    {
        $link = '/katereviews/write/index/id/';
        $link .= $this->product_id;
        $this->write_link = $link;
    }

    protected function _loadProductId()
    {
        $this->product_id = $this->getRequest()->getParam('id');
    }

    protected function _loadProductName()
    {
        $this->product_name = $this->_product->getName();
    }

    public function getHelpfuls($review_id)
    {
        $collection = Mage::getResourceModel('alliance_katereviews/helpful_collection');
        $collection->addFieldToFilter('review_id', array(
            'eq' => $review_id,
        ));

        $collection2 = Mage::getResourceModel('alliance_katereviews/helpful_collection');
        $collection2->addFieldToFilter('review_id', array(
            'eq' => $review_id,
        ));
        $collection2->addFieldToFilter('helpful', array(
            'eq' => 1,
        ));

        $collection3 = Mage::getResourceModel('alliance_katereviews/helpful_collection');
        $collection3->addFieldToFilter('customer_id', array(
            'eq' => $this->customer_id,
        ));
        $collection3->addFieldToFilter('review_id', array(
            'eq' => $review_id,
        ));
        if ($collection3->count() < 1) {
            $current_helpful = 'Null';
        }
        else {
            foreach ($collection3 as $customer_helpful) {
                if ($customer_helpful->getHelpful() == 1) {
                    $current_helpful = 'Yes';
                } else {
                    $current_helpful = 'No';
                }
            }
        }
        $data = array(
            'helpful_yes' => $collection2->getSize(),
            'helpful_total' => $collection->getSize(),
            'customer_helpful' => $current_helpful,
        );

        return $data;
    }
}
