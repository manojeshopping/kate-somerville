<?php

class Alliance_KateReviews_Model_Topcontributor extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('alliance_katereviews/topcontributor');
    }

    public function loadByRank($rank)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('rank', $rank);
        if ($this->load($collection->getFirstItem()->getId())) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function loadByCustomerId($customer_id)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('customer_id', $customer_id);
        if ($this->load($collection->getFirstItem()->getId())) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}