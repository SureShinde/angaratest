<?php
class Mage_Shipping_PopupController extends Mage_Core_Controller_Front_Action
{

   public function PopupHandleAction()
    {
       //$_SESSION['newspopups']=1;
	   setcookie("shippingpopups",1,time()+3600000,"/");
    }
	public function GetshippingpopupsessionAction()
	{
		if(!isset($_COOKIE['shippingpopups']) && empty($_COOKIE['shippingpopups']))
 		{
			echo 0;
		}
		else
		{
			echo "1";
		}
	}
}