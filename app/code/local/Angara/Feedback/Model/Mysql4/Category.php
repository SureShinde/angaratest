<?php
class Angara_Feedback_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("feedback/category", "category_id");
    }
}