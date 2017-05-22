<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Runa_Promotions_Model_Api_Debuglog {
    const RECORDS_PER_BATCH = 100;

    public function __construct()
    {
        $_authKey = mage::getModel('runapromotions/config_settings')->getClientAuthKey();
        if (mage::app()->getRequest()->get('secret_key') != $_authKey)
        {
            throw new Zend_Exception("Invalid key: (Autorization failed)", 'FATAL');
        }
    }

    /**
     * Enable logging
     * @return SimpleXMLElement
     */
    public function enable_log()
    {
        Mage::getConfig()->saveConfig('sales/runa_promote/debug_mode', 1);
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    /**
     * Enable logging
     * @return SimpleXMLElement
     */
    public function disable_log()
    {
        Mage::getConfig()->saveConfig('sales/runa_promote/debug_mode', 0);
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    /**
     * delete logs
     * @param string $log_type
     * @return <type>
     */
    public function delete_log($log_type)
    {
        switch ($log_type) {
            case 'runa_debug_log':
                $_collection = 'debug_log';
                break;
            case 'runa_service_log':
                $_collection = 'service_log';
                break;

            default:
                $log_type = 'blank';
                throw new Zend_Exception("Invalid Log Type: (Invalid Log type given [$log_type])", 'FATAL');
        }

        //deleting the logs on request from runa
        $logEntryCollection = mage::getModel("runapromotions/$_collection")->getCollection();
        foreach ($logEntryCollection as $_entry)
        {
            $_entry->delete();
        }
        return;
    }

    /**
     * @param integer $start_date
     * @param integer $end_date
     * @param string $log_type
     * $param integer $log_id
     * $param integer $priority
     * $param integer $quote_id
     * @return SimpleXMLElement
     */
    public function download_log($log_type, $start_date, $end_date, $log_id = null, $priority = null, $quote_id = null)
    {

        $_dateFilter = array(
            'from' => $start_date,
            'to' => $end_date
        );


        switch ($log_type) {
            case 'runa_debug_log':

                $_logEntryCollection = mage::getModel("runapromotions/debug_log")
                                ->getCollection();

                $_logEntryCollection->addFieldToFilter('timestamp', $_dateFilter);

                if ($priority)
                {
                    $_logEntryCollection->addFieldToFilter('priority', array('lteq' => $priority));
                }

                $_logName = 'debug';

                break;
            case 'runa_service_log':

                $_logEntryCollection = mage::getModel("runapromotions/service_log")
                                ->getCollection();
                $_logEntryCollection->addFieldToFilter('logged_at', $_dateFilter);

                if ($quote_id)
                {
                    $_logEntryCollection->addFieldToFilter('quote_id', array('eq' => $quote_id));
                }

                 $_logName = 'service';

                break;

            default:
                $log_type = 'blank';
                throw new Zend_Exception("Invalid Log Type: (Invalid Log type given [$log_type])", 'FATAL');
        }



        //if order id is given then paginate
        if (isset($log_id))
        {
            if (!is_numeric($log_id))
            {
                throw new Zend_Exception('Argument (log_id) must be of type (integer)', 'FATAL');
            }

            $_logIdFilter = array(
                'gt' => $log_id
            );

            $_logEntryCollection->addFieldToFilter('log_id', $_logIdFilter);
        }


        $_actual_count = $_logEntryCollection->count();

        $_logEntryCollection->setPageSize(Runa_Promotions_Model_Api_Orders::RECORDS_PER_BATCH);


        $_logEntries = array();
        $_logRecords = array();
        foreach ($_logEntryCollection as $_entry)
        {
            $_tmp = $_entry->getData();
            foreach ($_tmp as $_k => $_val)
            {
                if ($_k == 'request_xml' || $_k == 'response_xml' || $_k == 'message')
                {
                    $_tmp[$_k] = $this->_wrapInCDATA($_tmp[$_k]);
                }
            }
            $_logRecords['log-entry'][] = $_tmp;
        }

        $_logEntries['log-entries'] = $_logRecords;
        $_xmlCreator = mage::getModel('runapromotions/api_response_processor');
        /* @var $_xmlCreator Runa_Promotions_Model_Api_Response_Processor */

        if ($_actual_count > Runa_Promotions_Model_Api_Orders::RECORDS_PER_BATCH)
        {
            $_logEntries['status'] = 'PARTIAL';
        } else
        {
            $_logEntries['status'] = 'complete';
        }

        $_logEntries['runa-client-version'] = mage::getModel('runapromotions/config_settings')->getRunaModuleInfo()->version;

        $_xmlLog = $_xmlCreator->toXML($_logEntries, "$_logName-log-download");
        $_xmlLog = simplexml_load_string($_xmlLog);

        return $_xmlLog;
    }

    private function _wrapInCDATA($text)
    {
        return "<![CDATA[$text]]>";
    }

}

