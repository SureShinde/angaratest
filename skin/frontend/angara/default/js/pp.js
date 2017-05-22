function addAppraisal(){
	jQuery('.toggle-jewelry-appraisal').addClass('active');
	jQuery('#jewelryappraisal').prop('checked', true);
	jewelProduct.setAppraisal(true);
	jQuery('#nonjewelry').show();
	jewelProduct.updateShippingDate();
	//updateSelectedAddons();
}

function removeAppraisal(){
	jQuery('.toggle-jewelry-appraisal').removeClass('active');
	jQuery('#jewelryappraisal').prop('checked', false);
	jewelProduct.setAppraisal(false);
	jQuery('#nonjewelry').hide();
	jewelProduct.updateShippingDate();
	//updateSelectedAddons();
}

function addInsurance(){
	jQuery('.toggle-jewelry-insurance').addClass('active');
	jQuery('#insurance').prop('checked', true);
	jewelProduct.setWarranty(true);
	//updateSelectedAddons();
}

function removeInsurance(){
	jQuery('.toggle-jewelry-insurance').removeClass('active');
	jQuery('#insurance').prop('checked', false);
	jewelProduct.setWarranty(false);
	//updateSelectedAddons();
}

var sideView;

function setupMoreViews(){
	sideView = {
		currentView: 0,
		totalViewsToShow: 4,
		totalViews: jQuery('#moreviews li').length,
		sideViewHeight: jQuery('#moreviews li').outerHeight(true),
		containerHeight: jQuery('#moreviews').height()
	};
	if(sideView.sideViewHeight * sideView.totalViews > sideView.containerHeight){
		jQuery('#more-views-down').addClass('active');
		jQuery('#more-views-up').removeClass('active');
	}
	else{
		jQuery('#more-views-down').removeClass('active');
		jQuery('#more-views-up').removeClass('active');
	}
}

function updateMoreViews(){
	if(typeof(sideView) != 'undefined'){
		sideView.totalViews = jQuery('#moreviews li').length;
		sideView.currentView = 0;
	}
	if(sideView.sideViewHeight * sideView.totalViews > sideView.containerHeight){
		jQuery('#more-views-down').addClass('active');
		jQuery('#more-views-up').removeClass('active');
	}
	else{
		jQuery('#more-views-down').removeClass('active');
		jQuery('#more-views-up').removeClass('active');
	}
	
	jQuery('#moreviews ul').stop(true,true).animate({top: 0})
}

/*function updateSelectedAddons(){
	var selectedText = [];
	
	if(jewelProduct.getAppraisal()){
		selectedText.push('<div class="selected-addon">Jewelry Appraisal</div>');
	}
	
	if(jewelProduct.getWarranty()){
		selectedText.push('<div class="selected-addon">Warranty</div>');
	}
	
	if(jewelProduct.getEngraving()){
		selectedText.push('<div class="selected-addon">Engraving</div>');
	}
	
	if(selectedText.length != 0){
		jQuery('#user-selection-addons').html('<div class="detailrow-addon"><div>Selected Add-Ons:</div>'+selectedText.join('<div class="addon-seperator">/</div>')+'<div class="clear"></div></div>');
	}
	else{
		jQuery('#user-selection-addons').html('');
	}
}*/

jQuery(function(){
	jQuery('.certpopup').click(function(){
		jQuery('.appraisalcertimg').show();
	});
	
	jQuery('.appraisalclose').click(function(){
		jQuery('.help-info-content-close').hide();
	});
		
	setupMoreViews();	
	jQuery('#more-views-down').click(function(){
		if(sideView.currentView + sideView.totalViewsToShow < sideView.totalViews ){
			sideView.currentView++;
			jQuery('#moreviews ul').stop(true,true).animate({top: - (sideView.currentView * sideView.sideViewHeight)})
			jQuery('#more-views-up').addClass('active');
			if((sideView.currentView + sideView.totalViewsToShow) == sideView.totalViews){
				jQuery('#more-views-down').removeClass('active');
			}
		}
	});
	
	jQuery('#more-views-up').click(function(){
		if(sideView.currentView > 0 ){
			sideView.currentView--;
			jQuery('#moreviews ul').stop(true,true).animate({top: - (sideView.currentView * sideView.sideViewHeight)})
			jQuery('#more-views-down').addClass('active');
			if(sideView.currentView ==0){
				jQuery('#more-views-up').removeClass('active');
			}
		}
	});
	
	<!-- S: AddOn Selected-->
	jQuery('.toggle-jewelry-appraisal').click(function(){
		if(jewelProduct.getAppraisal()){
			removeAppraisal();
			
			/*if(checkUserAgent('iPad')){
				jQuery('.addon-option').removeClass('addon-option:hover');
			}*/
		}
		else{
			addAppraisal();
		}
	});
	
	jQuery('.toggle-jewelry-insurance').click(function(){
		if(jQuery('#insurance').is(':checked')){
			removeInsurance();
			
			/*if(checkUserAgent('iPad')){
				jQuery('.addon-option').removeClass('addon-option:hover');
			}*/
		}
		else{
			addInsurance();
			customLinkTracking('Insurance Clicked');
		}
	});	
	<!-- E: AddOn Selected--> 
	
	jQuery('#product-info-tabs li').click(function(){
		if(!jQuery(this).hasClass('active')){
			jQuery('#product-info-tabs li').removeClass('active');
			var index = jQuery(this).addClass('active').index();
			jQuery('.product-tab-content').hide();
			jQuery('.product-tab-content:eq('+index+')').show();
			
			if(typeof s != 'undefined'){
				productTabSwitch(jQuery(this).find('span').attr('title'));
			}
		}
	});
	
	jQuery('.writeareviewlink').click(function(){
		jQuery('#product-info-tabs li').removeClass('active');
		jQuery('#product-info-tabs li:eq(1)').addClass('active');
		jQuery('.product-tab-content').hide();
		jQuery('.product-tab-content:eq(1)').show();
		jQuery('#write-customer-reviews').show();
	});
	
	jQuery('#show-review-btn').click(function(){
		jQuery('#product-info-tabs li').removeClass('active');
		jQuery('#product-info-tabs li:eq(1)').addClass('active');
		jQuery('.product-tab-content').hide();
		jQuery('.product-tab-content:eq(1)').show();
	});	
	
	<!-- S: Request Shipping Label -->
	jQuery("#requestlabel-dialog").dialog({
		autoOpen: false,
		show: "blind",
		hide: "explode",			
		width:450,
		zIndex:11000,
		resizable:false,
		draggable:false,
		minHeight:390
	});
	 
	jQuery("#returnshipping-dialog-opener").click(function() {
		jQuery('#resultsuccess').hide();
		jQuery('#formrequestlabel').each (function(){  
			this.reset();
		});
		jQuery('#formrequestlabel').show();
		jQuery("#requestlabel-dialog").dialog("open");
		if(jQuery("#requestlabel-dialog").data('loaded')!== 'true'){
			jQuery("#requestlabel-dialog").load('/popup/index/openreturnshippinglabel',function(){					
				dataForm = new VarienForm('formrequestlabel', true);
				dataForm.validator.options.onFormValidate = function (result, form){}
				jQuery('#formrequestlabel').submit(function(){
					return submitReturnShippingLabel();
				});
			});
			jQuery("#requestlabel-dialog").data('loaded','true'); 
		}
		return false;
 	});
	<!-- E: Request Shipping Label -->
	
	
	/*jQuery('#pckImg').click(function(){
		jQuery('#moreviews').find('ul li').removeClass('active');
		jQuery(this).addClass('active');
	});*/
	
	if(jQuery('#moreviews').find('ul li').length > 0){
		jQuery('#moreviews').find('ul li').removeClass('active');
		jQuery('#moreviews').find('ul li:first').addClass('active');
		
		jQuery('#moreviews').find('ul li').click(function(){
			jQuery('#moreviews').find('ul li').removeClass('active');
			jQuery(this).addClass('active');
		});
	}
	
	if(jQuery('#mycarousel1').find('li').length > 5){
		jQuery('#mycarousel1').data('currentIndex',0);
		jQuery('#mycarousel1').append(
			jQuery('<div></div>').addClass('jcarousel-prev').click(function(){
				if(jQuery('#mycarousel1').data('currentIndex') > 0){
					jQuery('#mycarousel1').data('currentIndex',jQuery('#mycarousel1').data('currentIndex') - 1);
					jQuery('#mycarousel1 ul').animate({ left: -jQuery('#mycarousel1 li').outerWidth() * jQuery('#mycarousel1').data('currentIndex') });
				}
				if(jQuery('#mycarousel1').data('currentIndex') > 0){
					jQuery('.jcarousel-prev').addClass('active');
				}
				else{
					jQuery('.jcarousel-prev').removeClass('active');
				}
				if(jQuery('#mycarousel1').data('currentIndex') < jQuery('#mycarousel1').find('li').length - 5){
					jQuery('.jcarousel-next').addClass('active');
				}
				else{
					jQuery('.jcarousel-next').removeClass('active');
				}
			})
		);

		jQuery('#mycarousel1').append(
			jQuery('<div></div>').addClass('jcarousel-next').addClass('active').click(function(){
				if(jQuery('#mycarousel1').data('currentIndex') < jQuery('#mycarousel1').find('li').length - 5){
					jQuery('#mycarousel1').data('currentIndex', jQuery('#mycarousel1').data('currentIndex') + 1);
					jQuery('#mycarousel1 ul').animate({ left: -jQuery('#mycarousel1 li').outerWidth() * jQuery('#mycarousel1').data('currentIndex') });
				}
				if(jQuery('#mycarousel1').data('currentIndex') > 0){
					jQuery('.jcarousel-prev').addClass('active');
				}
				else{
					jQuery('.jcarousel-prev').removeClass('active');
				}
				if(jQuery('#mycarousel1').data('currentIndex') < jQuery('#mycarousel1').find('li').length - 5){
					jQuery('.jcarousel-next').addClass('active');
				}
				else{
					jQuery('.jcarousel-next').removeClass('active');
				}
			})
		);
	}	
});

<!-- S: Request Shipping Label -->
function submitReturnShippingLabel(){
	if(dataForm.validator.validate()===true){
		jQuery('#LoadingImage').show();
		jQuery.post('/popup/index/requestreturnshippinglabel',jQuery('#formrequestlabel').serialize(),function(res){	
			jQuery('#LoadingImage').hide();			
			jQuery('#resultsuccess').html(res);
			jQuery('#formrequestlabel').hide();
			jQuery('#resultsuccess').show();
		});
		setTimeout(function(){
			  jQuery('#requestlabel-dialog').dialog('close');               
		}, 5000);
		return false;
	}
	return false;
}
<!-- E: Request Shipping Label -->

<!-- S: Estimated Ship Engraving-->
function estimateShipEngraving(){
	if(jQuery('#engraveringchk').is(":checked")){
		jewelProduct.setEngraving(true);
		//updateSelectedAddons();
		
		/*if(checkUserAgent('iPad')){
			jQuery('.addon-option').removeClass('addon-option:hover');
		}*/
		//jQuery('#ultimate-engraving').addClass('active');
	}
	else{
		jewelProduct.setEngraving(false);
		//updateSelectedAddons();
		//jQuery('#ultimate-engraving').removeClass('active');
	}	
	jewelProduct.updateShippingDate();
}

jQuery(document).ready(function(){
	jQuery('#jewelryappraisal').click(function(){
		estimateShipEngraving();
	});
});
<!-- E: Estimated Ship Engraving-->

/*function checkUserAgent(vs){
	var pattern = new RegExp(vs, 'i');
	return !!pattern.test(navigator.userAgent);
}*/