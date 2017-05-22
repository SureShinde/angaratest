<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Runa_Promotions_Model_Api_Orders extends Mage_Sales_Model_Order_Api_V2 {

    private $_collectionTotal;

    const RECORDS_PER_BATCH = 100;

    /**
     * Retrieve list of orders by filters
     * 
     * @param integer $start_date
     * @param integer $end_date
     * @param integer $order_id
     * @return SimpleXMLElement
     */
    public function order_download($start_date, $end_date, $order_id = null)
    {

        $_authKey = mage::getModel('runapromotions/config_settings')->getClientAuthKey();
        if (mage::app()->getRequest()->get('secret_key') != $_authKey)
        {
            throw new Zend_Exception("Invalid key: (Autorization failed)", 'FATAL');
        }

        $collection = Mage::getModel("sales/order")->getCollection()
                        ->addAttributeToSelect('*');

        /*
          $_dateFilter = array(
          'from' => $this->_stringToTime($start_date),
          'to' => $this->_stringToTime($end_date)
          );
         */
        $_dateFilter = array(
            'from' => $start_date,
            'to' => $end_date
        );

        $collection->addAttributeToFilter('created_at', $_dateFilter);

        //if order id is given then paginate
        if (isset($order_id))
        {
            if (!is_numeric($order_id))
            {
                throw new Zend_Exception('Argument (order_id) must be of type (integer)', 'FATAL');
            }

            $_orderTmp = mage::getModel('sales/order')->loadByIncrementId($order_id);

            if (!$_orderTmp->getId())
            {
                throw new Zend_Exception("order_id ($order_id) does not exist", 'FATAL');
            }

            $_orderIdFilter = array(
                'gt' => $order_id
            );

            $collection->addAttributeToFilter('increment_id', $_orderIdFilter);
        }

        $collectionTmp = clone $collection;

        $this->_collectionTotal = $collectionTmp->count();

        $collection->setPageSize(self::RECORDS_PER_BATCH);
        $result = array();

        $_attributes = array('status');
        $this->_attributesMap['order'] =
                array(
                    'datetime' => 'created_at',
                    'status' => 'status',
                    'order-id' => 'increment_id'
        );

        $_orders = array(); 
        foreach ($collection as $order)
        {
            $_orderAttribs = $this->_getAttributes($order, 'order', $_attributes);

            $_orderAttribs['datetime'] = strtotime($order->created_at);
            $_orderAttribs['items'] = $this->_getOrderItems($order);
            $_orderAttribs['charges'] = $this->_getOrderShippingAndTaxCharges($order);

            if ($this->_getNonRunaDiscounts($order) != false)
            {
                $_orderAttribs['discounts-non-runa'] = $this->_getNonRunaDiscounts($order);
            }
            if ($this->_getRunaDiscounts($order) != false)
            {
                $_orderAttribs['discounts-runa'] = $this->_getRunaDiscounts($order);
            }
            $_orderAttribs['totals'] = $this->_getTotals($order);
            $_orderAttribs['total'] = $this->_getTotal($order);

            $_orders['order'][] = $_orderAttribs;
        }

        $result['orders'] = $_orders;
        $result['status'] = 'COMPLETE';

        if ($this->_collectionTotal > self::RECORDS_PER_BATCH)
        {
            $result['status'] = 'PARTIAL';
        }

        $_xmlCreator = mage::getModel('runapromotions/api_response_processor');
        /* @var $_xmlCreator Runa_Promotions_Model_Api_Response_Processor */
        $_xmlOrders = $_xmlCreator->toXML($result, 'order-download');

        $_xmlOrders = simplexml_load_string($_xmlOrders);

        return $_xmlOrders;
    }


    private function _getOrderItems(Mage_Sales_Model_Order $order)
    {
        $_itemsTemp = array();
        $_attributes = array('name');
        $this->_attributesMap['item'] =
                array(
                    'name' => 'name',
                    'product-id' => 'product_id',
                    'unit-price' => 'base_price',
                    'quantity' => 'qty_ordered'
        );

        $_items = array();
        $quote = mage::getModel('sales/quote')->load($order->getQuoteId());

        foreach ($order->getAllItems() as $_item)
        {
            if ($_item->getParentItemId())
            {
                continue;
            }

            $_itemInfo = $this->_getAttributes($_item, 'item', $_attributes);
            $_itemOptions = $this->_getItemOptions($_item, $quote);
            $_itemInfo['attributes'] = $_itemOptions;
            $_items[] = $_itemInfo;
        }

        $_itemsTemp['item'] = $_items;
        return $_itemsTemp;
    }

    private function _getItemOptions(Mage_Sales_Model_Order_Item $item, $quote)
    {
        $_quoteItem = $quote->getItemById($item->getQuoteItemId());
        if (!$_quoteItem)
        {
            return $_attribs['attribute'] = array();
        }
        $_requestBuilder = mage::getModel('runapromotions/service_request_builder');
        /* @var $_requestBuilder Runa_Promotions_Model_Service_Request_Builder */

        $_options = $_requestBuilder->_getQuoteItemAttributes($_quoteItem);
        // var_dump( $_options);die;
        $_optsTmp = array();
        foreach ($_options as $_option)
        {
            $_optTmp = array();
            $_optTmp['name-prompt'] = $_option->getName();
            $_optTmp['value'] = $_option->getValue();
            $_optTmp['value-prompt'] = $_option->getvalueLabel();
            $_optTmp['price'] = $_option->getPrice();

            $_optionVal = mage::getModel('catalog/product_option_value')->load($_option['value']);

            $_optsTmp[] = $_optTmp;
        }
        $_attribs['attribute'] = $_optsTmp;

        return $_attribs;
    }

    private function _getOrderShippingAndTaxCharges(Mage_Sales_Model_Order $order)
    {
        $_chargesTmp = array();

        $_chargeTmp = array();
        $_chargeTmp['type'] = 'shipping';
        $_chargeTmp['description'] = $order->shipping_description;
        $_chargeTmp['amount'] = $order->base_shipping_amount;

        $_chargesTmp[] = $_chargeTmp;

        $_taxTmp = array();
        $_taxTmp['type'] = 'tax';
        $_taxTmp['description'] = 'sales tax';
        $_taxTmp['amount'] = $order->base_tax_amount;

        $_chargesTmp[] = $_taxTmp;

        $_chargeAttribs['charge'] = $_chargesTmp;

        return $_chargeAttribs;
    }

    private function _getNonRunaDiscounts(Mage_Sales_Model_Order $order)
    {
        if ($order->base_discount_amount <= 0)
        {
            return false;
        }

        $_discountsTmp = array();

        $_disountTmp = array();
        $_disountTmp['type'] = 'coupon';
        $_disountTmp['description'] = 'coupon';
        $_disountTmp['amount'] = $order->base_discount_amount;

        $_discountsTmp[] = $_disountTmp;

        $_discountAttribs['discount'] = $_discountsTmp;

        return $_discountAttribs;
    }

    private function _getRunaDiscounts(Mage_Sales_Model_Order $order)
    {

        if ($order->runa_discount_total == 0)
        {
            return false;
        }
        $_discountsRunaTmp = array();

        $_disountRunaTmp = array();
        $_disountRunaTmp['type'] = 'recapture';
        $_disountRunaTmp['description'] = 'Recapture';
        $_disountRunaTmp['amount'] = -$order->runa_discount_total;

        $_discountsRunaTmp[] = $_disountRunaTmp;

        $_discountRunaAttribs['discount'] = $_discountsRunaTmp;

        return $_discountRunaAttribs;
    }

    private function _getTotals(Mage_Sales_Model_Order $order)
    {
        $_totalsTmp = array();

        $_totalTmp = array();
        $_totalTmp['items'] = $order->base_subtotal;
        $_totalTmp['charges'] = $order->base_shipping_amount + $order->base_tax_amount;
        $_totalTmp['discounts-non-runa'] = $order->base_discount_amount;
        $_totalTmp['discounts-runa'] = abs($order->runa_discount_total); 

        $_totalsTmp[] = $_totalTmp;

        return $_totalsTmp;
    }

    private function _getTotal(Mage_Sales_Model_Order $order)
    {
        return $order->grand_total;
    }

}

