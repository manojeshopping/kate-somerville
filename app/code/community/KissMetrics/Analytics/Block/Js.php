<?php
class KissMetrics_Analytics_Block_Js extends Mage_Core_Block_Text
{

	protected $script = '';

    protected function _prepareLayout() {
	    $this->_addInitScript();
	    $this->_addIdentifyScript();
	    $this->_generateTrackingScripts();
	    $this->_addListenerScripts();
	    $this->_addBrowserInfo();
        return parent::_prepareLayout();
    }

    protected function _beforeToHtml() {
	    
	    $scripts = "\n\n<!-- KISSmetrics tracking snippet -->\n<script type=\"text/javascript\">\n".$this->script."\n</script>\n<!-- End KISSmetrics tracking snippet -->\n\n";
	    $this->setText($scripts);
        return parent::_beforeToHtml();
    }

	protected function _generateTrackingScripts() {
		
		$helper  = Mage::helper('kissmetrics_analytics');
		$handles = $this->getLayout()->getUpdate()->getHandles();
		$title   = $this->getLayout()->getBlock('head')->getTitle();
		
		// Show on all pages		
		$this->_addEventRecord('Viewed Page',array('title'=>$title,'url'=>Mage::helper('core/url')->getCurrentUrl()));
		$this->script .= "_kmq.push(['trackClick', '#cartHeader', 'Viewed My Bag']);\n";
		$this->script .= "_kmq.push(['trackSubmit', '#newsletter-validate-detail-footer', 'Newsletter Signup']);\n";
		//$this->_addClickEvent('.form-subscribe .button','Newsletter Signup',array(),'.form-subscribe #newsletter-footer');

		
		// Product Search
		if (in_array('catalogsearch_result_index',$handles)) {
			$query = Mage::app()->getRequest()->getParam('q');
			$this->_addEventRecord('Product Search',array('query'=>$query));
		}
		
		// Viewed Product / Item
		if (in_array('catalog_product_view',$handles)) {
			$product = Mage::registry('current_product');
			$data = array(
				'sku' => $product->getSku(),
				'name' => $product->getName(),
				'price' => $product->getFinalPrice(),
			);
			$this->_addEventRecord('Viewed Product',$data);
			$this->_addClickEvent('.btn-cart','Add to Cart (click)',$data,array('qty'));
		
			
		}
		
		// cart / onepage
		if (in_array('onestepcheckout_index_index',$handles) || in_array('checkout_cart_index',$handles)) {
			//Mage::log("\n\n".'onestepcheckout_index_index',null,'kiss.log');
			$quote = Mage::getModel('checkout/session')->getQuote();

			$data = array(
				'order total' => ($quote->getData('grand_total')) ? $quote->getData('grand_total') : '0',
				'order subtotal' => ($quote->getData('subtotal')) ? $quote->getData('subtotal') : '0'
			);
			$this->_addEventRecord('Checkout Step',$data);

			foreach ($quote->getAllItems() as $item) {
				
				if(!$item->getData('parent_item_id')) {
					$data = $helper->getKissItemData($item);
					$this->_addEventRecord('Checkout Step Item',$data);
				}
				
			}
			
		}
		
		
		
		// onepage
		if (in_array('onestepcheckout_index_index',$handles) ) {
			//Mage::log("\n\n".'onestepcheckout_index_index',null,'kiss.log');
			$quote = Mage::getModel('checkout/session')->getQuote();

			$data = array(
				'order total' => ($quote->getData('grand_total')) ? $quote->getData('grand_total') : '0',
				'order subtotal' => ($quote->getData('subtotal')) ? $quote->getData('subtotal') : '0'
			);
			$this->_addEventRecord('Onepage Checkout View',$data);

/*
			foreach ($quote->getAllItems() as $item) {
				
				if(!$item->getData('parent_item_id')) {
					$data = $helper->getKissItemData($item);
					$this->_addEventRecord('Checkout Step Item',$data);
				}
				
			}
*/
			
		}
		
		

		// Login // Logout
		if (in_array('customer_account_login',$handles)) {
			$data = array();
			$this->_addClickEvent('#login-form #send2','Logged In',$data,array('email'));
		}
		if (in_array('customer_account_logoutsuccess',$handles)) {
			$data = array();
			$this->_addEventRecord('Logged Out',$data);
		}

		//Add To Cart
		$products_added = Mage::getSingleton('core/session')->getData('products_added');
		if ( $products_added ) {
			foreach ( $products_added as $data ) {
				$this->_addEventRecord('Add to Cart',$data);
			}
			
			Mage::getSingleton('core/session')->setData('products_added',false);
			
		}



//Remove Item From


		//Entered Promo Code
		if (in_array('checkout_cart_index',$handles)) {
			
			$quote = Mage::getSingleton('checkout/session')->getQuote();
			$coupon_code = $quote->getData('coupon_code');
			$promotion_added = Mage::getSingleton('core/session')->getData('promotion_added');
			if ( $coupon_code && $coupon_code != $promotion_added ) {
				$this->_addEventRecord('Entered Promo Code',array('data'=>$coupon_code));
				Mage::getSingleton('core/session')->setData('promotion_added',$coupon_code);
			}
			
		}
		
		//Registered / Sign Up
		if (Mage::getSingleton('core/session')->getData('customer_register')) {
			
			$session = Mage::getSingleton('customer/session');
			$customer = $session->getCustomer();
			
			$data = $customer->getData();
			// NOTE :: city country info has not been collected yet
			//Customer ID
			//City
			//Country
			//Phone Number/Gender/Demographic Info

			unset(
				$data['entity_type_id'],
				$data['attribute_set_id'],
				$data['group_id'],
				$data['increment_id'],
				$data['is_active'],
				$data['disable_auto_group_change'],
				$data['password_hash']
			);
			
			$this->_addEventRecord('Registered',$data);
			Mage::getSingleton('core/session')->setData('customer_register',false);
			
		}
			
/* Moved To Event Observer Now Tracks admin area too
		//Purchased / Completed Order
		if (in_array('checkout_onepage_success',$handles)) {
			
			$order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
			
			$data = array(
				'Order ID' => $order->getIncrementId(),
				'Order Total' => $order->getGrandTotal(),
				'Order Subtotal' => $order->getSubtotal(),
				'Order Shipping' => $order->getShippingAmount(),
				'Order Discount' => $order->getDiscountAmount(),
				'Order Tax' => $order->getTaxAmount(),
				'Revenue Total' => $order->getGrandTotal(),
				'Revenue Subtotal' => $order->getSubtotal(),
				'Revenue Shipping' => $order->getShippingAmount(),
				'Revenue Discount' => $order->getDiscountAmount(),
				'Revenue Tax' => $order->getTaxAmount(),
				
			);
			$this->_addEventRecord('Completed Order',$data);
			
			foreach ($order->getAllItems() as $item ) {
				
				$data = $helper->getKissItemData($item);
				$this->_addEventRecord('Completed Order Item',$data);
				
			}
			
		}
*/
			
		//Canceled Order
			//Order ID
			//Order Total
			//Order Subtotal
			//SKU/ProductID
			//Product Name
			//Color/Size/Category/Variation
			//Price
			//Quantity
			
		//Refunded / Returned Item
			//SKU/ProductID
			//Product Name
			//Color/Size/Category/Variation
			//Price
			//Quantity
			
		//Referred Friend (or any Social Actions)
			//Referral Recipient
			$this->_addSocialLinkListenerScripts();
		
		return $this;
		
	}

	protected function _addEventRecord($event,$data) {
		
		$data = json_encode($data);
		
		$this->script .= "_kmq.push(['record', '".$event."', ".$data."]);\n";
		
		return $this;
	}

	protected function _addPropertyRecord($name,$value) {

		$this->script .= "_kmq.push(['set', '".$name."', ".$value."]);\n";

		return $this;
	}


	protected function _addClickEvent($selector,$event,$data,$fields=array()) {
		
		$data = json_encode($data);
		$field_str = '';
		foreach($fields as $field) {
			$field_str .= "kiss_data.".$field." = jQuery('#".$field."').val();";
		}
		
		$this->script .= "
if(window.jQuery) {
	var kiss_data = ".$data.";
	jQuery('".$selector."').click(function() {
		".$field_str."
	   _kmq.push(['record', '".$event."', kiss_data]);
	});
}
		";
		
	}


	protected function _addInitScript() {
		
		$id = Mage::helper('kissmetrics_analytics')->getWriteKey();//"39c899b9d14f9cffa9f98eb310a3593e61fb3937";
		$str = "
var _kmq = _kmq || [];
var _kmk = _kmk || '".$id."';
function _kms(u){
  setTimeout(function(){
    var d = document, f = d.getElementsByTagName('script')[0],
    s = d.createElement('script');
    s.type = 'text/javascript'; s.async = true; s.src = u;
    f.parentNode.insertBefore(s, f);
  }, 1);
}
_kms('//i.kissmetrics.com/i.js');
_kms('//doug1izaerwt3.cloudfront.net/' + _kmk + '.1.js');

";		
		$this->script .= $str;
		return $this;
	}

	protected function _addIdentifyScript() {
		
		$customer_session = Mage::getSingleton('customer/session');
		$quote = Mage::getModel('checkout/cart')->getQuote();
		$email = false;
		
		if($customer_session->isLoggedIn()) {
			$email = $customer_session->getCustomer()->getEmail();
		}
		else if ( $quote->getEmail() ) {
			$email = $quote->getEmail();
		}
		
		if($email) {
			$this->script .= "_kmq.push(['identify', '".$email."']);\n";
		}
		
		return $this;
	}

	protected function _addListenerScripts() {
		
		$this->script .= "
if(window.jQuery) {		
	jQuery('.products-grid li.item .button').click(function() {
		var parent_elem = jQuery(this).closest('li.item');
		var parent_arr = jQuery(this).parent().attr('id').split('_');
		var parent_id = parent_arr[ parent_arr.length - 1 ];
		
		var data = {};
		data.id = parent_id;
		data.name = parent_elem.find('.product-name').text();
		data.price = Number( parent_elem.find('.product-price').text().replace(/[^0-9\.]+/g,'') );
		data.qty = parent_elem.find('#qty_'+parent_id).val();
		
		_kmq.push(['record', 'Add to Cart (click)', data]);	
	
	});
}
";
	}
	
	protected function _addSocialLinkListenerScripts() {

		$this->script .= "
if(window.jQuery) {		
	jQuery('.social-links a').click(function() {
		var data = {};
		data.src = jQuery(this).attr('href');
		_kmq.push(['record', 'Social Link (click)', data]);	
	});
	jQuery('.social-icons span').click(function() {
		var data = {};
		data.type = jQuery(this).text();
		_kmq.push(['record', 'Social Link (click)', data]);	
	});
}
";

		
	}

	protected function _addBrowserInfo() {
		$this->script .= "

var browser = false;
jQuery.each( Prototype.Browser, function( i, val ) {
  if (val) {
    browser = i;
  }
});
if(browser) {
_kmq.push(['set', {'Browser' : browser}]);
}
var isMobile = function() {
    return (navigator.userAgent.match(/Android/i) ||
            navigator.userAgent.match(/BlackBerry/i) ||
            navigator.userAgent.match(/iPhone|iPod/i) ||
            navigator.userAgent.match(/iPad/i) ||
            navigator.userAgent.match(/Opera Mini/i) ||
            navigator.userAgent.match(/IEMobile/i) );
};

if (isMobile()) _kmq.push(['set', {'Mobile Session' : 'Yes' }]);		
		_kmq.push(['set', {'Screen Resolution' : screen.width + \" x \" +
  screen.height }]);

";

	}

}

