<?php
class Angara_Reminder_Model_Mysql4_Reminder extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('reminder/reminder', 'reminder_id');
    }
}
