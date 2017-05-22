// JavaScript Document

(function($){
	
    $.fn.mastslider = function(params){
        //plugin name - mastslider
		
		var settings = jQuery.extend({},$.fn.mastslider.defaults, params);
		return this.each(function() {
			//Assign current element to variable, in this case is UL element
			var slider = $(this);             
			if(!slider.hasClass('converted2slider')){
				slider.settings = $.meta ?  $.extend({}, settings, slider.data()) : settings;
			
				$.fn.mastslider.init(slider);
			
				// add class to chack later if slider is ready to use
				slider.addClass('converted2slider');
			}
			
		});
    }
	
	$.fn.mastslider.init = function(slider){
		//initialization code here
		
		//turn off navigation in start
		slider.find('.as_slides_nav_left').addClass('left_nav_disabled');
		slider.find('.as_slides_nav_right').addClass('right_nav_disabled');
		
		slider.find('.as_category_slides').hide().each(function(){
			$.fn.mastslider.setupCategorySlides($(this),slider);
		})
		slider.find('.as_category_slides:first').show();
		$.fn.mastslider.setupCategories(slider);
	}
	
	$.fn.mastslider.setupCategorySlides = function(bigslider,slider){
		//alert(bigslider.index())
		bigslider.totalslides = slider.settings.categories[bigslider.index()-1].count;// # bigslider.find('.as_slide_wrapper').length; how much products
		bigslider.current = 1;
		$.fn.mastslider.updateNavTitle(bigslider,slider);
		$.fn.mastslider.setupNavigation(bigslider,slider);
	}
	
	$.fn.mastslider.updateNavTitle = function(bigslider,slider){
		var text = '';
		var to = 0;
		if(bigslider.totalslides < slider.settings.slides){
			text = bigslider.current + ' - ' + bigslider.totalslides + ' of ' + bigslider.totalslides;
		}
		else{
			to = bigslider.current + slider.settings.slides - 1;
			text = bigslider.current + ' - ' + ((to>bigslider.totalslides)?bigslider.totalslides:to) + ' of ' + bigslider.totalslides;
		}
		bigslider.find('.as_slides_nav_title').text(text);
	}
	
	$.fn.mastslider.updateNavigation = function(bigslider,slider){
		
		// update right nav
		if((bigslider.current + slider.settings.slides -1) < bigslider.totalslides){
			bigslider.find('.as_slides_nav_right').removeClass('right_nav_disabled');
		}
		else{
			bigslider.find('.as_slides_nav_right').addClass('right_nav_disabled');
		}
		
		// update left nav
		if(bigslider.current > 1){
			bigslider.find('.as_slides_nav_left').removeClass('left_nav_disabled');
		}
		else{
			bigslider.find('.as_slides_nav_left').addClass('left_nav_disabled');
		}
		
	}
	
	$.fn.mastslider.setupCategories = function(slider){
		/*
		//hover functionality off
		slider.find('.as_category').hover(
			function(){
				if(!$(this).hasClass('selected_category')){
					$(this).addClass('hover_category');
					if($(this).prev('.as_category').length>0){
						$(this).prev('.as_category').addClass('previous_category');
					}
				}
			},
			function(){
				if(!$(this).hasClass('selected_category')){
					$(this).removeClass('hover_category');
					if($(this).prev('.as_category').length>0){
						$(this).prev('.as_category').removeClass('previous_category');
					}
				}
			}
		)*/
		slider.find('.as_category').click(function(){
			if(!$(this).hasClass('selected_category')){
				var index = $(this).index();
				slider.find('.as_category').removeClass('selected_category').removeClass('hover_category').removeClass('previous_category');
				$(this).addClass('selected_category');
				
				if($(this).prev('.as_category').length>0){
					$(this).prev('.as_category').addClass('previous_category');
				}
				
				slider.find('.as_category_slides').hide();
				slider.find('.as_category_slides:eq('+index+')').show();
				slider.find('.as_category_slides:eq('+index+')').find('.as_slides_wrapper').hide(0);
				
				$.fn.mastslider.getSlides(slider.settings.categories[$(this).index()].title,  $('.as_slides:eq('+$(this).index()+')'));
				
				slider.find('.as_category_slides:eq('+index+')').find('.as_slides_wrapper').fadeIn(slider.settings.speed);
			}
		})
	}
	
	
	$.fn.mastslider.setupNavigation = function(bigslider,slider){
		if(bigslider.totalslides > slider.settings.slides){
			bigslider.find('.as_slides_nav_right').removeClass('right_nav_disabled');
			bigslider.find('.as_slides_nav_right').click(function(){
				$.fn.mastslider.showLeft(bigslider,slider);
			})
			bigslider.find('.as_slides_nav_left').click(function(){
				$.fn.mastslider.showRight(bigslider,slider);
			})
		}
	}
	
	//slide left means right nav button pressed
	$.fn.mastslider.showLeft = function(bigslider,slider){
		if((bigslider.current + slider.settings.slides -1) < bigslider.totalslides){
			bigslider.current = bigslider.current + slider.settings.slides;
			$.fn.mastslider.updateNavTitle(bigslider,slider);
			$.fn.mastslider.updateNavigation(bigslider,slider);
			
			$.fn.mastslider.getSlides(slider.settings.categories[bigslider.index()-1].title,  bigslider.find('.as_slides'));
			
			bigslider.find('.as_slides').stop().animate({left:-(bigslider.find('.as_slides_wrapper').width() * ((bigslider.current-1)/slider.settings.slides))},slider.settings.speed);
		}
	}
	
	$.fn.mastslider.showRight = function(bigslider,slider){
		if(bigslider.current > 1){
			bigslider.current = bigslider.current - slider.settings.slides;
			$.fn.mastslider.updateNavTitle(bigslider,slider);
			$.fn.mastslider.updateNavigation(bigslider,slider);
			
			$.fn.mastslider.getSlides(slider.settings.categories[bigslider.index()-1].title, bigslider.find('.as_slides'));
			
			bigslider.find('.as_slides').stop().animate({left:-(bigslider.find('.as_slides_wrapper').width() * ((bigslider.current-1)/slider.settings.slides))},slider.settings.speed);
		}
	}
	
	$.fn.mastslider.categoriesUpdated = [];
	
	$.fn.mastslider.getSlides = function(categoryTitle, container){
		if($.inArray(categoryTitle, $.fn.mastslider.categoriesUpdated)==-1){
			$.post('/hprcv/slider/getslides',{title:categoryTitle},function(slides){
				container.html(slides);
				$.fn.mastslider.categoriesUpdated.push(categoryTitle);
			});
		}
	}
	
	// plugin defaults
	$.fn.mastslider.defaults = {
		//width: 950, not used
		slides: 6,	// slides to show at once
		speed: 1000,
		categories: [			

			{
			title:"sapphire-jewelry",
				count:10
			},
			{				
				title:"ruby-jewelry",
				count:15	
			},
			{
				title:"tanzanite-jewelry",
				count:10
			},	
			{
				title:"emerald-jewelry",
				count:15
			},
			{
				title:"aquamarine-jewelry",
				count:10
			},
			{
				title:"diamond-jewelry",
				count:15
			}
		]
	};
	
})(jQuery);