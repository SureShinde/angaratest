// JavaScript Document

(function($){
	
    $.fn.mastmenu = function(params){
        //plugin name - mastmenu
		
		var settings = jQuery.extend({},$.fn.mastmenu.defaults, params);
		return this.each(function() {
			//Assign current element to variable, in this case is UL element
			var menu = $(this);             
			if(!menu.hasClass('converted2menu')){
				menu.settings = $.meta ?  $.extend({}, settings, menu.data()) : settings;
			
				$.fn.mastmenu.init(menu);
			
				// add class to chack later if menu is ready to use
				menu.addClass('converted2menu');
			}
			
		});
    }
	
	$.fn.mastmenu.init = function(menu){
		//initialization code here
		
		menu.find('.navlist2').each(function(i){
			jQuery(this).appendTo(
				menu.find('.populatable:not(.populated)').eq(0).addClass('populated')
			)
		})
		
		menu.find('.dropmenudiv').each(function(i){
			jQuery(this).appendTo(
				menu.find('>ul>li:eq('+i+')')
			);
		})
		
		
		
		menu.listPadding = parseInt( menu.find('.navlist').css('padding-left')) + parseInt( menu.find('.navlist').css('padding-right'));//tested
		menu.listBorder = parseInt( menu.find('.dropmenudiv').css('border-left-width')) + parseInt(menu.find('.dropmenudiv').css('border-right-width') );	
		menu.find('>ul>li').each(function(){	//tested
			$.fn.mastmenu.setupMainItem($(this),menu);
		})
		
		//
		
	}
	
	$.fn.mastmenu.setupMainItem = function(mainitem,menu){
		//mainitem.totalwidth = mainitem.width();	//tested
		mainitem.totalwidth = 0;	//tested
		mainitem.dropdown_width = 0;
		
		mainitem.find('.navlist').each(function(){
			$.fn.mastmenu.setupInnerList($(this),mainitem,menu);
		})
		
		//alert(mainitem.find('.navlist').length)
		if(mainitem.find('.navlist').length == 2){
			var IE6 = false /*@cc_on || @_jscript_version < 5.7 @*/;
			mainitem.dropdown_width += IE6?16:8;
		}
		
		// 6 is added for ie6 fix	[i.e. left:-1px]
		mainitem.find('>.dropmenudiv').width(mainitem.dropdown_width-menu.listBorder + 12);	//tested
		
	}
	
	$.fn.mastmenu.setupInnerList = function(innerlist,mainitem,menu){
		// 1 is subtracted for ie6 fix	[i.e. left:-1px]
		innerlist.totalwidth = mainitem.totalwidth ;
		innerlist.hasMoreLvl = false;
		innerlist.find('>ul>li').each(function(){	//tested
			innerlist.totalwidth = (($(this).width()+menu.listPadding)>innerlist.totalwidth)?($(this).width()+menu.listPadding ):innerlist.totalwidth;
			innerlist.hasMoreLvl |= $(this).find('.navlist2').hasClass('lvl2');
		})
		
		innerlist.find('>ul>li').each(function(){	//tested
			$(this).width(innerlist.totalwidth+'px');
			if($(this).find('.navlist2').hasClass('lvl2')){
				$.fn.mastmenu.setupInnerList2($(this),innerlist,mainitem,menu);
			}
		});
		
		//innerlist.totalwidth += (innerlist.hasMoreLvl==1)?menu.settings.arrow_width:menu.settings.arrow_width;
		
		innerlist.width((innerlist.totalwidth )+'px');
		mainitem.dropdown_width += innerlist.totalwidth;
	}
	
	
	$.fn.mastmenu.setupInnerList2 = function(item,innerlist,mainitem,menu){
		item.addClass('haslvl2').hover(
			function(){
				item.addClass('lvlactive');
			},
			function(){
				item.removeClass('lvlactive');
			}
		)
		if(item.find('.navlist2').hasClass('has_navlistparts')){
			$('<div class="border-hider"></div>').css('right',innerlist.totalwidth + 1).appendTo(item).height($('.haslvl2').height()+'px');
			item.find('.navlist2').css('right',innerlist.totalwidth  + 2);
		}
		else{
			$('<div class="border-hider"></div>').css('left',innerlist.totalwidth  +1).appendTo(item).height($('.haslvl2').height()+'px');
			item.find('.navlist2').css('left',innerlist.totalwidth  + 2);
		}
	}
	
	// plugin defaults
	$.fn.mastmenu.defaults = {
		//width: 950, not used
		arrow_width: 0,	// arrow width to show on inner menu
		speed: 1000
	};
	
})(jQuery);



jQuery(function(){
	
	jQuery('.mainlvl').parent().hover(
		function(){
			jQuery(this).addClass('active-link');
		},
		function(){
			jQuery(this).removeClass('active-link');
		}
	)
})