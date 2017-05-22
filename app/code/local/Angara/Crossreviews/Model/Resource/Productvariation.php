<?php
class Angara_Crossreviews_Model_Resource_Productvariation extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('crossreviews/productvariation', 'id');
    }
}
?>