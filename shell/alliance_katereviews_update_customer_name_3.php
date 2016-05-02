<?php

echo "\n** Updating reviews...";

include_once '../app/Mage.php';
umask(0);
Mage::app();

$review_collection = Mage::getModel('alliance_katereviews/review')->getCollection();
$review_collection->addFieldToFilter('id', array(
	'from' => 3101,
	'to' => 4650,
));

foreach ($review_collection as $review) {
	$customer_name = Mage::getModel('customer/customer')->load($review->getCustomerId())->getName();
	$review->setData('customer_name', $customer_name);
	if ($review->save()) {
		echo "\n... Successfully updated customer_name column on Review #" . $review->getId();
	}
}

echo "\n** Reviews updated successfully.\n\n";