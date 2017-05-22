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
 * Country dropdown block
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Block_Country extends Mage_Core_Block_Template
{

	protected function _construct()
    {
		$request = Mage::app()->getRequest();
        $agent = $request->getServer('HTTP_USER_AGENT');
        $this->addData(array(
            'cache_lifetime'    => 86400,
			'cache_key'			=> Mage::getModel('countrymapping/country')->getCountryParamCode().'~'.$agent,
        ));
    }
    /**
     * Retrieve Country Swith URL
     *
     * @return string
     */
    public function getSwitchUrl()
    {
        return $this->getUrl('directory/country/switch');
    }

    /**
     * Return URL for specified Country to switch
     *
     * @param string $code Country code
     * @return string
     */
    public function getSwitchCountryUrl($code)
    {
        return Mage::helper('directory/url')->getSwitchCountryUrl(array('country' => $code));
    }
	
	
	public function getCountryCount()
	{
		 return count($this->getCountries());
	}
	
	public function getCountries()
	{
        $countries = $this->getData('countries');
        if (is_null($countries)) {
            $countries = array();
            $codes = Mage::app()->getStore()->getAvailableCountryCodes();
            if (is_array($codes) && count($codes) > 1) {
                foreach ($codes as $code) {
					// get country name from countrymapping model
                    $countries[$code] = Mage::getModel('countrymapping/country')->getCountryParamName($code);
					if($countries[$code] == 'South Georgia and the South Sandwich Islands'){
						$countries[$code] = 'S.G.S.S.I.';
					}
                }
            }

            $this->setData('countries', $countries);
        }
        return $countries;	
	}
	
    public function getCurrentCountryCode()
    {
        if (is_null($this->_getData('current_country_code'))) {
            $this->setData('current_country_code', Mage::getModel('countrymapping/country')->getCountryParamCode());
        }
        return $this->_getData('current_country_code');
    }	
	
    public function getCurrentCountryName()
    {
        if (is_null($this->_getData('current_country_name'))) {
            $this->setData('current_country_name', Mage::getModel('countrymapping/country')->getCountryParamName());
        }
        return $this->_getData('current_country_name');
    }		

	public function getCurrencyCode($code)
    {
		return Mage::getModel('countrymapping/country')->getCountryParamCurrency($code);
    }
}