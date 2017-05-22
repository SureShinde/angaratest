/* **********************************************
     Begin script.js
********************************************** */

jQuery.noConflict();
;(function($) {
    'use strict';

    function Site(settings) {

        this.windowLoaded = false;

    }

    Site.prototype = {
        constructor: Site

        , start: function() {
            var me = this;

            $(window).load(function() {
                me.windowLoaded = true;
            });

            this.attach();
        }

        , attach: function() {
            this.attachMedia();
        }

        , attachMedia: function() {
            var $links = $('[data-toggle="media"]');
            if ( ! $links.length) return;

            // When somebody clicks on a link, slide the
            // carousel to the slide which matches the
            // image index and show the modal
            $links.on('click', function(e) {
                e.preventDefault();

                var $link = $(this),
                   $modal = $($link.attr('href')),
                $carousel = $modal.find('.carousel'),
                    index = parseInt($link.data('index'));

                $carousel.carousel(index);
                $modal.modal('show');

                return false;
            });
        }
    }

    jQuery(document).ready(function($) {
        var site = new Site();
        site.start();
    });
	
	$(function(){
		/* Accordion Script */
		$('.ress-accordion-xs .ress-accordion-header').click(function(){
			$(this).parent('.ress-accordion-xs').find('>ul').toggleClass('hidden-xs');
		})
		
		/* Slider Script */
		$('.ress-slider').each(function(){
			var slider = $(this).data('current-index',0);
			if(slider.data('auto-slide-after') > 100){
				setInterval(function(){autoSlideAtNextIndex(slider)},slider.data('auto-slide-after'));
			}
			// check if slider has nav-controller
			if(slider.data('slider-nav-controller')){
				var navElement = slider.data('slider-nav-controller');
				slider.navElement = $('#'+navElement);
				
				if($(window).width() > 768){
					slider.navElement.hoverIntent(
						function(){
							$('#'+slider.data('child-container')).stop(true,true).slideDown(300,"easeOutQuint");
							$('#'+slider.data('child-container')).data('open-state',true);
						}
					)
					slider.navElement.find('li').hoverIntent(function(){
						var newIndex = $(this).index();
						slider.data('current-index', newIndex);
						if($('#'+slider.data('child-container')).data('open-state')){
							slideAtCurrentIndex(slider);
						}
						else{
							slideAtCurrentIndex(slider, true);
						}
					})
				}else{
					slider.navElement.touchend(
						function(){
							$('#'+slider.data('child-container')).stop(true,true).slideDown(300,"easeOutQuint");
							$('#'+slider.data('child-container')).data('open-state',true);
						}
					)
					slider.navElement.find('li').touchend(function(){
						var newIndex = $(this).index();
						slider.data('current-index', newIndex);
						if($('#'+slider.data('child-container')).data('open-state')){
							slideAtCurrentIndex(slider);
						}
						else{
							slideAtCurrentIndex(slider, true);
						}
					})
				}
				
				$('#'+slider.data('master-container')).mouseleave(function(){
					$('#'+slider.data('master-container')).data('cursor-inside',false);
					setTimeout(function(){
						if(!$('#'+slider.data('master-container')).data('cursor-inside')){
							$('#'+slider.data('child-container')).stop(true,true).slideUp(300,"easeOutQuint");
							$('#'+slider.data('child-container')).data('open-state',false);
							slider.navElement.find('li.active').removeClass('active');
						}
					}, 600);
				});
				$('.close-nav').click(function(){
					$('#'+slider.data('child-container')).stop(true,true).slideUp(300,"easeOutQuint");
					$('#'+slider.data('child-container')).data('open-state',false);
					slider.navElement.find('li.active').removeClass('active');
				})
				$('#'+slider.data('master-container')).mouseenter(function(){
					$('#'+slider.data('master-container')).data('cursor-inside',true);
				})
			}
			
			// check if mouse wheel supoort is needed
			// 113 - 124 Removed for mouse wheel
			
			slider.parents('.has-slider').find('.ress-slider-down').click(function(){
				moveSliderAtNextNIndex(1, slider);
				slider.data('pause-auto-slide', true);
			});
			
			slider.parents('.has-slider').find('.ress-slider-up').click(function(){
				moveSliderAtPreviousNIndex(1, slider);
				slider.data('pause-auto-slide', true);
			});
			
			slider.parents('.has-slider').find('.ress-slider-index').click(function(){
				var newIndex = $(this).data('item-index');
				slider.data('current-index', newIndex);
				slideAtCurrentIndex(slider);
				slider.data('pause-auto-slide', true);
			});
			
			// adding swipe events
			//slider.on('touchmove', function(e) { e.preventDefault(); });
			if(slider.hasClass('ress-slider-horizontal')){
				slider.swiperight(function(event){
					event.preventDefault();
					moveSliderAtPreviousNIndex(1, slider);
				})
				slider.swipeleft(function(event){
					event.preventDefault();
					moveSliderAtNextNIndex(1, slider);
				})
			}
			else if(slider.hasClass('ress-slider-vertical')){
				slider.swipedown(function(event){
					event.preventDefault();
					moveSliderAtPreviousNIndex(1, slider);
				})
				slider.swipeup(function(event){
					event.preventDefault();
					moveSliderAtNextNIndex(1, slider);
				})
			}			
		});		
	})
	
	function moveSliderAtNextNIndex(n, slider, noAnimation){
		if(slider.data('total-items') > slider.data('items-to-show')){
			var newIndex = slider.data('current-index') + n;
			// check if rotary slider or not
			if(newIndex > (slider.data('total-items') - slider.data('items-to-show'))){
				if(slider.data('slider-rotary')){
					newIndex = newIndex - (slider.data('total-items') - slider.data('items-to-show')) - 1;
				}
				else{
					newIndex = (slider.data('total-items') - slider.data('items-to-show'));
				}
			}
			slider.data('current-index', newIndex);
			slideAtCurrentIndex(slider, noAnimation);
		}
	}
	
	function moveSliderAtPreviousNIndex(n, slider, noAnimation){
		if(slider.data('total-items') > slider.data('items-to-show')){
			var newIndex = slider.data('current-index') - n;
			// check if rotary slider or not
			if(newIndex < 0){
				if(slider.data('slider-rotary')){
					newIndex = (slider.data('total-items') - slider.data('items-to-show')) + newIndex + 1;
				}
				else{
					newIndex = 0;
				}
			}
			slider.data('current-index', newIndex);
			slideAtCurrentIndex(slider, noAnimation);
		}
	}
	
	function slideAtCurrentIndex(slider, noAnimation){
		if(slider.data('slider-type') == 'indexable'){
			slider.parents('.has-slider').find('.ress-slider-index').removeClass('fa-circle').addClass('fa-circle-o');
			slider.parents('.has-slider').find('.ress-slider-index:eq('+slider.data('current-index')+')').addClass('fa-circle').removeClass('fa-circle-o');
		}
		else if(slider.data('slider-type') == 'updown'){
			if(slider.data('current-index') == 0){
				//up
				slider.parents('.has-slider').find('.ress-slider-up').addClass('disabled');
				slider.parents('.has-slider').find('.ress-slider-down').removeClass('disabled');
			}
			else if((slider.data('total-items') - slider.data('items-to-show')) == slider.data('current-index')){
				//down
				slider.parents('.has-slider').find('.ress-slider-down').addClass('disabled');
				slider.parents('.has-slider').find('.ress-slider-up').removeClass('disabled');
			}
			else{
				slider.parents('.has-slider').find('.ress-slider-down').removeClass('disabled');
				slider.parents('.has-slider').find('.ress-slider-up').removeClass('disabled');
			}
		}
		else if(slider.data('slider-type') == 'nav'){
			slider.navElement.find('li.active').removeClass('active');
			slider.navElement.find('li:eq('+slider.data('current-index')+')').addClass('active');
		}
		if(slider.hasClass('ress-slider-horizontal')){
			if(noAnimation)
				slider.stop(false, true).css({'left':-(slider.data('current-index') * slider.find('.ress-slider-item').outerWidth(true))});
			else
				slider.stop(false, true).css({'left':-(slider.data('current-index') * slider.find('.ress-slider-item').outerWidth(true))});


		}
		else if(slider.hasClass('ress-slider-vertical')){
			if(noAnimation)
				slider.stop(false, true).css({'top':-(slider.data('current-index') * slider.find('.ress-slider-item').outerHeight(true))});
			else
				slider.stop(false, true).css({'top':-(slider.data('current-index') * slider.find('.ress-slider-item').outerHeight(true))});
		}
	}
	
	function autoSlideAtNextIndex(slider){
		slider.parents('.has-slider').mouseenter(function(){
			slider.data('pause-auto-slide', true);
		})
		slider.parents('.has-slider').mouseleave(function(){
			slider.data('pause-auto-slide', false);
		})
		
		if(!slider.data('pause-auto-slide')){
			var newIndex = slider.data('current-index') + 1;
			if(newIndex == slider.data('total-items')) newIndex = 0;
			slider.data('current-index', newIndex);
			slideAtCurrentIndex(slider);
		}
	}
	
	/* Nav tab/pills script */
	/* product-extra-info-tabs */
	$(function(){
		$('.ress-nav').each(function(){
			var nav = $(this);
			nav.find('.nav li').click(function(){
				var navItem = $(this);
				if(!navItem.hasClass('active')){
					var navIndex = navItem.index();
					nav.find('.nav li.active').removeClass('active');
					navItem.addClass('active');
					nav.find('.ress-nav-content').removeClass('show').addClass('hidden');
					nav.find('.ress-nav-content:eq('+navIndex+')').removeClass('hidden').addClass('show');
					productTabSwitch(navItem.text());
					window.scrollBy(0, 1);
				}
			});
		})
		
		$('.rating-links span').click(function(){
			$('html, body').animate({
			scrollTop: $("#customer-reviews-tab").offset().top - 75}, 10);
		})
		
		$('#review-form-opener').click(function(){
			$(this).removeClass('fontcolor-type1 text-underline clickable').next('i').remove();
			$('#review-form').show();
		})
		
		$('#write-review').click(function(){
			$('#review-form-opener').removeClass('fontcolor-type1 text-underline').next('.fa-caret-right').hide();
			$('#review-form').show();
			var extraAmt = jQuery('#main-head-inner').outerHeight();
			$('html, body').animate({
			scrollTop: $('#customer-reviews .form-add').offset().top - extraAmt}, 1000);
		})
		
		//	S:VA	handle click on Product Details
		$('#view-product-details').click(function(){
			$('#product-extra-info-tabs').find('.nav li.active').removeClass('active');
			$('#product-extra-info-tabs').find('.nav li:eq(0)').addClass('active');
			$('#product-extra-info-tabs').find('.ress-nav-content').removeClass('show').addClass('hidden');
			$('#product-extra-info-tabs').find('.ress-nav-content:eq(0)').removeClass('hidden').addClass('show');
		})
		//	S:VA	handle click on Reviews
		$('#view-product-reviews').click(function(){
			$('#product-extra-info-tabs').find('.nav li.active').removeClass('active');
			$('#product-extra-info-tabs').find('.nav li:eq(1)').addClass('active');
			$('#product-extra-info-tabs').find('.ress-nav-content').removeClass('show').addClass('hidden');
			$('#product-extra-info-tabs').find('.ress-nav-content:eq(1)').removeClass('hidden').addClass('show');
		})
		//	S:VA	handle click on Shipping And Returns
		$('#view-product-returns').click(function(){
			$('#product-extra-info-tabs').find('.nav li.active').removeClass('active');
			$('#product-extra-info-tabs').find('.nav li:eq(2)').addClass('active');
			$('#product-extra-info-tabs').find('.ress-nav-content').removeClass('show').addClass('hidden');
			$('#product-extra-info-tabs').find('.ress-nav-content:eq(2)').removeClass('hidden').addClass('show');
		})
		//	S:VA	handle click on The Angara Difference
		$('#view-angara-difference').click(function(){
			$('#product-extra-info-tabs').find('.nav li.active').removeClass('active');
			$('#product-extra-info-tabs').find('.nav li:eq(3)').addClass('active');
			$('#product-extra-info-tabs').find('.ress-nav-content').removeClass('show').addClass('hidden');
			$('#product-extra-info-tabs').find('.ress-nav-content:eq(3)').removeClass('hidden').addClass('show');
		})
		$('#view-product-details, #view-product-reviews, #view-product-returns, #view-angara-difference').click(function(){
			$('html, body').animate({
				scrollTop: $("#product-extra-info-tabs").offset().top
			}, 10);
		})
		
		if(document.URL.indexOf('#product-extra-info-tabs') != -1){
			$('#product-extra-info-tabs').find('.nav li.active').removeClass('active');
			$('#product-extra-info-tabs').find('.nav li:eq(1)').addClass('active');
			$('#product-extra-info-tabs').find('.ress-nav-content').removeClass('show').addClass('hidden');
			$('#product-extra-info-tabs').find('.ress-nav-content:eq(1)').removeClass('hidden').addClass('show');
		}
	})
	
	/* country drop down list */
	$(function(){
		$('.country-dropdown-list-trigger').on('vclick', function(e){
			e.preventDefault();
			e.stopPropagation();
			//$('.country-dropdown-list-container .country-dropdown-list').addClass('show').removeClass('hidden');
			if($('.country-dropdown-list-container .country-dropdown-list').hasClass('hidden')){
				$('.country-dropdown-list-container .country-dropdown-list').removeClass('hidden').addClass('show');
			}
			else{
				$('.country-dropdown-list-container .country-dropdown-list').removeClass('show').addClass('hidden');
			}
			if($(this).parent().hasClass('first')){
				$('.country-dropdown-list-container .country-dropdown-list').focus();
			}
		})
		$('.country-dropdown-list li').on('vclick',function(e){
			e.stopPropagation();
		})
		$('body').on('vclick',function(){
			$('.country-dropdown-list-container .country-dropdown-list').removeClass('show').addClass('hidden');
		})
	})
	
	/* main menu mobile */
	$(function(){
		$('#main-menu-xs-toggler').click(function(){
			/*if(!($('#sub-menu-search-toggler').hasClass('hidden-xs'))){
				$('#sub-menu-search-toggler').addClass('hidden-xs');
				$("#search-icon").removeClass("fa-search-minus").addClass('fa-search-plus');
			}*/
							
			$('.child-category .container-fluid').each(function(childIndex){
				if(!($('.child-category .container-fluid:eq('+childIndex+')').hide())){
					$('.child-category .container-fluid:eq('+childIndex+')').hide();
				}
			});			
			
			$('.parent-category li').each(function(parentIndex){
				if($('.parent-category li:eq('+parentIndex+')').hide()){
					$('.parent-category li:eq('+parentIndex+')').show();
				}
			});
			
			$('#main-menu-xs').slideToggle('fast');
		});
		
		$('.parent-category li').click(function(){
			var index = $(this).index();
			
			$('.parent-category li').each(function(parentIndex){
				if(!($('.parent-category li:eq('+parentIndex+')').hide())){
					$('.parent-category li:eq('+parentIndex+')').hide();
				}
			});
			
			if(!($('.child-category .container-fluid:eq('+index+')').show()))
				$('.child-category .container-fluid:eq('+index+')').show("slow");
		});
		
		$('.child-category .container-fluid li span.back-btn').click(function(){
			var index = $(this).index();
			
			$('.parent-category li').each(function(parentIndex){
				if($('.parent-category li:eq('+parentIndex+')').hide()){
					$('.parent-category li:eq('+parentIndex+')').show();
				}
			});
			
			$('.child-category .container-fluid').each(function(childIndex){
				if(!($('.child-category .container-fluid:eq('+childIndex+')').hide())){
					$('.child-category .container-fluid:eq('+childIndex+')').hide();
				}
			});	
		});
		
		$('.child-category .container-fluid li').click(function(e){
			e.stopPropagation();
		})
	})	
	
	/* Newsletter Popup */
	$(function(){
		if(($('#newsletterModal').length > 0) || (jQuery('#fetchcountry').length > 0)) {
			
			//	S:VA
			jQuery.ajaxSetup({
				beforeSend: function() {
					// Show loader
					//jQuery('#newsletterModal').html('<div class="loading-indicator text-center"><div class="modal-backdrop fade in"></div><div class="loader fa fa-spinner fa-spin fa-5x max-margin-top"></div></div>');
				},
				complete: function() {
					// Hide loader
				}
			});
			//	E:VA

			jQuery.get('/popup/index/countryfetch',function(response){				
					if(response!='false'){	
						if(jQuery('#fetchcountry').length > 0){			
						jQuery('#fetchcountry').modal('show').html(response);
						}
					}
					else if($('#newsletterModal').length > 0){
						setTimeout(showNewsletterPopup, 10000);
					}
			});		
		}
		if($('#newsletterModalbottom').length > 0){
			setTimeout(showNewsletterBottomPopup, 3000);
		}
	})
	function showNewsletterBottomPopup(){
		if(jQuery(window).width() > 360 && document.URL.indexOf('contacts/index/subscribe') == -1)
		{
			jQuery.get( "/newsletter/popup/index/poptype/1", function( data ) {
				jQuery("#newsletterModalbottom").html( data );
				setTimeout(function() {
						jQuery("#newsletterModalbottom .nsbottom").animate({
							bottom: "0px",
						}, 750);
				}, 2000 );
			});
		}
	}
	
	/* Product Price move in Mobile */
	/*$(function(){
		if($(window).width() < 768){
			$(window).data('move-down',false);
			$(window).scroll(function(){
				if(!$(window).data('move-down') && ($(window).scrollTop() + $(window).height() - $('.product-img-box').offset().top) > 0){
					$(window).data('move-down',true);					
					$('#block-top').insertAfter('.product-img-box');
					$('#block-top').addClass('high-margin-top');
					$('#easypaybox').removeClass('hidden');					
				}
				if($(window).data('move-down') == true && ($('.product-img-box').offset().top) > $(window).scrollTop()){
					$(window).data('move-down',false);					
					$('#block-top').insertBefore('.product-img-box');
					$('#easypaybox').addClass('hidden');
					$('#block-top').removeClass('high-margin-top');		
				}
			})
		}
	})*/
	
	/* Catalog Promotion */
	$(function(){
		$("#mini-hero-showcase-md").addClass("clickable");
		$('#mini-hero-showcase-md').click(function(){
			$(this).height(99);
			$(this).find('img').css('margin-top','-34px');
			$("#mini-hero-showcase-md").removeClass("clickable");
			$('#countdownticker2').show();
			$('#hide-mini-hero-banner').show(function(){
				$('.mini-hero-newletter').stop(1,1).fadeIn();
			});
		})
		
		$('#hide-mini-hero-banner').click(function(){
				$('#mini-hero-showcase-md').height(34);
				$('#mini-hero-showcase-md').find('img').css('margin-top','0');
				$('#hide-mini-hero-banner').hide();
				$('.mini-hero-newletter').hide();
				$('#countdownticker2').hide();
				$("#mini-hero-showcase-md").addClass("clickable");
			}
		)
		
		if($(window).width() < 768){
			$("#mini-hero-showcase-xs").addClass("clickable");
			$('#mini-hero-showcase-xs').on('click', function(){
				$(this).height(47);
				$(this).find('img').css('margin-top','-23px');
				$("#mini-hero-showcase-xs").removeClass("clickable");
				$('#hide-mini-hero-banner-xs').show();
			})
			
			$('#hide-mini-hero-banner-xs').on('click',function(){
					$('#mini-hero-showcase-xs').height(23);
					$('#mini-hero-showcase-xs').find('img').css('margin-top','0');
					$('#hide-mini-hero-banner-xs').hide();
					$("#mini-hero-showcase-xs").addClass("clickable");
				}
			)
		}
	})
	
	/* show/hide search in mobile */
	jQuery(function(){
		jQuery('#main-menu-search-toggler').click(function(){
			jQuery('#sub-menu-search-toggler').toggleClass('hidden-xs');
			jQuery("#search-icon",this).toggleClass("fa-search-minus fa-search-plus");
			if($(this).hasClass('active')){
				$(this).removeClass('active');
			}
			else{
				$('#navbar-main-xs > ul > li').removeClass('active');
				$(this).addClass('active');
			}
			
			if($('#main-menu-xs').is(':visible'))
				$('#main-menu-xs').hide();
		});
	});
	
	/* show/hide menu in mobile */
	jQuery(function(){
		jQuery('#chatPop').click(function(){
			if($('#main-menu-xs').is(':visible'))
				$('#main-menu-xs').hide();
		});
	});
	
	/* slide towards top in mobile on change options */
	/*jQuery(function(){
		if($(window).width() < 768){
			 $('.option-container').on('click',function(){
				$('html, body').delay(50).animate({
					scrollTop: $(".product-name").offset().top
				}, 2000);
			});
			$('.special-variation-select').on('change',function(){
				$('html, body').delay(50).animate({
					scrollTop: $(".product-name").offset().top
				}, 2000);
			});
		}
	});*/
	
	/* Redesign catalog filter page. */
	$(function(){
		if(jQuery(window).width() > 767){
			//var gemStoneTypeLength = jQuery('#filter-panel #adj-nav-filter-filterable_stone_names').find('.catalog-filter-item').length;
			
			jQuery('.catalog-filter-nav-item').each(function(index, element) {
				if(jQuery('.catalog-filter-nav-item:eq('+index+')').hasClass('active')){
					jQuery('.catalog-filter-nav-item:eq('+index+')').removeClass('active');
					jQuery('.catalog-filter-content:eq('+index+')').hide();
				}
			});
			
			/*if(gemStoneTypeLength > 5){
				jQuery('.catalog-filter-nav-item:eq('+jQuery('#filter-panel #adj-nav-filter-filterable_stone_names').index()+')').addClass('active');
				jQuery('.catalog-filter-content:eq('+jQuery('#filter-panel #adj-nav-filter-filterable_stone_names').index()+')').show();
			}
			else{
				jQuery('.catalog-filter-nav-item:eq('+jQuery('#filter-panel #adj-nav-filter-filterable_metal_types').index()+')').addClass('active');
				jQuery('.catalog-filter-content:eq('+jQuery('#filter-panel #adj-nav-filter-filterable_metal_types').index()+')').show();
			}*/
			
			jQuery('#filter-panel').show('slow');
		}
	});
	
	/* Collapsible and tabs */
	$(function(){
		$(document).on('click','*[data-toggle="collapse"]', function(){
			$($(this).data('to-toggle')).toggle();
			if($(this).find('#catalog-filter-toggle')){
				jQuery('#catalog-filter-toggle i').toggleClass("fa-angle-down fa-angle-up");
			}
		})
		$(document).on('click','*[data-toggle="tab"]', function(){
			if($($(this).data('to-toggle')).hasClass('hidden')){
				$('.' + $(this).data('to-toggle-class')).addClass('hidden').hide();
				$($(this).data('to-toggle')).removeClass('hidden').show();
				$(this).parents('#product-extra-info-tabs').find('button i.fa').each(function(index, element) {
					if($(this).hasClass('fa-minus')){
						$(this).removeClass('fa-minus').addClass('fa-plus');
					}
				});
				$(this).find('i.fa').addClass('fa-minus').removeClass('fa-plus');
			}
			else{
				$('.' + $(this).data('to-toggle-class')).addClass('hidden').hide();
				$(this).find('i.fa').addClass('fa-plus').removeClass('fa-minus');
			}
			
			if($(window).width() < 768){
				var scrollTop = $(this).offset().top - 3;
				$('html,window,document,body').animate({scrollTop:scrollTop},10);
			}
		})
	})
	
	/* Customize button on mobile platform */
	$(function(){
		if($(window).width() < 768){
			$('#customize-product').click(function(){
				customizeProductClicked();
				if($('#variations-container .highlight-bg'))
					$('#variations-container .highlight-bg').show();
				$('#customize-product-panel').show();
				if($('#customize-product-panel').is(':visible'))
					$('#customize-product').hide();
				else
					$('#customize-product').show();	
				var scrollTop = $('#product-options-wrapper').offset().top;
				$('html,window,document,body').animate({scrollTop:scrollTop},10);
			});
		}
	});
	
	/* Product Engraving */
	$(function(){
		if($('#engravingContainer').length > 0){
			$('#engravingContainer').appendTo($('#add-ons-panel'))
		}
	})
	
	/* Recently Viewed Product Page */
	$(function(){
		var productId = jQuery('input[name="product"]').val();
		if(productId){
			jQuery('#recently-viewed-jewelry-container').load('/rvi_panelstate/ajax/gethtmlrecentviewed/productId/'+productId);
		}	
	})
	
	/* Category Testimonal on Catalog Page */
	$(function(){
		$('#categoryTestimonal').load('/testimonials/index/getcustomertestimonial');
	})
	
	/* Flash Deal on Home & Catalog Page */
	$(function(){
		//$('#flashDeal').load('/offers/deal/showDeals');
	})
	
	/* Similar Items on Product Page */
	$(function(){
		var productId = jQuery('input[name="product"]').val();
		if(productId){
			$('#similarItemsAjax').load('/catalog/product/getsimilaritems/productId/'+productId);
		}	
	})
	
	/* Product Estimated Shipping & add on selections */
	$(function(){
		if(typeof(variations) == 'undefined'){
			updateEstimateShipping();
		}
		$('#appraisal').change(function(){
			updateEstimateShipping();
			if(jQuery(this).prop('checked')){
				addSelection("Appraisal");
				jQuery('#jewel-appraisal').removeClass('hidden');
			}
			else{
				removeSelection("Appraisal");
				jQuery('#jewel-appraisal').addClass('hidden');
			}
		});
		$('#engraving').change(function(){
			updateEstimateShipping();
		});
		$('#insurance').change(function(){
			if(jQuery(this).prop('checked')){
				addSelection("Warrenty");
			}
			else{
				removeSelection("Warrenty");
			}
		})
	})
	
	/* Catalog Ajax Scroll */
	$(function(){
		if($('.category-product-list, .category-product-list-view').length > 6){
			$(window).data('catalog-loading', 'stop');
			if($(window).data('page-catalog')){
				$(window).data('page-catalog', $(window).data('page-catalog') + 1);
			}
			else{
				$(window).data('page-catalog',1);
			}
			$(window).data('view-all', 'no');
			$(window).data('user-on-top', true);
			$('#go-to-top-link').click(function(){
				$(window).scrollTop(0);
			})
				
			$(window).scroll(function(){
				if($(window).data('view-all') == 'yes' && $(this).data('catalog-loading') == 'stop' && ($(this).scrollTop() + $(this).height() - $('.category-product-list:last, .category-product-list-view:last').offset().top + 100) > 0){
					$(this).data('catalog-loading','start');
					if($('.toolbar-bottom .pages li.active').next().length > 0 && $('.toolbar-bottom .pages li.active').next().find('a').length > 0){
						var nextUrl = $('.toolbar-bottom .pages li.active').next().find('a').attr('href');
						if(nextUrl.indexOf('adjclear=true&') > -1){
							nextUrl = nextUrl.replace('adjclear=true&','');
						}
						else if(nextUrl.indexOf('&adjclear=true') > -1){
							nextUrl = nextUrl.replace('&adjclear=true','');
						}
						$('.toolbar-bottom').remove();
						/*if($(window).data('page-catalog') == 3){
							$('.category-products:last')
							.append(
								$('<div>')
								.append($('<div class="col-sm-4"></div>'))
								.append(
									$('<div class="col-sm-4" id="load-more-button">')
									.append(
										$('<div class="btn btn-block btn-dark-gray">')
										.append('<div class="max-padding-right max-padding-left fontsize-type6">Load More Results</div>')
									)									
								)
								.append('<div class="col-sm-4"></div>')
								.addClass('row no-gutters')		
							);
							$('#load-more-button')	
							.click(function(){
								loadMoreClicked(); // Tracking
								$(this).hide();										
								adj_nav_load_more_make_request(nextUrl);
								$(window).data('page-catalog',$(window).data('page-catalog') + 1);									
							})					
							$(this).data('catalog-loading','stop');	
						}
						else{*/
							adj_nav_load_more_make_request(nextUrl);
							$(window).data('page-catalog',$(window).data('page-catalog') + 1);		
						//}
					}
					else{
						$(this).data('catalog-loading','finish');
					}
				}
								
				if($(this).scrollTop() > 200){
					if($(this).data('user-on-top')){
						$('#go-to-top-link').stop().fadeIn().animate({
							bottom: 0
							}, 500);
						$(this).data('user-on-top',false);
					}
				}
				else {
					if(!$(this).data('user-on-top')){
						$('#go-to-top-link').stop().fadeOut().animate({
						   bottom: '-100px'
						}, 500);
						$(this).data('user-on-top',true);
					}
				}
			})
		}
	})
	
	
	
	/* Ui Basics */
	$(function(){
		
		$("img").unveil();
		
		$('div[data-ui-flow="go-with-page"]').each(function(){
			$(this).data('original-offset-top',$(this).offset().top);
		})
		$(window).scroll(function(){
			$('div[data-ui-flow="go-with-page"]').each(function(){
				if( ($(window).scrollTop() > $(this).data('original-offset-top')) && $(this).data('set-go-with-page') !=='yes'){
					$(this).removeClass($(this).data('remove-classes')).addClass($(this).data('add-classes')+' set-go-with-page').slideDown();
					$(this).data('set-go-with-page', 'yes');
				}
				else if($(this).data('set-go-with-page') !=='no' && ($(window).scrollTop() < $(this).data('original-offset-top'))){
					$(this).removeClass($(this).data('add-classes')+' set-go-with-page').addClass($(this).data('remove-classes'));
					$(this).data('set-go-with-page', 'no');
				}
			})
		})
		
		//jQuery('.offscreen').removeClass('offscreen').hide();
		//setInterval("animateSpecialKeylights()",10000);
		
		setTimeout('specialHighlightEffect(4000, "easeInOutCubic")', 3000);
		setTimeout('specialHighlightEffect(4000, "easeInOutCubic")', 25000);
		
		$('input, textarea').placeholder();
		
		$('i[data-toggle="tooltip"]').tooltip();
		
		$('body').on('click', function (e) {
			$('[data-toggle="tooltip"]').each(function () {
				//the 'is' for buttons that trigger popups

				//the 'has' for icons within a button that triggers a popup
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.tooltip').has(e.target).length === 0) {
					$(this).tooltip('hide');
				}
			});
		});
	})
	
	$(window).load(function(){
		$('.offscreen').removeClass('offscreen');
		if(jQuery(window).width() > 768){
			/* Product Zoom */
			$('#image').simpleLens();
		}
	})
	
	/* Cart Popup */
	$(function(){
		$('#cart-opener').on('click', function(e){ 
		   if( e.which == 2 ) {
			  e.preventDefault();
			  return false;
		   }
		});
		$(document).on('hidden.bs.modal', function (e){
			if($(e.target).data('clear-on-close')){
				$(e.target).removeData('bs.modal').html('<div class="loading-indicator hide text-center" style="display: none;"><div class="modal-backdrop fade in"></div><div class="loader fa fa-spinner fa-spin fa-5x max-margin-top"></div></div>');
			}
		});
		$(document).ajaxStart(function(event, request, settings) {
			$('.loading-indicator').show();
		});
		
		$(document).ajaxStop(function(event, request, settings) {
			$('.loading-indicator').hide();
		});
	})
	
	/* View special offer product page */
	$(function(){
		$('#special-offer-click').click(function(e){
			e.stopPropagation();
			productSpecialOfferClicked();
			
			if($('i[data-toggle="tooltip"]').data("tooltip"))
				$('i[data-toggle="tooltip"]').tooltip('hide');
			
			if($(this).find('i').hasClass('fa-caret-right')){
				$(this).find('i').removeClass('fa-caret-right');
				$(this).find('i').addClass('fa-caret-down');
			}
			
			if(jQuery('#special-offer-details').is(':visible')){
				jQuery('#special-offer-details').hide();
				$('#special-offer-click').find('i').removeClass('fa-caret-down');
				$('#special-offer-click').find('i').addClass('fa-caret-right');
			}
			else{
				jQuery('#special-offer-details').show();
				$('#special-offer-click').find('i').removeClass('fa-caret-right');
				$('#special-offer-click').find('i').addClass('fa-caret-down');
			}
			
			jQuery('#special-offer-details').click(function(event){
				event.stopPropagation();
				event.preventDefault();
				event.stopImmediatePropagation();
			});
			
			jQuery('#special-offer-text').hide();
			jQuery('#special-offer-text').html();
			jQuery('#close-special-offer').hide();
			jQuery('#spinner-offer').show();
			
			if(typeof(variations) != 'undefined'){
				var productId = variations.getCurrentProduct();
			}
			else{
				var productId = jQuery('input[name="product"]').val();
			}
			
			//var hasEngraving = 0;
			//var hasRange = 0;
			
			/*if(jQuery('#engraving').prop('checked')){
				hasEngraving++;
			}*/
			
			/*if(jQuery('#add-cart-stopper').length == 1){
				hasRange = 1;
			}*/
			
			//var productMainId = jQuery('input[name="product"]').val();
			var additionalCost = 0;
			jQuery.each(optionsPrice.customPrices,function(index1, customPriceObj){
			  	if(customPriceObj.priceValue)
					additionalCost += parseFloat(customPriceObj.priceValue)
			})
			if(typeof(productId) != 'undefined'){
				jQuery.get('/promotions/index/index/productId/'+productId+'/additionalCost/'+additionalCost, function(result){	/*jQuery.get('/offers/coupon/viewSpecialOffer/productId/'+productId+'/hasEngraving/'+hasEngraving+'/hasRange/'+hasRange+'/productMainId/'+productMainId, function(result){*/
					jQuery('#spinner-offer').hide();
					jQuery('#close-special-offer').show();					
					jQuery('#special-offer-text').html(result);
					jQuery('#special-offer-text').show();
					jQuery('#close-special-offer').click(function(){
						jQuery('#special-offer-details').hide();
						jQuery('#special-offer-text').hide();
						jQuery('#special-offer-text').html();
						jQuery('#close-special-offer').hide();
						$('#special-offer-click').find('i').removeClass('fa-caret-down');
						$('#special-offer-click').find('i').addClass('fa-caret-right');	
					});
				}).done(function() {
					// request successfully completed
				}).fail(function() {
					// request not completed
					//jQuery('#special-offer-click').parent().hide();
				});
			}
		});
		$('body').on('click', function(){
			jQuery('#special-offer-details').hide();
			if($('#special-offer-click').find('i').hasClass('fa-caret-down')){
				$('#special-offer-click').find('i').removeClass('fa-caret-down');
				$('#special-offer-click').find('i').addClass('fa-caret-right');	
			}
		});
	});
	
	/* Easy pay click message */
	$(function(){
		jQuery('label[for="#easy-pay-box"]').click(function(){
			if(jQuery('#add-cart-stopper').length == 1){
				jQuery('#add-cart-stopper-msg').removeClass('hidden');
			}
		});
	});
	
	/* More views for product page */
	$(function(){
		if(jQuery(window).width() > 767){
			setupMoreViews();
		}
		jQuery('#more-views-right').click(function(){
			if(sideView.currentView + sideView.totalViewsToShow < sideView.totalViews ){
				sideView.currentView++;
				jQuery('#moreviews ul').stop(true,true).animate({left: - (sideView.currentView * sideView.sideViewWidth)})
				jQuery('#more-views-left i').removeClass('hide');
				if((sideView.currentView + sideView.totalViewsToShow) == sideView.totalViews){
					jQuery('#more-views-right i').addClass('hide');
				}
			}
		});
		
		jQuery('#more-views-left').click(function(){
			if(sideView.currentView > 0 ){
				sideView.currentView--;
				jQuery('#moreviews ul').stop(true,true).animate({left: - (sideView.currentView * sideView.sideViewWidth)})
				jQuery('#more-views-right i').removeClass('hide');
				if(sideView.currentView ==0){
					jQuery('#more-views-left i').addClass('hide');
				}
			}
		});
	});
})(jQuery);

function viewAllClicked(){
	jQuery(window).data('view-all','yes')
	var firstUrl = jQuery('.toolbar-bottom .pages li.active').next().find('a').attr('href');
	if(firstUrl.indexOf('adjclear=true&') > -1){
		firstUrl = firstUrl.replace('adjclear=true&','');
	}
	else if(firstUrl.indexOf('&adjclear=true') > -1){
		firstUrl = firstUrl.replace('&adjclear=true','');
	}	
	jQuery('.toolbar-bottom').remove();	
	adj_nav_load_more_make_request(firstUrl);	
	jQuery(window).data('page-catalog', jQuery(window).data('page-catalog') + 1);	
}

function showNewsletterPopup(){
	//	S:VA
	if(jQuery(window).width() > 360 && document.URL.indexOf('contacts/index/subscribe') == -1 && document.URL.indexOf('checkout/cart') == -1 && document.URL.indexOf('onepagecheckout/index/index') == -1){
		jQuery('#newsletterModal').modal({remote: '/newsletter/popup/index'});
	}
	//	E:VA
}
function showNewsletterPopupSticker(){
	
	//	S:VA
	jQuery.ajaxSetup({
		beforeSend: function() {
			// Show loader
			jQuery('#newsletterModalWithSticker').html('<div class="loading-indicator text-center"><div class="modal-backdrop fade in"></div><div class="loader fa fa-spinner fa-spin fa-5x max-margin-top"></div></div>');
		}
	});
	//	E:VA	
	
	if(jQuery(window).width() > 360 && document.URL.indexOf('contacts/index/subscribe') == -1)
		jQuery('#newsletterModalWithSticker').modal({remote: '/newsletter/popup/index'});
}

function animateSpecialKeylights(){
	jQuery('#special-animation').fadeTo(600, 0, "swing",function(){jQuery('#special-animation').css({left:jQuery('#special-animation').width()})});
	jQuery('#special-animation').animate({opacity:1, left:0},2300,"easeOutCubic")
}

function executeCountryUrl(url,countryCode,currency,name){
	if(url != ''){
		if(typeof(countryCode) == 'undefined'){
			var countryCode = 'US';
		}
		
		if(typeof(currency) == 'undefined'){
			var currency = 'USD';
		}
		
		if(typeof(name) == 'undefined'){
			var name = 'United States';
		}
		
		if(countryCode == 'GS'){
			var countryName = 'SGSSI';
		}
		else{
			var countryName = name;
		}
		
		var countryFlagCode = countryCode.toLowerCase();
		
		var countryHtml = '<span class="country-flag country-'+countryFlagCode+'-smallf country-name-with-flag">'+countryName+'</span><span class="small block-inline-display valign-top text-light" style="height:25px; line-height:25px; padding-left:3px">('+currency+')</span>';
		
		jQuery('.country-dropdown-list-container .country-dropdown-list').addClass('hidden').removeClass('show');
		jQuery('#country-option-selected').html(countryHtml);
		window.location.href = url;
	}else{
		window.location.href = '/';
	}
}

/* Product Page Estimate Shipping Update */
function updateEstimateShipping(){
	if(typeof(variations) != 'undefined'){
		var productId = variations.getCurrentProduct();
	}
	else{
		var productId = jQuery('input[name="product"]').val();
	}
	
	var extraDays = 0;
	var arriveDays = 0;
	if(jQuery('#appraisal').length) {
		if(jQuery('#appraisal').prop('checked')){
			extraDays++;
			arriveDays++;
		}
	}
	
	// checked for free jewelry appraisal which is checked by default
	if(typeof(jQuery('#appraisal0')) != 'undefined' && jQuery('#appraisal0').is(":checked")){
		extraDays++;
		arriveDays++;
	}
	
	if(jQuery('#engraving').length && jQuery('#engraving-options').length) {
		if(jQuery('#engraving').prop('checked')){
			extraDays++;
			arriveDays++;
			jQuery('#engraving-options').removeClass('hidden');
		}
		else{
			jQuery('#engraving-options').addClass('hidden');
		}
	}
	
	if(typeof(productId) != 'undefined'){		
		jQuery('.dyn_arrive_lead_time').html('<i class="fa fa-spinner fa-spin"></i>');
		//jQuery('.dyn_delivered_by').html('<i class="fa fa-spinner fa-spin"></i>');		//	S:VA
		jQuery('#getItToday').html('<i class="fa fa-spinner fa-spin"></i>');
		//jQuery('#shippingMethod').html('<i class="fa fa-spinner fa-spin"></i>');
		
		jQuery.getJSON('/estimateShipping/ajax/getDate/productId/'+productId+'/extraDays/'+extraDays+'/arriveDays/'+arriveDays, function(result){
			//jQuery('.dyn_arrive_lead_time').html(result.arriveDays);
			jQuery('.dyn_arrive_lead_time').html(result.wantitDays);
			//jQuery('.dyn_delivered_by').html(result.deliveredByDays);						//	S:VA
			//jQuery('#getItToday').html(result.wantitDays);
			//jQuery('#shippingMethod').html(result.shippingMethod);
		}).done(function() {
			// request successfully completed
		}).fail(function() {
			jQuery('.dyn_arrive_lead_time').parent().hide();
			jQuery('#getItToday').parent().hide();
		});
	}
}

/*
	to show view special offer price on product page
*/
function specialOfferPrice(){
	if(typeof(variations) != 'undefined'){
		var productId = variations.getCurrentProduct();
	}
	else{
		var productId = jQuery('input[name="product"]').val();
	}
	if(typeof(productId) != 'undefined'){
		if(typeof(variations) != 'undefined'){
			variations.getOfferPrice(productId);		//	S:VA
		}else{		
			var additionalCost = 0;
			jQuery.each(optionsPrice.customPrices,function(index1, customPriceObj){
				if(customPriceObj.priceValue)
					additionalCost += parseFloat(customPriceObj.priceValue)
			});
			jQuery('#view-offer-price').html('<i class="fa fa-spinner fa-spin"></i>');
			jQuery.get('/promotions/index/ajax/productId/'+productId+'/additionalCost/'+additionalCost, function(result){
				jQuery('#view-offer-price').html(result);
			})
		}
	}
}

/*
	to show product name & description dynamically on product page
*/
function updateDescriptionName(categoryIds){
	if(typeof(variations) != 'undefined'){
		var productId = variations.getCurrentProduct();
	}
	else{
		var productId = jQuery('input[name="product"]').val();
	}
	if(typeof(productId) != 'undefined'){		
		//jQuery('.dyn_short_description').html('<i class="fa fa-spinner fa-spin"></i>');
		//jQuery('.dyn_long_description').html('<i class="fa fa-spinner fa-spin"></i>');
		if(typeof categoryIds != 'undefined' && categoryIds.indexOf('482') > -1 || categoryIds.indexOf('483') > -1 || categoryIds.indexOf('484') > -1){
			var postData = { product_id: productId, category_ids: categoryIds };
		}
		else{
			var postData = { product_id: productId };
		}	
		jQuery.post('/catalog/product/getProductDetailsUpdate/', postData, function(result){
			if(result){
				if(result.name){
					jQuery('.dyn_short_description').html(result.name);
				}
				if(result.description){	
					jQuery('.dyn_long_description').html(result.description);
				}	
			}	
		}, 'json')
		.done(function() {
			// request successfully completed
		}).fail(function() {
			// request not completed
		})
	}
}

/* Product Page thumbnail clicked */
function productThumbnailClicked(thumbnailElement, imageUrl){
	moreViewClicked();
	jQuery('#image').attr('src', imageUrl);
	jQuery('#moreviews').find('ul li').removeClass('active');
	jQuery(thumbnailElement).addClass('active');
}

/* Product Page user customization */
function showSelection(){
	//jQuery('#customize-product-selection-list-container').show();
}

function addSelection(value){
	//jQuery('#customize-product-selection-list').append('<div class="pull-left padding-type-5 showcase-bg-dark high-margin-left high-padding-left high-padding-right low-margin-bottom">'+value+'</div>');
}

function removeSelection(value){
	//jQuery('#customize-product-selection-list').find('div:contains("'+value+'")').remove();
}

//S: Open product page offer if price above 2500
function openProductOffer(){
	var h = jQuery('#exclusive-offer-footer-offer-new img').height();
	jQuery('#exclusive-offer-footer-opener-new').removeClass('deactive').addClass('active').css({'bottom':h+'px'});
	jQuery('#exclusive-offer-footer-offer-new').css({'bottom':'0px'});
}
function closeProductOffer(){
	jQuery('#exclusive-offer-footer-opener-new').removeClass('active first-time').addClass('deactive').css({'bottom':'0px'});
	jQuery('#exclusive-offer-footer-offer-new').css({'bottom':'-100%'});
}
//E: Open product page offer if price above 2500

/* Product price Change function */
function priceChanged(price, categoryIds){
		
	if(price == 0)
		return false;
	
	var installments = 3;
	var currentInstallment = jQuery('#easy-pay-box :selected').index() + ((jQuery('#easy-pay-box :selected').index() == -1) ? 2 : 1);
	
	if((price) > 2500){
		installments = 2;
	}
	
	if(categoryIds && (categoryIds.indexOf('73') > -1 || categoryIds.indexOf('435') > -1)){
		installments = 2;
	}

	if(currentInstallment > installments){
		currentInstallment = installments;
	}
	
	if(installments == 2){
		jQuery('#easy-pay-box').html('<option value="0_' + (price).toFixed(2) + '">1 payment(s) of ' + optionsPrice.formatPrice(price) + '</option><option value="1_' + (price / 2).toFixed(2) + '">2 payment(s) of ' + optionsPrice.formatPrice(price / 2) + '</option>');
	} 
	else{
		jQuery('#easy-pay-box').html('<option value="0_' + (price).toFixed(2) + '">1 payment(s) of ' + optionsPrice.formatPrice(price) + '</option><option value="1_' + (price / 2).toFixed(2) + '">2 payment(s) of ' + optionsPrice.formatPrice(price / 2) + '</option><option value="2_' + (price / 3).toFixed(2) + '">3 payment(s) of ' + optionsPrice.formatPrice(price / 3) + '</option>');
	}
	
	jQuery('#easy-pay-box option:eq('+(currentInstallment - 1)+')').prop('selected', true);
	
	if(price < 200){
		var warranty_price = 50;
	}
	else{
		var warranty_price = price * .20;	//	S:VA	Set Warranty Percentage
		warranty_price=Math.round(warranty_price);
	}
	
	if(price > 2500){
		jQuery('#exclusive-offer-footer-opener-new').removeClass('hidden');
		if(jQuery('#exclusive-offer-footer-opener-new').hasClass('first-time')){
			openProductOffer();
			setTimeout(function(){
				if(jQuery('#exclusive-offer-footer-opener-new').hasClass('active')){
					closeProductOffer();
				}
			}, 5000);
			jQuery('#exclusive-offer-footer-opener-new i').toggleClass('fa-angle-up fa-angle-down');
		}
	}
	
	if(jQuery('#byo-total-diamond-price')){
		var byoTotalDiamondPrice = parseInt(jQuery('#byo-total-diamond-price').val());
		jQuery('#total-byo-price').text(optionsPrice.formatPrice(price + byoTotalDiamondPrice));
	}
	
	jQuery('.dyn_warranty_price').html(optionsPrice.formatPrice(warranty_price));
	jQuery('.regular-price').fadeTo(1,.9).fadeTo(1,1);
}

jQuery(function(){
	jQuery('#exclusive-offer-footer-opener').click(function(){
		if(jQuery('#exclusive-offer-footer-opener i').hasClass('fa-angle-up')){
			jQuery('#exclusive-offer-footer-opener i').addClass('fa-angle-down').removeClass('fa-angle-up');
			jQuery('#exclusive-offer-footer').css('height', 143);
		}
		else{
			jQuery('#exclusive-offer-footer-opener i').removeClass('fa-angle-down').addClass('fa-angle-up');
			jQuery('#exclusive-offer-footer').css('height', 34);
		}
	})
})

function specialHighlightEffect(time,easing){
	jQuery('.shine-effect').css('transform','rotateY(0deg)').animate({left:200},time/2,easing,function(){
		jQuery('.shine-effect').animate({left:1000},time/2,easing,function(){
			jQuery('.shine-effect').css('transform','rotateY(180deg)').animate({left:200},time/2,easing,function(){
				jQuery('.shine-effect').animate({left:-100},time/2,easing)
			})
		})
	})
}

/* Update cart by posting cart form */
function postCart(){
	if(document.URL.indexOf('checkout/cart') == -1){
		var postData = jQuery('#updatepost_form').serialize();
		jQuery.post('/fancycart/ajax/updatePost', postData, function(result){
			if(!isNaN(result) && parseInt(result) == result) {
				jQuery('.dyn_cart-items-count').text(result);
			}
			jQuery('#cartModal').load('/fancycart/ajax/index');
		}).done(function() {
			// request successfully completed
		}).fail(function() {
			// request not completed
			//alert("Unable to process cart. Please try again after some time!");
		});
		return false;
	}
	else{
		jQuery('#updatepost_form').submit();
	}
}

/* More Views product page */

var sideView;
/* function setupMoreViews(){
	sideView = {
		currentView: 0,
		totalViewsToShow: (jQuery(window).width() > 360 && jQuery(window).width() < 999)?4:5,
		totalViews: jQuery('#moreviews li').length,
		sideViewWidth: jQuery('#moreviews li').outerWidth(true),
		containerWidth: jQuery('#moreviews').width()
	};
	
	if(sideView.sideViewWidth * sideView.totalViews > sideView.containerWidth){
		jQuery('#more-views-right i').removeClass('hide');
		jQuery('#more-views-left i').addClass('hide');
	}
	else{
		jQuery('#more-views-right i').addClass('hide');
		jQuery('#more-views-left i').addClass('hide');
	}
} */
function setupMoreViews(){
	sideView = {
		currentView: 0,
		totalViewsToShow: (jQuery(window).width() > 360 && jQuery(window).width() < 999)?4:5,
		totalViews: jQuery('#moreviews li').length,
		sideViewWidth: jQuery('#moreviews li').outerWidth(true),
		containerWidth: jQuery('#moreviews').width()
	};
	
	if(sideView.totalViews > 4){
		jQuery('#more-views-right i').removeClass('hide');
		jQuery('#more-views-left i').removeClass('hide');
	}
}

function updateMoreViews(){
	if(typeof(sideView) != 'undefined'){
		sideView.totalViews = jQuery('#moreviews li').length;
		sideView.currentView = 0;
	}
	if(sideView.totalViews > 4){
		jQuery('#more-views-right i').removeClass('hide');
		jQuery('#more-views-left i').removeClass('hide');
	}
	jQuery('#moreviews ul').stop(true,true).animate({left: 0})
}
/* function updateMoreViews(){
	if(typeof(sideView) != 'undefined'){
		sideView.totalViews = jQuery('#moreviews li').length;
		sideView.currentView = 0;
	}
	if(sideView.sideViewWidth * sideView.totalViews > sideView.containerWidth){
		jQuery('#more-views-right i').removeClass('hide');
		jQuery('#more-views-left i').addClass('hide');
	}
	else{
		jQuery('#more-views-right i').addClass('hide');
		jQuery('#more-views-left i').addClass('hide');
	}	
	jQuery('#moreviews ul').stop(true,true).animate({left: 0})
} */


/* Quick View Setup */
function quickView(obj, callerId){
	var $this = jQuery(obj);
	var loader = jQuery('<div class="loading-indicator text-center"><div class="modal-backdrop fade in"></div><div class="loader fa fa-spinner fa-spin fa-5x max-margin-top"></div></div>');
	jQuery('#ajaxModal').modal('show').html('')
	.append(loader)
	.append(
		jQuery('<div class="center-block quick-view-container" style="margin-top:100px; width:1005px;">').append(
			jQuery('<iframe class="no-border offscreen" frameborder="0" width="100%" style="height:0px;" src='+$this.attr('href')+'></iframe>').load(function(){
				jQuery(this).height(this.contentWindow.document.body.scrollHeight + 40).removeClass('offscreen');
				loader.hide();
				jQuery('#ajaxModal .quick-view-container').prepend(
					jQuery('<div class="clearfix showcase-bg-white low-padding-top low-padding-right">')
					.append(
						jQuery('<i data-dismiss="modal" class="fa fa-times close"></i>')
						.click(function(){
							jQuery('#ajaxModal').modal('hide').html('');
						})
					)
				)
			})
		)
	);
	QuickViewClicked(callerId);
}

//ccard.js
function validateCreditCard(s) {
    // remove non-numerics
    var v = "0123456789";
    var w = "";
    for (i=0; i < s.length; i++) {
        x = s.charAt(i);
        if (v.indexOf(x,0) != -1)
        w += x;
    }
    // validate number
    j = w.length / 2;
    k = Math.floor(j);
    m = Math.ceil(j) - k;
    c = 0;
    for (i=0; i<k; i++) {
        a = w.charAt(i*2+m) * 2;
        c += a > 9 ? Math.floor(a/10 + a%10) : a;
    }
    for (i=0; i<k+m; i++) c += w.charAt(i*2+1-m) * 1;
    return (c%10 == 0);
}