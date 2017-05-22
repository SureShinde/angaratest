<?php

class Runa_Promotions_Model_Service_Client {

    private $_httpClient;
    private $_module;
    private $_serviceBaseUrl;
    public $serviceUrl;
    public $clientError = false;

    function __construct()
    {

        $settings = mage::getModel('runapromotions/config_settings');
        
        $this->_serviceBaseUrl =  $settings->getServiceUrl();

        try {
            //setting up the http client for api communication
            $client = new Zend_Http_Client($this->_serviceBaseUrl,
                            array(
                                'maxredirects' => 0,
                                'timeout' => $settings->getApiTimeOut(),
                                'keepalive' => true
                    ));

            $client->setMethod('POST');
        } catch (exception $e) {

            Mage::getSingleton('runapromotions/log_debug')->err(new Zend_Exception($e->getMessage()));
            $this->clientError = true;
            return $this;
        }

        $this->_httpClient = $client;
        $this->_httpClient->setUri($this->_serviceBaseUrl);

        $modules = Mage::getConfig()->getNode('modules')->children();
        $this->_module = $modules->Runa_Promotions; //fetching runa promotions module details
    }

    public function shareAuthKeyWithRuna()
    {
        $_randAuthKey = Mage::helper('runapromotions')->createRandomString();

        Mage::getConfig()->saveConfig('sales/runa_promote/connect_key', $_randAuthKey);
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        $this->setServicePath('/io/miva-key-upload');

        $settings = mage::getModel('runapromotions/config_settings');
        /* @var $settings  Runa_Promotions_Model_Config_Settings */

        $this->_httpClient->setParameterGet('merchant-id', $settings->getMerchantId());
        $this->_httpClient->setParameterPost('key', $settings->getClientAuthKey());

        $_baseUrl = mage::getBaseUrl() . "runa/api";
        $this->_httpClient->setParameterPost('url', $_baseUrl);


        $_logEntry = mage::getModel('runapromotions/service_log');
        $_logEntry->setQuoteId(mage::getSingleton('checkout/session')->getQuoteId());
        $_logEntry->setRequestType("Send Key");
        $_logEntry->setRequestXml($settings->getClientAuthKey());
        $_logEntry->setRequestXml($settings->getClientAuthKey());
        try {

            $response = $this->_httpClient->request();
            $_logEntry->setResponseXml($response->getStatus());
            $_logEntry->save();
        } catch (exception $e) {

            $_logEntry->setErrorMessage($e->getMessage());
            $_logEntry->save();
            Mage::getSingleton('runapromotions/log_debug')->err(new Zend_Exception($e->getMessage()));
            return false;
        }

        if ($response->getStatus() == '200')
        {
            return true;
        }
    }

    public function setServicePath($path)
    {
        if ($this->clientError)
        {
            return false;
        }

        $this->serviceUrl = $this->_serviceBaseUrl . "$path";
        $this->_httpClient->setUri($this->serviceUrl);
    }

    function send($xml)
    {
        $settings = mage::getModel('runapromotions/config_settings');
        /* @var $settings  Runa_Promotions_Model_Config_Settings */

        $this->_httpClient->setParameterPost('merchant_id', $settings->getMerchantId());
        $this->_httpClient->setParameterPost('site_cid', $settings->getSiteCid());

        $this->_httpClient->setParameterPost('xml', $xml);

        try {
            $response = $this->_httpClient->request();
            $_reponseXml = new DOMDocument; //logging the response
            $_reponseXml->loadXML($response->getBody());

            if (trim($response->getBody()) == '')
            {
                throw new Zend_Exception("Blank response recieved");
            }
        } catch (exception $e) {

            Mage::getSingleton('runapromotions/log_debug')->err(new Zend_Exception($e->getMessage()));
            $this->clientError = true;

            return $e;
        }

        return $response;
    }

    /**
     * tests service connectivity
     * @return <type>
     */
    function canConnect()
    {

        if ($this->_httpClient->request()->getStatus() != '200')
        {
            return false;
        }

        return true;
    }

}