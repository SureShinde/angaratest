var OPC = Class.create();
OPC.prototype = {
    initialize: function (form, urls, agreement) {
        this.in_process = false;
        this.s_code = 'v2_2_20';
        this.acceptAgreementText = agreement;
        this.successUrl = check_secure_url(urls.success);
        this.saveUrl = check_secure_url(urls.save);
        this.updateUrl = check_secure_url(urls.update);
        this.failureUrl = check_secure_url(urls.failure);
        this.persistentUrl = check_secure_url(urls.persistent);
        this.form = form;
        this.loadWaiting = false;
        this.validator = new Validation(this.form);
        this.sectionsToValidate = [payment];
        if (typeof shippingMethod === 'object') {
            this.sectionsToValidate.push(shippingMethod);
        }
        this._addEventListeners();
    },
    _addEventListeners: function () {
        $('login-form') && $('login-form').observe('submit', function (e) {
            Event.stop(e);
            if (!loginForm.validator.validate()) {
                return;
            }
            $('login-please-wait').show();
            $('send2').setAttribute('disabled', 'disabled');
            $$('#login-form .buttons-set')[0].addClassName('disabled').setOpacity(0.5);
            new Ajax.Request($('login-form').action, {
                parameters: $('login-form').serialize(),
                onSuccess: function (transport) {
                    OPC.Messenger.clear('login-form');
                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        OPC.Messenger.add(response.error, 'login-form', 'error');
                    }
                    if (response.redirect) {
                        document.location = response.redirect;
                        return;
                    }
                    $('login-please-wait').hide();
                    $('send2').removeAttribute('disabled');
                    $$('#login-form .buttons-set')[0].removeClassName('disabled').setOpacity(1);
                }
            })
        });
        $('forgot-password-form') && $('forgot-password-form').observe('submit', function (e) {
            Event.stop(e);
            if (!forgotForm.validator.validate()) {
                return;
            }
            $('forgot-please-wait').show();
            $('btn-forgot').setAttribute('disabled', 'disabled');
            $$('#forgot-password-form .buttons-set')[0].addClassName('disabled').setOpacity(0.5);
            new Ajax.Request($('forgot-password-form').action, {
                parameters: $('forgot-password-form').serialize(),
                onSuccess: function (transport) {
                    OPC.Messenger.clear('forgot-password-form');
                    $('forgot-please-wait').hide();
                    $('btn-forgot').removeAttribute('disabled');
                    $$('#forgot-password-form .buttons-set')[0].removeClassName('disabled').setOpacity(1);
                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        OPC.Messenger.add(response.error, 'forgot-password-form', 'error');
                    } else if (response.message) {
                        open_login();
                        OPC.Messenger.clear('login-form');
                        OPC.Messenger.add(response.message, 'login-form', 'success');
                    }
                }
            })
        })
    },
    ajaxFailure: function () {
        location.href = this.failureUrl;
    },
    _disableEnableAll: function (element, isDisabled) {
        var descendants = element.descendants();
        for (var k in descendants) {
            descendants[k].disabled = isDisabled;
        }
        element.disabled = isDisabled;
    },
    setLoadWaiting: function (flag) {
        if (flag) {
			var containerBottom = $('review-buttons-container-bottom');
			containerBottom.addClassName('disabled');
			containerBottom.setStyle({
				opacity: 0.5
			});
			this._disableEnableAll(containerBottom, true);
		
			var container = $('review-buttons-container');
			container.addClassName('disabled');
			container.setStyle({
				opacity: 0.5
			});
			this._disableEnableAll(container, true);
			
        } else if (this.loadWaiting) {
			
			var containerBottom = $('review-buttons-container-bottom');
			containerBottom.removeClassName('disabled');
			containerBottom.setStyle({
				opacity: 1
			});
			this._disableEnableAll(containerBottom, false);
		
			var container = $('review-buttons-container');
			container.removeClassName('disabled');
			container.setStyle({
				opacity: 1
			});
			this._disableEnableAll(container, false);
        }
        this.loadWaiting = flag;
    },
    save: function (bottomPos) {
        if (this.loadWaiting != false) {
            return;
        }
		
        var isValid = true;
        if (!this.validator.validate()) {
            isValid = false;
        }
        for (i in this.sectionsToValidate) {
            if (typeof this.sectionsToValidate[i] === 'function') {
                continue;
            }
            if (!this.sectionsToValidate[i].validate()) {
                isValid = false;
            }
        }
        OPC.Messenger.clear('checkout-review-submit');
        $$('#checkout-review-submit .checkout-agreements input[type="checkbox"]').each(function (el) {
            if (!el.checked) {
                OPC.Messenger.add(this.acceptAgreementText, 'checkout-review-submit', 'error');
                isValid = false;
                throw $break
            }
        }.bind(this));
        if (!isValid) {
            var validationMessages = $$('.validation-advice, .messages').findAll(function (el) {
                return el.visible();
            });
            if (!validationMessages.length) {
                return;
            }
            var viewportSize = document.viewport.getDimensions();
            var hiddenMessages = [];
            var needToScroll = true;
            validationMessages.each(function (el) {
                var offset = el.viewportOffset();
                if (offset.top < 0 || offset.top > viewportSize.height || offset.left < 0 || offset.left > viewportSize.width) {
                    hiddenMessages.push(el);
                } else {
                    needToScroll = false;
                }
            });
            if (needToScroll) {
                Effect.ScrollTo(validationMessages[0], {
                    duration: 1,
                    offset: -20
                });
            }
            return;
        }
        checkout.setLoadWaiting(true);
        var params = Form.serialize(this.form);
        
		if(bottomPos == 'bottom'){
			$('review-please-wait-bottom').show();
		}else{
			$('review-please-wait').show();
		}
		
		var request = new Ajax.Request(this.saveUrl, {
            method: 'post',
            parameters: params,
            onSuccess: this.setResponse.bind(this),
            onFailure: this.ajaxFailure.bind(this)
        });
    },
    update: function (params) { // need to use timeout for chrome autocomplete
        setTimeout(function(){
        	checkout.real_update(params);   
        },1000);
    },    
    real_update: function (params) {
        if (this.loadWaiting != false) {
            return;
        }
        var parameters = $(this.form).serialize(true);
        for (var i in params) {
            if (!params[i]) {
                continue;
            }
            var obj = $('checkout-' + i + '-load');
            if (obj != null) {
                var size = obj.getDimensions();
                obj.setStyle({
                    'width': size.width + 'px',
                    'height': size.height + 'px'
                }).addClassName('loading').addClassName('disabled');
				jQuery('#checkout-' + i).find('.loading-indicator').removeClass('hide').show();
                parameters[i] = params[i];
            }
// special rule if payment method changed
            if(i=='payment-changed')
            	parameters[i] = params[i];
//
        }
        checkout.setLoadWaiting(true);
		
        var request = new Ajax.Request(this.updateUrl, {
            method: 'post',
            onSuccess: this.setResponse.bind(this),
            onFailure: this.ajaxFailure.bind(this),
            parameters: parameters
        });
    },
    setResponse: function (response) {
        response = response.responseText.evalJSON();
        if (response.redirect) {
            location.href = check_secure_url(response.redirect);
            return true;
        }
        checkout.setLoadWaiting(false);

        if (response.order_created) {
            $('onepagecheckout_orderform').action = this.successUrl;
            $('opc_submit_form').click();
            return true;
        } else if (response.error_messages) {
            var msg = response.error_messages;
            if (typeof (msg) == 'object') {
                msg = msg.join("\n");
            }
            alert(msg);
        }
		
        $('review-please-wait').hide();
        $('review-please-wait-bottom').hide();
		
        if (response.update_section) {
            for (var i in response.update_section) {
                ch_obj = $('checkout-' + i + '-load');
                if (ch_obj != null) {
                    ch_obj.setStyle({
                        'width': 'auto',
                        'height': 'auto'
                    }).update(response.update_section[i]).setOpacity(1).removeClassName('loading').removeClassName('disabled') ;
					jQuery('#checkout-' + i).find('.loading-indicator').addClass('hide').hide();
                    if (i === 'shipping-method') {
                        shippingMethod.addObservers();
                    }
                }
            }
        }
        if (response.duplicateBillingInfo) {
            /* shipping.syncWithBilling(); */
        }
        if (response.reload_totals) {
            checkout.update({
                'review': 1
            });
        }
        
        if (response.not_valid_address || response.billing_valid || response.shipping_valid){        	
        	show_verifications_error();
        }
        
        return false;
    },
};
var BillingAddress = Class.create();
BillingAddress.prototype = {

    initialize: function () {
		 //	S:VA
		 var previousCountry;
		 $('billing:country_id') && $('billing:country_id').observe('focus', function () {
			 previousCountry = $(this).value;	
		 })
		 
        $('billing:country_id') && $('billing:country_id').observe('change', function () {
			Event.stopObserving('billing:country_id', 'focus');		//	S:VA
				
            if ($('billing:region_id')) {
                function resetRegionId() {
                    $('billing:region_id').value = '';
                    $('billing:region_id')[0].selected = true;
                }
                resetRegionId.delay(0.2)
            }
            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked) {
                shipping.syncWithBilling();
            }
			
			//	S:VA
			//var country = $(this).value;
			if(previousCountry=='US'){
				jQuery('.validate-zip-shipping').val('');
			}
			//	E:VA
			
            checkout.update({
                //'payment-method': 1,
                'shipping-method': !$('shipping:same_as_billing') || $('shipping:same_as_billing').checked ? 1 : 0
            });
        });
        $('billing_customer_address') && $('billing_customer_address').observe('change', function () {
            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked) {
                shipping.syncWithBilling();
            }
            checkout.update({
                //'payment-method': 1,
                'shipping-method': !$('shipping:same_as_billing') || $('shipping:same_as_billing').checked ? 1 : 0
            });
        });
        $('billing:region_id') && $('billing:region_id').observe('change', function () {
            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked) {
                shipping.syncWithBilling();
                checkout.update({
                    'review': 1
                });
            } else if (!$('shipping:same_as_billing')) {
                checkout.update({
                    'review': 1
                });
            }
        });
        $('billing:postcode') && $('billing:postcode').observe('change', function () {
            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked) {
                shipping.syncWithBilling();
                checkout.update({
                    'review': 1
                });
            } else if (!$('shipping:same_as_billing')) {
                checkout.update({
                    'review': 1
                });
            }
        })
    },
    newAddress: function (isNew) {
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('bill_form');
        } else {
            Element.hide('bill_form');
        }
    },
    resetSelectedAddress: function () {
        var selectElement = $('billing_customer_address');
        if (selectElement) {
            selectElement.value = '';
        }
    },
    setCreateAccount: function (flag) {
        if (flag) {
            $('register-customer-password').show();
        } else {
            $('register-customer-password').hide();
        }
    }
};
var ShippingAddress = Class.create();
ShippingAddress.prototype = {
    initialize: function (form) {
        this.form = form;
        $('shipping:country_id') && $('shipping:country_id').observe('change', function () {
            if ($('shipping:region_id')) {
                $('shipping:region_id').value = '';
                $('shipping:region_id')[0].selected = true;
            }
            checkout.update({
                'shipping-method': 1
            });
        });
        $('shipping_customer_address') && $('shipping_customer_address').observe('change', function () {
            checkout.update({
                'shipping-method': 1
            });
        });
        $('shipping:region_id') && $('shipping:region_id').observe('change', function () {
            checkout.update({
                'review': 1
            });
        });
        $('shipping:postcode') && $('shipping:postcode').observe('change', function () {
            checkout.update({
                'review': 1
            });
        });
    },
    newAddress: function (isNew) {
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('ship_form');
        } else {
            Element.hide('ship_form');
        }
    },
    resetSelectedAddress: function () {
        var selectElement = $('shipping_customer_address');
        if (selectElement) {
            selectElement.value = '';
        }
    },
    setSameAsBilling: function (flag) {
        $('shipping:same_as_billing').checked = flag;
        $('billing:use_for_shipping').value = flag ? 1 : 0;
        if (flag) {
            $('ship_address_block').hide();
            this.syncWithBilling();
            checkout.update({
                'shipping-method': 1
            });
        } else {
            $('ship_address_block').show();
        }
    },
    syncWithBilling: function () {
		return;
        $('billing_customer_address') && this.newAddress(!$('billing_customer_address').value);
        $('shipping:same_as_billing').checked = true;
        $('billing:use_for_shipping').value = 1;
        if (!$('billing_customer_address') || !$('billing_customer_address').value) {
            arrElements = Form.getElements(this.form);
            for (var elemIndex in arrElements) {
                if (arrElements[elemIndex].id) {
                    var sourceField = $(arrElements[elemIndex].id.replace(/^shipping:/, 'billing:'));
                    if (sourceField) {
                        arrElements[elemIndex].value = sourceField.value;
                    }
                }
            }
            shippingRegionUpdater.update();
            $('shipping:region_id').value = $('billing:region_id').value;
            $('shipping:region').value = $('billing:region').value;
        } else {
            $('shipping_customer_address').value = $('billing_customer_address').value;
        }
    },
    setRegionValue: function () {
        $('shipping:region').value = $('billing:region').value;
    }
};
var ShippingMethod = Class.create();
ShippingMethod.prototype = {
    initialize: function () {
        this.addObservers();
    },
    addObservers: function () {
        $$('input[name="shipping_method"]').each(function (el) {
            el.observe('click', function () {
                checkout.update({
                    'review': 1
                });
            });
        });
    },
    validate: function () {
        OPC.Messenger.clear('checkout-shipping-method-load');
        var methods = document.getElementsByName('shipping_method');
        if (methods.length == 0) {
            OPC.Messenger.add(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make neccessary changes in your shipping address.'), 'checkout-shipping-method-load', 'error');
            return false;
        }
        for (var i = 0; i < methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        OPC.Messenger.add(Translator.translate('Please specify shipping method.'), 'checkout-shipping-method-load', 'error');
        return false;
    }
};
var Payment = Class.create();
Payment.prototype = {
    beforeInitFunc: $H({}),
    afterInitFunc: $H({}),
    beforeValidateFunc: $H({}),
    afterValidateFunc: $H({}),
    initialize: function (container) {
        this.cnt = container;
    },
    // special rule if payment method changed
    addObservers: function () {
    	$$('input[name="payment[method]"]').each(function (el) {
          el.observe('click', function () {
              checkout.update({
                  'review': 1,
                  'payment-changed': 1
              });
          });
      });
    },  
/////////////
    addBeforeInitFunction: function (code, func) {
        this.beforeInitFunc.set(code, func);
    },
    beforeInit: function () {
        (this.beforeInitFunc).each(function (init) {
            (init.value)();
        });
    },
    init: function () {
        this.beforeInit();
        var method = null;
        var elements = $(this.cnt).select('input');
        for (var i = 0; i < elements.length; i++) {
            if (elements[i].name == 'payment[method]') {
                if (elements[i].checked) method = elements[i].value;
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete', 'off');
        }
        elements = $(this.cnt).select('select');
        for (var i = 0; i < elements.length; i++) {
            if (elements[i].name == 'payment[method]') {
                if (elements[i].checked) method = elements[i].value;
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete', 'off');
        }
        elements = $(this.cnt).select('textarea');
        for (var i = 0; i < elements.length; i++) {
            if (elements[i].name == 'payment[method]') {
                if (elements[i].checked) method = elements[i].value;
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete', 'off');
        }
        if (method) this.switchMethod(method);
        this.afterInit();
        this.initWhatIsCvvListeners();
// init special rule if payment method changed        
        this.addObservers();        
    },
    addAfterInitFunction: function (code, func) {
        this.afterInitFunc.set(code, func);
    },
    afterInit: function () {
        (this.afterInitFunc).each(function (init) {
            (init.value)();
        });
    },
    switchMethod: function (method) {
        if (this.currentMethod && $('payment_form_' + this.currentMethod)) {
            var form = $('payment_form_' + this.currentMethod);
            form.style.display = 'none';
            var elements = form.select('input');
            for (var i = 0; i < elements.length; i++) elements[i].disabled = true;
            elements = form.select('select');
            for (var i = 0; i < elements.length; i++) elements[i].disabled = true;
            elements = form.select('textarea');
            for (var i = 0; i < elements.length; i++) elements[i].disabled = true;
        }
        if ($('payment_form_' + method)) {
            var form = $('payment_form_' + method);
            form.style.display = '';
            var elements = form.select('input');
            for (var i = 0; i < elements.length; i++) elements[i].disabled = false;
            elements = form.select('select');
            for (var i = 0; i < elements.length; i++) elements[i].disabled = false;
            elements = form.select('textarea');
            for (var i = 0; i < elements.length; i++) elements[i].disabled = false;
        } else {
            document.body.fire('payment-method:switched', {
                method_code: method
            });
        }
        this.currentMethod = method;
    },
    addBeforeValidateFunction: function (code, func) {
        this.beforeValidateFunc.set(code, func);
    },
    beforeValidate: function () {
        var validateResult = true;
        var hasValidation = false;
        (this.beforeValidateFunc).each(function (validate) {
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },
    validate: function () {
        OPC.Messenger.clear('checkout-payment-method-load');
        var result = this.beforeValidate();
        if (result) {
            return true;
        }
        var methods = document.getElementsByName('payment[method]');
        if (methods.length == 0) {
            OPC.Messenger.add(Translator.translate('Your order cannot be completed at this time as there is no payment methods available for it.'), 'checkout-payment-method-load', 'error');
            return false;
        }
        for (var i = 0; i < methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        result = this.afterValidate();
        if (result) {
            return true;
        }
        OPC.Messenger.add(Translator.translate('Please specify payment method.'), 'checkout-payment-method-load', 'error');
        return false;
    },
    addAfterValidateFunction: function (code, func) {
        this.afterValidateFunc.set(code, func);
    },
    afterValidate: function () {
        var validateResult = true;
        var hasValidation = false;
        (this.afterValidateFunc).each(function (validate) {
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },
    initWhatIsCvvListeners: function () {
        $$('.cvv-what-is-this').each(function (element) {
            Event.observe(element, 'click', toggleToolTip);
        });
    }
};
OPC.Messenger = {
    add: function (message, section, type) {
        var s_obj = $(section);
        if (s_obj != null) {
            var ul = $(section).select('.messages')[0];
            if (!ul) {
                $(section).insert({
                    top: '<ul class="messages"></ul>'
                });
                ul = $(section).select('.messages')[0];
            }
            var li = $(ul).select('.' + type + '-msg')[0];
            if (!li) {
                $(ul).insert({
                    top: '<li class="' + type + '-msg"><ul></ul></li>'
                });
                li = $(ul).select('.' + type + '-msg')[0];
            }
            $(li).select('ul')[0].insert('<li>' + message + '</li>');
        }
    },
    clear: function (section) {
        var s_obj = $(section);
        if (s_obj != null) {
            var ul = $(section).select('.messages')[0];
            if (ul) {
                ul.remove();
            }
        }
    }
};
OPC.Window = Class.create();
OPC.Window.prototype = {
    initialize: function (config) {
        this.config = Object.extend({
            width: 'auto',
            height: 'auto',
            maxWidth: 500,
            maxHeight: 400,
            triggers: null,
            markup: '<div class="d-shadow-wrap">' + '<div class="content"></div>' + '<div class="d-sh-cn d-sh-tl"></div><div class="d-sh-cn d-sh-tr"></div>' + '</div>' + '<div class="d-sh-cn d-sh-bl"></div><div class="d-sh-cn d-sh-br"></div>' + '<a href="javascript:void(0)" class="close"></a>'
        }, config || {});
        this._prepareMarkup();
        this._attachEventListeners();
    },
    show: function () {
        if (!this.centered) {
            this.center();
        }
        $$('select').invoke('addClassName', 'onepagecheckout-hidden');
        this.window.show();
    },
    hide: function () {
        this.window.hide();
        $$('select').invoke('removeClassName', 'onepagecheckout-hidden');
    },
    update: function (content) {
        this.content.setStyle({
            width: isNaN(this.config.width) ? this.config.width : this.config.width + 'px',
            height: isNaN(this.config.height) ? this.config.height : this.config.height + 'px'
        });
        this.content.update(content);
        this.updateSize();
        this.center();
        return this;
    },
    center: function () {
        var viewportSize = document.viewport.getDimensions();
        var viewportOffset = document.viewport.getScrollOffsets();
        this.setPosition(viewportSize.width / 2 - this.window.getWidth() / 2 + viewportOffset.left, viewportSize.height / 2 - this.window.getHeight() / 2 + viewportOffset.top);
        this.centered = true;
    },
    setPosition: function (x, y) {
        this.window.setStyle({
            left: x + 'px',
            top: y + 'px'
        });
    },
    activate: function (trigger) {
        this.update(this.config.triggers[trigger].window.show()).show();
    },
    updateSize: function () {
        this.window.setStyle({
            visibility: 'hidden'
        }).show();
        var size = this.content.getDimensions();
        if ('auto' === this.config.width && size.width > this.config.maxWidth) {
            this.content.setStyle({
                width: this.config.maxWidth + 'px'
            });
        }
        if ('auto' === this.config.height && size.height > this.config.maxHeight) {
            this.content.setStyle({
                height: this.config.maxHeight + 'px'
            });
        }
        this.window.hide().setStyle({
            visibility: 'visible'
        });
    },
    _prepareMarkup: function () {
        this.window = new Element('div');
        this.window.addClassName('onepagecheckout-window');
        this.window.update(this.config.markup).hide();
        this.content = this.window.select('.content')[0];
        this.close = this.window.select('.close')[0];
        $(document.body).insert(this.window);
    },
    _attachEventListeners: function () {
        this.close.observe('click', this.hide.bind(this));
        document.observe('keypress', this._onKeyPress.bind(this));
        if (this.config.triggers) {
            if (this.config.triggers.length != 0) {
                for (var i in this.config.triggers) {
                    this.config.triggers[i].el.each(function (el) {
                        var trigger = this.config.triggers[i];
                        el.observe(this.config.triggers[i].event, function (e) {
                            Event.stop(e);
                            if (!trigger.window) {
                                return;
                            }
                            var oldContent = this.content.down();
                            oldContent && $(document.body).insert(oldContent.hide());
                            this.update(trigger.window.show()).show();
                        }.bind(this));
                    }.bind(this));
                }
            }
        }
    },
    _onKeyPress: function (e) {
        var code = e.keyCode;
        if (code == Event.KEY_ESC) {
            this.hide();
        }
    }
};

function setOpcHeight(){
	var l = jQuery('.op-checkout-left-box').height();
	var r = jQuery('.op-checkout-right-box').height();
	if(l<r){ 
		jQuery('.op-checkout-left-box').css('min-height',r+'px'); 
	}
}

function open_login() {
    $('onepagecheckout_forgotbox').hide();
    $('onepagecheckout_loginbox').show();
	jQuery('#onepagecheckout_login-container').removeClass('hidden');
}

function open_forgot() {
    $('onepagecheckout_loginbox').hide();
    $('onepagecheckout_forgotbox').show();
	jQuery('#checkout-text').addClass('hidden');
}

function close_login() {
    $('onepagecheckout_forgotbox').hide();
    $('onepagecheckout_loginbox').hide();
	jQuery('#onepagecheckout_login-container').addClass('hidden');
	jQuery('#onepagecheckout_login-container').removeClass('showcase-bg-dark max-margin-bottom showcase-border-bottom').prev().addClass('max-margin-bottom');
}

function check_secure_url(url) {
	if(url != '')
	{
	    if (http_type == 'https') {
	        var u1 = url.substr(0, 5);
	        if (u1 != 'https') {
	            if (u1 == 'http:') url = 'https:' + url.substr(5);
	            else url = 'https://' + url;
	        }
	    }
	}
    return url;
}

	
function continue_verification()
{
	var va_bill_exist = false;
	var va_ship_exist = false;
	
	var va_bill_id = -1;
    $$('.va_bill_id').each(function(el){
    	va_bill_exist = true; // check if exist any radiobox
        if (el.checked)
        	va_bill_id = el.value; // get checked value 
    });

	var va_ship_id = -1;
    $$('.va_ship_id').each(function(el){
    	va_ship_exist = true; // check if exist any radiobox
        if (el.checked)
        	va_ship_id = el.value; // get checked value
    });

    // check if user made all choises
    if(va_bill_exist && va_bill_id == -1)
    {
    	alert('Please, make your choice');
    	return false;
    }

    if(va_ship_exist && va_ship_id == -1)
    {
    	alert('Please, make your choice');
    	return false;
    }
    //
    
	close_verification();

	if(va_bill_id != -1 && va_bill_id != 'use_original_address')
	{
		copy_valid_address('billing', va_bill_id);
		
		var obj =$('billing_customer_address');
		if(obj != null && obj != undefined && typeof(obj) != 'undefined')
		{
			if(obj.value != '') // customer has address, new to open new form
			{
				billing.newAddress(true);
			}
		}
	}

	if(va_ship_id != -1  && va_ship_id != 'use_original_address')
	{
		copy_valid_address('shipping', va_ship_id);
		
		var obj =$('shipping_customer_address');
		if(obj != null && obj != undefined && typeof(obj) != 'undefined')
		{
			if(obj.value != '') // customer has address, new to open new form
			{
				shipping.newAddress(true);
			}
		}
	}
	
	// update 
	var ship_updated = false;
	if(va_bill_id != -1  && va_bill_id != 'use_original_address')
	{
	    if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked)
	    	shipping.syncWithBilling();
	    
    	ship_updated = true;
        checkout.update({
            'review': 1
        });
	}
	
	if(!ship_updated)
	{
		if(va_ship_id != -1 && va_ship_id != 'use_original_address')
		{
		    checkout.update({
		        'review': 1
		    });
		}
	}
}

function copy_valid_address(type, candidate_id)
{
	if(candidate_id != '')
		candidate_id = '_'+candidate_id;

	$(type+':street1').value	= $('va_'+type+'_street'+candidate_id).value;
	$(type+':city').value		= $('va_'+type+'_city'+candidate_id).value;
	$(type+':region_id').value	= $('va_'+type+'_region'+candidate_id).value;
	$(type+':postcode').value	= $('va_'+type+'_postcode'+candidate_id).value;	
}



function getClientWidth() {
    return document.compatMode == 'CSS1Compat' && !window.opera ? document.documentElement.clientWidth : document.body.clientWidth;
}
document.getElementsByClassName = function (cl) {
    var retnode = [];
    var myclass = new RegExp('\\b' + cl + '\\b');
    var elem = this.getElementsByTagName('*');
    for (var i = 0; i < elem.length; i++) {
        var classes = elem[i].className;
        if (myclass.test(classes)) retnode.push(elem[i]);
    }
    return retnode;
};

window.onload = function () {
    if(checkout.persistentUrl != '')
    {
    	location.href = checkout.persistentUrl
    }
    else
    {
	    /*checkout.update({
	        //'payment-method': 1,
	        'shipping-method': 1,
	        'review': 1
	    });*/
    }
};

jQuery(function(){
	/* jQuery('.has-subform input, .has-subform select, .has-subform button').focus(function(){
		var current_subform_index = jQuery(this).parents('.has-subform').data('subform-index');
		
		jQuery('.has-subform').removeClass('padding-type-8 showcase-border-thick-green').addClass('padding-type-10');
		jQuery(this).parents('.has-subform').addClass('padding-type-8 showcase-border-thick-green');
	}) */
	
	jQuery('#onepagecheckout_login-close').click(function(){
		close_login();
	});
})


/* Google map address suggestion on checkout address page */
// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
/* Start:Asheesh */
	var placeSearch, autocomplete, autocomplete2, enableToggle = true;
	var componentForm = {
	street_number: 'short_name',
	route: 'long_name',
	locality: 'long_name',
	sublocality_level_1: 'long_name',
	sublocality_level_2: 'long_name',
	administrative_area_level_1: 'long_name',
	administrative_area_level_2: 'long_name',
	administrative_area_level_3: 'long_name',
	country: 'short_name',
	postal_code: 'short_name'
	};

	function initAutocomplete() {
		// Create the autocomplete object, restricting the search to geographical
		// location types.
		if(isMobile) {
			if(isMobile != 'us') {
				googletoggle('block');
				googletoggleShipping('block');
				enableToggle = false;
				return;
			} else {
				var stretAddress1 = document.getElementById('shipping:street1');
				var stretAddressBilling1 = document.getElementById('billing:street1');
				
				if(stretAddress1 && stretAddress1.value) {
					googletoggle('block');
				}
				
				if(stretAddressBilling1 && stretAddressBilling1.value) {
					googletoggleShipping('block');
				}
			}
			autocomplete = new google.maps.places.Autocomplete(
				/** @type {!HTMLInputElement} */(document.getElementById('shipping:street1')),
				{types: ['geocode'],
				componentRestrictions: {country: isMobile}
			});
			autocomplete.addListener('place_changed', fillInAddress);
			
			autocomplete2 = new google.maps.places.Autocomplete(
				/** @type {!HTMLInputElement} */(document.getElementById('billing:street1')),
				{types: ['geocode'],
				componentRestrictions: {country: isMobile}
			});
			autocomplete2.addListener('place_changed', fillInAddressShipping);
		}
	}
	
	function fillInAddress() {
		// Get the place details from the autocomplete object.
		var place = autocomplete.getPlace();

		for (var component in componentForm) {
		  /* document.getElementById(component).value = '';
		  document.getElementById(component).disabled = false; */
			if(component == 'street_number') {
				document.getElementById('shipping:street1').value = '';
			} else if(component == 'route') {
				document.getElementById('shipping:street1').value = '';
			} else if(component == 'locality') {
				document.getElementById('shipping:city').value = '';
			} else if(component == 'administrative_area_level_1') {
				objSelect = document.getElementById('shipping:region_id')
				setSelectedValue(objSelect, '');
			} else if(component == 'country') {
				objSelect = document.getElementById('shipping:country_id')
				//setSelectedValue(objSelect, '');
			} else if (component == 'postal_code') {
				document.getElementById('shipping:postcode').value = '';
			}
		}

		// Get each component of the address from the place details
		// and fill the corresponding field on the form.
		var stateL = '';
		var cityLocality = '';
		var streetValue1 = '';
		var streetValue2 = '';
		
		if(place.address_components){
			for (var i = 0; i < place.address_components.length; i++) {
			  var addressType = place.address_components[i].types[0];
			  if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				if(addressType == 'street_number') {
					streetValue1 = val;
				} else if(addressType == 'route') {
					streetValue2 = val;
				} else if(addressType == 'locality') {
					if(val)
						cityLocality = val;
				} else if(addressType == 'sublocality_level_1') {
					if(val)
						cityLocality = val;
				} else if(addressType == 'administrative_area_level_1') {
					objSelect = document.getElementById('shipping:region_id');
					if(objSelect)
						setSelectedValue(objSelect, val);
					objSelectText = document.getElementById('shipping:region');
					if(objSelectText)
						objSelectText.value = val;
					stateL = val;
				} else if(addressType == 'country') {
					objSelect = document.getElementById('shipping:country_id');
					if(objSelect)
						setSelectedValue(objSelect, val);
					/* alert('Country:'+val); */
					if(stateL) {
						/* alert("State:"+stateL); */
						objSelect = document.getElementById('shipping:region_id');
						if(objSelect)
							setSelectedValue(objSelect, stateL);
						objSelectText = document.getElementById('shipping:region');
						if(objSelectText)
							objSelectText.value = stateL;
					}
				} else if (addressType == 'postal_code') {
					document.getElementById('shipping:postcode').value = val;
				} else {
					/* alert(addressType+'=='+val); */
				}
				/* switch(addressType) {
					case 'street_number'; document.getElementById('shipping:street1').value = val; break;
					case 'route'; document.getElementById('shipping:street2').value = val; break;
					case 'locality'; document.getElementById('shipping:city').value = val; break;
				} */
				/* document.getElementById(addressType).value = val; */
			  } else {
				/* alert(addressType); */
			  }
			}
		}	
		document.getElementById('shipping:street1').value = streetValue1 + ' '+ streetValue2;
		document.getElementById('shipping:city').value = cityLocality;
		googletoggle('block');
	}
	
	function fillInAddressShipping() {
		// Get the place details from the autocomplete object.
		var place2 = autocomplete2.getPlace();

		for (var component in componentForm) {
		  /* document.getElementById(component).value = '';
		  document.getElementById(component).disabled = false; */
			if(component == 'street_number') {
				document.getElementById('billing:street1').value = '';
			} else if(component == 'route') {
				document.getElementById('billing:street1').value = '';
			} else if(component == 'locality') {
				document.getElementById('billing:city').value = '';
			} else if(component == 'administrative_area_level_1') {
				objSelect = document.getElementById('billing:region_id')
				setSelectedValue(objSelect, '');
			} else if(component == 'country') {
				objSelect = document.getElementById('billing:country_id')
				//setSelectedValue(objSelect, '');
			} else if (component == 'postal_code') {
				document.getElementById('billing:postcode').value = '';
			}
		}

		// Get each component of the address from the place details
		// and fill the corresponding field on the form.
		var stateL = '';
		var cityLocality = '';
		var streetValue1 = '';
		var streetValue2 = '';
		
		if(place2.address_components){
			for (var i = 0; i < place2.address_components.length; i++) {
			  var addressType = place2.address_components[i].types[0];
			  if (componentForm[addressType]) {
				var val = place2.address_components[i][componentForm[addressType]];
				if(addressType == 'street_number') {
					streetValue1 = val;
				} else if(addressType == 'route') {
					streetValue2 = val;
				} else if(addressType == 'locality') {
					if(val)
						cityLocality = val;
				} else if(addressType == 'sublocality_level_1') {
					if(val)
						cityLocality = val;
				} else if(addressType == 'administrative_area_level_1') {
					objSelect = document.getElementById('billing:region_id');
					if(objSelect)
						setSelectedValue(objSelect, val);
					objSelectText = document.getElementById('billing:region');
					if(objSelectText)
						objSelectText.value = val;
					stateL = val;
				} else if(addressType == 'country') {
					objSelect = document.getElementById('billing:country_id');
					if(objSelect)
						setSelectedValue(objSelect, val);
					/* alert('Country:'+val); */
					if(stateL) {						
						objSelect = document.getElementById('billing:region_id');
						if(objSelect) {
							setTimeout(function(){ setSelectedValue(objSelect, stateL); },200);
							//alert("State:"+stateL);
						}
						objSelectText = document.getElementById('billing:region');
						if(objSelectText)
							objSelectText.value = stateL;
					}
				} else if (addressType == 'postal_code') {
					document.getElementById('billing:postcode').value = val;
				} else {
					/* alert(addressType+'=='+val); */
				}
				/* switch(addressType) {
					case 'street_number'; document.getElementById('billing:street1').value = val; break;
					case 'route'; document.getElementById('billing:street2').value = val; break;
					case 'locality'; document.getElementById('billing:city').value = val; break;
				} */
				/* document.getElementById(addressType).value = val; */
			  } else {
				/* alert(addressType); */
			  }
			}
		}	
		document.getElementById('billing:street1').value = streetValue1 + ' '+ streetValue2;
		document.getElementById('billing:city').value = cityLocality;
		googletoggleShipping('block');
	}

	function setSelectedValue(selectObj, valueToSet) {
		for (var i = 0; i < selectObj.options.length; i++) {
			if (selectObj.options[i].value== valueToSet || selectObj.options[i].text== valueToSet) {
				selectObj.options[i].selected = true;
				if ("createEvent" in document) {
					var evt = document.createEvent("HTMLEvents");
					evt.initEvent("change", false, true);
					selectObj.dispatchEvent(evt);
				}
				else
					selectObj.fireEvent("onchange");
				return;
			}
		}
	}
	
	function googletoggle(displayState){
		if(enableToggle) {
			var elements = document.getElementsByClassName('google-suggestion');
			if(elements){
				for (var i = 0; i < elements.length; i++){
					elements[i].style.display = displayState;
				}
			}	
		}
	}

	function googletoggleShipping(displayState){
		if(enableToggle) {
			var elements = document.getElementsByClassName('google-suggestion-shipping');
			if(elements){
				for (var i = 0; i < elements.length; i++){
					elements[i].style.display = displayState;
				}
			}	
		}
	}
/* End:Asheesh */
function cardBraintreeValidate()
    {
        //alert(str);
        var obj2= new vZero();
        if(obj2.cardValidate().length>0)
        {
            var str=obj2.cardValidate();
            str = str.split('+');
            for (var i = 0; i < str.length; i++) 
            { 
                if(str[i]=='card-number')
                {
                    jQuery('#bt_cardnumber').html('Please enter a valid credit card number.');
                    jQuery('#bt_cardnumber').css("color","red");
                    
                }
               
                if(str[i]=='expiration-month')
                {
                    jQuery('#bt_expmonth').html('Please enter a valid expiration date.');
                   jQuery('#bt_expmonth').css("color","red");
                   
                }
                
                
                
                if(str[i]=='cvv')
                {
                   jQuery('#bt_cvv').html('This is a required field.');
                   jQuery('#bt_cvv').css("color","red");
                   
                }
           }
           return 0;    
        }
        else
            return 1;
            
    }
function cardBraintreeValidate2(str)
    {
        var str = str.split('+');
        for (var i = 0; i < str.length; i++) 
        { 
            if(str[i]=='card-number')
            {
                jQuery('#bt_cardnumber').html('');
            }
           
            if(str[i]=='expiration-month')
            {
                jQuery('#bt_expmonth').html('');
            }
            
            if(str[i]=='cvv')
            {
               jQuery('#bt_cvv').html('');
            }
       }
            
    }