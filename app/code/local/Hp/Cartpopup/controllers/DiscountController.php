<?php
class Hp_Cartpopup_DiscountController extends Mage_Core_Controller_Front_Action
{
	public function applyAction()
	{
		$cart = Mage::helper('checkout/cart')->getCart()->getQuote();
		$cartid = $cart->getId();
		if(!$cartid)
		{
			$cartObj = Mage::getModel('checkout/cart');
			$cartObj->init();
			$cartObj->save();
			$cart = Mage::helper('checkout/cart')->getCart()->getQuote();
			$cartid = $cart->getId();
		}
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$db->query("update `sales_flat_quote_address` set spdisc='1' where quote_id='" . $cartid . "' and address_type='shipping'");
		$db->query("insert into sales_flat_quote_spdisc set quoteid='" . $cartid . "', val='1',percentage='" . $_POST['percentage_spdisc'] . "'");
		$str = $_SERVER['HTTP_REFERER'];
		header("Refresh:0;URL='" . $str . "'");
	}
	public function checkAction()
	{
		$cartpopupactive = 0;
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		if($db)
		{
			$result = $db->query("SELECT * FROM `salesrule` where name='DO NOT DELETE' and is_active='1' limit 0,1");
			if($result)
			{
				$rows = $result->fetch(PDO::FETCH_ASSOC);
				if($rows) 
				{
					$cartpopupactive = 1;
				}
			}
		}
		
		$cart = Mage::helper('checkout/cart')->getCart()->getQuote()->getShippingAddress();
		$spdisc = $cart->getSpdisc();
		
		$curproduct = Mage::getModel('catalog/product')->load($_POST['product']);
		$categoryIds = $curproduct->getCategoryIds();
		$validprductflag = 0;
		foreach($categoryIds as $categoryId) {
			$category = Mage::getModel('catalog/category')->load($categoryId);
			$url = $category->getUrlPath();
			if(strpos("/" . $url,"/ruby-rings.html") > -1)
			{
				$validprductflag = 1;
				break;
			}
			if(strpos("/" . $url,"/emerald-rings.html") > -1)
			{
				$validprductflag = 1;
				break;
			}
		 }
		 if($spdisc != 1 && $cartpopupactive == 1 && $validprductflag == 1)
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
	}
}
?>