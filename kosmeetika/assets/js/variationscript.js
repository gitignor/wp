jQuery(document).ready(function(){
	jQuery( 'table.variations' ).append( '<a class="reset_variations" href="#">'+object_name.puhas_string+'</a>' );
	var $form_name = jQuery('table.variations select').attr('id');
	jQuery('div[data-role="controlgroup"] a').click(function() {
		jQuery('div.product-variation.active').removeClass('active');
		jQuery(this).children('div.product-variation').addClass('active');
	});
	jQuery('a.reset_variations').click(function() {
		jQuery('div.product-variation.active').removeClass('active');
	});
	jQuery('div[data-role="controlgroup"] a').click(function(){
		jQuery("#"+$form_name + " option:contains('"+jQuery(this).attr("data-toodename")+"')").attr("selected",true);
	    jQuery("#"+$form_name).change();
	});
});