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


class Mirasvit_Misspell_Block_Suggest extends Mage_Core_Block_Template
{
    public function _construct()
    {
        $layer = Mage::getSingleton('catalogsearch/layer');
        $collection = $layer->getProductCollection();
        
        if ($collection->getSize() == 0
            && $this->getSuggestText()
            && $this->getSuggestText() != $this->getOriginalQueryText()
            && $this->getSuggestText() != $this->getQueryText()) {

            //do redirect
            $suggest = $this->getSuggestText();
            if ($this->testSuggest($suggest)) {
                $url = $this->getSuggestUrl($suggest, Mage::helper('catalogsearch')->getQueryText());
                Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            }
        }
    }

    public function testSuggest($suggest)
    {
        $query = Mage::getModel('catalogsearch/query')
                ->loadByQuery($suggest);

        if ($query->getId()) {
            return $query->getNumResults();
        }
        return true;
    }

    public function isSuggested()
    {
        if ($this->getOriginalQueryText()) {
            return true;
        }

        return false;
    }

    public function getOriginalQueryText()
    {
        return Mage::app()->getFrontController()->getRequest()->getParam('o');
    }

    public function getQueryText()
    {
        return Mage::app()->getFrontController()->getRequest()->getParam('q');
    }

    public function getSuggestText()
    {
        $helper = Mage::helper('catalogsearch');
        if ($this->isSuggested()) {
            return $helper->getQueryText();
        } else {
            $query   = $helper->getQueryText();
            $suggest = $helper->getSuggestQueryText();

            if ($helper->clearText($query) == $helper->clearText($suggest)) {
                return false;
            }

            return $suggest;
        }
    }

    public function getCountResults()
    {
        $count = Mage::helper('catalogsearch')->getCountResults();
        return $count;
    }

    public function getQueryUrl($query)
    {
        return Mage::getUrl('catalogsearch/result',
            array('_query' => array('q' => $query)));
    }

    public function getSuggestUrl($query, $original)
    {
        return Mage::getUrl('catalogsearch/result',
            array('_query' => array('q' => $query, 'o' => $original)));
    }

    public function highlight($suggest)
    {
        $query = Mage::helper('catalogsearch')->getOriginalQueryText();

        return $this->htmlDiff($query, $suggest);
    }

    function htmlDiff($old, $new)
    {
        $ret = '';
        $diff = $this->diff(explode(' ', $old), explode(' ', $new));
        foreach($diff as $k) {
            if(is_array($k))
                $ret .= (!empty($k['i'])?"<em>".implode(' ',$k['i'])."</em> ":'');
            else $ret .= $k . ' ';
        }
        return $ret;
    }

    function diff($old, $new)
    {
        $maxlen = 0;
        foreach($old as $oindex => $ovalue){
            $nkeys = array_keys($new, $ovalue);
            foreach($nkeys as $nindex){
                $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                    $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                if($matrix[$oindex][$nindex] > $maxlen) {
                    $maxlen = $matrix[$oindex][$nindex];
                    $omax   = $oindex + 1 - $maxlen;
                    $nmax   = $nindex + 1 - $maxlen;
                }
            }
        }

        if($maxlen == 0) return array(array('d' => $old, 'i' => $new));

        return array_merge(
            $this->diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
            array_slice($new, $nmax, $maxlen),
            $this->diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
    }
}