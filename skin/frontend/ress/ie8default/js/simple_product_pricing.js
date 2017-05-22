isXS = false;

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
        return inScopeProductIds;
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

Product.Config.prototype.getPriceRange = function() {
	var priceType = 'finalPrice';
    var childProducts = this.config.childProducts;

    var minPrice = Infinity;
	var maxPrice = 0;

    for (x in childProducts) {
        var thisPrice = Number(childProducts[x][priceType]);
        if (thisPrice < minPrice) {
            minPrice = thisPrice;
        }
		if (thisPrice >= maxPrice) {
            maxPrice = thisPrice;
        }
    }
    return {min:minPrice, max:maxPrice};
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
		//	S:VA
		//var childProductStock =	this.config.stockInfo[childProductId]["stockQty"]
		//this.updateAddToCartButton(childProductStock);
		
        var price = childProducts[childProductId]["price"];
        var finalPrice = childProducts[childProductId]["finalPrice"];
		var categoryIds = childProducts[childProductId]["categoryIds"];
        optionsPrice.productPrice = finalPrice;
        optionsPrice.productOldPrice = price;
        optionsPrice.reload();
        optionsPrice.reloadPriceLabels(true);
        optionsPrice.updateSpecialPriceDisplay(price, finalPrice);
		
		if(typeof(opConfig) != 'undefined')
			opConfig.reloadPrice();
		// # @todo we should not depend on this script use magento attributes instead
		else if(typeof(priceChanged) != 'undefined'){
			priceChanged(parseFloat( finalPrice ), categoryIds);
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
		var categoryIds = childProducts[cheapestPid]["categoryIds"];
        optionsPrice.productPrice = finalPrice;
        optionsPrice.productOldPrice = price;
        optionsPrice.reload();
        optionsPrice.reloadPriceLabels(false);
        optionsPrice.updateSpecialPriceDisplay(price, finalPrice);
		
		if(typeof(opConfig) != 'undefined')
			opConfig.reloadPrice();
		// # @todo we should not depend on this script use magento attributes instead
		else if(typeof(priceChanged) != 'undefined'){
			priceChanged(finalPrice, categoryIds);
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

//	S:VA	-	Show grey button if child product quantity is 0
/*Product.Config.prototype.updateAddToCartButton = function(childProductStock) {
	if(childProductStock=='0'){
		jQuery('.add-to-cart').find('button').addClass('btn-dark-gray').attr('disabled','disabled');
		jQuery('#out-of-stock').removeClass('hide');
	}else{
		jQuery('.add-to-cart').find('button').removeClass('btn-dark-gray').removeAttr('disabled','disabled');
		jQuery('#out-of-stock').addClass('hide');
	}
};*/
//	E:VA


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
    if(element.value){
        this.state[element.config.id] = element.value;
        if(element.nextSetting){
            element.nextSetting.disabled = false;
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
				element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), (this.getOptionLabel(options[i], options[i].price) == 'Select Ring Size')?'':options[i].id);
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
		
			
		currentIndex = 0;
		
		var currentIndex = jQuery('#moreviews').find('ul li.active').index();
		if(currentIndex < 0){
			currentIndex = 0;
		}
		if($('image'))
		$('image').src = images[currentIndex].url;
		jQuery('#moreviews').find('ul li').remove();
		jQuery.each(images, function(index, image){
			if(typeof(image.url) != 'undefined'){
				if(jQuery(window).width() < 768){
					jQuery('#moreviews').find('ul').append(
						jQuery('<li class="img-responsive no-padding pull-left ress-slider-item">').append(
							jQuery('<img width="320" class="img-responsive">')
								.attr('src',image.url)
						)
					);
				}
				else{
					jQuery('#moreviews').find('ul').append(
						jQuery('<li>').append(
							jQuery('<img width="60" height="60">')
								.attr('src',image.thumbUrl)
						).click(function(){
							productThumbnailClicked(this, image.url);
						}).addClass('clickable')
					);
				}
			}
		});
		
		jQuery('#moreviews').find('ul li').removeClass('active');
		jQuery('#moreviews').find('ul li:eq('+currentIndex+')').addClass('active');
		if(jQuery(window).width() > 767){
			updateMoreViews();
		}
	}
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
		bandWidthAttributeId = false,
		metalTypeAttributeId = false,
		stoneSizes = [],
		stoneSizeImages = [],
		bandWidthImages = [],
		bandWidthAttributeId = false,
		metalTypeAttributeId = false,
		optionListCounter = 0,
		showRange = false,
		settings = $.extend( {
		  // add default settings here
		}, config),
		
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
		
		updateBandWidthOptionImages = function(){
			$.each(bandWidthImages, function(iCounter, bandWidthImage){
				
				var matchingOptions = new Array();
				
				matchingOptions = [{
					'attributeId': bandWidthAttributeId,
					'optionId': bandWidthImage["optionId"]
				}];
				var productId = module.getMatchingProduct(matchingOptions);
					bandWidthImage['element'].attr('src',settings.productImages[productId][0].thumbUrl);
				
			})
		},
		
		beautifyOption = function(optionClickable, optionContainer, option, attributeId, attribute, parent, optionDetailsContainer){
			var optionDetail = $('<div id="option-details-text'+option.id+'" class="option-details-text"></div>');
			if(attribute.defaultOption == option.id){
				userOptions[attributeId] = option.id;
				//if(attribute.code=='stone1_grade' && ((location.search.split('stone1_grade=')[1]||'').split('&')[0] == ""))
					//optionDetailsContainer.append(optionDetail.hide());
				//else
					optionDetailsContainer.append(optionDetail.show());
			}
			else{
				optionDetailsContainer.append(optionDetail.hide());
			}
			
			if(attribute.code=='metal1_type'){
				if(attribute.defaultOption == option.id){
					defaultmetal = option.label;
				}
				else{
					var defaultmetal = '';
				}
			}
			
			switch(attribute.code){
				case 'stone1_size':
					if(spConfig.getMatchingSimpleProduct()){
						var url = settings.productImages[spConfig.getMatchingSimpleProduct()][0].url
					}else{
						var url = settings.productImages[option.products[0]][0].url;
					}
					stoneSizeAttributeId = attributeId;
					var imageWidth = 69 - (5 * (attribute.options.length - stoneSizeImages.length));
					var imageElement = $('<img width="'+imageWidth+'" height="'+imageWidth+'" src="' + url + '" />').addClass('imgsize');
					stoneSizeImages.push({
						'optionId': option.id,
						'element': imageElement
					});
					optionClickable.append(
						$('<span>').addClass('user-option-image icon-64')
						.append(imageElement)
					);
					
					if(typeof(settings.childProducts[option.products[0]]['total_weight']) != 'undefined'){
						var optionLabel = settings.childProducts[option.products[0]]['total_weight'];
					}
					else{
						var optionLabel = option.label;
					}
					optionContainer.text(optionLabel);
					//optionDetail.html(getStoneSizeDescription(settings.childProducts[option.products[0]].stones));
					optionDetail.html('<strong>Approximate Carat Weight:</strong> '+settings.childProducts[option.products[0]]['weight_description']+'.');
					break;
				case 'stone1_grade':
					if(typeof(settings.stone1_shape) != 'undefined'){
						optionClickable.append('<span class="user-option-image product-fi user-option-stone-image '+settings.stone1_shape.replace(/\W/g,'-')+'-'+option.label.replace(/\W/g,'-')+'"></span>');
					}
					var label = option.label;
					if(label == 'A') label = 'Good';
					if(label == 'AA') label = 'Better';
					if(label == 'AAA') label = 'Best';
					if(label == 'AAAA') label = 'Heirloom';
					if(label == 'Classic Moissanite') label = 'Classic<br />Moissanite';
					if(label == 'Forever Brilliant') label = 'Forever<br />Brilliant';
					optionContainer.append(
						$('<div class="padding-type-5 low-padding-left low-padding-right">').append(optionClickable.append('<span class="user-option-title">'+label+'</span>'))
					);
					optionDetail.html(getStoneGradeDescription(settings.stone1_name, option.label, settings.stone1_shape));
					
					break;
				case 'stone2_grade':
					if(typeof(settings.stone2_shape) != 'undefined'){
						optionClickable.append('<span class="user-option-image product-fi user-option-stone-image '+settings.stone2_shape.replace(/\W/g,'-')+'-'+option.label.replace(/\W/g,'-')+'"></span>');
					}
					var label = option.label;
					if(label == 'A') label = 'Good';
					if(label == 'AA') label = 'Better';
					if(label == 'AAA') label = 'Best';
					if(label == 'AAAA') label = 'Heirloom';
					if(label == 'Classic Moissanite') label = 'Classic<br />Moissanite';
					if(label == 'Forever Brilliant') label = 'Forever<br />Brilliant';
					optionContainer.append(
						$('<div class="padding-type-5 low-padding-left low-padding-right">').append(optionClickable.append('<span class="user-option-title">'+label+'</span>'))
					);					
				
					optionDetail.html(getStoneGradeDescription(settings.stone2_name, option.label, settings.stone2_shape));
					break;
				case 'metal1_type':
					optionContainer.text(option.label);
					
					optionDetail.html(getMetalDescription(option.label));
					break;
				case 'band_width':
					bandWidthOptions = attribute.options.length;
				 	if(spConfig.getMatchingSimpleProduct()){
						var url = settings.productImages[spConfig.getMatchingSimpleProduct()][0].url
					}
					else{
						var url = settings.productImages[option.products[0]][0].url;
					}
					bandWidthAttributeId = attributeId;
					var imageWidth = 69 - (5 * (attribute.options.length - bandWidthImages.length));
					var imageElement = $('<img width="'+imageWidth+'" height="'+imageWidth+'" src="' + url + '" />').addClass('imgsize');
					bandWidthImages.push({
						'optionId': option.id,
						'element': imageElement
					});
					optionClickable.append(
						$('<span>').addClass('user-option-image icon-64')
						.append(imageElement)
					);
					optionContainer.text(option.label);
					optionDetail.html('<strong>Band Width:</strong> '+settings.childProducts[option.products[0]]['band_width']+'.');
					break;
				default:
					optionClickable.append('<span class="user-option-image product-fi"></span>');
					var optionHtmlContent = $('<div class="padding-type-5">').append(optionClickable).append('<span class="user-option-title">'+option.label+'</span>').html();
					optionContainer.data('content', optionHtmlContent);
					optionContainer.text(option.label);
					break;
			}			
		},
		
		optionClicked = function(option, attributeId, attribute, parent, optionContainer, optionDetailsContainer){
			parent.find('.default-user-option').removeClass('default-user-option').children().removeClass('showcase-border-thick padding-type-3 border-active').addClass('padding-type-5 low-padding-left low-padding-right no-transition');
			if(attribute.code=='stone1_grade' || attribute.code=='stone2_grade'){
				optionContainer.addClass('default-user-option').children().addClass('showcase-border-thick padding-type-3 border-active no-transition').removeClass('padding-type-5');
				$('#add-cart-stopper').remove();
				$('#add-cart-stopper-msg').remove();
				hidePriceRange();
			}
			else{
				if($('#add-cart-stopper').length == 1){
					$('#add-cart-stopper-msg').removeClass('hidden');
				}
			}
			optionDetailsContainer.find('.option-details-text').hide();
			optionDetailsContainer.find('#option-details-text' + option.id).show();
			if(stoneSizeAttributeId && attribute.code != 'stone1_size'){
				updateStoneSizeOptionImages();
			}
						
			// change user option
			$('#attribute'+attributeId).val(option.id);
			//# @todo needed? just for reloadPrice
			spConfig.configureElement($('#attribute'+attributeId));
			variations.updateUserOption(attributeId, option.id);
			
			if(bandWidthAttributeId && attribute.code != 'band_width'){
				updateBandWidthOptionImages();
			}
			
			// custom event triggered when user selects any option
			$(document).trigger('variationChanged',{"id": attribute.code, "value":option.label});
			
			// # @todo use events instead of coding omniture here
			if(typeof(s) != 'undefined'){
				if(attribute.code == 'stone1_size')
					gemstoneSizeSelect(option.label);
				if(attribute.code == 'stone1_grade' || attribute.code == 'stone2_grade')
					gemstoneQualitySelect(option.label);
				if(attribute.code == 'metal1_type')
					metalTypeSelect(option.label);
				if(attribute.code == 'band_width')
					metalTypeSelect(option.label);
			}
			
		},
		
		optionMarkup = function(option, attributeId, attribute, parent, optionDetailsContainer){
			if(attribute.code=='stone1_grade' || attribute.code=='stone2_grade')
				//	S:VA Dynamic width of variation box
				var optionContainer = $('<div class="option-container pull-left customize-product-option no-transition" id="option-container'+option.id+'" style="width:'+(100/attribute.options.length)+'%">').appendTo(parent);
			else
				var optionContainer = $('<option value="'+option.id+'" class="option-container"></option>').appendTo(parent);
			
			var optionClickable = $('<div class="user-option-clickable text-center clickable"></div>');

			beautifyOption(optionClickable, optionContainer, option, attributeId, attribute, parent, optionDetailsContainer);	
			
			// setup default user option
			if(attribute.defaultOption == option.id){
				//$(function(){
					if(attribute.code=='stone1_grade' || attribute.code=='stone2_grade'){
						//if((location.search.split('stone1_grade=')[1]||'').split('&')[0] != ""){
							optionContainer.addClass('default-user-option').children().addClass('showcase-border-thick padding-type-3 border-active no-transition').removeClass('padding-type-5');
						//}
						/*else{
							showRange = true;
							$(function(){
								$('#authenticity-cert').addClass('hidden')
							});
							optionContainer.append('<div id="add-cart-stopper" class="hidden"><input class="required-entry" type="hidden" value="" name="add-cart-stopper" /></div>');
							/*var priceRange = spConfig.getPriceRange();
							if(priceRange.min < priceRange.max){
								$('#product-price-'+$('input[name="product"]').val()).addClass('hidden').after('<span id="product-price-range" class="regular-price"><span class="price">'+optionsPrice.formatPrice(priceRange.min)+' - '+optionsPrice.formatPrice(priceRange.max)+'</span></span>');
								$('#product-retail-price').addClass('hidden').after('<span id="product-retail-price-range">Was: <del>' + optionsPrice.formatPrice((parseInt((priceRange.min/6) + 1) * 10) - 1) + ' - ' +optionsPrice.formatPrice((parseInt((priceRange.max/6) + 1) * 10) - 1)+ '</del></span>');
							}*/
							//$('#easy-pay-box').hide();
						//}
					}
					else
						optionContainer.attr('selected','selected');
				//})
				// # @todo use events instead of coding omniture here
				if(typeof(s) != 'undefined'){
					if(attribute.code == 'stone1_size')
						defaultGemstoneSizeSelect(option.label);
					if(attribute.code == 'stone1_grade' || attribute.code == 'stone2_grade')
						defaultGemstoneQualitySelect(option.label);
					if(attribute.code == 'metal1_type')
						defaultMetalTypeSelect(option.label);						
				}				
			}
		
			if(attribute.code=='stone1_grade' || attribute.code=='stone2_grade'){
				optionClickable.click(function(){
					optionClicked(option, attributeId, attribute, parent, optionContainer, optionDetailsContainer)
				})
			}
			else{
				parent.change(function(){
					if (/Android/.test(navigator.userAgent)){
						parent.blur();
					}
					if(option.id == $(this).val()){
						optionClicked(option, attributeId, attribute, parent, optionContainer, optionDetailsContainer)
					}
				})
			}
		},

		optionListMarkup = function(attributeId, attribute, parent){
			if(attribute.code!='ring_size'){
				var optionListContainer = $('<div class="option-list-container customize-product-tab-content clearfix" id="option-list-container'+attributeId+'">');
				if(attribute.code=='stone1_grade' || attribute.code=='stone2_grade')
					var optionListClickableOptions = $('<div class="col-md-6 col-sm-12 pull-left stone-holder-box">');
				else{
					var optionListClickableOptions = $('<select class="special-variation-select form-control'+((isXS)?' input-text':'')+'">');
				}
				var optionDetailsContainer = $('<div>');
				$.each(attribute.options,function(iCounter,option){
					optionMarkup(option, attributeId, attribute, optionListClickableOptions, optionDetailsContainer)
				});
				
				if(attribute.code=='stone1_grade' || attribute.code=='stone2_grade'){
					optionListContainer.append($('<div class="col-md-2 col-sm-12 customize-product-tab" style="padding-left:0px" id="option-list-tab'+attributeId+'"><div class="low-padding-top low-padding-bottom h3">'+((spConfig.config.isBuildYourOwn) ? 'Diamond 2 Grade' : attribute.label)+'<span id="add-cart-stopper-msg" class="hidden validation-advice text-normal"> (Please select stone quality first.)</span></div></div>').addClass('option-list-tab'));
					//	S:VA Dynamic width of variation box
					optionListContainer.append($('<div class="col-sm-9 col-md-10 option-hldr">').append(optionListClickableOptions).append($('<div class="clearfix hidden-md"></div>')).append($('<div class="option-details-container col-md-6 col-sm-12 high-margin-top-sm hidden-xs">').append($('<div class="small">').append(optionDetailsContainer)))).prependTo(parent);
				}
				else{
					optionListContainer.append($('<div class="col-md-2 col-sm-12 customize-product-tab" style="padding-left:0px" id="option-list-tab'+attributeId+'"><div class="low-padding-top low-padding-bottom h3">'+(attribute.code == 'stone1_size'?'Total Carat Weight':attribute.label)+'</div></div>').addClass('option-list-tab'));
					optionListContainer.append($('<div class="col-sm-9 col-md-10 option-hldr">').append($('<div class="col-sm-12 col-md-6 padding-type-0">').append($('<div class="min-padding-top min-padding-bottom">').append(optionListClickableOptions))).append($('<div class="clearfix hidden-md"></div>')).append($('<div class="option-details-container col-md-6 col-sm-12  high-margin-top-sm hidden-xs">').append($('<div class="small">').append(optionDetailsContainer)))).appendTo(parent);
				}
			}
		},
		
		// generate variation html here
		variationsMarkup = function(){
			var variationsContainer = $('<div id="customize-product-panel">').appendTo(container);
			if($(window).width() < 768)
				variationsContainer.hide();
			if(typeof(settings.stone1_name) != 'undefined')
				variationsContainer.addClass('Stone-'+settings.stone1_name.replace(/\W/g,''));
			if(typeof(settings.stone2_name) != 'undefined')
				variationsContainer.addClass('Stone-'+settings.stone2_name.replace(/\W/g,''));
				
			var optionsContainer = $('<div class="min-padding-bottom clearfix">');
			var attributeOptions = false;
			$.each(settings.attributes,function(iCounter,attribute){
				if(attribute.options.length > 1){ // show when option having more than one
					attributeOptions = true;
					optionListMarkup(iCounter, attribute, optionsContainer)
				}	
				else{
					jQuery('#productOptions').addClass('hidden');
				}		
			});
			
			if(attributeOptions == false){
				if($(window).width() < 768 && !$('.add-to-box #customize-product').hasClass('hide')){
					$(function(){
						$('.add-to-box #customize-product').addClass('hide');
					});
				}
			}
			variationsContainer.append($('<div class="row no-gutters">').append($('<div class="col-xs-12">').append(optionsContainer)));			
		},
		
		hidePriceRange = function(){
			$('#product-price-'+$('input[name="product"]').val()).removeClass('hidden');
			$('#product-price-range-'+$('input[name="product"]').val()).remove();
			$('#product-retail-price-'+$('input[name="product"]').val()).removeClass('hidden');
			$('#product-retail-price-range-'+$('input[name="product"]').val()).remove();
			$('#easy-pay-box').show();
			$('#authenticity-cert').removeClass('hidden');
		},

		setup = function(){
			if($(window).width() < 768){
				isXS = true
			}
			if(typeof(settings.jewelry_type) != 'undefined' && (settings.jewelry_type == "Ring" || settings.jewelry_type == "Band")){
				optionListCounter = 1;
			}
			variationsMarkup();
			
			if(!showRange){
				hidePriceRange();
			}
			
			$(function(){
				$('.user-selection-step-count').each(function(){
					$(this).text( (++optionListCounter) + '. ');
				})
			})
			
			//var totalTabs = $('#user-option-tabs .col-sm-x').length;
			/*$('#user-option-tabs .col-sm-x').each(function() {
				$(this).removeClass('col-sm-x').addClass('col-sm-' + (12 / totalTabs));
			});	*/		
			return module;
		},
		
		getStoneSizeDescription = function(stones){
			var html = '<strong>Approximate Gemstone Carat Weight:</strong> ';
			var stoneDetails = new Array();
			$.each(stones,function(iCounter,stone){
				var stoneDetail = stone.weight + ' of ';
				stoneDetail += stone.name.toLowerCase();
				stoneDetails.push(stoneDetail);
			});
			html += stoneDetails.join(', ') + '.';
			return html;
			// e.g. 0.35 carats of Blue Sapphire, 0.14 carats of Diamonds.'
		},
		
		getStoneGradeDescription = function(stone, grade, shape){	
			if(stone == 'Blue Sapphire'){				
				stone = 'Sapphire';
				if(shape == 'Emerald Cut'){	
					shape = 'Emerald+Cut';
				}
				var popupUrl = '/hprcv/qualitycompare/get/?stonetype=Blue+Sapphire&stoneshape=' + shape;				
			}else{
				var popupUrl = '/hprcv/qualitycompare/get/?stonetype='+encodeURI(stone)+'&stoneshape='+encodeURI(shape);
			}
			var stoneGrade = stone+grade;
			if(stoneGrade == 'SapphireA' || stoneGrade == 'SapphiresA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of sapphires in terms of quality. <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Dark blue and opaque</span><span class="text-blue">.</span>  This quality is comparable to that used by mall jewelers and chain stores.';		
			}		
			else if(stoneGrade == 'SapphireAA' || stoneGrade == 'SapphiresAA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of sapphires in terms of quality. <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Dark to medium blue and moderately included</span><span class="text-blue">.</span>  This quality is comparable to that used by leading independent/family jewelers.';		
			}
			else if(stoneGrade == 'SapphireAAA' || stoneGrade == 'SapphiresAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of sapphires in terms of quality. <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Medium to rich blue, slightly included and exhibits high brilliance</span><span class="text-blue">.</span>  This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'SapphireAAAA' || stoneGrade == 'SapphiresAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of sapphires in terms of quality.  Truly exceptional <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">deep rich blue, very slightly included and exhibits high brilliance</span><span class="text-blue">.</span>  This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'RubyA' || stoneGrade == 'RubiesA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of rubies in terms of quality. &nbsp;Dark pinkish red and opaque. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'RubyAA' || stoneGrade == 'RubiesAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of rubies in terms of quality. &nbsp;Medium pinkish red and moderately included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'RubyAAA' || stoneGrade == 'RubiesAAAA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of rubies in terms of quality. &nbsp;Medium red, slightly included and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'RubyAAAA' || stoneGrade == 'RubiesAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of rubies, in terms of quality. &nbsp;Truly exceptional deep rich red, very slightly included and exhibits high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'EmeraldA' || stoneGrade == 'EmeraldsA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of emeralds in terms of quality. &nbsp;Dark green and opaque. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'EmeraldAA' || stoneGrade == 'EmeraldsAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of emeralds in terms of quality. &nbsp;Medium green and heavily included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'EmeraldAAA' || stoneGrade == 'EmeraldsAAA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of emeralds in terms of quality. &nbsp;Rich medium green, moderately included and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'EmeraldAAAA' || stoneGrade == 'EmeraldsAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of emeralds in terms of quality. &nbsp;Truly exceptional rich green, moderately to slightly included and exhibits high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'TanzaniteA' || stoneGrade == 'TanzanitesA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of tanzanites in terms of quality. &nbsp;Light violet blue and slightly included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'TanzaniteAA' || stoneGrade == 'TanzanitesAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of tanzanites in terms of quality. &nbsp;Medium violet blue and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'TanzaniteAAA' || stoneGrade == 'TanzanitesAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of tanzanites in terms of quality. &nbsp;Rich violet blue, eye clean and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'TanzaniteAAAA' || stoneGrade == 'TanzanitesAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of tanzanites in terms of quality. &nbsp;Truly exceptional rich violet blue, eye clean and exhibits very high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'AquamarineA' || stoneGrade == 'AquamarinesA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of aquamarines in terms of quality. &nbsp;Very light sea blue and moderately included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'AquamarineAA' || stoneGrade == 'AquamarinesAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of aquamarines in terms of quality. &nbsp;Light sea blue and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'AquamarineAAA' || stoneGrade == 'AquamarinesAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of aquamarines in terms of quality. &nbsp;Medium sea blue, eye clean and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'AquamarineAAAA' || stoneGrade == 'AquamarinesAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of aquamarines in terms of quality. &nbsp;Truly exceptional medium sea blue, eye clean and exhibits very high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'DiamondJ I2' || stoneGrade == 'DiamondsJ I2'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">('+grade+')</span>: </strong> Top 50% of diamonds in terms of quality. &nbsp;J Color and I2 Clarity. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'DiamondI I1' || stoneGrade == 'DiamondsI I1'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">('+grade+')</span>: </strong> Top 25% of diamonds in terms of quality. &nbsp;I Color and I1 Clarity. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'DiamondH SI2' || stoneGrade == 'DiamondsH SI2'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">('+grade+')</span>: </strong> Top 10% of diamonds in terms of quality. &nbsp;H Color, SI2 Clarity and exhibits high brilliance. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'DiamondG-H VS' || stoneGrade == 'DiamondsG-H VS'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">('+grade+')</span>: </strong> Top 1% of diamonds in terms of quality. &nbsp;Truly exceptional G-H Color, VS Clarity and exhibits very high brilliance. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'Enhanced Black DiamondA' || stoneGrade == 'Enhanced Black DiamondsA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 50% of enhanced black diamond in terms of quality. &nbsp;Opaque in Clarity. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'Enhanced Black DiamondAA' || stoneGrade == 'Enhanced Black DiamondsAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 25% of enhanced black diamond in terms of quality. &nbsp;Opaque in Clarity. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'Enhanced Blue DiamondAA' || stoneGrade == 'Enhanced Blue DiamondsAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 25% of enhanced blue diamond in terms of quality. &nbsp;Greenish Blue in Color. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'Enhanced Blue DiamondAAA' || stoneGrade == 'Enhanced Blue DiamondsAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of enhanced blue diamond in terms of quality. &nbsp;Teal Blue in Color. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';					
			}
			else if(stoneGrade == 'MoissaniteClassic Moissanite' || stoneGrade == 'MoissanitesClassic Moissanite'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Classic Charles & Colvard Created Moissanite</span><sup>TM</sup>: </strong> Off white in color with high brilliance. Quality of color and sparkle in these moissanites are comparable to diamonds used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'MoissaniteForever Brilliant' || stoneGrade == 'MoissanitesForever Brilliant'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Charles & Colvard Created Forever Brilliant</span><sup class="fontsize-type1">&reg;</sup>: </strong> Truly exceptional in color, cut and clarity, exhibits high brilliance and fire. Quality of color and sparkle in these moissanites are comparable to diamonds found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'AmethystA' || stoneGrade == 'AmethystsA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of amethyst in terms of quality. &nbsp;Light purple and slightly Included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'AmethystAA' || stoneGrade == 'AmethystsAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of amethyst in terms of quality. &nbsp;Medium purple and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'AmethystAAA' || stoneGrade == 'AmethystsAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of amethyst in terms of quality. &nbsp;Medium dark purple and Eye clean. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'AmethystAAAA' || stoneGrade == 'AmethystsAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of amethyst in terms of quality. &nbsp;Dark purple and Eye clean. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'Blue TopazA' || stoneGrade == 'Blue TopazesA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of blue topaz in terms of quality. &nbsp;Light sky blue and slightly Included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'Blue TopazAA' || stoneGrade == 'Blue TopazesAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of blue topaz in terms of quality. &nbsp;Sky blue and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'Blue TopazAAA' || stoneGrade == 'Blue TopazesAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of blue topaz in terms of quality. &nbsp;Light swiss blue and Eye clean. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'Blue TopazAAAA' || stoneGrade == 'Blue TopazesAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of blue topaz in terms of quality. &nbsp;Swiss blue and Eye clean. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'CitrineA' || stoneGrade == 'CitrinesA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of citrine in terms of quality. &nbsp; Light yellow and slightly Included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'CitrineAA' || stoneGrade == 'CitrinesAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of citrine in terms of quality. &nbsp;Yellow and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'CitrineAAA' || stoneGrade == 'CitrinesAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of citrine in terms of quality. &nbsp;Golden and Eye clean. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'CitrineAAAA' || stoneGrade == 'CitrinesAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of citrine in terms of quality. &nbsp;Deep golden and Eye clean. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'GarnetA' || stoneGrade == 'GarnetsA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of garnet in terms of quality. &nbsp;Dark red and slightly Included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'GarnetAA' || stoneGrade == 'GarnetsAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of garnet in terms of quality. &nbsp;Dark to medium red and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'GarnetAAA' || stoneGrade == 'GarnetsAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of garnet in terms of quality. &nbsp;Medium red and Eye clean. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'GarnetAAAA' || stoneGrade == 'GarnetsAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of garnet in terms of quality. &nbsp;Rich Red and Eye clean. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'OpalA' || stoneGrade == 'OpalsA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of opal in terms of quality. &nbsp;Milky with no play of colour and Opaque & Surface Blemishes. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'OpalAA' || stoneGrade == 'OpalsAA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of opal in terms of quality. &nbsp;Milky with low play of colour and Opaque & Slight Surface Blemishes. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'OpalAAA' || stoneGrade == 'OpalsAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of opal in terms of quality. &nbsp;Milky with medium play of colour and Opaque & Very Slight Surface Blemishes. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'OpalAAAA' || stoneGrade == 'OpalsAAAA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of opal in terms of quality. &nbsp;Milky with high play of colour and Opaque & Surface Clean. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'MorganiteA' || stoneGrade == 'MorganitesA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of morganite in terms of quality. &nbsp;Very light peach and moderate included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'MorganiteAA' || stoneGrade == 'MorganitesAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of morganite in terms of quality. &nbsp;Light peach and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'MorganiteAAA' || stoneGrade == 'MorganitesAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of morganite in terms of quality. &nbsp;Peach and Eye clean. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'MorganiteAAAA' || stoneGrade == 'MorganitesAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of morganite in terms of quality. &nbsp;Deep peach and Eye clean. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'PeridotA' || stoneGrade == 'PeridotsA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of peridot in terms of quality. &nbsp;Light yellowish green and slightly included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'PeridotAA' || stoneGrade == 'PeridotsAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of peridot in terms of quality. &nbsp;Medium yellowish green and slightly included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'PeridotAAA' || stoneGrade == 'PeridotsAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of peridot in terms of quality. &nbsp;Yellowish green and Eye clean. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'PeridotAAAA' || stoneGrade == 'PeridotsAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of peridot in terms of quality. &nbsp;Green and Eye clean. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'Pink TourmalineA' || stoneGrade == 'Pink TourmalinesA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of pink tourmaline in terms of quality. &nbsp;Baby pink and heavily included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'Pink TourmalineAA' || stoneGrade == 'Pink TourmalinesAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of pink tourmaline in terms of quality. &nbsp;Light to medium pink and moderately included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'Pink TourmalineAAA' || stoneGrade == 'Pink TourmalinesAAA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of pink tourmaline in terms of quality. &nbsp;Medium pink and slightly included. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'Pink TourmalineAAAA' || stoneGrade == 'Pink TourmalinesAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of pink tourmaline in terms of quality. &nbsp;Rich pink and very slightly included to eye clean. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'Pink SapphireA' || stoneGrade == 'Pink SapphiresA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of pink sapphire in terms of quality. &nbsp;Baby Pink and included. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'Pink SapphireAA' || stoneGrade == 'Pink SapphiresAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of pink sapphire in terms of quality. &nbsp;Light to Medium Pink and moderately included. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'Pink SapphireAAA' || stoneGrade == 'Pink SapphiresAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of pink sapphire in terms of quality. &nbsp;Medium Pink and slightly included. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'Pink SapphireAAAA' || stoneGrade == 'Pink SapphiresAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of pink sapphire in terms of quality. &nbsp;Rich Pink and very slightly included to eye clean. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			/*else if(stoneGrade == 'Freshwater Cultured PearlA' || stoneGrade == 'Freshwater Cultured PearlsA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of freshwater cultured pearl in terms of quality. &nbsp;White and blemished surface. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'Freshwater Cultured PearlAA' || stoneGrade == 'Freshwater Cultured PearlsAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of freshwater cultured pearl in terms of quality. &nbsp;White and slighty blemished surface. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'South Sea Cultured PearlAAA' || stoneGrade == 'South Sea Cultured PearlsAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of white south sea cultured pearl in terms of quality. &nbsp;White and very slightly blemished surface. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'South Sea Cultured PearlAAAA' || stoneGrade == 'South Sea Cultured PearlsAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of white south sea cultured pearl in terms of quality. &nbsp;White and blemish free surface. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'Akoya Cultured PearlAAA' || stoneGrade == 'Akoya Cultured PearlsAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of akoya cultured pearl in terms of quality. &nbsp;White and very slightly blemished surface. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'Akoya Cultured PearlAAAA' || stoneGrade == 'Akoya Cultured PearlsAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of akoya cultured pearl in terms of quality. &nbsp;White and blemish free surface. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'Golden South Sea Cultured PearlA' || stoneGrade == 'Golden South Sea Cultured PearlsA'){			
					return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of golden south sea cultured pearl in terms of quality. &nbsp;Very light golden and blemished surface. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'Golden South Sea Cultured PearlAA' || stoneGrade == 'Golden South Sea Cultured PearlsAA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of golden south sea cultured pearl in terms of quality. &nbsp;Light golden and slighty blemished surface. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'Golden South Sea Cultured PearlAAA' || stoneGrade == 'Golden South Sea Cultured PearlsAAA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 75% of golden south sea cultured pearl in terms of quality. &nbsp;Golden and very slightly blemished surface. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'Golden South Sea Cultured PearlAAAA' || stoneGrade == 'Golden South Sea Cultured PearlsAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of golden south sea cultured pearl in terms of quality. &nbsp;Bright golden and blemish free surface. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'Tahitian Cultured PearlA' || stoneGrade == 'Tahitian Cultured PearlsA'){	
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of tahitian cultured pearl in terms of quality. &nbsp;Black and blemished surface. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'Tahitian Cultured PearlAA' || stoneGrade == 'Tahitian Cultured PearlsAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of tahitian cultured pearl in terms of quality. &nbsp;Black and slighty blemished surface. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'Tahitian Cultured PearlAAA' || stoneGrade == 'Tahitian Cultured PearlsAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of tahitian cultured pearl in terms of quality. &nbsp;Black to bronz and very slightly blemished surface. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'Tahitian Cultured PearlAAAA' || stoneGrade == 'Tahitian Cultured PearlsAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of tahitian cultured pearl in terms of quality. &nbsp;Super peacock and blemish free surface. &nbsp;This quality can be found only at the top boutiques in the world.';
			}
			else if(stoneGrade == 'Golden Japanese Cultured PearlA' || stoneGrade == 'Golden Japanese Cultured PearlsA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Good ('+grade+')</span>: </strong> Top 75% of golden japanese cultured pearl in terms of quality. &nbsp;Very light golden and blemished surface. &nbsp;This quality is comparable to that used by mall jewelers and chain stores.';
			}
			else if(stoneGrade == 'Golden Japanese Cultured PearlAA' || stoneGrade == 'Golden Japanese Cultured PearlsAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Better ('+grade+')</span>: </strong> Top 33% of golden japanese cultured pearl in terms of quality. &nbsp;Light golden and slighty blemished surface. &nbsp;This quality is comparable to that used by leading independent/family jewelers.';
			}
			else if(stoneGrade == 'Golden Japanese Cultured PearlAAA' || stoneGrade == 'Golden Japanese Cultured PearlsAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Best ('+grade+')</span>: </strong> Top 10% of golden japanese cultured pearl in terms of quality. &nbsp;Golden and very slightly blemished surface. &nbsp;This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
			}
			else if(stoneGrade == 'Golden Japanese Cultured PearlAAAA' || stoneGrade == 'Golden Japanese Cultured PearlsAAAA'){
				return '<strong>Quality Grade - <span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="'+ popupUrl +'" class="clickable hover-underline text-blue">Heirloom ('+grade+')</span>: </strong> Top 1% of golden japanese cultured pearl in terms of quality. &nbsp;Bright golden and blemish free surface. &nbsp;This quality can be found only at the top boutiques in the world.';
						
			}*/		
		},
		
		getMetalDescription = function(metal){
			switch (metal){
				case 'Silver':
					return '<strong>Silver:</strong> The same great look as white gold at a lower price.  Angara uses the highest standard available, .925 sterling silver.  Silver is lighter to wear than gold.';
					break;
				case '14K White Gold':
					return '<strong>14K White Gold:</strong> The standard for fine jewelry.  14K White gold has been the most popular choice for fine jewelry over the last twenty years as it blends well with diamonds.';
					break;
				case '14K Yellow Gold':
					return '<strong>14K Yellow Gold:</strong> Glowing and rich, yellow gold adds more color to the jewelry piece.  If buying as a gift, please note that women generally have a clear preference for Yellow or White Gold.';
					break;
				case '14K Rose Gold':
					return '<strong>14K Rose Gold:</strong> Glowing and rich, rose gold adds more color to the jewelry piece. If buying as a gift, please note that women generally have a clear preference for Rose or Yellow Gold.';
					break;
				case 'Platinum':
					return '<strong>Platinum:</strong> The most durable and premium metal for fine jewelry.  Platinum feels more substantial to wear due to its greater weight.';
					break;
				case '14K Rose and White Gold':
					return '<strong>14K Rose and White Gold:</strong> Glowing and rich, two-tone rose and white gold adds more color to the jewelry piece. If buying as a gift, please note that women generally have a clear preference for two-tone Rose or Yellow Gold.';
					break;
				case '14K Yellow and White Gold':
					return '<strong>14K Yellow and White Gold:</strong> Glowing and rich, two-tone yellow and white gold adds more color to the jewelry piece. If buying as a gift, please note that women generally have a clear preference for two-tone Yellow or Rose Gold.';
					break;
				case '18K White Gold':
					return '<strong>18K White Gold:</strong> The preferred metal for luxury jewelry. 18K White Gold provides that extra strength to hold your precious gemstones better, apart from blending well with diamonds and colored gemstones - a popular choice for high jewelry.';
					break;
				case '18K Yellow Gold':
					return '<strong>18K Yellow Gold:</strong> Lustrous, warm and enduring, 18K Yellow Gold adds more strength and color to fine jewelry. It has been a discerning choice for jewelry that you want to pass down to future generations.';
					break;
				case 'Tungsten Carbide':
					return '<strong>Tungsten Carbide:</strong> Strong, stylish and nearly indestructible - a great choice for wedding rings. Tungsten carbide gives a brilliant and shatter-proof look for carefree wearing.';
					break;
			}			
		},
		
		getStoneEnhancementDetail = function(stone){
			var stoneText = '';
			if((stone=='Diamonds' || stone=='Diamond') || (stone=='Peridots' || stone=='Peridot') || (stone=='Garnets' || stone=='Garnet') || (stone=='Opals' || stone=='Opal') || (stone=='Amethysts' || stone=='Amethyst') || (stone=='Citrines' || stone=='Citrine') || (stone == 'Rose Quartz' || stone == 'Rose Quartzes') || (stone=='South Sea Cultured Pearls' || stone=='South Sea Cultured Pearl') || (stone=='Golden South Sea Cultured Pearl' || stone=='Golden South Sea Cultured Pearls') || (stone=='Tahitian Cultured Pearl' || stone=='Tahitian Cultured Pearls') || (stone=='Tsavorite' || stone=='Tsavorites')){
				stoneText = 'None';
			}
			else if(stone == 'Turquoise' || stone == 'Turquoises'){
				stoneText = 'Stabilized';
			}
			else if(stone == 'Moissanite' || stone == 'Moissanites'){
				stoneText = 'Lab Created';
			}
			else if((stone=='Enhanced Black Diamonds' || stone=='Enhanced Black Diamond') || (stone=='Green Amethysts' || stone=='Green Amethyst')){
				stoneText = 'Irradiated';
			}
			else if((stone=='Enhanced Blue Diamonds' || stone=='Enhanced Blue Diamond') || (stone=='Blue Topazes' || stone=='Blue Topaz') || (stone == 'Morganite' || stone == 'Morganites')){
				stoneText = 'Heated and Irradiated';
			}
			else if((stone=='Rubies' || stone=='Ruby') || (stone=='Blue Sapphires' || stone=='Blue Sapphire') || (stone=='Pink Sapphires' || stone=='Pink Sapphire') || (stone=='Tanzanites' || stone=='Tanzanite') || (stone=='Aquamarines' || stone=='Aquamarine') || (stone=='Pink Tourmalines' || stone=='Pink Tourmaline') || (stone=='Carnelians' || stone=='Carnelian') || (stone == 'White Sapphire' || stone == 'White Sapphires') || (stone == 'Yellow Sapphire' || stone == 'Yellow Sapphires')){
				stoneText = 'Heated';
			}
			else if(stone=='Emeralds' || stone=='Emerald'){
				stoneText = 'Oiling';
			}
			else if((stone=='Black Onyxs' || stone=='Black Onyx') || (stone=='Golden Japanese Cultured Pearl' || stone=='Golden Japanese Cultured Pearls')){
				stoneText = 'Dyed';
			}
			else if((stone=='Akoya Cultured Pearls' || stone=='Akoya Cultured Pearl') || (stone=='Freshwater Cultured Pearls' || stone=='Freshwater Cultured Pearl')){
				stoneText = 'Bleached';
			}
			return stoneText;
		},
		
		getEnhancementPopupHtml = function(stone){
			var stoneHtml = '';
			if(stone=='Diamonds' || stone=='Diamond'){
				stoneHtml = "Angara supports and deals in conflict free diamonds. Conflict-free diamonds are guaranteed not to be obtained through the use of violence, human rights abuses, child labor, or environmental destruction. Angara completely supports the Kimberley Process, which is an International process to track and certify diamonds.";
			}
			else if(stone=='Enhanced Black Diamonds' || stone=='Enhanced Black Diamond'){
				stoneHtml = "Irradiation is an age-old process where gemstones are irradiated to enhance their optical properties. Irradiation may be followed by a heating/annealing process.";
			}
			else if(stone=='Enhanced Blue Diamonds' || stone=='Enhanced Blue Diamond'){
				stoneHtml = "Irradiation is an age-old process where gemstones are irradiated to enhance their optical properties. Irradiation may be followed by a heating/annealing process. HPHT process involves use of high heat and high pressure to affect desired alterations of color.";
			}
			else if(stone=='Rubies' || stone=='Ruby'){
				stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all rubies in order to maximize brilliance and purity.";
			}
			else if(stone=='Blue Sapphires' || stone=='Blue Sapphire'){
				stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all blue sapphires in order to maximize brilliance and purity.";
			}
			else if(stone=='Pink Sapphires' || stone=='Pink Sapphire'){
				stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all pink sapphires in order to maximize brilliance and purity.";
			}
			else if(stone == 'White Sapphire' || stone == 'White Sapphires'){
				stoneHtml = "Heat enhancement is a commonly accepted age-old high temperature treatment that is performed on all white sapphires in order to maximize brilliance and purity.";
			}
			else if(stone == 'Yellow Sapphire' || stone == 'Yellow Sapphires'){
				stoneHtml = "Heat enhancement is a commonly accepted age-old high temperature treatment that is performed on all yellow sapphires in order to maximize brilliance and purity.";
			}
			else if(stone=='Emeralds' || stone=='Emerald'){
				stoneHtml = "The filling of surface-breaking fissures with a colorless oil, wax or other colorless substance except glass or plastic, to improve the gemstone's clarity is an age-old process adopted for virtually all emeralds.";
			}
			else if(stone=='Tanzanites' || stone=='Tanzanite'){
				stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all tanzanites in order to maximize brilliance and purity.";
			}
			else if(stone=='Aquamarines' || stone=='Aquamarine'){
				stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all aquamarines in order to maximize brilliance and purity.";
			}
			else if(stone=='Amethysts' || stone=='Amethyst'){
				stoneHtml = "Amethysts are not enhanced in any way.";
			}
			else if(stone=='Green Amethysts' || stone=='Green Amethyst'){
				stoneHtml = "Irradiation is an age-old process in which green amethysts are treated to improve their optical properties.";
			}
			else if(stone=='Citrines' || stone=='Citrine'){
				stoneHtml = "Citrines are not enhanced in any way.";
			}
			else if(stone == 'Garnet' || stone == 'Garnets'){
				stoneHtml = "Garnets are not enhanced in any way.";
			}
			else if(stone == 'Opal' || stone == 'Opals'){
				stoneHtml = "Opals are not enhanced in any way.";
			}
			else if(stone == 'Peridot' || stone == 'Peridots'){
				stoneHtml = "Peridots are not enhanced in any way.";
			}
			else if(stone == 'Rose Quartz' || stone == 'Rose Quartzes'){
				stoneHtml = "Rose quartzes are not enhanced in any way.";
			}
			else if(stone == 'Moissanite' || stone == 'Moissanites'){
				stoneHtml = "Moissanites are created in controlled environments using advanced technologies.";
			}
			else if(stone == 'Turquoise' || stone == 'Turquoises'){
				stoneHtml = "Stabilization is a process in which turquoises are treated for enhanced color, hardness and durability. Almost all turquoises used in jewelry are stabilized.";
			}
			else if(stone == 'Morganite' || stone == 'Morganites'){
				stoneHtml = "Irradiation is an age-old process in which morganites are treated to enhance their optical properties. Irradiation is followed by a heating/annealing process.";
			}
			else if(stone == 'Tsavorite' || stone == 'Tsavorites'){
				stoneHtml = "Tsavorites are not enhanced in any way.";
			}
			else if(stone=='Pink Tourmalines' || stone=='Pink Tourmaline'){
				stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all pink tourmalines in order to maximize brilliance and purity.";
			}
			else if(stone=='Black Onyxs' || stone=='Black Onyx'){
				stoneHtml = "The introduction of coloring matter into a gemstone to give it new color, intensify existing color or improve color uniformity.";
			}
			else if(stone=='Carnelians' || stone=='Carnelian'){
				stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all carnelians in order to maximize brilliance and purity.";
			}
			else if(stone=='Akoya Cultured Pearls' || stone=='Akoya Cultured Pearl'){
				stoneHtml = "Bleaching is a commonly accepted method used to lighten and even out the color of Akoya cultured pearls.";	
			}
			else if(stone == 'South Sea Cultured Pearl' || stone == 'South Sea Cultured Pearls'){
				stoneHtml = "South Sea cultured pearls are not enhanced in any way.";
			}
			else if((stone=='Freshwater Cultured Pearls' || stone=='Freshwater Cultured Pearl') || (stone=='Golden Japanese Cultured Pearl' || stone=='Golden Japanese Cultured Pearls') || (stone=='Golden South Sea Cultured Pearl' || stone=='Golden South Sea Cultured Pearls') || (stone=='Tahitian Cultured Pearl' || stone=='Tahitian Cultured Pearls')){
				stoneHtml = "Cultured pearl is a natural pearl grown under controlled conditions. This is a process where a seed pearl is inserted into the mantle of an oyster and kept in the sea bed for some years for complete formation.";
			}
			else if(stone=='Blue Topazes' || stone=='Blue Topaz'){
				stoneHtml = "Irradiation is an age-old process where gemstones are irradiated to enhance their optical properties. Irradiation may be followed by a heating/annealing process.";
			}
			return stoneHtml;			
		};
	
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
	
	//	S:VA	Stackable Products
	/*module.getMetalVariations = function(){
		var metalVariations = new Array();
		$.each(spConfig.settings,function(iCounter, setting){
			if(typeof(setting.attributeId) != 'undefined'){
				if(setting.config.code!='metal1_type'){
					if(setting.attributeId!='0' && setting.attributeId!='' && typeof(setting.attributeId) != 'undefined'){
						metalVariations[setting.attributeId]	=	setting.options[setting.selectedIndex].config.id;		
					}
				}
			}
		})
		return metalVariations;
	};
	
	module.getMatchingProducts = function(){
		var matchingOptions = module.getMetalVariations();
		var allowedProducts = new Array();
		
		$.each(spConfig.settings,function(iCounter, setting){
			if(typeof(matchingOptions[setting.attributeId]) != 'undefined'){
				$.each(setting.options,function(jCounter, option){
					if(option.config.id == matchingOptions[setting.attributeId]){
						var selected = setting.options[jCounter];
						if (allowedProducts.length==0){
							allowedProducts = selected.config.allowedProducts;
						} else {
							allowedProducts = allowedProducts.intersect(selected.config.allowedProducts).uniq();
						}
					}
				})
			}
		})
		return allowedProducts;
	};
	
	module.getStackablesProductsHtml	=	function(){
		var stackablesProductIds	=	module.getMatchingProducts();
		
		$.each(stackablesProductIds, function(iCounter, productId ){
			if(productId){
				if(spConfig.config.childProducts[productId].price){
					var imageUrl = spConfig.config.productImages[productId][0].thumbUrl;
					$('#stackables-pro').append('<li class="col-md-12 col-sm-12 col-xs-12"><div class="col-md-2 col-sm-2 col-xs-2 stackable-pro-img" style="border-right:1px solid #f1f1f1"><img src="'+imageUrl+'" class="img-responsive" /></div><div class="col-md-8 col-sm-8 col-xs-8 stackable-pro-detail" style="padding:0px 8px"><div class="col-md-12 metal"><div class="col-md-12"><strong>Metal Type:</strong> <span>'+spConfig.config.childProducts[productId].metals[0].type+'</span></div></div><div class="col-md-12 price-qty"><div class="col-md-8 price"><strong>Price:</strong> <span>$'+spConfig.config.childProducts[productId].price+'</span></div><div class="col-md-4 qty"><strong>Qty:</strong><span class="stackable-qty-box"><input type="text" class="qtybox" name="quantity" value="1" /><i class="fa fa-angle-up incr"></i><i class="fa fa-angle-down decr"></i></span></div></div></div><div class="col-md-2 col-sm-2 col-xs-2 stackable-pro-checkbox" style=""><span class="stackable-products" id="sp_'+productId+'"></span></div></li>');
				}				
			}
		});
	};	*/
	//	E:VA
	
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
			for(var iCounter=0; iCounter < settings.childProducts[productId].stones.length;  iCounter++){	// looping all stones
				if($('.stone-detail-box-container:eq('+iCounter+')').length == 0){
					var stoneColor = settings.childProducts[productId].stones[iCounter].color;
					var stoneClarity = settings.childProducts[productId].stones[iCounter].clarity;
					
					if((stoneColor && stoneColor != null) || (stoneClarity && stoneClarity != null)){
						var showQualityGrade = false;
					}
					else{
						var showQualityGrade = true;
					}
					module.addStoneDetails(iCounter+1, settings.childProducts[productId].stones[iCounter], settings.childProducts[productId].categoryIds, settings.childProducts[productId].jewelryStyles, showQualityGrade);
				}
				$.each(settings.childProducts[productId].stones[iCounter],function(label,value){ 	// looping all details of stone
					if(label == 'grade' && $('#add-cart-stopper').length > 0){
						value = 'Not selected yet';
					}
					$('.dyn_stone'+(iCounter+1)+'_'+label).text(value);		// replacing stone details
				});
			}
			for(var iCounter=0; iCounter < settings.childProducts[productId].metals.length;  iCounter++){	// looping all metals
				$.each(settings.childProducts[productId].metals[iCounter],function(label,value){ 	// looping all details of metal
					$('.dyn_metal'+(iCounter+1)+'_'+label).text(value);		// replacing stone details
				});
			}
			
			$('.dyn_msrp').text(optionsPrice.formatPrice(settings.childProducts[productId].msrp));
			$('.price-box').fadeTo(1,.9).fadeTo(1,1);	// iPad text rendering hack
			$('.dyn_band_width').text(settings.childProducts[productId].band_width);
			$('.dyn_name').text(settings.childProducts[productId].prod_name);
			$('.dyn_band_height').text(settings.childProducts[productId].band_height);
			
			$('.dyn_clasp_type').text(settings.childProducts[productId].clasp_type);
			$('.dyn_length').text(settings.childProducts[productId].length);
			$('.dyn_width').text(settings.childProducts[productId].width);
			$('.dyn_butterfly_type').text(settings.childProducts[productId].butterfly_type);
			
			if(settings.childProducts[productId].approximate_metal_weight == 'Select Ring Size' || settings.childProducts[productId].approximate_metal_weight == 'SELECT RING SIZE' || settings.childProducts[productId].approximate_metal_weight == 'select ring size'){
				$('.dyn_approximate_metal_weight').text(settings.childProducts[productId].approximate_metal_weight);
			}
			else{
				$('.dyn_approximate_metal_weight').text(settings.childProducts[productId].approximate_metal_weight+' grams');
			}
			$('.dyn_short_description').text(settings.childProducts[productId].shortDescription);
			$('.dyn_long_description').html(settings.childProducts[productId].description);
			updateEstimateShipping();
			//specialOfferPrice();
			// # @todo implement retail price change logic
			//$('.dyn_msrp').text(optionsPrice.priceFormat.pattern + (settings.childProducts[productId].msrp).toFixed(2));
			
			// # @todo implement shipping date change logic
		}
	};
	
	module.addStoneDetails = function(stoneIndex, stone, categoryIds, jewelryStyles){
		var oddOrEven = (stoneIndex%2==1)?'-odd':'';
		$('.stones-details-box').append('<div class="col-sm-12 max-margin-bottom stone-detail-box-container stone-detail-box'+oddOrEven+'">'+
			'<div><div class="detail-box-title">'+
				'<span class="dyn_stone'+stoneIndex+'_type"></span> Information:'+
			'</div>'+((spConfig.config.isBuildYourOwn) ? '' : (((categoryIds.indexOf('347') > -1) || (jewelryStyles.indexOf('Eternity') > -1)) ? '' : ((stone['count'] > 0) ? '<div class="field"><div class="fieldtitle text-light pull-left text-left">'+'Number of <span class="dyn_stone'+stoneIndex+'_shape"></span> <span class="dyn_stone'+stoneIndex+'_name"></span>:</div><div class="fieldvalue text-right dyn_stone'+stoneIndex+'_count"></div><div style="clear:both"></div></div>' : '')))+
			(((stone['grade'] != 'Lab Created' && stone['grade'] != 'Simulated' && categoryIds.indexOf('457') == -1) || (categoryIds.indexOf('457') > -1 && (stone['type'] == 'Diamond' || stone['type'] == 'Diamonds')))?(((stone['name'] == 'Diamond' || stone['name'] == 'Diamonds') || (stone['name'] == 'Peridot' || stone['name'] == 'Peridots') || (stone['name'] == 'Garnet' || stone['name'] == 'Garnets') || (stone['name'] == 'Opal' || stone['name'] == 'Opals') || (stone['name'] == 'Enhanced Black Diamond' || stone['name'] == 'Enhanced Black Diamonds') || (stone['name'] == 'Enhanced Blue Diamond' || stone['name'] == 'Enhanced Blue Diamonds') || (stone['name'] == 'Ruby' || stone['name'] == 'Rubies') || (stone['name'] == 'Blue Sapphire' || stone['name'] == 'Blue Sapphires') || (stone['name'] == 'Pink Sapphire' || stone['name'] == 'Pink Sapphires') || (stone['name'] == 'White Sapphire' || stone['name'] == 'White Sapphires') || (stone['name'] == 'Yellow Sapphire' || stone['name'] == 'Yellow Sapphires') || (stone['name'] == 'Turquoise' || stone['name'] == 'Turquoises') || (stone['name'] == 'Morganite' || stone['name'] == 'Morganites') || (stone['name'] == 'Moissanite' || stone['name'] == 'Moissanites') || (stone['name'] == 'Rose Quartz' || stone['name'] == 'Rose Quartzes') || (stone['name'] == 'Tsavorite' || stone['name'] == 'Tsavorites') || (stone['name'] == 'Tanzanite' || stone['name'] == 'Tanzanites') || (stone['name'] == 'Aquamarine' || stone['name'] == 'Aquamarines') || (stone['name'] == 'Amethyst' || stone['name'] == 'Amethysts') || (stone['name'] == 'Green Amethyst' || stone['name'] == 'Green Amethysts') || (stone['name'] == 'Citrine' || stone['name'] == 'Citrines') || (stone['name'] == 'Pink Tourmaline' || stone['name'] == 'Pink Tourmalines') || (stone['name'] == 'Carnelian' || stone['name'] == 'Carnelians') || (stone['name'] == 'Emerald' || stone['name'] == 'Emeralds') || (stone['name'] == 'Black Onyx' || stone['name'] == 'Black Onyxs') || (stone['name'] == 'Akoya Cultured Pearl' || stone['name'] == 'Akoya Cultured Pearls') || (stone['name'] == 'Freshwater Cultured Pearl' || stone['name'] == 'Freshwater Cultured Pearls') || (stone['name'] == 'South Sea Cultured Pearl' || stone['name'] == 'South Sea Cultured Pearls') || (stone['name'] == 'Golden Japanese Cultured Pearl' || stone['name'] == 'Golden Japanese Cultured Pearls') || (stone['name'] == 'Golden South Sea Cultured Pearl' || stone['name'] == 'Golden South Sea Cultured Pearls') || (stone['name'] == 'Tahitian Cultured Pearl' || stone['name'] == 'Tahitian Cultured Pearls') || (stone['name']=='Blue Topaz' || stone['name'] == 'Blue Topazes'))?'<div class="field">'+
				'<div class="fieldtitle text-light pull-left text-left">Enhancement: '+(((stone['name'] == 'Diamond' || stone['name'] == 'Diamonds') || (stone['name'] == 'Enhanced Black Diamond' || stone['name'] == 'Enhanced Black Diamonds') || (stone['name'] == 'Enhanced Blue Diamond' || stone['name'] == 'Enhanced Blue Diamonds') || (stone['name'] == 'Ruby' || stone['name'] == 'Rubies') || (stone['name'] == 'Blue Sapphire' || stone['name'] == 'Blue Sapphires') || (stone['name'] == 'Pink Sapphire' || stone['name'] == 'Pink Sapphires')  || (stone['name'] == 'Emerald' || stone['name'] == 'Emeralds') || (stone['name'] == 'Tanzanite' || stone['name'] == 'Tanzanites') || (stone['name'] == 'Aquamarine' || stone['name'] == 'Aquamarines') || (stone['name'] == 'Amethyst' || stone['name'] == 'Amethysts') || (stone['name'] == 'Green Amethyst' || stone['name'] == 'Green Amethysts') || (stone['name'] == 'Citrine' || stone['name'] == 'Citrines') || (stone['name'] == 'Pink Tourmaline' || stone['name'] == 'Pink Tourmalines') || (stone['name'] == 'Black Onyx' || stone['name'] == 'Black Onyxs') || (stone['name'] == 'Carnelian' || stone['name'] == 'Carnelians') || (stone['name'] == 'Akoya Cultured Pearl' || stone['name'] == 'Akoya Cultured Pearls') || (stone['name'] == 'Freshwater Cultured Pearl' || stone['name'] == 'Freshwater Cultured Pearls') || (stone['name'] == 'South Sea Cultured Pearl' || stone['name'] == 'South Sea Cultured Pearls') || (stone['name'] == 'Golden Japanese Cultured Pearl' || stone['name'] == 'Golden Japanese Cultured Pearls') || (stone['name'] == 'Golden South Sea Cultured Pearl' || stone['name'] == 'Golden South Sea Cultured Pearls') || (stone['name'] == 'Tahitian Cultured Pearl' || stone['name'] == 'Tahitian Cultured Pearls') || (stone['name']=='Blue Topaz' || stone['name'] == 'Blue Topazes') || (stone['name'] == 'Garnet' || stone['name'] == 'Garnets') || (stone['name'] == 'Moissanite' || stone['name'] == 'Moissanites') || (stone['name'] == 'Morganite' || stone['name'] == 'Morganites') || (stone['name'] == 'Opal' || stone['name'] == 'Opals') || (stone['name'] == 'Peridot' || stone['name'] == 'Peridots') || (stone['name'] == 'Rose Quartz' || stone['name'] == 'Rose Quartzes') || (stone['name'] == 'Turquoise' || stone['name'] == 'Turquoises') || (stone['name'] == 'Tsavorite' || stone['name'] == 'Tsavorites') || (stone['name'] == 'White Sapphire' || stone['name'] == 'White Sapphires') || (stone['name'] == 'Yellow Sapphire' || stone['name'] == 'Yellow Sapphires'))?'<span class="hidden-xs pull-right popup-icon gmprd-popup low-padding-left"><i id="enhancement_aa" class="fa fa-info-circle fa-fw text-lighter fontsize-type4 clickable" data-html="true" data-placement="bottom" data-trigger="click" data-toggle="tooltip" title="'+getEnhancementPopupHtml(stone['name'])+'"></i></span>':'')+'</div>'+
				'<div class="fieldvalue text-right">'+getStoneEnhancementDetail(stone['name'])+'</div>'+
				'<div style="clear:both"></div>'+
			'</div>':''):'')+
			((stone['name'] != 'Diamond' && stone['name'] != 'Diamonds') ? '<div class="field">'+
				'<div class="fieldtitle text-light pull-left text-left">Approximate Dimensions:'+(((((stone['name'] == 'Akoya Cultured Pearl' || stone['name'] == 'Akoya Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Freshwater Cultured Pearl' || stone['name'] == 'Freshwater Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'South Sea Cultured Pearl' || stone['name'] == 'South Sea Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Golden Japanese Cultured Pearl' || stone['name'] == 'Golden Japanese Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Golden South Sea Cultured Pearl' || stone['name'] == 'Golden South Sea Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Tahitian Cultured Pearl' || stone['name'] == 'Tahitian Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Amethyst' || stone['name'] == 'Amethysts') && stone["shape"] != 'Marquise' && stone["shape"] != 'Drop') || (stone['name'] == 'Blue Topaz' || stone['name'] == 'Blue Topazes') || ((stone['name'] == 'Citrine' || stone['name'] == 'Citrines') && stone["shape"] != 'Rectangle') || (stone['name'] == 'Garnet' || stone['name'] == 'Garnets') || (stone['name'] == 'Opal' || stone['name'] == 'Opals') || (stone['name'] == 'Peridot' || stone['name'] == 'Peridots') || ((stone['name'] == 'Pink Tourmaline' || stone['name'] == 'Pink Tourmalines') && stone["shape"] != 'Trillion') || (stone['name'] == 'Ruby' || stone['name'] == 'Rubies') || (stone['name'] == 'Blue Sapphire' || stone['name'] == 'Blue Sapphires') || ((stone['name'] == 'Pink Sapphire' || stone['name'] == 'Pink Sapphires') && stone["shape"] != 'Marquise') || (stone['name'] == 'Emerald' || stone['name'] == 'Emeralds') || (stone['name'] == 'Tanzanite' || stone['name'] == 'Tanzanites')/* || ((stone['name'] == 'Diamond' || stone['name'] == 'Diamonds') && stone["shape"] != 'Marquise' && stone["shape"] != 'Emerald Cut')*/ || ((stone['name'] == 'Enhanced Black Diamond' || stone['name'] == 'Enhanced Black Diamonds') && stone["shape"] != 'Emerald Cut') || ((stone['name'] == 'Enhanced Blue Diamond' || stone['name'] == 'Enhanced Blue Diamonds') && stone["shape"] != 'Emerald Cut') || ((stone['name'] == 'Moissanite' || stone['name'] == 'Moissanites') && (stone["shape"] == 'Round' || stone["shape"] == 'Princess' || stone["shape"] == 'Oval')) || ((stone['name'] == 'Black Onyx' || stone['name'] == 'Black Onyxs') && stone['shape'] != 'Ball' && stone['shape'] != 'Emerald Cut' && stone['shape'] != 'Marquise' && stone['shape'] != 'Rectangle') || ((stone['name'] == 'Morganite' || stone['name'] == 'Morganites') && (stone["shape"] == 'Cushion' || stone["shape"] == 'Emerald Cut' || stone["shape"] == 'Oval' || stone["shape"] == 'Pear' || stone["shape"] == 'Round' || stone["shape"] == 'Trillion')) || (stone['name'] == 'Aquamarine' || stone['name'] == 'Aquamarines')) && (stone["shape"] != 'Trapezoid' && stone["shape"] != 'Half Moon' && stone["shape"] != 'Baguette'))?'<span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="/hprcv/qualitycompare/getweightchart/?stonetype='+encodeURI(stone["name"])+'&stoneshape='+encodeURI(stone["shape"])+'" class="hidden-xs pull-right popup-icon gmprd-popup clickable"><i class="fa fa-question-circle low-padding-left fa-fw text-lighter fontsize-type4 clickable"></i></span>':'')+'</div>'+
				'<div class="fieldvalue text-right dyn_stone'+stoneIndex+'_size"></div>'+
				'<div style="clear:both"></div>'+
			'</div>' : '')+
			'<div class="field">'+
				'<div class="fieldtitle text-light pull-left text-left">Approximate Carat Total Weight:'+(((((stone['name'] == 'Akoya Cultured Pearl' || stone['name'] == 'Akoya Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Freshwater Cultured Pearl' || stone['name'] == 'Freshwater Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'South Sea Cultured Pearl' || stone['name'] == 'South Sea Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Golden Japanese Cultured Pearl' || stone['name'] == 'Golden Japanese Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Golden South Sea Cultured Pearl' || stone['name'] == 'Golden South Sea Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Tahitian Cultured Pearl' || stone['name'] == 'Tahitian Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Amethyst' || stone['name'] == 'Amethysts') && stone["shape"] != 'Marquise' && stone["shape"] != 'Drop') || (stone['name'] == 'Blue Topaz' || stone['name'] == 'Blue Topazes') || ((stone['name'] == 'Citrine' || stone['name'] == 'Citrines') && stone["shape"] != 'Rectangle') || (stone['name'] == 'Garnet' || stone['name'] == 'Garnets') || (stone['name'] == 'Opal' || stone['name'] == 'Opals') || ((stone['name'] == 'Peridot' || stone['name'] == 'Peridots') && stone["shape"] != 'Heart' && stone["shape"] != 'Square') || ((stone['name'] == 'Pink Tourmaline' || stone['name'] == 'Pink Tourmalines') && stone["shape"] != 'Trillion') || (stone['name'] == 'Ruby' || stone['name'] == 'Rubies') || (stone['name'] == 'Blue Sapphire' || stone['name'] == 'Blue Sapphires') || ((stone['name'] == 'Pink Sapphire' || stone['name'] == 'Pink Sapphires') && stone["shape"] != 'Marquise') || (stone['name'] == 'Emerald' || stone['name'] == 'Emeralds') || (stone['name'] == 'Tanzanite' || stone['name'] == 'Tanzanites')/* || ((stone['name'] == 'Diamond' || stone['name'] == 'Diamonds') && stone["shape"] != 'Marquise' && stone["shape"] != 'Emerald Cut')*/ || ((stone['name'] == 'Enhanced Black Diamond' || stone['name'] == 'Enhanced Black Diamonds') && stone["shape"] != 'Emerald Cut') || ((stone['name'] == 'Enhanced Blue Diamond' || stone['name'] == 'Enhanced Blue Diamonds') && stone["shape"] != 'Emerald Cut') || ((stone['name'] == 'Moissanite' || stone['name'] == 'Moissanites') && (stone["shape"] == 'Round' || stone["shape"] == 'Princess' || stone["shape"] == 'Oval')) || ((stone['name'] == 'Black Onyx' || stone['name'] == 'Black Onyxs') && stone['shape'] != 'Ball' && stone['shape'] != 'Emerald Cut' && stone['shape'] != 'Marquise' && stone['shape'] != 'Rectangle') || ((stone['name'] == 'Morganite' || stone['name'] == 'Morganites') && (stone["shape"] == 'Cushion' || stone["shape"] == 'Emerald Cut' || stone["shape"] == 'Oval' || stone["shape"] == 'Pear' || stone["shape"] == 'Round' || stone["shape"] == 'Trillion')) || (stone['name'] == 'Aquamarine' || stone['name'] == 'Aquamarines')) && (stone["shape"] != 'Trapezoid' && stone["shape"] != 'Half Moon' && stone["shape"] != 'Baguette'))?'<span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="/hprcv/qualitycompare/getweightchart/?stonetype='+encodeURI(stone["name"])+'&stoneshape='+encodeURI(stone["shape"])+'" class="hidden-xs pull-right popup-icon gmprd-popup clickable"><i class="fa fa-question-circle low-padding-left fa-fw text-lighter fontsize-type4 clickable"></i></span>':'')+'</div>'+
				'<div class="fieldvalue text-right dyn_stone'+stoneIndex+'_weight"></div>'+
				'<div style="clear:both"></div>'+
			'</div>'+
			((showQualityGrade)?'<div class="field">'+
				'<div class="fieldtitle text-light pull-left text-left">Quality Grade:'+(((((stone['name'] == 'Akoya Cultured Pearl' || stone['name'] == 'Akoya Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Freshwater Cultured Pearl' || stone['name'] == 'Freshwater Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'South Sea Cultured Pearl' || stone['name'] == 'South Sea Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Golden Japanese Cultured Pearl' || stone['name'] == 'Golden Japanese Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Golden South Sea Cultured Pearl' || stone['name'] == 'Golden South Sea Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Tahitian Cultured Pearl' || stone['name'] == 'Tahitian Cultured Pearls') && stone["shape"] == 'Round') || ((stone['name'] == 'Amethyst' || stone['name'] == 'Amethysts') && stone["shape"] != 'Marquise' && stone["shape"] != 'Drop' && stone["shape"] != 'Heart') || (stone['name'] == 'Blue Topaz' || stone['name'] == 'Blue Topazes') || ((stone['name'] == 'Citrine' || stone['name'] == 'Citrines') && stone["shape"] != 'Rectangle' && stone["shape"] != 'Square') || ((stone['name'] == 'Garnet' || stone['name'] == 'Garnets') && stone["shape"] != 'Heart') || ((stone['name'] == 'Opal' || stone['name'] == 'Opals') && (stone["shape"] == 'Oval' || stone["shape"] == 'Pear' || stone["shape"] == 'Round' || stone["shape"] == 'Cushion')) || ((stone['name'] == 'Peridot' || stone['name'] == 'Peridots') && stone["shape"] != 'Heart' && stone["shape"] != 'Square') || ((stone['name'] == 'Pink Tourmaline' || stone['name'] == 'Pink Tourmalines') && stone["shape"] != 'Trillion') || (stone['name'] == 'Ruby' || stone['name'] == 'Rubies') || (stone['name'] == 'Blue Sapphire' || stone['name'] == 'Blue Sapphires') || ((stone['name'] == 'Pink Sapphire' || stone['name'] == 'Pink Sapphires') && stone["shape"] != 'Marquise') || (stone['name'] == 'Emerald' || stone['name'] == 'Emeralds') || (stone['name'] == 'Tanzanite' || stone['name'] == 'Tanzanites') || ((stone['name'] == 'Diamond' || stone['name'] == 'Diamonds') && stone["shape"] != 'Marquise' && stone["shape"] != 'Emerald Cut') || ((stone['name'] == 'Enhanced Black Diamond' || stone['name'] == 'Enhanced Black Diamonds') && stone["shape"] != 'Emerald Cut') || ((stone['name'] == 'Enhanced Blue Diamond' || stone['name'] == 'Enhanced Blue Diamonds') && stone["shape"] != 'Emerald Cut') || ((stone['name'] == 'Moissanite' || stone['name'] == 'Moissanites') && (stone["shape"] == 'Round' || stone["shape"] == 'Princess' || stone["shape"] == 'Oval')) || ((stone['name'] == 'Morganite' || stone['name'] == 'Morganites') && (stone["shape"] == 'Cushion' || stone["shape"] == 'Emerald Cut' || stone["shape"] == 'Oval' || stone["shape"] == 'Pear' || stone["shape"] == 'Round' || stone["shape"] == 'Trillion')) || ((stone['name'] == 'Aquamarine' || stone['name'] == 'Aquamarines')/* && stone["shape"] != 'Trillion' && stone["shape"] != 'Square Emerald Cut'*/)) && (stone["shape"] != 'Trapezoid' && stone["shape"] != 'Half Moon' && stone["shape"] != 'Baguette'))?'<span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="/hprcv/qualitycompare/get/?stonetype='+encodeURI(stone["name"])+'&stoneshape='+encodeURI(stone["shape"])+'" class="hidden-xs pull-right popup-icon gmprd-popup clickable"><i class="fa fa-question-circle low-padding-left fa-fw text-lighter fontsize-type4 clickable"></i></span>':'')+'</div>'+
				'<div class="fieldvalue text-right dyn_stone'+stoneIndex+'_grade"></div>'+
				'<div style="clear:both"></div>'+
			'</div>':'')+((showQualityGrade == false && 'dyn_stone'+stoneIndex+'_color' && (stone['color'] && stone['color'] != null))?
			'<div class="field">'+
				'<div class="fieldtitle text-light pull-left text-left">Color:</div>'+
				'<div class="fieldvalue text-right dyn_stone'+stoneIndex+'_color"></div>'+
				'<div style="clear:both"></div>'+
			'</div>':'')+((showQualityGrade == false && 'dyn_stone'+stoneIndex+'_clarity' && (stone['clarity'] && stone['clarity'] != null))?
			'<div class="field">'+
				'<div class="fieldtitle text-light pull-left text-left">Clarity:</div>'+
				'<div class="fieldvalue text-right dyn_stone'+stoneIndex+'_clarity"></div>'+
				'<div style="clear:both"></div>'+
			'</div>':'')+(('dyn_stone'+stoneIndex+'_setting')?
			'<div class="field">'+
				'<div class="fieldtitle text-light pull-left text-left">Setting Type:</div>'+
				'<div class="fieldvalue text-right dyn_stone'+stoneIndex+'_setting"></div>'+
				'<div style="clear:both"></div>'+
			'</div>':'')+'</div></div>')
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