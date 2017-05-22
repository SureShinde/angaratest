<?php

class Angara_Wishlist_Block_Product extends Mage_Catalog_Block_Product
{
    
    private $product;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('angara_wishlist/product.phtml');
    }

    protected function _toHtml() {
        return parent::_toHtml();
    }
    
    public function setProduct($product) {
        $this->product = $product;
        return $this;
    }
    
    public function getProduct() {
        return $this->product;
    }
}
