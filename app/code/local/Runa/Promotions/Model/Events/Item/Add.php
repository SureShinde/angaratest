<?php

class Runa_Promotions_Model_Events_Item_Add {
    const VALID_ROUTE_KEY = 'checkout_cart_add';

    public function sendItemToRuna($event)
    {
        $routeKey = mage::helper('runapromotions')->getRouteKey();
        if ($routeKey != self::VALID_ROUTE_KEY)
        {
            return;
        }

        $_item = $event->getItem();

        if ($_item->getParentItemId())
        {
            return;
        }

        $_requestBuilder = mage::getModel('runapromotions/service_request_builder');
        /* @var $_requestBuilder Runa_Promotions_Model_Service_Request_Builder */

        $_qtyToSet = mage::registry('runa_item_qty_for_id_' . $_item->getId());
        if ($_qtyToSet)
        {
            $_item->setQty($_qtyToSet);
        }

        $itemProcessedIdentifier ='runa_item_id_' . $_item->getId().'_processed';
        //if a new item is not being inserted exit
        if (!$_item->getIsNew() || mage::registry($itemProcessedIdentifier))
        {
            return;
        }

        mage::register($itemProcessedIdentifier,true);
        $_requestXml = $_requestBuilder->getRequestXmlForItem($_item);

        $_runaClient = mage::getModel('runapromotions/events_processor');
        /* @var $_runaClient Runa_Promotions_Model_Events_Processor */
        $_runaClient->addProduct($_requestXml);
    }

    public function registerItemQuantity($event)
    {

        $_item = $event->getItem();

        if ($_item->getId())
        {
            $_oldItem = mage::getModel('sales/quote_item')->load($_item->getId());
            
            $_qtyToSet = mage::registry('runa_item_qty_for_id_' . $_item->getId());
            if (!isset($_qtyToSet))
            {
                mage::register('runa_item_qty_for_id_' . $_item->getId(), $_item->getQty() - $_oldItem->getQty());
            }

            $_hasQtyChanged = $_item->getQty() - $_oldItem->getQty();
            if ($_hasQtyChanged > 0)
            {
                $_item->setIsNew(true);
            }
        } else
        {
            $_item->setIsNew(true);
        }
        
    }

}