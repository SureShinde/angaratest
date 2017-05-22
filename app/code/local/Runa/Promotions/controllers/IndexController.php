<?php

class Runa_Promotions_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction()
    {
        
        
        $_order = mage::getModel('sales/order');
        $_incrementId = '100000058';
        $_order->loadByIncrementId($_incrementId);
        $_requestBuilder = mage::getModel('runapromotions/service_request_builder');
        $_requestXml = $_requestBuilder->buildOrderRequest($_order);
        $_requestBuilder = mage::getModel('runapromotions/service_request_builder');
        /* @var $_requestBuilder Runa_Promotions_Model_Service_Request_Builder */

        $_requestXml = $_requestBuilder->buildOrderRequest($_order);

        $_runaClient = mage::getModel('runapromotions/events_processor');
        /* @var $_runaClient Runa_Promotions_Model_Events_Processor */
        $_runaClient->buyCart($_requestXml);
        die;

        $settings = mage::getModel('runapromotions/config_settings');
        echo $settings->getClientAuthKey();
        die;

        $client = new Runa_Promotions_Model_Service_Client();
        var_dump($client->shareAuthKeyWithRuna());
        return;

        $client = new Runa_Promotions_Model_Api_Orders();
        $result = $client->order_download();

        echo ($result->saveXml());
        return;
        $settings = mage::getModel('runapromotions/config_settings');
        /* @var $settings  Runa_Promotions_Model_Config_Settings */

        echo 'http://devshashi.runa.com/runa/api?key=' . $settings->getClientAuthKey() . '&runa_remote_type=orders';
        $client = new Zend_Rest_Client('http://devshashi.runa.com/runa/api?key=' . $settings->getClientAuthKey() . '&runa_remote_type=orders');

        $pageNum = 1;
        $pageSize = 10;

        $result = $client->fetch()->get();
        var_dump($result);
        die;


//        $client = new Zend_XmlRpc_Client('http://devshashi.runa.com/api/xmlrpc/');
//        $session = $client->call('login', array('shashi', 'abc123'));
//        $result = $client->call('call', array($session, 'catalog_product.list'),array('filters'=>array()));
        foreach ($result as $prod)
        {
            var_dump($prod);
        }



        $this->loadLayout();
        $this->renderLayout();
    }

}