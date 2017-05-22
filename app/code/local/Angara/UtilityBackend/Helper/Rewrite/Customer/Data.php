<?php
/*
	S:VA	Helper Rewrite
*/

class Angara_UtilityBackend_Helper_Rewrite_Customer_Data extends Mage_Customer_Helper_Data
{
	//Angara Modification Start
	public function getFBLogoutUrl()
    {
		/*$new_logouturl = 'javascript:customer_logout();';
		return $new_logouturl;*/
		return $this->_getUrl('customer/account/logout');
    }
	//Angara Modification End    
}
