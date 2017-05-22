<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Cart extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Prepare Quote Item Product URLs
     *
     */
	public function __construct()
    {
        parent::__construct();
        $this->prepareItemUrls();
    }
	 
    /**
     * Prepare cart items URLs
     *
     * @deprecated after 1.7.0.2
     */
    public function prepareItemUrls()
    {
        $products = array();
        /* @var $item Mage_Sales_Model_Quote_Item */
        foreach ($this->getItems() as $item) {
            $product    = $item->getProduct();
            $option     = $item->getOptionByCode('product_type');
            if ($option) {
                $product = $option->getProduct();
            }

            if ($item->getStoreId() != Mage::app()->getStore()->getId()
                && !$item->getRedirectUrl()
                && !$product->isVisibleInSiteVisibility())
            {
                $products[$product->getId()] = $item->getStoreId();
            }
        }

        if ($products) {
            $products = Mage::getResourceSingleton('catalog/url')
                ->getRewriteByProductStore($products);
            foreach ($this->getItems() as $item) {
                $product    = $item->getProduct();
                $option     = $item->getOptionByCode('product_type');
                if ($option) {
                    $product = $option->getProduct();
                }

                if (isset($products[$product->getId()])) {
                    $object = new Varien_Object($products[$product->getId()]);
                    $item->getProduct()->setUrlDataObject($object);
                }
            }
        }
    }

    public function chooseTemplate()
    {
        $itemsCount = $this->getItemsCount() ? $this->getItemsCount() : $this->getQuote()->getItemsCount();
        if ($itemsCount) {
            $this->setTemplate($this->getCartTemplate());
        } else {
            $this->setTemplate($this->getEmptyTemplate());
        }
    }

    public function hasError()
    {
        return $this->getQuote()->getHasError();
    }

    public function getItemsSummaryQty()
    {
        return $this->getQuote()->getItemsSummaryQty();
    }

    public function isWishlistActive()
    {
        $isActive = $this->_getData('is_wishlist_active');
        if ($isActive === null) {
            $isActive = Mage::getStoreConfig('wishlist/general/active')
                && Mage::getSingleton('customer/session')->isLoggedIn();
            $this->setIsWishlistActive($isActive);
        }
        return $isActive;
    }

    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', array('_secure'=>true));
    }

    /**
     * Return "cart" form action url
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('checkout/cart/updatePost', array('_secure' => $this->_isSecure()));
    }

    public function getContinueShoppingUrl()
    {
        $url = $this->getData('continue_shopping_url');
        if (is_null($url)) {
            $url = Mage::getSingleton('checkout/session')->getContinueShoppingUrl(true);
            if (!$url) {
                $url = Mage::getUrl();
            }else{
			//	S:: Code added by Vaseem for continue shopping url
				if(strstr($url,'matchingband')){
					$lastProductAddedToCartId = Mage::getSingleton('checkout/session')->getLastAddedProductId();
					if($lastProductAddedToCartId) {
						$productCategoryIdsArray = Mage::getModel('catalog/product')->load($lastProductAddedToCartId)->getCategoryIds();
						//$url = Mage::getModel('catalog/category')->load($productCategoryIdsArray[0])->getUrl();
						$url = Mage::getUrl();
					}
				}
			}
			//	E:: Code added by Vaseem for continue shopping url
            $this->setData('continue_shopping_url', $url);
        }
        return $url;
    }
	
	public function getRefererUrlClose(){
		$refererHttpUrl = Mage::helper('core/http')->getHttpReferer();
		$sessionRefUrl = Mage::getSingleton('core/session')->getRefererUrl();
		
		if(empty($sessionRefUrl)){
			$sessionRefererUrl = $refererHttpUrl;
			Mage::getSingleton('core/session')->setRefererUrl($sessionRefererUrl);
		}
		else{
			if($refererHttpUrl && stripos($refererHttpUrl, 'checkout/cart') === false && stripos($refererHttpUrl, 'onepagecheckout/index') === false && stripos($refererHttpUrl, 'paypal') === false && stripos($refererHttpUrl, 'amazon') === false){	
				$sessionRefererUrl = $refererHttpUrl;
				Mage::getSingleton('core/session')->setRefererUrl($sessionRefererUrl);
			}
			else{
				$sessionRefererUrl = Mage::getSingleton('core/session')->getRefererUrl();
			}
		}
		
		return $sessionRefererUrl;
	}

    public function getIsVirtual()
    {
        return $this->helper('checkout/cart')->getIsVirtualQuote();
    }

    /**
     * Return list of available checkout methods
     *
     * @param string $nameInLayout Container block alias in layout
     * @return array
     */
    public function getMethods($nameInLayout)
    {
        if ($this->getChild($nameInLayout) instanceof Mage_Core_Block_Abstract) {
            return $this->getChild($nameInLayout)->getSortedChildren();
        }
        return array();
    }

    /**
     * Return HTML of checkout method (link, button etc.)
     *
     * @param string $name Block name in layout
     * @return string
     */
    public function getMethodHtml($name)
    {
        $block = $this->getLayout()->getBlock($name);
        if (!$block) {
            Mage::throwException(Mage::helper('checkout')->__('Invalid method: %s', $name));
        }
        return $block->toHtml();
    }
	
	/**
     * Return customer quote items
     *
     * @return array
     */
    public function getItems()
    {
        if ($this->getCustomItems()) {
            return $this->getCustomItems();
        }

        return parent::getItems();
    }
	
	// Start Angara Modification
//start old functions to be remove
	public function getGrandtotalWithoutInstallments()
	{		
		/*$temp_price = 1;
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode(); 
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$convFactor = Mage::helper('directory')->currencyConvert($temp_price, $baseCurrencyCode, $currentCurrencyCode);*/
		
		$subtotAmt = $this->getQuote()->getSubtotal();
		//$grandtotal = $this->getQuote()->getGrandTotal();
		$qa = $this->getQuote()->getShippingAddress();
		$DiscountAmt = $qa->getDiscountAmount();
		$taxAmt = $this->getQuote()->getShippingAddress()->getTaxAmount();
				
		$OrderTotalAmt = $subtotAmt + $DiscountAmt + $taxAmt;
		//echo $OrderTotalAmt;
		return $OrderTotalAmt;
		
		/*$subt = $this->getQuote()->getSubtotal();
		$grandtotal = $this->getQuote()->getGrandTotal();
		$mainsubt = $this->getTotalWithoutInstallments();
		$qa = $this->getQuote()->getShippingAddress();
		$totdiscount = $qa->getDiscountAmount() * -1;
		$totdiscountwi = $this->getDiscountWithoutInstallments();
		$tax = $this->getQuote()->getShippingAddress()->getTaxAmount();
		$taxwi = $this->getTaxWithInstallments();
		if(strrpos($totdiscountwi,'sp|') > -1)
		{
			$arr = explode("|", $totdiscountwi);
			$totdiscountwi = round($arr[1],2);
		}
		else
		{
			$totdiscountwi = round($totdiscountwi,2);
		}
		$grandtotal = $grandtotal + ($mainsubt - $subt) - ($totdiscountwi - $totdiscount) + ($taxwi - $tax);
		return $grandtotal;*/
	}
	
	public function getTotalWithoutInstallments(){
		$amount = 0;
		foreach ($this->getItems() as $item) {
			$amount = $amount + ($item->getProduct()->getFinalPrice() * $item->getQty());
		}
		return $amount;
	}
	
	//	This function will give the final amount that we will charge from customer in future (in case EMI)
	public function getCartFinalTotal(){
		$amount = 0;
		foreach ($this->getItems() as $item) {
			//echo '<br>getID->'.$item->getID();
			/*echo '<br>getPrice->'.$item->getPrice();
			echo '<br>getSku->'.$item->getSku();
			echo '<br>getFinalPrice->'.$item->getPrice();*/
			$amount = $amount + ($item->getPrice() * $item->getQty());
		}
		
		$checkDiscount 			= 	$this->getQuote()->getShippingAddress();
		$totalDiscountAmount 	= 	$checkDiscount->getDiscountAmount() * -1;
		//echo '<br>totalDiscountAmount->'.$totalDiscountAmount;
		if($totalDiscountAmount>0){
			$amount = $amount - $totalDiscountAmount;
		}
		return $amount;
	}
	//	Function added by Vaseem Ends
	
	
	public function getTaxPerc()
	{
		$tx = $this->getQuote()->getShippingAddress()->getAppliedTaxes();
		$taxperc = 0;
		$amnt = 0;
		foreach($tx as $tx1 => $tx2)
		{
			$taxperc = $tx2['percent'];
			$amnt = $tx2['amount'];
			break;
		}
		return $taxperc;
	}
	
	public function getoveralldiscountpercentageincluding()
	{
		$subt = $this->getQuote()->getSubtotal();
		$qa = $this->getQuote()->getShippingAddress();
		$totdiscount = $qa->getDiscountAmount() * -1;
		return ($totdiscount / $subt) * 100;
	}
	
	public function installmentamountwithtaxanddiscount($am,$qty)
	{
		$tax = $this->getTaxPerc();
		$disperc = $this->getoveralldiscountpercentageincluding();
		$disam = $am * $disperc/100;
		$am = $am - $disam;
		$am = $am + ($am*$tax/100);
		return $am * $qty;
	}
	
	public function getTaxWithInstallments()
	{
		$tx = $this->getQuote()->getShippingAddress()->getAppliedTaxes();
		$taxperc = 0;
		$amnt = 0;
		foreach($tx as $tx1 => $tx2)
		{
			$taxperc = $tx2['percent'];
			$amnt = $tx2['amount'];
			break;
		}
		$mainsubt = $this->getTotalWithoutInstallments();
		$totdiscountwi = $this->getDiscountWithoutInstallments();
		if(strrpos($totdiscountwi,'sp|') > -1)
		{
			$arr = explode("|", $totdiscountwi);
			$totdiscountwi = round($arr[1],2);
		}
		else
		{
			$totdiscountwi = round($totdiscountwi,2);
		}
		return ($mainsubt - $totdiscountwi) * $taxperc / 100;
	}
	
	public function getDiscountWithoutInstallments(){
		$qa = $this->getQuote()->getShippingAddress();
		$spdisc = $qa->getSpdisc();
		$subt = $this->getQuote()->getSubtotal();
		$mainsubt = $this->getTotalWithoutInstallments();
		$totdiscount = $qa->getDiscountAmount() * -1;
		if($spdisc == 1)
		{
			$percentage = '0';
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$result = $db->query("SELECT * FROM `salesrule` where name='DO NOT DELETE' and is_active='1' limit 0,1");
			if($result)
			{
				$rows = $result->fetch(PDO::FETCH_ASSOC);
				if($rows) 
				{
					$shoppingCartPriceRule = Mage::getModel('salesrule/rule');
					$rule = $shoppingCartPriceRule->load($rows['rule_id']);
					$percentage = $rule->getData('discount_amount');
				}
			}
			$spdis = $subt * $percentage /100;
			$subtwithoutspdisc = $subt - $spdis;
			$totwithoutspdisc = $totdiscount - $spdis;
			$maindiscpercentage	 = 	($totwithoutspdisc/$subtwithoutspdisc)*100;
			$maindiscpercentage = round($maindiscpercentage,2);
			$originalspdis = $mainsubt * $percentage/100;
			$mainsubtwithoutspdisc = $mainsubt - $originalspdis;
			$coupondisc = $mainsubtwithoutspdisc * $maindiscpercentage / 100;
			$maindiscount = $coupondisc + $originalspdis;
			return "sp|" . $maindiscount . "|" . $coupondisc . "|" . $originalspdis;
			//sp| Total Disc | Coupon Disc | Sp Disc
		}
		else
		{
			$perc = ($totdiscount / $subt)*100; 
			$maindisc = $mainsubt * $perc / 100;
			return $maindisc;
		}
	}

//end old functions to be remove
	
// start functions to be remain	
	/* custom function to cjheck if recurring is enabled */
	public function hasRecurringItem(){
		$is_reccuring = false;
		foreach ($this->getItems() as $item) {
			$recInstall = explode('_', $item->getBuyRequest()->getData('easyopt'));
			if($recInstall[0]>0){
				$is_reccuring = true;
			}
			//var_dump($item);exit;
		}
		return $is_reccuring;
	}
	
	public function getCartItemInstallmentWithDiscAmount($ItemTotAmt=NULL, $ItemTotEMI=NULL, $ItemQty=NULL, $ItemDiscountAmt=NULL)
	{	
		$perItemDiscountAmt = ($ItemDiscountAmt / $ItemQty) ;
		//echo '<br>'.$ItemTotAmt.'---'.$ItemTotEMI.'---'.$ItemQty.'---'.$ItemDiscountAmt;
		$EMIAmount = (($ItemTotAmt - $perItemDiscountAmt) * $ItemQty ) / $ItemTotEMI;
		return $EMIAmount;
	}
	
	public function getInstallmentAmountWithTaxAndDiscount($ItemInstallmentAmt=NULL, $ItemTotEMI=NULL, $ItemQty=NULL)
	{
		$am = ($ItemTotAmt * $ItemQty) / $ItemTotEMI;
		return $am;
	}
	
	public function updateOrderPayMode($order_id=NULL,$pay_mode=NULL)
	{
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$sql = "UPDATE sales_flat_order set pay_mode='".addslashes(trim($pay_mode))."' where increment_id='".$order_id."'";
		$result = $db->query($sql);
	}
	
	public function addEasyPayHistoryWithOrder($parent_id=NULL,$comment=NULL)
	{
		if($comment!='')
		{	
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$sql = "INSERT INTO sales_flat_order_status_history (entity_id, parent_id, is_customer_notified, is_visible_on_front, comment, status, created_at) 
			VALUES (NULL, '".$parent_id."', '0', '0', '".addslashes(trim($comment))."', 'processing','".date('Y-m-d H:i:s')."')";
			$result = $db->query($sql);
		}	
	}
	
	public function getNoOfInstallmentByQuoteItemId($quote_item_id=NULL)
	{
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$sql = "SELECT quote_item.no_of_installment FROM sales_flat_quote_item as quote_item
		LEFT JOIN sales_flat_order_item as order_item on order_item.quote_item_id = quote_item.item_id
		WHERE quote_item.item_id='".$quote_item_id."'";
		$result = $db->query($sql);
		$rows = array();
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		$no_of_installment = 1;
		if(count($rows)>0){			
			$no_of_installment = $rows[0]['no_of_installment'];
		}else{
			$no_of_installment = 1;
		}
		return $no_of_installment;
	}
	
	public function getAllShippingMethodListAction()
	{		
		$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
		$options = array();	
		foreach($methods as $_ccode => $_carrier)
		{
			$_methodOptions = array();
			if($_methods = $_carrier->getAllowedMethods())
			{
				foreach($_methods as $_mcode => $_method)
				{
					$_code = $_ccode . '_' . $_mcode;
					$_methodOptions[] = array('value' => $_code, 'label' => $_method);
				}
	
				if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
					$_title = $_ccode;
	
				$options[] = array('value' => $_methodOptions, 'label' => $_title);
			}
		}
		return $options[0]['value'][0]['value'];
	}
// end functions to be remain	
	// End Angara Modification
}