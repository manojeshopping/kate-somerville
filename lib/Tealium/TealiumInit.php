<?php
// TealiumInit.php var definition file
// Replace $STRING or $ARRAY with your server side variable reference unique to that key 
$store = $data["store"];
$page  = $data["page"];
$STRING = "";
$ARRAY = array();

//define variables from magento *************************************************************************
$customer_id       = "n/a";
$customer_email    = "n/a";
$customer_type     = "n/a";
$ids               = array();
$skus              = array();
$names             = array();
$qtys              = array();
$prices            = array();
$discounts         = array();
$discount_quantity = array();
$checkout_ids      = array();
$checkout_skus     = array();
$checkout_names    = array();
$checkout_qtys     = array();
$checkout_prices   = array();
$section           = "n/a";
$category          = "n/a";
$subcategory       = "n/a";
$_category         = null;

if ($page->getCurrentCategory()) {
    $_category   = $page->getCurrentCategory();
    $parent      = false;
    $grandparent = false;
    
    // check for parent and grandparent
    if ($_category->getParentId()) {
        $parent = Mage::getModel('catalog/category')->load($_category->getParentId());
        
        if ($parent->getParentId()) {
            $grandparent = Mage::getModel('catalog/category')->load($parent->getParentId());
        }
    }
    
    // Set the section and subcategory with parent and grandparent
    if ($grandparent) {
        $section     = $grandparent->getName();
        $category    = $parent->getName();
        $subcategory = $_category->getName();
    } elseif ($parent) {
        $section  = $parent->getName();
        $category = $_category->getName();
    } else {
        $category = $_category->getName();
    }
}

if (Mage::helper('checkout')) {
    $quote = Mage::helper('checkout')->getQuote();
    foreach ($quote->getAllVisibleItems() as $item) {
        $checkout_ids[]    = $item->getProductId();
        $checkout_skus[]   = $item->getSku();
        $checkout_names[]  = $item->getName();
        $checkout_qtys[]   = number_format($item->getQty(), 0, ".", "");
        $checkout_prices[] = number_format($item->getPrice(), 2, ".", "");
    }
}

if (Mage::getSingleton('customer/session')->isLoggedIn()) {
    $customer       = Mage::getSingleton('customer/session')->getCustomer();
    $customer_id    = $customer->getEntityId();
    $customer_email = $customer->getEmail();
    $groupId        = $customer->getGroupId();
    $customer_type  = Mage::getModel('customer/group')->load($groupId)->getCode();
}

if (Mage::getModel('sales/order')) {
    $order = Mage::getModel('sales/order')->loadByIncrementId($page->getOrderId());
    foreach ($order->getAllVisibleItems() as $item) {
        
        $ids[]           = $item->getProductId();
        $skus[]          = $item->getSku();
        $names[]         = $item->getName();
        $qtys[]          = number_format($item->getQtyOrdered(), 0, ".", "");
        $prices[]        = number_format($item->getPrice(), 2, ".", "");
        $discount        = number_format($item->getDiscountAmount(), 2, ".", "");
        $discounts[]     = $discount;
        $applied_rules   = explode(",", $item->getAppliedRuleIds());
        $discount_object = array();
        foreach ($applied_rules as $rule) {
            $quantity          = number_format(Mage::getModel('salesrule/rule')->load($rule)->getDiscountQty(), 0, ".", "");
            $amount            = number_format(Mage::getModel('salesrule/rule')->load($rule)->getDiscountAmount(), 2, ".", "");
            $type              = Mage::getModel('salesrule/rule')->load($rule)->getSimpleAction();
            $discount_object[] = array("rule"		=>$rule,
										"quantity"	=>$quantity,
										"amount"	=>$amount,
										"type"		=>$type);
        }
        $discount_quantity[] = array("product_id" => $item->getProductId(),
									 "total_discount"	=> $discount,
									 "discounts"		=> $discount_object);
        
    }
}
//**************************************************************************************************

$udoElements = array(
    'Home' => array(
        'site_region' => Mage::app()->getLocale()->getLocaleCode() ?: "", //"uk"
        'site_currency' => $store->getCurrentCurrencyCode() ?: "", //"GBP"
        'page_name' => $page->getLayout()->getBlock('head')->getTitle() ?: "", //"Home" 
        'page_type' => $page->getTealiumType() ?: "" //"home"
    ),
    'Search' => array(
        'site_region' => Mage::app()->getLocale()->getLocaleCode() ?: "", //"uk"
        'site_currency' => $store->getCurrentCurrencyCode() ?: "", //"GBP" 
        'page_name' => "search results", //"results" 
        'page_type' => "search", //"search"
        'search_results' => $page->getResultCount() ?: "", //"234"
        'search_keyword' => $page->helper('catalogsearch')->getEscapedQueryText() ?: "" //"shorts"
    ),
    'Category' => array(
        'site_region' => Mage::app()->getLocale()->getLocaleCode() ?: "", //"uk",
        'site_currency' => $store->getCurrentCurrencyCode() ?: "", //"GBP", 
        'page_name' => $_category ? ($_category->getName() ?: "") : "", //"shorts", 
        'page_type' => "category", //"category",
        'page_section_name' => $section ?: "", //"Men's",
        'page_category_name' => $category ?: "", //"Clothing",
        'page_subcategory_name' => $subcategory ?: "" //"Shorts"
    ),
    'ProductPage' => array(
        'site_region' => Mage::app()->getLocale()->getLocaleCode() ?: "", //"uk",
        'site_currency' => $store->getCurrentCurrencyCode() ?: "", //"GBP", 
        'page_name' => $page->getProduct() ? ($page->getProduct()->getName() ?: "") : "", //"Dr. Denim Chase Check Cargo Short", 
        'page_type' => "product", //"product",
        'page_section_name' => $STRING ?: "", //"Men's",
        'page_category_name' => $STRING ?: "", //"Clothing",
        'page_subcategory_name' => $STRING ?: "", //"Shorts",
        // THE FOLLOWING NEEDS TO BE MATCHED ARRAYS (SAME NUMBER OF ELEMENTS)
        'product_id' => $page->getProduct() ? (array(
            $page->getProduct()->getId()
        ) ?: array(
            ""
        )) : array(
            ""
        ), //array("5225415241111"),
        'product_sku' => $page->getProduct() ? (array(
            $page->getProduct()->getSku()
        ) ?: array(
            ""
        )) : array(
            ""
        ), //array("42526"),
        'product_name' => $page->getProduct() ? (array(
            $page->getProduct()->getName()
        ) ?: array(
            ""
        )) : array(
            ""
        ), //array("Dr. Denim Chase Check Cargo Short"),
        'product_brand' => $page->getProduct() ? (array(
            $page->getProduct()->getBrand()
        ) ?: array(
            ""
        )) : array(
            ""
        ), //array("Dr. Denim"),
        'product_category' => array(
            Mage::registry('current_category') ? Mage::registry('current_category')->getName() : 'no_category'
        ) ?: array(
            ""
        ), //array("Shorts"),
        'product_unit_price' => $page->getProduct() ? (array(
            number_format($page->getProduct()->getSpecialPrice(), 2)
        ) ?: array(
            ""
        )) : array(
            ""
        ), //array("11.99"),
        'product_list_price' => $page->getProduct() ? (array(
            number_format($page->getProduct()->getPrice(), 2)
        ) ?: array(
            ""
        )) : array(
            ""
        ) //array("59.00")
    ),
    'Cart' => array(
        'site_region' => Mage::app()->getLocale()->getLocaleCode() ?: "", //"uk",
        'site_currency' => $store->getCurrentCurrencyCode() ?: "", //"GBP", 
        'page_name' => $page->getLayout()->getBlock('head')->getTitle() ?: "", //"cart", 
        'page_type' => "checkout", //"checkout",
        // THE FOLLOWING NEEDS TO BE MATCHED ARRAYS (SAME NUMBER OF ELEMENTS)
        'product_id' => $checkout_ids ?: array(), //array("5225415241111","5421423520051"),
        'product_sku' => $checkout_skus ?: array(), //array("42526","24672"),
        'product_name' => $checkout_names ?: array(), //array("Dr. Denim Chase Check Cargo Short","Renewal Denim Shirt"),
        'product_brand' => $ARRAY ?: array(), //array("Dr. Denim",""),
        'product_category' => $ARRAY ?: array(), //array("Shorts","Shirts"),
        'product_quantity' => $checkout_qtys ?: array(), //array("1","1"),
        'product_unit_price' => $ARRAY ?: array(), //array("1//array("11.99","37.00"),
        'product_list_price' => $checkout_prices ?: array() //array("59.00","")
    ),
    'Confirmation' => array(
        'site_region' => Mage::app()->getLocale()->getLocaleCode() ?: "", //"uk",
        'site_currency' => $store->getCurrentCurrencyCode() ?: "", //"GBP", 
        'page_name' => "cart success", //"confirmation",    
        'page_type' => "cart", //"checkout",
        'order_id' => $order->getIncrementId() ?: "", //"12345678",
        'order_discount' => number_format($order->getDiscountAmount(), 2, ".", "") ?: "", //"0.00",
        'order_subtotal' => number_format($order->getSubtotal(), 2, ".", "") ?: "", //"70.99",
        'order_shipping' => number_format($order->getShippingAmount(), 2, ".", "") ?: "", //"10.00",
        'order_tax' => number_format($order->getTaxAmount(), 2, ".", "") ?: "", //"5.00",
        'order_payment_type' => $order->getPayment() ? $order->getPayment()->getMethodInstance()->getTitle() : 'unknown', //"visa",
        'order_total' => number_format($order->getGrandTotal(), 2, ".", "") ?: "", //"85.99",
        'order_currency' => $order->getOrderCurrencyCode() ?: "", //"gbp",
        'customer_id' => $customer_id ?: "", //"12345678",
        'customer_email' => $order->getCustomerEmail() ?: "", //"customer@email.com"
        // THE FOLLOWING NEEDS TO BE MATCHED ARRAYS (SAME NUMBER OF ELEMENTS)
        'product_id' => $ids ?: array(), //array("5225415241111","5421423520051"),
        'product_sku' => $skus ?: array(), //array("42526","24672"),
        'product_name' => $names ?: array(), //array("Dr. Denim Chase Check Cargo Short","Renewal Denim Shirt"),
        'product_brand' => $ARRAY ?: array(), //array("Dr. Denim",""),
        'product_category' => $ARRAY ?: array(), //array("Shorts","Shirts"),
        'product_unit_price' => $ARRAY ?: array(), //array("11.99","37.00"),
        'product_list_price' => $prices ?: array(), //array("59.00",""),
        'product_quantity' => $qtys ?: array(), //array("1","1"),
        'product_discount' => $discounts ?: array(), //array("0.00","0.00"),
        'product_discounts' => $discount_quantity ?: array()
    ),
    'Customer' => array(
        'site_region' => Mage::app()->getLocale()->getLocaleCode() ?: "",
        'site_currency' => $store->getCurrentCurrencyCode() ?: "",
        'page_name' => $page->getLayout()->getBlock('head')->getTitle() ?: "",
        'page_type' => $page->getTealiumType() ?: "",
        'customer_id' => $customer_id ?: "",
        'customer_email' => $customer_email ?: "",
        'customer_type' => $customer_type ?: ""
    )
);



// *** Added by Alliance *** //
// Add two variables for each page: order_discount and country_code.
foreach($udoElements as $pageKey => $pageArray) {
	// Order discount.
	$order_discount = 0;
	if(isset($quote)) {
		// Add discount of coupon code.
		$totals = $quote->getTotals();
		if(isset($totals['discount']) && $totals['discount']->getValue()) {
			$order_discount += abs($totals['discount']->getValue());
		}
		
		// Add discount of products.
		foreach ($quote->getAllVisibleItems() as $_item) {
			if($_item->getPrice() < $_item->getProduct()->getPrice()) {
				$order_discount += ($_item->getProduct()->getPrice() - $_item->getPrice());
			}
		}
	}
	
	// Country code.
	$country_code = "US";
	if($store->getCode() == "ca_store") $country_code = "CA";
	elseif($store->getCode() == "uk_store") $country_code = "UK";
	
	$udoElements[$pageKey] = array_merge($pageArray, array(
		'order_discount' => number_format($order_discount, 2),
		'country_code' => $country_code,
	));
}
// *** Added by Alliance *** //
?> 

