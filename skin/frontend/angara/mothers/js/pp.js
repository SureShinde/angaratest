function showDiveSection(activeDiv){
		
	/*if(document.getElementById("productdescription"))document.getElementById("productdescription").className="alltab";
	if(document.getElementById("viewmoreoptions"))document.getElementById("viewmoreoptions").className="alltab";
	if(document.getElementById("productdetails"))document.getElementById("productdetails").className="alltab";
	if(document.getElementById("shippingreturns"))document.getElementById("shippingreturns").className="alltab";
	if(document.getElementById("angaradifference"))document.getElementById("angaradifference").className="difference";
	if(document.getElementById("productreview"))document.getElementById("productreview").className="alltab";*/
	
	if(activeDiv=="product-description" && !jQuery('#productdescription').hasClass('activeted1')){
		jQuery('#detailstab li').addClass('alltab').removeClass('activeted1').removeClass('activeted2');
		document.getElementById("productdescription").className="activeted1";
		if(typeof s != 'undefined'){
			productTabSwitch('Product Details')
		}
	}
	if(activeDiv=="product-recentlyviewed" && !jQuery('#recentlyviewed').hasClass('activeted1')){
		jQuery('#detailstab li').addClass('alltab').removeClass('activeted1').removeClass('activeted2');
		document.getElementById("recentlyviewed").className="activeted1";
		if(typeof s != 'undefined'){
			productTabSwitch('Recently Viewed Items')
		}
	}
	if(activeDiv=="shipping-returns" && !jQuery('#shippingreturns').hasClass('activeted1')){
		jQuery('#detailstab li').addClass('alltab').removeClass('activeted1').removeClass('activeted2');
		document.getElementById("shippingreturns").className="activeted1";
		if(typeof s != 'undefined'){
			productTabSwitch('Shipping Returns')
		}
	}
	if(activeDiv=="angara-difference" && !jQuery('#angaradifference').hasClass('activeted2')){
		jQuery('#detailstab li').addClass('alltab').removeClass('activeted1').removeClass('activeted2');
		document.getElementById("angaradifference").className="activeted2";
		if(typeof s != 'undefined'){
			productTabSwitch('Angara Difference')
		}
	}
	if(activeDiv=="product-review" && !jQuery('#productreview').hasClass('activeted1')){
		jQuery('#detailstab li').addClass('alltab').removeClass('activeted1').removeClass('activeted2');
		document.getElementById("productreview").className="activeted1";
		if(typeof s != 'undefined'){
			productTabSwitch('Product Review')
		}
	}
	
	if(document.getElementById("product-description"))document.getElementById("product-description").style.display="none";
	if(document.getElementById("more-options"))document.getElementById("more-options").style.display="none";
	if(document.getElementById("product-details"))document.getElementById("product-details").style.display="none";
	if(document.getElementById("shipping-returns"))document.getElementById("shipping-returns").style.display="none";
	if(document.getElementById("angara-difference"))document.getElementById("angara-difference").style.display="none";
	if(document.getElementById("product-review"))document.getElementById("product-review").style.display="none";	
	if(document.getElementById(activeDiv))document.getElementById(activeDiv).style.display="block";
}




jQuery(function(){
				
	
	
	jQuery('.writeareviewlink').click(function(){
		showDiveSection('product-review');
		jQuery('html,body').animate({
			scrollTop: jQuery("#write-customer-reviews").offset().top},
        'slow');
	})
	
})