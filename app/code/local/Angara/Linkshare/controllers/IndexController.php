<?php
class Angara_Linkshare_IndexController extends Mage_Core_Controller_Front_Action {        

	public function indexAction() {
		$url = '';
		$siteId = '';
		$url = $this->getRequest()->getParam('url');
		$siteId = $this->getRequest()->getParam('siteID');
		$timeStamp = time();
		setcookie('siteID',$siteId.'hashhash'.$timeStamp,$timeStamp+86400*30);
		if($url==='' || stristr($url, 'angara') === FALSE) {
			$url = 'http://www.angara.com?'. $_SERVER['QUERY_STRING']. "&utm_campaign=ls";
		}else {
			$url = $url."?". $_SERVER['QUERY_STRING']. "&utm_campaign=ls";
		}
		header("Status: 301");
		header("Location: ".$url) ;
		exit;
	}
}
?>