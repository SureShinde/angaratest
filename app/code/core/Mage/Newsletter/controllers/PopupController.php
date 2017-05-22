<?php
class Mage_Newsletter_PopupController extends Mage_Core_Controller_Front_Action
{
	
	public function IndexAction(){
		echo $this->getLayout()->createBlock('newsletter/subscribe')->setTemplate('popup/newsletter.phtml')->toHTML();
	}

   	public function PopupHandleAction()
    {
       //$_SESSION['newspopups']=1;
	   setcookie("newspopups",1,time()+3600000,"/");
    }
	
	public function GetnewspopupsessionAction()
	{		
		if(!isset($_COOKIE['newspopups']) && empty($_COOKIE['newspopups']))
 		{
			echo '0';
		}
		else
		{
			echo '1';
		}		
		//echo '1';
	}
	
	public function GetcookiepopupsessionAction()
	{
		if(!isset($_COOKIE['shippingpopups']) && empty($_COOKIE['shippingpopups']) && !isset($_COOKIE['newspopups']) && empty($_COOKIE['newspopups']) && !isset($_COOKIE['cookiepopups']) && empty($_COOKIE['cookiepopups']))
 		{		
			echo '0';
		}else{
			echo '1';
		}
	}
	
	public function SetcookiepopupsessionAction()
	{
		if(!isset($_COOKIE['cookiepopups']) && empty($_COOKIE['cookiepopups'])){
			setcookie("cookiepopups",1,time()+3600000,"/");			
		}
	}
	
	public function showThankYouPopupAction(){
		$this->loadLayout();
		echo $this->getLayout()->createBlock('newsletter/subscribe')->setTemplate('newsletter/thankyou.phtml')->toHTML();
	}
}