<?php
/*
	S:VA	Model Rewrite
*/
class Angara_UtilityBackend_Model_Checkout_Cart extends Mage_Checkout_Model_Cart
{
    /**
     * Remove item from cart
     *
     * @param   int $itemId
     * @return  Mage_Checkout_Model_Cart
     */
    public function removeItem($itemId)
    {
        // Angara Modification Start
		Mage::dispatchEvent('checkout_cart_remove_item_before', array('cart'=>$this, 'item_deleting' => $itemId));
		$this->getQuote()->removeItem($itemId);			
		Mage::dispatchEvent('checkout_cart_remove_item_after', array('cart'=>$this, 'item_deleted' => $itemId));
		// Angara Modification End
        return $this;
    }
}