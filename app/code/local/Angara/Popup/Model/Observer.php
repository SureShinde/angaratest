<?php
class Angara_Popup_Model_Observer extends Mage_Core_Model_Abstract
{
    public function newsletterSubscribed()
    {
		$time = time() + (10*365*24*60* 60);
		setcookie('bottompopupvisitor', $time, $time, '/', '.angara.com');
    }
}