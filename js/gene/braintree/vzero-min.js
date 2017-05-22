var braintree_cards='';
var validate_cardnumber=0;
var validate_month=0;
var validate_date=0;
var validate_year=0;
var validate_cvv=0;
var card_values_invalid='';
var vZero = Class.create();
vZero.prototype = {

        initialize: function(e, t, i, n, o, s, a, r, d) {
            this.code = e, this.clientToken = t, this.threeDSecure = i, this.hostedFields = n, o && (this.billingName = o), s && (this.billingPostcode = s), a && (this.quoteUrl = a), r && (this.tokenizeUrl = r), this._hostedFieldsTokenGenerated = !1, this.acceptedCards = !1, this._hostedFieldsTimeout = !1, this._updateDataXhr = !1, this._updateDataCallbacks = [], this._updateDataParams = {}, this.client = !1, this.initEvents()
        },
        initEvents: function() {
            this.events = {
                onBeforeUpdateData: [],
                onAfterUpdateData: [],
                onHandleAjaxRequest: [],
                integration: {
                    onInit: [],
                    onInitDefaultMethod: [],
                    onShowHideOtherMethod: [],
                    onCheckSavedOther: [],
                    onPaymentMethodSwitch: [],
                    onReviewInit: [],
                    onBeforeSubmit: [],
                    onAfterSubmit: [],
                    onObserveAjaxRequests: []
                }
            }
        },
        observeEvent: function(e, t, i) {
            var n = this._resolveEvent(e);
            void 0 === n ? console.warn("Event for " + e + " does not exist.") : n.push({
                fn: t,
                params: i
            })
        },
        fireEvent: function(e, t, i) {
            var n = this._resolveEvent(t);
            void 0 !== n && n.length > 0 && n.each(function(t) {
                if ("function" == typeof t.fn) {
                    var arguments = [i];
                    "object" == typeof t.params && arguments.push(t.params), t.fn.apply(e, arguments)
                }
            })
        },
        _resolveEvent: function(e) {
            return e.split(".").reduce(function(e, t) {
                return e ? e[t] : void 0
            }, this.events)
        },
        getClient: function(e) {
            this.client !== !1 ? "function" == typeof e && e(this.client) : braintree.client.create({
                authorization: this.clientToken
            }, function(t, i) {
                return t ? void console.log(t) : (this.client = i, void e(this.client))
            }.bind(this))
        },
        initHostedFields: function(e) {
            return $$('iframe[name^="braintree-"]').length > 0 ? !1 : null === $("braintree-hosted-submit") ? !1 : (this.integration = e, this._hostedFieldsTokenGenerated = !1, clearTimeout(this._hostedFieldsTimeout), void(this._hostedFieldsTimeout = setTimeout(function() {
                if (this._hostedIntegration !== !1) try {
                    this._hostedIntegration.teardown(function() {
                        this._hostedIntegration = !1, this.setupHostedFieldsClient()
                    }.bind(this))
                } catch (e) {
                    this.setupHostedFieldsClient()
                } else this.setupHostedFieldsClient()
            }.bind(this), 50)))
        },
        teardownHostedFields: function(e) {
            "undefined" != typeof this._hostedIntegration && this._hostedIntegration !== !1 ? this._hostedIntegration.teardown(function() {
                this._hostedIntegration = !1, "function" == typeof e && e()
            }.bind(this)) : "function" == typeof e && e()
        },
        setupHostedFieldsClient: function() {
            if( /Android|webOS|iPhone|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                return $$('iframe[name^="braintree-"]').length > 0 ? !1 : (this._hostedIntegration = !1, void this.getClient(function(e) {
					var t = {
						client: e,
						styles: this.getHostedFieldsStyles(),
						fields: {
							number: {
								selector: "#card-number",
								placeholder: "Card Number"

							},
							expirationDate: {
								selector: "#expiration-month",
								placeholder: "MM/YY"
							}
							
							
						}
					};
					null !== $("cvv") && (t.fields.cvv = {
						selector: "#cvv",
						placeholder: "CVC"
					}), braintree.hostedFields.create(t, function(e, t) {
						return e ? void console.log(e) : this.hostedFieldsOnReady(t)
					}.bind(this))
				}.bind(this)))
            }
            else{
		        var uA = navigator.userAgent; //:RV
                if(uA.indexOf('MSIE 9.0') > -1 || uA.indexOf('MSIE 10.0') > -1 || uA.indexOf('rv:11.0') > -1){ //:RV
					return $$('iframe[name^="braintree-"]').length > 0 ? !1 : (this._hostedIntegration = !1, void this.getClient(function(e) {
						var t = {
							client: e,
							styles: this.getHostedFieldsStyles(),
							fields: {
								number: {
									selector: "#card-number"
								},
								expirationMonth: {
									selector: "#expiration-month",
									placeholder: "Month",
									select: {
									options: [
									  '01 - January',
									  '02 - February',
									  '03 - March',
									  '04 - April',
									  '05 - May',
									  '06 - June',
									  '07 - July',
									  '08 - August',
									  '09 - September',
									  '10 - October',
									  '11 - November',
									  '12 - December'
									]
								  }
								},
								expirationYear: {
									selector: "#expiration-year",
									placeholder: "Year",
									select: {}
								}, 
							}
						};
						null !== $("cvv") && (t.fields.cvv = {
							selector: "#cvv"
						}), braintree.hostedFields.create(t, function(e, t) {
							return e ? void console.log(e) : this.hostedFieldsOnReady(t);
							hostedFieldsInstance.on('inputSubmitRequest', function () {
						   //alert("hh");
							// User requested submission, e.g. by pressing Enter or equivalent
							document.getElementById("payment-submit-button").click();
						  });
						}.bind(this))
					}.bind(this)))
				}else{
					return $$('iframe[name^="braintree-"]').length > 0 ? !1 : (this._hostedIntegration = !1, void this.getClient(function(e) {
						var t = {
							client: e,
							styles: this.getHostedFieldsStyles(),
							fields: {
								number: {
									selector: "#card-number",
									placeholder: "Card Number"

								},
								expirationMonth: {
									selector: "#expiration-month",
									placeholder: "Month",
									select: {
									options: [
									  '01 - January',
									  '02 - February',
									  '03 - March',
									  '04 - April',
									  '05 - May',
									  '06 - June',
									  '07 - July',
									  '08 - August',
									  '09 - September',
									  '10 - October',
									  '11 - November',
									  '12 - December'
									]
								  }
								},
								expirationYear: {
									selector: "#expiration-year",
									placeholder: "Year",
									select: {}
								}, 
							}
						};
						null !== $("cvv") && (t.fields.cvv = {
							selector: "#cvv",
							placeholder: "Security Code"
						}), braintree.hostedFields.create(t, function(e, t) {
							return e ? void console.log(e) : this.hostedFieldsOnReady(t);
							hostedFieldsInstance.on('inputSubmitRequest', function () {
						   //alert("hh");
							// User requested submission, e.g. by pressing Enter or equivalent
							document.getElementById("payment-submit-button").click();
						  });
						}.bind(this))
					}.bind(this)))
				}
            }

        },
        hostedFieldsOnReady: function(e) {
            if (this._hostedIntegration = e, $$("#credit-card-form.loading").length && $$("#credit-card-form.loading").first().removeClassName("loading"), this.integration.submitAfterPayment) {
                var t = new Element("input", {
                    type: "hidden",
                    name: "payment[submit_after_payment]",
                    value: 1,
                    id: "braintree-submit-after-payment"
                });
                $("payment_form_gene_braintree_creditcard").insert(t)
            } else $("braintree-submit-after-payment") && $("braintree-submit-after-payment").remove();
            e.on("cardTypeChange", this.hostedFieldsCardTypeChange.bind(this));
             e.on("validityChange", this.getFormValidation.bind(this));
             e.on("inputSubmitRequest", this.getEnter.bind(this));
             e.on("validityChange", this.cardValidateBind.bind(this));
            
        },
        getEnter: function(e)
        {
            jQuery('#payment-submit-button').click();
            jQuery('#btn-order-button').click();
   
        },
        cardValidate: function()
        {
            if( /Android|webOS|iPhone|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
            {
               card_values_invalid='';
                var flag=0;
                if(!validate_cardnumber)
                {
                    card_values_invalid='card-number';
                    flag=1;
                }
                if(!validate_date)
                {
                    card_values_invalid+='+expiration-month';
                    flag=1;
                }
                if(!validate_cvv)
                {
                    card_values_invalid+='+cvv';
                    flag=1;
                }
                return card_values_invalid;
            }
            else
            {
                card_values_invalid='';
                var flag=0;
                if(!validate_cardnumber)
                {
                    card_values_invalid='card-number';
                    flag=1;
                }
                if(!validate_month)
                {
                    card_values_invalid+='+expiration-month';
                    flag=1;
                }
                if(!validate_year)
                {
                    card_values_invalid+='+expiration-year';
                    flag=1;
                }
                if(!validate_cvv)
                {
                    card_values_invalid+='+cvv';
                    flag=1;
                }
                return card_values_invalid;
            }
                
        },
        cardValidateBind: function()
        {
            if(! /Android|webOS|iPhone|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
            {
                card_values_invalid='';
                var flag=0;
                if(validate_cardnumber)
                {
                    card_values_invalid='card-number';
                    flag=1;
                }
                if(validate_month)
                {
                    card_values_invalid+='+expiration-month';
                    flag=1;
                }
                if(validate_year)
                {
                    card_values_invalid+='+expiration-year';
                    flag=1;
                }
                if(validate_cvv)
                {
                    card_values_invalid+='+cvv';
                    flag=1;
                }
                cardBraintreeValidate2(card_values_invalid);
            }
            else
            {
                card_values_invalid='';
                var flag=0;
                if(validate_cardnumber)
                {
                    card_values_invalid='card-number';
                    flag=1;
                }
                if(validate_date)
                {
                    card_values_invalid+='+expiration-month';
                    flag=1;
                }
                if(validate_cvv)
                {
                    card_values_invalid+='+cvv';
                    flag=1;
                }
                cardBraintreeValidate2(card_values_invalid);
            } 
                 
        },
            
valuesFormValidation: function(value,field,agent)
{
    
    if( /Android|webOS|iPhone|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
    {
        if(field.container.id=="card-number")
        validate_cardnumber=value;
      else if(field.container.id=="expiration-month")
        validate_date=value;
      else if(field.container.id=="cvv")
        validate_cvv=value;
        
    }
    else
    {
        if(field.container.id=="card-number")
        validate_cardnumber=value;
      else if(field.container.id=="expiration-month")
        validate_month=value;
      else if(field.container.id=="expiration-year")
        validate_year=value;
      else if(field.container.id=="cvv")
        validate_cvv=value;
    }
     
},
getFormValidation: function(event) {

var field = event.fields[event.emittedBy];
if( /Android|webOS|iPhone|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
    var agent="mobile";
else
    var agent="desktop";

if (field.isValid) {
      console.log(event.emittedBy, 'is fully valid');
     
      this.valuesFormValidation(1,field,agent);
    } else if (field.isPotentiallyValid) {
      console.log(event.emittedBy, 'is potentially valid');
      
      this.valuesFormValidation(0,field,agent);
    } else {
      console.log(event.emittedBy, 'is not valid');
      
      this.valuesFormValidation(0,field,agent);
    }

},


        getHostedFieldsStyles: function() {
			if(! /Android|webOS|iPhone|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
              return "function" == typeof this.integration.getHostedFieldsStyles ? this.integration.getHostedFieldsStyles() : {
                input: {
                    "font-size": "14pt",
                    color: "#3A3A3A"
                },
                ":focus": {
                    color: "black"
                },
                ".valid": {
                    color: "green"
                },
                ".invalid": {
                    color: "red"
                },
				"#cvv, #credit-card-number": {
					"font-size": "14px",
                },
				"#expiration-month, #expiration-year, .cvv": {
					"padding": "0px 4px"
                }
				
            }
			}else {
				return "function" == typeof this.integration.getHostedFieldsStyles ? this.integration.getHostedFieldsStyles() : {
                input: {
                    "font-size": "14pt",
                    color: "#3A3A3A"
                },
                ":focus": {
                    color: "black"
                },
                ".valid": {
                    color: "green"
                },
                ".invalid": {
                    color: "red"
                },
				"#cvv, #credit-card-number, #expiration": {
					"font-size": "16px",
                    color: "#555"
                },
				"#expiration-month, #expiration-year, .cvv": {
					"padding": "0px 4px"
                }
            }
				
			}
        },
        hostedFieldsCardTypeChange: function(e) {
            var field = e.fields[e.emittedBy];

            if (field.isEmpty) 
                this.updateCardType(!1, "card");
            else
            {    
            if ("undefined" != typeof e.cards) {
                var t = {
                    visa: "VI",
                    "american-express": "AE",
                    "master-card": "MC",
                    discover: "DI",
                    jcb: "JCB",
                    maestro: "ME"
                };
                void 0 !== typeof t[e.cards[0].type] ? this.updateCardType(!1, t[e.cards[0].type]) : this.updateCardType(!1, "card")
            }
        }
        },
        hostedFieldsNonceReceived: function(e, t) {
            this.threeDSecure ? ("function" == typeof this.integration.setLoading && this.integration.setLoading(), this.updateData(function() {
                this.verify3dSecureNonce(e, {
                    onSuccess: function(e) {
                        this.updateNonce(e.nonce), "function" == typeof t.onSuccess && t.onSuccess()
                    }.bind(this),
                    onFailure: function() {
                        "function" == typeof t.onFailure && t.onFailure()
                    }.bind(this)
                })
            }.bind(this))) : (this.updateNonce(e), "function" == typeof t.onSuccess && t.onSuccess())
        },
        updateNonce: function(e) {
            $("creditcard-payment-nonce").value = e, $("creditcard-payment-nonce").setAttribute("value", e), "function" == typeof this.integration.resetLoading && this.integration.resetLoading(), this._hostedFieldsTokenGenerated = !0
        },
        hostedFieldsError: function(e) {
            return "function" == typeof this.integration.resetLoading && this.integration.resetLoading(), "undefined" != typeof e.message && -1 == e.message.indexOf("Cannot place two elements in") && -1 == e.message.indexOf("Unable to find element with selector") && -1 == e.message.indexOf("User did not enter a payment method") && alert(e.message), this._hostedFieldsTokenGenerated = !1, "function" == typeof this.integration.afterHostedFieldsError && this.integration.afterHostedFieldsError(e.message), !1
        },
        usingSavedCard: function() {
            return void 0 != $("creditcard-saved-accounts") && void 0 != $$("#creditcard-saved-accounts input:checked[type=radio]").first() && "other" !== $$("#creditcard-saved-accounts input:checked[type=radio]").first().value
        },
        usingSavedThreeDCard: function() {
            return this.usingSavedCard() && $$("#creditcard-saved-accounts input:checked[type=radio]").first().hasAttribute("data-threedsecure-nonce")
        },
        setThreeDSecure: function(e) {
            this.threeDSecure = e
        },
        setAmount: function(e) {
            this.amount = parseFloat(e)
        },
        setBillingName: function(e) {
            this.billingName = e
        },
        getBillingName: function() {
            return "object" == typeof this.billingName ? this.combineElementsValues(this.billingName) : this.billingName
        },
        setBillingPostcode: function(e) {
            this.billingPostcode = e
        },
        getBillingPostcode: function() {
            return "object" == typeof this.billingPostcode ? this.combineElementsValues(this.billingPostcode) : this.billingPostcode
        },
        setAcceptedCards: function(e) {
            this.acceptedCards = e
        },
        getBillingAddress: function() {
            if ("function" == typeof this.integration.getBillingAddress) return this.integration.getBillingAddress();
            var e = {};
            return null !== $("co-billing-form") ? e = "FORM" == $("co-billing-form").tagName ? $("co-billing-form").serialize(!0) : this.extractBilling($("co-billing-form").up("form").serialize(!0)) : null !== $("billing:firstname") && (e = this.extractBilling($("billing:firstname").up("form").serialize(!0))), e ? e : void 0
        },
        extractBilling: function(e) {
            var t = {};
            return $H(e).each(function(e) {
                0 == e.key.indexOf("billing") && -1 == e.key.indexOf("password") && (t[e.key] = e.value)
            }), t
        },
        getAcceptedCards: function() {
            return this.acceptedCards
        },
        combineElementsValues: function(e, t) {
            t || (t = " ");
            var i = [];
            return e.each(function(e, t) {
                void 0 !== $(e) && (i[t] = $(e).value)
            }), i.join(t)
        },
        updateCardType: function(e, t) {
            if (void 0 != $("card-type-image")) {
                if(t=="VI")
                    braintree_cards = "/skin/frontend/ress/default/images/cards/visa.png";
                else if(t=="AE")
                    braintree_cards = "/skin/frontend/ress/default/images/cards/amex.png";
                else if(t=="MC")
                    braintree_cards = "/skin/frontend/ress/default/images/cards/master.png";
                else if(t=="DI")
                    braintree_cards = "/skin/frontend/ress/default/images/cards/discover.png";
                
                var i = $("card-type-image").src.substring(0, $("card-type-image").src.lastIndexOf("/"));
                $("card-type-image").setAttribute("src", i + "/" + t + ".png")
            }
        },
        observeAjaxRequests: function(e, t) {
            return vZero.prototype.observingAjaxRequests ? !1 : (vZero.prototype.observingAjaxRequests = !0, Ajax.Responders.register({
                onComplete: function(i) {
                    return this.handleAjaxRequest(i.url, e, t)
                }.bind(this)
            }), void(window.jQuery && jQuery(document).ajaxComplete(function(i, n, o) {
                return this.handleAjaxRequest(o.url, e, t)
            }.bind(this))))
        },
        handleAjaxRequest: function(e, t, i) {
            if ("undefined" != typeof i && i instanceof Array && i.length > 0) {
                var n = !1;
                if (i.each(function(t) {
                        e && -1 != e.indexOf(t) && (n = !0)
                    }), n === !0) return !1
            }
            e && -1 == e.indexOf("/braintree/") && (this.fireEvent(this, "integration.onHandleAjaxRequest", {
                url: e
            }), t ? t(e) : this.updateData())
        },
        updateData: function(e, t) {
            this.fireEvent(this, "integration.onBeforeUpdateData", {
                params: t
            }), this._updateDataCallbacks.push(e), this._updateDataParams = t, this._updateDataXhr !== !1 && this._updateDataXhr.transport.abort(), this._updateDataXhr = new Ajax.Request(this.quoteUrl, {
                method: "post",
                parameters: this._updateDataParams,
                onSuccess: function(e) {
                    if (e && (e.responseJSON || e.responseText)) {
                        var t = this._parseTransportAsJson(e);
                        void 0 != t.billingName && (this.billingName = t.billingName), void 0 != t.billingPostcode && (this.billingPostcode = t.billingPostcode), void 0 != t.grandTotal && (this.amount = t.grandTotal), void 0 != t.threeDSecure && this.setThreeDSecure(t.threeDSecure), "undefined" != typeof vzeroPaypal && void 0 != t.grandTotal && void 0 != t.currencyCode && vzeroPaypal.setPricing(t.grandTotal, t.currencyCode), this._updateDataParams = {}, this._updateDataXhr = !1, this._updateDataCallbacks.length && (this._updateDataCallbacks.each(function(e) {
                            e(t)
                        }.bind(this)), this._updateDataCallbacks = []), this.fireEvent(this, "onAfterUpdateData", {
                            response: t
                        })
                    }
                }.bind(this),
                onFailure: function() {
                    this._updateDataParams = {}, this._updateDataXhr = !1, this._updateDataCallbacks = []
                }.bind(this)
            })
        },
        tokenize3dSavedCards: function(e) {
            if (this.threeDSecure)
                if (void 0 !== $$("[data-token]").first()) {
                    var t = [];
                    $$("[data-token]").each(function(e, i) {
                        t[i] = e.getAttribute("data-token")
                    }), new Ajax.Request(this.tokenizeUrl, {
                        method: "post",
                        onSuccess: function(t) {
                            if (t && (t.responseJSON || t.responseText)) {
                                var i = this._parseTransportAsJson(t);
                                i.success && $H(i.tokens).each(function(e) {
                                    void 0 != $$('[data-token="' + e.key + '"]').first() && $$('[data-token="' + e.key + '"]').first().setAttribute("data-threedsecure-nonce", e.value)
                                }), e && e(i)
                            }
                        }.bind(this),
                        parameters: {
                            tokens: Object.toJSON(t)
                        }
                    })
                } else e();
            else e()
        },
        verify3dSecureNonce: function(e, t) {
            this.getClient(function(i) {
                braintree.threeDSecure.create({
                    client: i
                }, function(i, n) {
                    if (i) return void console.log(i);
                    var o = {
                        amount: this.amount,
                        nonce: e,
                        addFrame: function(e, t) {
                            $$("#three-d-modal .bt-modal-body").first().insert(t), $("three-d-modal").removeClassName("hidden")
                        },
                        removeFrame: function() {
                            $$("#three-d-modal .bt-modal-body iframe").first().remove(), $("three-d-modal").addClassName("hidden")
                        }.bind(this)
                    };
                    n.verifyCard(o, function(e, i) {
                        e ? t.onFailure && t.onFailure(i, e) : i.liabilityShifted ? t.onSuccess && t.onSuccess(i) : i.liabilityShiftPossible ? t.onSuccess && t.onSuccess(i) : t.onSuccess && t.onSuccess(i)
                    })
                }.bind(this))
            }.bind(this))
        },
        verify3dSecureVault: function(e) {
            var t = $$("#creditcard-saved-accounts input:checked[type=radio]").first().getAttribute("data-threedsecure-nonce");
            t ? this.verify3dSecureNonce(t, {
                onSuccess: function(t) {
                    $("creditcard-payment-nonce").removeAttribute("disabled"), $("creditcard-payment-nonce").value = t.nonce, $("creditcard-payment-nonce").setAttribute("value", t.nonce), "function" == typeof e.onSuccess && e.onSuccess()
                },
                onFailure: function(t, i) {
                    alert(i), "function" == typeof e.onFailure ? e.onFailure() : checkout.setLoadWaiting(!1)
                }
            }) : (alert("No payment nonce present."), "function" == typeof e.onFailure ? e.onFailure() : checkout.setLoadWaiting(!1))
        },
        processCard: function(e) {
            this._hostedIntegration.tokenize(function(t, i) {
                return t ? ("function" == typeof e.onFailure ? e.onFailure() : checkout.setLoadWaiting(!1), void("string" == typeof t.message && alert(t.message))) : this.hostedFieldsNonceReceived(i.nonce, e)
            }.bind(this))
        },
        shouldInterceptCreditCard: function() {
            return "0.00" != this.amount
        },
        shouldInterceptPayPal: function() {
            return !0
        },
        process: function(e) {
            return e = e || {}, this._hostedFieldsTokenGenerated || this.usingSavedCard() && !this.usingSavedThreeDCard() ? void("function" == typeof e.onSuccess && e.onSuccess()) : this.usingSavedThreeDCard() ? this.verify3dSecureVault(e) : this.processCard(e)
        },
        creditCardLoaded: function() {
            return !1
        },
        paypalLoaded: function() {
            return !1
        },
        _parseTransportAsJson: function(transport) {
            return transport.responseJSON && "object" == typeof transport.responseJSON ? transport.responseJSON : transport.responseText ? "object" == typeof JSON && "function" == typeof JSON.parse ? JSON.parse(transport.responseText) : eval("(" + transport.responseText + ")") : {}
        }
    },
    function() {
        for (var e, t = function() {}, i = ["assert", "clear", "count", "debug", "dir", "dirxml", "error", "exception", "group", "groupCollapsed", "groupEnd", "info", "log", "markTimeline", "profile", "profileEnd", "table", "time", "timeEnd", "timeStamp", "trace", "warn"], n = i.length, o = window.console = window.console || {}; n--;) e = i[n], o[e] || (o[e] = t)
    }();