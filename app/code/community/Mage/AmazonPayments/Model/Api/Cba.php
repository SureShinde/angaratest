<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_AmazonPayments
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/*
The following code is Copyright 2011 Amazon Technologies, Inc. All Rights Reserved.

Licensed under the Open Software License ("OSL"), version 3.0 (the .License.). 
You may not use this file except in compliance with the License. A copy of the License is located at

      http://opensource.org/licenses/osl-3.0.php  

*/
/**
 * AmazonPayments CBA API wrappers model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Cba extends Mage_AmazonPayments_Model_Api_Abstract
{
    protected static $HMAC_SHA1_ALGORITHM = "sha1";
    protected $_paymentCode = 'amazonpayments_cba';

    protected $_carriers;
    protected $_address;

    const STANDARD_SHIPMENT_RATE    = 'Standard';
    const EXPEDITED_SHIPMENT_RATE   = 'Expedited';
    const ONEDAY_SHIPMENT_RATE      = 'OneDay';
    const TWODAY_SHIPMENT_RATE      = 'TwoDay';

    protected $_shippingRates = array(
        self::STANDARD_SHIPMENT_RATE,
        self::EXPEDITED_SHIPMENT_RATE,
        self::ONEDAY_SHIPMENT_RATE,
        self::TWODAY_SHIPMENT_RATE,
    );

    protected $_configShippingRates = null;

    /**
     * Return Merchant Id from config
     *
     * @return string
     */
    public function getMerchantId()
    {
        return Mage::getStoreConfig('payment/amazonpayments_cba/merchant_id');
    }

    /**
     * Return action url for CBA Cart form to Amazon
     *
     * @return unknown
     */
    public function getAmazonRedirectUrl()
    {
        $_url = $this->getPayServiceUrl();
        $_merchantId = Mage::getStoreConfig('payment/amazonpayments_cba/merchant_id');
        return $_url.$_merchantId;
    }

    public function getConfigShippingRates()
    {
        if (is_null($this->_configShippingRates)) {
            $this->_configShippingRates = array();
            foreach ($this->_shippingRates as $_rate) {
                $_carrier = unserialize(Mage::getStoreConfig('payment/amazonpayments_cba/' . strtolower($_rate) . '_rate'));
                if ($_carrier['method'] && $_carrier['method'] != 'None') {
                    $_carrierInfo = explode('/', $_carrier['method']);
                    $this->_configShippingRates[$_rate] = array(
                        'carrier' => $_carrierInfo[0],
                        'method' => $_carrierInfo[1]
                    );
                }
            }
        }
        return $this->_configShippingRates;
    }

    /**
     * Computes RFC 2104-compliant HMAC signature.
     *
     * @param data Array
     *            The data to be signed.
     * @param key String
     *            The signing key, a.k.a. the AWS secret key.
     * @return The base64-encoded RFC 2104-compliant HMAC signature.
     */
    public function calculateSignature($data, $secretKey)
    {
        $stringData = '';
        if (is_array($data)) {
            ksort($data);
            foreach ($data as $key => $value) {
                $stringData .= $key.'='.rawurlencode($value).'&';
            }
        } elseif (is_string($data)) {
            $stringData = $data;
        } else{
            $stringData = $data;
        }

        // compute the hmac on input data bytes, make sure to set returning raw hmac to be true
        $rawHmac = hash_hmac(self::$HMAC_SHA1_ALGORITHM, $stringData, $secretKey, true);

        // base64-encode the raw hmac
        return base64_encode($rawHmac);
    }

    /**
     * Format amount value (2 digits after the decimal point)
     *
     * @param float $amount
     * @return float
     */
    public function formatAmount($amount)
    {
        return Mage::helper('amazonpayments')->formatAmount($amount);
    }

    /**
     * Build XML-based Cart for Checkout by Amazon
     *
     * @param Mage_Sales_Model_Quote
     * @return string
     */
    public function getXmlCart(Mage_Sales_Model_Quote $quote)
    {
        $_xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
                .'<Order xmlns="http://payments.amazon.com/checkout/2008-11-30/">'."\n";
        if (!$quote->hasItems()) {
            // Log if the checkout is done with an empty cart
            Mage::log("No Items found in the quote");
            return false;
        }
		// discount code	
		if($quote->getShippingAddress()->getBaseDiscountAmount()){
			if($quote->getShippingAddress()->getBaseDiscountAmount() != ''){
				$discount_tot_amt = abs($quote->getShippingAddress()->getBaseDiscountAmount());
			}else{
				$discount_tot_amt = 0;
			}
		}else{
			$discount_tot_amt = 0;
		}
		$discount_tot_amt = $this->formatAmount($discount_tot_amt);
		if($quote->getCouponCode()){
			$discount_cart_code = trim($quote->getCouponCode());
		}else{
			$discount_cart_code = 'SPLDISC';
		}
		// discount code	
        //$_xml .= " <ClientRequestId>{$quote->getId()}</ClientRequestId>\n"; // Returning parameter

        $_xml .= " <Cart>\n"
                ."   <Items>\n";
        foreach ($quote->getAllVisibleItems() as $_item) {
			$product = $_item->getProduct();
	
			$configurabledata=Mage::getModel('Mage_Catalog_Model_Product_Type_Configurable');
			$configurablearray=$configurabledata->getOrderOptions($product);
			if($_item->getProductType() == "configurable") {
				$sku=$configurablearray['simple_sku'];
				$product=$_item->getProduct($product->getIdBySku($sku));
				$name=$configurablearray['simple_name'];
			} 
			else
			{
				$sku=$_item->getSku();
				$name=$_item->getName();
			};
			
			
			//	Code modified by vaseem to show ring size for amazon order
			if ($product->hasCustomOptions()) {  			//	This will check for custom option or use if($product->hasOptions()) {
				$productOptions	= $_item->getProduct()->getTypeInstance(true)->getOrderOptions($_item->getProduct());
				$options = $productOptions['options'];
				foreach($options as $option){
					$label		=	$option['label'];
					$value		=	$option['value'];
					// Code modified by Vaseem for configurable product
					if($label=='Engraving' || $label=='Engrave this Product'){
						if( strstr($value,'(SnellRoundhand Script)') || strstr($value,'(Helvetica 45 Light)') ){
							$value 	=	str_replace(' (SnellRoundhand Script)','(SR)',$value);
							$value 	=	str_replace(' (Helvetica 45 Light)','(HL)',$value);
							
							$sku		=	$sku.'-E-'.$value;		//	HL SR
							$engString	=	'E-'.$value;
						}
						
					}
					if($label=='Ring Size'){
						$ringSize	=	$value;
						$sku		=	$sku.'-RS-'.$ringSize;
					}
					// Code modified by Vaseem for configurable product
				}
			}
			//	Code modified by vaseem to show ring size for amazon order

			// VAT inclusive prices only for UK and DE. Taxes are ignored in EU callbacks
			if($this->getCountry  == "US" ) {
				$amount = $this->formatAmount($_item->getPrice());
			} 
			else {
				$amount = $this->formatAmount($_item->getPrice());
				//$amount = $this->formatAmount($_item['price_incl_tax']);
			}
			
			$sku = str_replace('--','-',$sku);
			
			// Code modified by Vaseem for simple product
			if(strstr($sku,'engraving') && $engString!=''){
				//echo $engString;
				$sku 	=	str_replace('engraving',$engString,$sku);
				//echo 'sku after engraving'.$sku;
			}
 			// Code modified by Vaseem for simple product
		
            // use currency and weight format based on the country configured
                // Previously currency and weights were hardcoded.
			$sku_arr = explode('-',$sku);
			$sku_master = $sku_arr[0];	
			
			$_xml .= "<Item><SKU>{$sku_master}/{$_item->getId()}</SKU><MerchantId>{$this->getMerchantId()}</MerchantId><Title>{$sku}: {$name}</Title><Price><Amount>{$amount}</Amount><CurrencyCode>{$this->getCurrencyFormat()}</CurrencyCode></Price><Quantity>{$_item->getQty()}</Quantity><Weight><Amount>{$this->formatAmount($_item->getWeight())}</Amount><Unit>{$this->getWeightUnit()}</Unit></Weight>";
			
			/*$_xml .= "<Item><SKU>{$sku}/{$_item->getId()}</SKU><MerchantId>{$this->getMerchantId()}</MerchantId><Title>{$name}</Title><Price><Amount>{$amount}</Amount><CurrencyCode>{$this->getCurrencyFormat()}</CurrencyCode></Price><Quantity>{$_item->getQty()}</Quantity><Weight><Amount>{$this->formatAmount($_item->getWeight())}</Amount><Unit>{$this->getWeightUnit()}</Unit></Weight>";
			*/
			/*$_xml .= "<Images>
						<Image>
						  <URL>http://www.angara.com/media/catalog/product/images/rings/Oval-Amethyst-and-Diamond-Split-Shank-Ring-in-14k-White-Gold_SR0112AMH_Reg_1.jpg</URL>
						</Image>
					  </Images>";*/
            $_xml .= "</Item>";

        }
        $_xml .= "</Items><CartPromotionId>".$discount_cart_code."</CartPromotionId></Cart>";
		$_xml .= "<Promotions>
					<Promotion>
					  <PromotionId>".$discount_cart_code."</PromotionId>
					  <Description>promotion</Description>	 
					  <Benefit>
						<FixedAmountDiscount>
						  <Amount>".$discount_tot_amt."</Amount>
						  <CurrencyCode>USD</CurrencyCode>
						</FixedAmountDiscount>
					  </Benefit>
					</Promotion>
				  </Promotions>";		
        $_xml .= "</Order>";
		//echo $_xml;
		return $_xml;
    }

    /**
     * Retrieve checkout tax tables xml
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return string
     */
    protected function _getCheckoutTaxXml(Mage_Sales_Model_Quote $quote)
    {
        $xml = '';

        // shipping tax table (used as default, because we have no ability to specify shipping tax table)
        // removed because of no ability to use default tax table as shipping tax table

        $xml .= "<TaxTables>\n";

        // item tax tables
        $xml .= $this->_getTaxTablesXml($quote, $this->_getTaxRules($quote));

        // empty tax table for products without tax class
        $xml .= "   <TaxTable>\n"
             ."      <TaxTableId>none</TaxTableId>\n"
             ."      <TaxRules>\n"
             ."        <TaxRule>\n"
             ."          <Rate>0</Rate>\n"
             ."          <PredefinedRegion>WorldAll</PredefinedRegion>\n"
             ."        </TaxRule>\n"
             ."      </TaxRules>\n"
             ."    </TaxTable>\n";

        $xml .= "</TaxTables>\n";


        return $xml;
    }

    /**
     * Retrieve specified tax rates xml
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param array $rates
     * @param string $type
     * @param mixed $addTo
     * @return string
     */
    protected function _getTaxTablesXml($quote, $rules, $isShipping = false, $addTo = null)
    {
        $xml = '';
        if ($isShipping) {
            $isShippingTaxed = 'true';
            $taxTableTag = 'DefaultTaxTable';
        } else {
            $isShippingTaxed = 'false';
            $taxTableTag = 'TaxTable';
        }

        if (is_array($rules)) {
            if ($addTo) {
                $_tables = $addTo->addChild('TaxTables');
            }

            foreach ($rules as $group=>$taxRates) {
                $isShippingTaxed = ($isShipping ? 'true' : 'false');
                if ($isShipping) {
                    $tableName = 'default-tax-table';
                } else {
                    $tableName = "tax_{$group}";
                    if ($group == $this->_getShippingTaxClassId($quote)) {
                        $isShippingTaxed = 'true';
                    }
                }
                if ($addTo) {
                    $_table = $_tables->addChild($taxTableTag);
                    $_table->addChild('TaxTableId', $tableName);
                    $_rules = $_table->addChild('TaxRules');
                } else {
                    $xml .= " <{$taxTableTag}>\n";
                    $xml .= "  <TaxTableId>{$tableName}</TaxTableId>\n";
                    $xml .= "  <TaxRules>\n";
                }

                if (is_array($taxRates)) {
                    foreach ($taxRates as $rate) {
                        if ($addTo) {
                            $_rule = $_rules->addChild('TaxRule');
                            $_rule->addChild('Rate', $rate['value']);
                            $_rule->addChild('IsShippingTaxed', $isShippingTaxed);
                        } else {
                            $xml .= "   <TaxRule>\n";
                            $xml .= "    <Rate>{$rate['value']}</Rate>\n";
                            $xml .= "    <IsShippingTaxed>{$isShippingTaxed}</IsShippingTaxed>\n";
                        }

                        if ($rate['country']==='US') {
                            if (!empty($rate['postcode']) && $rate['postcode']!=='*') {
                                if ($addTo) {
                                    $_rule->addChild('USZipRegion', $rate['postcode']);
                                } else {
                                    $xml .= "    <USZipRegion>{$rate['postcode']}</USZipRegion>\n";
                                }
                            } else if (!empty($rate['state']) && $rate['state']!=='*') {
                                if ($addTo) {
                                    $_rule->addChild('USStateRegion', $rate['state']);
                                } else {
                                    $xml .= "    <USStateRegion>{$rate['state']}</USStateRegion>\n";
                                }
                            } else {
                                if ($addTo) {
                                    $_rule->addChild('PredefinedRegion', 'USAll');
                                } else {
                                    $xml .= "    <PredefinedRegion>USAll</PredefinedRegion>\n";
                                }
                            }
                        } else {
                            if ($addTo) {
                                $_region = $_rule->addChild('WorldRegion');
                                $_region->addChild('CountryCode', $rate['country']);
                                if (!empty($rate['postcode']) && $rate['postcode']!=='*') {
                                    $_region->addChild('PostalRegion', $rate['postcode']);
                                }
                            } else {
                                $xml .= "    <WorldRegion>\n";
                                $xml .= "     <CountryCode>{$rate['country']}</CountryCode>\n";
                                if (!empty($rate['postcode']) && $rate['postcode']!=='*') {
                                    $xml .= "     <PostalRegion>{$rate['postcode']}</PostalRegion>\n";
                                }
                                $xml .= "    </WorldRegion>\n";
                            }
                        }

                        $xml .= "   </TaxRule>\n";
                    }
                } else {
                    $taxRate = $taxRates/100;
                    if ($addTo) {
                        $_rule = $_rules->addChild('TaxRule');
                        $_rule->addChild('Rate', $taxRate);
                        $_rule->addChild('IsShippingTaxed', $isShippingTaxed);
                        $_rule->addChild('PredefinedRegion', 'WorldAll');
                    } else {
                        $xml .= "   <TaxRule>\n";
                        $xml .= "    <Rate>{$taxRate}</Rate>\n";
                        $xml .= "    <IsShippingTaxed>{$isShippingTaxed}</IsShippingTaxed>\n";
                        $xml .= "    <PredefinedRegion>WorldAll</PredefinedRegion>\n";
                        $xml .= "   </TaxRule>\n";
                    }
                }

                $xml .= "  </TaxRules>\n";
                $xml .= " </{$taxTableTag}>\n";
            }

        } else {
            if (is_numeric($rules)) {
                $taxRate = $rules/100;

                if ($addTo) {
                    $_table = $addTo->addChild($taxTableTag);
                    $_rules = $_table->addChild('TaxRules');
                    $_rule = $_rules->addChild('TaxRule');
                    $_rule->addChild('Rate', $taxRate);
                    $_rule->addChild('IsShippingTaxed', $isShippingTaxed);
                    $_rule->addChild('PredefinedRegion', 'WorldAll');
                } else {
                    $xml .= " <{$taxTableTag}>\n";
                    $xml .= "  <TaxRules>\n";
                    $xml .= "   <TaxRule>\n";
                    $xml .= "    <Rate>{$taxRate}</Rate>\n";
                    $xml .= "    <IsShippingTaxed>{$isShippingTaxed}</IsShippingTaxed>\n";
                    $xml .= "    <PredefinedRegion>WorldAll</PredefinedRegion>\n";
                    $xml .= "   </TaxRule>\n";
                    $xml .= "  </TaxRules>\n";
                    $xml .= " </{$taxTableTag}>\n";
                }
            }
        }

        if ($addTo) {
            return $addTo;
        }
        return $xml;
    }


    /**
     * Retrieve tax rules applicable to quote items
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    protected function _getTaxRules(Mage_Sales_Model_Quote $quote)
    {
        $customerTaxClass = $this->_getCustomerTaxClass($quote);
        if (Mage::helper('tax')->getTaxBasedOn() == 'origin') {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest();
            return Mage::getSingleton('tax/calculation')->getRatesForAllProductTaxClasses($request->setCustomerClassId($customerTaxClass));
        } else {
            $customerRules = Mage::getSingleton('tax/calculation')->getRatesByCustomerTaxClass($customerTaxClass);
            $rules = array();
            foreach ($customerRules as $rule) {
                $rules[$rule['product_class']][] = $rule;
            }
            return $rules;
        }
    }

    /**
     * Retrieve tax rules applicable to shipping
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    protected function _getShippingTaxRules(Mage_Sales_Model_Quote $quote)
    {
        $customerTaxClass = $this->_getCustomerTaxClass($quote);
        if ($shippingTaxClass = $this->_getShippingTaxClassId($quote)) {
            if (Mage::helper('tax')->getTaxBasedOn() == 'origin') {
                $request = Mage::getSingleton('tax/calculation')->getRateRequest();
                $request
                    ->setCustomerClassId($customerTaxClass)
                    ->setProductClassId($shippingTaxClass);
                return Mage::getSingleton('tax/calculation')->getRate($request);
            }
            $customerRules = Mage::getSingleton('tax/calculation')->getRatesByCustomerAndProductTaxClasses($customerTaxClass, $shippingTaxClass);
            $rules = array();
            foreach ($customerRules as $rule) {
                $rules[$rule['product_class']][] = $rule;
            }
            return $rules;
        } else {
            return array();
        }
    }

    /**
     * Retrieve shipping tax class
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return int
     */
    protected function _getShippingTaxClassId(Mage_Sales_Model_Quote $quote)
    {
        return Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $quote->getStoreId());
    }

    /**
     * Retrieve customer tax class from quote
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return int
     */
    protected function _getCustomerTaxClass(Mage_Sales_Model_Quote $quote)
    {
        $customerGroup = $quote->getCustomerGroupId();
        if (!$customerGroup) {
            $customerGroup = Mage::getStoreConfig('customer/create_account/default_group', $quote->getStoreId());
        }
        return Mage::getModel('customer/group')->load($customerGroup)->getTaxClassId();
    }

    /**
     * Handle Callback from CBA and calculate Shipping, Taxes in case XML-based shopping cart
     *
     */
    public function handleXmlCallback($xmlRequest, $session)
    {
        $_address = $this->_parseRequestAddress($xmlRequest);

        $quoteId = $_address['ClientRequestId'];
        $quote = Mage::getModel('sales/quote')->load($quoteId);

        $baseCurrency = $session->getQuote()->getBaseCurrencyCode();
        $currency = Mage::app()->getStore($session->getQuote()->getStoreId())->getBaseCurrency();

        $billingAddress = $quote->getBillingAddress();
        $address = $quote->getShippingAddress();

        $this->_address = $_address;

        $regionModel = Mage::getModel('directory/region')->loadByCode($_address['regionCode'], $_address['countryCode']);
        $_regionId = $regionModel->getId();

        $address->setCountryId($_address['countryCode'])
            ->setRegion($_address['regionCode'])
            ->setRegionId($_regionId)
            ->setCity($_address['city'])
            ->setStreet($_address['street'])
            ->setPostcode($_address['postCode']);
        $billingAddress->setCountryId($_address['countryCode'])
            ->setRegion($_address['regionCode'])
            ->setRegionId($_regionId)
            ->setCity($_address['city'])
            ->setStreet($_address['street'])
            ->setPostcode($_address['postCode']);

        $quote->setBillingAddress($billingAddress);
        $quote->setShippingAddress($address);
        $quote->save();

        $address->setCollectShippingRates(true)->collectShippingRates();

        $errors = array();
        $_carriers = array();
        foreach ($this->getConfigShippingRates() as $_cfgRate) {
            if ($carrier = Mage::getStoreConfig('carriers/' . $_cfgRate['carrier'], $this->getStoreId())) {
                if (isset($carrier['title']) && $carrier['active'] && !in_array($_cfgRate['carrier'], $_carriers)) {
                    $_carriers[] = $_cfgRate['carrier'];
                }
            }
        }

        $result = Mage::getModel('shipping/shipping')
            ->collectRatesByAddress($address, $_carriers)
            ->getResult();
        $rateCodes = array();
        foreach ($this->getConfigShippingRates() as $_cfgRateLevel => $_cfgRate) {
            if ($rates = $result->getRatesByCarrier($_cfgRate['carrier'])) {
                foreach ($rates as $rate) {
                    if (!$rate instanceof Mage_Shipping_Model_Rate_Result_Error && $rate->getMethod() == $_cfgRate['method']) {
                        if ($address->getFreeShipping()) {
                            $price = 0;
                        } else {
                            $price = $rate->getPrice();
                        }
                        if ($price) {
                            $price = Mage::helper('tax')->getShippingPrice($price, true, $address);
                        }
                        $this->_carriers[] = array(
                            'service_level' => $_cfgRateLevel,
                            'code'          => $rate->getCarrier() . '_' . $rate->getMethod(),
                            'price'         => $price,
                            'currency'      => $currency['currency_code'],
                            'description'   => $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle() . ' (Amazon ' . $_cfgRateLevel . ' Service Level)'
                        );
                    }
                }
            }
        }

        if ($_extShippingInfo = unserialize($quote->getExtShippingInfo())) {
            $_extShippingInfo = array_merge($_extShippingInfo, array('amazon_service_level' => $this->_carriers));
        } else {
            $_extShippingInfo = array('amazon_service_level' => $this->_carriers);
        }
        $quote->setExtShippingInfo(serialize($_extShippingInfo));

        $_items = $this->_parseRequestItems($xmlRequest);
	$_itemsrates = $this->_parseRequestRates($xmlRequest);
        $xml = $this->_generateXmlResponse($quote, $_items, $_itemsrates);
        $session->getQuote()
            ->setForcedCurrency($currency)
            ->collectTotals()
            ->save();
        $quote->save();
        return $xml;
    }

    /**
     * Parse request from Amazon and return order details
     *
     * @param string xml
     */
    public function parseOrder($xmlData)
    {
Mage::log("ParseOrder");
        $parsedOrder = array();
        if (strlen(trim($xmlData)) > 0) {
            $xml = simplexml_load_string($xmlData, 'Varien_Simplexml_Element');
	if (($xml->descend("ProcessedOrder/ShippingAddress/AddressFieldOne")))
	{
		$streetname=(string)$xml->descend("ProcessedOrder/ShippingAddress/AddressFieldOne").",\n".$xml->descend("ProcessedOrder/ShippingAddress/AddressFieldTwo");
	}
	else
	{
		$streetname=(string)$xml->descend("ProcessedOrder/ShippingAddress/AddressFieldTwo");
	}


            $parsedOrder = array(
                'NotificationReferenceId'   => (string) $xml->descend("NotificationReferenceId"),
                'amazonOrderID'     => (string) $xml->descend("ProcessedOrder/AmazonOrderID"),
                'orderDate'         => (string) $xml->descend("ProcessedOrder/OrderDate"),
                'orderChannel'      => (string) $xml->descend("ProcessedOrder/OrderChannel"),
                'buyerName'         => (string) $xml->descend("ProcessedOrder/BuyerInfo/BuyerName"),
                'buyerEmailAddress' => (string) $xml->descend("ProcessedOrder/BuyerInfo/BuyerEmailAddress"),
                'ShippingLevel'     => (string) $xml->descend("ProcessedOrder/ShippingServiceLevel"),
                'shippingAddress'   => array(
                    'name'          => (string) $xml->descend("ProcessedOrder/ShippingAddress/Name"),
                    'street'        => (string) $streetname,
                    'city'          => (string) $xml->descend("ProcessedOrder/ShippingAddress/City"),
                    'regionCode'    => (string) $xml->descend("ProcessedOrder/ShippingAddress/State"),
                    'postCode'      => (string) $xml->descend("ProcessedOrder/ShippingAddress/PostalCode"),
                    'countryCode'   => (string) $xml->descend("ProcessedOrder/ShippingAddress/CountryCode"),
                ),
                'items'             => array(),
            );
            $_total = $_shipping = $_tax = $_shippingTax = $_subtotalPromo = $_shippingPromo = $_subtotal = 0;
            $_itemsCount = $_itemsQty = 0;
            foreach ($xml->descend("ProcessedOrder/ProcessedOrderItems/ProcessedOrderItem") as $_item) {
                $parsedOrder['ClientRequestId'] = (string) $_item->ClientRequestId;
                $_compositeSku = explode('/', (string) $_item->SKU);
                $_sku = '';
                if (isset($_compositeSku[0])) {
                    $_sku = $_compositeSku[0];
                }
                $_itemId = '';
                if ($_compositeSku[1]) {
                    $_itemId = $_compositeSku[1];
                }
                $_itemQty = (string) $_item->Quantity;
                $_itemsQty += $_itemQty;
                $_itemsCount++;
                $parsedOrder['items'][$_itemId] = array(
                    'AmazonOrderItemCode' => (string) $_item->AmazonOrderItemCode,
                    'sku'   => $_sku,
                    'title' => (string) $_item->Title,
                    'price' => array(
                        'amount'       => (string) $_item->Price->Amount,
                        'currencyCode' => (string) $_item->Price->CurrencyCode,
                        ),
                    'qty' => $_itemQty,
                    'weight' => array(
                        'amount' => (string) $_item->Weight->Amount,
                        'unit'   => (string) $_item->Weight->Unit,
                        ),
                );
                $_itemSubtotal = 0;
                foreach ($_item->ItemCharges->Component as $_component) {
                    switch ((string) $_component->Type) {
                        case 'Principal':
                            $_itemSubtotal  += (string) $_component->Charge->Amount;
                            $parsedOrder['items'][$_itemId]['subtotal'] = $_itemSubtotal;
                            break;
                        case 'Shipping':
                            $_shipping      += (string) $_component->Charge->Amount;
			    $parsedOrder['items'][$_itemId]['shipping'] = $_shipping;
                            break;
                        case 'Tax':
                            $_tax           += (string) $_component->Charge->Amount;
			    $parsedOrder['items'][$_itemId]['tax'] = $_tax;
                            break;
                        case 'ShippingTax':
                            $_shippingTax   += (string) $_component->Charge->Amount;
			    $parsedOrder['items'][$_itemId]['shipping'] = $_shippingTax;
                            break;
                        case 'PrincipalPromo':
                            $_subtotalPromo += (string) $_component->Charge->Amount;
                            $parsedOrder['items'][$_itemId]['principal_promo'] = $_subtotalPromo;
                            break;
                        case 'ShippingPromo':
                            $_shippingPromo += (string) $_component->Charge->Amount;
                            $parsedOrder['items'][$_itemId]['shipping_promo'] = $_shippingPromo;
                            break;
                    }
                }
                $_subtotal += $_itemSubtotal;
            }

            $parsedOrder['itemsCount'] = $_itemsCount;
            $parsedOrder['itemsQty'] = $_itemsQty;

            $parsedOrder['subtotal'] = $_subtotal;
            $parsedOrder['shippingAmount'] = $_shipping;
            $parsedOrder['tax'] = $_tax + $_shippingTax;
            $parsedOrder['shippingTax'] = $_shippingTax;
            $parsedOrder['discount'] = $_subtotalPromo + $_shippingPromo;
            $parsedOrder['discountShipping'] = $_shippingPromo;

            $parsedOrder['total'] = $_subtotal + $_shipping + $_tax + $_shippingTax - abs($_subtotalPromo) - abs($_shippingPromo);
        }
        return $parsedOrder;
    }

    /**
     * Parsing xml notification for amazon order id
     *
     * @param string $xmlData
     * @return array
     */
    public function parseOrderReadyToShipNotification($xmlData)
    {
        $readyToShipData = array();
        if (strlen(trim($xmlData))) {
            $xml = simplexml_load_string($xmlData, 'Varien_Simplexml_Element');
            $aOrderId = (string) $xml->descend('ProcessedOrder/AmazonOrderID');
            $readyToShipData['amazon_order_id'] = $aOrderId;
            $readyToShipData['items'] = array();
            foreach ($xml->descend('ProcessedOrder/ProcessedOrderItems/ProcessedOrderItem') as $item) {
                $readyToShipData['items'][(string)$item->AmazonOrderItemCode] = (string)$item->Quantity;
            }
        }
        return $readyToShipData;
    }

    public function parseCancelNotification($xmlData)
    {
        $cancelData = array();
        if (strlen(trim($xmlData))) {
            $xml = simplexml_load_string($xmlData, 'Varien_Simplexml_Element');
            $aOrderId = (string) $xml->descend('ProcessedOrder/AmazonOrderID');
            $cancelData['amazon_order_id'] = $aOrderId;
        }
        return $cancelData;
    }

    /**
     * Return address from Amazon request
     *
     * @param array $responseArr
     */
    protected function _parseRequestAddress($xmlResponse)
    {
        $address = array();
        if (strlen(trim($xmlResponse)) > 0) {
	    $xmlRes = iconv("iso-8859-1","utf-8", $xmlResponse);
            $xml = simplexml_load_string($xmlRes, 'Varien_Simplexml_Element');
            $address = array(
                'addressId'         => (string) $xml->descend("CallbackOrders/CallbackOrder/Address/AddressId"),
                'regionCode'        => (string) $xml->descend("CallbackOrders/CallbackOrder/Address/State"),
                'countryCode'       => (string) $xml->descend("CallbackOrders/CallbackOrder/Address/CountryCode"),
                'city'              => (string) $xml->descend("CallbackOrders/CallbackOrder/Address/City"),
                'street'            => (string) $xml->descend("CallbackOrders/CallbackOrder/Address/AddressFieldOne"),
                'postCode'          => (string) $xml->descend("CallbackOrders/CallbackOrder/Address/PostalCode"),
                'ClientRequestId'   => (string) $xml->descend("ClientRequestId"),
            );
        } else {
            $address = array(
                'addressId'   => '',
                'regionCode'  => '',
                'countryCode' => '',
                'city'        => '',
                'street'      => '',
                'postCode'    => '',
            );
        }
        return $address;
    }

    /**
     * Return items SKUs from Amazon request
     *
     * @param array $responseArr
     */
    protected function _parseRequestItems($xmlResponse)
    {
        $items = array();
        if (strlen(trim($xmlResponse)) > 0) {
	    $xmlRes = iconv("iso-8859-1","utf-8", $xmlResponse);
            $xml = simplexml_load_string($xmlRes, 'Varien_Simplexml_Element');
            $itemsXml = $xml->descend("CallbackOrderCart/CallbackOrderCartItems");
		
            foreach ($itemsXml->CallbackOrderCartItem as $_item) {
                $sku = '';
                $compositeSku = explode('/', (string)$_item->Item->SKU);
if (isset($compositeSku[0])) {
                    $sku = $compositeSku[0];
                }
                $items[(string)$_item->Item->SKU] = $sku;
            }
        } else {
            return false;
        }
        return $items;
    }

   /**
     * Return items SKUs from Amazon request
     *
     * @param array $responseArr
     */
    protected function _parseRequestRates($xmlResponse)
    {
        if (strlen(trim($xmlResponse)) > 0) {
	    $xmlRes = iconv("iso-8859-1","utf-8", $xmlResponse);
            $xml = simplexml_load_string($xmlRes, 'Varien_Simplexml_Element');

 $items = array(
                'CalculateTaxRates'         => (string) $xml->descend("OrderCalculationCallbacks/CalculateTaxRates"),
                'CalculatePromotions'        => (string) $xml->descend("OrderCalculationCallbacks/CalculatePromotions"),
                'CalculateShippingRates'       => (string) $xml->descend("OrderCalculationCallbacks/CalculateShippingRates"),
            );
        }
        return $items;
    }

    /**
     * Generate XML Responce for Amazon with Shipping, Taxes, Promotions
     *
     * @return string xml
     */
    protected function _generateXmlResponse($quote, $items = array(), $itemsrates = array())
    {
        /*$_xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OrderCalculationsResponse xmlns="http://payments.amazon.com/checkout/2009-05-15/">
</OrderCalculationsResponse>
XML;*/
		$_xmlString = '<?xml version="1.0" encoding="UTF-8"?>
<OrderCalculationsResponse xmlns="http://payments.amazon.com/checkout/2009-05-15/">
</OrderCalculationsResponse>';

        $xml = new SimpleXMLElement($_xmlString);
        if (count($this->_carriers) > 0) {
            $_xmlResponse = $xml->addChild('Response');
            $_xmlCallbackOrders = $_xmlResponse->addChild('CallbackOrders');
            $_xmlCallbackOrder = $_xmlCallbackOrders->addChild('CallbackOrder');

            $_xmlAddress = $_xmlCallbackOrder->addChild('Address');
            $_xmlAddressId = $_xmlAddress->addChild('AddressId', $this->_address['addressId']);

            $_xmlCallbackOrderItems = $_xmlCallbackOrder->addChild('CallbackOrderItems');
            foreach ($items as $_itemSku) {
                $_quoteItem = null;
                foreach ($quote->getAllItems() as $_item) {

		$configurabledata=Mage::getModel('Mage_Catalog_Model_Product_Type_Configurable');
                $configurablearray=$configurabledata->getOrderOptions($_item->getProduct());
                if($_item->getProductType() == "configurable") {
                        $sku=$configurablearray['simple_sku'];
                }
                else
                {
                        $sku=$_item->getSku();
                }



                    if ($sku == $_itemSku) {
                        $_quoteItem = $_item;
                        break;
                    }
                }
                if (is_null($_quoteItem)) {
                    Mage::throwException($this->__('Item specified in callback request XML was not found in quote.'));
                }

                $_xmlCallbackOrderItem = $_xmlCallbackOrderItems->addChild('CallbackOrderItem');
                $_xmlCallbackOrderItem->addChild('CallbackOrderItemId', $_itemSku . '/' . $_quoteItem->getId());
		if ($itemsrates['CalculateTaxRates'] == 'true')
            	{
                	$_xmlCallbackOrderItem->addChild('TaxTableId', 'tax_'.$_quoteItem->getTaxClassId());
		}

 		if ($itemsrates['CalculateShippingRates'] == 'true')
           	{
                $_xmlShippingMethodIds = $_xmlCallbackOrderItem->addChild('ShippingMethodIds');
                foreach ($this->_carriers as $_carrier) {
                    $_xmlShippingMethodIds->addChild('ShippingMethodId', $_carrier['code']);
                }
		}
            }

	    if ($itemsrates['CalculateTaxRates'] == 'true')
	    {
            	$this->_appendTaxTables($xml, $quote, $this->_getTaxRules($quote));
	    }

 	    if ($itemsrates['CalculatePromotions'] == 'true')
	    {
	    	$this->_appendDiscounts($xml, $quote);
	    }

	    if ($itemsrates['CalculateShippingRates'] == 'true')
            {
		$_xmlShippingMethods = $xml->addChild('ShippingMethods');
            foreach ($this->_carriers as $_carrier) {
                $_xmlShippingMethod = $_xmlShippingMethods->addChild('ShippingMethod');

                $_xmlShippingMethod->addChild('ShippingMethodId', $_carrier['code']);
                $_xmlShippingMethod->addChild('ServiceLevel', $_carrier['service_level']);

                $_xmlShippingMethodRate = $_xmlShippingMethod->addChild('Rate');
                // Posible values: ShipmentBased | WeightBased | ItemQuantityBased
                $_xmlShippingMethodRateItem = $_xmlShippingMethodRate->addChild('ShipmentBased');
                $_xmlShippingMethodRateItem->addChild('Amount', $_carrier['price']);
                $_xmlShippingMethodRateItem->addChild('CurrencyCode', $_carrier['currency']);

                $_xmlShippingMethodIncludedRegions = $_xmlShippingMethod->addChild('IncludedRegions');
                $_xmlShippingMethodIncludedRegions->addChild('PredefinedRegion', 'WorldAll');
		if($_carrier['price'] == 0) {
                       $shippingLabelToken = "Free Shipping";
                }
                else {
		$shippingLabelToken = strtok($_carrier['description'],"("); 
                $currSymbol=$this->getCurrencySymbol($_carrier['currency']);
		$shippingLabelToken = $shippingLabelToken." - ".$currSymbol.$_carrier['price']; 
                }
		$_xmlShippingMethod->addChild('DisplayableShippingLabel',$shippingLabelToken);
            }
	    }
 	    if ($itemsrates['CalculatePromotions'] == 'true')
            {
                $xml->addChild('CartPromotionId', 'cart-total-discount');
	    }
}
        return $xml;
    }

    protected function _appendTaxTables($xml, $quote, $rules, $isShipping = false)
    {
        return $this->_getTaxTablesXml($quote, $rules, $isShipping, $xml);
    }

    protected function _appendDiscounts($xml, $quote)
    {
        $totalDiscount = $quote->getShippingAddress()->getBaseDiscountAmount() + $quote->getBillingAddress()->getBaseDiscountAmount();
        $discountAmount = ($totalDiscount ? $totalDiscount : 0);

        $_promotions = $xml->addChild('Promotions');
        $_promotion = $_promotions->addChild('Promotion');
        $_promotion->addChild('PromotionId', 'cart-total-discount');
        $_promotion->addChild('Description', 'Discount');
        $_benefit = $_promotion->addChild('Benefit');
        $_fad = $_benefit->addChild('FixedAmountDiscount');
        $_fad->addChild('Amount', abs($discountAmount));
        $_fad->addChild('CurrencyCode', $quote->getBaseCurrencyCode());

        return $xml;
    }

    /**
     * Generate XML with error message in case Calculation Callbacks error
     *
     * @param Exception $e
     */
    public function callbackXmlError(Exception $e)
    {
        // Posible error codes: INVALID_SHIPPING_ADDRESS | INTERNAL_SERVER_ERROR | SERVICE_UNAVAILABLE
        $xmlErrorString = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<OrderCalculationsResponse xmlns="http://payments.amazon.com/checkout/2009-05-15/">'."\n"
            .' <Response>'."\n"
            .'   <Error>'."\n"
            .'     <Code>INTERNAL_SERVER_ERROR</Code>'."\n"
            .'     <Message>[MESSAGE]</Message>'."\n"
            .'   </Error>'."\n"
            .' </Response>'."\n"
            .'</OrderCalculationsResponse>';
        $_errorMsg = $e->getMessage();
        $_errorMessage = "{$_errorMsg}\n\n"
            ."code: {$e->getCode()}\n\n"
            ."file: {$e->getFile()}\n\n"
            ."line: {$e->getLine()}\n\n"
            ."trac: {$e->getTraceAsString()}\n\n";
        if ($this->getDebug()) {
         $debug = Mage::getModel('amazonpayments/api_debug')
            ->setResponseBody($_errorMessage)
            ->setRequestBody(time() .' - error callback response')
            ->save();
        }

        if ($_errorMsg = $e->getMessage() && 0) {
            $xmlErrorString = str_replace('[MESSAGE]', $_errorMsg, $xmlErrorString);
        } else {
            $xmlErrorString = str_replace('[MESSAGE]', 'Error', $xmlErrorString);
        }
        $xml = new SimpleXMLElement($xmlErrorString);
        return $xml;
    }

    /**
     * Get order amazon api
     *
     * @return Mage_AmazonPayments_Model_Api_Cba_Document
     */
    public function getDocumentApi()
    {
        if (is_null($this->getData('document_api'))) {
            $_documentApi = Mage::getModel('amazonpayments/api_cba_document')
                ->setMerchantInfo(array(
                    'merchantIdentifier' => Mage::getStoreConfig('payment/amazonpayments_cba/merchant_tocken'),
                    'merchantName' => Mage::getStoreConfig('payment/amazonpayments_cba/merchant_name'),
                ));
            $this->setData('document_api', $_documentApi);
        }
        return $this->getData('document_api');
    }

    /**
     * Associate Magento real order id with Amazon order id
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_AmazonPayments_Model_Api_Cba
     */
    public function syncOrder($order)
    {
        if ($order->getId()) {
            $this->getDocumentApi()->sendAcknowledgement($order);
        }
        return $this;
    }

    /**
     * Cancel order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_AmazonPayments_Model_Api_Cba
     */
    public function cancel($order)
    {
        $this->getDocumentApi()->cancel($order);
        return $this;
    }

    /**
     * Refund order
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Mage_AmazonPayments_Model_Api_Cba
     */
    public function refund($payment, $amount)
    {
        $this->getDocumentApi()->refund($payment, $amount);
        return $this;
    }

    /**
     * Confirm crating of shipment
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return Mage_AmazonPayments_Model_Api_Cba
     */
    public function confirmShipment($shipment)
    {
        $items = array();
        foreach ($shipment->getAllItems() as $item) {
            /* @var $item Mage_Sales_Model_Order_Shipment_Item */
            if ($item->getOrderItem()->getParentItemId()) {
                continue;
            }
            $items[] = array(
                'id' => $item->getOrderItem()->getExtOrderItemId(),
                'qty' => $item->getQty()
            );
        }

        $carrierName = '';
        $shippingMethod = '';
        $trackNumber = '';
        /**
         * Magento track numbers is not connected with items.
         * Get only first track number
         */
        foreach ($shipment->getAllTracks() as $track) {
            $carrierName = $track->getTitle();
            $trackNumber = $track->getNumber();
            break;
        }
        if (preg_match("/\((.+)\)/", $shipment->getOrder()->getShippingDescription(), $_result)) {
            $shippingMethod = $_result[1];
        }

        $this->getDocumentApi()->confirmShipment(
            $shipment->getOrder()->getExtOrderId(),
            $carrierName,
            $shippingMethod,
            $items,
            $trackNumber
        );
        $shipment->addComment(
            Mage::helper('amazonpayments')->__('Shipment was created with Checkout by Amazon.')
        );
        return $this;
    }

    /**
     * Send shipping track number
     *
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Order_Shipment_Track $track
     * @param Mage_AmazonPayments_Model_Api_Cba
     */
    public function sendTrackingNumber($order, $track)
    {
        return $this;
    }
	
    public function statusUpdate()
    {
	 $this->getDocumentApi()->statusUpdate();
}

}

