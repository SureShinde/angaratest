jQuery(function(){
	jQuery('.search-icon').click(function(){
		if(jQuery("input[name='q']").val() != '') {
			jQuery("#search_mini_form").submit();
		}	
	});	
	
	jQuery('.m-listmenu').click(function() {
		jQuery('.drop-down-list').toggle();
	});
	
	jQuery('.gridview').click(function(){
		jQuery('#catalog-view').removeClass('productlistview').addClass('productgridview');
		jQuery('.listview').removeClass('listviewselected');
		jQuery('.gridview').addClass('gridviewselected');
		jQuery('.catproductlist div').removeClass('arrive-text-list');
		jQuery('.catproductlist div').addClass('arrive-text-grid');
	});
	
	jQuery('.listview').click(function(){
		jQuery('#catalog-view').addClass('productlistview').removeClass('productgridview');
		jQuery('.gridview').removeClass('gridviewselected');
		jQuery('.listview').addClass('listviewselected');
		jQuery('.catproductlist div').removeClass('arrive-text-grid');
		jQuery('.catproductlist div').addClass('arrive-text-list');
	});

	jQuery('.pricerange').each(function(){
		jQuery(this).find('.currencysymbol').width(jQuery(this).parent().width()).css('display','');
	});
	
	jQuery('.writeareviewlink').click(function(){
		jQuery('#write-customer-reviews').show();
	});
		
	jQuery('.parent-category li, .drop-down-list li').click(function(){
		var index = jQuery(this).index();
		jQuery('.parent-category').hide();
		jQuery('.landingcategory_list').hide();
		jQuery('.landingcategory_list:eq('+index+')').show();
		jQuery('.search-wrapper').removeClass('search-wrapper-home');
		jQuery('.search-input-box').removeClass('home-search-input');
		jQuery('.search-icon').removeClass('search-icon-home');
		jQuery('.m-banner').hide();
		jQuery('#adj-nav-container').hide();
		jQuery('.bottomcategories').show();
		
		if(jQuery('.main-container').show()){
			jQuery('.main-container').hide();
		}
		if(jQuery('.productpagewrapper').show()){
			jQuery('.productpagewrapper').hide();
			jQuery('.mage-iphone').attr('style','margin:0;');
		}
		if(jQuery('.cart').show()){
			jQuery('.cart').hide();
		}
		if(jQuery('.checkoutbg').show()){
			jQuery('.checkoutbg').hide();
		}
		if(jQuery('.staticrightpart').show()){
			jQuery('.staticrightpart').hide();
		}
		if(jQuery('#engagementbodyhome').show()){
			jQuery('#engagementbodyhome').hide();
		}
		if(jQuery('.my-account').show()){
			jQuery('.my-account').hide();
		}
		
		jQuery('#catpagestaticbanner').show();
		
		if(jQuery('.m-pageback').length == 0) {
			jQuery('<a class="appended" href="javascript:void(0);"><div class="m-pageback"></div></a>').prependTo('.append-container')
			.click(function(){
				jQuery('.landingcategory_list').hide();
				jQuery('.parent-category').show();
				jQuery(this).remove();
				jQuery('.search-wrapper').addClass('search-wrapper-home');
				jQuery('.search-icon').addClass('search-icon-home');
				jQuery('.search-input-box').addClass('home-search-input');
				jQuery('.mage-iphone').removeAttr('style');
				jQuery('.m-banner').show();
				jQuery('#catpagestaticbanner').hide();
			});
		}
	});
	
	if(!isHome){
		jQuery('.bottomcategories').hide();
	}
});