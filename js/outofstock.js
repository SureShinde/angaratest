var outofstock = {
    initialize: function() {      
        this.bindEvents();
    },
    bindEvents: function () {
        this.addSubmitEvent();
    },
	notifyPopUp: function (obj) {
		var url = obj.form.action;
		data =    obj.form.serialize();
		new Ajax.Request(url, {
			method      : 'post',
			dataType    : 'json',
			postBody    : data,
            onCreate    : function() {
				jQuery('#add-cart-wait').show();
            },
            onSuccess    : function(response) {  
                try{
					var res = response.responseText.evalJSON();   
					if(res && typeof res.success != 'undefined' && typeof res.message != 'undefined' && res.message.length > 0) { 
						if(res.success == 1) {
							var notifyWrapper = document.getElementsByClassName('notifyme');
							for(var i=0;i<notifyWrapper.length;i++) {
								notifyWrapper[i].style.display = 'none';
							}
						}
						/* Place for Popup html */
						/* Start */
						//alert(res.message);
						jQuery('#add-cart-wait').hide();
						
						
						
						if(jQuery(window).width() > 1024){
							jQuery('.notify-me').css({'opacity':'1', 'bottom':'40px'}).find('.text').text(res.message);
							jQuery('.notify-me-box').css({'padding-bottom':'220px'});
						}else if(jQuery(window).width() >= 768 && jQuery(window).width() <= 1024){
							jQuery('.notify-me').css({'opacity':'1', 'bottom':'40px'}).find('.text').text(res.message);
							jQuery('.notify-me-box').css({'padding-bottom':'135px'});
						}else if(jQuery(window).width() < 768){
							jQuery('.notify-me').css({'opacity':'1', 'bottom':'48px'}).find('.text').text(res.message);
						}
						
						setTimeout(function(){
							jQuery('.notify-me').removeAttr('style');
						},5000);
						
						
						
						/* End */
					}
				} catch(e) {
					console.log(e);
				}
            }
        });
	},
	addSubmitEvent: function () {        
        if(typeof productAddToCartForm != 'undefined') {
            var _this = this;
            productAddToCartForm.submit = function(url){                
                if(Validation.validate($('subscription_email'))) {
					_this.notifyPopUp(this);
				}
                return false;
            }                                  
            productAddToCartForm.form.onsubmit = function() {            
                productAddToCartForm.submit();
                return false;
            };
        }
    }	
};
document.observe("dom:loaded", function() {
	if(document.getElementById('subscription_email')){
		setTimeout(function(){
			jQuery("form#product_addtocart_form :input").each(function(){
				if(jQuery(this).hasClass('required-entry') && jQuery(this).attr('id') != 'subscription_email') {
					jQuery(this).removeClass('required-entry');
				}
			});
		},1000);
		outofstock.initialize();
		if(Prototype.Browser.IE){}else{
			$('subscription_email').observe('blur', function(e){
				Validation.validate($('subscription_email'));
			});
		}
	}
});