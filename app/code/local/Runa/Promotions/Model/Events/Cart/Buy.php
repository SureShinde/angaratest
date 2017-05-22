<?php

class Runa_Promotions_Model_Events_Cart_Buy extends Varien_Object {

    public function buyCart($event)
    {

        $_order = mage::getModel('sales/order');
        $_incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();

        $_order->loadByIncrementId($_incrementId);

        $_requestBuilder = mage::getModel('runapromotions/service_request_builder');
        /* @var $_requestBuilder Runa_Promotions_Model_Service_Request_Builder */

        $_requestXml = $_requestBuilder->buildOrderRequest($_order);

        $_runaClient = mage::getModel('runapromotions/events_processor');
        /* @var $_runaClient Runa_Promotions_Model_Events_Processor */
        $_runaClient->buyCart($_requestXml);
        //added step to clear any session data tat might have be reinitialized
        Mage::getSingleton('checkout/session')->clear();
    }

}