<?php
class Angara_Testimonials_Model_Resource_Reviewdetail extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('testimonials/reviewdetail', 'detail_id');
    }
}
?>