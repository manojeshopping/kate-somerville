jQuery(function() {
	jQuery('.creation-sample input[name="samplekit"]:last').addClass('validate-sample');
	
	jQuery('.creation-sample-image img').tooltip({
		position: {
			my: 'center bottom-20',
			at: 'center top',
			using: function(position, feedback) {
				jQuery(this).css(position);
				jQuery('<div>')
					.addClass('arrow')
					.addClass(feedback.vertical)
					.addClass(feedback.horizontal)
					.appendTo(this);
			}
		}
	});
});

Validation.add('validate-sample', 'Please select one sample kit', function(value, element) {
	jQuery(".validation-advice").remove();
	setTimeout(function() {jQuery(".validation-advice:first").remove()}, 10);
	
	if(jQuery('.creation-sample input[name="samplekit"]:checked').length == 1) return true;
	
	jQuery('#sample-submit').after('<div class="validation-advice">Please select one sample kit.</div>');
	return false;
});

