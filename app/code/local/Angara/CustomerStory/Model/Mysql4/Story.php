<?php
class Angara_CustomerStory_Model_Mysql4_Story extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("customerstory/story", "id");
    }
}?>