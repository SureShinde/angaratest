<?php
/**
 * Sharecart Extension
 *
 * @category   Sutunam
 * @package    Sharecart
 * @author     Sututeam <dev@sutunam.com>
 */

class Sutunam_Sharecart_Helper_Data extends Mage_Core_Helper_Data
{
    
    /**
     * Generate Url to share cart
     * 
     * @param Sutunam_Sharecart_Model_Cart $params
     * @return String Url to share cart
     */

    public function generateUrlShare(Sutunam_Sharecart_Model_Cart $sharecart)
    {
        if (!($sharecart instanceof Sutunam_Sharecart_Model_Cart && $sharecart->getId())) {
            return '';
        }
        $crypt = Mage::helper('core')->encrypt(Sutunam_Sharecart_Model_Config::STRING_ENCRYPT . $sharecart->getId());
        $crypt = $this->urlEncode($crypt);
        
        return Mage::getUrl('sharecart/index/cart' , array('crypt' => $crypt));
    }
    
    public function getItems()
    {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        $params = array();
        foreach ($items as $item) {
			if($item->getPrice() == 0){
				continue;
			}
			if(substr($item->getSku(), 0, 2) == 'IN' || substr($item->getSku(), 0, 2) == 'JA'){
				continue;
			}
			
            $product    = $item->getProduct();            
            $options     = $item->getOptionsByCode();
            if (is_array($options) && !empty($options)) {
                $option = $options['info_buyRequest'];
                if(isset($option) && ($option instanceof Mage_Sales_Model_Quote_Item_Option)){
                    $optionValue = unserialize($option->getValue());
                }
            }            
            $params[] = array(
                'product_id'=> $product->getId(),
                'qty'       => $item->getQty(),
                'option'    => $optionValue
            );
        }        
        return $params;
    }
    
    public function getShareCartByCode($code)
    {
        $shareCart = Mage::getModel('sharecart/cart');
        $crypt = Mage::helper('core')->decrypt($this->urlDecode($code));
        $sharecartId = substr($crypt, strlen(Sutunam_Sharecart_Model_Config::STRING_ENCRYPT));
        
        if((int) $sharecartId){
            $shareCart->load($sharecartId);
        }
        
        return $shareCart;
    }
}