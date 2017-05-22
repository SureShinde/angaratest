<?php
class Angara_Abandoncart_Helper_Data extends Mage_Core_Helper_Abstract{
	/*Added by saurabh to generate centralise api key for pardot*/
	public function getApiKeyPardot(){
		$pardot_email = "nagendra.nukala@angara.com";
		$pardot_pass = "Angara1!";
		$pardot_user_key = "beaa6610d5ddaaa0dbafb8f155084916";
		
		// Get Api Key
		$url = "https://pi.pardot.com/api/login/version/3&email=".$pardot_email."&password=".$pardot_pass."&user_key=".$pardot_user_key;
		$getApi = simplexml_load_file($url);
		$apiKey=$getApi->api_key;
		return $apiKey;
	}
	/*End Added by saurabh to generate centralise api key for pardot*/
}


