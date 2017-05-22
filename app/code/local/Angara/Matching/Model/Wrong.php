<?php

class Angara_Matching_Model_Wrong extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
		//echo "hiii";exit;
		parent::_construct();
        $this->_init('matching/wrong');
		
    }
}

?>