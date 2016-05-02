document.observe('dom:loaded', function() {
	var $grid;
	
	$$('.productfilter-attribute').each(function(attributeContainer) {
		var attributeTitle = attributeContainer.down('.productfilter-attribute-title');
		var attributeOptions = attributeContainer.down('.productfilter-attribute-options');
		
		attributeTitle.observe('click', function(e) {
			e.stop();
			
			// Check if options are open.
			if(attributeContainer.hasClassName('opened')) {
				attributeContainer.removeClassName('opened');
			} else {
				$$('.productfilter-attribute.opened').invoke('removeClassName', 'opened');
				attributeContainer.addClassName('opened');
			}
		});
		
		attributeOptions.observe('mouseleave', function() {
			attributeContainer.removeClassName('opened');
		});
	});
	
	$$('.productfilter-attribute-options li').each(function(option) {
		var attributeTitle = option.up('.productfilter-attribute-suboptions');
		var attributeContainer = option.up('.productfilter-attribute');		

		// Remove options without products.
		var showOption = false;
		for(productId in filterData) {
			var optionId = option.readAttribute('data-option');
			var productData = ','+filterData[productId]+',';
			if(productData.indexOf(','+optionId+',') != -1) {
				showOption = true;
				break;
			}
		}
		if(! showOption) {
			option.hide();
			return;
		}
		
		option.observe('click', function(e) {
			e.stop();
			
			// Check if options are open.
			if(option.hasClassName('checked')) {
				option.removeClassName('checked');
			} else {
				option.addClassName('checked');
			}
			$('nomatches-message').hide();
			shuffleFilter();
			
			// if any option is checked on attribute, show as active.
			if(attributeContainer.select('.checked').length > 0) {
				attributeContainer.addClassName('filter-active');	
			} else {
				attributeContainer.removeClassName('filter-active');
			}
			
			if(attributeTitle){
				if(attributeTitle.select('.checked').length > 0) {
					attributeTitle.addClassName('filter-active');	
				} else {
					attributeTitle.removeClassName('filter-active');
				}
			}
			
			// If there is some option checked, show "Clear Filter" button.
			if($$('.productfilter-attribute-options li.checked').length > 0) {
				$('productfilter-clear').show();
			} else {
				$('productfilter-clear').hide();
				shuffleShowAll();
			}
		
			// If no result, show message.
			if($$('.products-grid li.filtered').length == 0) {
				$('nomatches-message').show();
			}
		});
	});
	
	// Clear all button.
	$('productfilter-clear').observe('click', function(e) {
		e.stop();
		$$('.productfilter-attribute-options li.checked').invoke('removeClassName', 'checked');
		$$('.productfilter-attribute.filter-active').invoke('removeClassName', 'filter-active');
		$$('.productfilter-attribute-suboptions').invoke('removeClassName', 'filter-active');
		$$('.productfilter-attribute-suboptions').invoke('removeClassName', 'open');
		shuffleShowAll();
		$('productfilter-clear').hide();
		$('nomatches-message').hide();
	});
	
	// Init shuffle.
	shuffleInit();


	// Shuffle product.
	function shuffleInit() {
		// Product Filter, works with jQuery.
		$grid = jQuery('.products-grid');
		$grid.shuffle({
			itemSelector: '.item'
		});
	}
	function shuffleShowAll() {
		$grid.shuffle('shuffle', 'all');
	}
	function shuffleFilter() {
		var activeOptions = [];
		$$('.productfilter-attribute-options li.checked').each(function(checkedOption) {
			activeOptions.push(checkedOption.readAttribute('data-option'));
		});
		
		$grid.shuffle('shuffle', function($el) {
			// Get productId
			var productId = parseInt($el.data('productid'));
			
			// Get product data.
			var productData = ','+filterData[productId]+',';
			
			// Check all active options.
			for(var i = 0; i < activeOptions.length; i++) {
				if(productData.indexOf(','+activeOptions[i]+',') == -1) {
					return false;
				}
			}
			
			return true;
		});
	}
	
	jQuery('.productfilter-benefits .productfilter-attribute-options .productfilter-attribute-subtitle').click(function(){
		jQuery(this). parent().siblings().removeClass("open");
		jQuery(this). parent().toggleClass("open")
	});
	
});
