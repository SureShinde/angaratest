// JavaScript Document

(function( $ ){
	$.fn.JewelProduct = function( config ) {
		
		var module = this,
		vendorLeadTime = 0,
		settings = {
			hasAppraisalSelected: false,
			hasWarrantySelected: false,
			hasEngravingSelected: false,
			vendorLeadTime: 4,
		  	// add default settings here
		};
		
		module.extendedShippingDays = 0;
		
		module.setup = function(config){
			settings = $.extend( settings, config );
			module.setVendorLeadTime(settings.vendorLeadTime);
			
			$(function(){
				module.dialogBox = $('<div class="popupDialog">').appendTo('body');
				$('.help-info-content').click(function(e){
					$('.help-info-content').fadeOut();
					e.stopPropagation();
				});
				
				$('.help-info').click(function(e){
					e.stopPropagation();
					$('.popupDialog').fadeOut();
					$('.help-info-content').fadeOut();
					module.showHelpInfo($(this));
				});
				
				//module.setupSocialNetworking();
				
				$('body').click(function(){
					$('.help-info-content').fadeOut();
				});
			})
			/*module.dialogBox = $('<div>').dialog({
				appendTo: 'body',
				autoOpen: false,
				autoResize:true,
				modal: true,
				show: {
					effect: "blind",
					duration: 1000
				},
				hide: {
					effect: "blind",
					duration: 1000
				}
			});*/
			
			return module;
		}
		
		module.setAppraisal = function($value){
			settings.hasAppraisalSelected = $value;
		}
		
		module.getAppraisal = function(){
			return settings.hasAppraisalSelected;
		}
		
		module.setEngraving = function($value){
			settings.hasEngravingSelected = $value;
		}
		
		module.getEngraving = function(){
			return settings.hasEngravingSelected;
		}
		
		module.setWarranty = function($value){
			settings.hasWarrantySelected = $value;
		}
		
		module.getWarranty = function(){
			return settings.hasWarrantySelected;
		}
		
		module.getExtendedShippingDays = function(){
			var days = 0;
			if(settings.hasEngravingSelected)
				days++;
			if(settings.hasAppraisalSelected)
				days++;
			return days;
		}
		
		module.setVendorLeadTime = function(vendorLeadTime){
			settings.vendorLeadTime = parseFloat(vendorLeadTime);
		}
		
		module.updateShippingDate = function( ){
			if((settings.vendorLeadTime + module.getExtendedShippingDays()) <= 5){
				$('#dyn_arrive-date').text(settings.shippingDates[ settings.vendorLeadTime + module.getExtendedShippingDays() + 4]);
				$('#valentineDate').show();
			}
			else{
				$('#dyn_arrive-date').text();
				$('#valentineDate').hide();				
			}
			
			$('.dyn_vendor_lead_time').text(settings.shippingDates[ settings.vendorLeadTime + module.getExtendedShippingDays()]);
		}
		
		module.showHelpInfo = function(parent){
			parent.find('.help-info-content').stop(true, true).fadeIn();
		}
				
		return module;
		
	};
})( jQuery );

var jewelProduct = jQuery('<div id="jewel-product"></div>').JewelProduct();
