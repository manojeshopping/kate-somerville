<?php

class Alliance_Ajaxcart_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $moduleConfig;
	
	
	public function initProduct($productId)
	{
		if ($productId) {
			$product = Mage::getModel('catalog/product')
				->setStoreId(Mage::app()->getStore()->getId())
				->load($productId)
			;
			
			if ($product->getId()) {
				return $product;
			}
		}
		
		return false;
	}
	
	public function getConfigureUrl($productId)
	{
		$item = $this->getItemByProductId($productId);
		if(! $item) {
			return false;
		}
		
		return Mage::getUrl(
			'checkout/cart/configure',
			array('id' => $item->getId())
		);
	}
	
	public function getItemId($productId)
	{
		$item = $this->getItemByProductId($productId);
		if(! $item) {
			return false;
		}
		
		return $item->getId();
	}
	
	public function printError($msg, $data = null)
	{
		$jsonData = array('success' => 0, 'msg' => $msg, 'data' => $data);
		$this->printAjax($jsonData);
	}
	public function printSuccess($msg, $data = null)
	{
		$jsonData = array('success' => 1, 'msg' => $msg, 'data' => $data);
		$this->printAjax($jsonData);
	}
	public function printAjax($jsonData)
	{
		$response = Mage::app()->getResponse();
		$response->setHeader('Content-type', 'application/json');
		$response->setBody(json_encode($jsonData));
	}
	
	
	public function getItemByProductId($productId)
	{
		$product = $this->initProduct($productId);
		if(! $product) {
			return false;
		}
		
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$item = $quote->getItemByProduct($product);
		
		return $item;
	}
	
	public function checkMaxSaleQty($product, $qty)
	{
		// Get max quantity.
		$maxSaleQty = $this->getMaxSaleQty($product);
		
		// Get current quantity.
		$quote = $this->_getSession()->getQuote();
		$item = $quote->getItemByProduct($product);
		if($item) $currentQty = $item->getQty();
		else $currentQty = 0;
		
		$newQty = $currentQty + $qty;
		
		return ($maxSaleQty >= $newQty);
	}
	
	public function getMaxSaleQty($product)
	{
		$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
		$maxSaleQty = $stock->getMaxSaleQty();
		
		return $maxSaleQty;
	}
	
	public function filterMaximumQtyAllowed($itemId, $qty)
	{
		// Check Maximum Qty Allowed in Shopping Cart.
		$item = Mage::getModel("sales/quote_item")->load($itemId);
		$currentQty = $item->getQty();
		$qtyAux = $qty - $currentQty;
		$productId = $item->getProductId();
		$product = $this->initProduct($productId);
		
		// Max qty is exceeded, force to put the max value.
		if(! $this->checkMaxSaleQty($product, $qtyAux)) {
			$qty = $this->getMaxSaleQty($product);
		}
		
		// Get filter.
		$filter = new Zend_Filter_LocalizedToNormalized(
			array('locale' => Mage::app()->getLocale()->getLocaleCode())
		);
		$qty = $filter->filter(trim($qty));
		
		return $qty;
	}
	
	public function checkFreesample($product)
	{
		$freesampleCategoryId = $this->getFreesampleCategoryId();
		$categories = $product->getCategoryIds();
		
		
		return ! empty($categories) && in_array($freesampleCategoryId, $categories);
	}
	
	public function checkProductInCart($productId)
	{
		if(empty($productId)) return false;
		
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		if(empty($quote)) return false;
		
		return $quote->hasProductId($productId);
	}
	
	public function checkFreesampleLimit()
	{
		$freesampleCount = $this->getFreesampleCount();
		return ($freesampleCount >= $this->getFreesampleLimit());
	}
	
	public function getFreesampleCount()
	{
		$freesampleCount = 0;
		
		$cart = $this->_getCart();
		$items = $this->getRealItemsCollection();
		foreach($items as $_item) {
			if($this->checkFreesample($_item->getProduct())) $freesampleCount++;
		}
		
		return $freesampleCount;
	}
	
	public function getItemsCount()
	{
		$cart = $this->_getCart();
		$itemsCount = $cart->getQuote()->getItemsCount();
		
		return $itemsCount;
	}
	
	public function getRealItemsCollection()
	{
		$_items = Mage::getModel('sales/quote_item')->getCollection();
		$_items->addFieldToFilter('parent_item_id', array('null' => true));
		$_items->setQuote($this->_getCart()->getQuote());
		
		return $_items;
	}
	
	public function getRealItemsCount()
	{
		$_items = $this->getRealItemsCollection();
		return $_items->count();
	}
	
	public function getRealItemsQty()
	{
		$_items = $this->getRealItemsCollection();
		$qtyTotal = 0;
		foreach($_items as $_item) $qtyTotal += $_item->getQty();
		
		return $qtyTotal;
	}
	
	public function getTotals()
	{
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		return $quote->getTotals();
	}
	
	public function getDiscountAmount()
	{
		$totals = $this->getTotals();
		
		if(! empty($totals) && ! empty($totals['discount'])) {
			$discount = round($totals['discount']->getValue());
		} else {
			$discount = 0;
		}
		
		return $discount;
	}
	
	public function getSubtotalAmount()
	{
		$totals = $this->getTotals();
		
		if(! empty($totals) && ! empty($totals['subtotal'])) {
			$subtotal = round($totals['subtotal']->getValue());
		} else {
			$subtotal = 0;
		}
		
		return $subtotal;
	}
	
	public function getGrandTotalAmount()
	{
		$totals = $this->getTotals();
		
		if(! empty($totals) && ! empty($totals['grand_total'])) {
			$grandTotal = round($totals['grand_total']->getValue());
		} else {
			$grandTotal = 0;
		}
		
		return $grandTotal;
	}
	
	public function getTotalsArray()
	{
		$getTotals = $this->getTotals();
		$totals = array();
		foreach($getTotals as $key => $_total) {
			$value = $_total->getValue();
			if(! empty($value)) $totals[$key] = $value;
		}
		
		return $totals;
	}
	
	public function getApplicableSubtotal()
	{
		// Get all item collection.
		$items = $this->getRealItemsCollection();
		
		// Get subtotal excluding Giftcard product type.
		$subtotal = 0;
		foreach($items as $_item) {
			if($_item->getProduct()->getTypeId() != "giftcard") {
				$subtotal += $_item->getRowTotal();
			}
		}
		
		return $subtotal;
	}
	
	public function getModuleConfig()
	{
		if(empty($this->moduleConfig)) {
			$this->moduleConfig = array(
				'ajaxcart' => array(
					'enabled' => Mage::getStoreConfig('alliance_ajaxcart/ajaxcart_config/enabled'),
				),
				'freesamples' => array(
					'enabled' => Mage::getStoreConfig('alliance_ajaxcart/ajaxcart_freesamples/enabled'),
					'title' => Mage::getStoreConfig('alliance_ajaxcart/ajaxcart_freesamples/title'),
					'category_id' => Mage::getStoreConfig('alliance_ajaxcart/ajaxcart_freesamples/category_id'),
					'limit' => Mage::getStoreConfig('alliance_ajaxcart/ajaxcart_freesamples/limit'),
				),
				'wishlist' => array(
					'enabled' => Mage::getStoreConfig('alliance_ajaxcart/ajaxcart_wishlist/enabled'),
					'show_cartpage' => Mage::getStoreConfig('alliance_ajaxcart/ajaxcart_wishlist/show_cartpage'),
				),
			);
		}
		
		return $this->moduleConfig;
	}
	
	public function getAjaxcartEnabled()
	{
		$moduleConfig = $this->getModuleConfig();
		return $moduleConfig['ajaxcart']['enabled'];
	}
	public function getFreesampleEnabled()
	{
		$moduleConfig = $this->getModuleConfig();
		return $moduleConfig['freesamples']['enabled'];
	}
	public function getWishlistEnabled()
	{
		$moduleConfig = $this->getModuleConfig();
		return $moduleConfig['wishlist']['enabled'];
	}
	public function getWishlistShowCart()
	{
		$moduleConfig = $this->getModuleConfig();
		return $moduleConfig['wishlist']['show_cartpage'];
	}
	public function getFreesampleCategoryId()
	{
		$moduleConfig = $this->getModuleConfig();
		return $moduleConfig['freesamples']['category_id'];
	}
	public function getFreesampleLimit()
	{
		$moduleConfig = $this->getModuleConfig();
		return $moduleConfig['freesamples']['limit'];
	}
	public function getFreesampleTitle()
	{
		$moduleConfig = $this->getModuleConfig();
		return $moduleConfig['freesamples']['title'];
	}

	public function getItemsArray()
	{
		$itemsArray = array();
		$items = $this->getRealItemsCollection();
		foreach($items as $_item) {
			$itemId = $_item->getId();
			
			// Get Product.
			$product = $_item->getProduct();
			$option = $_item->getOptionByCode('product_type');
			if($option) {
				$product = $option->getProduct();
			}
			
			// Get thumnail.
			$getProductThumbnail = Mage::helper('catalog/image')->init($product, 'thumbnail');
			
			// Get product Name.
			$name = Mage::helper('core')->escapeHtml($product->getName());
			
			// Get options.
			$options = array();
			if($product->isConfigurable()) {
				$options = $this->getItemOptions($itemId);
			}
			
			// Build array.
			$itemsArray[$itemId] = array(
				'id' => $itemId,
				'productId' => $_item->getProductId(),
				'url' => $product->getUrlModel()->getUrl($product),
				'img' => '<img src="'.$getProductThumbnail->resize(50)->setWatermarkSize('30x10').'" alt="'.$name.'"/>',
				'name' => $name,
				'qty' => $_item->getQty(),
				'price' => $_item->getCalculationPrice(),
				'editUrl' => Mage::getUrl('checkout/cart/configure', array('id' => $itemId)),
				'isNotEditable' => $this->isNotEditable($product),
				'freesample' => $this->checkFreesample($product),
			);
			if(! empty($options)) $itemsArray[$itemId]['options'] = $options;
		}
		
		return array_reverse($itemsArray, true);
	}
	
	public function getItemOptions($itemId)
	{
		$options = array();
		
		$_itemsOptions = Mage::getModel('sales/quote_item_option')->getCollection();
		$_itemsOptions->
			addFieldToFilter('item_id', $itemId)->
			addFieldToFilter('code', "attributes")
		;
		foreach($_itemsOptions as $_itemOption) {
			$data = unserialize($_itemOption->getValue());
			
			foreach($data as $attributeId => $attributeValue) {
				$options[$attributeId] = $this->getOptionData($attributeId, $attributeValue);
			}
		}
		
		return $options;
	}
	
	function getOptionData($attributeId, $attributeValue)
	{
		// Load Attribute model.
		$attribute = Mage::getModel('eav/entity_attribute')->load($attributeId);
		$attributeOptionsModel = Mage::getModel('eav/entity_attribute_source_table');
		$attributeOptionsModel->setAttribute($attribute);
		$attributeOptions = $attributeOptionsModel->getAllOptions(false);
		
		foreach($attributeOptions as $_attrOption) {
			if($_attrOption['value'] == $attributeValue) {
				if(is_array($_attrOption['label'])) {
					$text = nl2br(implode("\n", $_attrOption['label']));
				} else {
					$text = $_attrOption['label'];
				}
			}
		}
		
		return array(
			'label' => Mage::helper('core')->escapeHtml($attribute->getFrontendLabel()),
			'value' => $attributeValue,
			'text' => $text,
		);
	}
	
	public function isNotEditable($product)
	{
		if(method_exists($product, 'isVisibleInSiteVisibility')) {
			$isVisibleInSiteVisibility = $product->isVisibleInSiteVisibility();
		} else {
			$product = $product->getProduct();
			$isVisibleInSiteVisibility = $product->isVisibleInSiteVisibility();
		}
		
		return ($this->checkFreesample($product) || ! $isVisibleInSiteVisibility || $product->getPrice() == 0);
	}
	
	public function getCartBlock()
	{
		$cartBlock = Mage::app()->getLayout()->createBlock('checkout/cart_sidebar');
		$cartBlock->setTemplate('alliance_ajaxcart/cartheader.phtml');
		$cartBlock->addItemRender('simple', 'checkout/cart_item_renderer', 'alliance_ajaxcart/cartitem.phtml');
		$cartBlock->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'alliance_ajaxcart/cartitem.phtml');
		$cartBlock->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'alliance_ajaxcart/cartitem.phtml');
		
		$cartBlock->setData("cartExists", true);
		
		return $cartBlock->toHtml();
	}
	
	
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}
}

