jQuery.fn.lW = function (limit) {
  jQuery(this).attr("maxlength", limit+1);
  jQuery(this).after('<p class="errorMsg">The limit for this field is ' + limit + ' characters</p>');
  jQuery(this).bind('keyup', function() {
    if (jQuery(this).val().length > limit) {
      jQuery(this).parent().parent().addClass("errorField"); 
      var input = jQuery(this).val();
      jQuery(this).val(input.substr(0,input.length-1));
    } else {
      jQuery(this).parent().parent().removeClass("errorField");
    }
  
  });
};


jQuery('document').ready(function(){
if(typeof jQuery.fn.lW != 'undefined') {
	jQuery('input[name="firstname"]').lW(17);
	jQuery('input[name="lastname"]').lW(18);
	jQuery('input[name="telephone"]').lW(35);
	jQuery('input[name="fax"]').lW(35);
	jQuery('input[name="company"]').lW(35);
	jQuery('input[name="street[]"]').lW(35);
	jQuery('input[name="city"]').lW(35);
	jQuery('input[name="postcode"]').lW(10);

	// Billing
	jQuery('input[name="billing[firstname]"]').lW(17);
	jQuery('input[name="billing[lastname]"]').lW(18);
	jQuery('input[name="billing[company]"]').lW(35);
	jQuery('input[name="billing[street][]"]').lW(35);
	jQuery('input[name="billing[city]"]').lW(35);
	jQuery('input[name="billing[postcode]"]').lW(10);
	jQuery('input[name="billing[telephone]"]').lW(35);
	jQuery('input[name="billing[fax]"]').lW(35);
	// Shipping 
	jQuery('input[name="shipping[firstname]"]').lW(17);
	jQuery('input[name="shipping[lastname]"]').lW(18);
	jQuery('input[name="shipping[company]"]').lW(35);
	jQuery('input[name="shipping[street][]"]').lW(35);
	jQuery('input[name="shipping[postcode]"]').lW(10);
	jQuery('input[name="shipping[city]"]').lW(35);
	jQuery('input[name="shipping[telephone]"]').lW(35);
	jQuery('input[name="shipping[fax]"]').lW(35);
}
});