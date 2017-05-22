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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Country controller
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_CountryController extends Mage_Core_Controller_Front_Action
{
    public function switchAction()
    {
		$currencyBeforeSwitch = Mage::app()->getStore()->getCurrentCurrencyCode();
		$currencyAfterSwitch = $currencyBeforeSwitch;
        if ($country = (string) $this->getRequest()->getParam('country')) {
		// getcurrency code from country code
			Mage::getModel('countrymapping/country')->saveCountryCode($country);
				$currency = (string)Mage::getModel('countrymapping/country')->getCountryParamCurrency($country);
				if($currency=='') {
					$currency = Angara_Countrymapping_Model_Country::default_countryCurrency;
				}
			    Mage::app()->getStore()->setCurrentCurrencyCode($currency);
				$currencyAfterSwitch = $currency;
        }


		$quote = Mage::getSingleton('checkout/session')->getQuote();		
        if ($quote) 
		{
			$allowedCurrencies[] = $currencyBeforeSwitch;
			$allowedCurrencies[] = $currencyAfterSwitch;
			$baseCurrencyCode=Mage::app()->getStore()->getBaseCurrencyCode();
			$currencyRates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode,  array_values($allowedCurrencies));
			
			$items = $quote->getAllItems();			
			foreach ($items as $item) {
				if ($item->getCustomPrice() ){
					$oldCustomPrice = $item->getCustomPrice();
					$newCustomPrice = ($oldCustomPrice/$currencyRates[$currencyBeforeSwitch])*$currencyRates[$currencyAfterSwitch];
					$item->setCustomPrice($newCustomPrice);
					$item->setOriginalCustomPrice($newCustomPrice);					
				}
			}
			$quote->save();	
        }

		$refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
		$refererUrl = empty($refererUrl) ? Mage::getBaseUrl() : $refererUrl;
		$refererUrl = str_replace('switchCountry','oldCountry',$refererUrl);
		$this->getResponse()->setRedirect($refererUrl);
    }
}
