	var dataForm = new VarienForm('frm-email-capture-popup_global', false);
	var dataForm = new VarienForm('frm-newsletter-validate-cat', false);
	function setWishlistUrlSession(wishurl)
	{
		jQuery.get('/catalog/product/setredirectsessionurl/?wishurl='+wishurl, function(data) {
		  //
		});	
	}
	
	function shownewspopup() 
	{
		if(document.getElementById("div_news_popup"))
		{
			document.getElementById("div_news_popup").style.display = "block" ;
			a = jQuery.get('/newsletter/popup/popuphandle/',{},abc);
		}
	}
	
	function abc(data)
	{
			
	}
	
	function closepopup() {
		if(document.getElementById("div_news_popup")){
			document.getElementById("div_news_popup").style.display = "none" ;
		}
	}
		
	function getnewspopupsession()
	{
		a = jQuery.get('/newsletter/popup/getnewspopupsession/',{},getnewspopupsessionresponse);	
	}
	function getnewspopupsessionresponse(data)
	{
		var ua = navigator.userAgent.toLowerCase();
		//var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
		
		if(ua.indexOf("android") != -1){
			// Do Nothing!
		}else if(ua.indexOf("mobile") != -1){
			// Do Nothing! 	 	
		}else{			
			if(data=="0")
			{
				setTimeout("shownewspopup()", 5000); // 5 seconds
			}
		}	
	}    
	
	jQuery(function(){
		jQuery('#official-rules-link').hover(
			function(){
				jQuery('#official-rules-popup').show();
			},
			function(){
				jQuery('#official-rules-popup').hide();
			}
		)
	})
	
	// start country.phtml file javascript
	jQuery(document).ready(function()
	{
	jQuery(".account").click(function()
	{
	var X=jQuery(this).attr('id');
	if(X==1)
	{
	jQuery(".submenu").hide();
	jQuery(this).attr('id', '0');	
	}
	else
	{
	jQuery(".submenu").show();
	jQuery(this).attr('id', '1');
	}	
	});
	//Mouseup textarea false
	jQuery(".submenu").mouseup(function()
	{
	return false
	});
	jQuery(".account").mouseup(function()
	{
	return false
	});
	//Textarea without editing.
	jQuery(document).mouseup(function()
	{
	jQuery(".submenu").hide();
	jQuery(".account").attr('id', '');
	});	
	});
	
	function showcountrypopup(show)	
	{
		if(show=='1') {
			jQuery('#country-popup').removeClass('hide');
			jQuery('#popuparrow').removeClass('hide');
			jQuery("#country-popup").focus();
		}else{
			if(jQuery('#country-popup').hasClass('hide'))
			{
				jQuery('#country-popup').removeClass('hide');
				jQuery('#popuparrow').removeClass('hide');
				jQuery("#country-popup").focus();
			}else  {
				jQuery('#country-popup').addClass('hide');
				jQuery('#popuparrow').addClass('hide');
			}
		}
	}
	
	function executeCountryUrl(urlvaltop)
	{
		if(urlvaltop != ''){	
			window.location.href = urlvaltop;
		}else{
			window.location.href = '/';
		}	
	}	
	// end country.phtml file javascript
	
	// start shipping.phtml file javascript
	
	function showshippingpopup() 
	{
		if(document.getElementById("div_shippinginfo_popup"))
		{
			document.getElementById("div_shippinginfo_popup").style.display = "block" ;
			a = jQuery.get('/shipping/popup/popuphandle/',{},abc);
		}
	}
	
	function closeshippingpopup() {
		if(document.getElementById("div_shippinginfo_popup")){
			document.getElementById("div_shippinginfo_popup").style.display = "none" ;
		}
		getnewspopupsession();
	}
	
	function getshippingpopupsession()
	{
		a = jQuery.get('/shipping/popup/getshippingpopupsession/',{},getshippingpopupsessionresponse);
		
	}
	function getshippingpopupsessionresponse(data)
	{
		if(data=="0")
		{
			setTimeout("showshippingpopup()", 5000); // 10 seconds
		}
	}
	
	/* start cookie popup */
	
	function getcookiepopupsession()
	{
		cookie_popup_val = jQuery.get('/newsletter/popup/getcookiepopupsession/',{},getcookiepopupsessionresponse);	
	}
	function setcookiepopupsession()
	{
		set_cookie_popup_val = jQuery.get('/newsletter/popup/setcookiepopupsession/');	
	}
	
	function getcookiepopupsessionresponse(data){
		if(data=="0")
		{		
			setcookiepopupsession();
			if(countryCodeShipPopup=='us') {
				getnewspopupsession();
			}else {
				getshippingpopupsession();
			}
		}
	}
	
	function closecookiepopup() {	
		setcookiepopupsession();
		if(countryCodeShipPopup=='us') {
			getnewspopupsession();
		}else {
			getshippingpopupsession();
		}
	}	
	getcookiepopupsession();		
	// end shipping.phtml file javascript