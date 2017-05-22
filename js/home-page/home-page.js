/* Angara Home page Js by Hitesh Agarwal */
	var banner_link = '/c/';
	var banner_step_1 = '#';

	jQuery(function(){
    	// home page slider
		jQuery('#slider1').mastslider({speed:800});        
	});	
	
	jQuery(function(){
		jQuery('.showoff-content').jshowoff({controlText:{play:'', pause:'', previous:'', next:''}});
	});
	
	function getHomeSearchOption(id) {
		searchCatVal = document.getElementById("SearchCat").options[document.getElementById("SearchCat").selectedIndex].value;
		searchStoneVal = document.getElementById("de_stone_type").options[document.getElementById("de_stone_type").selectedIndex].value;
		searchPriceVal = document.getElementById("price_range_opt").options[document.getElementById("price_range_opt").selectedIndex].value;
		
		if(id==0){
			jQuery.get('ajaxact/ajax_home_search_option.php?section=0&cat_id='+searchCatVal, function(data) {
			  	document.getElementById("gemSelection").innerHTML = data;				  
			});
			
			jQuery.get('ajaxact/ajax_home_search_option.php?section=1&cat_id='+searchCatVal+'&option_id=0', function(data) {			  
			  	document.getElementById("priceSelection").innerHTML = data;	
			});
		}
		else{
			jQuery.get('ajaxact/ajax_home_search_option.php?section=1&cat_id='+searchCatVal+'&option_id='+searchStoneVal, function(data) {			  
			  	document.getElementById("priceSelection").innerHTML = data;	
			});	
		}		
	}
	
	function SubmitHomeSearch() {
		searchCatVal = document.getElementById("SearchCat").options[document.getElementById("SearchCat").selectedIndex].value;
		searchStoneVal = document.getElementById("de_stone_type").options[document.getElementById("de_stone_type").selectedIndex].value;
		searchPriceVal = document.getElementById("price_range_opt").options[document.getElementById("price_range_opt").selectedIndex].value;
		
		clause = '';
		actPageVal = '/catalogsearch/result/?q=';
		switch(searchCatVal) {
			case '264':				
				searchCatVal = 'Rings';
				actPageVal = '/rings.html?search=1';
				break;
			case '5':				
				searchCatVal = 'Engagement+Rings';
				actPageVal = '/engagement-rings.html?search=1';
				break;
			case '266':				
				searchCatVal = 'Pendants';
				actPageVal = '/pendants.html?search=1';
				break;
			case '267':				
				searchCatVal = 'Earrings';
				actPageVal = '/earrings.html?search=1';
				break;
			case '270':				
				searchCatVal = 'Bracelets';
				actPageVal = '/bracelets.html?search=1';
				break;
			case '227':				
				searchCatVal = 'Loose Gemstones';
				actPageVal = '/loose-gemstones.html?search=1';
				break;
			case '279':				
				searchCatVal = 'Stud Earrings';
				actPageVal = '/rings.html?search=1';
				break;	
			case '73':				
				searchCatVal = 'Solitaire Pendants';
				actPageVal = '/rings.html?search=1';
				break;			
			default:
				searchCatVal = 'Gemstone Jewelry';
				actPageVal = '/catalogsearch/result/?q=Gemstone+Jewelry';
				break;
		}
		
		if(searchStoneVal!=''){
			clause = clause+'&de_stone_type='+searchStoneVal;	
		}
		else{
			clause = clause+'&de_stone_type=clear';	
		}
		
		if(searchPriceVal!=''){
			clause = clause+'&price='+searchPriceVal;	
		}
		else{
			clause = clause+'&price=clear';		
		}
		window.location.href = server_url_val + actPageVal + clause + '&order=position&dir=asc&no_cache=true';
	}

	jQuery(function(){
		jQuery('.custom-scroll').mCustomScrollbar({
			scrollButtons:{
				enable:true,
			},
			callbacks:{
				onScroll: function (){}
			},
			advanced:{ 
				updateOnContentResize:true
			}
		});		
	});	
	
	// Form validation by Vaseem
	var customForm = new VarienForm('newsletter-secret-deal', false); //Edited by pankaj regarding validation.
	
	jQuery(function(){
		jQuery('#secretSubscribe').click(function(){
			if(jQuery('#secretSubscribe').is(":checked")){
				jQuery("#l_code").val('1');
			}
			else{
				jQuery("#l_code").val('0');
			}
		});
	
		// On blur effect on text box
		jQuery('#emaildealoftheday').click(function(){
			value 		= 	jQuery.trim(jQuery("#emaildealoftheday").val());
			if(value == 'Enter Your Email Address'){
				jQuery("#emaildealoftheday").val('');
			}
		});
		
		jQuery('#emaildealoftheday').focusout(function(){
			value 		= 	jQuery.trim(jQuery("#emaildealoftheday").val());
			if(value == ''){
				jQuery("#emaildealoftheday").val('Enter Your Email Address');
			}
		});
		
		//Edited by pankaj regarding validation of success message.
		jQuery('#deal-submit-btn').click(function(){
			jQuery('#successmessage_deal').hide();			
		});		
	});

	/* start recently viewed items on home page */
	jQuery(function(){
		var currentRecentViewed = 1;
		var totalRecentViewed = jQuery('#recentviewed-items').width() / 260;
		jQuery('#recentviewed-items-right').click(function(){
			if(currentRecentViewed < totalRecentViewed){
				currentRecentViewed++;
				jQuery('#recentviewed-items').stop(true,true).animate({left:'-=260'});
			}
			else{
				currentRecentViewed = 1;
				jQuery('#recentviewed-items').stop(true,true).animate({left:0});
			}
		});
		
		jQuery('#recentviewed-items-left').click(function(){
			if(currentRecentViewed > 1){
				currentRecentViewed--;
				jQuery('#recentviewed-items').stop(true,true).animate({left:'+=260'});
			}
			else{
				currentRecentViewed = totalRecentViewed;
				jQuery('#recentviewed-items').stop(true,true).animate({left:-(jQuery('#recentviewed-items').width() - 260)});
			}
		});		
	});
	/* end recently viewed items on home page */	

	/* start recently sold items on home page */
	jQuery(function(){
		var currentRecentSold = 1;
		var totalRecentSold = jQuery('#recentsold-items').width() / 211;
		jQuery('#recentsold-items-right').click(function(){
			if(currentRecentSold < totalRecentSold){
				currentRecentSold++;
				jQuery('#recentsold-items').stop(true,true).animate({left:'-=211'});
			}
			else{
				currentRecentSold = 1;
				jQuery('#recentsold-items').stop(true,true).animate({left:0});
			}
		});
		
		jQuery('#recentsold-items-left').click(function(){
			if(currentRecentSold > 1){
				currentRecentSold--;
				jQuery('#recentsold-items').stop(true,true).animate({left:'+=211'});
			}
			else{
				currentRecentSold = totalRecentSold;
				jQuery('#recentsold-items').stop(true,true).animate({left:-(jQuery('#recentsold-items').width() - 211)});
			}
		});
	});
	/* end recently sold items on home page */