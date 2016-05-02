document.observe('dom:loaded', function() {
	AjaxCart.setEnvironment('cart');
	console.log(AjaxCart.version);
	
	// Add view all function.
	if($('free-samples-view-all') != undefined) {
		$('free-samples-view-all').observe('click', function (e) {
			e.stop();
			new Effect.SlideDown('free-samples-list-hide', {
				duration: 0.5,
				beforeStart: function(effect) {
					$('free-samples-collapse').show();
					$('free-samples-view-all').hide();
					$('free-samples-collapse-2').show();
					$('free-samples-view-all-2').hide();
					var e = parseInt(jQuery('#free-samples-list-hide').css('height'),10);
					var d = 503+e+"px"		
					jQuery('#collateral-tabs').css('height',d);
				}
			});
		});
		$('free-samples-collapse').observe('click', function (e) {
			e.stop();
			
			new Effect.SlideUp('free-samples-list-hide', {
				duration: 0.5,
				beforeStart: function(effect) {
					$('free-samples-view-all').show();
					$('free-samples-collapse').hide();
					$('free-samples-view-all-2').show();
					$('free-samples-collapse-2').hide();
					jQuery('#collateral-tabs').css('height',"493px");
				}
			});
		});
	
	}
	
	
	if($('free-samples-view-all-2') != undefined) {
		// Add second view all function.
		$('free-samples-view-all-2').observe('click', function (e) {
			e.stop();
			
			new Effect.SlideDown('free-samples-list-hide', {
				duration: 0.5,
				beforeStart: function(effect) {
					$('free-samples-collapse-2').show();
					$('free-samples-view-all-2').hide();
					$('free-samples-collapse').show();
					$('free-samples-view-all').hide();
					var e = parseInt(jQuery('#free-samples-list-hide').css('height'),10);
					var d = 503+e+"px"		
					jQuery('#collateral-tabs').css('height',d);
				}
			});
		});
		$('free-samples-collapse-2').observe('click', function (e) {
			e.stop();
			
			new Effect.SlideUp('free-samples-list-hide', {
				duration: 0.5,
				beforeStart: function(effect) {
					$('free-samples-view-all-2').show();
					$('free-samples-collapse-2').hide();
					$('free-samples-view-all').show();
					$('free-samples-collapse').hide();
					jQuery('#collateral-tabs').css('height',"493px");
				}
			});
		});
	}

	
	
});

