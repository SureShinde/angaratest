<?php

class Runa_Promotions_Model_Mysql4_Debug_log extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct()
    {
        $this->_init('runapromotions/debug_log','log_id');
    }

}

