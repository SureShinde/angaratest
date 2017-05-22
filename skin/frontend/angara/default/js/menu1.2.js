// start top.phtml javascript
	jQuery(function(){
		jQuery('.customerdiscountlink').click(
			function(){
				jQuery('.customerdiscountbtn').hide();
				jQuery('.searchbarbox').width('100%');
				jQuery('html, body').animate({ scrollTop: 0 }, 0);
				jQuery('.customerdiscountarea').slideDown(400);				
			}
		)
		jQuery('<img>').src = '../images/new_member_screen/accountloginbg.jpg';	
		
		if(jQuery('.submenu').length > 0){
			jQuery('#countrybox').click(
				function(){
					jQuery('html, body').animate({ scrollTop: jQuery(document).height() }, 'slow');
					jQuery('.account').trigger('click');
					jQuery('.submenu').show();
				}
			)
		}
		else{
			jQuery('.countryflagheader').css('cursor','default');
		}
	})
	
	function showSelectedMemberFormOption(customertype){
		if(customertype=='new'){
			jQuery('#registerblock').show();
			jQuery('#loginblock').hide();
			jQuery('#oldmemberlink').show();
			jQuery('#newmemberlink').hide();
		}else{
			jQuery('#registerblock').hide();
			jQuery('#loginblock').show();
			jQuery('#oldmemberlink').hide();
			jQuery('#newmemberlink').show();
		}		
	}
	/* S: Thankyou Popup on home page */
	function closethankspopup() {
		if(document.getElementById("thanks-wrapper")){
			document.getElementById("thanks-wrapper").style.display = "none";
			document.getElementById("layoverlayer").style.display = "none" ;
		}
	}
	
	jQuery(function(){
		jQuery(window).resize(function () {
  			var box = jQuery('#thanks-wrapper #thanks-main');
 
        	//Get the screen height and width
        	var layoverlayerHeight = jQuery(document).height();
			var layoverlayerWidth = jQuery(window).width();
		  
			//Set height and width to mask to fill up the whole screen
			jQuery('#layoverlayer').css({'width':layoverlayerWidth,'height':layoverlayerHeight});
				   
			//Get the window height and width
			var winH = jQuery(window).height();
			var winW = jQuery(window).width();
	
			//Set the popup window to center
			box.css('top',  winH/2 - box.height()/2);
			box.css('left', winW/2 - box.width()/2);  
 		});
		
		jQuery('#layoverlayer').click(function(){
			closethankspopup();
		});
	});
	/* E: Thankyou Popup on home page */

// end top.phtml javascript
 jQuery(function(){
	//	Code added by Vaseem to fix IE 7 bug
	if(jQuery.browser.msie == true) {
		//alert('IE');
		var browserVersion	=	jQuery.browser.version;
		//alert(browserVersion);
	}
	jQuery('#top-nav-block').data('current-nav-index', -1);
  //jQuery('#chromemenu').mastmenu({speed:400});
  jQuery('#top-nav-block').hoverIntent({
   over: function(){
    if(jQuery('#top-nav-block').data('current-nav-index') ==-1){
		jQuery('#top-nav-block').data('current-nav-index',jQuery('#nav-menu-bar li:last').index());
	}
     jQuery('#top-nav-block').data('nav-status','open');
     jQuery('#nav-menu-bar li:eq('+jQuery('#top-nav-block').data('current-nav-index')+')').addClass('active');
     jQuery('#nav-menu-subcontainer .sub-menu-list').hide();
     jQuery('#nav-menu-subcontainer .sub-menu-list:eq('+jQuery('#top-nav-block').data('current-nav-index')+')').show();
     	//	Code added by Vaseem to fix IE 7 bug
		if(browserVersion==7.0){
			jQuery('#nav-menu-subcontainer').show();	
		}else{
			jQuery('#nav-menu-subcontainer').stop(true,true).slideDown(500,'easeInOutQuint');
		}
   },
   out: function(){
    jQuery('#top-nav-block').data('nav-status','close');
    jQuery('#nav-menu-bar li').removeClass('active');
    	//	Code added by Vaseem to fix IE 7 bug
		if(browserVersion==7.0){
			jQuery('#nav-menu-subcontainer').hide();	
		}else{
			jQuery('#nav-menu-subcontainer').stop(true,true).slideUp(500,'easeInOutQuint')
		}
   },
   timeout: 200
  })
  
  // S: Different hover impact for ipad
	if(typeof(navigator) != 'undefined' && typeof(navigator.platform) != 'undefined' && navigator.platform == "iPad") {				
		jQuery("#nav-menu-bar li a").each(function(){
			var firstClick = true;
			jQuery(this).click(function() {
				firstClick = !firstClick;
				return firstClick;
			});
		});
		
		jQuery('#nav-menu-bar li').hover(
			function(){
				var index = jQuery(this).index();
				if(jQuery('#top-nav-block').data('nav-status') == 'close'){
					jQuery('#nav-menu-bar li').removeClass('active');
					jQuery('#nav-menu-bar li:eq('+index+')').addClass('active');
					jQuery('#nav-menu-subcontainer .sub-menu-list').hide();
					jQuery('#nav-menu-subcontainer .sub-menu-list:eq('+index+')').show();
					jQuery('#nav-menu-subcontainer').stop(true,true).slideDown(500,'easeInOutQuint');
				}
			}
		);
	}
	// E: Different hover impact for ipad
  
  jQuery('#nav-menu-bar li').hover(
   function(){
    var index = jQuery(this).index();
    jQuery('#top-nav-block').data('current-nav-index', index);
    if(jQuery('#top-nav-block').data('nav-status') == 'open'){
     jQuery('#nav-menu-bar li').removeClass('active');
     jQuery('#nav-menu-bar li:eq('+index+')').addClass('active');
     jQuery('#nav-menu-subcontainer .sub-menu-list').hide();
     jQuery('#nav-menu-subcontainer .sub-menu-list:eq('+index+')').show();
    }
   },
   function(){
    jQuery('#top-nav-block').data('current-nav-index', -1);
   }
  )
  
  jQuery('.up-menu-arrow').click(function (){
   jQuery('#top-nav-block').data('nav-status','close');
    jQuery('#nav-menu-bar li').removeClass('active');
   jQuery('#nav-menu-subcontainer').stop(true,true).slideUp(500,'easeInOutQuint')
})

	//jQuery('.UI-SEARCH').focusin(function(){
    //if(jQuery('.UI-SEARCH').val()=='Search...'){
       // jQuery('.UI-SEARCH').val('');
    //}
   // jQuery('.searchbarsection').animate({width:227}, 300, 'swing')
//})
//.focusout(function(){
    //if(jQuery('.UI-SEARCH').val()==''){
       // jQuery('.UI-SEARCH').val('Search...');
       // jQuery('.searchbarsection').animate({width:80}, 300, 'swing')
    //}
//})
	
});
 

function closeShadowBox(){
	shadowBoxOverlay.fadeOut();
	jQuery('#shadow-box-wrapper').fadeOut();
	if(typeof(oldPageName) != 'undefined'){
		s.pageName = oldPageName;
	}
}
function updatePostForm(){
	var postData = jQuery('#updatepost_form').serialize();
	showProgressBar();
	jQuery.post('/fancycart/ajax/updatePost', postData, function(result){
		jQuery.get('/fancycart/ajax/index', function(cartHtml){
						hideProgressBar();
						updateCartPopup(cartHtml);
					})
	});
	return false;
}
function discountCodeApply(){
	var postData = {
		'coupon_code': jQuery('#coupon_code').val(),
		'remove': 0
	};
	showProgressBar();
	jQuery.post('/fancycart/ajax/couponPost', postData, function(result){
		jQuery.get('/fancycart/ajax/index', function(cartHtml){
						hideProgressBar();
						updateCartPopup(cartHtml);
						updateCartSummary();
					})
	});
	return false;
}
function discountCodeCancel(){
	if(jQuery('#coupon_code').val()!=''){
		showProgressBar();
		var postData = {
			'coupon_code': '',
			'remove': 1
		};
		jQuery.post('/fancycart/ajax/couponPost', postData, function(result){
			jQuery.get('/fancycart/ajax/index', function(cartHtml){
							hideProgressBar();
							updateCartPopup(cartHtml);
							updateCartSummary();
						})
		});
	}
	return false;
}


function showProgressBar(){
 //jQuery('#offer-choose-your-gift, #offer-choose-your-gift-shadow').hide();
 shadowBoxLoader.show();
 jQuery('#countdownticker').show();
}

function hideProgressBar(){
 //jQuery('#offer-choose-your-gift, #offer-choose-your-gift-shadow').show();
 shadowBoxLoader.hide();
 jQuery('#countdownticker').hide();
}

function updateCartPopup(cartHtml){
	shadowBox.html('<img id="shadow-box-close"  alt="close" src="/skin/frontend/angara/default/images/popup/close-popup.jpg" onclick="closeShadowBox()" /><div class="clear"></div>'+cartHtml);
	shadowBoxLoader = jQuery('<div id="progressing-loader" style="background-image:none;"> <div class="loading-msg"><img src="/skin/frontend/angara/default/images/logoanimatedgif.gif" /><br/>Updating your cart.<br/><b>Please Wait...</b></div>').appendTo(shadowBox);
	shadowBoxOverlay.height(jQuery(document).height());
}

function updateCartSummary(){
	showProgressBar();
	jQuery.get('/fancycart/ajax/getItemsCount', function(count){
		hideProgressBar();
		if(/^\d+$/.test(count)){
			jQuery('.mycartitem').html(
				jQuery('<span>'+count+' items</span>')
				.click(function(){
					if(count > 0){
						shadowBoxOverlay.height(jQuery(document).height()).fadeIn();
						jQuery('#shadow-box-wrapper').fadeIn();
						showProgressBar();
						jQuery.get('/fancycart/ajax/index', function(cartHtml){
							hideProgressBar();							
							updateCartPopup(cartHtml);
						})
					}
				})
			)
		}
	})
}
	
jQuery(function(){
	
	shadowBoxOverlay = jQuery('<div id="shadow-box-overlay">')
		.height(jQuery(document).height())
		.width(jQuery(document).width())
		.appendTo('body')
		.click(function(){
			closeShadowBox();
		});
	shadowBox = jQuery('<div id="shadow-box-content">').appendTo(jQuery('<div id="shadow-box-wrapper">').appendTo('body'))
					.click(function(e){
						e.stopPropagation();
					});
	
	shadowBoxLoader = jQuery('<div id="progressing-loader"> <div class="loading-msg">Updating your cart.<br/><b>Please Wait...</b></div>').appendTo(shadowBox);
					
		jQuery('#shadow-box-wrapper').click(function(){
			closeShadowBox();
		})
	
	updateCartSummary();
	
	jQuery('#product_addtocart_form').submit(function(){
		if(productAddToCartForm.validator.validate()){
			var postData = jQuery(this).serialize();
			//var url = jQuery(this).attr('action');
			var url = '/fancycart/ajax/add';
			shadowBoxOverlay.height(jQuery(document).height()).fadeIn();
			jQuery('#shadow-box-wrapper').fadeIn();
			showProgressBar();
			jQuery.post(url, postData, function(data){
				jQuery.get('/fancycart/ajax/index', function(cartHtml){
					hideProgressBar();
					updateCartPopup(cartHtml);
				})
				updateCartSummary();
			});
			return false;
		}
	})
})