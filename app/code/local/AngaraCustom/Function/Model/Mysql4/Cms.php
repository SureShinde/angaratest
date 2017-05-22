<?php

class AngaraCustom_Function_Model_Mysql4_Cms extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('cms/cms_page', 'page_id');
    }
}
