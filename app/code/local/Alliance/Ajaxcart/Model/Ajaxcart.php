<?php


class Alliance_Ajaxcart_Model_Ajaxcart extends Mage_Core_Model_Abstract
{
	private $_newItemsQty;
	
	public function _construct()
	{
		parent::_construct();
		$this->_init('ajaxcart/ajaxcart');
	}
	
	
	public function addProduct($product, $qty, $options, $extraParams = null)
	{
		$cart = $this->_getCart();
		$helper = $this->_getHelper();
		
		// Check Maximum Qty Allowed in Shopping Cart.
		if(! $helper->checkMaxSaleQty($product, $qty)) {
			throw new Exception('The amount selected for the product exceeds the maximum allowed for purchase.');
			return false;
		}
		
		// If product price is 0, only freesamples can be added.
		$isFreesample = $helper->checkFreesample($product);
		// Mage::log("addProduct: extraParams: ".print_r($extraParams, 1).".", null, 'ajaxcart.log');
		if(! $isFreesample && ($product->getTypeId() != 'giftcard' && $product->getPrice() == 0) && ($product->getTypeId() == 'giftcard' && (float)$extraParams['giftcard_amount'] == 0)) {
			throw new Exception('Error.');
			return false;
		}
		
		
		// Check for FreeSample.
		if($helper->getFreesampleEnabled() && $isFreesample) {
			// Check item in cart.
			if($helper->checkProductInCart($product->getId())) {
				throw new Exception('The Free Sample product already been added to cart.');
				return false;
			}
			
			// Check freesamples limit.
			if($helper->checkFreesampleLimit()) {
				throw new Exception('You can only select up to '.$helper->getFreesampleLimit().' free samples per order.');
				return false;
			}
			
			// Check cart amount.
			$cartAmount = $helper->getApplicableSubtotal();
			// Mage::log("addProduct: Freesample - cartAmount: ".$cartAmount, null, 'ajaxcart.log');
			if($cartAmount <= 0) {
				throw new Exception('Your purchase does not qualify for free samples.');
				return false;
			}
			
			// Force freesample qty to 1.
			$qty = 1;
		}
		
		// Mage::log("helperaddProduct: productId: ".$product->getId()." - ".$qty." - name: ".$product->getName(), null, 'ajaxcart.log');
		$params = array('qty' => $qty, 'super_attribute' => $options);
		if(! empty($extraParams)) $params += $extraParams;
		$cart->addProduct($product, $params);
		$cart->save();
		if(! $cart->getQuote()->getHasError()) {
			return true;
		}
		
		return false;
	}
	
	public function updateProduct($product, $qty, $options = null)
	{
		$quote = $this->_getSession()->getQuote();
		$cart = $this->_getCart();
		$helper = $this->_getHelper();
		
		// Get item to change.
		$item = $quote->getItemByProduct($product);
		if(! $item->getId()) {
			throw new Exception('The product does not exist in the cart.');
			return;
		}
		
		// Check Maximum Qty Allowed in Shopping Cart.
		if(! $helper->checkMaxSaleQty($product, $qty)) {
			throw new Exception('The amount selected for the product exceeds the maximum allowed for purchase.');
			return;
		}
		
		// Check for FreeSample.
		if($helper->getFreesampleEnabled() && $helper->checkFreesample($product)) {
			// Force freesample qty to 1.
			$qty = 1;
		} else {
			$qty = ($item->getQty() + $qty);
		}
		
		// Update item.
		try {
			$cartData = array($item->getId() => array('qty' => $qty, 'super_attribute' => $options));
			$cartData = $cart->suggestItemsQty($cartData);
			
			$cart->updateItems($cartData);
			$cart->save();
			// Mage::log("updateProduct: saved.", null, 'ajaxcart.log');
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
			return;
		}
	}
	
	public function removeItem($itemId)
	{
		$helper = $this->_getHelper();
		$cart = $this->_getCart();
		
		// Delete item.
		try {
			$cart->removeItem($itemId);
			$cart->save();
			$this->_getSession()->setCartWasUpdated(true);
			
			if(! $cart->getQuote()->getHasError()) {
				return true;
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
			return false;
		}
	}
	
	public function updateItem($itemId, $qty)
	{
		// Validate data.
		if(! is_numeric($itemId) || ! is_numeric($qty)) {
			throw new Exception('Itemg data or quantity error.');
			return;
		}
		
		// Load Helper and Cart.
		$helper = $this->_getHelper();
		$cart = $this->_getCart();
		
		// Update item.
		try {
			// Update Item.
			$qty = $helper->filterMaximumQtyAllowed($itemId, $qty);
			$item = $cart->getQuote()->getItemById($itemId);
			$item->setQty($qty);
			
			// $params['qty'] = $helper->filterMaximumQtyAllowed($itemId, $qty);
			// $item = $cart->updateItem($itemId, new Varien_Object($params));
			
			// Dispatch Magento event.
			Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
			
			// Save cart.
			$cart->save();
			$this->_getSession()->setCartWasUpdated(true);
			
			if(! $item->getHasError()) {
				return true;
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
			return false;
		}
	}
	
	public function updateCart($cartData)
	{
		if(empty($cartData) || ! is_array($cartData)) {
			throw new Exception('Cart data is empty.');
			return;
		}
		
		// Load Helper and Cart.
		$helper = $this->_getHelper();
		$cart = $this->_getCart();
		
		foreach($cartData as $index => $data) {
			if (isset($data['qty'])) {
				// Filter qty.
				$cartData[$index]['qty'] = $helper->filterMaximumQtyAllowed($index, $data['qty']);
			}
		}
		
		if(! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
			$cart->getQuote()->setCustomerId(null);
		}
		
		try {
			$cartData = $cart->suggestItemsQty($cartData);
			$cart->updateItems($cartData)->save();
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
			return false;
		}
	}
	
	public function emptyCart()
	{
		$cart = $this->_getCart();
		
		try {
			$cart->truncate()->save();
			$this->_getSession()->setCartWasUpdated(true);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
			return;
		}
	}
	
	
	public function checkFreesamples()
	{
		// Load helper
		$helper = $this->_getHelper();
		
		if($helper->getFreesampleEnabled()) {
			// Load cart.
			$cart = $this->_getCart();
			$items = $helper->getRealItemsCollection();
			$this->_newItemsQty = $cart->getSummaryQty();
			$itemCartCount = $cart->getItemsCount();
			$freesampleCount = $helper->getFreesampleCount();
			
			if($freesampleCount > 0) {
				// Check if only samples or cart subtotal is 0.
				$subtotalAmount = $helper->getApplicableSubtotal();
				// Mage::log("checkFreesamples: subtotalAmount: ".$subtotalAmount, null, 'ajaxcart.log');
				if($freesampleCount == $itemCartCount || $subtotalAmount <= 0) {
					
					// Only the first item is needed to remove, because all other items are 
					// removed when this item is saved and the observer run again.
					foreach ($items as $_item) {
						$product = $_item->getProduct();
						if($helper->checkFreesample($product)) {
							$firstFreesampleItem = $_item;
							break;
						}
					}
					
					$cart->removeItem($firstFreesampleItem->getId());
					$cart->save();
					$this->_getSession()->setCartWasUpdated(true);
					
					return true;
				}
				
				
				$freesamplesItems = array();
				foreach ($items as $_item) {
					$itemID = $_item->getId();
					if(! empty($itemID)) {
						$product = $_item->getProduct();
						if($helper->checkFreesample($product)) {
							// Quantity to 1.
							$qty = $_item->getQty();
							if($qty > 1) {
								$this->_newItemsQty -= ($qty - 1);
								$this->updateProduct($product, -($qty - 1));
								// Mage::log("checkFreesamples: updateProduct done.", null, 'ajaxcart.log');
							}
							
							// Save item in array.
							$freesamplesItems[] = $_item;
						}
					}
				}
				
				// Delete extra free samples.
				$freesampleLimit = $helper->getFreesampleLimit();
				if($freesampleCount > $freesampleLimit) {
					for($i = ($freesampleCount - 1); $i >= $freesampleLimit; $i--) {
						// Check the freesamples count again.
						$newFreesampleCount = $helper->getFreesampleCount();
						if($newFreesampleCount <= $freesampleLimit) break;
						
						$this->removeItem($freesamplesItems[$i]->getId());
						$this->_newItemsQty--;
					}
				}
			}
		}
		
		return true;
	}
	
	public function getNewItemsQty()
	{
		// return $this->_newItemsQty;
		return $this->_getHelper()->getRealItemsQty();
	}
	
	public function getAjaxcartEnabled()
	{
		return $this->_getHelper()->getAjaxcartEnabled();
	}
	
	
	public function addToWishlist($product, $qt, $options = null)
	{
		// Get Wishlist.
		$wishlist = $this->_getWishlist();
		if(! $wishlist) {
			throw new Exception('No wishlist.');
			return;
		}
		
		// Check product.
		if (! $product->getId() || ! $product->isVisibleInCatalog()) {
			throw new Exception('Cannot specify product.');
			return;
		}
		
		// Insert item in wishlist.
		try {
			$requestParams = array(
				'qty' => $qt,
				'super_attribute' => $options,
			);
            $buyRequest = new Varien_Object($requestParams);
			
			$result = $wishlist->addNewItem($product, $buyRequest);
			if (is_string($result)) {
				throw new Exception('Error inserting wishlist item: '.$e->getMessage());
				return;
			}
			$wishlist->save();
			
			// Dispatch Event.
			Mage::dispatchEvent(
				'wishlist_add_product',
				array(
					'wishlist' => $wishlist,
					'product' => $product,
					'item' => $result
				)
			);
			
			return $wishlist;
		} catch (Exception $e) {
			throw new Exception('Error inserting wishlist item: '.$e->getMessage());
			return;
		}
	}
	
	public function removeToWishlist($itemId)
	{
		// Get Wishlist.
		$wishlist = $this->_getWishlist();
		if(! $wishlist) {
			throw new Exception('No wishlist.');
			return;
		}
		
		// Get Wishlist item.
		$item = Mage::getModel('wishlist/item')->load($itemId);
        if (! $item->getId()) {
			throw new Exception('No item in wishlist.');
			return;
        }
		
		// Delete item.
		try {
			$item->delete();
			$wishlist->save();
			
			return $wishlist;
		} catch (Exception $e) {
			throw new Exception('Error inserting wishlist item: '.$e->getMessage());
			return;
		}
	}
	
	public function addWishlistToCart($itemId)
	{
		// Get Wishlist.
		$wishlist = $this->_getWishlist();
		if(! $wishlist) {
			throw new Exception('No wishlist.');
			return;
		}
		
		// Get Wishlist item.
		$item = Mage::getModel('wishlist/item')->load($itemId);
        if (! $item->getId()) {
			throw new Exception('No item in wishlist.');
			return;
        }
		
		// Get Cart.
		$cart = $this->_getCart();
		
		try {
			$options = Mage::getModel('wishlist/item_option')->getCollection()->addItemFilter(array($itemId));
			$item->setOptions($options->getOptionsByItem($itemId));
			
			$product = $item->getProduct();
			
			$options = array();
			if($product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
				$attributesOptions = $product->getCustomOptions();
				foreach($attributesOptions as $_attribute) {
					if($_attribute->getCode() == "attributes") {
						$options = unserialize($_attribute->getValue());
					}
				}
			}
			
			if($this->addProduct($product, $item->getQty(), $options)) {
				$item->delete();
				$cart->save()->getQuote()->collectTotals();
			}

			$wishlist->save();
			Mage::helper('wishlist')->calculate();
			
			return $wishlist;
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
			return;
		}
	}
	
	public function moveToWishlist($cartItemId)
	{
		// Get Wishlist.
		$wishlist = $this->_getWishlist();
		if(! $wishlist) {
			throw new Exception('No wishlist.');
			return;
		}
		
		// Get Cart.
		$cart = $this->_getCart();
		$session = $this->_getSession();
		
		try {
			$item = $cart->getQuote()->getItemById($cartItemId);
			if(! $item) {
				throw new Exception("Requested cart item doesn't exist (#".$cartItemId.").");
				return;
			}
			
			$productId = $item->getProductId();
			$buyRequest = $item->getBuyRequest();
			
			// Add item to Wishlist.
			$wishlist->addNewItem($productId, $buyRequest);
			$wishlist->save();
			Mage::helper('wishlist')->calculate();
			
			// Remove item from Cart.
			$cart->getQuote()->removeItem($cartItemId);
			$cart->save();
			
			
			return $wishlist;
		} catch (Exception $e) {
			throw new Exception('Error moving cart in wishlist: '.$e->getMessage());
			return;
		}
	}
	
	public function getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}
	
	protected function _getHelper()
	{
		return Mage::helper('ajaxcart');
	}
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
	
	protected function _getWishlist()
	{
		$wishlist = Mage::registry('wishlist');
		if($wishlist) {
			return $wishlist;
		}
		
		try {
			$customerId = $this->getCustomerSession()->getCustomerId();
			
			$wishlist = Mage::getModel('wishlist/wishlist');
			$wishlist->loadByCustomer($customerId, true);
			
			if (! $wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
				$wishlist = null;
				throw new Exception("Requested wishlist doesn't exist: ".$e->getMessage());
			}

			Mage::register('wishlist', $wishlist);
			
			return $wishlist;
		} catch (Exception $e) {
			throw new Exception('Error getting wishlist: '.$e->getMessage());
			return false;
		}

		return false;
	}
}