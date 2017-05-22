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

// end top.phtml javascript
 jQuery(function(){
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
     jQuery('#nav-menu-subcontainer').stop(true,true).slideDown(500,'easeInOutQuint');
   },
   out: function(){
    jQuery('#top-nav-block').data('nav-status','close');
    jQuery('#nav-menu-bar li').removeClass('active');
    jQuery('#nav-menu-subcontainer').stop(true,true).slideUp(500,'easeInOutQuint')
   },
   timeout: 200
  })
  
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
		//if(jQuery(this).val() =='Search entire store here...'){
			//jQuery(this).val('');
		//}
	//})
	//jQuery('.searchbarsection').hoverIntent({
		//over: function(){
			//jQuery('.searchbarsection').stop(true, true).animate({width:227},'easeInOutQuint', function(){
				//if(jQuery('.UI-SEARCH').val()==''){
				 //  jQuery('.UI-SEARCH').val('Search entire store here...');
				//}
		//	})
			//.click(function(e){
			//	e.stopPropagation();
			//})
		//},
		//interval: 10,
		//timeout: 1000
	//}
	//)
	
	//jQuery('body').click(function(){
		//if(jQuery('.UI-SEARCH').val()=='Search entire store here...' || jQuery('.UI-SEARCH').val()==''){
			//jQuery('.UI-SEARCH').val('');
			//jQuery('.searchbarsection').animate({width:25},'easeInOutQuint')
		//}
	//})
});
  