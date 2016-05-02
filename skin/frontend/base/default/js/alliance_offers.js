
document.observe('dom:loaded', function() {
	var button = $$('.offers-checkout .btn-proceed-checkout')[0];
	button.writeAttribute('onclick');
	
	button.observe('click', function (e) {
		e.stop();
		
		$('frmOffers').submit();
	});
});

