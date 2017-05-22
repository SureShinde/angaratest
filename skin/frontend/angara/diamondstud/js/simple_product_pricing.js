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
	var separatorIndex = window.location.href.indexOf('?');
	if (separatorIndex != -1) {
		var paramsStr = window.location.href.substr(separatorIndex+1);
		var urlValues = paramsStr.toQueryParams();
		if (!this.predefinedValues) {
			this.predefinedValues = {};
		}
		for (var i in urlValues) {
			this.predefinedValues[i] = urlValues[i];
		}
	}
	
	// Overwrite defaults by inputs values if needed Fix
	if (this.predefinedValues) {
		for(var attributeId in this.config.attributes){//this.config.attributes.each(function(element) {
			for (var valueCode in this.predefinedValues) {
				if (valueCode == this.config.attributes[attributeId].code) {
					this.config.attributes[attributeId].defaultOption = this.predefinedValues[valueCode];
				}
			}
		}
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
			//this.settings[i].disabled = true;
		//}
		$(this.settings[i]).childSettings = childSettings.clone();
		$(this.settings[i]).prevSetting   = prevSetting;
		$(this.settings[i]).nextSetting   = nextSetting;
		childSettings.push(this.settings[i]);
	}
	
	
	/*jQuery(function(){
		spConfig.settings.each(function(element){
			// fill attribute map
			if(element.attributeId) {
				spConfig.setMap(element);
			}
		}.bind(this))
	})*/
	
	/**/

	// Set values to inputs
	this.configureForValues();
	document.observe("dom:loaded", this.configureForValues.bind(this));
}

Product.Config.prototype.isIntersecting = function(set1, set2){
	for(var i = set1.length - 1; i >= 0; i--){
		for(var j = set2.length - 1; j >= 0; j--){
			if(set1[i] == set2[j]){
				return true;
			}
		}
	}
	return false;
}

Product.Config.prototype.resetChildren = function(element){
	if(element.childSettings) {
		for(var i=0;i<element.childSettings.length;i++){
			element.childSettings[i].selectedIndex = 0;
			//element.childSettings[i].disabled = true;
			if(element.config){
				this.state[element.config.id] = false;
			}
		}
	}
}

Product.Config.prototype.setMap = function(element){
	//console.log(element.attributeId);
	var attributeId = element.attributeId;
	var options = this.getAttributeOptions(attributeId);
	
	if(options) {
		
		for(var i=options.length-1;i>=0;i--){
			setting = element;
			options[i].map = Array();
			options[i].noMap = Array();
			while(setting.nextSetting){
				var map = Array();
				var noMap = Array();
				for(var nextOptions = setting.nextSetting.options, j = nextOptions.length-1; j >= 0; j--){
					//if((options[i].products.intersect(nextOptions[j].config.allowedProducts)).length == 0){
					if(this.isIntersecting(options[i].products, nextOptions[j].config.allowedProducts)){
						map.push(nextOptions[j].config.id);
					}
					else{
						noMap.push(nextOptions[j].config.id);
					}
				}
				options[i].map = [{
					"attributeId": setting.nextSetting.config.id,
					"map": map
				}];
				options[i].noMap = [{
					"attributeId": setting.nextSetting.config.id,
					"map": noMap
				}];
				
				setting = setting.nextSetting;
			}
		}
		
		
		/*jQuery.each(options, function(iCounter, option){
			setting = element;
			option.map = Array();
			option.noMap = Array();
			while(setting.nextSetting){
				jQuery.each(setting.nextSetting.options, function(jCounter, nextOption){
					if(nextOption.config.allowedProducts && (option.products.intersect(nextOption.config.allowedProducts).uniq()).length == 0){
						option.noMap.push(nextOption.config.id);
					}
					else{
						option.map.push(nextOption.config.id);
					}
				})
				setting = setting.nextSetting;
			}
		})*/
	}
	
	

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

/*
    Some of these override earlier varien/product.js methods, therefore
    varien/product.js must have been included prior to this file.
    script by Matt Dean ( http://organicinternet.co.uk/ )
*/

Product.Config.prototype.getMatchingSimpleProduct = function(){
    var inScopeProductIds = this.getInScopeProductIds();
    //if ((typeof inScopeProductIds != 'undefined') && (inScopeProductIds.length == 1)) {
	if ((typeof inScopeProductIds != 'undefined')) {
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
        if (this.settings[s].selectedIndex <= 0){
            //break;
        }
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

Product.Config.prototype.configureStudElement = function(element) {
    if(element.value){
        this.state[element.config.id] = element.value;
        if(element.nextSetting){
            this.fillSelect(element.nextSetting);
        }
		else{
			this.reloadPrice();
		}
    }
    else {
        //this.resetChildren(element);
    }
};


//SCP: Forces the 'next' element to have it's optionLabels reloaded too
Product.Config.prototype.configureElement = function(element) {
    //this.reloadOptionLabels(element);
    if(element.value){
        this.state[element.config.id] = element.value;
        if(element.nextSetting){
            element.nextSetting.disabled = false;
            this.fillSelect(element.nextSetting);
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
	var selectedValue = element.options[element.selectedIndex].value;
	this.clearSelect(element);
	element.options[0] = new Option(this.config.chooseText, '');

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
				if(selectedValue == options[i].id){
					element.options[index].selected = true;
				}
				/*if(options[i].id == element.config.defaultOption){
					element.options[index].selected = true;
				}*/
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
		var activeIndex = jQuery('#vertical-carousel').find('.activethumb').parent().index();
		if($('image')) {
			/*jQuery('#image').load(function(){
				if(typeof(variations) != 'undefined')
	    			variations.hideWaiting();
			})*/
			jQuery('#vertical-carousel').html('');
			//jQuery('#ulternateviewthumb').html('<ul id="vertical-carousel" class="jcarousel-skin-tango">');
			
			jQuery.each(images, function(index, image){
				//var class = (()?"activethumb":"thumbs");
				if(typeof(image.url) != 'undefined'){
					jQuery('#vertical-carousel').append(
						jQuery('<li>').append(
							jQuery('<span>').append(
								jQuery('<img width="70">')
									.attr('src',image.thumbUrl)
									.click(function(){
										$('image').src = image.url;
										moreViewClicked();
									})
							)
						)
					)
				}
				if(activeIndex == index) {
					$('image').src = image.url;
				}
				jQuery('#vertical-carousel').find('span').addClass('thumbs');
				jQuery('#vertical-carousel').find('span:eq('+activeIndex+')').removeClass('thumbs').addClass('activethumb');
			})
			
			/*jQuery('#vertical-carousel').jcarousel('reload', {
				'animation': 'slow'
			});*/
			//jQuery('#vertical-carousel').jcarousel('destroy');
			/*jQuery('#vertical-carousel').jcarousel({
                vertical: true
            });*/
			
			jQuery('#vertical-carousel').find('li').click(function(){
				jQuery('#vertical-carousel').find('li .activethumb').removeClass('activethumb').addClass('thumbs');
				jQuery(this).find('.thumbs').addClass('activethumb').removeClass('thumbs');
			})
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
		masterContainer = $(module),
		userProduct = {},
		userOptions = [],
		userVariations = [],
		currentShape = 'Round',
		currentMetal = '',
		currentSetting = '',
		settings = $.extend( {
		  // add default settings here
		}, config),
		
		toWeightAlias = function(weight){
			if(weight == '<sup>1</sup>&frasl;<sub>4</sub>' )
				return '250';
			if(weight == '<sup>1</sup>&frasl;<sub>2</sub>' )
				return '500';
			if(weight == '<sup>2</sup>&frasl;<sub>3</sub>' )
				return '650';
			if(weight == '<sup>3</sup>&frasl;<sub>4</sub>' )
				return '750';
			if(weight == '<sup>8</sup>&frasl;<sub>9</sub>' )
				return '900';
			if(weight == '1' )
				return '1000';
			if(weight == '1 <sup>1</sup>&frasl;<sub>2</sub>' )
				return '1500';
			if(weight == '1 <sup>4</sup>&frasl;<sub>5</sub>' )
				return '1800';
			if(weight == '2' )
				return '2000';
			if(weight == '2 <sup>1</sup>&frasl;<sub>2</sub>' )
				return '2500';
			if(weight == '3' )
				return '3000';
			if(weight == '3 <sup>1</sup>&frasl;<sub>2</sub>' )
				return '3500';
			if(weight == '4' )
				return '4000';
			if(weight == '5' )
				return '5000';
			// if any of the above condition false return the weight itself
			return weight;
		},
		
		toNonHtmlWeightAlias = function(weight){
			if(weight == '<sup>1</sup>&frasl;<sub>4</sub>' )
				return '1/4';
			if(weight == '<sup>1</sup>&frasl;<sub>2</sub>' )
				return '1/2';
			if(weight == '<sup>2</sup>&frasl;<sub>3</sub>' )
				return '2/3';
			if(weight == '<sup>3</sup>&frasl;<sub>4</sub>' )
				return '3/4';
			if(weight == '<sup>8</sup>&frasl;<sub>9</sub>' )
				return '8/9';
			if(weight == '1' )
				return '1';
			if(weight == '1 <sup>1</sup>&frasl;<sub>2</sub>' )
				return '1 1/2';
			if(weight == '1 <sup>4</sup>&frasl;<sub>5</sub>' )
				return '1 4/5';
			if(weight == '2' )
				return '2';
			if(weight == '2 <sup>1</sup>&frasl;<sub>2</sub>' )
				return '2 1/2';
			if(weight == '3' )
				return '3';
			if(weight == '3 <sup>1</sup>&frasl;<sub>2</sub>' )
				return '3 1/2';
			if(weight == '4' )
				return '4';
			if(weight == '5' )
				return '5';
			// if any of the above condition false return the weight itself
			return weight;
		},
		
		
		updatePanel = function(){
			/*$.each(userVariations, function(iCounter, variation){
				if(variation.element.is(':disabled')){
					variation.container.css('opacity',.5).find('.customize-block-slide').append($('<div class="clickOverlapper"></div>').click(function(e){e.stopPropagation()}))
				}
				else{
					variation.container.css('opacity',1).find('.clickOverlapper').remove();
				}
			})*/
			$('#customization-block-wrapper').attr('class','Shape-'+currentShape+' Metal-'+currentMetal.replace(/ /g,'-')+' Setting-'+currentSetting.replace(/ /g,'-'))
		},
				
		updateStudAttribute = function(container, element){
			// container.type, container.sliderElement, attributeId, slideSlidableUserOption(), container.find('.userOption'), container.find('.selection-range-block')
			
			if(container.type == 'slider'){
				container.sliderElement.slider("option", "max", element.find('option').length);
				container.sliderElement.slider("option", "value", element[0].selectedIndex + 1);
				slideSlidableUserOption(element.val(), container.attributeId, element, container.attribute, container);
				//updatePanel();
				container.find('.userOption').addClass('userOptionDisabled');
				//var optionWidth = container.find('.selection-range-block').width() / element[0].options.length;
					//n = s!==i? ((r-i)/(s-i)*100 - this.handle.width() * 100/ ( 2 * this.handle.parent().width()) ):0
				element.find('option').each(function(iCounter){
					var optionLeftPosition = ((element[0].options.length > 1 ) ? ((( iCounter ) / ( element[0].options.length - 1))* 100) - ( ( ($('.ui-slider-handle').width()/2)/container.find('.selection-range-block').width()) * 100 ) : 41);
					$('#userOption'+$(this).attr('value')).removeClass('userOptionDisabled').css('left', optionLeftPosition + '%');
				})
			}
			else{
				
				container.find('.user-option-block ul li').addClass('userOptionDisabled');
				element.find('option').each(function(){
					if(container.attribute.code == 'setting_style'){
						$('#userOption'+$(this).attr('value'))
							.removeClass('userOptionDisabled');
							//.find('img').attr('src','/skin/frontend/angara/diamondstud/images/options/'+($(this).text() + '-' + currentShape + '-' + currentMetal).replace(/ /g,'-') +'.jpg');
					}
					else{
						$('#userOption'+$(this).attr('value'))
							.removeClass('userOptionDisabled');
					}
					
				})
				/*container.find('.userOption').hide();
				element.find('option').each(function(){
					$('.userOption'+$(this).attr('value')).show();
				})*/
				selectClickableUserOption(element.val(), container.attributeId, element, container.attribute);
				//updatePanel();
			}
			if(element[0].options.length == 1){
				container.find('.cancel').hide();
			}
			else{
				container.find('.cancel').show();
			}
		},
		
		cancelStudAttribute = function(container, element){
			element[0].selectedIndex = 0;
			if(container.type == 'slider'){
				container.sliderElement.slider("option", "max", element.find('option').length);
				container.sliderElement.slider("option", "value", element[0].selectedIndex + 1);
				slideSlidableUserOption(element.val(), container.attributeId, element, container.attribute, container);
				updatePanel();
			}
			else{
				container.find('.user-option-block ul li').addClass('userOptionDisabled');
				element.find('option').each(function(){
					$('.userOption'+$(this).attr('value')).removeClass('userOptionDisabled');
				})
				selectClickableUserOption(element.val(), container.attributeId, element, container.attribute);
				updatePanel();
			}
		},
		
		beautifyOption = function(attributeId, attribute, container, option, element){
			if(option.id == attribute.defaultOption){
				container.find('.selected-user-option-description').append(
					$('<div id="user-option-description'+option.id+'" class="user-option-description">')
						.html(getDescription(attribute.code, option.label))
						.show()
				)
			}
			else{
				container.find('.selected-user-option-description').append(
					$('<div id="user-option-description'+option.id+'" class="user-option-description">')
						.html(getDescription(attribute.code, option.label))
						.hide()
				)
			}
			
			switch(attribute.code){
				case 'stud_weight':
					var optionLeftPosition = ((( container.find('.userOption').length ) / ( element[0].options.length - 1))* 100) - ( ( (36/2)/container.find('.selection-range-block').width()) * 100 );
					container.find('.selection-range-block ul').append(
						$('<li id="userOption'+option.id+'" class="userOption round-shape-weight-range userOption'+option.id+'" style="left:'+optionLeftPosition+'%"><span>|</span><br>'+option.label+'</li>')
						.click(function(){
							slideSlidableUserOption(option.id, attribute.id, element, attribute, container);
							container.sliderElement.slider("option", "value", element[0].selectedIndex + 1);
						})
					);
					container.find('.view-active-range-option').append(
						'<span style="display:'+((option.id == attribute.defaultOption)?"block":"none")+'" class="active-option-summary active-option-summary'+option.id+'">' + toNonHtmlWeightAlias(option.label) + ' Carat tw</span>'
					);
					if(option.id == attribute.defaultOption){
						container.sliderElement.slider("option", "value", element[0].selectedIndex + 1);
						container.find('.ui-slider-handle').addClass('round-carat-tw-'+(toWeightAlias(option.label)));
						
						defaultStoneWeightSelect(toNonHtmlWeightAlias(option.label));
					}
					
					break;
				case 'stone1_grade':
					var optionLeftPosition = ((( container.find('.userOption').length ) / ( element[0].options.length - 1))* 100) - ( ( (36/2)/container.find('.selection-range-block').width()) * 100 );
					container.find('.selection-range-block ul').append(
						$('<li id="userOption'+option.id+'" class="userOption round-shape-colorclarity-range userOption'+option.id+'" style="left:'+optionLeftPosition+'%"><span>|</span><br>'+option.label+'</li>')
						.click(function(){
							slideSlidableUserOption(option.id, attribute.id, element, attribute, container);
							container.sliderElement.slider("option", "value", element[0].selectedIndex + 1);
						})
					);
					container.find('.view-active-range-option').append(
						'<span style="display:'+((option.id == attribute.defaultOption)?"block":"none")+'" class="active-option-summary active-option-summary'+option.id+'">'+option.label+'</span>'
					);
					if(option.id == attribute.defaultOption){
						container.sliderElement.slider("option", "value", element[0].selectedIndex + 1);
						container.find('.ui-slider-handle').attr('class','ui-slider-handle ui-state-default ui-corner-all '+currentShape.toLowerCase()+'-quality-'+option.label);
						if(option.label == 'GH-VS2'){
							jQuery(function(){
								$('.stud-grade-best').hide();
								$('.stud-grade-heirloom').show();
								$('#angara_selection').show();
							})
						}
						else if(option.label == 'GH-SI2'){
							jQuery(function(){
								$('.stud-grade-heirloom').hide();
								$('.stud-grade-best').show();
								$('#angara_selection').show();
							})
						}
						else{
							jQuery(function(){
								$('#angara_selection').hide();
							})
						}
						
						defaultGemstoneQualitySelect(option.label);
					}
					break;
				case 'stone1_shape':
					if(option.id == attribute.defaultOption){
						currentShape = option.label;
						defaultStoneShapeSelect(option.label);
					}
				case 'metal1_type':
					if(option.id == attribute.defaultOption){
						currentMetal = option.label;
						defaultMetalTypeSelect(option.label);
					}
					container.find('.user-option-block ul').append(
					$('<li id="userOption'+option.id+'" class="userOption userOption'+option.id+'">')
							.append('<span class="'+((option.id == attribute.defaultOption)?"activethumb":"thumbs")+'"><span class="clickable-option-image option-'+option.label.replace(/ /g,'-')+'"></span></span><span class="option-title">'+option.label+'</span>')
							.click(function(){
								clickableOptionClicked(option.id, attributeId, element, attribute);
							})
					);
					container.find('.view-active-option').append(
						'<span style="display:'+((option.id == attribute.defaultOption)?"block":"none")+'" class="active-option-summary active-option-summary'+option.id+'">'+option.label+'<span class="small-option-image small-option-'+option.label.replace(/ /g,'-')+'"></span></span>'
					);
					container.find('.done').click(function(){
						clickableOptionClicked(element.val(), attributeId, element, attribute);
						masterContainer.accordion({ active: container.index()+1 });
					})
					break;
				case 'setting_style':
					if(option.id == attribute.defaultOption){
						currentSetting = option.label;
						defaultStoneSettingSelect(option.label);
					}
					container.find('.user-option-block ul').append(
					$('<li id="userOption'+option.id+'" class="userOption userOption'+option.id+'">')
							.append('<span class="'+((option.id == attribute.defaultOption)?"activethumb":"thumbs")+'"><span class="setting-style-sprite Setting-'+(option.label).replace(/ /g,'-')+'"></span></span><span class="option-title">'+option.label+'</span>')
							.click(function(){
								clickableOptionClicked(option.id, attributeId, element, attribute);
							})
					);
					container.find('.view-active-option').append(
						'<span style="display:'+((option.id == attribute.defaultOption)?"block":"none")+'" class="active-option-summary active-option-summary'+option.id+'">'+option.label+'<span class="setting-style-sprite-small Setting-Small-'+(option.label).replace(/ /g,'-')+'"></span></span>'
					);
					container.find('.done').click(function(){
						clickableOptionClicked(element.val(), attributeId, element, attribute);
						masterContainer.accordion({ active: container.index()+1 });
					})
					break;
				
			}
		},
		
		slideSlidableUserOption = function(userOptionId, attributeId, element, attribute, container){
			//var container = getContainerByAttributeId(attributeId);
			
			$('#attribute'+attributeId).val(userOptionId);
			spConfig.configureStudElement($('#attribute'+attributeId)[0]);
			container.find('.active-option-summary').hide();
			$('.active-option-summary' + element.val()).show();
			container.find('.user-option-description').hide();
			$('#user-option-description'+element.val()).show();
			
			if(attribute.code == 'stud_weight'){
				$.each(attribute.options,function(iCounter,option){
					if(element.val() == option.id){ 
						container.find('.ui-slider-handle').attr('class','ui-slider-handle ui-state-default ui-corner-all '+currentShape.toLowerCase()+'-carat-tw-'+(toWeightAlias(option.label)));
						stoneWeightSelect(toNonHtmlWeightAlias(option.label));
					}
				})
			}
			else if(attribute.code == 'stone1_grade'){
				$.each(attribute.options,function(iCounter,option){
					if(element.val() == option.id){ 
						container.find('.ui-slider-handle').attr('class','ui-slider-handle ui-state-default ui-corner-all '+currentShape.toLowerCase()+'-quality-'+option.label);
						if(option.label == 'GH-VS2'){
							$('.stud-grade-best').hide();
							$('.stud-grade-heirloom').show();
							$('#angara_selection').show();
						}
						else if(option.label == 'GH-SI2'){
							$('.stud-grade-heirloom').hide();
							$('.stud-grade-best').show();
							$('#angara_selection').show();
						}
						else{
							$('#angara_selection').hide()
						}
						gemstoneQualitySelect(option.label);
					}
				})
			}
			updatePanel();
			if(typeof(container.nextContainer) != 'undefined')
				updateStudAttribute(container.nextContainer, container.nextElement);
		},
		
		slidableOptionSliding = function(element, container, attribute, valueIndex){
			if(attribute.code == 'stud_weight'){
				container.find('.ui-slider-handle').attr('class','ui-slider-handle ui-state-default ui-corner-all '+currentShape.toLowerCase()+'-carat-tw-'+(toWeightAlias(element[0].options[valueIndex-1].text)));
			}
			else if(attribute.code == 'stone1_grade'){
				container.find('.ui-slider-handle').attr('class','ui-slider-handle ui-state-default ui-corner-all '+currentShape.toLowerCase()+'-quality-'+element[0].options[valueIndex-1].text);
			}
		},
		
		slidableOptionSlided = function(element, container, attribute, valueIndex){
			//module.showWaiting();
			element[0].selectedIndex = valueIndex - 1;
			slideSlidableUserOption(element.val(), attribute.id, element, attribute, container);
			
		},
		
		slidableAttributeSetup = function(attributeId, attribute, container, element){
			container.find('.selection-range-block').prepend('<ul>')
			
			container.find('.done').click(function(){
				slidableOptionSlided(element, container, attribute, element[0].selectedIndex + 1);
				masterContainer.accordion({ active: container.index()+1 });
			})
			
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
				container.find('.slider_wrapper').append(
					$('<div class="move-slider-right"></div>').click(function(){
						if(element[0].selectedIndex + 1 < element[0].options.length){
							element[0].selectedIndex = element[0].selectedIndex + 1;
							updateStudAttribute(container, element);
						}
					})
				)
				.append(
					$('<div class="move-slider-left"></div>').click(function(){
						if(element[0].selectedIndex > 0){
							element[0].selectedIndex = element[0].selectedIndex - 1;
							updateStudAttribute(container, element);
						}
					})
				)
			}
			
			container.sliderElement.slider({
				range: false,
				value: element[0].selectedIndex + 1,
				min: 1,
				max: $('#attribute'+attributeId).find('option').length,
				stop: function( event, ui ) {
					slidableOptionSlided(element, container, attribute, ui.value);
				},
				slide: function( event, ui ){
					slidableOptionSliding(element, container, attribute, ui.value);
				}
			});
			
			$.each(attribute.options,function(iCounter,option){
				if(option.id == attribute.defaultOption){
					$('#attribute'+attributeId).val(option.id)
				}
				beautifyOption(attributeId, attribute, container, option, element);
			});
			
			container.find('.cancel a').click(function(){
				cancelStudAttribute(container, element);
				return false;
			})
			
		},
		
		getContainerByAttributeId = function(attributeId){
			for(var i = 0; i < userVariations.length; i++){
				if(userVariations[i].attributeId == attributeId){
					return userVariations[i].container;
				}
			}
			return false;
		},
		
		selectClickableUserOption = function(userOptionId, attributeId, element, attribute){
			var userOption = $('#userOption'+userOptionId);
			if(!userOption.hasClass('userOptionDisabled')){
				var container = getContainerByAttributeId(attributeId);
				//optionChanged(attributeId, attribute, option);
				$('#attribute'+attributeId).val(userOptionId);
				spConfig.configureStudElement($('#attribute'+attributeId)[0]);
				if(userOption.find('.activethumb').length == 0){
					userOption.parent().find('.activethumb').removeClass('activethumb').addClass('thumbs');
					userOption.find('.thumbs').removeClass('thumbs').addClass('activethumb');
					container.find('.active-option-summary').hide();
					$('.active-option-summary' + $('#attribute'+attributeId).val()).show();
				}
				container.find('.user-option-description').hide();
				$('#user-option-description'+userOptionId).show();
				
				
				$.each(attribute.options,function(iCounter,option){
					if(element.val() == option.id){
						if(attribute.code == 'stone1_shape'){
							currentShape = option.label;
							stoneShapeSelect(option.label);
						}
						else if(attribute.code == 'metal1_type'){
							currentMetal = option.label;
							metalTypeSelect(option.label);
						}
						else if(attribute.code == 'setting_style'){
							currentSetting = option.label;
							stoneSettingSelect(option.label);
						}
					}
				})
				updatePanel();
				
				if(typeof(container.nextContainer) != 'undefined')
					updateStudAttribute(container.nextContainer, container.nextElement);
			}
		},
		
		clickableOptionClicked = function(userOptionId, attributeId, element, attribute){
			var userOption = $('#userOption'+userOptionId);
			if(!userOption.hasClass('userOptionDisabled')){
				//module.showWaiting();
				selectClickableUserOption(userOptionId, attributeId, element, attribute);
			}
		},
		
		clickableAttributeSetup = function(attributeId, attribute, container, element){
			container.find('.user-option-block').prepend('<ul>')
			$.each(attribute.options,function(iCounter,option){
				beautifyOption(attributeId, attribute, container, option, element);
				if(option.id == attribute.defaultOption){
					$('#attribute'+attributeId).val(option.id)
				}
			});
			container.find('.cancel a').click(function(){
				cancelStudAttribute(container, element);
				return false;
			})
		},
		
		attributeSetup = function(attributeId, attribute){
			switch(attribute.code){
				case 'stone1_shape':
					//module.shapeVariation.element = $('#attribute'+attributeId);
					//module.shapeVariation.attribute = attribute;
					module.shapeVariation.attributeId = attributeId;
					module.shapeVariation.attribute = attribute;
					module.shapeVariation.type = 'clickable';
					userVariations.push({'attributeId': attributeId, 'element': $('#attribute'+attributeId), 'container': module.shapeVariation});
					
					clickableAttributeSetup(attributeId, attribute, module.shapeVariation, $('#attribute'+attributeId))
					break;
				case 'setting_style':
					module.settingVariation.attributeId = attributeId;
					module.settingVariation.attribute = attribute;
					module.settingVariation.type = 'clickable';
					userVariations.push({'attributeId': attributeId, 'element': $('#attribute'+attributeId), 'container': module.settingVariation});
					
					//setting related element
					module.shapeVariation.nextContainer = module.settingVariation;
					module.shapeVariation.nextElement = $('#attribute'+attributeId);
					
					clickableAttributeSetup(attributeId, attribute, module.settingVariation, $('#attribute'+attributeId))
					break;
				case 'stud_weight':
					module.weightVariation.attributeId = attributeId;
					module.weightVariation.attribute = attribute;
					module.weightVariation.type = 'slider';
					module.weightVariation.sliderElement = $('#slider_dw');
					userVariations.push({'attributeId': attributeId, 'element': $('#attribute'+attributeId), 'container': module.weightVariation});
					
					//setting related element
					module.settingVariation.nextContainer = module.weightVariation;
					module.settingVariation.nextElement = $('#attribute'+attributeId);
					
					slidableAttributeSetup(attributeId, attribute, module.weightVariation, $('#attribute'+attributeId));
					break;
				case 'stone1_grade':
					module.qualityVariation.attributeId = attributeId;
					module.qualityVariation.attribute = attribute;
					module.qualityVariation.type = 'slider';
					module.qualityVariation.sliderElement = $('#slider_dq');
					userVariations.push({'attributeId': attributeId, 'element': $('#attribute'+attributeId), 'container': module.qualityVariation});
					
					//setting related element
					module.weightVariation.nextContainer = module.qualityVariation;
					module.weightVariation.nextElement = $('#attribute'+attributeId);
					
					slidableAttributeSetup(attributeId, attribute, module.qualityVariation, $('#attribute'+attributeId));
					break;
				case 'metal1_type':
					module.metalVariation.attributeId = attributeId;
					module.metalVariation.attribute = attribute;
					module.metalVariation.type = 'clickable';
					userVariations.push({'attributeId': attributeId, 'element': $('#attribute'+attributeId), 'container': module.metalVariation});
					
					//setting related element
					module.qualityVariation.nextContainer = module.metalVariation;
					module.qualityVariation.nextElement = $('#attribute'+attributeId);
					
					clickableAttributeSetup(attributeId, attribute, module.metalVariation, $('#attribute'+attributeId))
					break;
				
			}
			/*var optionListContainer = $('<div class="option-list-container" id="option-list-container'+attributeId+'">').appendTo(parent);
			var optionDetailsContainer = $('<div class="option-details-container"></div>');
			optionListContainer.append('<div class="small-title">Select: <strong style="color:#2b2b2b">'+attribute.label+'</strong></div>');
			$.each(attribute.options,function(iCounter,option){optionMarkup(option, attributeId, attribute, optionListContainer, optionDetailsContainer)});
			optionListContainer.append('<div style="clear:both"></div>')
			optionListContainer.append(optionDetailsContainer);*/
			
		},
		
		// generate variation html here
		variationsMarkup = function(){
			
			masterContainer.accordion({
				header		: 	'div.customize-block-slide',//	Main div of accordion tab	
				collapsible	: 	true,
				heightStyle	: 	"content",					//	Controls the height of accordion content.	auto,	content,	fill
				icons		:	false,
				animate		:	true,
				active		: false
			});
			
			if(ie && ie < 9){
				masterContainer.accordion({animate		:	false })
			}
			
			module.weightVariation = $('#diamond-weight-customizer');
			module.qualityVariation = $('#diamond-quality-customizer');
			module.shapeVariation = $('#diamond-shape-customizer');
			module.metalVariation = $('#metal-type-customizer');
			module.settingVariation = $('#setting-style-customizer');
			
			$('#vertical-carousel').find('li').click(function(){
				$('#vertical-carousel').find('li .activethumb').removeClass('activethumb').addClass('thumbs');
				$(this).find('.thumbs').addClass('activethumb').removeClass('thumbs');
			})
			
			$.each(settings.attributes,function(iCounter,attribute){attributeSetup(iCounter, attribute)});
			
			// set first variation
			$(function(){
				updateStudAttribute(module.shapeVariation , $('#attribute'+module.shapeVariation.attributeId));
				masterContainer.accordion({ active: 0 })
			})
			updatePanel();
		},

		setup = function(){
			variationsMarkup();
			return module;
		},
		
		getDescription = function(attributeCode, optionLabel){
			switch(attributeCode){
				case 'stud_weight':
					if(optionLabel == '<sup>2</sup>&frasl;<sub>3</sub>')
						return '<div class="selected-option-title">'+(optionLabel)+' Carat tw:</div>A smart buy - looks less than 5% smaller than a 3/4 carat pair, at ~ 20% less in price.';
					if(optionLabel == '<sup>8</sup>&frasl;<sub>9</sub>')
						return '<div class="selected-option-title">'+(optionLabel)+' Carat tw:</div>A smart buy - looks less than 5% smaller than a 1 carat pair, at ~ 25% less in price.';
					if(optionLabel == '1')
						return '<div class="selected-option-title">'+(optionLabel)+' Carat tw:</div>The most popular size for diamond studs.';
					if(optionLabel == '1 <sup>4</sup>&frasl;<sub>5</sub>')
						return '<div class="selected-option-title">'+(optionLabel)+' Carat tw:</div>A smart buy - looks less than 5% smaller than a 2 carat pair, at ~ 24% less in price.';
					break;
				case 'stone1_grade':
					if(optionLabel == 'GH-VS2')
						return '<div class="selected-option-title">'+optionLabel+':</div>When only the best will do.  Color is very close to pure white, with even minor traces of color being very difficult to detect.  Nearly flawless clarity, with imperfections that are difficult to see even under 10X magnification.';
					else if(optionLabel == 'GH-SI2')
						return '<div class="selected-option-title">'+optionLabel+':</div>High quality at a great value.  An ideal mix of quality and size given a defined budget.  Possible to see slight hints of color in the diamond.  Imperfections can be seen under 10X magnification, but are difficult to see with the unaided eye.';
					else if(optionLabel == 'IJ-I1')
						return '<div class="selected-option-title">'+optionLabel+':</div>Medium quality at value pricing.  When size is more important than quality given a defined budget.  Hints of color can be seen in the diamond.  Small imperfections can be seen with the unaided eye.';
					else if(optionLabel == 'KM-I2')
						return '<div class="selected-option-title">'+optionLabel+':</div>When size matters most given a defined budget.  Has noticeable color.  Imperfections are clearly visible to the naked eye.';
					break;
				case 'stone1_shape':
					if(optionLabel == 'Round')
						return '<div class="selected-option-title">'+optionLabel+':</div>The most brilliant and most popular shape for diamonds.';
					else if(optionLabel == 'Princess')
						return '<div class="selected-option-title">'+optionLabel+':</div>The most popular non-round diamond shape.  Princess cut diamonds have pointed corners and are square in shape.';
					break;
				case 'metal1_type':
					if(optionLabel == '14K White Gold')
						return '<div class="selected-option-title">'+optionLabel+':</div>The most popular choice for fine jewelry.  Highly durable and scratch resistant.  Angara recommends 14k white gold for most jewelry.';
					if(optionLabel == '14K Yellow Gold')
						return '<div class="selected-option-title">'+optionLabel+':</div>Classic, highly durable and scratch resistant.  The golden yellow color adds a glow to the jewelry piece.';
					if(optionLabel == 'Platinum')
						return '<div class="selected-option-title">'+optionLabel+':</div>The most premium, scratch resistant metal offered.  Recommended for highly exclusive fine jewelry.';
					if(optionLabel == 'Silver')
						return '<div class="selected-option-title">'+optionLabel+':</div>The most affordable option.  Looks identical to white gold, but is lighter in weight and scratches easily.  Holds gemstones less securely.';
					break;
				case 'setting_style':
					break;
			}
			return '';
		}
		;
	
	module.showWaiting = function(){
		$('#page-mid').addClass('ui-in-progress');
	};
	
	module.hideWaiting = function(){
		$('#page-mid').removeClass('ui-in-progress');
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
			/*for(var iCounter=0; iCounter < settings.childProducts[productId].stones.length;  iCounter++){	// looping all stones
				$.each(settings.childProducts[productId].stones[iCounter],function(label,value){ 	// looping all details of stone
					$('.dyn_stone'+(iCounter+1)+'_'+label).text(value);		// replacing stone details
				});
			}
			for(var iCounter=0; iCounter < settings.childProducts[productId].metals.length;  iCounter++){	// looping all metals
				$.each(settings.childProducts[productId].metals[iCounter],function(label,value){ 	// looping all details of stone
					$('.dyn_metal'+(iCounter+1)+'_'+label).text(value);		// replacing stone details
				});
			}
			
			$('.dyn_msrp').text(optionsPrice.formatPrice(settings.childProducts[productId].msrp));*/
			jewelProduct.setVendorLeadTime(settings.childProducts[productId].vendorLeadTime);
			jewelProduct.updateShippingDate();
			
			// # @todo implement retail price change logic
			//$('.dyn_msrp').text(optionsPrice.priceFormat.pattern + (settings.childProducts[productId].msrp).toFixed(2));
			
			// # @todo implement shipping date change logic
		}
	};
	  
    return setup();
	
  };
})( jQuery );