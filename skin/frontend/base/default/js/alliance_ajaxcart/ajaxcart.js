(function(global) {
	var
		cartInitiated = false,
		
		topCartContainerClass = 'top-cart',
		topCartContentId = 'topCartContent',
		topCartLinkId = 'topCartLink',
		minCartId = 'mini-cart',
		cartHeaderId = 'cartHeader',
		cartTotalsTableId = 'shopping-cart-totals-table',
		cartTableId = 'shopping-cart-table',
		
		controller = '/ajaxcart/index/',
		
		maxsaleqtyClass = 'maxsaleqty-exceeded',
		
		topCartContainer,
		topCartContent,
		topCartLink,
		minCart,
		cartHeader,
		subTotalBlock,
		actionsBlock,
		emptyContentBlock,
		cartItems,
		wishlistLink,
		wishlistSidebar,
		
		initTopPosition,
		initLeftPosition,
		topCartExpanded = false,
		topCartInterval = null,
		
		environment,
		
		dialogLoaded = false,
		
		publicCart = {
			version: "1.0.3",
			debug: false,
			userLoggedIn: false,
			currencySymbol: '$'
		}
	;
	
	// *** Constructor *** //
	var supported = checkSupport();
	if(! supported) return false;
	// *** Constructor *** //
	
	
	// *** Public functions *** //
	publicCart.init = function(container) {
		debug('init AjaxCart.');
		
		// Overwrite if container.
		if(container != undefined) topCartContentId = container;
		
		// Check top cart content.
		var topcart = getTopcartContentElement();
		if(topcart == undefined) {
			debug('No such '+topCartContentId+' element.', 1);
			return false;
		}
		
		// Get init values.
		getInitCartValues();
		
		// Get init cart items.
		var _cartItems = getCartItems(true);
		debug(_cartItems.items);
		if(_cartItems.itemsCount > 0) {
			showTopcartElements();
		} else {
			showTopcartEmpty();
		}
		
		// Add defaults actions.
		setDefaultActions();
		
		// Relocate topcart.
		_relocateTopcart();
		
		// Set cart initiated.
		cartInitiated = true;
	}
	
	
	publicCart.setEnvironment = function(env) {
		if(! cartInitiated) return;
		
		debug('Setting environment.');
		environment = env;
		
		switch(env) {
			case "list":
				debug('List environment.');
				
				var list = new listFunctions();
				list.setListActions();
				
				break;
			case "view":
				debug('View environment.');
				
				var view = new viewFunctions();
				view.setViewActions();
				
				break;
			case "cart":
				debug('Cart environment.');
				
				var cart = new cartFunctions();
				cart.setCartActions();
				
				break;
			case "wishlist":
				debug('Wishlist environment.');
				
				var wishlist = new cartWishlist();
				wishlist.setWishlistActions();
				
				break;
			default:
				break;
		}
	}
	// *** Public functions *** //
	
	
	// *** Private functions *** //
	function checkSupport() {
		// Check protoype.
		if(Prototype == undefined) {
			debug('Prototype it is necessary for the operation of this library.', 1);
			return false;
		}
		
		return true;
	}
	
	function debug(msg, level) {
		if(publicCart.debug || level == 1) console.log(msg);
	}
	
	
	function getTopcartContentElement(flushCache) {
		if(topCartContent == undefined || flushCache) topCartContent = $(topCartContentId);
		
		return topCartContent;
	}
	function getTopcartContainerElement() {
		if(topCartContainer == undefined) topCartContainer = getTopcartContentElement().up('.'+topCartContainerClass);
		
		return topCartContainer;
	}
	function getTopCartLinkId() {
		if(topCartLink == undefined) topCartLink = $(topCartLinkId);
		
		return topCartLink;
	}
	function getTopcartElement() {
		if(minCart == undefined) minCart = $(minCartId);
		
		return minCart;
	}
	function getCartHeader() {
		if(cartHeader == undefined) cartHeader = $(cartHeaderId);
		
		return cartHeader;
	}
	function getSubtotalElement() {
		if(subTotalBlock == undefined) subTotalBlock = getTopcartContentElement().down('.subtotal');
		
		return subTotalBlock;
	}
	function getActionsElement() {
		if(actionsBlock == undefined) actionsBlock = getTopcartContentElement().down('.actions');
		
		return actionsBlock;
	}
	function getEmptyContentElement() {
		if(emptyContentBlock == undefined) emptyContentBlock = getTopcartContentElement().down('#topcart-empty-content');
		
		return emptyContentBlock;
	}
	function getWishlistLinkElement() {
		
		if(wishlistLink == undefined) wishlistLink = $$(".quick-access .links a:contains('Wishlist')")[0];
		
		return wishlistLink;
	}
	function getWishlistSidebarElement() {
		if(wishlistSidebar == undefined) setWishlistSidebarElement();
		
		// If is null, create the new wishilist sidebar empty.
		if(wishlistSidebar == null) {
			debug('getWishlistSidebarElement - insert wishlistSidebar');
			var compareBlock = $$('.block.block-list.block-compare')[0];
			if(compareBlock != undefined) {
				compareBlock.insert({'after': '<div class="block block-wishlist" id="wishlist-sidebar"></div>'});
				setWishlistSidebarElement();
			}
		}
		
		return wishlistSidebar;
	}
	function setWishlistSidebarElement() {
		wishlistSidebar = $("wishlist-sidebar");
	}
	
	function setDefaultActions() {
		debug('Setting default actions.');
		
		// Add action to "My Cart".
		var _topCartLink = getTopCartLinkId();
		_topCartLink.observe('click', function (e) {
			e.stop();
			debug('topCartLink click.');
			
			if (! topCartExpanded)  {
				_showCart();
			} else {
				_hideCart();
			}
		});
		
		// Add action to close mini cart button.
		getTopcartContentElement().down('.close-btn').observe('click', function (e) {
			e.stop();
			debug('close-btn click.');
			
			_hideCart();
		});
		
		// Add action to Edit item links.
		$$('#'+minCartId+' .btn-edit').each(function(link) {
			debug('Adding edit click action.');
			addEditClickAction(link);
		});
		// Add action to Remove item links.
		$$('#'+minCartId+' .btn-remove').each(function(link) {
			debug('Adding remove click action.');
			addRemoveClickAction(link);
		});
	}
	
	
	function getInitCartValues() {
		// Flush cached data.
		topCartContainer = undefined;
		topCartContent = undefined;
		topCartLink = undefined;
		minCart = undefined;
		cartHeader = undefined;
		subTotalBlock = undefined;
		actionsBlock = undefined;
		emptyContentBlock = undefined;
		
		var topcartContainer = getTopcartContainerElement();
		initTopPosition = topcartContainer.cumulativeOffset().top;
		initLeftPosition = (topcartContainer.cumulativeOffset().left - (getTopcartContentElement().getWidth() - topcartContainer.getWidth() + parseInt(topcartContainer.getStyle('padding-left').replace('px', ''))));
		debug('initTopPosition: '+initTopPosition+' - initLeftPosition: '+initLeftPosition);
	}
	
	function getCartItems(flushCache) {
		if(cartItems == undefined || flushCache) cartItems = new CartItemCollection(getTopcartElement());
		
		return cartItems;
	}
	
	function showTopcartElements() {
		getEmptyContentElement().hide();
		
		getTopcartElement().show();
		getSubtotalElement().show();
		getActionsElement().show();
	}
	
	function showTopcartEmpty() {
		getTopcartElement().hide();
		getSubtotalElement().hide();
		getActionsElement().hide();
		
		getEmptyContentElement().show();
	}
	
	function addEditClickAction(link) {
		debug('addEditClickAction');
		link.observe('click', function (e) {
			e.stop();
			if(link.hasClassName('suspended')) {
				debug('Suspended link.');
				return;
			}
			
			location.href = link.getAttribute('href');
		});
	}
	function addRemoveClickAction(link) {
		debug('addRemoveClickAction');
		link.observe('click', function (e) {
			e.stop();
			
			if(link.hasClassName('suspended')) {
				debug('Suspended link.');
				return;
			}
			
			var liItem = link.up('li.item');
			var itemId = getItemId(liItem);
			var productId = liItem.getAttribute('data-productid');
			var _cartItems = getCartItems();
			debug('deleting product: Item #'+itemId+' - product #'+productId);
			
			// If is cart environment, we need to delete the item in Shopping Cart.
			if(environment == "cart") {
				debug('deleting product: cart environment');
				var removeButton = $(cartTableId).down('> tbody tr[data-id="'+itemId+'"] .btn-remove.btn-remove2');
				removeButton.click();
			} else {
				var deleted = _cartItems.deleteItem(itemId);
				if(! deleted) {
					debug('addRemoveClickAction. Error deleting item.', 1);
				} else {
					// Send changes to ajax.
					updateRemote('delete', {id: itemId});
				
				}
			}
		});
	}
	
	function getItemId(liItem) {
		var id = liItem.getAttribute('id');
		return id.substr(id.indexOf('-')+1);
	}
	
	
	function _showCart(timePeriod) {
		debug('showCart (timePeriod: '+timePeriod+').');
		
		if(! topCartExpanded) {
			_relocateTopcart();
			
			new Effect.SlideDown(getTopcartContentElement(), {
				duration: 0.5,
				beforeStart: function(effect) {topCartExpanded = true;}
			});
		}
			
		// Reset timeout if exists.
		if (topCartInterval !== null) {
			clearTimeout(topCartInterval);
			topCartInterval = null;
		}
		// Set new timeout.
		if(timePeriod != undefined) {
			topCartInterval = setTimeout(_hideCart, timePeriod * 1000);
		}
	}
	
	function _hideCart() {
		debug('_hideCart: '+topCartExpanded);
        if (topCartExpanded) {
            new Effect.SlideUp(getTopcartContentElement(), {
				duration: 0.5,
				afterFinish: function(effect) {topCartExpanded = false;},
			});
        }
	
		if (topCartInterval !== null) {
			clearTimeout(topCartInterval);
			topCartInterval = null;
		}
	}
	
	function _relocateTopcart() {
		debug('_relocateTopcart');
		var _topCartContent = getTopcartContentElement();
		
		_topCartContent.setStyle({'position': 'fixed', 'left': initLeftPosition+'px'});
		
		var pageOffset = window.pageYOffset;
		if(pageOffset > 40) {
			// _topCartContent.setStyle({'top': (pageOffset - 40)+'px'});
			_topCartContent.setStyle({'top': '20px'});
		} else {
			_topCartContent.setStyle({'top': (initTopPosition + 20) + 'px'});
		}
	}
	
	function updateTopcart(productData, qty, callback) {
		debug('updateTopcart');
		var id = productData.id;
		var productId = productData.productId;
		var _cartItems = getCartItems();
		var itemId = _cartItems.getItemIdByProduct(productData);
		debug('itemId: '+itemId);
		
		// Set parameters.
		var parameters = {id: itemId, productId: productId, qty: qty, options: productData.options};
		if(productData.giftcard != undefined) {
			for(giftcardOption in productData.giftcard) {
				parameters[giftcardOption] = productData.giftcard[giftcardOption];
			}
		}
		
		if(! itemId) {
			var added = _cartItems.insertItem(productData, qty);
			if(! added) {
				debug('Error adding item.', 1);
			} else {
				// Send changes to ajax.
				updateRemote('insert', parameters, callback);
			}
			
			return added;
		} else {
			var updated = _cartItems.updateItem(itemId, qty);
			if(! updated) {
				debug('Error updating item.', 1);
			} else {
				// Send changes to ajax.
				updateRemote('insert', parameters, callback);
			}
			
			return updated;
		}
	}
	
	
	function updateRemote(action, parameters, callback) {
		debug('updateRemote: '+controller+action+' - parameters: '+JSON.stringify(parameters));
		var id = parameters.id;
		var qty = parameters.qty;
		
		// Check options.
		if(parameters.options != undefined) {
			for(var i in parameters.options) {
				if (! parameters.options.hasOwnProperty(i)) continue;
				parameters['options['+i+']'] = parameters.options[i].value;
			}
		}
		
		new Ajax.Request(controller+action+'/', {
			method: 'get',
			parameters: parameters,
			onCreate: function() {
				suspendAddToCart();
			},
			onSuccess: function(response) {
				debug('updateRemote.onSuccess');
				if(response.responseJSON != undefined) {
					if(response.responseJSON.success == 1) {
						var data = response.responseJSON.data;

						if(data != undefined) {
							// Check if need to redirect.
							if(data.redirectUrl != undefined) {
								location.href = data.redirectUrl;
								return;
							}
							
							var _cartItems = getCartItems();
							
							// Check if cart is empty or distinct to real cart.
							debug('itemsCount: '+data.itemsCount);
							if(data.itemsCount != undefined) {
								if(data.itemsCount == 0) {
									_cartItems.emptyCart();
									if(environment == "cart") {
										debug('updateRemote - cart environment - empty cart - reload.');
										location.reload();
									}
								} else {
									_cartItems.mergeCart(data);
								}
							}
						}
						
						// Execute callback function.
						debug('callback: '+callback);
						if(callback != undefined) callback(data);
						
						return;
					}
					
					// If some wrong, back to previous state.
					goBackRemote(id, qty, response.responseJSON.msg);
					return;
				}
				
				// If some wrong, back to previous state.
				if(response.status != 0) goBackRemote(id, qty, "Fatal server error.");
				return;
			},
			onFailure: function(response) {
				debug('onFailure');
				goBackRemote(id, qty, "Fatal server error.");
			},
			onComplete: function(response) {
				activeAddToCart();
			},
		});
	}
	function suspendAddToCart() {
		$$('.btn-cart, .btn-edit, .btn-remove, .btn-update, .btn-empty, .btn-free-cart, .btn-free-remove, .btn-wishlist-cart, .btn-wishlist-remove, .link-wishlist, .product-edit a, .favorites a').each(function(button) {
			button.addClassName('suspended');
		});
	}
	function activeAddToCart() {
		$$('.btn-cart, .btn-edit, .btn-remove, .btn-update, .btn-empty, .btn-free-cart, .btn-free-remove, .btn-wishlist-cart, .btn-wishlist-remove, .link-wishlist, .product-edit a, .favorites a').each(function(button) {
			button.removeClassName('suspended');
		});
	}
	
	function goBackRemote(id, qty, msg) {
		if(id == false) id = "new";
		
		debug('goBackRemote - id: '+id);
		if(id != undefined) {
			// Update topcart.
			var _cartItems = getCartItems();
			var deleted = _cartItems.deleteItem(id, qty);
			if(! deleted) {
				debug('goBackRemote. Error deleting item.', 1);
			}
			
			// Update Shopping cart.
			if(environment == "cart") {
				debug('goBackRemote: Update Cart.');
				var cart = new cartFunctions();
				var tr = cart.getRowById(id);
				cart.removeItemFromShopping(tr);
			}
		}
		
		
		debug('msg: '+msg);
		printErrorMsg(msg);
	}
	
	
	function isInt(value) {
		return !isNaN(value) && parseInt(value) == value;
	}
	
	function moneyFormat(value) {
		// Check if price already has format.
		if(isNaN(value) && value.indexOf('<span class="price">') != -1) return value;
		
		var fixedValue = parseFloat(value).toFixed(2);
		
		if(fixedValue < 0) return fixedValue.replace('-', '-'+publicCart.currencySymbol);
		
		return publicCart.currencySymbol+fixedValue;
	}
	
	function printErrorMsg(msg) {
		// Load dialog plugin.
		if(! dialogLoaded) {
			Dialogs.load();
			dialogLoaded = true;
		}
		
		Dialogs.alert(msg);
	}
	
	function printSuccessMsg(msg, container) {
		debug('printSuccessMsg: '+msg);
		var template = '<ul class="messages"><li class="success-msg"><ul><li><span>#{msg}</span></li></ul></li></ul>';
		var msgTemplate = new Template(template);
		var newMessage = msgTemplate.evaluate({msg: msg});
		
		if(container != undefined) {
			container.insert({top: newMessage});
		}
		
		debug('printSuccessMsg - end');
	}
	
	
	function getOptionsFromItem(itemOptions) {
		if(itemOptions.length > 0) {
			var options = {};
			for(var i in itemOptions) {
				if (! itemOptions.hasOwnProperty(i)) continue;
				
				options[itemOptions[i].getAttribute('data-id')] = {
					label: itemOptions[i].getAttribute('data-label'),
					value: itemOptions[i].getAttribute('data-value'),
					text: itemOptions[i].getAttribute('data-text'),
				};
			}
		}
		
		return options;
	}
	// *** Private functions *** //
	
	
	// *** Cart Item Collection Object *** //
	var CartItemCollection = function (minCartObj) {
		var that = this;
		this.items = {};
		this.itemsCount = 0;
		this.itemsQty = 0;
		
		// Constructor.
		debug('Cart Items length: '+minCartObj.childElements().length);
		if(minCartObj != undefined && minCartObj.down('li') != undefined) {
			minCartObj.childElements().each(function(liElement, index) {
				// Set last.
				if(index == 0) liElement.addClassName('last');
				
				var _cartItem = new CartItem();
				_cartItem.parseItem(liElement);
				that.items[_cartItem.itemData.id] = _cartItem.itemData;
				that.itemsCount++;
				that.itemsQty += parseInt(_cartItem.itemData.qty);
			});
		}
		
		
		this.insertItem = function(itemData, qty) {
			debug('CartItemCollection.insertItem');
			var _cartItem = new CartItem();
			_cartItem.itemData = itemData;
			_cartItem.itemData.qty = qty;
			var inserted = _cartItem.insertItemToCart();
			
			if(inserted) {
				this.items[_cartItem.itemData.id] = _cartItem.itemData;
				this.itemsCount++;
				this.itemsQty += parseInt(_cartItem.itemData.qty);
				
				updateSubTotal();
				updateQty();
				return true;
			} else {
				return false;
			}
		}
		
		this.updateItem = function(id, qty) {
			debug('updateItem');
			if(this.items[id] == undefined) {
				return false;
			}
			
			var _cartItem = new CartItem();
			_cartItem.itemData = this.items[id];
			var updated = _cartItem.updateItemCart(qty);
			
			if(updated) {
				this.items[_cartItem.itemData.id] = _cartItem.itemData;
				this.itemsQty += parseInt(qty);
				
				updateSubTotal();
				updateQty();
				return true;
			} else {
				return false;
			}
		}
		
		this.deleteItem = function(id, qty) {
			debug('CartItemCollection.deleteItem');
			
			// Check item.
			if(this.items[id] == undefined) {
				return false;
			}
			
			// IF no qty, is delete.
			if(qty == undefined) qty = this.items[id].qty;
			
			var _cartItem = new CartItem();
			debug('CartItemCollection.deleteItem: id = '+id+'.');
			_cartItem.itemData = this.items[id];
			debug('CartItemCollection.deleteItem: '+this.items[id].qty+' - '+qty+' > 0');
			if(this.items[id].qty - qty > 0) {
				// Update quantity.
				var updated = _cartItem.updateItemCart(-qty);
				if(updated) {
					this.items[id] = _cartItem.itemData;
					this.itemsQty -= parseInt(qty);
					
					updateSubTotal();
					updateQty();
					return true;
				} else {
					return false;
				}
			} else {
				// Delete item from cart.
				var deleted = _cartItem.deleteItemCart();
				if(deleted) {
					this.itemsQty -= parseInt(this.items[id].qty);
					this.items[id] = undefined;
					this.itemsCount--;
					
					updateSubTotal();
					updateQty();
					return true;
				} else {
					return false;
				}
			}
		}
		
		this.updateConfigureUrl = function(id, url) {
			debug('updateConfigureUrl');
			if(this.items[id] == undefined) {
				return false;
			}
			
			var _cartItem = new CartItem();
			_cartItem.itemData = this.items[id];
			var updated = _cartItem.updateItemConfigureUrlCart(url);
		}
		
		this.getItemIdByProduct = function(product) {
			for(id in this.items) {
				if(this.items[id] != undefined && this.items[id].productId == product.productId) {
					// Check options.
					var optionChecked = false;
					if(this.items[id].options != undefined || product.options != undefined) {
						for(attributeId in this.items[id].options) {
							if(this.items[id].options[attributeId] != undefined && this.items[id].options[attributeId].value == product.options[attributeId].value) {
								optionChecked = true;
							}
						}
					} else {
						optionChecked = true;
					}
					
					if(optionChecked) return id;
				}
			}
			
			return false;
		}
		
		this.removeEditLink = function(id) {
			debug('CartItemCollection.removeEditLink');
			if(this.items[id] == undefined) {
				return false;
			}
			
			var _cartItem = new CartItem();
			_cartItem.itemData = this.items[id];
			var updated = _cartItem.updateItemRemoveEdit();
		}
		
		this.emptyCart = function() {
			debug('CartItemCollection.emptyCart');
			for(var id in this.items) {
				this.deleteItem(id);
			}
		}
		
		this.mergeCart = function(cartData) {
			debug('CartItemCollection.mergeCart');
			var cartDataItems = cartData.items;
			
			// Keep the same image as html cart.
			for(var id in cartDataItems) {
				cartDataItems[id].img = loadPreviousImage(cartDataItems[id], this.items);
			}

			// Check if need delete items.
			var thersNewItem = false;
			for(var id in this.items) {
				if(cartDataItems[id] == undefined) {
					if(id == "new") thersNewItem = true;
					
					this.deleteItem(id);
					
					// Update Shopping cart.
					if(environment == "cart" && id != "new") {
						debug('CartItemCollection.mergeCart - Update cart environment');
						if(cart == undefined) var cart = new cartFunctions();
						var tr = cart.getRowById(id);
						
						if(tr != undefined) cart.removeItemFromShopping(tr);
					}
				}
			}
			
			// Add new items and edit others.
			for(var id in cartDataItems) {
				if(this.items[id] != undefined) {
					this.deleteItem(id);
					var isNewItem = false;
				} else {
					var isNewItem = true;
				}
				
				this.insertItem(cartDataItems[id], cartDataItems[id].qty);
				if(cartDataItems[id].isNotEditable) {
					this.removeEditLink(id);
				}
				
				// Add new item in cart environment.
				if(isNewItem && environment == "cart" && ! thersNewItem) {
					debug('CartItemCollection.mergeCart - Add new cart environment - thersNewItem: '+thersNewItem);
					if(cart == undefined) var cart = new cartFunctions();
					
					cart.addNewProduct(cartDataItems[id], cartData, cartDataItems[id].qty);
				}
			}
			
			
			function loadPreviousImage(newData, oldItems) {
				img = newData.img;
				var newImg = getImageFile(img);
				
				for(var id in oldItems) {
					if(oldItems[id] == undefined) continue;
					
					itemImg = getImageFile(oldItems[id].img);
					if(itemImg == newImg) {
						var img = oldItems[id].img;
						break;
					}
				}
				
				return img;
				
				function getImageFile(img) {
					var regex = /<img.*?src="(.*?)"/;
					var imgSrc = regex.exec(img);
					
					if(imgSrc[1] == undefined) return '';
					
					return imgSrc[1].substr(imgSrc[1].lastIndexOf('/')+1);
				}
			}
		}
		
		this.freeSampleCount = function() {
			var count = 0;
			for(id in this.items) {
				if(this.items[id] != undefined && this.items[id].freesample == 1) count++;
			}
			
			return count;
		}
		
		function updateSubTotal() {
			debug('CartItemCollection.updateSubTotal');
			
			var subTotal = 0;
			if(that.itemsCount > 0) {
				for(var id in that.items) {
					if (that.items.hasOwnProperty(id) && that.items[id] != undefined) {
						subTotal += (parseFloat(that.items[id].price) * parseInt(that.items[id].qty));
					}
				}
			} else {
				showTopcartEmpty();
			}
			
			getSubtotalElement().down('.price').innerHTML = moneyFormat(subTotal);
		}
		function updateQty() {
			debug('CartItemCollection.updateQty');
			var _cartHeader = getCartHeader();
			_cartHeader.down('span').innerHTML = that.itemsQty;
			
			// Update generic qty.
			if($$('.cart-qty')[0] != undefined) {
				$$('.cart-qty')[0].innerHTML = that.itemsQty;
			}
			
			// Update top cart.
			var qtyTopCart = $$('#headerbag .top-link-cart span');
			if(qtyTopCart != undefined && qtyTopCart[0] != undefined) {
				qtyTopCart[0].innerHTML = that.itemsQty;
			}
			
			// Mobile top cart.
			var mobileTopCart = $$('.menu-wrapper .top-link-cart.ui-link');
			if(mobileTopCart != undefined && mobileTopCart[0] != undefined) {
				mobileTopCart[0].innerHTML = that.itemsQty;
			}
		}
	}
	// *** Cart Item Collection Object *** //
	
	
	// *** Cart Item Object *** //
	var CartItem = function () {
		this.itemData = {};
	}
	
	CartItem.prototype.parseItem = function (li) {
		this.itemData = {
			'id': function(liItem) {
				return getItemId(liItem);
			}(li),
			'productId': function(liItem) {
				var productId = liItem.getAttribute('data-productid');
				return productId;
			}(li),
			'img': function(liItem) {
				return liItem.down('.product-image').innerHTML;
			}(li),
			'url': function(liItem) {
				var anchor = liItem.down('.product-name a');
				
				if(anchor != undefined) return anchor.getAttribute('href');
				return "";
			}(li),
			'name': function(liItem) {
				var anchor = liItem.down('.product-name a');
				
				if(anchor != undefined) return anchor.innerHTML;
				return liItem.down('.product-name').innerHTML;
			}(li),
			'qty': function(liItem) {
				return parseInt(liItem.down('.cart-item-qty').innerHTML);
			}(li),
			'price': function(liItem) {
				return parseFloat(liItem.down('.price').innerHTML.replace(publicCart.currencySymbol, ''));
			}(li),
			'freesample': function(liItem) {
				var freesample = liItem.getAttribute('data-freesample');
				return freesample;
			}(li),
			'options': function(liItem) {
				var options = getOptionsFromItem(liItem.select('.item-options-data'));
				
				return options;
			}(li),
		}
	}
	
	CartItem.prototype.insertItemToCart = function () {
		debug('CartItem.insertItemToCart');
		// Check item data.
		if(this.itemData == undefined) {
			debug('No item data to insert.', 1);
			return;
		}
		
		// Set price formated to print.
		this.itemData.priceFormated = moneyFormat(this.itemData.price);
		
		// Set id if is undefined.
		if(this.itemData.id == undefined) this.itemData.id = "new";
		if(this.itemData.editUrl == undefined) this.itemData.editUrl = "#";
		
		// Add option list.
		if(this.itemData.options != undefined) {
			debug('CartItem.insertItemToCart - option list');
			var newOptionTemplate = new Template(getOptionTemplate());
			
			var options = this.itemData.options;
			
			var newOptionHTML = "";
			debug(options);
			for(attributeId in options) {
				newOptionHTML += newOptionTemplate.evaluate(options[attributeId]);
			}
			
			var newOptionListTemplate = new Template(getOptionListTemplate());
			var optionList = newOptionListTemplate.evaluate({optionsList: newOptionHTML});
			this.itemData.optionList = optionList;
		}
		
		// Evalute remplate and get HTML block.
		var newItemTemplate = new Template(getTopcartTemplate());
		var newItemHTML = newItemTemplate.evaluate(this.itemData);
		
		// Add new row to cart.
		var topcart = getTopcartElement();
		if(topcart == null) {
			debug('Unable to get topcart.', 1);
			return false;
		}
		
		topcart.insert({top: newItemHTML});
		
		// Get new item.
		var newAddedItem = topcart.down('#ajaxcart-'+this.itemData.id);
		
		// Set as last.
		var lastItem = topcart.down('.item.last');
		if(lastItem != undefined) lastItem.removeClassName('last');
		newAddedItem.addClassName('last');
		
		// Add click action to edit.
		addEditClickAction(newAddedItem.down('.btn-edit'));
		// Add click action to delete.
		addRemoveClickAction(newAddedItem.down('.btn-remove'));
		
		
		// Add functions to product configurable help.
		truncateOptions(); // This is an external function (/js/varien/js.js)
		
		// Show cart.
		showTopcartElements();
		
		return true;
		
		
		function getTopcartTemplate() {
			var template = '<li class="item" id="ajaxcart-#{id}" data-productid="#{productId}" data-freesample="#{freesample}">';
			template += '<a href="#{url}" class="product-image">#{img}</a>';
			template += '<div class="product-details">';
			template += '<p class="product-name"><a href="#{url}">#{name}</a></p>';
			template += '<table cellpadding="0"><tr><th>Price</th><td>#{priceFormated}</td></tr><tr><th>Qty</th><td class="cart-item-qty">#{qty}</td></tr></table>';
			template += '#{optionList}';
			template += '<a href="#{editUrl}" title="Edit item" class="btn-edit suspended">Edit item</a> <span>|</span> ';
			template += '<a href="#" title="Remove item" class="btn-remove suspended">Remove item</a>';
			template += '</div>';
			template += '</li>';
			
			return template;
		}
		
		function getOptionListTemplate() {
			var template = '<div class="truncated">';
			template += '<div class="truncated_full_value">';
			template += '<div class="item-options">';
			template += '<p>Options Details</p>';
			template += '<dl>#{optionsList}</dl>';
			template += '</div>';
			template += '</div>';
			template += '<a href="#" onclick="return false;" class="details">View Details</a>';
			template += '</div>';
			
			return template;
		}
		
		function getOptionTemplate() {
			var template = '<dt>#{label}</dt>';
			template += '<dd>#{text}</dd>';
			
			return template;
		}
	}
	
	CartItem.prototype.updateItemCart = function (qty) {
		debug('updateItemCart');
		// Check item data.
		if(this.itemData == undefined) {
			debug('No item data to update.', 1);
			return false;
		}
		
		// Check quantity.
		if(! isInt(qty)) {
			debug('No item qty to update.', 1);
			return false;
		}
		
		// Add new quantity.
		this.itemData.qty += parseInt(qty);
		
		var row = getCartRow(this.itemData.id);
		if(row == undefined) {
			debug('No item loaded in cart.', 1);
			return false;
		}
		
		row.down('.cart-item-qty').innerHTML = this.itemData.qty;
		showTopcartElements();
		
		return true;
		
		
		function getCartRow(id) {
			return $('ajaxcart-'+id);
		}
	}
	
	CartItem.prototype.deleteItemCart = function () {
		debug('CartItem.deleteItemCart');
		// Check item data.
		if(this.itemData == undefined) {
			debug('No item data to delete.', 1);
			return false;
		}
		
		var row = getCartRow(this.itemData.id);
		if(row == undefined) {
			debug('No item loaded in cart.', 1);
			return false;
		}
		
		row.remove();
		
		// Change last.
		var lastItem = getTopcartElement().down('.item');
		if(lastItem != undefined) lastItem.addClassName('last');
		
		return true;
		
		
		function getCartRow(id) {
			debug('CartItem.getCartRow: '+id);
			return $('ajaxcart-'+id);
		}
	}
	
	CartItem.prototype.updateItemConfigureUrlCart = function (url) {
		debug('updateItemConfigureUrlCart');
		
		// Check item data.
		if(this.itemData == undefined) {
			debug('No item data to update.', 1);
			return false;
		}
		
		var row = getCartRow(this.itemData.id);
		if(row == undefined) {
			debug('No item loaded in cart.', 1);
			return false;
		}
		
		row.down('.btn-edit').setAttribute('href', url);
		return true;
		
		
		function getCartRow(id) {
			return $('ajaxcart-'+id);
		}
	}
	
	CartItem.prototype.updateItemRemoveEdit = function (url) {
		debug('updateItemRemoveEdit');
		
		// Check item data.
		if(this.itemData == undefined) {
			debug('No item data to update.', 1);
			return false;
		}
		
		// Get row of item.
		var row = getCartRow(this.itemData.id);
		if(row == undefined) {
			debug('No item loaded in cart.', 1);
			return false;
		}
		
		// Remove Edit and pipe.
		var btnEdit = row.down('.btn-edit');
		btnEdit.next('span').remove();
		btnEdit.remove();
		return true;
		
		
		function getCartRow(id) {
			return $('ajaxcart-'+id);
		}
	}
	// *** Cart Item Object *** //
	
	
	// *** List Template Object *** //
	var listFunctions = function () {
		
		this.setListActions = function () {
			var qty = 1;
			
			$$('.btn-cart').each(function(button) {
				// Check if is button.
				if(button.tagName.toLowerCase() != "button") {
					button = button.down('button');
				}
				
				// Get product Id.
				var productId = getProductId(button);
				if(! productId) {
					debug('Unable to get product id.', 1);
					return;
				}
				
				// Remove prev onclick action.
				button.writeAttribute('onclick');
				
				// Add click action.
				button.observe('click', function (e) {
					e.stop();
					debug('Add to cart click.');
					
					// Check suspended button.
					var suspendedCheck = button.hasClassName('suspended');
					if(! suspendedCheck && button.up('.btn-cart') != undefined) suspendedCheck = button.up('.btn-cart').hasClassName('suspended');
					if(suspendedCheck) {
						debug('Suspended button.');
						return;
					}
					
					// Remove stick-it.
					getTopcartContentElement().removeClassName('stick-it');
					
					// Relocate topcart.
					_relocateTopcart();
					
					// Add / Edit item.
					var itemData = getListProductData(productId, button);
					if(itemData.qty != undefined) var qty = itemData.qty;
					debug(itemData);
					updateTopcart(itemData, qty);
					
					// Show cart.
					_showCart(7);
				});
			});
			
			// Remove action to qty box.
			$$('select.qty').each(function(dropDown) {
				dropDown.writeAttribute('onchange');
			});
		
			// Add action to wishlist just if the user is logged in.
			debug('userLoggedIn: '+publicCart.userLoggedIn);
			if(publicCart.userLoggedIn) {
				$$('.link-wishlist').each(function(link) {
					// Get product Id.
					var productId = getProductId(link);
					if(! productId) {
						debug('Wishlist - Unable to get product id.', 1);
						return;
					}
					
					// Add click action.
					link.observe('click', function (e) {
						e.stop();
						debug('listFunctions. Add to wishlist click.');
						
						if(link.hasClassName('suspended')) {
							debug('Suspended button.');
							return;
						}
						
						// Add item to wishlist.
						updateRemote('addToWishlist', {'id': productId}, function(data) {
							updateWishlist(data);
						});
					});
				});
			
				loadWishlistSidebarActions();
			}
		}
		
		
		function getProductId(item) {
			var itemType = item.tagName.toLowerCase();
			
			if(itemType == "div") {
				var newItem = item.down('button');
				itemType = "button";
				if(newItem == undefined) {
					var newItem = item.down('a');
					itemType = "a";
				}
				
				item = newItem;
			}
			
			if(item == undefined) {
				debug("Button undefined (1114)", 1);
				return false;
			}
			
			var onclickAttribute = item.readAttribute((itemType == "button" ? 'onclick' : 'href'));
			if(onclickAttribute == undefined) {
				debug("Onclick undefined (1120)", 1);
				return false;
			}
			
			var productId = onclickAttribute.substr((onclickAttribute.indexOf('/product/') + 9));
			productId = parseInt(productId.substr(0, productId.indexOf('/')));
			
			return productId;
		}
		
		function getListProductData(productId, button) {
			var itemData = {};
			itemData.productId = productId;
			var currentItem = button.up('.item');
			
			if(currentItem == undefined) {
				debug("Product Data - item undefined", 1);
				return;
			}
			
			var img = currentItem.down('.product-image');
			if(img == undefined) {
				var imgHTML = currentItem.down('img').outerHTML;
			} else {
				var imgHTML = img.innerHTML;
			}
			
			itemData.img = imgHTML;
			itemData.name = currentItem.down('.product-name a').innerHTML;
			itemData.price = getPriceElement(currentItem).innerHTML.stripTags().replace(publicCart.currencySymbol, '');
			itemData.url = currentItem.down('.product-name a').readAttribute('href');
			
			// Check if qty exists, if not set qty to 1.
			var qty = currentItem.down('.qty');
			if(qty == undefined || isNaN(qty.value)) {
				itemData.qty = 1;
			} else {
				itemData.qty = qty.value;
			}
			
			
			return itemData;
		}
		
		function getPriceElement(item) {
			var priceElement = item.down('.regular-price .price')
			
			// If price is undefined, then is an special price.
			if(priceElement == undefined) {
				priceElement = item.down('.special-price .price');
			}
			
			return priceElement;
		}
	
		function updateWishlist(data) {
			debug('listFunctions.updateWishlist');
			
			// Update wishlist Link.
			var _wishlistLink = getWishlistLinkElement();
			_wishlistLink.innerHTML = data.linkTitle;
			_wishlistLink.writeAttribute('title', data.linkTitle);
			
			// Update Wishlist Sidebar.
			var _wishlistSidebar = getWishlistSidebarElement();
			new Ajax.Request(controller+'wishlistSidebar/', {
				method: 'get',
				onCreate: function() {
					_wishlistSidebar.innerHTML = "";
					_wishlistSidebar.addClassName('loading');
				},
				onSuccess: function(response) {
					loadWishlistSidebar(response.responseText);
				},
				onFailure: function(response) {
					debug('updateWishlist.onFailure');
					printErrorMsg("Fatal server error.");
				},
				onComplete: function(response) {
					_wishlistSidebar.removeClassName('loading');
				},
			});
			
		}
		
		function loadWishlistSidebar(content) {
			debug('listFunctions.loadWishlistSidebar');
			var _wishlistSidebar = getWishlistSidebarElement();
			
			_wishlistSidebar.replace(content);
			setWishlistSidebarElement();
			loadWishlistSidebarActions();
		}
		
		function loadWishlistSidebarActions() {
			$$('#wishlist-sidebar .btn-remove').each(function(link) {
				var itemId = link.getAttribute('data-itemid');
				var id = link.getAttribute('data-productid');
				
				// Add click action.
				link.observe('click', function (e) {
					e.stop();
					debug('listFunctions. remove to wishlist click.');
					
					if(link.hasClassName('suspended')) {
						debug('Suspended button.');
						return;
					}
					
					// Remove item to wishlist.
					updateRemote('removeToWishlist', {'id': id, 'itemId': itemId}, function(data) {
						updateWishlist(data);
					});
				});
			});
			
			$$('#wishlist-sidebar .link-cart').each(function(link) {
				var itemId = link.getAttribute('data-itemid');
				var _cartItems = getCartItems();
				var itemData = getWishlistProductData(link);
				var productId = itemData.id;
				
				// Add click action.
				link.observe('click', function (e) {
					e.stop();
					debug('listFunctions. add to cart from wishlist click.');
					
					if(link.hasClassName('suspended')) {
						debug('Suspended button.');
						return;
					}
					
					// Relocate topcart.
					_relocateTopcart();
					
					// Add / Edit item.
					debug(itemData);
					if(_cartItems.items[productId] != undefined) {
						var updated = _cartItems.updateItem(productId, itemData.qty);
						if(! updated) {
							debug('Error updating item.', 1);
							return;
						}
					} else {
						var added = _cartItems.insertItem(itemData, itemData.qty);
						if(! added) {
							debug('Error adding item.', 1);
							return;
						}
					}
					
					// Show cart.
					_showCart(7);
					
					// Add item to cart from wishlist.
					updateRemote('addWishlistToCart', {'id': productId, 'itemId': itemId}, function(data) {
						updateWishlist(data);
					});
				});
			});
		}
		
		function getWishlistProductData(link) {
			var itemData = {};
			
			var currentItem = link.up('.item');
			itemData.productId = link.readAttribute('data-productid');
			itemData.img = currentItem.down('.product-image').innerHTML;
			itemData.name = currentItem.down('.product-name a').innerHTML;
			itemData.price = getPriceElement(currentItem).innerHTML.stripTags().replace(publicCart.currencySymbol, '');
			itemData.url = currentItem.down('.product-image').readAttribute('href');
			itemData.qty = parseInt(link.readAttribute('data-qty'));
			
			return itemData;
		}
	}
	// *** List Template Object *** //
	
	
	// *** View Template Object *** //
	var viewFunctions = function () {
		
		this.setViewActions = function () {
			$$('.button.btn-cart').each(function(button) {
				debug('setViewActions. .button.btn-cart button');
				// Get product Id.
				var productId = getProductId(button);
				debug('productId: '+productId+'.');
				if(! productId) {
					debug('Unable to get product id.', 1);
					return;
				}
				
				// Remove prev onclick action.
				button.writeAttribute('onclick');
				
				// Add click action.
				debug('Adding click action.');
				button.observe('click', function (e) {
					debug('Add to cart click.');
					
					// Get Quantity.
					var qty = getProductQty(button);
					
					// Check if button is suspended.
					if(button.hasClassName('suspended')) {
						debug('Suspended button.');
						return;
					}
					
					// If can, validate.
					var validated = productAddToCartForm.validator.validate();
					if(! validated) {
						debug('Validation error.');
						return;
					}
					
					// Relocate topcart.
					_relocateTopcart();
					
					// Add / Edit item.
					var itemData = getViewProductData(productId, button);
					debug(itemData);
					updateTopcart(itemData, qty);
					
					// Show cart.
					_showCart(7);
				});
			});
			
			// Add action to wishlist just if the user is logged in.
			debug('setViewActions. userLoggedIn: '+publicCart.userLoggedIn);
			if(publicCart.userLoggedIn) {
				var addToWishlistButton = $$('.favorites a')[0];
				// if(addToWishlistButton == undefined) addToWishlistButton = $$('.favorites img')[0];
				
				debug('addToWishlistButton: '+addToWishlistButton);
				if(addToWishlistButton != undefined) {
					addToWishlistButton.writeAttribute('onclick');
					
					addToWishlistButton.observe('click', function (e) {
						e.stop();
						debug('viewFunctions. Add to wishlist click.');
						
						// Check if button is suspended.
						if(this.hasClassName('suspended')) {
							debug('Suspended button.');
							return;
						}
						
						// get product id.
						var productId = getProductId(this);
						
						// Get Quantity.
						var qty = getProductQty(addToWishlistButton);
						
						// get item data.
						var itemData = getViewProductData(productId, addToWishlistButton);
						
						// Add item to wishlist.
						updateRemote('addToWishlist', {'productId': productId, 'qty': qty, 'options': itemData.options}, function(data) {
							debug('viewFunctions. updateRemote. addToWishlist.');
							
							// Update wishlist Link.
							var _wishlistLink = getWishlistLinkElement();
							_wishlistLink.innerHTML = data.linkTitle;
							_wishlistLink.writeAttribute('title', data.linkTitle);
							
							printSuccessMsg(data.successMessage, $$('.col-main')[0]);
							debug('viewFunctions. updateRemote. finished.');
						});
					});
				}
			}
		}
		
		
		function getProductId(button) {
			console.log('getProductId');
			var actionAttribute = button.up('form').readAttribute('action');
			var productId = actionAttribute.substr((actionAttribute.indexOf('/product/') + 9));
			productId = parseInt(productId.substr(0, productId.indexOf('/')));
			
			// Get product id for configure page.
			if(isNaN(productId)) {
				productId = parseInt(button.up('form').down('input[name="product"]').value);
			}
			
			return productId;
		}
		
		function getProductQty(button) {
			var qtyInput = button.up('form').down('#qty');
			
			return parseInt(qtyInput.value);
		}
		
		function getViewProductData(productId, button) {
			var itemData = {};
			itemData.productId = productId;
			
			var currentItem = button.up('form');
			itemData.img = getViewImgItem(currentItem);
			itemData.name = getProductName(currentItem);
			itemData.price = getViewPrice(currentItem);
			itemData.url = location.href;
			
			// Check for configurable proudcts.
			var productOption = currentItem.down('.product-options');
			if(productOption != undefined) {
				var options = currentItem.select('[name*="super_attribute["]');
				if(options.length > 0) {
					debug('Product configurable.');
					itemData.options = {};
					for(var i in options) {
						if (! options.hasOwnProperty(i)) continue;
						
						var _option = options[i];
						var name = _option.getAttribute('name');
						var attributeId = name.substr(name.indexOf('['));
						attributeId = attributeId.substr(1, (attributeId.indexOf(']') - 1));
						
						itemData.options[attributeId] = {
							label: _option.up('dl').down('label').innerHTML.replace('<em>*</em>', '').stripTags(),
							value: _option.value,
							text: _option.options[_option.selectedIndex].innerHTML
						};
					}
				}
			}
			
			// Check for giftcard fields.
			if($$('.giftcard-form').length > 0) {
				var giftCardForm = $$('.giftcard-form')[0];
				
				itemData.giftcard = {};
				
				var fields = giftCardForm.select('input,select,textarea');
				for(i in fields) {
					if (! fields.hasOwnProperty(i)) continue;
					
					element = fields[i];
					itemData.giftcard[element.name] = element.value;
				}
				
				// Check for custom gift card amount.
				if((itemData.giftcard['giftcard_amount'] == undefined || itemData.giftcard['giftcard_amount'] == "") && ! isNaN(itemData.giftcard['custom_giftcard_amount'])) {
					itemData.giftcard['giftcard_amount'] = "custom";
				}
			}
			
			
			return itemData;
		}
		
		function getViewImgItem(item) {
			var imgItem = item.down('.more-views li img');
			
			if(imgItem == undefined) {
				var imgItem = item.down('.product-img-box img');
			}
			
			if(imgItem == undefined) return '';
			
			return imgItem.outerHTML;
		}
		
		function getProductName(item) {
			var productName = item.down('.product-name h1');
			if(productName != undefined) return productName.innerHTML;
			
			// Get product name from breadcrumbs.
			productName = $$('.breadcrumbs .product')[0].innerHTML;
			return productName.stripTags();
		}
		
		function getViewPrice(item) {
			debug('viewFunctions.getViewPriceElement');
			
			var priceElement = item.down('.regular-price .price')
			
			// If price is undefined, then is an special price.
			if(priceElement == undefined) {
				priceElement = item.down('.special-price .price');
			}
			
			// If still price is undefined, then is an giftcard.
			if(priceElement == undefined) {
				var priceElement = item.down('[name="custom_giftcard_amount"]');
				if(priceElement == undefined || priceElement.value == "") priceElement = item.down('#giftcard_amount');
				
				price = priceElement.value;
			} else {
				price =  priceElement.innerHTML.stripTags().replace(publicCart.currencySymbol, '');
			}
			
			debug('viewFunctions.getViewPriceElement - price: '+price);
			return price;
		}
	
	}
	// *** View Template Object *** //
	
	
	// *** Cart Template Object *** //
	var cartFunctions = function () {
		var cartTable;
		var cartTotals;
		var freeSampleLimit;
		
		
		this.setCartActions = function () {
			debug('cartFunctions.setCartActions');
			// Check if cart is empty.
			if(getCartTable() == null) return;
			
			// Config Free Samples rows.
			addItemIds();
			configFreeSamples();
			
			// Get Freesample limit.
			getFreeSampleLimit();
			
			// Remove item buttons.
			$$('.btn-remove.btn-remove2').each(function(button) {
				removeCartClickAction(button);
			});
			
			// Qty autoupdate.
			$$('select.input-text.qty').each(function(dropdown) {
				// Add click button.
				dropdown.observe('change', function (e) {
					e.stop();
					debug('Update cart change dropdown - new.');
					
					updateItem(dropdown);
				});
			});
			
			// Update cart button.
			$$('.button.btn-update').each(function(button) {
				// Add click button.
				button.observe('click', function (e) {
					e.stop();
					debug('Update cart click.');
					
					updateCartClick(button);
				});
			});
			
			// Move to Wishlist link.
			$$('.link-wishlist').each(function(link) {
				moveToWishlistAction(link);
			});
			
			// Clear Shopping cart button.
			$$('.button.btn-empty').each(function(button) {
				// Add click button.
				button.observe('click', function (e) {
					e.stop();
					debug('Clear cart click.');
					
					// Check if button is suspended.
					if(button.hasClassName('suspended')) {
						debug('Suspended button.');
						return;
					}
					
					// Clear cart.
					updateRemote('clearCart', {}, function() {
						debug('Clear Shopping cart');
						location.reload();
					});
				});
			});
		
			// Add Free Samples.
			$$('.button.btn-free-cart').each(function(button) {
				var productData = getFreeSampleProduct(button);
				var qty = 1;
				
				// Add click button.
				button.observe('click', function (e) {
					e.stop();
					debug('Add free sample.');
					
					// Check if button is suspended.
					if(button.hasClassName('suspended')) {
						debug('Suspended button.');
						return;
					}
					
					// Check freesample limit.
					var _cartItems = getCartItems();
					var freeSampleCount = _cartItems.freeSampleCount();
					debug('cartFunctions. btn-free-cart - freeSampleCount: '+freeSampleCount);
					if(freeSampleCount >= freeSampleLimit) {
						debug('cartFunctions. btn-free-cart - Free sample limit excedeed.');
						alert("You can only select up to "+freeSampleLimit+" free samples per order.");
						return;
					}
					
					
					// Add item.
					debug(productData);
					var newLine = updateShoppingCart(productData, qty);
					button.addClassName('clicked');
					updateTopcart(productData, qty, function(postData) {
						debug('cartFunctions.updateTopcart callback');
						
						addToCartCallback(newLine, postData);
						
						// Update Free Sample.
						button.hide();
						button.next('.btn-free-remove').show();
						debug("Free samples added.");
						button.removeClassName('clicked');
					});
				});
			});
			
			// Remove Free Samples.
			$$('.button.btn-free-remove').each(function(button) {
				// Add click button.
				button.observe('click', function (e) {
					e.stop();
					debug('Remove free sample.');
					
					// Check if button is suspended.
					if(button.hasClassName('suspended')) {
						debug('Suspended button.');
						return;
					}
					
					
					button.addClassName('clicked');
					
					// Add Remove Item action.
					var productData = getFreeSampleProduct(button);
					var _cartItems = getCartItems();
					var itemId = _cartItems.getItemIdByProduct(productData);
					
					debug('Remove free sample - itemId: '+itemId+'.');
					var removeButton = getCartTable().down('> tbody tr[data-id="'+itemId+'"] .btn-remove.btn-remove2');
					removeButton.click();
				});
			});
			
			
			// Load Wishlist actions.
			loadWishlistActions();
		}
		
		this.removeItemFromShopping = function(tr) {
			debug('cartFunctions.removeItemFromShopping');
			_removeItemFromShopping(tr);
		}
		
		this.getRowById = function(id) {
			return _getRowById(id);
		}
		
		this.addNewProduct = function(productData, postData, qty) {
			debug('cartFunctions.addNewProduct');
			var newLine = updateShoppingCart(productData, qty);
			
			// Set lastItemId to cart callback.
			var newData = [];
			newData = postData;
			newData.lastItemId = productData.id;
			
			addToCartCallback(newLine, newData);
		}
		
		
		function loadWishlistActions() {
			debug('cartFunctions.loadWishlistActions');
			
			// Add Wishlist to cart.
			$$('.button.btn-wishlist-cart').each(function(button) {
				// XXXXXXXXXX - THIS ACTION WAS REMOVED BECAUSE HAD CONFLICTS WITH ORDERGROOVE MODULE.
				return false;
				// XXXXXXXXXX - THIS ACTION WAS REMOVED BECAUSE HAD CONFLICTS WITH ORDERGROOVE MODULE.
				
				
				var productData = getWishlistProduct(button);
				var qty = productData.qty;
				
				// Add click button.
				button.observe('click', function (e) {
					e.stop();
					debug('cartFunctions.loadWishlistActions - Add Wishlist.');
					
					// Check if button is suspended.
					if(button.hasClassName('suspended')) {
						debug('cartFunctions.loadWishlistActions - Suspended button.');
						return;
					}
					
					
					// Add item.
					debug(productData);
					var _cartItems = getCartItems();
					var itemId = _cartItems.getItemIdByProduct(productData);
					debug('cartFunctions.loadWishlistActions - itemId: '+itemId);
					if(! itemId) {
						var added = _cartItems.insertItem(productData, qty);
						debug('cartFunctions.loadWishlistActions - added');
						var newLine = updateShoppingCart(productData, qty);
					} else {
						var updated = _cartItems.updateItem(itemId, qty);
						debug('cartFunctions.loadWishlistActions - updated');
						var newLine = _getRowById(itemId);
						_updateQtyHtml(newLine, productData.isNotEditable, _cartItems.items[itemId].qty);
					}
					
					updateRemote('addWishlistToCart', {'id': productData.productId, 'itemId': productData.wishlistId}, function(postData) {
						debug('cartFunctions.loadWishlistActions - addWishlistToCart - callback');
						
						addToCartCallback(newLine, postData)
						
						// Update Wishlist.
						wishlistCartProducts();
						debug("cartFunctions.loadWishlistActions - addWishlistToCart - Wishlist added.");
					});
				});
			});
			
			// Remove Wishlist.
			$$('.button.btn-wishlist-remove').each(function(button) {
				// Add click button.
				button.observe('click', function (e) {
					e.stop();
					debug('Remove Wishlist.');
					
					// Check if button is suspended.
					if(button.hasClassName('suspended')) {
						debug('Suspended button.');
						return;
					}
					
					// Add Remove Item action.
					var productData = getWishlistProduct(button);
					var removeButton = getCartTable().down('> tbody tr[data-id="'+productData.id+'"] .btn-remove.btn-remove2');
					removeButton.click();
				});
			});
		}
		
		function moveToWishlistAction(link) {
			// Get data.
			var _wishlistLink = getWishlistLinkElement();
			var currentTr = link.up('tr');
			
			var productUrl = currentTr.down('.product-name a');
			if(productUrl == undefined) productUrl = currentTr.down('.product-image');
			
			var itemId = getWishlistItemId(link);
			
			// Add click link.
			link.observe('click', function (e) {
				e.stop();
				debug('Move to wishlist click.');
				
				// Check if link is suspended.
				if(link.hasClassName('suspended')) {
					debug('Suspended button.');
					return;
				}
				
				// Add item to wishlist.
				updateRemote('moveToWishlist', {'id': itemId, 'itemId': itemId}, function(data) {
					debug('cartFunctions.moveToWishlist - Callback');
					
					// Update wishlist Link.
					_wishlistLink.innerHTML = data.linkTitle;
					_wishlistLink.writeAttribute('title', data.linkTitle);
					
					// Update wishlist sidebar.
					wishlistCartProducts();
					
					// Update total in Shopping Cart.
					updateTotals(data);
					
					debug('cartFunctions.moveToWishlist - Finished');
				});
			});
		}
		
		function wishlistCartProducts() {
			debug('cartFunctions.wishlistCartProducts');
			
			// Update Wishlist Sidebar.
			var _wishlistProducts = $('wishlist-products');
			if(_wishlistProducts == undefined) _wishlistProducts = $('wishlist-products-none');
			new Ajax.Request(controller+'wishlistCartProducts/', {
				method: 'get',
				onCreate: function() {
					_wishlistProducts.innerHTML = "";
					_wishlistProducts.addClassName('loading');
				},
				onSuccess: function(response) {
					debug('addToWishlist.onSuccess');
					_wishlistProducts.up('.wishlist').replace(response.responseText);
					loadWishlistActions();
				},
				onFailure: function(response) {
					debug('addToWishlist.onFailure');
					printErrorMsg("Fatal server error.");
				},
				onComplete: function(response) {
					_wishlistProducts.removeClassName('loading');
				},
			});
		}
		
		
		function updateCartClick(button) {
			
			// Check if button is suspended.
			if(button.hasClassName('suspended')) {
				debug('Suspended button.');
				return;
			}
			
			// Edit items.
			var itemData = getCartProductData(button);
			debug(itemData);
			updateCartData(itemData);
		}
		
		function addToCartCallback(newLine, postData) {
			debug('cartFunctions.addToCartCallback');
			
			var itemId = postData.lastItemId;
			var isNotEditable = postData.items[itemId].isNotEditable;
			
			// Update new id data.
			newLine.setAttribute('data-id', itemId);
			
			// Update Edit link.
			updateEditLink(newLine, isNotEditable);
			
			// Update Move link.
			updateMoveLink(newLine, isNotEditable, itemId);
			
			// Update Remove link.
			updateRemoveLink(newLine, itemId);
			
			// Update Qty input.
			updateQtyInput(newLine, itemId, isNotEditable);
			
			// Update total in Shopping Cart.
			updateTotals(postData);
			
			debug('cartFunctions.addToCartCallback - Finished');
		}
		
		
		function addItemIds() {
			debug('cartFunctions.addItemIds');
			var _cartTable = getCartTable();
			
			_cartTable.select('> tbody tr').each(function(tr) {
				var button = tr.down('.btn-remove.btn-remove2');
				if(button != undefined) {
					var itemId = getItemId(button);
				} else {
					var qty = tr.down('select.input-text.qty');
					if(qty == undefined) var qty = tr.down('input.input-text.qty');
					
					var itemId = getItemIdDataFromQtyInput(qty.name);
				}
				
				debug('cartFunctions.addItemIds - itemId: '+itemId);
				
				tr.setAttribute('data-id', itemId);
			});
		}
		
		function configFreeSamples() {
			debug('cartFunctions.configFreeSamples');
			var _cartTable = getCartTable();
			var _cartItems = getCartItems();
			
			for(oneItem in _cartItems.items) {
				var productData = _cartItems.items[oneItem];
				
				if(productData.freesample == 1 || productData.price == 0) {
					debug('freesample: '+oneItem);
					
					// Remove Edit and qty input.
					var freeTr = _cartTable.down('tr[data-id="'+oneItem+'"]');
					updateEditLink(freeTr, true);
					updateMoveLink(freeTr, true);
					
					// Update qty.
					_updateQtyHtml(freeTr, true);
					
					// Update Remove link.
					_updateRemoveHtml(freeTr);
					
					// Update Price.
					_updatePriceHtml(freeTr, productData);
				}
			}
		}
		
		function getFreeSampleLimit() {
			debug('cartFunctions.getFreeSampleLimit');
			new Ajax.Request(controller+'freeSampleLimit/', {
				method: 'get',
				onSuccess: function(response) {
					debug('cartFunctions.getFreeSampleLimit - onSuccess');
					if(response.responseJSON.success == 1 && response.responseJSON.data != undefined) {
						freeSampleLimit = response.responseJSON.data.freeSampleLimit;
						debug('cartFunctions.getFreeSampleLimit - freeSampleLimit: '+freeSampleLimit);
					} else {
						freeSampleLimit = 0;
						debug('cartFunctions.getFreeSampleLimit - onSuccess - error.');
					}
				},
				onFailure: function(response) {
					debug('cartFunctions.getFreeSampleLimit - onFailure');
					freeSampleLimit = 0;
				},
			});
		}
		
		
		function updateItem(qtyField) {
			var itemId = getItemIdDataFromQtyInput(qtyField.name);
			var qty = qtyField.value;
			updateRemote('update', {'id': itemId, 'qty': qty}, function(returnData) {
				debug('cartFunctions.updateItem - itemId: '+itemId+' - qty: '+qty);
				
				// Get Product Data.
				var _cartItems = getCartItems();
				var productData = _cartItems.items[itemId];
				
				// Update subtotal in shopping cart.
				var cartTr = getCartTable().down('tr[data-id="'+productData.id+'"]');
				var priceElements = cartTr.select('.cart-price .price');
				priceElements[(priceElements.length-1)].innerHTML = moneyFormat((productData.price * qty));
				
				// Fix excedeed limit.
				if(cartTr.hasClassName(maxsaleqtyClass)) {
					cartTr.removeClassName(maxsaleqtyClass);
					if(cartTr.down('.item-msg.error') != undefined) cartTr.down('.item-msg.error').remove();
					debug('cartFunctions.updateItem - maxsaleqtyClass removed');
					
					// Remove last quantity option (is exceeded).
					if(qtyField.options != undefined && qtyField.options.length > 0) {
						qtyField.options[(qtyField.options.length - 1)].remove();
					}
					
					// If is the last excedeed item, delete the red message.
					if(getCartTable().select('.'+maxsaleqtyClass).length == 0 && $$('.messages .error-msg').length > 0) {
						$$('.messages .error-msg')[0].remove();
						debug('cartFunctions.updateItem - error-msg removed');
						addCheckoutButtons();
						debug('cartFunctions.updateItem - checkout button added');
					}
				}
				
				// Update total in Shopping Cart.
				updateTotals(returnData);
					
				// Update TOA Message.
				updateTOAMessage();
				
				// Update Reward Block.
				updateRewardBlock();
				
				debug('cartFunctions.updateItem - Finished');
			});
		}
		
		
		function getCartProductData(button) {
			var form = button.up('.cart').down('form');
			var cartData = {};
			
			form.getElements().each(function(input) {
				if(input.hasClassName('qty')) {
					cartData[input.getAttribute('name')] = input.value;
				}
			});
			
			return cartData;
		}
		
		function updateCartData(cartData) {
			debug('cartFunctions.updateCartData');
			
			updateRemote('updateCart', cartData, function(returnData) {
				// Update TopCart.
				var _cartItems = getCartItems();
				for(cartIndex in cartData) {
					// Get Product Data.
					var qty = parseInt(cartData[cartIndex]);
					var itemId = getItemIdDataFromQtyInput(cartIndex);
					var productData = _cartItems.items[itemId];
					
					// Update subtotal in shopping cart.
					var cartTr = getCartTable().down('tr[data-id="'+productData.id+'"]');
					var priceElements = cartTr.select('.cart-price .price');
					priceElements[(priceElements.length-1)].innerHTML = moneyFormat((productData.price * qty));
					
					// Update total in Shopping Cart.
					updateTotals(returnData);
					
					// Update TOA Message.
					updateTOAMessage();
				
					// Update Reward Block.
					updateRewardBlock();
					
					debug('cartFunctions.updateCartData - Finished');
				}
			});
		}
		
		function updateTOAMessage() {
			debug('cartFunctions.updateTOAMessage - Init');
			var _toaMessager = $('toa_message');
			if(_toaMessager != null) {
				debug('cartFunctions.updateTOAMessage - Updating');
				new Ajax.Request(controller+'toaMessage/', {
					method: 'get',
					onCreate: function() {
						_toaMessager.innerHTML = "";
						_toaMessager.addClassName('loading');
					},
					onSuccess: function(response) {
						debug('cartFunctions.updateTOAMessage - Success');
						_toaMessager.outerHTML = response.responseText;
					},
					onFailure: function(response) {
						debug('cartFunctions.updateTOAMessage - Failure');
						printErrorMsg("Fatal server error.");
					},
					onComplete: function(response) {
						_toaMessager.removeClassName('loading');
					},
				});
			}
			debug('cartFunctions.updateTOAMessage - Finished');
		}
		
		function updateRewardBlock() {
			debug('cartFunctions.updateRewardBlock - Init');
			var _rewardBlock = $('fivehundredfriends.redemption.rewards');
			if(_rewardBlock != null) {
				debug('cartFunctions.updateRewardBlock - Updating');
				
				// Remove Reward message.
				if($$('.reward-session-message').length > 0) {
					$$('.reward-session-message')[0].up('li').remove();
				}
				
				// Update block via Ajax.
				new Ajax.Request('/rewards-program/redemption/rewardblock/', {
					method: 'get',
					onCreate: function() {
						_rewardBlock.innerHTML = "";
						_rewardBlock.addClassName('loading');
					},
					onSuccess: function(response) {
						debug('cartFunctions.updateRewardBlock - Success');
						_rewardBlock.insert(response.responseText);
					},
					onFailure: function(response) {
						debug('cartFunctions.updateRewardBlock - Failure');
						printErrorMsg("Fatal server error.");
					},
					onComplete: function(response) {
						_rewardBlock.removeClassName('loading');
					},
				});
			}
			debug('cartFunctions.updateRewardBlock - Finished');
		}
	
		function updateTotals(data) {
			debug('cartFunctions.updateTotals');
			var _totalsTable = getTotalsTable();
			if(_totalsTable != null) {
				// Get totals tbody.
				var tBodyTotals = _totalsTable.select('> tbody .price');
				
				// If not discount and UKR was applied previous, remove tr.
				if($('ukr-reward-total') != null && data.totals['discount'] == undefined) {
					$('ukr-reward-total').remove();
				}
				
				// Update all totals.
				var count = 0;
				for(var key in data.totals) {
					if(key != "grand_total") {
						tBodyTotals[count].innerHTML = moneyFormat(data.totals[key]);
						count++;
					}
				}
				
				// Set grandtotal.
				var grandtotal = parseFloat(data.totals['grand_total']);
				_totalsTable.down('tfoot .price').innerHTML = moneyFormat(grandtotal);
			}
			
			// Check totals in mobile version.
			if($$('.grandtotal').length > 0 && data.totals.grand_total != undefined) {
				$$('.grandtotal span.price').invoke('replace', '<span class="price">'+moneyFormat(data.totals.grand_total)+'</span>');
			}
			
			debug('cartFunctions.updateTotals - End');
		}
		
		function getItemId(button) {
			var onclickAttribute = button.readAttribute('href');
			var itemtId = onclickAttribute.substr((onclickAttribute.indexOf('/id/') + 4));
			itemtId = parseInt(itemtId.substr(0, itemtId.indexOf('/')));
			
			return itemtId;
		}
		function getWishlistItemId(link) {
			var hrefAttribute = link.readAttribute('href');
			var itemId = hrefAttribute.substr((hrefAttribute.indexOf('/item/') + 6));
			itemId = parseInt(itemId.substr(0, itemId.indexOf('/')));
			
			return itemId;
		}
		function getItemIdDataFromQtyInput(name) {
			debug('cartFunctions.getItemIdDataFromQtyInput');
			
			var productId = name.substr(name.indexOf('['));
			productId = productId.substr(1, (productId.indexOf(']') - 1));
			
			return productId;
		}
		
		function getFreeSampleProduct(button) {
			var itemData = {};
			
			var currentItem = button.up('.free-sample-product');
			itemData.productId = currentItem.getAttribute('data-id');
			itemData.img = currentItem.down('.free-sample-product-image').innerHTML;
			itemData.name = currentItem.down('.free-sample-product-title a').innerHTML;
			itemData.price = currentItem.getAttribute('data-price');
			itemData.url = currentItem.down('.free-sample-product-title a').getAttribute('href');
			itemData.url = currentItem.down('.free-sample-product-title a').getAttribute('href');
			itemData.isNotEditable = true;
			itemData.qty = 1;
			
			return itemData;
		}
		function getWishlistProduct(button) {
			var itemData = {};
			
			var currentItem = button.up('.wishlist-product');
			
			// Get product name item.
			var productItem = currentItem.down('.wishlist-product-title a');
			if(productItem == undefined) {
				productItem = currentItem.down('.wishlist-product-info a');
			}
			
			// Get options.
			var optionsHtml = "";
			var optionsObj = currentItem.down('.wishlist-product-options')
			if(optionsObj != undefined) {
				optionsHtml = optionsObj.outerHTML;
				var options = getOptionsFromItem(optionsObj.select('.wishlist-options-data'));
			}
			
			itemData.wishlistId = currentItem.getAttribute('data-id');
			itemData.productId = currentItem.getAttribute('data-productid');
			itemData.qty = currentItem.getAttribute('data-qty');
			itemData.img = currentItem.down('.wishlist-product-image').innerHTML;
			itemData.name = productItem.innerHTML;
			itemData.price = currentItem.getAttribute('data-price');
			itemData.url = productItem.getAttribute('href');
			itemData.isNotEditable = currentItem.getAttribute('data-isnoteditable');
			itemData.optionsHtml = optionsHtml;
			itemData.options = options;
			
			return itemData;
		}
	
		function updateShoppingCart(productData, qty) {
			debug('cartFunctions.updateShoppingCart');
			// Get current cart data.
			var _cartTable = getCartTable();
			var firstLine = _cartTable.down('> tbody tr td .input-text.qty').up('tr');
			debug(firstLine);
			
			// Create new Line.
			var newLine = firstLine.clone(true);
			
			var productImage = newLine.down('.product-image');
			var productImageSize = productImage.down('img').getAttribute('width');
			var productName = newLine.down('.product-name a');
			if(productName == undefined) productName = newLine.down('.product-name');
			
			// Remove tr class.
			newLine.removeClassName('first').removeClassName('odd').removeClassName('even');
			
			// Set id if is undefined.
			if(productData.id == undefined) productData.id = "new";
			
			// Set data id.
			debug('Set data-id: '+productData.id);
			newLine.setAttribute('data-id', productData.id);
			
			// Image.
			productImage.setAttribute('href', productData.url);
			productImage.setAttribute('title', productData.name);
			productImage.innerHTML = productData.img;
			productImage.down('img').setAttribute('width', productImageSize);
			
			// Product Name.
			productName.setAttribute('href', productData.url)
			productName.innerHTML = productData.name;
			
			// Quantity.
			_updateQtyHtml(newLine, productData.isNotEditable, qty);
			
			// Price.
			_updatePriceHtml(newLine, productData);
			
			// Hide Remove line.
			if(productData.isNotEditable) {
				_updateRemoveHtml(newLine);
				_updateAddToWishlistHtml(newLine);
				updateEditLink(newLine, true);
				
			}
			
			// Subtotal.
			var subtotalPrice = newLine.down('.cart-price .price', 1);
			if(subtotalPrice != undefined) {
				subtotalPrice.innerHTML = moneyFormat((parseFloat(productData.price) * qty));
			}
			
			// Remove options and giftcards fields.
			if(newLine.down('.item-options') != undefined) {
				newLine.down('.item-options').remove();
			}
			
			// Options.
			if(productData.optionsHtml != undefined) {
				newLine.down('.product-name').insert({after: productData.optionsHtml});
			}
			
			// Remove Ordergroove.
			if(newLine.down('.og-offer') != undefined) {
				newLine.down('.og-offer').remove();
			}
			
			// Insert new line.
			_cartTable.down('> tbody').insert(newLine);
			
			// Fix style.
			_cartTable.down('> tbody tr.last').removeClassName('last');
			decorateTable(cartTableId);
			
			debug('cartFunctions.updateShoppingCart - END');
			return newLine;
		}
		
		
		function getCartTable() {
			if(cartTable == undefined) cartTable = $(cartTableId);
			
			return cartTable;
		}
		function getTotalsTable() {
			if(cartTotals == undefined) cartTotals = $(cartTotalsTableId);
			
			return cartTotals;
		}
		
		
		function addCheckoutButtons() {
			debug('cartFunctions.addCheckoutButtons');
			new Ajax.Request(controller+'checkoutButtons/', {
				method: 'get',
				onSuccess: function(response) {
					debug('cartFunctions.addCheckoutButtons - onSuccess');
					if(getTotalsTable() != null) {
						var totalContainer = getTotalsTable().up('div');
					} else {
						var totalContainer = $$('.total')[($$('.total').length - 1)].up('div'); // Mobile version.
					}
					
					totalContainer.insert(response.responseText);
					
					// Fix button align.
					totalContainer.down('.continue').setStyle({'float': "left"});
					
					debug('cartFunctions.addCheckoutButtons - finished');
				}
			});
		}
		
		function removeCartClickAction(button) {
			debug('cartFunctions.removeCartClickAction');
			var itemId = getItemId(button);
			var _cartItems = getCartItems();
			var productId = _cartItems.items[itemId].productId;
			
			// Add click button.
			button.observe('click', function (e) {
				e.stop();
				debug('cartFunctions - Remove item click.');
				
				// Check if button is suspended.
				if(button.hasClassName('suspended')) {
					debug('Suspended button.');
					return;
				}
				
				// Remove item.
				debug('deleting item: #'+itemId+' - productId: #'+productId);
				var deleted = _cartItems.deleteItem(itemId);
				if(! deleted) {
					debug('removeCartClickAction. Error deleting item.', 1);
				} else {
					// Send changes to ajax.
					updateRemote('delete', {id: itemId}, function(returnData) {
						debug('cartFunctions - Delete callback');
						
						// Check if is Free Samples.
						var freeSampleProduct = $$('.free-sample-product[data-id="'+productId+'"]');
						debug(freeSampleProduct);
						if(freeSampleProduct.length > 0) {
							freeSampleProduct[0].down('.button.btn-free-cart').show();
							freeSampleProduct[0].down('.button.btn-free-remove').hide();
							freeSampleProduct[0].down('.button.btn-free-remove').removeClassName('clicked');
						}
						
						// Update total in Shopping Cart.
						updateTotals(returnData);
						
						// Update TOA Message.
						updateTOAMessage();
				
						// Update Reward Block.
						updateRewardBlock();
					
						debug('cartFunctions - Delete callback - Finished');
					});
					
					// Delete from Shopping Cart.
					_removeItemFromShopping(button.up('tr'));
				}
			});
		}
		
		function updateEditLink(newLine, isNotEditable) {
			debug('cartFunctions.updateEditLink');
			var editLink = newLine.down('a:contains("Edit")');
			
			// Check if need remove link.
			if(editLink != undefined && isNotEditable) {
				editLink.remove();
				return;
			}
		}
		function updateMoveLink(newLine, isNotEditable, itemId) {
			debug('cartFunctions.updateMoveLink');
			var editLink = newLine.down('a:contains("Move")');
			
			// Check if need remove link.
			if(editLink != undefined) {
				if(isNotEditable) {
					editLink.remove();
					return;
				}
			
				var oldItemId = getWishlistItemId(editLink);
				var oldHref = editLink.getAttribute('href');
				editLink.setAttribute('href', oldHref.replace('/'+oldItemId+'/', '/'+itemId+'/'));
				
				// Add action to link.
				moveToWishlistAction(editLink);
			}
		}
	
		function updateRemoveLink(newLine, itemId) {
			debug('cartFunctions.updateRemoveLink');
			var removeButton = newLine.down('.btn-remove.btn-remove2');
			
			var oldItemId = getItemId(removeButton);
			var oldHref = removeButton.getAttribute('href');
			removeButton.setAttribute('href', oldHref.replace('/'+oldItemId+'/', '/'+itemId+'/'));
			
			// Add click action to remove button.
			removeCartClickAction(removeButton);
		}
		
		function updateQtyInput(newLine, itemId, isNotEditable) {
			debug('cartFunctions.updateQtyInput');
			
			var qtyField = newLine.down('.input-text.qty');
			if(qtyField == undefined) return;
			
			if(isNotEditable) {
				qtyField.up('tr').down('.product-name').innerHTML = qtyField.up('tr').down('.product-name').innerHTML.stripTags();
				qtyField.up('td').innerHTML = qtyField.value;
				return;
			} else {
				qtyField.name = "cart["+itemId+"][qty]";
			}
		}
		
		function _removeItemFromShopping(tr) {
			debug('cartFunctions._removeItemFromShopping');
			tr.remove();
			
			// Fix Style.
			var _cartTable = getCartTable();
			var trLast = _cartTable.down('> tbody tr.last');
			if(trLast != undefined) trLast.removeClassName('last');
			decorateTable(cartTableId);
		}
	
		function _updateQtyHtml(line, isNotEditable, qty) {
			var qtyInput = line.down('.input-text.qty');
			
			if(isNaN(qty)) qty = qtyInput.value;
			
			var qtyContainer = qtyInput.up('.product-qty');
			if(qtyContainer == undefined) qtyContainer = qtyInput.up('.quantity-container');
			if(qtyContainer == undefined) qtyContainer = qtyInput.up('td');
			
			if(isNotEditable) {
				qtyContainer.hide();
				qtyContainer.innerHTML = qty;
			} else {
				var qtyInput = qtyContainer.down('.qty');
				qtyInput.name = "cart[new][qty]";
				qtyInput.value = qty;
			}
		}
		
		function _updateRemoveHtml(line) {
			var removeDiv = line.down('.product-remove');
			if(removeDiv == undefined) removeDiv = line.down('.btn-remove.btn-remove2');
			
			if(removeDiv != undefined) {
				removeDiv.hide();
				
				// Hide divider.
				var dividers = line.select('.product-divider');
				if(dividers == undefined || dividers.length == 0) dividers = line.select('.separator');
				
				if(dividers != undefined && dividers.length > 0) {
					for(var i in dividers) {
						if (! dividers.hasOwnProperty(i)) continue;
						
						dividers[i].hide();
					}
				}
			}
		}
		
		function _updateAddToWishlistHtml(line) {
			var wishlistDiv = line.down('.product-wishlist');
			if(wishlistDiv == undefined) wishlistDiv = line.down('.link-wishlist');
			
			if(wishlistDiv != undefined) {
				wishlistDiv.hide();
			}
		}
		
		function _updatePriceHtml(line, productData) {
			var priceContainer = line.down('.cart-price .price');
			var subtotalPrice = line.down('.cart-price .price', 1);
			
			var price = productData.price;
			if(subtotalPrice == undefined) price = price * productData.qty;
			
			priceContainer.innerHTML = moneyFormat(price);
			
			if(productData.price == 0) {
				priceContainer.hide();
			}
		}
	
		function _getRowById(id) {
			debug('cartFunctions.getRowById');
			var tr = getCartTable().down('tr[data-id="'+id+'"]');
			
			return tr;
		}
	}
	// *** Cart Template Object *** //
	
	
	// *** Wishlist Template Object *** //
	var cartWishlist = function () {
		this.setWishlistActions = function () {
			$$('.button.btn-cart').each(function(button) {
				var _cartItems = getCartItems();
				var itemData = getWishlistProductData(button);
				var productId = itemData.id;
				var itemId = itemData.itemId;
				
				// Remove prev onclick action.
				button.writeAttribute('onclick');
				
				// Add click action.
				button.observe('click', function (e) {
					e.stop();
					debug('cartWishlist. Add to wishlist click.');
					
					if(button.hasClassName('suspended')) {
						debug('Suspended button.');
						return;
					}
					
					// Relocate topcart.
					_relocateTopcart();
					
					// Add / Edit item.
					debug(itemData);
					if(_cartItems.items[productId] != undefined) {
						var updated = _cartItems.updateItem(productId, itemData.qty);
						if(! updated) {
							debug('Error updating item.', 1);
							return;
						}
					} else {
						var added = _cartItems.insertItem(itemData, itemData.qty);
						if(! added) {
							debug('Error adding item.', 1);
							return;
						}
					}
					
					// Show cart.
					_showCart(7);
					
					// Add item to cart from wishlist.
					updateRemote('addWishlistToCart', {'id': productId, 'itemId': itemId}, function(data) {
						debug('cartWishlist.addWishlistToCart - callback');
						button.up('tr').remove();
					});
				});
			});
		
			$$('.btn-remove.btn-remove2').each(function(link) {
				var _cartItems = getCartItems();
				var itemData = getWishlistProductData(link);
				var productId = itemData.id;
				var itemId = itemData.itemId;
				
				// Remove prev onclick action.
				link.writeAttribute('onclick');
				
				// Add click action.
				link.observe('click', function (e) {
					e.stop();
					debug('cartWishlist. remove to wishlist click.');
					
					if(link.hasClassName('suspended')) {
						debug('Suspended button.');
						return;
					}
					
					// Remove item to wishlist.
					updateRemote('removeToWishlist', {'id': productId, 'itemId': itemId}, function(data) {
						debug('cartWishlist.addWishlistToCart - callback');
						link.up('tr').remove();
					});
				});
			});
		}
		
		function getWishlistProductData(button) {
			var itemData = {};
			
			var currentItem = button.up('tr');
			itemData.id = getProductId(currentItem.down('.product-name a'));
			itemData.itemId = getItemId(currentItem.down('.input-text.qty').readAttribute('name'));
			itemData.img = currentItem.down('.product-image').innerHTML;
			itemData.name = currentItem.down('.product-name a').innerHTML;
			itemData.price = getPriceElement(currentItem).innerHTML.replace(publicCart.currencySymbol, '');
			itemData.url = currentItem.down('.product-image').readAttribute('href');
			itemData.qty = parseInt(currentItem.down('.input-text.qty').value);
			
			return itemData;
		}
		
		function getProductId(item) {
			var href = item.readAttribute('href');
			var productId = href.substr((href.indexOf('/id/') + 4));
			productId = parseInt(productId.substr(0, productId.indexOf('/')));
			
			return productId;
		}
		function getItemId(name) {
			var itemId = name.substr((name.indexOf('[') + 1));
			itemId = parseInt(itemId.substr(0, itemId.indexOf(']')));
			
			return itemId;
		}
		function getPriceElement(item) {
			var priceElement = item.down('.regular-price .price')
			
			// If price is undefined, then is an special price.
			if(priceElement == undefined) {
				priceElement = item.down('.special-price .price');
			}
			
			return priceElement;
		}
	}
	// *** Wishlist Template Object *** //
	
	
	global.AjaxCart = publicCart;
	return publicCart;
}(window));
