<?php

class Runa_Promotions_Model_Events_Item_Delete extends Varien_Object {

    public function sendItemToRuna($event)
    {


        $_requestBuilder = mage::getModel('runapromotions/service_request_builder');
        /* @var $_requestBuilder Runa_Promotions_Model_Service_Request_Builder */

        $_item = $event->getQuoteItem();

        $_requestXml = $_requestBuilder->getRequestXmlForItem($_item);

        $_runaClient = mage::getModel('runapromotions/events_processor');
        /* @var $_runaClient Runa_Promotions_Model_Events_Processor */
        $_runaClient->removeProduct($_requestXml);
    }

}