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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

        // prepare cart items URLs
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
        if ($this->getQuote()->getItemsCount()) {
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
            $isActive = Mage::getStoreConfig('wishlist/general/active') && Mage::getSingleton('customer/session')->isLoggedIn();
            $this->setIsWishlistActive($isActive);
        }
        return $isActive;
    }

    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', array('_secure'=>true));
    }

    public function getContinueShoppingUrl()
    {
        $url = $this->getData('continue_shopping_url');
        if (is_null($url)) {
            $url = Mage::getSingleton('checkout/session')->getContinueShoppingUrl(true);
            if (!$url) {
                $url = Mage::getUrl();
            }
            $this->setData('continue_shopping_url', $url);
        }
        return $url;
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
	public function getGrandtotalWithoutInstallments()
	{
		$subt = $this->getQuote()->getSubtotal();
		$grandtotal = $this->getQuote()->getGrandTotal();
		$mainsubt = $this->getTotalWithoutInstallments();
		$qa = $this->getQuote()->getShippingAddress();
		$totdiscount = $qa->getDiscountAmount() * -1;
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
		$grandtotal = $grandtotal + ($mainsubt - $subt) - ($totdiscountwi - $totdiscount);
		return $grandtotal;
	}
	public function getTotalWithoutInstallments(){
		$amount = 0;
		foreach ($this->getItems() as $item) {
			$amount = $amount + ($item->getProduct()->getFinalPrice() * $item->getQty());
		}
		return $amount;
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
}
