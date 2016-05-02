<?php

require_once(dirname(__FILE__) . '/../abstract.php');

/**
 * Class Alliance_Shell_Queue_Handler
 */
class Alliance_Shell_Google_Reviews extends Mage_Shell_Abstract
{
	public function __construct()
	{
		parent::__construct();
		Mage::app()->setCurrentStore('default');
	}

	/**
	 * Run the script
	 */
	public function run()
	{
		$reviews = Mage::getModel('alliance_katereviews/review')->getCollection();

		$feed_node = <<<EOD
<feed xmlns:vc="http://www.w3.org/2007/XMLSchema-versioning"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:noNamespaceSchemaLocation="http://www.google.com/shopping/reviews/schema/product/2.1/product_reviews.xsd"></feed>
EOD;

		$feed = new SimpleXMLElement($feed_node);

		$publisher_node = $feed->addChild('publisher');
		$publisher_node->addChild('name', 'Kate Somerville');

		$reviews_node = $feed->addChild('reviews');

		foreach ($reviews as $review) {
			if ($review->getStatus() !== 'Approved' || !$review->getCustomerId()
				|| !$review->getReviewHeadline() || !$review->fetchCustomerName()) continue;

			$product = Mage::getModel('catalog/product')->load($review->getProductId());

			if (!$product->getId()) continue;
			if ($product->getStatus() != 1) continue;
			if (!in_array($product->getVisibility(), array(2, 3, 4))) continue;

			$review_node = $reviews_node->addChild('review');

			$review_node->review_id = $review->getId();

			if ($review->getCustomerId()) {
				$reviewer_node = $review_node->addChild('reviewer');

				$reviewer_node->name        = $review->fetchCustomerName();
				$reviewer_node->reviewer_id = $review->getCustomerId();
			}

			$review_node->review_timestamp = str_replace(' ', 'T', $review->getDate());

			$review_node->title = $review->getReviewHeadline();

			$review_node->content = $review->getReviewText();

			$url             = 'http://www.katesomerville.com/katereviews/view/index/id/' . $review->getProductId() . '/?limit=all';
			$review_url_node = $review_node->addChild('review_url', $url);
			$review_url_node->addAttribute('type', 'group');

			$ratings_node = $review_node->addChild('ratings');
			$overall_node = $ratings_node->addChild('overall', $review->getStarRating());
			$overall_node->addAttribute('min', '1');
			$overall_node->addAttribute('max', '5');

			$product = Mage::getModel('catalog/product')->load($review->getProductId());

			$products_node = $review_node->addChild('products');
			$product_node  = $products_node->addChild('product');

			$product_ids_node   = $product_node->addChild('product_ids');
			$skus_node          = $product_ids_node->addChild('skus');
			$skus_node->sku     = $product->getSku();
			$brands_node        = $product_ids_node->addChild('brands');
			$brands_node->brand = 'Kate Somerville';

			$product_node->product_name = $product->getName();
			$product_node->product_url  = $product->getProductUrl();

			$review_node->is_spam           = 'false';
			$review_node->collection_method = 'unsolicited';
		}

		$feed->asXML(dirname(__FILE__) . '/../../feeds/google_product_reviews.xml');
	}
}

$shell = new Alliance_Shell_Google_Reviews;
$shell->run();
