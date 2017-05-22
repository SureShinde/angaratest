/**
 * @author Asheesh Singh
 */
 /* To Load Items and Totals simulteneously on cart page. */
var CartDataOnAjax = [];
var ajaxcart = {
	initialize: function() { 
		this.agaq = [];
        this.bindEvents();
    },
    bindEvents: function () {
		// this.discountSubmitEvent();
    },
	loadItems: function (url) {
		var _this = this;
		new Ajax.Request(url, {
			method      : 'get',
			dataType    : 'json',
            onCreate    : function() {
            },
            onSuccess    : function(response) {
				_this.initialize();
                var ihtml = '';
				jQuery('#buy-now-loader').addClass('hide');
				jQuery('body').removeClass('disabled');
				try{
					var res = response.responseText.evalJSON();
					if(res && typeof res.content != 'undefined' && document.getElementById('shopping-cart-totals-table')) { 
						document.getElementById('shopping-cart-totals-table').parentNode.innerHTML = res.content;
					}
					if(res && typeof res.items != 'undefined' && res.items.length > 0) {
						var ihtml = '';
						for(var i=0;i<res.items.length;i++) {
							if(typeof res.items[i].id != 'undefined' && typeof res.items[i].content != 'undefined') {
								ihtml = ihtml + res.items[i].content;
								_this.agaq.push([res.items[i].catgeory,res.items[i].sku]);
							}
						}
						if(document.getElementById('item_container_wrapper')) {
							document.getElementById('item_container_wrapper').innerHTML = ihtml;
						}
					}
				} catch(e) {
					console.log(e);
					/* window.location = window.location; */
					return;
				}
            }
        });
	},
	loadTotals: function (url) {
		var _this = this;
		new Ajax.Request(url, {
			method      : 'get',
			dataType    : 'json',
            onCreate    : function() {
            },
            onSuccess    : function(response) {  
                jQuery('#buy-now-loader').addClass('hide');
				jQuery('body').removeClass('disabled');
				try{
					var res = response.responseText.evalJSON();
					if(res && typeof res.content != 'undefined') { 
						CartDataOnAjax.push(['shopping-cart-totals-table',res.content]); 
					}	
				} catch(e) {
					console.log(e);
					/* window.location = window.location; */
					return;
				}
				if(CartDataOnAjax.length == 2) {
					for(var k=0;k<CartDataOnAjax.length;k++){
						if(typeof CartDataOnAjax[k][0] != 'undefined' && document.getElementById(CartDataOnAjax[k][0])) {
							document.getElementById(CartDataOnAjax[k][0]).innerHTML = CartDataOnAjax[k][1];
							console.log('Last Totals: '+CartDataOnAjax[k][0]);
						}
					}
				}
            }
        });
	},
	loadCartHelpCallout: function (url) {
		var _this = this;
		new Ajax.Request(url, {
			method      : 'get',
			dataType    : 'json',
            onCreate    : function() {
            },
            onSuccess    : function(response) {
                jQuery('#buy-now-loader').addClass('hide');
				jQuery('body').removeClass('disabled');
				try{
					var res = response.responseText.evalJSON();
					if(res && typeof res.content != 'undefined' && document.getElementById('cart_help_callout')) { 
						document.getElementById('cart_help_callout').innerHTML = res.content;
					}
				} catch(e) {
					//console.log(e);
				}
            }
        });
	},
	discountSubmitEvent: function (url,message) { 
		var _this = this;
		new Ajax.Request(url, {
			method      : 'get',
			dataType    : 'json',
            onCreate    : function() {
            },
            onSuccess    : function(response) { 
				_this.initialize();
                jQuery('#buy-now-loader').addClass('hide');
				jQuery('body').removeClass('disabled');
				try{
					var res = response.responseText.evalJSON();
					if(res && typeof res.content != 'undefined' && document.getElementById('shopping-cart-totals-table')) { 
						document.getElementById('shopping-cart-totals-table').parentNode.innerHTML = res.content;
					}
					if(res && typeof res.items != 'undefined' && res.items.length > 0) {
						var ihtml = '';
						for(var i=0;i<res.items.length;i++) {
							if(typeof res.items[i].id != 'undefined' && typeof res.items[i].content != 'undefined') {
								ihtml = ihtml + res.items[i].content;
								_this.agaq.push([res.items[i].catgeory,res.items[i].sku]);
							}
						}
						if(document.getElementById('item_container_wrapper')) {
							document.getElementById('item_container_wrapper').innerHTML = ihtml;
						}
					}
					if(message=="This promo code is not valid.")
						jQuery('#invCoupon').html(message);
				} catch(e) {
					console.log(e);
					/* window.location = window.location; */
					return;
				}
				
            }
        });
    }
};
function pushOPGA(){
	_gaq = _gaq || [];
	if(ajaxcart.agaq && ajaxcart.agaq.length > 0) {
		for(var i=0;i<ajaxcart.agaq.length;i++) {
			if(typeof ajaxcart.agaq[i][0] != 'undefined') {
				_gaq.push(['_trackEvent', ajaxcart.agaq[i][0], 'opcheckout', ajaxcart.agaq[i][1], undefined, true]);
			} else {
				_gaq.push(['_trackEvent', '', 'opcheckout', ajaxcart.agaq[i][1], undefined, true]);
			}
			
		}
	}
}
/* Coupon Code apply */
jQuery(document).on('keyup','#user_coupon_code', function(e){
	if(e.keyCode == 13){
		e.preventDefault();
		applyCoupon(e);
	}
	return false;
});
jQuery(document).on('keypress keydown keyup', function(e){
   if(e.keyCode == 13) { if(jQuery(e.target).attr('id') != 'qtyeditor'){ e.preventDefault(); } }
});	
function applyCoupon(e){
	var couponCode	=	jQuery('#user_coupon_code').val();
	if(couponCode.length == 0 || jQuery('#coupon_code').val() == couponCode){
		return false;
	}
	try{
		applyCouponClicked(couponCode);
	}
	catch(e){
		
	}
	jQuery('#coupon_code').val(couponCode);
	jQuery('#buy-now-loader').removeClass('hide');
	jQuery('body').addClass('disabled');
	jQuery.ajax({
		type: 'POST',
		url: location.protocol+'//'+location.hostname+'/checkout/cart/couponPost/',
		data: { coupon_code : couponCode , remove : 0},
	}).done(function( html ){
		var couponCheck = html.evalJSON();
		
		var message='';
		if(couponCheck.invalidCoupon)
		{
			message="This promo code is not valid.";
		}
		ajaxcart.discountSubmitEvent(location.protocol+'//'+location.hostname+'/checkout/cart/index/',message);
		/* ajaxcart.initialize();
		jQuery('#buy-now-loader').addClass('hide');
		jQuery('body').removeClass('disabled');
		var res = html.evalJSON();
		if(res && typeof res.content != 'undefined' && document.getElementById('shopping-cart-totals-table')) { 
			document.getElementById('shopping-cart-totals-table').parentNode.innerHTML = res.content;
		}
		if(res && typeof res.items != 'undefined' && res.items.length > 0) {
			var ihtml = '';
			for(var i=0;i<res.items.length;i++) {
				if(typeof res.items[i].id != 'undefined' && typeof res.items[i].content != 'undefined') {
					ihtml = ihtml + res.items[i].content;
					ajaxcart.agaq.push([res.items[i].catgeory,res.items[i].sku]);
				}
			}
			if(document.getElementById('item_container_wrapper')) {
				document.getElementById('item_container_wrapper').innerHTML = ihtml;
			}
		} */
	}).fail(function(jqXHR, textStatus){
		jQuery('#buy-now-loader').addClass('hide');
		jQuery('body').removeClass('disabled');
	});
}



/* Parallel Request using progressive technique */
ajaxcart.initialize();
//ajaxcart.loadTotals(location.protocol+'//'+location.hostname+'/checkout/cart/loadTotals/');
ajaxcart.loadItems(location.protocol+'//'+location.hostname+'/checkout/cart/loadItems/');
if(document.getElementById('cart_help_callout')) {
	setTimeout(function(){ajaxcart.loadCartHelpCallout(location.protocol+'//'+location.hostname+'/checkout/cart/loadCartHelpCallout/');},200);
}
/* Shipping drop down script */
jQuery(document).on('click','#shipping_dropdown_box label', function(e){
	var text = jQuery(this).find('.title').text();
	var date = jQuery(this).children('span:eq(1)').text();
	if(jQuery(window).width() <= 768){
		jQuery(this).parent('#shipping_dropdown_box').addClass('hide');
	}
	jQuery(this).siblings().removeClass('active');
	jQuery(this).addClass('active');
	jQuery('#shipping_active_box .active-option').html('');
	jQuery('#shipping_active_box .active-option').html('<span class="title">'+ text +'</span><br><span>'+ date +'</span>');
	jQuery('#shipping_active_box').attr('data-visibility','hidden');
});
jQuery(document).on('click','#shipping_active_box', function(e){
	if(jQuery(this).attr('data-visibility') == 'hidden'){
		jQuery('#shipping_dropdown_box').removeClass('hide');
		jQuery(this).attr('data-visibility','visible');
	}else{
		jQuery('#shipping_dropdown_box').addClass('hide');
		jQuery(this).attr('data-visibility','hidden');
	}
});
if(jQuery(window).width() <= 768){
	jQuery(document).on('click', function(e){
		if (!jQuery(e.target).closest('#shipping_option').length) {
			jQuery('#shipping_dropdown_box').addClass('hide');
			jQuery('#shipping_active_box').attr('data-visibility','hidden');
		}

	});	
}
/* Show/Hide Coupon/Tax/Shipping Options */
jQuery(document).on('click','td', function(e){
	if($(this).hasAttribute('data-class')){
		var elem = jQuery(this).attr('data-class');
		if(jQuery('tr[data-class=open-'+elem+']').is(':visible')){
			jQuery('tr[data-class=open-'+elem+']').attr('style','display:none');
		}else{
			jQuery('tr[data-class=open-'+elem+']').attr('style','display:table-row');
		}
		jQuery(this).find('i').toggleClass('fa-angle-down fa-angle-up');
	}
});	

/* Tax Calculation */
var dataLoaded	=	'';	
function estimateTaxRate(old_estimate_postcode){
	var pLength = jQuery("#estimate_postcode").val().length;
	var estimate_postcode = jQuery("#estimate_postcode").val();
	
	if(pLength=='5' && dataLoaded!='1' && estimate_postcode!=old_estimate_postcode){
		dataLoaded	=	1;
		jQuery('#buy-now-loader').removeClass('hide');
		jQuery('body').addClass('disabled');
		jQuery.ajax({
			type: 'POST',
			url: location.protocol+'//'+location.hostname+'/checkout/cart/estimateTax/',
			data: { estimate_postcode : estimate_postcode },
			beforeSend: function( xhr ) {
				jQuery('#estimate_postcode').attr('disabled','disabled');
			},
		}).done(function( html ){
			//ajaxcart.initialize();
			dataLoaded = '';
			jQuery('#buy-now-loader').addClass('hide');
			jQuery('body').removeClass('disabled');
			try{
				var res = html.evalJSON();
				if(res && typeof res.content != 'undefined' && document.getElementById('shopping-cart-totals-table')) { 
					document.getElementById('shopping-cart-totals-table').parentNode.innerHTML = res.content;
				}
				/* if(res && typeof res.items != 'undefined' && res.items.length > 0) {
					var ihtml = '';
					for(var i=0;i<res.items.length;i++) {
						if(typeof res.items[i].id != 'undefined' && typeof res.items[i].content != 'undefined') {
							ihtml = ihtml + res.items[i].content;
							ajaxcart.agaq.push([res.items[i].catgeory,res.items[i].sku]);
						}
					}
					document.getElementById('item_container_wrapper').innerHTML = ihtml;
				} */
			} catch(e) {
				console.log(e);
				/* window.location = window.location; */
				return;
			}
		}).fail(function(jqXHR, textStatus){
			dataLoaded = '';
			jQuery('#buy-now-loader').addClass('hide');
			jQuery('body').removeClass('disabled');
		});
	}
}

/* Shipping Drop down ajax request */
 jQuery(document).on('click','input[name=estimate_method]', function(e){
	var shipping	=	jQuery(this).val();
	jQuery('#buy-now-loader').removeClass('hide');
	jQuery('body').addClass('disabled');
	jQuery.ajax({
		data: { estimate_method :  shipping },
		url: location.protocol+'//'+location.hostname+'/checkout/cart/updatePost/',
		success:function(html)
		 {
			try{
				var res = html.evalJSON();
				if(res && typeof res.content != 'undefined' && document.getElementById('shopping-cart-totals-table')) { 
					document.getElementById('shopping-cart-totals-table').parentNode.innerHTML = res.content;
				}
				if(res && typeof res.deliveryDate != 'undefined' && res.deliveryDate.length > 0) {
					for(var i=0;i<res.deliveryDate.length;i++) {
						if(typeof res.deliveryDate[i].id != 'undefined' && typeof res.deliveryDate[i].content != 'undefined' && res.deliveryDate[i].content != false) {
							if(document.getElementById(res.deliveryDate[i].id)) {
								document.getElementById(res.deliveryDate[i].id).innerHTML = res.deliveryDate[i].content;
							}	
						}
					}
				}
			} catch(e) {
				console.log(e);
				/* window.location = window.location; */
				return;
			}
			
			jQuery('#buy-now-loader').addClass('hide');
			jQuery('body').removeClass('disabled');
		}
   });
   return false;
});

/* Abandon Cart Mail Chimp Capture Request */
jQuery(function(){
	jQuery.get('/checkout/cart/abandoncartmailchimp', function(result){
	});
});

/* Add Chain and Remove Chain jewelry as a gift */
jQuery(document).on('click','input[type="checkbox"]', function(){
	var chainValue = jQuery(this).val();
	var chainData = 'sku/'+chainValue;
	
	if(jQuery(this).parent('.afc').find('.mac-check').hasClass('hide')){
		jQuery(this).parent('.afc').find('.mac-check').removeClass('hide');
	}else{
		jQuery(this).parent('.afc').find('.mac-check').addClass('hide');
	}
	
	if(jQuery(this).parent('.afc').find('.mac-uncheck').hasClass('hide')){
		jQuery(this).parent('.afc').find('.mac-uncheck').removeClass('hide');
	}else{
		jQuery(this).parent('.afc').find('.mac-uncheck').addClass('hide');
	}
	
	if(jQuery(this).is(":checked")){
		var url = location.protocol+'//'+location.hostname+'/checkout/cart/addchain/'+chainData;		
	}else if(jQuery(this).is(":not(:checked)")){
		var url = location.protocol+'//'+location.hostname+'/checkout/cart/removechain/'+chainData;
	}
	setTimeout(function(){
		jQuery(location).attr('href',url);
	},100)
});

jQuery('td').click(function(){
	if($(this).hasAttribute('data-class')){
		var elem = jQuery(this).attr('data-class');
		jQuery('tr[data-class=open-'+elem+']').slideToggle(300);
		jQuery(this).find('i').toggleClass('fa-angle-down fa-angle-up');
	}
});

function vrsn_splash(){sw=window.open('https://trustsealinfo.websecurity.norton.com/splash?form_file=fdf/splash.fdf&dn=www.angara.com&lang=en','VRSN_Splash','location=yes,status=yes,resizable=yes,scrollbars=yes,width=560,height=500');sw.focus();}