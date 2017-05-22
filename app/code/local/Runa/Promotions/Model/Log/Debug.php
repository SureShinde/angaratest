<?php

class Runa_Promotions_Model_Log_Debug extends Zend_Log {
    const CLEAN_DAYS_LOW = 3;
    const CLEAN_DAYS_HIGH = 7;
    const CLEAN_ROWS_LOW = 3000;

    /**
     * Call Zend_Log contructor with db writer attached to table runa_debug_log
     * 
     * If debug_mode configuration setting is on, log all messages to the db.  Otherwise, only priority ERR and higher will be logged.
     */
    public function __construct()
    {
        parent::__construct();

        $res = Mage::getSingleton('core/resource');
        $writer = new Zend_Log_Writer_Db($res->getConnection('runapromotions_setup'), $res->getTableName('runapromotions/debug_log'));

        $this->addWriter($writer);


        if (!Mage::getSingleton('runapromotions/config_settings')->getDebugMode())
        {
            $this->addFilter(new Zend_Log_Filter_Priority(Zend_Log::ERR));
        }
    }

    /**
     * Clean log entries
     * 
     * Remove error messages or higher priority after 7 days.  Remove lower priority messages after 3 days, or
     * when there are more than 3000 messages.
     */
    public function clean()
    {
        $db = Mage::getSingleton('core/resource')->getConnection('runapromotions_setup');
        $tblName = Mage::getSingleton('core/resource')->getTableName('runapromotions/debug_log');

        $lowPriorityTime = self::CLEAN_DAYS_LOW * 60 * 60 * 24;
        $lowPriorityMaxRows = self::CLEAN_ROWS_LOW;
        $highPriorityTime = self::CLEAN_DAYS_HIGH * 60 * 60 * 24;

        $logIds = array();

        // Low priority message ids to be deleted because of # of rows
        $select = $db->select()
                        ->from(
                                array('log' => $tblName),
                                array('log_id' => 'log.log_id'))
                        ->where('log.priority > ?', Zend_Log::ERR)
                        ->limit(100, $lowPriorityMaxRows)
                        ->order('log.log_id DESC');

        $query = $db->query($select);
        while ($row = $query->fetch())
        {
            $logIds[] = $row['log_id'];
        }

        if (count($logIds) < 100)
        {
            // Low priority message ids to be deleted because of age
            $select->reset(Zend_Db_Select::LIMIT_OFFSET)
                    ->where('log.timestamp < ?', gmdate('Y-m-d H:i:s', time() - $lowPriorityTime))
                    ->limit(100);

            $query = $db->query($select);
            while ($row = $query->fetch())
            {
                $logIds[] = $row['log_id'];
            }
        }

        // High priority message ids to be deleted
        $select->reset(Zend_Db_Select::LIMIT_OFFSET)
                ->reset(Zend_Db_Select::WHERE)
                ->where('log.timestamp < ?', gmdate('Y-m-d H:i:s', time() - $highPriorityTime))
                ->where('log.priority <= ?', Zend_Log::ERR)
                ->limit(100);

        $query = $db->query($select);
        while ($row = $query->fetch())
        {
            $logIds[] = $row['log_id'];
        }

        $db->delete($tblName, $db->quoteInto('log_id IN(?)', $logIds));
    }

}

