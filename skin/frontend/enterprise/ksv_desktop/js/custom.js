//store locator filter drop down
jQuery(document).ready(function(){
	jQuery('.store-locator-tag ul').each(function(){
		var select = jQuery(document.createElement('div')).insertBefore(jQuery(this).hide());
		select.html("Show All").addClass("filter-drop");
		jQuery(select).click(function(){
			jQuery('.store-locator-tag ul').toggle();
		});
		jQuery('>li',this).click(function(){
			select.html(jQuery('>a',this).html());
			jQuery('.store-locator-tag ul').hide();			
		});
	});	
});

//giftcard area
jQuery(document).ready(function() {
	var element = jQuery("button#onestepcheckout-giftcard-remove");
	if(element){
		if(element.css('display')!=="none"){
			jQuery("input#id_giftcardcode").css('width','60%');
			jQuery("button#onestepcheckout-giftcard-remove").css('margin-right',"10px");
			jQuery("button#onestepcheckout-giftcard-add").css('margin-right',"4%");
		}
	}
});

jQuery(function($) {

	$(document).ready(function(){
				
		// Fancy select boxes
		$('.region-index-index select').select2({
		    minimumResultsForSearch: -1,	// turns off search feature
		    dropdownAutoWidth: 'true'		// lets options be wider than select box 
		});
		
		$('.footer-region-selector select').select2({
			minimumResultsForSearch: -1,
		    dropdownAutoWidth: 'true'		    
		});
		$('.footer-region-selector select').on("change", function(e) {
			var region_picker = jQuery('#region-picker');
			var selected_region = jQuery(':selected', region_picker);
			if (jQuery.inArray(selected_region.val(), redirectDomainsJson) == -1) {
				// nothing
			}
			else {
				jQuery.removeCookie('katesomerville_region_route', { path: '/' });
				if (selected_region.val() != 'www.katesomerville.co.kr' && selected_region.val().substring(0, 3) != 'hk.') {
					if (jQuery.cookie('katesomerville_region_route', selected_region.val(), { path: '/', expires: 5, domain: '.'+regionRouteDomain })) {
						window.location.replace('//' + jQuery.cookie('katesomerville_region_route'));
					} else {
						window.location.replace('//' + selected_region.val());
					}
				} else {
					window.location.replace('//' + selected_region.val());
				}
			}
		});
		
		
		// Assign different class to mini-cart, ONLY when clicked
		$('#cartHeader').click(function() {
		
			if ($('#topCartContent').hasClass('stick-it')) {			
				setTimeout(function(){
	            	$('#topCartContent').removeClass('stick-it');
	            },1000);
	        } else {
		        $('#topCartContent').addClass('stick-it');
	        }
      
			
		});
		// Force removal of class when close button is used
		$('.top-cart .close-btn').click(function() {
			setTimeout(function(){
				$('#topCartContent').removeClass('stick-it');
			},1000);
		});
	
	}); // document ready end
	
}); // jquery end

//whishlist heart position 
jQuery(document).ready(function() {
	// jQuery('.favorites').css('display','none');
	// var content = jQuery('.favorites').html();
	// jQuery(content).appendTo('.product-options-bottom');
	// jQuery(content).appendTo('.add-to-cart');
	
	jQuery('.favorites').detach().appendTo('.add-to-cart').show();
});
	
//freesample hover 
jQuery(document).ready(function(){
	jQuery('.checkout-cart-index .free-sample-product').mouseenter(function(){
		var title = '<div class="freesample-hover">' + jQuery(this).find('.free-sample-product-title a').html().replace('(Sample)','') +'<img src="/skin/frontend/enterprise/ksv_desktop/images/tab-arrow-gray.png" table="arrow"/></div>';
		jQuery(title).appendTo(this);
	})
	jQuery('.checkout-cart-index .free-sample-product').mouseleave(function(){
		jQuery(this).find(".freesample-hover").remove();
	})		
});

//simple product show out of stock message
jQuery(document).ready(function(){
  if(jQuery('.catalog-product-view .input-with-price .availability').hasClass('out-of-stock')) {
	var message = jQuery('.catalog-product-view .input-with-price .out-of-stock').text();
	jQuery('.outofstockmessage').show();
	jQuery('.outofstockmessage').text(message);
  }
});

//contact us page notices message 
jQuery(document).ready(function(){
	jQuery(".contact-index-index #admin_messages").hide();
	var adminmessage = jQuery(".contact-index-index #admin_messages").text();
	jQuery("#recaptcha_message").text(adminmessage);
});
	
	
	
	


