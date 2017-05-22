<?php
class Ayasoftware_SimpleProductPricing_Checkout_Block_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    protected function getConfigurableProductParentId()
    {
        if ($this->getItem()->getOptionByCode('cpid')) {
            return $this->getItem()->getOptionByCode('cpid')->getValue();
        }
        
        try {
            $buyRequest = unserialize($this->getItem()->getOptionByCode('info_buyRequest')->getValue());
            if(!empty($buyRequest['cpid'])) {
                return $buyRequest['cpid'];
            }
        } catch (Exception $e) {
        }
        return null;
    }

    protected function getConfigurableProductParent()
    {
        return Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($this->getConfigurableProductParentId());
    }

    public function getProduct()
    {
        return Mage::getModel('catalog/product')
           ->setStoreId(Mage::app()->getStore()->getId())
                ->load($this->getItem()->getProductId());
    }

	// removed by hitesh as name was not shown on some products in cart
    /*public function getProductName()
    {
            $product = $this->getConfigurableProductParent();
            return $product->getName();
    }*/


    /* Bit of a hack this - assumes configurable parent is always linkable */
    public function hasProductUrl()
    {
        if ($this->getConfigurableProductParentId()) {
            return true;
        } else {
            return parent::hasProductUrl();
        }
    }

    public function getProductUrl()
    {
        if ($this->getConfigurableProductParentId()) {
            return $this->getConfigurableProductParent()->getProductUrl();
        } else {
            return parent::getProductUrl();
            #return $this->getProduct()->getProductUrl();
        }
    }

    public function getOptionList()
    {

        $options = parent::getOptionList();
        if ($this->getConfigurableProductParentId()) {
                $attributes = $this->getConfigurableProductParent()
                    ->getTypeInstance()
                    ->getUsedProductAttributes();
                foreach($attributes as $attribute) {
                    $options[] = array(
                        'label' => $attribute->getFrontendLabel(),
                        'value' => $this->getProduct()->getAttributeText($attribute->getAttributeCode()),
                        'option_id' => $attribute->getId(),
                    );
                }
        }
    if ($optionIds = $this->getOptionByCode('option_ids')) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $this->getProduct()->getOptionById($optionId)) {

                    $quoteItemOption = $this->getItem()->getOptionByCode('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setQuoteItemOption($quoteItemOption);

                    $options[] = array(
                        'label' => $option->getTitle(),
                        'value' => $group->getFormattedOptionValue($quoteItemOption->getValue()),
                        'print_value' => $group->getPrintableOptionValue($quoteItemOption->getValue()),
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'custom_view' => $group->isCustomizedView()
                    );
                }
            }
        }
       
        return $options;
    }

   
    public function getProductThumbnail()
    {
       
        if (!$this->getConfigurableProductParentId()) {
           return parent::getProductThumbnail();
        }
        #If showing simple product image
            $product = $this->getProduct();
            #if product image is not a thumbnail
            if($product->getData('thumbnail') && ($product->getData('thumbnail') != 'no_selection')) {
                return $this->helper('catalog/image')->init($product, 'thumbnail');
            }
        #If simple prod thumbnail image is placeholder, or we're not using simple product image
        #show configurable product image
        $product = $this->getConfigurableProductParent();
        return $this->helper('catalog/image')->init($product, 'thumbnail');
    }
}