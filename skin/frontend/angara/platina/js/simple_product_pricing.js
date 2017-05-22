Product.Config.prototype.initialize = function(config){
	this.config     = config;
	this.taxConfig  = this.config.taxConfig;
	if (config.containerId) {
		this.settings   = $$('#' + config.containerId + ' ' + '.super-attribute-select');
	} else {
		this.settings   = $$('.super-attribute-select');
	}
	this.state      = new Hash();
	this.priceTemplate = new Template(this.config.template);
	this.prices     = config.prices;
	
	// Set default values from config
	if (config.defaultValues) {
		this.values = config.defaultValues;
	}
	
	// Overwrite defaults by url
	/*var separatorIndex = window.location.href.indexOf('#');
	if (separatorIndex != -1) {
		var paramsStr = window.location.href.substr(separatorIndex+1);
		var urlValues = paramsStr.toQueryParams();
		if (!this.values) {
			this.values = {};
		}
		for (var i in urlValues) {
			this.values[i] = urlValues[i];
		}
	}*/
	
	// Overwrite defaults by inputs values if needed
	if (config.inputsInitialized) {
		this.values = {};
		this.settings.each(function(element) {
			if (element.value) {
				var attributeId = element.id.replace(/[a-z]*/, '');
				this.values[attributeId] = element.value;
			}
		}.bind(this));
	}
		
	// Put events to check select reloads 
	this.settings.each(function(element){
		Event.observe(element, 'change', this.configure.bind(this))
	}.bind(this));

	// fill state
	this.settings.each(function(element){
		var attributeId = element.id.replace(/[a-z]*/, '');
		if(attributeId && this.config.attributes[attributeId]) {
			element.config = this.config.attributes[attributeId];
			element.attributeId = attributeId;
			this.state[attributeId] = false;
		}
	}.bind(this))

	// Init settings dropdown
	var childSettings = [];
	for(var i=this.settings.length-1;i>=0;i--){
		var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
		var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;
		//if (i == 0){
			this.fillSelect(this.settings[i])
		//} else {
			this.settings[i].disabled = false;
		//}
		$(this.settings[i]).childSettings = childSettings.clone();
		$(this.settings[i]).prevSetting   = prevSetting;
		$(this.settings[i]).nextSetting   = nextSetting;
		childSettings.push(this.settings[i]);
	}

	// Set values to inputs
	this.configureForValues();
	document.observe("dom:loaded", this.configureForValues.bind(this));
}
/*
    Some of these override earlier varien/product.js methods, therefore
    varien/product.js must have been included prior to this file.
    script by Matt Dean ( http://organicinternet.co.uk/ )
*/

Product.Config.prototype.getMatchingSimpleProduct = function(){
    var inScopeProductIds = this.getInScopeProductIds();
    if ((typeof inScopeProductIds != 'undefined') && (inScopeProductIds.length == 1)) {
        return inScopeProductIds[0];
    }
    return false;
};

/*
    Find products which are within consideration based on user's selection of
    config options so far
    Returns a normal array containing product ids
    allowedProducts is a normal numeric array containing product ids.
    childProducts is a hash keyed on product id
    optionalAllowedProducts lets you pass a set of products to restrict by,
    in addition to just using the ones already selected by the user
*/
Product.Config.prototype.getInScopeProductIds = function(optionalAllowedProducts) {

    var childProducts = this.config.childProducts;
    var allowedProducts = [];

    if ((typeof optionalAllowedProducts != 'undefined') && (optionalAllowedProducts.length > 0)) {
        allowedProducts = optionalAllowedProducts;
    }

    for(var s=0, len=this.settings.length-1; s<=len; s++) {
        //if (this.settings[s].selectedIndex <= 0){
            //break;
        //}
        var selected = this.settings[s].options[this.settings[s].selectedIndex];
        if (s==0 && allowedProducts.length == 0){
            allowedProducts = selected.config.allowedProducts;
        } else {
            allowedProducts = allowedProducts.intersect(selected.config.allowedProducts).uniq();
        }
    }

    //If we can't find any products (because nothing's been selected most likely)
    //then just use all product ids.
    if ((typeof allowedProducts == 'undefined') || (allowedProducts.length == 0)) {
        productIds = Object.keys(childProducts);
    } else {
        productIds = allowedProducts;
    }
    return productIds;
};


Product.Config.prototype.getProductIdOfCheapestProductInScope = function(priceType, optionalAllowedProducts) {

    var childProducts = this.config.childProducts;
    var productIds = this.getInScopeProductIds(optionalAllowedProducts);

    var minPrice = Infinity;
    var lowestPricedProdId = false;

    //Get lowest price from product ids.
    for (var x=0, len=productIds.length; x<len; ++x) {
        var thisPrice = Number(childProducts[productIds[x]][priceType]);
        if (thisPrice < minPrice) {
            minPrice = thisPrice;
            lowestPricedProdId = productIds[x];
        }
    }
    return lowestPricedProdId;
};


Product.Config.prototype.getProductIdOfMostExpensiveProductInScope = function(priceType, optionalAllowedProducts) {

    var childProducts = this.config.childProducts;
    var productIds = this.getInScopeProductIds(optionalAllowedProducts);

    var maxPrice = 0;
    var highestPricedProdId = false;

    //Get highest price from product ids.
    for (var x=0, len=productIds.length; x<len; ++x) {
        var thisPrice = Number(childProducts[productIds[x]][priceType]);
        if (thisPrice >= maxPrice) {
            maxPrice = thisPrice;
            highestPricedProdId = productIds[x];
        }
    }
    return highestPricedProdId;
};

Product.OptionsPrice.prototype.updateSpecialPriceDisplay = function(price, finalPrice) {

    var prodForm = $('product_addtocart_form');

    var specialPriceBox = prodForm.select('p.special-price');
    var oldPricePriceBox = prodForm.select('p.old-price, p.was-old-price');
    var magentopriceLabel = prodForm.select('span.price-label');

    if (price == finalPrice) {
        specialPriceBox.each(function(x) {x.hide();});
        magentopriceLabel.each(function(x) {x.hide();});
        oldPricePriceBox.each(function(x) {
            x.removeClassName('old-price');
            x.addClassName('was-old-price');
        });
    }else{
        specialPriceBox.each(function(x) {x.show();});
        magentopriceLabel.each(function(x) {x.show();});
        oldPricePriceBox.each(function(x) {
            x.removeClassName('was-old-price');
            x.addClassName('old-price');
        });
    }
};

//This triggers reload of price and other elements that can change
//once all options are selected
Product.Config.prototype.reloadPrice = function() {
    var childProductId = this.getMatchingSimpleProduct(); 
    var childProducts = this.config.childProducts;
    var usingZoomer = false;
    if(this.config.imageZoomer){
        usingZoomer = true;
    }

    if(childProductId){
		
        var price = childProducts[childProductId]["price"];
        var finalPrice = childProducts[childProductId]["finalPrice"];
        optionsPrice.productPrice = finalPrice;
        optionsPrice.productOldPrice = price;
        optionsPrice.reload();
        optionsPrice.reloadPriceLabels(true);
        optionsPrice.updateSpecialPriceDisplay(price, finalPrice);
		
		// # @todo we should not depend on this script use magento attributes instead
		if(typeof(priceChanged) != 'undefined'){
			priceChanged(parseFloat( finalPrice ));
		}
        /*if(this.config.updateShortDescription) {
          this.updateProductShortDescription(childProductId);
        }
        if(this.config.updateDescription) {
          this.updateProductDescription(childProductId);
        }
        if(this.config.updateProductName) {
          this.updateProductName(childProductId);
        }
		*/ 
        this.updateProductAttributes(childProductId);
        if(this.config.customStockDisplay) {
        	this.updateProductAvailability(childProductId);
        }       
      //  this.updateFormProductId(childProductId);
      //  this.addParentProductIdToCartForm(this.config.productId);
        this.showCustomOptionsBlock(childProductId, this.config.productId);
        if (usingZoomer) {
            this.showFullImageDiv(childProductId, this.config.productId);
        }else{
            this.updateProductImage(childProductId);
        }

    } else {
        var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice");
        var price = childProducts[cheapestPid]["price"];
        var finalPrice = childProducts[cheapestPid]["finalPrice"];
        optionsPrice.productPrice = finalPrice;
        optionsPrice.productOldPrice = price;
        optionsPrice.reload();
        optionsPrice.reloadPriceLabels(false);
        optionsPrice.updateSpecialPriceDisplay(price, finalPrice);
		
		// # @todo we should not depend on this script use magento attributes instead
		if(typeof(priceChanged) != 'undefined'){
			priceChanged(finalPrice);
		}
        //this.updateProductShortDescription(false);
        //this.updateProductDescription(false);
      //  this.updateProductName(false);
        this.updateProductAttributes(false);
        if(this.config.customStockDisplay) {
        	this.updateProductAvailability(false);
        } 
        this.showCustomOptionsBlock(false, false);
        if (usingZoomer) {
            this.showFullImageDiv(false, false);
        } else {
        	//this.ColorSwitcher(childProducts[cheapestPid]["image"]);
        }
    }
};


Product.Config.prototype.ColorSwitcher = function(imageURL) {
	var mainImgObj = document.getElementById("productMainImg");
	    var iw = mainImgObj.width;
	    var ih = mainImgObj.height;
	  mainImgObj.width = iw;
	  mainImgObj.height = ih;
	  mainImgObj.src = imageURL;
};

/*Product.Config.prototype.updateProductImage = function(productId) {
    var imageUrl = this.config.imageUrl;
    if(productId && this.config.childProducts[productId].imageUrl) {
        imageUrl = this.config.childProducts[productId].imageUrl;
    }
    if (!imageUrl) {
        return;
    }
    if($('image')) {
        $('image').src = imageUrl;
    } else {
        $$('#product_addtocart_form p.product-image img').each(function(el) {
            var dims = el.getDimensions();
            el.src = imageUrl;
            el.width = dims.width;
            el.height = dims.height;
        });
    }
};*/

Product.Config.prototype.updateProductName = function(productId) {
      
    if (productId && this.config.ProductNames[productId].ProductName) {
    	productName = this.config.ProductNames[productId].ProductName;
    }
    $$('#product_addtocart_form div.product-name h1').each(function(el) {
        el.innerHTML = productName;
    });
};

Product.Config.prototype.updateProductAvailability = function(productId) {
	var stockInfo = this.config.stockInfo;
    if (productId && stockInfo[productId]["stockLabel"]) {
    	stockLabel = stockInfo[productId]["stockLabel"];
    	stockQty = stockInfo[productId]["stockQty"];
    	is_in_stock =  stockInfo[productId]["is_in_stock"];
    }
    $$('#product_addtocart_form p.availability span').each(function(el) {
    	if(is_in_stock) {
    		 el.innerHTML = stockQty + '  ' + stockLabel;
    	} else {
    		 el.innerHTML = stockLabel;
    	}
       
    });
};

Product.Config.prototype.updateProductShortDescription = function(productId) {
   // var shortDescription = '';
    if (productId && this.config.shortDescriptions[productId].shortDescription) {
        shortDescription = this.config.shortDescriptions[productId].shortDescription;
    }
    $$('#product_addtocart_form div.short-description div.std').each(function(el) {
        el.innerHTML = shortDescription;
    });
};

Product.Config.prototype.updateProductDescription = function(productId) {
   // var description = '';
	if (productId && this.config.Descriptions[productId].Description) {
        description = this.config.Descriptions[productId].Description;
    }
    $$('div.box-description div.std').each(function(el) {
        el.innerHTML = description;
    });
};

Product.Config.prototype.updateProductAttributes = function(productId) {
    //var productAttributes = this.config.productAttributes;
    /*if (productId && this.config.childProducts[productId].productAttributes) {
        productAttributes = this.config.childProducts[productId].productAttributes;
    }*/
	
	// update stone details
	if(typeof(variations) != 'undefined')
	    variations.updateProductDetails(productId);
};

Product.Config.prototype.showCustomOptionsBlock = function(productId, parentId) {
    var coUrl = this.config.ajaxBaseUrl + "co/?id=" + productId + '&pid=' + parentId;
    var prodForm = $('product_addtocart_form');

   if ($('SCPcustomOptionsDiv')==null) {
      return;
   }

    Effect.Fade('SCPcustomOptionsDiv', { duration: 0.5, from: 1, to: 0.5 });
    if(productId) {
        //Uncomment the line below if you want an ajax loader to appear while any custom
        //options are being loaded.
        //$$('span.scp-please-wait').each(function(el) {el.show()});

        //prodForm.getElements().each(function(el) {el.disable()});
        new Ajax.Updater('SCPcustomOptionsDiv', coUrl, {
          method: 'get',
          evalScripts: true,
          onComplete: function() {
              $$('span.scp-please-wait').each(function(el) {el.hide()});
              Effect.Fade('SCPcustomOptionsDiv', { duration: 0.5, from: 0.5, to: 1 });
              //prodForm.getElements().each(function(el) {el.enable()});
          }
        });
    } else {
        $('SCPcustomOptionsDiv').innerHTML = '';
        try{window.opConfig = new Product.Options([]);} catch(e){}
    }
};


Product.OptionsPrice.prototype.reloadPriceLabels = function(productPriceIsKnown) {
    var priceFromLabel = '';
    var prodForm = $('product_addtocart_form');

    if (!productPriceIsKnown && typeof spConfig != "undefined") {
        priceFromLabel = spConfig.config.priceFromLabel;
    }

    var priceSpanId = 'configurable-price-from-' + this.productId;
    var duplicatePriceSpanId = priceSpanId + this.duplicateIdSuffix;

    if($(priceSpanId) && $(priceSpanId).select('span.configurable-price-from-label'))
        $(priceSpanId).select('span.configurable-price-from-label').each(function(label) {
        label.innerHTML = priceFromLabel;
    });

    if ($(duplicatePriceSpanId) && $(duplicatePriceSpanId).select('span.configurable-price-from-label')) {
        $(duplicatePriceSpanId).select('span.configurable-price-from-label').each(function(label) {
            label.innerHTML = priceFromLabel;
        });
    }
};


Product.Config.prototype.configure = function(event){
	var element = Event.element(event);
	this.configureElement(element);
}

//SCP: Forces the 'next' element to have it's optionLabels reloaded too
Product.Config.prototype.configureElement = function(element) {
    //this.reloadOptionLabels(element);
    if(element.value){
        this.state[element.config.id] = element.value;
        if(element.nextSetting){
            element.nextSetting.disabled = false;
            //this.fillSelect(element.nextSetting);
            //this.reloadOptionLabels(element.nextSetting);
            //this.resetChildren(element.nextSetting);
        }
    }
    else {
        //this.resetChildren(element);
    }
    this.reloadPrice();
};


//SCP: Changed logic to use absolute price ranges rather than price differentials
Product.Config.prototype.reloadOptionLabels = function(element){
    var selectedPrice;
    var childProducts = this.config.childProducts;
    var stockInfo = this.config.stockInfo;

    //Don't update elements that have a selected option
    if(element.options[element.selectedIndex].config){
        return;
    }

    for(var i=0;i<element.options.length;i++){
        if(element.options[i].config){
            var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice", element.options[i].config.allowedProducts);
            var mostExpensivePid = this.getProductIdOfMostExpensiveProductInScope("finalPrice", element.options[i].config.allowedProducts);
            var cheapestFinalPrice = childProducts[cheapestPid]["finalPrice"];
            var mostExpensiveFinalPrice = childProducts[mostExpensivePid]["finalPrice"];
            var stock = stockInfo[cheapestPid]["stockLabel"];
            element.options[i].text = this.getOptionLabel(element.options[i].config, cheapestFinalPrice, mostExpensiveFinalPrice, stock);
        }
    }
};


Product.Config.prototype.fillSelect = function(element){
	var attributeId = element.id.replace(/[a-z]*/, '');
	var options = this.getAttributeOptions(attributeId);
	//this.clearSelect(element);
	//element.options[0] = new Option(this.config.chooseText, '');

	var prevConfig = false;
	if(element.prevSetting){
		prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
	}

	if(options) {
		var index = 0;
		for(var i=0;i<options.length;i++){
			var allowedProducts = [];
			if(prevConfig) {
				for(var j=0;j<options[i].products.length;j++){
					if(prevConfig.config.allowedProducts
						&& prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
						allowedProducts.push(options[i].products[j]);
					}
				}
			} else {
				allowedProducts = options[i].products.clone();
			}

			if(allowedProducts.size()>0){
				options[i].allowedProducts = allowedProducts;
				element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
				if(options[i].id == element.config.defaultOption){
					element.options[index].selected = true;
				}
				if (typeof options[i].price != 'undefined') {
					element.options[index].setAttribute('price', options[i].price);
				}
				element.options[index].config = options[i];
				index++;
			}
		}
	}
}

//SCP: Changed label formatting to show absolute price ranges rather than price differentials
Product.Config.prototype.getOptionLabel = function(option, lowPrice, highPrice, stock){

    var str = option.label;

    if (!this.config.showPriceRangesInOptions) {
        return str;
    }
    
    if (!this.config.showOutOfStock){
    	stock = '';
    }
	
	if (this.config.hideprices) {
    	 return str;
    }
	

    var to = ' ' + this.config.rangeToLabel + ' ';
    var separator = ': ( ';

    lowPrices = this.getTaxPrices(lowPrice);
    highPrices = this.getTaxPrices(highPrice);

    if(lowPrice && highPrice){
        if (lowPrice != highPrice) {
            if (this.taxConfig.showBothPrices) {
                str+= separator + this.formatPrice(lowPrices[2], false) + ' (' + this.formatPrice(lowPrices[1], false) + ' ' + this.taxConfig.inclTaxTitle + ')';
                str+= to + this.formatPrice(highPrices[2], false) + ' (' + this.formatPrice(highPrices[1], false) + ' ' + this.taxConfig.inclTaxTitle + ')';
                str += " ) ";
            } else {
                str+= separator + this.formatPrice(lowPrices[0], false);
                str+= to + this.formatPrice(highPrices[0], false);
                str += " ) ";
            }
        } else {
            if (this.taxConfig.showBothPrices) {
                str+= separator + this.formatPrice(lowPrices[2], false) + ' (' + this.formatPrice(lowPrices[1], false) + ' ' + this.taxConfig.inclTaxTitle + ')';
                str += " ) ";
                str += stock;
            } else {
                str+= separator + this.formatPrice(lowPrices[0], false);
                str += " ) ";
                str += stock;
            }
        }
    }
    return str;
};


//SCP: Refactored price calculations into separate function
Product.Config.prototype.getTaxPrices = function(price) {
    var price = parseFloat(price);
    if (this.taxConfig.includeTax) {
        var tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
        var excl = price - tax;
        var incl = excl*(1+(this.taxConfig.currentTax/100));
    } else {
        var tax = price * (this.taxConfig.currentTax / 100);
        var excl = price;
        var incl = excl + tax;
    }
    if (this.taxConfig.showIncludeTax || this.taxConfig.showBothPrices) {
        price = incl;
    } else {
        price = excl;
    }

    return [price, incl, excl];
};
//SCP: Forces price labels to be updated on load
//so that first select shows ranges from the start
/*document.observe("dom:loaded", function() {
    //Really only needs to be the first element that has configureElement set on it,
    //rather than all.
    $('product_addtocart_form').getElements().each(function(el) {
        if(el.type == 'select-one') {
            if(el.options && (el.options.length > 1)) {
                //el.options[0].selected = true;
                //spConfig.reloadOptionLabels(el);
            }
        }
    });
});*/


Product.Config.prototype.updateProductImage = function(productId) {
    if(productId && this.config.productImages[productId].length > 0) {
        var images = this.config.productImages[productId];
		if($('image')) {
			$('image').src = images[0].url;
			jQuery('#moreviews').find('ul li:not(.static-view)').remove();
			
			jQuery.each(images, function(index, image){
				if(typeof(image.url) != 'undefined'){
					jQuery('#moreviews').find('ul').append(
						jQuery('<li>').append(
							jQuery('<span>').append(
								jQuery('<img width="63" height="63">')
									.attr('src',image.thumbUrl)
							).addClass('thumbs')
						).click(function(){
							$('image').src = image.url;
							moreViewClicked();
							jQuery('#moreviews').find('ul li').removeClass('active');
							jQuery(this).addClass('active');
						})
					);
				}
			});
			
			jQuery('#moreviews').find('ul li.static-view').each(function(i){
				if(i==0)
					jQuery(this).insertAfter(jQuery('#moreviews').find('ul li:not(.static-view)').last());
				else
					jQuery(this).insertAfter(jQuery('#moreviews').find('ul li.static-view').last());
			});
			
			jQuery('#moreviews').find('ul li').removeClass('active');
			jQuery('#moreviews').find('ul li:first').addClass('active');
			updateMoreViews();
		}
	}
	//console.log(productId);
};


// -------------------------------------
// functions overridden by Hitesh






/*  -------------------------------------------
	Plugin to show product variations on a page
	Author: Hitesh Agarwal
	-------------------------------------------
*/

(function( $ ){
	
  $.fn.Variations = function( config ) {
	  
	var module = this,
		container = $(module),
		userProduct = {},
		userOptions = [],
		stoneSizeAttributeId = false,
		stoneSizes = [],
		stoneSizeImages = [],
		settings = $.extend( {
		  // add default settings here
		}, config),
		
		/*setupProduct = function(){
			
		},*/
		
		updateStoneSizeOptionImages = function(){
			$.each(stoneSizeImages, function(iCounter, stoneSizeImage){
				var matchingOptions = new Array();
				matchingOptions = [{
					'attributeId': stoneSizeAttributeId,
					'optionId': stoneSizeImage["optionId"]
				}];
				var productId = module.getMatchingProduct(matchingOptions);
				stoneSizeImage['element'].attr('src',settings.productImages[productId][0].thumbUrl)
				
			})
		},
		
		beautifyOption = function(optionClickable, optionContainer, option, attributeId, attribute, parent, optionDetailsContainer){
			//console.dir(option);
			var optionDetail = $('<div id="option-details-text'+option.id+'" class="option-details-text"></div>');
			if(attribute.defaultOption == option.id){
				userOptions[attributeId] = option.id;
				optionDetailsContainer.append(optionDetail.show());
			}
			else{
				optionDetailsContainer.append(optionDetail.hide());
			}
			switch(attribute.code){
				case 'stone1_size':
					if(spConfig.getMatchingSimpleProduct()){
						var url = settings.productImages[spConfig.getMatchingSimpleProduct()][0].url
					}else{
						var url = settings.productImages[option.products[0]][0].url;
					}
					stoneSizeAttributeId = attributeId;
					var imageWidth = 98 - (10 * (attribute.options.length - stoneSizeImages.length));
					var imageElement = $('<img width="'+imageWidth+'" height="'+imageWidth+'" src="' + url + '" />');
					stoneSizeImages.push({
						'optionId': option.id,
						'element': imageElement
					});
					optionClickable.append(
						$('<span>').addClass('user-option-image')
						.append(imageElement)
					);
					
					
					if(typeof(settings.childProducts[option.products[0]].stones[0]) != 'undefined'){
						/* hard coded moissanite & color diamonds total carat weight functionality */
						if(settings.childProducts[option.products[0]].stones[0].name == 'White Moissanite' 
						|| settings.childProducts[option.products[0]].stones[0].name == 'Blue Diamond'
						|| settings.childProducts[option.products[0]].stones[0].name == 'Black Diamond'
						){
							var weightCombined = 0;
							for(var i = settings.childProducts[option.products[0]].stones.length -1; i >= 0; i-- ){
								weightCombined += parseFloat(settings.childProducts[option.products[0]].stones[i].weight);
							}
							$('<div>').append(optionClickable.append('<span class="user-option-title user-option-weight-combined">'+(weightCombined.toFixed(2) + ((weightCombined == 1)?' carat':' carats'))+'</span>')).appendTo(optionContainer);
							optionDetail.html(getCombinedStoneSizeDescription(settings.childProducts[option.products[0]].stones));
						}
						else{
							$('<div>').append(optionClickable.append('<span class="user-option-title">'+settings.childProducts[option.products[0]].stones[0].weight+'</span>')).appendTo(optionContainer);
							optionDetail.html(getStoneSizeDescription(settings.childProducts[option.products[0]].stones));
						}
					}
					else{
						$('<div>').append(optionClickable.append('<span class="user-option-title">'+option.label+'</span>')).appendTo(optionContainer);
						optionDetail.html(getStoneSizeDescription(settings.childProducts[option.products[0]].stones));
					}
					break;
				case 'stone1_grade':
					optionClickable.append('<span class="user-option-image user-option-stone-image '+option.label+'-stone-'+settings.stone1_name.replace(/\W/g,'')+'"></span>');
					var label = option.label;
					if(label == 'A') label = 'Good';
					if(label == 'AA') label = 'Better';
					if(label == 'AAA') label = 'Best';
					if(label == 'AAAA') label = 'Heirloom';
					optionContainer.append(
						$('<div>').append(optionClickable.append('<span class="user-option-title">'+label+'</span>'))
					);
					optionDetail.html(getStoneGradeDescription(settings.stone1_name, option.label));
					break;
				case 'metal1_type':
					var metalAlias = 'WG';
					if(option.label == '14K Yellow Gold'){
						metalAlias = 'YG';
					}
					optionClickable.append('<span class="user-option-image user-option-metal-image metaltype-'+metalAlias+'"></span>');
					optionContainer.append(
						$('<div>').append(optionClickable.append('<span class="user-option-title">'+option.label+'</span>'))
					);
					optionDetail.html(getMetalDescription(option.label));
					break;
				default:
					optionClickable.append('<span class="user-option-image"></span>');
					optionContainer.append(
						$('<div>').append(optionClickable.append('<span class="user-option-title">'+option.label+'</span>'))
					);
					break;
			}
			
			
		},
		
		optionClicked = function(option, attributeId, attribute, parent, optionContainer, optionDetailsContainer){
			// change user option
			$('#attribute'+attributeId).val(option.id);
			//# @todo needed? just for reloadPrice
			spConfig.configureElement($('#attribute'+attributeId));
			variations.updateUserOption(attributeId, option.id);
			
			// custom event triggered when user selects any option
			$(document).trigger('variationChanged',{"id": attribute.code, "value":option.label});
			
			parent.find('.default-user-option').removeClass('default-user-option');
			optionContainer.addClass('default-user-option');
			optionDetailsContainer.find('.option-details-text').hide();
			optionDetailsContainer.find('#option-details-text' + option.id).show();
			if(stoneSizeAttributeId){
				updateStoneSizeOptionImages();
			}
			
			// # @todo use events instead of coding omniture here
			if(typeof(s) != 'undefined'){
				if(attribute.code == 'stone1_size')
					gemstoneSizeSelect(option.label);
				if(attribute.code == 'stone1_grade')
					gemstoneQualitySelect(option.label);
				if(attribute.code == 'metal1_type')
					metalTypeSelect(option.label);
				
			}
			
			/* hard coded moissanite & color diamonds total carat weight functionality */
			if(	$('.default-user-option .user-option-weight-combined').length > 0 ){
							var weightCombined = $('.default-user-option .user-option-weight-combined').text();
							if(weightCombined){
								$('#user-selection-details .dyn_stone1_weight').text(weightCombined);
							}
			}
			
		},
		
		optionMarkup = function(option, attributeId, attribute, parent, optionDetailsContainer){
			var optionContainer = $('<div class="option-container" id="option-container'+option.id+'">').appendTo(parent);
			// setup default user option
			if(attribute.defaultOption == option.id){
				optionContainer.addClass('default-user-option');
				$('#user-selection-details').append('<div class="detailrow"><span class="detail-title">Selected '+(attribute.code == 'stone1_size'?'Total Carat Weight':attribute.label)+':</span> <span class="detail-value dyn_'+(attribute.code == 'stone1_size'?'stone1_weight':attribute.code)+'">'+(attribute.code == 'stone1_size'?'':option.label)+'</span></div>');
				$('#user-option-tabs ul').append(
					$('<li id="option-list-tab'+attributeId+'"><span>'+(attribute.code == 'stone1_size'?'Total Carat Weight':attribute.label)+'</span></li>').addClass('option-list-tab')
						.click(function(){
							selectTab(attributeId);
						})
				);
		
				// custom event triggered when default option initialized
				$(document).trigger('variationInitialized',{"id": attribute.code, "value":option.label});
				// # @todo use events instead of coding omniture here
				if(typeof(s) != 'undefined'){
					if(attribute.code == 'stone1_size')
						defaultGemstoneSizeSelect(option.label);
					if(attribute.code == 'stone1_grade')
						defaultGemstoneQualitySelect(option.label);
					if(attribute.code == 'metal1_type')
						defaultMetalTypeSelect(option.label);
					
				}
				
			}
			
			var optionClickable = $('<div class="user-option-clickable"></div>');

			beautifyOption(optionClickable, optionContainer, option, attributeId, attribute, parent, optionDetailsContainer);

			optionClickable.click(function(){
				optionClicked(option, attributeId, attribute, parent, optionContainer, optionDetailsContainer)
			})
		},

		optionListMarkup = function(attributeId, attribute, parent){
			var optionListContainer = $('<div class="option-list-container" id="option-list-container'+attributeId+'" style="display:none">').appendTo(parent);
			var optionDetailsContainer = $('<div class="option-details-container"></div>');
			optionListContainer.append('<div class="customize-option-title">Select '+(attribute.code == 'stone1_size'?'Total Carat Weight':attribute.label)+'</div>');
			$.each(attribute.options,function(iCounter,option){optionMarkup(option, attributeId, attribute, optionListContainer, optionDetailsContainer)});
			//optionListContainer.find('.option-container:last').addClass('last-user-option');
			optionListContainer.find('.option-container:eq(3)').addClass('last-user-option');
			optionListContainer.append('<div style="clear:both"></div>')
			optionListContainer.append(optionDetailsContainer);
		},
		
		// generate variation html here
		variationsMarkup = function(){
			var variationsContainer = $('<div id="variations-inner-container">').addClass('stone-'+settings.stone1_shape.replace(/\W/g,'')+'-'+settings.stone1_name.replace(/\W/g,'')).appendTo(container);
			$('#user-option-tabs').append('<ul>');
			$.each(settings.attributes,function(iCounter,attribute){optionListMarkup(iCounter, attribute, variationsContainer)});
			$('#user-option-tabs ul').append(
				$('<li id="option-list-tabOptions"><span>Add-ons</span></li>').addClass('option-list-tab')
					.click(function(){
						selectTab('Options')
					})
			);
			try{
				$('#option-list-containerOptions').appendTo($('#variations-inner-container'));
			}
			catch(e){
				//alert(e);
			}
			selectTab(0);
			$('<div id="customizer-next-btn">').text('Next: '+$('.option-list-tab.active').next().text()).click(function(){
				selectTab($('.option-list-tab.active').index() + 1);
			}).insertBefore($('#addcart'));
			
		},
		
		selectTab = function(tabId){
			if(typeof(tabId) == 'string'){
				$('.option-list-container').hide();
				$('#option-list-container'+tabId).show();
				$('.option-list-tab').removeClass('active');
				$('#option-list-tab'+tabId).addClass('active');
			}else{
				$('.option-list-container').hide();
				$('.option-list-container:eq('+tabId+')').show();
				$('.option-list-tab').removeClass('active');
				$('.option-list-tab:eq('+tabId+')').addClass('active');
			}
			
			if($('#option-list-tabOptions').hasClass('active')){
				$('#customizer-next-btn').hide();
				$('#ultimate-engraving').addClass('engtop');					//	Added by Vaseem for to show Engraving when user click on 4 Add on tab
				$('#ultimate-engraving').removeClass('engtopnone');
			}
			else{
				$('#customizer-next-btn').text('Next: '+$('.option-list-tab.active').next().text()).show();
				$('#ultimate-engraving').removeClass('engtop');					//	Added by Vaseem for Engraving
				$('#ultimate-engraving').addClass('engtopnone');
			}			
		}

		setup = function(){
			/*setupProduct();*/
			variationsMarkup();
			//console.dir(settings);
			return module;
		},
		
		getStoneSizeDescription = function(stones){
			var html = '<strong>Approximate Gemstone Carat Weight:</strong> ';
			var stoneDetails = new Array();
			$.each(stones,function(iCounter,stone){
				var stoneDetail = stone.weight + ' of ';
				/*if(stone.count > 1){
					if(stone.name == 'Ruby')
						stoneDetail += 'rubies';
					else
						stoneDetail += stone.name.toLowerCase() + 's';
				}*/
				//else{
					stoneDetail += stone.name.toLowerCase();
				//}
				stoneDetails.push(stoneDetail);
			});
			html += stoneDetails.join(', ') + '.';
			return html;
			// e.g. 0.35 carats of Blue Sapphire, 0.14 carats of Diamonds.'
		},
		
		getCombinedStoneSizeDescription = function(stones){
			var html = '<strong>Center Stone Carat Weight:</strong> ';
			var stoneDetails = new Array();
			$.each(stones,function(iCounter,stone){
				var stoneDetail = stone.weight + ' of ';
				/*if(stone.count > 1){
					if(stone.name == 'Ruby')
						stoneDetail += 'rubies';
					else
						stoneDetail += stone.name.toLowerCase() + 's';
				}*/
				//else{
					stoneDetail += stone.name.toLowerCase();
				//}
				stoneDetails.push(stoneDetail);
			});
			html += stoneDetails.join(', ') + '.';
			return html;
			// e.g. 0.35 carats of Blue Sapphire, 0.14 carats of Diamonds.'
		},
		
		getStoneGradeDescription = function(stone, grade){
			if(stone == 'Blue Sapphire'){
				stone = 'Sapphire';
			}
			
			switch(stone+grade){
				case 'SapphireA':
					return '<strong>Quality Grade - Good (A): </strong> Top 75% of sapphires in terms of quality.  Dark blue and opaque.  This quality is comparable to that used by mall jewelry and chain stores.';
					break;
				case 'SapphireAA':
					return '<strong>Quality Grade - Better (AA): </strong> Top 33% of sapphires in terms of quality.  Medium blue and moderately included.  This quality is comparable to that used by leading independent/family jewelers.';
					break;
				case 'SapphireAAA':
					return '<strong>Quality Grade - Best (AAA): </strong> Top 10% of sapphires in terms of quality.  Medium blue, slightly included and exhibits high brilliance.  This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					break;
				case 'SapphireAAAA':
					return '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of sapphires in terms of quality.  Truly exceptional deep rich blue, very slightly included and exhibits high brilliance.  This quality can be found only at the top boutiques in the world.';
					break;
				
				case 'RubyA':
					return '<strong>Quality Grade - Good (A): </strong> Top 75% of rubies in terms of quality. &nbsp;Dark pinkish red and opaque. &nbsp;This quality is comparable to that used by mall jewelry and chain stores.';
					break;
				case 'RubyAA':
					return '<strong>Quality Grade - Better (AA): </strong> Top 33% of rubies in terms of quality. &nbsp;Medium pinkish red and moderately included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
					break;
				case 'RubyAAA':
					return '<strong>Quality Grade - Best (AAA): </strong> Top 10% of rubies in terms of quality. &nbsp;Medium red, slightly included and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					break;
				case 'RubyAAAA':
					return '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of rubies, in terms of quality. &nbsp;Truly exceptional deep rich red, very slightly included and exhibits high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
					break;
				
				case 'EmeraldA':
					return '<strong>Quality Grade - Good (A): </strong> Top 75% of emeralds in terms of quality. &nbsp;Dark green and opaque. &nbsp;This quality is comparable to that used by mall jewelry and chain stores.';
					break;
				case 'EmeraldAA':
					return '<strong>Quality Grade - Better (AA): </strong> Top 33% of emeralds in terms of quality. &nbsp;Medium green and heavily included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
					break;
				case 'EmeraldAAA':
					return '<strong>Quality Grade - Best (AAA): </strong> Top 10% of emeralds in terms of quality. &nbsp;Rich medium green, moderately included and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					break;
				case 'EmeraldAAAA':
					return '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of emeralds in terms of quality. &nbsp;Truly exceptional rich green, moderately to slightly included and exhibits high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
					break;
				
				case 'TanzaniteA':
					return '<strong>Quality Grade - Good (A): </strong> Top 75% of tanzanites in terms of quality. &nbsp;Light violet blue and slightly included. &nbsp;This quality is comparable to that used by mall jewelry and chain stores.';
					break;
				case 'TanzaniteAA':
					return '<strong>Quality Grade - Better (AA): </strong> Top 33% of tanzanites in terms of quality. &nbsp;Medium violet blue and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
					break;
				case 'TanzaniteAAA':
					return '<strong>Quality Grade - Best (AAA): </strong> Top 10% of tanzanites in terms of quality. &nbsp;Rich violet blue, eye clean and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					break;
				case 'TanzaniteAAAA':
					return '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of tanzanites in terms of quality. &nbsp;Truly exceptional rich violet blue, eye clean and exhibits very high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
					break;
				
				case 'AquamarineA':
					return '<strong>Quality Grade - Good (A): </strong> Top 75% of aquamarines in terms of quality. &nbsp;Very light sea blue and moderately included. &nbsp;This quality is comparable to that used by mall jewelry and chain stores.';
					break;
				case 'AquamarineAA':
					return '<strong>Quality Grade - Better (AA): </strong> Top 33% of aquamarines in terms of quality. &nbsp;Light sea blue and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
					break;
				case 'AquamarineAAA':
					return '<strong>Quality Grade - Best (AAA): </strong> Top 10% of aquamarines in terms of quality. &nbsp;Medium sea blue, eye clean and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					break;
				case 'AquamarineAAAA':
					return '<strong>Quality Grade - Heirloom (AAAA): </strong> Top 1% of aquamarines in terms of quality. &nbsp;Truly exceptional medium sea blue, eye clean and exhibits very high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
					break;
			}
			
		},
		
		getMetalDescription = function(metal){
			switch (metal){
				case 'Silver':
					return '<strong>Silver:</strong> The same great look as white gold at a lower price.  Angara uses the highest standard available, .925 sterling silver.  Silver is lighter to wear than gold.';
					break
				case '14K White Gold':
					return '<strong>14K White Gold:</strong> The standard for fine jewelry.  14K White gold has been the most popular choice for fine jewelry over the last twenty years as it blends well with diamonds.';
					break
				case '14K Yellow Gold':
					return '<strong>14K Yellow Gold:</strong> Glowing and rich, yellow gold adds more color to the jewelry piece.  If buying as a gift, please note that women generally have a clear preference for Yellow or White Gold - see what other jewelry she has to determine her preference.';
					break
				case '14K Rose Gold':
					return '<strong>14K Rose Gold:</strong> Glowing and rich, rose gold adds more color to the jewelry piece. If buying as a gift, please note that women generally have a clear preference for Rose or Yellow Gold - see what other jewelry she has to determine her preference.';
					break
				case 'Platinum':
					return '<strong>Platinum:</strong> The most durable and premium metal for fine jewelry.  Platinum feels more substantial to wear due to its greater weight.';
					break
			}			
		}
		;
	
	// Public functions
	
	module.getMatchingProduct = function(matchingOptions){
		var options = new Array();
		$.each(matchingOptions, function(iCounter, matchingOption){
			options[matchingOption['attributeId']] = matchingOption['optionId'];
		})
		
		var childProducts = spConfig.childProducts;
	
		for(var s=0, len = spConfig.settings.length - 1; s <= len; s++) {
			var selected = spConfig.settings[s].options[spConfig.settings[s].selectedIndex];
			
			if (s==0){
            	allowedProducts = selected.config.allowedProducts;
			} else {
				allowedProducts = allowedProducts.intersect(selected.config.allowedProducts).uniq();
			}
		}
		
		return allowedProducts[0];
	};
	
	module.updateUserOption = function(attributeId, optionId){
		userOptions[attributeId] = optionId;
	};
	
	module.getCurrentProduct = function(){
		var products = spConfig.getInScopeProductIds();
		return products[0];
	};
	
	module.updateProductDetails = function(productId){
		// # @todo remove extra stone info in case of StoneVariationCount is less than default or increase it in case it is greater than default value.
		if (productId){
			
			if(settings.childProducts[productId].stones.length < $('.stone-detail-box-container').length){
				$('.stone-detail-box-container:gt('+(settings.childProducts[productId].stones.length-1)+')').hide();
			}
			else{
				$('.stone-detail-box-container').show();
			}
			
			for(var iCounter=0; iCounter < settings.childProducts[productId].stones.length;  iCounter++){	// looping all stones
				$.each(settings.childProducts[productId].stones[iCounter],function(label,value){ 	// looping all details of stone
					$('.dyn_stone'+(iCounter+1)+'_'+label).text(value);		// replacing stone details
				});
			}
			for(var iCounter=0; iCounter < settings.childProducts[productId].metals.length;  iCounter++){	// looping all metals
				$.each(settings.childProducts[productId].metals[iCounter],function(label,value){ 	// looping all details of stone
					$('.dyn_metal'+(iCounter+1)+'_'+label).text(value);		// replacing stone details
				});
			}
			
			$('.dyn_msrp').text(optionsPrice.formatPrice(settings.childProducts[productId].msrp));
			jewelProduct.setVendorLeadTime(settings.childProducts[productId].vendorLeadTime);
			jewelProduct.updateShippingDate();
			
			/* hard coded moissanite & color diamonds total carat weight functionality */
			if(	$('.default-user-option .user-option-weight-combined').length > 0 ){
				var weightCombined = $('.default-user-option .user-option-weight-combined').text();
				if(weightCombined){
					$('#user-selection-details .dyn_stone1_weight').text(weightCombined);
				}
			}
			
			// # @todo implement retail price change logic
			//$('.dyn_msrp').text(optionsPrice.priceFormat.pattern + (settings.childProducts[productId].msrp).toFixed(2));
			
			// # @todo implement shipping date change logic
		}
	};
	
	module.addStoneDetails = function(stoneIndex, stone){
		$('.stones-details-box').append('<div class="stone-detail-box-container stone-detail-box-odd">'+
			'<div class="detail-box-title">'+
				'<span class="dyn_stone'+stoneIndex+'_type"></span> Information:'+
			'</div>'+
			'<div class="field">'+
				'<div class="fieldtitle">Number of <span class="dyn_stone'+stoneIndex+'_shape"></span> <span class="dyn_stone'+stoneIndex+'_name"></span>:</div>'+
				'<div class="fieldvalue dyn_stone'+stoneIndex+'_count"></div>'+
				'<div style="clear:both"></div>'+
			'</div>'+
			'<div class="field">'+
				'<div class="fieldtitle">Approximate Dimensions:'+((stone['name'] == 'Ruby' ||  stone['name'] == 'Blue Sapphire' || stone['name'] == 'Emerald' || stone['name'] == 'Tanzanite')?'<span class="popup-icon" onclick="jewelProduct.showStonePopup(\'stone size\', \''+stone["name"]+'\', \''+stone["shape"]+'\')"></span>':'')+'</div>'+
				'<div class="fieldvalue dyn_stone'+stoneIndex+'_size"></div>'+
				'<div style="clear:both"></div>'+
			'</div>'+
			'<div class="field">'+
				'<div class="fieldtitle">Approximate Carat Total Weight:'+((stone['name'] == 'Ruby' ||  stone['name'] == 'Blue Sapphire' || stone['name'] == 'Emerald' || stone['name'] == 'Tanzanite')?'<span class="popup-icon" onclick="jewelProduct.showStonePopup(\'stone size\', \''+stone["name"]+'\', \''+stone["shape"]+'\')"></span>':'')+'</div>'+
				'<div class="fieldvalue dyn_stone'+stoneIndex+'_weight"></div>'+
				'<div style="clear:both"></div>'+
			'</div>'+
			'<div class="field">'+
				'<div class="fieldtitle">Quality Grade:'+((stone['name'] == 'Ruby' ||  stone['name'] == 'Blue Sapphire' || stone['name'] == 'Emerald' || stone['name'] == 'Tanzanite' || stone['name'] == 'Diamond')?'<span class="popup-icon" onclick="jewelProduct.showStonePopup(\'stone quality\', \''+stone["name"]+'\', \''+stone["shape"]+'\')"></span>':'')+'</div>'+
				'<div class="fieldvalue dyn_stone'+stoneIndex+'_grade"></div>'+
				'<div style="clear:both"></div>'+
			'</div></div>')
	};
	
	/* Remove it after usage */
	module.debug = function(){
		console.log('<<<<<<<<<<<<<<< Start Debug >>>>>>>>>>>>')
		console.log('----------- User options -----------')
		console.dir(userOptions);
		console.log('----------- User product -----------')
		console.dir(userProduct);
		//console.log('----------- This module -----------')
		//console.dir(module);
		//console.dir(element);
		console.log('<<<<<<<<<<<<<<< End Debug >>>>>>>>>>>>>>')
	};
	  
    return setup();
	
  };
})( jQuery );