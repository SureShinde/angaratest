<?php 
class Angara_Promotions_Block_Offer_Coupon extends Mage_Core_Block_Template {
	
	protected $_coupon;
	protected $_rule;
	
	public function getCoupon(){
		if (!$this->_coupon) {
            $this->_coupon = $this->getData('coupon');
        }
		return $this->_coupon;
	}
	
	public function getRule(){
		if(!$this->_rule){
			if($this->getCoupon()){
				$this->_rule = Mage::getModel('salesrule/coupon')->load($this->getCoupon()->getRuleId(), 'rule_id');
			}
		}
		return $this->_rule;
	}
	
	public function setCoupon($coupon){
		$this->_rule = NULL;
		$this->_coupon = $coupon;
		return $this;
	}
	
	public function isApplied(){
		return $this->getCode() == Mage::getModel('checkout/cart')->getQuote()->getCouponCode();
	}

    public function _prepareLayout(){
        return parent::_prepareLayout();
    }
    
    public function getApplyUrl(){
		return '/checkout/cart/couponPost/coupon_code/'.$this->getCode();
	}
	
	public function getCode(){
		return $this->getRule()->getCode();
	}
	
	public function getAfterDiscountProductPrice($productId, $additionalCost,$trio1=null,$trio2=null,$trio3=null){
		$product = Mage::getModel('catalog/product')->load($productId);
		
		if($trio1 && $trio2 && $trio3)
		{
			
			$t1 = Mage::getModel('catalog/product')->loadByAttribute('sku', $trio1);
			
			$t2 = Mage::getModel('catalog/product')->loadByAttribute('sku', $trio2);
			$t3 = Mage::getModel('catalog/product')->loadByAttribute('sku', $trio3);
			$p1=$t1->getFinalPrice();
			$p2=$t2->getFinalPrice();
			$p3=$t3->getFinalPrice();
			
			$productPrice=$p1+$p2+$p3;
			
		}
		else
			$productPrice = $product->getPrice() + $additionalCost;
		
		
		$db = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = "SELECT discount_amount, conditions_serialized
			FROM salesrule_coupon AS a INNER JOIN salesrule AS b ON a.rule_id = b.rule_id
			WHERE simple_action = 'by_percent' AND a.code = ?";
		$sql = $db->quoteInto($sql, $this->getCode());
		$rows = $db->fetchAll($sql);
		if (isset($rows[0])) {
			$discount = ($rows[0]['discount_amount'] / 100);
			
			/*** This assumes that if there is a shopping cart price rule condition,
			  *  it has no effect on product.
			  *  Every product is applicable for the given price rule
			***/
			/* Though we can check each and every condition for e.g.
				$conditions = unserialize($rows[0]['conditions_serialized']);
				$conditions = $conditions['conditions'][0]['conditions'][0];
				if (is_array($conditions) && $conditions['value'] == $_product->getAttributeSetId()){ // do something }
			*/
			$productPrice = $productPrice - ($productPrice * $discount);
			$sale_price = true;
		}
		$productPrice=round($productPrice);
		return $productPrice;
	}
	
	public function getFreeGifts(){
		return Mage::getModel('promotions/offer')->getFreeProducts($this->getCoupon());
	}
}