jQuery(document).ready(function(){
	jQuery('#frm-email-capture-popup_global').submit(function(){
		var val = jQuery('#newsletter').hasClass('validation-failed');
		if(!val){
			customLinkTracking("email signup popup");
		}
	})
	
	jQuery('#frm-newsletter-validate-cat').submit(function(){
		var val = jQuery('#newsletter0233').hasClass('validation-failed');
		if(!val){
			customLinkTracking("email signup leftnav");
		}
	})
	
	jQuery('#frm-newsletter-validate-footer').submit(function(){
		var val = jQuery('#newsletter11').hasClass('validation-failed');
		if(!val){
			customLinkTracking("email signup footer");
		}
	})
})