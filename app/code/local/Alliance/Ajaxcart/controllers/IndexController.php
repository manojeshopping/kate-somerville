<?php
class Alliance_Ajaxcart_IndexController extends Mage_Core_Controller_Front_Action
{
	
	public function insertAction()
	{
		// Load helper and model.
		$helper = $this->_getHelper();
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		// Get data.
		$extraParams = array();
		$params = $this->getRequest()->getParams();
		foreach($params as $_paramKey => $_paramValue) {
			if($_paramKey == "productId") $productId = $_paramValue;
			elseif($_paramKey == "qty") $qty = $_paramValue;
			elseif($_paramKey == "options") $options = $_paramValue;
			elseif($_paramKey != "id") {
				$extraParams[$_paramKey] = $_paramValue;
			}
		}
		
		// Validate data.
		if(! $this->_validateData($productId, $qty)) {
			return;
		}
		
		// Load product.
		$product = $helper->initProduct($productId);
		if(! $product) {
			$helper->printError("Error loading product.");
			return;
		}
		
		// Add product to cart.
		try {
			$model->addProduct($product, $qty, $options, $extraParams);
			
			// Dispatch Event.
			Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
			);
			
			// Print Success.
			$itemsArray = $helper->getItemsArray();
			$helper->printSuccess("Product added", array(
				'lastItemId' => key($itemsArray),
				'itemsCount' => $helper->getRealItemsCount(),
				'totals' => $helper->getTotalsArray(),
				'items' => $itemsArray,
			));
			return;
		} catch (Exception $e) {
			$helper->printError($e->getMessage());
			return;
		}
	}

	public function deleteAction()
	{
		// Load helper and model.
		$helper = $this->_getHelper();
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		// Get data.
		$itemId = $this->getRequest()->getParam('id');
		
		// Validate data.
		if(! is_numeric($itemId)) {
			$helper->printError("Unable to process the item id.");
			return;
		}
		
		// Update cart.
		try {
			$model->removeItem($itemId);
			$helper->printSuccess("Product removed", array(
				'itemId' => $itemId,
				'itemsCount' => $helper->getRealItemsCount(),
				'totals' => $helper->getTotalsArray(),
				'items' => $helper->getItemsArray())
			);
			return;
		} catch (Exception $e) {
			$helper->printError($e->getMessage());
			return;
		}
	}
	
	public function updateAction()
	{
		// Load helper and model.
		$helper = $this->_getHelper();
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		// Get data.
		$itemId = $this->getRequest()->getParam('id');
		$qty = $this->getRequest()->getParam('qty');
		
		// Validate data.
		if(! is_numeric($itemId) || ! is_numeric($qty)) {
			$helper->printError("Unable to process the item id.");
			return;
		}
		
		// Update cart.
		try {
			$model->updateItem($itemId, $qty);
			$helper->printSuccess("Item updated", array(
				'itemId' => $itemId,
				'itemsCount' => $helper->getRealItemsCount(),
				'totals' => $helper->getTotalsArray(),
				'items' => $helper->getItemsArray())
			);
			return;
		} catch (Exception $e) {
			$helper->printError($e->getMessage());
			return;
		}
	}
	
	
	public function updateCartAction()
	{
		// Load helper and model.
		$helper = $this->_getHelper();
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		$cartData = $this->getRequest()->getParam('cart');
		try {
			$model->updateCart($cartData);
			$helper->printSuccess("Cart updated", array(
				'itemsCount' => $helper->getRealItemsCount(),
				'totals' => $helper->getTotalsArray(),
				'items' => $helper->getItemsArray(),
			));
			return;
		} catch (Exception $e) {
			$helper->printError($e->getMessage());
			return;
		}
	}
	
	public function clearCartAction()
	{
		// Load helper and model.
		$helper = $this->_getHelper();
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		try {
			$model->emptyCart();
			$helper->printSuccess("Cart emptied");
			return;
		} catch (Exception $e) {
			$helper->printError($e->getMessage());
			return;
		}
	}
	
	public function checkoutButtonsAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	
	public function addToWishlistAction()
	{
		// Load helper and model.
		$helper = $this->_getHelper();
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		// Check if user is loged in.
		if(! $model->getCustomerSession()->isLoggedIn()) {
			$helper->printError("Error.");
			return;
		}
		
		// Get data.
		$productId = $this->getRequest()->getParam('productId');
		$qty = $this->getRequest()->getParam('qty');
		$options = $this->getRequest()->getParam('options');
		
		// Validate data.
		if(! $this->_validateData($productId, $qty)) {
			return;
		}
		
		// Load product.
		$product = $helper->initProduct($productId);
		if(! $product) {
			$helper->printError("Error loading product.");
			return;
		}
		
		// Check $0 products.
		if($product->getPrice() == 0) {
			$helper->printError("Error.");
			return;
		}
		
		// Add product to wishlist.
		try {
			$wishlist = $model->addToWishlist($product, $qty, $options);
			
			// Print Success.
			$itemCount = Mage::helper('wishlist')->getItemCount();
			$helper->printSuccess("Product added", array(
				'itemCount' => $itemCount,
				'linkTitle' => __("My wishlist (".$itemCount." items)"),
				'successMessage' => __($product->getName()." has been added to your wishlist.")
			));
			return;
		} catch (Exception $e) {
			$helper->printError($e->getMessage());
			return;
		}
	}
	
	public function removeToWishlistAction()
	{
		// Load helper and model.
		$helper = $this->_getHelper();
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		// Get data.
		$itemId = $this->getRequest()->getParam('itemId');
		
		// Add product to wishlist.
		try {
			$wishlist = $model->removeToWishlist($itemId);
			
			// Print Success.
			$itemCount = Mage::helper('wishlist')->getItemCount();
			$helper->printSuccess("Product added", array(
				'itemCount' => $itemCount,
				'linkTitle' => __("My wishlist (".$itemCount." items)")
			));
			return;
		} catch (Exception $e) {
			$helper->printError($e->getMessage());
			return;
		}
	}
	
	public function addWishlistToCartAction()
	{
		// Load helper and model.
		$helper = $this->_getHelper();
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		// Get data.
		$itemId = $this->getRequest()->getParam('itemId');
		
		// Add product to wishlist.
		try {
			$wishlist = $model->addWishlistToCart($itemId);
			
			// Print Success.
			$wishlistItemCount = Mage::helper('wishlist')->getItemCount();
			$itemsArray = $helper->getItemsArray();
			$helper->printSuccess("Product added", array(
				'wishlistItemCount' => $wishlistItemCount,
				'linkTitle' => __("My wishlist (".$itemCount." items)"),
				'lastItemId' => key($itemsArray),
				'itemsCount' => $helper->getRealItemsCount(),
				'totals' => $helper->getTotalsArray(),
				'items' => $itemsArray,
			));
			return;
        } catch (Exception $e) {
			$itemData = Mage::helper('wishlist')
				->getWishlistItemCollection()
				->addFieldToFilter('wishlist_item_id', $itemId)
				->getFirstItem()
			;
			$product = $itemData->getProduct();
			
			if($product->isConfigurable()) {
				Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
                $redirectUrl = Mage::helper('wishlist')->getListUrl().'index/configure/id/'.$itemId;
				
				$helper->printSuccess("Product need confgured.", array('redirectUrl' => $redirectUrl));
				return;
			}
			
			$helper->printError($e->getMessage());
			return;
		}
	}
	
	public function wishlistSidebarAction()
	{
		$this->loadLayout();
		$wishlist = $this->getLayout()->createBlock('wishlist/customer_sidebar');
		$wishlist->setTemplate('alliance_ajaxcart/wishlist_sidebar.phtml');
		echo $wishlist->toHtml();
		return;
	}
	
	public function moveToWishlistAction()
	{
		// Load helper and model.
		$helper = $this->_getHelper();
		$model = Mage::getModel('ajaxcart/ajaxcart');
		
		// Get data.
		$itemId = $this->getRequest()->getParam('itemId');
		
		// Validate data.
		if(! is_numeric($itemId)) {
			$helper->printError("Error validating data.");
			return;
		}
		
		// Add product to wishlist.
		try {
			$wishlist = $model->moveToWishlist($itemId);
			
			// Print Success.
			$helper->printSuccess("Product added", array(
				'itemsCount' => $helper->getRealItemsCount(),
				'totals' => $helper->getTotalsArray(),
				'items' => $helper->getItemsArray(),
				'linkTitle' => __("My wishlist (".$itemCount." items)")
			));
			return;
		} catch (Exception $e) {
			$helper->printError($e->getMessage());
			return;
		}
	}
	
	public function wishlistCartProductsAction()
	{
		$this->loadLayout();
		$wishlist = $this->getLayout()->createBlock('ajaxcart/wishlist');
		$wishlist->setTemplate('alliance_ajaxcart/wishlist.phtml');
		echo $wishlist->toHtml();
		return;
	}
	
	public function freeSampleLimitAction()
	{
		$helper = $this->_getHelper();
		$freeSampleLimit = $helper->getFreesampleLimit();
		$helper->printSuccess("freeSampleLimit obtained.", array('freeSampleLimit' => $freeSampleLimit));
		return;
	}
	
	public function toaMessageAction()
	{
		$this->loadLayout();
		$toaBlock = $this->getLayout()->createBlock('toa/messages');
		$toaBlock->setTemplate('alliance_toa/messages.phtml');
		echo $toaBlock->toHtml();
		return;
	}
	
	// *** Protected / Private functions *** //
	protected function _validateData($productId, $qty)
	{
		if(! is_numeric($productId)) {
			$this->_getHelper()->printError("Unable to process the product id.");
			return false;
		}
		if(! is_numeric($qty)) {
			$this->_getHelper()->printError("Unable to process the product qty.");
			return false;
		}
		
		return true;
	}
	
	private function _getHelper()
	{
		return Mage::helper('ajaxcart');
	}
	// *** Protected / Private functions *** //
}

