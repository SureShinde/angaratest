<?php

class Runa_Promotions_Model_Events_Cart_Update extends Varien_Object {

    public function sendItemsToRuna($event)
    {

        $_data = $event->getData();
        $_items = $_data['info'];

        $_quote = mage::getModel('checkout/session')->getQuote();
        /* @var $_cart Mage_Sales_Model_Quote */

        $_requestBuilder = mage::getModel('runapromotions/service_request_builder');
        /* @var $_requestBuilder Runa_Promotions_Model_Service_Request_Builder */

        $_runaClient = mage::getModel('runapromotions/events_processor');
        /* @var $_runaClient Runa_Promotions_Model_Events_Processor */

        mage::getModel('core/session')->setCartFromUpdateRequest(true);//seting the cart updated session variable
      
    }

}