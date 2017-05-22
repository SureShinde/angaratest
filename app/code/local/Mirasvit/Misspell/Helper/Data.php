<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.9
 * @revision  370
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */




class Mirasvit_Misspell_Helper_Data extends Mage_CatalogSearch_Helper_Data
{
    public function getSuggestQueryText()
    {
        $model = Mage::getModel('misspell/suggest')->loadByQuery($this->_queryText);
        $suggest = $model->getSuggest();

        if (strtolower($this->_queryText) == strtolower($suggest) || !$suggest) {
            return $this->_queryText;
        }

        return $suggest;
    }

    public function clearText($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[-\+()|"\'\><!\[\]~=\^\:,\/?.@#$€;]/', ' ', $text);
        $text = str_replace('  ', ' ', $text);

        return trim($text);
    }

    public function getOriginalQueryText()
    {
        return parent::getQueryText();
    }

    public function getCountResults()
    {
        return Mage::getSingleton('catalogsearch/layer')->getProductCollection()->count();
    }

    /**
     * Retrieve query model object
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    public function setSuggestQuery()
    {
        if (substr($this->_queryText, 0, 1) == '@'
            || substr($this->_queryText, 0, 1) == '=') {
            return null;
        }
        $this->_queryText = $this->getSuggestQueryText();

        $this->_query = Mage::getModel('catalogsearch/query')
            ->loadByQuery($this->_queryText);
        if (!$this->_query->getId()) {
            $this->_query->setQueryText($this->_queryText);

            $this->_query->setStoreId(Mage::app()->getStore()->getId());
            if ($this->_query->getId()) {
                $this->_query->setPopularity($this->_query->getPopularity() + 1);
            }
            else {
                $this->_query->setPopularity(1);
            }
            $this->_query->prepare();
        }
    }

}