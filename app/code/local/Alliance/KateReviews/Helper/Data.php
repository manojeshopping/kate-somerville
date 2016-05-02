<?php

class Alliance_KateReviews_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getConfigEnableCustomerNotification()
    {
        return Mage::getStoreConfig('alliance_katereviews/customer_notification_settings/enable_customer_notification');
    }

    public function getConfigSenderEmailIdentity()
    {
        return Mage::getStoreConfig('alliance_katereviews/customer_notification_settings/sender_email_identity');
    }

    public function getConfigEmailTemplate()
    {
        return Mage::getStoreConfig('alliance_katereviews/customer_notification_settings/email_template');
    }

    public function updateTopContributors()
    {
        $contributors = Mage::getResourceModel('alliance_katereviews/contributor_collection');
        $contributors->setOrder('reviews_count', 'DESC')
            ->setCurPage(1)
            ->setPageSize(100);
        $i = 1;
        foreach ($contributors as $contributor) {
            $topcontributor = Mage::getModel('alliance_katereviews/topcontributor');
            $topcontributor->loadByRank($i);
            if (!$topcontributor->getId()) $topcontributor->setRank($i);
            $topcontributor->setCustomerId($contributor->getCustomerId());
            $topcontributor->save();
            $i++;
        }
    }

	public function getStarAverage($product_id)
	{
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

		$reviews_collection = $final_collection;



		$number_reviews = 0;
		$total_stars = 0;
		foreach ($reviews_collection as $review) {
			$number_reviews++;
			$total_stars += $review->getStarRating();
		}
		if ($number_reviews) {
			return round($total_stars / $number_reviews, 1, PHP_ROUND_HALF_UP);
		}
		else {
			return 0;
		}
	}
}