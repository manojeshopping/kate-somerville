Validation.add('concerns-checkboxs', 'remove', function(value, element) {
	jQuery('.concerns-advice').remove();
	setTimeout(function() {jQuery(".validation-advice:contains('remove')").remove()}, 10);
	
	if(jQuery('.concerns-checkboxs:checked').length >= 1 && jQuery('.concerns-checkboxs:checked').length <= 2) return true;
	
	jQuery('#concerns-paragraph').after('<div class="validation-advice concerns-advice">Select up to two skin concerns.</div>');
	return false;
});

Validation.add('concerns-month', 'Month must be between 1 and 12', {
	minLength : 1,
	maxLength : 2,
	min : 1,
	max : 12,
	include : ['validate-digits']
});

Validation.add('concerns-day', 'Day must be between 1 and 31', {
	minLength : 1,
	maxLength : 2,
	min : 1,
	max : 31,
	include : ['validate-digits']
});

Validation.add('concerns-year', 'Year must be between 1900 and 2014', {
	minLength : 4,
	maxLength : 4,
	min : 1900,
	max : 2014,
	include : ['validate-digits']
});


// Captcha.
var RecaptchaOptions = {
	theme : 'custom',
	custom_theme_widget: 'recaptcha_widget' 
};

