<?php

class Runa_Promotions_Model_Events_Processor {

    private $_runaClient;
    public $_runaCartXml = "";

    public function __construct()
    {
        $_client = mage::getModel('runapromotions/service_client');
        /* @var $_client Runa_Promotions_Model_Service_Client */
        $this->_runaClient = $_client;
    }

    public function addProduct($xml)
    {

        $this->_runaClient->setServicePath('/io/add_product');

        $xml = "<product>$xml</product>";

        return $this->_sendRequest($xml, 'ADD Product');
    }

    public function removeProduct($xml)
    {

        $this->_runaClient->setServicePath('/io/remove_product');

        $xml = "<product>$xml</product>";

        return $this->_sendRequest($xml, 'Delete Product');
    }

    public function updateProduct($xml)
    {

        $this->_runaClient->setServicePath('/io/update_product');

        $xml = "<product>$xml</product>";

        return $this->_sendRequest($xml, 'Update Product');
    }

    public function buyCart($xml)
    {

        return $this->sendCart($xml, '/io/purchase_cart', 'Buy Cart');
    }

    public function sendCart($xml, $servicePath = null, $requestType = null)
    {

        $this->_runaClient->setServicePath('/io/show_cart');

        //overriding the servie path from argument
        if ($servicePath)
        {
            $this->_runaClient->setServicePath($servicePath);
        }

        $_requestType = "Show Cart";

        if ($requestType)
        {
            $_requestType = $requestType;
        }

        //if the user updated his cart then trigger the update cart request
        if (mage::getModel('core/session')->getCartFromUpdateRequest())
        {
            $this->_runaClient->setServicePath('/io/update_cart');
            mage::getModel('core/session')->setCartFromUpdateRequest(false);
            $_requestType = "Update Cart";
        }

        $_result = $this->_sendRequest($xml, $_requestType);
        $_response = $_result->getResponse();

        if ($_result->getStatus() != "200")
        {
            return false;
        }

        $this->_runaCartXml = simplexml_load_string($_response->getBody());

        return true;
    }

    /**
     *
     * @return DOMDocument
     */
    public function getRunaCartXml()
    {

        return $this->_runaCartXml;
    }

    private function _sendRequest($inputXml, $requestType)
    {
        $_result = new Varien_Object();
        if ($this->_runaClient->clientError)
        {

            $_result->setStatus('CLIENT_ERROR');
            $_result->setResponse('failed to initiate client');
            return $_result;
        }


        $_headerXml = '<?xml version="1.0" encoding="UTF-8"?>';
        $inputXml = $_headerXml . $inputXml;


        $_reqXmlElement = simplexml_load_string($inputXml);
        $_reqXmlElement->addChild('client-info');
        $_reqXmlElement->{'client-info'}->addChild('ecommerce-platform-name', 'magento');
        $_modVersion = mage::getModel('runapromotions/config_settings')->getRunaModuleInfo()->version;
        $_reqXmlElement->{'client-info'}->addChild('runa-module-version', $_modVersion);
        $_reqXmlElement->{'client-info'}->addChild('api-version', '1.6.1');


        $_inputXml = new DOMDocument; //logging the input
        $_inputXml->loadXML($_reqXmlElement->saveXML());
        $_inputXml->formatOutput = true;

        $_response = $this->_runaClient->send($_inputXml->saveXML());

        $_logEntry = mage::getModel('runapromotions/service_log');
        $_logEntry->setQuoteId(mage::getSingleton('checkout/session')->getQuoteId());
        $_logEntry->setRequestType($requestType);
        $_logEntry->setRequestXml($_inputXml->saveXML());

        if ($_response instanceof Zend_Http_Client_Adapter_Exception || $this->_runaClient->clientError)
        {
            $_reponseMessage = "Status:" . $_response->getCode() . " (" . $_response->getMessage() . ")";
            mage::log($requestType . ":" .
                            "\n\n******** (Request) **********:" . $this->_runaClient->serviceUrl . "  \n" . $_inputXml->saveXML() .
                            "\n******** (Response) *********:\n" . $_reponseMessage,
                            1, 'runa.log');

            $_logEntry->setResponseXml($_response->getCode());
            $_logEntry->setErrorMessage($_response->getMessage());
            $_logEntry->save();

            $_result->setStatus($_response->getCode());
            $_result->setResponse($_response->getMessage());

            return $_result;
        }

        $_reponseXml = new DOMDocument; //logging the response
        $_reponseXml->loadXML($_response->getBody());
        $_reponseXml->formatOutput = true;

        $_logEntry->setResponseXml($_reponseXml->saveXML());
        $_logEntry->save();

        $_reponseMessage = "Status:" . $_response->getStatus() . " (" . $_response->getMessage() . ")\n" . $_reponseXml->saveXML();

        mage::log($requestType . ":" .
                        "\n\n******** (Request) **********:" . $this->_runaClient->serviceUrl . "  \n" . $_inputXml->saveXML() .
                        "\n******** (Response) *********:\n" . $_reponseMessage,
                        1, 'runa.log');

        $this->_setResponseCookies($_reponseXml);


        $_result->setStatus($_response->getStatus());
        $_result->setResponse($_response);

        return $_result;
    }

    private function _setResponseCookies(DOMDocument $reponseXmlDom)
    {

        $_cookiesToSet = $reponseXmlDom->getElementsByTagName('cookie');

        foreach ($_cookiesToSet as $_cookieLocal)
        {
            $_cookieToSet = array();

            if ($_cookieLocal->childNodes->length)
            {
                foreach ($_cookieLocal->childNodes as $_cookieData)
                {
                    $_cookieToSet[$_cookieData->nodeName] = $_cookieData->nodeValue;
                }
            }

            //setting the cookies tat runa wants to set on client
            Mage::getModel('core/cookie')->set($_cookieToSet['name'],
                    $_cookieToSet['value'],
                    $_cookieToSet['duration']
            );
        }
    }

}