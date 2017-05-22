<?php

class Runa_Promotions_Block_Cart_Item_Extrainfo extends Mage_Core_Block_Abstract {

    /**
     * This html is supposed to appear at the bottom of every page as close to the end of <body></body> as possible
     * 
     * @see app/code/core/Mage/Core/Block/Mage_Core_Block_Abstract#_toHtml()
     */
    public function _toHtml()
    {
        $_messsages = '';
        foreach ($this->getMessages() as $message)
        {
            $_messsages.="<p class='item-msg " . $message['type'] . " '>* " . $message['text'] . "</p>";
        }
        return $_messsages;
    }

    public function getMessages()
    {

        $messages = array();
        $quoteItem = $this->getParentBlock()->getItem();

        if ($quoteItem->getProduct()->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE)
        {
            return $messages;
        }

        // Add basic messages occuring during this page load
        $baseMessages = $quoteItem->getMessage(false);
        if ($baseMessages)
        {
            foreach ($baseMessages as $message)
            {
                $messages[] = array(
                    'text' => $message,
                    'type' => $quoteItem->getHasError() ? 'error' : 'notice'
                );
            }
        }

        return $messages;
    }

}