/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: n/a
 * Generated:   2011-06-08 07:09:19
 * File path:   skin/frontend/default/default/js/adjnav-14.js
 * Copyright:   (c) 2011 AITOC, Inc.
 */
// checking if IE: this variable will be understood by IE: isIE = !false
isIE = /*@cc_on!@*/false;
isXS = false;
isUpdating = false;

Control.Slider.prototype.setDisabled = function()
{
    this.disabled = true;
    
    if (!isIE)
    {
        this.track.parentNode.className = this.track.parentNode.className + ' disabled';
    }
};


Control.Slider.prototype._isButtonForDOMEvents = function (event, code) {
    return event.which ? (event.which === code + 1) : (event.button === code);
}

Control.Slider.prototype.startDrag = function(event) {
    if((this._isButtonForDOMEvents(event,0)) || (Event.isLeftClick(event)) || this.isTouchDevice)  {
      if (!this.disabled){
        this.active = true;
		
		this.width		= this.track.getWidth();

        var handle = Event.element(event);
        var pointer  = [this.isTouchDevice?event.touches[0].pageX:Event.pointerX(event), this.isTouchDevice?event.touches[0].pageY:Event.pointerY(event)];
        var track = handle;
        if (track==this.track) {
          var offsets  = this.track.cumulativeOffset();
          this.event = event;
          this.setValue(this.translateToValue(
           (pointer[0]-offsets[0])-(this.handleLength/2)
          ));
          var offsets  = this.activeHandle.cumulativeOffset();
          this.offsetX = (pointer[0] - offsets[0]);
          this.offsetY = (pointer[1] - offsets[1]);
        } 
		else {
          // find the handle (prevents issues with Safari)
          while((this.handles.indexOf(handle) == -1) && handle.parentNode)
            handle = handle.parentNode;

          if (this.handles.indexOf(handle)!=-1) {
            this.activeHandle    = handle;
            this.activeHandleIdx = this.handles.indexOf(this.activeHandle);
            this.updateStyles();

            var offsets  = this.activeHandle.cumulativeOffset();
            this.offsetX = (pointer[0] - offsets[0]);
            this.offsetY = (pointer[1] - offsets[1]);
          }
        }
      }
      Event.stop(event);
    }
  };

 
function adj_nav_hide_products()
{
    // #todo hide catalog and filters
}

function adj_nav_show_products(transport)
{
	jQuery('#adj-nav-navigation, #adj-nav-container').removeClass('disabled');
	jQuery('#adj-nav-container .catalog-loader').addClass('hidden')
	isUpdating = false;
    var resp = {} ;
    if (transport && transport.responseText){
        try {
            resp = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            resp = {};
        }
    }
    
    if (resp.products){
        var el = $('adj-nav-container');
        var ajaxUrl = $('adj-nav-ajax').value;
        
        el.update(resp.products.gsub(ajaxUrl, $('adj-nav-url').value));
        adj_nav_toolbar_init(); // reinit listeners
                
        $('adj-nav-navigation').update(resp.layer.gsub(ajaxUrl, $('adj-nav-url').value));
        
        $('adj-nav-ajax').value = ajaxUrl;  
    }
    
    var items = $('filter-panel').select('a','input');
    n = items.length;
    for (i=0; i<n; ++i){
        items[i].removeClassName('adj-nav-disabled');
    }
    if (typeof(adj_slider) != 'undefined')
        adj_slider.setEnabled();
	jQuery('div[data-toggle="tooltip"]').tooltip();
	jQuery("img").unveil();
	
	lazyLoad();
	
}

function adj_nav_load_products(transport)
{
    var resp = {} ;
    if (transport && transport.responseText){
        try {
            resp = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            resp = {};
        }
    }
    
    if (resp.products){
        var el = $('adj-nav-container');
        var ajaxUrl = $('adj-nav-ajax').value;
        //console.log(resp.products.gsub(ajaxUrl, $('adj-nav-url').value));
        //el.update(resp.products.gsub(ajaxUrl, $('adj-nav-url').value));
		jQuery('#adj-nav-container').append(resp.products.gsub(ajaxUrl, $('adj-nav-url').value));
        adj_nav_toolbar_init(); // reinit listeners
                
        //$('adj-nav-navigation').update(resp.layer.gsub(ajaxUrl, $('adj-nav-url').value));
        
        $('adj-nav-ajax').value = ajaxUrl;  
    }
    
    var items = $('filter-panel').select('a','input');
    n = items.length;
    for (i=0; i<n; ++i){
        items[i].removeClassName('adj-nav-disabled');
    }
    if (typeof(adj_slider) != 'undefined')
        adj_slider.setEnabled();
		
	jQuery(window).data('catalog-loading','stop');
	jQuery('.adj-nav-progress').slice(0, -1).remove();
	jQuery('.adj-nav-progress').hide();
	jQuery('div[data-toggle="tooltip"]').tooltip();
	jQuery("img").unveil();
}

function adj_nav_add_params(k, v, isSingleVal)
{
    $('adj-nav-params').value = $('adj-nav-params').value.gsub(/\+/, ' ');
    var el = $('adj-nav-params');
    var params = el.value.parseQuery();
    
    var strVal = params[k];
    if (typeof strVal == 'undefined' || !strVal.length){
        params[k] = v;
    }
    else if('clear' == v ){
        params[k] = 'clear';
    }
    else {
        if (k == 'price')
            var values = strVal.split(',');
        else
            var values = strVal.split('-');
        
//        var values = strVal.split('-');
        if (-1 == values.indexOf(v)){
            if (isSingleVal)
                values = [v];
            else 
                values.push(v);
        } 
        else {
            values = values.without(v);
        }
                
        params[k] = values.join('-');
     }
        
   el.value = Object.toQueryString(params);//.gsub('%2B', '+');
}



function adj_nav_make_request()
{
	if(!(isUpdating)){
		isUpdating = true;
		jQuery('#catalog-filter-controller').data('filter-panel-display-state', jQuery('#filter-panel').css('display'));
		jQuery('#catalog-filter-controller').data('filter-panel-current-tab-index', jQuery('.catalog-filter .active').parent().index())
		jQuery(window).data('page-catalog',1);
		jQuery('.adj-nav-progress').hide();
		
		adj_nav_hide_products();   
		$('adj-nav-params').value = $('adj-nav-params').value.gsub(/\+/, ' ');
		var params = $('adj-nav-params').value.parseQuery();    
		
		if (!params['order']) // Respect Sort By settings!
		{
			params['order'] = 'position';
		}
		if (!params['dir'])
		{
			if(params['order'] == 'relevance'){
				params['dir'] = 'desc';
			}
			else{
				params['dir'] = 'asc';
			}
		}
		params['noFilters'] = 0;
		
		$('adj-nav-params').value = Object.toQueryString(params);
		
		jQuery('#adj-nav-navigation, #adj-nav-container').addClass('disabled');
		jQuery('#adj-nav-container .catalog-loader').removeClass('hidden')
		
		// tmp aitoc    
		new Ajax.Request($('adj-nav-ajax').value + '?' + $('adj-nav-params').value + '&no_cache=true', 
			{method: 'get', onSuccess: adj_nav_show_products}
		);		
	}

}

function adj_nav_make_load_more_request()
{
    $('adj-nav-params').value = $('adj-nav-params').value.gsub(/\+/, ' ');
    var params = $('adj-nav-params').value.parseQuery();    
    
    if (!params['order']) // Respect Sort By settings!
	{
		params['order'] = 'position';
	}
	if (!params['dir'])
    {
		if(params['order'] == 'relevance'){
			params['dir'] = 'desc';
		}
		else{
        	params['dir'] = 'asc';
		}
    }
	params['noFilters'] = 1;
	
	$('adj-nav-params').value = Object.toQueryString(params);
    
	jQuery('.adj-nav-progress:last').show();
    // tmp aitoc    
    new Ajax.Request($('adj-nav-ajax').value + '?' + $('adj-nav-params').value + '&no_cache=true', 
        {method: 'get', onSuccess: adj_nav_load_products}
    );
}

function adj_update_links(evt, className, isSingleVal)
{
    var link = Event.findElement(evt, 'A'),
        sel = className + '-selected';	
    
    if (link.hasClassName(sel)){
        link.removeClassName(sel);
		if(!(isUpdating))
			jQuery(link).parent().addClass('padding-type-5').removeClass('padding-type-3 showcase-border-thick border-active active');
	}
    else{
		
		if(link.id.split('-')[0] == 'filterable_metal_types'){
			/*jQuery('.adj-nav-attribute[id^="filterable_metal_types"]').each(function(){
				if(jQuery(this).attr('id') != link.id && jQuery(this).hasClass('adj-nav-attribute-selected')){
					jQuery(this).removeClass('adj-nav-attribute-selected');
					adj_nav_add_params(jQuery(this).attr('id').split('-')[0], jQuery(this).attr('id').split('-')[1], 1);
				}
			})*/
			isSingleVal = 1;
		}
		/*if(link.id.split('-')[0] == 'filterable_stone_grades'){
			jQuery('.adj-nav-attribute[id^="filterable_stone_grades"]').each(function(){
				//alert(jQuery(this).attr('id'))
				if(jQuery(this).attr('id') != link.id && jQuery(this).hasClass('adj-nav-attribute-selected')){
					jQuery(this).removeClass('adj-nav-attribute-selected');
					adj_nav_add_params(jQuery(this).attr('id').split('-')[0], jQuery(this).attr('id').split('-')[1], isSingleVal);
				}
			})
		}*/
		
		link.addClassName(sel);
		if(!(isUpdating))
			jQuery(link).parent().removeClass('padding-type-5').addClass('padding-type-3 showcase-border-thick border-active active');
	}
	
    
    //only one  price-range can be selected
    if (isSingleVal){
        var items = $('filter-panel').getElementsByClassName(className);
        var i, n = items.length;
        for (i=0; i<n; ++i){
            if (items[i].hasClassName(sel) && items[i].id != link.id)
                items[i].removeClassName(sel);   
        }
    }

    adj_nav_add_params(link.id.split('-')[0], link.id.split('-')[1], isSingleVal);
    
    adj_nav_make_request();    
    
    Event.stop(evt);    
}


function adj_nav_attribute_listener(evt)
{	
    adj_nav_add_params('p', 'clear', 1);
    adj_update_links(evt, 'adj-nav-attribute', 0);
}

function adj_nav_icon_listener(evt)
{
    adj_nav_add_params('p', 'clear', 1);
    adj_update_links(evt, 'adj-nav-icon', 0);
}

function adj_nav_price_listener(evt)
{
    adj_nav_add_params('p', 'clear', 1);
    adj_update_links(evt, 'adj-nav-price', 1);
}

function adj_nav_clear_listener(evt)
{
    var link = Event.findElement(evt, 'A'),
        varName = link.id.split('-')[0];
    
    adj_nav_add_params('p', 'clear', 1);
    adj_nav_add_params(varName, 'clear', 1);
    
    if ('price' == varName){
        var from =  $('adj-nav-price-from'),
            to   = $('adj-nav-price-to');
          
        if (Object.isElement(from)){
            from.value = from.name;
            to.value   = to.name;
        }
    }
    
    adj_nav_make_request();    
    
    Event.stop(evt);  
}


function adj_nav_round(num){
    num = parseFloat(num);
    if (isNaN(num))
        num = 0;
        
    return Math.round(num);
}

function adj_nav_price_input_listener(evt){
    if (evt.type == 'keypress' && 13 != evt.keyCode)
        return;
        
    if (evt.type == 'keypress')
    {
        var inpObj = Event.findElement(evt, 'INPUT');
    }
    else 
    {
        var inpObj = Event.findElement(evt, 'INPUT');
    }
    
    var sKey = inpObj.id.split('---')[1];
        
    var numFrom = adj_nav_round($('adj-nav-price-from---' + sKey).value),
        numTo   = adj_nav_round($('adj-nav-price-to---' + sKey).value);
	
	jQuery('#adj-nav-price-from---price').val('').attr('placeholder', formatCurrency(numFrom, {'pattern':'$%s', 'requiredPrecision':0}));
	jQuery('#adj-nav-price-to---price').val('').attr('placeholder', formatCurrency(numTo, {'pattern':'$%s', 'requiredPrecision':0}));
 
    if ((numFrom<0.01 && numTo<0.01) || numFrom<0 || numTo<0)   
        return;

    adj_nav_add_params('p', 'clear', 1);
//    adj_nav_add_params('price', numFrom + ',' + numTo, true);
    adj_nav_add_params(sKey, numFrom + ',' + numTo, true);
    adj_nav_make_request();         
}

function adj_nav_category_listener(evt){
    var link = Event.findElement(evt, 'A');
    var catId = link.id.split('-')[1];
    
    var reg = /cat-/;
    if (reg.test(link.id)){ //is search
        adj_nav_add_params('cat', catId, 1);
        adj_nav_add_params('p', 'clear', 1);
        adj_nav_make_request(); 
        Event.stop(evt);  
    }
    //do not stop event
}

function adj_nav_toolbar_listener(evt){
    adj_nav_toolbar_make_request(Event.findElement(evt, 'A').href);
    Event.stop(evt); 
}

function adj_nav_toolbar_make_request(href)
{
    var pos = href.indexOf('?');
    if (pos > -1){
        $('adj-nav-params').value = href.substring(pos+1, href.length);
    }
    adj_nav_make_request();
}

function adj_nav_load_more_make_request(href)
{
    var pos = href.indexOf('?');
    if (pos > -1){
        $('adj-nav-params').value = href.substring(pos+1, href.length);
    }
    adj_nav_make_load_more_request();
}

function adj_nav_toolbar_init()
{
	//var items = $('adj-nav-container').select('.pages a', '.view-by a');
    /*var items = $('adj-nav-container').select('.pages a', '.view-mode a', '.sort-by a');
    var i, n = items.length;
    for (i=0; i<n; ++i){
        Event.observe(items[i], 'click', adj_nav_toolbar_listener);
    }
	*/
}

function adj_nav_clearall_listener(evt)
{
    $('adj-nav-params').value = $('adj-nav-params').value.gsub(/\+/, ' '); 
    var params = $('adj-nav-params').value.parseQuery();
    $('adj-nav-params').value = 'adjclear=true';
    if (params['q'])
    {
        $('adj-nav-params').value += '&q=' + params['q'];
    }
    adj_nav_make_request();
    Event.stop(evt); 
}

function adj_nav_init()
{
	if(jQuery(window).width() < 768){
		isXS = true
	}
	
	jQuery(window).data('catalog-loading', 'stop'); // reset ajax loading
	
	jQuery('.catalog-filter-nav-item:eq(' + jQuery('#catalog-filter-controller').data('filter-panel-current-tab-index') + ')').addClass('active');
	jQuery('.catalog-filter-content:eq(' + jQuery('#catalog-filter-controller').data('filter-panel-current-tab-index') + ')').show();
	jQuery('#applied-filter-list').html('');
	
	/* For small devices separate code */
	if(isXS){		
		jQuery('.catalog-filter-content').each(function(index){
			jQuery('.catalog-filter-content:eq(' + index + ')').appendTo(jQuery('.catalog-filter:eq(' + index + ')'));
		})
		jQuery('.catalog-filter .catalog-filter-nav-item').on('vclick',function(){
			if(!(isUpdating)){
				jQuery('.catalog-filter-content').hide();
				if(jQuery(this).hasClass('active')){
					jQuery(this).parent().find('.catalog-filter-content').hide();
					jQuery('.catalog-filter-nav-item').removeClass('active');
				}
				else{
					jQuery(this).parent().find('.catalog-filter-content').show();
					jQuery('.catalog-filter-nav-item').removeClass('active');
					jQuery(this).addClass('active');
				}
			}
		});
	}
	else{
		jQuery('.catalog-filter').click(function(){
			if(!(isUpdating)){
				var currentIndex = jQuery(this).index();
				jQuery('.catalog-filter-content').hide();
				jQuery('.catalog-filter-content:eq(' + currentIndex + ')').show();
				jQuery('.catalog-filter .catalog-filter-nav-item').removeClass('active');
				jQuery(this).find('.catalog-filter-nav-item').addClass('active');
			}
		});
	}
	
	
	if(jQuery('.catalog-filter-item .active').length > 0){
		jQuery('#applied-filter-list-container').removeClass('hidden');
	}
	jQuery('.catalog-filter-item .active').each(function(){
		var filterTypeIndex = jQuery(this).parents('.catalog-filter-content').index();
		var filterText = jQuery.trim(jQuery('.catalog-filter:eq(' + filterTypeIndex + ') .catalog-filter-nav-item').text()) + ': ' + jQuery(this).text();
		var removeLink = jQuery(this).find('a').attr('href');
		var uid = jQuery(this).find('a').attr('id')
		jQuery('#applied-filter-list').append('<div class="pull-left padding-type-5 showcase-bg-dark high-margin-left high-padding-left low-margin-bottom">' + filterText + ' <a id="' + uid + '-2" class="adj-nav-attribute adj-nav-attribute-selected" href="' + removeLink + '"><i class="fa fa-times fa-fw low-padding-left clickable"></i></a></div>');
	})
	
	jQuery('#filter-panel').css('display',jQuery('#catalog-filter-controller').data('filter-panel-display-state'));
	
    var items, i, j, n, 
        classes = ['category', 'attribute', 'icon', 'price', 'clear', 'clearall'];
    
    for (j=0; j<classes.length; ++j){
        items = $('adj-nav-navigation').select('.adj-nav-' + classes[j]);
        n = items.length;
        for (i=0; i<n; ++i){
            Event.observe(items[i], 'click', eval('adj_nav_' + classes[j] + '_listener'));
        }
    }

	// start new fix code    
    items = $('filter-panel').select('.adj-nav-price-input-id');
    
    n = items.length;
    
    //var btn = $('adj-nav-price-go');
    
    for (i=0; i<n; ++i)
    {
        //btn = $('adj-nav-price-go---' + items[i].value);
        //if (Object.isElement(btn)){
            //Event.observe(btn, 'focusout', adj_nav_price_input_listener);
            Event.observe($('adj-nav-price-from---' + items[i].value), 'keypress', adj_nav_price_input_listener);
            Event.observe($('adj-nav-price-to---' + items[i].value), 'keypress', adj_nav_price_input_listener);
			Event.observe($('adj-nav-price-from---' + items[i].value), 'blur', adj_nav_price_input_listener);
            Event.observe($('adj-nav-price-to---' + items[i].value), 'blur', adj_nav_price_input_listener);
			jQuery('#adj-nav-price-from---price, #adj-nav-price-to---price').bind('vclick',function(e){
				e.stopPropagation();
				jQuery('#adj-nav-price-from---price').val(parseInt(jQuery('#adj-nav-price-from---price').attr('placeholder').replace(/[^0-9-.]/g, '')));
				jQuery('#adj-nav-price-to---price').val(parseInt(jQuery('#adj-nav-price-to---price').attr('placeholder').replace(/[^0-9-.]/g, '')));
			});
        //}
    }
// finish new fix code

}
  
function adj_nav_create_slider(width, from, to, min_price, max_price, sKey) 
{
    if(!(isUpdating)){
		var price_slider = $('adj-nav-price-slider' + sKey);
	
		return new Control.Slider(price_slider.select('.handle'), price_slider, {
		  range: $R(0, width),
		  sliderValue: [from, to],
		  restricted: true,
		  
		  onChange: function (values){
			  /* Customized by Hitesh for exponential slider functionality */
			  //$('chromemenu').innerHTML = 'changed: ' + Math.round(Math.pow(max_price,values[1])-min_price);
			  /* Customization Finish */
			  
	//        var f = adj_nav_round(max_price*values[0]/width),
	//            t = adj_nav_round(max_price*values[1]/width);
			var f = adj_nav_calculate(width, from, to, min_price, max_price, values[0]),
				t = adj_nav_calculate(width, from, to, min_price, max_price, values[1]);
			   
	//        adj_nav_add_params('price', f + ',' + t, true);
			adj_nav_add_params(sKey, f + ',' + t, true);
			
			// we can change values without sliding  
			$('adj-nav-range-from' + sKey).update(f); 
			$('adj-nav-range-to' + sKey).update(t);
				
			/* Omniture price slider tracking */
			categoryNavFilterSelect('price|'+f+'-'+t);
				
			adj_nav_make_request();  
		  },
		  onSlide: function(values) { 
				/* Customized by Hitesh for exponential slider functionality */
				//$('chromemenu').innerHTML = 'slide: ' + Math.round(Math.pow(max_price,values[1])-min_price);
				/* Customization Finish */
				
	//          $('adj-nav-range-from' + sKey).update(adj_nav_round(max_price*values[0]/width));
	//          $('adj-nav-range-to' + sKey).update(adj_nav_round(max_price*values[1]/width));
			  $('adj-nav-range-from' + sKey).update(adj_nav_calculate(width, from, to, min_price, max_price, values[0]));
			  $('adj-nav-range-to' + sKey).update(adj_nav_calculate(width, from, to, min_price, max_price, values[1]));
		  }
		});
	}
}

function adj_nav_calculate(width, from, to, min_price, max_price, value)
{
	/* Customized by Hitesh for exponential slider functionality */
     //var calculated = adj_nav_round(((max_price-min_price)*value/width) + min_price);
	
	//scale = ((Math.log(max_price) - Math.log(min_price)) / ((width)))
	
	//var calculated =   Math.floor(min_price *  Math.exp( ((Math.log(max_price) - Math.log(min_price)) / ((width)))*(value - from)) );  
	var calculated =   Math.floor(min_price *  Math.exp( ((Math.log(max_price) - Math.log(min_price)) / ((width)))*(value)) );  
	
	if( calculated < 1000)
		calculated = calculated - ( calculated % 10);
	else if(calculated < 3000)
		calculated = calculated + (50 - calculated % 50);
	else
		calculated = calculated + (250 - calculated % 250);
    
	
    return calculated;
}