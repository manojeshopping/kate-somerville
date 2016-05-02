<?php

class Alliance_ReviewsFeed_GetreviewsController extends Mage_Core_Controller_Front_Action
{
	public function getdataAction()
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', '10143_dermalquench');
		$product_id = $product->getId();
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

		$i = 0;
		foreach ($final_collection as $item) {
			$nickname = Mage::getModel('customer/customer')->load($item->getCustomerId())->getFirstname();
			$details = $item->getReviewText();
			$rate = strval(intval($item->getStarRating()) * 20);
			$result[$i] = array('nickname' => $nickname, 'details' => $details, 'rate' => $rate);
			$i++;
		}
		echo json_encode($result);
	}
}

?>
