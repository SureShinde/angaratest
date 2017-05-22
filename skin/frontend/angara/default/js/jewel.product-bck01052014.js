// JavaScript Document

(function( $ ){
	$.fn.JewelProduct = function( config ) {
		
		var module = this,
		vendorLeadTime = 0,
		settings = {
				hasAppraisalSelected: false,
				hasEngravingSelected: false,
				vendorLeadTime: 4,
			  // add default settings here
			};
		
		module.extendedShippingDays = 0;
		
		module.setup = function(config){
			settings = $.extend( settings, config );
			module.setVendorLeadTime(settings.vendorLeadTime);
			$(function(){
				module.dialogBox = $('<div class="popupDialog">').appendTo('.ppbottom');
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
		
		module.setEngraving = function($value){
			settings.hasEngravingSelected = $value;
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
		
		module.showStonePopup = function(type, stoneName, stoneShape){
			var popupUrl = '';
			switch(type){
				case 'stone size':
					popupUrl = '/hprcv/qualitycompare/getweightchart/?stonetype='+ stoneName +'&stoneshape=' + stoneShape;
					break;
				case 'stone quality':
					popupUrl = '/hprcv/qualitycompare/get/?stonetype='+ stoneName +'&stoneshape=' + stoneShape;
					break;
			}
			jQuery.ajax({
				url: popupUrl,
				success: function(matter){
					module.dialogBox.html(matter).show();
					jQuery('.popupboxcross').click(function(){
						module.dialogBox.hide();
					})
				}
			});
		}
				
		return module;
		
	};
})( jQuery );

var jewelProduct = jQuery('<div id="jewel-product"></div>').JewelProduct();
