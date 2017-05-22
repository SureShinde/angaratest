<?php

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class Runa_Promotions_Model_Config_Settings {
    const API_TIME_OUT =2;

    /**
     * Returns the merchent id
     * 
     * @return string
     */
    public function getMerchantId() {
        return Mage::getStoreConfig('sales/runa_promote/merchant_id_' . $this->getMode());
    }

    /**
     * Returns the service domain for runa webservice from the system configuration
     * 
     * @return string
     */
    public function getServiceDomain() {
        $mode = Mage::getStoreConfig('sales/runa_promote/mode');

        switch ($mode) {
            case "PROD":
                return "cheshire-production.runa.com";
            case "STAGE":
                return "cheshire-staging.runa.com";
            case "DEV":
                return "cheshire-sandbox.runa.com";
            case "CUSTOM_DOMAIN":
                return Mage::getStoreConfig('sales/runa_promote/custom_domain');
        }
    }

    public function getMode() {
        $mode = Mage::getStoreConfig('sales/runa_promote/mode');
        return $mode;
    }

    /**
     * Returns the service domain with 'http://'
     * 
     * @return string
     */
    public function getServiceUrl() {
        return 'http://' . $this->getServiceDomain();
    }

    /**
     * Returns the authentication key for 2 way authorizations
     * 
     * @return string
     */
    public function getClientAuthKey() {
        return Mage::getStoreConfig('sales/runa_promote/connect_key');
    }

    /**
     * Returns true if the component is configured for debug mode
     * 
     * @return bool
     */
    public function getDebugMode() {
        return Mage::getStoreConfig('sales/runa_promote/debug_mode') ? true : false;
    }

    /**
     * Stands for Site_ConsumerID. Runa boot code in Merchant site will create a first party cookie ( with site_cid)
     * every time a new user visits the site. This site_cid is used to uniquely identify a user in Runa backend.
     * 
     * @return string
     */
    public function getSiteCid() {
        return Mage::getModel('core/cookie')->get('site_cid');
    }

    /**
     * Runa system creates a first party cookie (runa_oreo) in merchant site visitor’s browser session. This cookie will be set to
     * either true or false. Only when runa_oreo cookie is set to true, cart API should be called. This feature will allow runa and
     * merchant to turn on and off runa system from runa dashboard
     * 
     * @return string
     */
    public function getRunaOreo() {
        return Mage::getModel('core/cookie')->get('runa_oreo');
    }

    public function getRunaModuleInfo() {
        $modules = Mage::getConfig()->getNode('modules')->children();
        return $modules->Runa_Promotions;
    }

    public function showLineItemsDiscounts() {
        return Mage::getStoreConfig('sales/runa_promote/show_line_item_discount');
    }

    public function getApiTimeOut() {
        $timeOut = Mage::getStoreConfig('sales/runa_promote/runa_api_timeout');
        if (!$timeOut) {
           return self::API_TIME_OUT;
        }
        return $timeOut;
    }

}
