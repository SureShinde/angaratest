<?php

class Angara_Wishlist_Block_Index extends Mage_Core_Block_Template
{
	/*
		S:VA	Login or Register Ajax Popup
	*/
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('angara_wishlist/login.phtml');
    }

    protected function _toHtml() {
        return parent::_toHtml();
    }
    
    
}
