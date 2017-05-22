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


class Mirasvit_SearchSphinx_Helper_Query extends Mage_Core_Helper_Abstract
{
    public function buildQuery($query, $store, $inverseNot = false)
    {
        $result = array();
        $config = Mage::getSingleton('searchsphinx/config');

        // split query
        $arWords = Mage::helper('core/string')->splitWords($query, true);

        $logic = 'like';
        foreach ($arWords as $word) {
            if (in_array($word, $config->getNotwords())) {
                $logic = 'not like';
                continue;
            }

            if ($this->isStopword($word, $store)) {
                continue;
            }

            $wordArr = array();
            $this->_addWord($wordArr, $word);
            

            if ($logic == 'like') {
                $longTail = $this->longtail($word);
                $this->_addWord($wordArr, $longTail);
                
                $singular = Mage::helper('searchsphinx/inflect')->singularize($word);
                $this->_addWord($wordArr, $singular);

                $synonyms = $this->getSynonyms($word, $store);
                $this->_addWord($wordArr, $synonyms);

                $template = Mage::getSingleton('searchsphinx/config')->getSearchTemplate();
                if (Mage::getSingleton('searchsphinx/config')->getMatchMode() == 1) {
                    $template = 'or';
                }
                $result[$logic][$template][$word] = array('or' => $wordArr);
            } else {
                if (!$inverseNot) {
                    $result[$logic]['and'][$word] = array('and' => $wordArr);
                } else {
                    $result[$logic]['or'][$word] = array('and' => $wordArr);
                }
            }
        }

        return $result;
    }

    protected function getSynonyms($words, $store)
    {
        $result = array();

        $userSynonyms = Mage::getSingleton('searchsphinx/config')->getSynonyms();

        if (!is_array($words)) {
            $words = array($words);
        }

        foreach ($words as $word) {
            $synonyms = Mage::getSingleton('searchsphinx/synonym')->getSynonymsByWord($word, $store);
            foreach ($synonyms as $synonym) {
                $result[$synonym] = $synonym;
            }

            if (isset($userSynonyms[$word])) {
                foreach ($userSynonyms[$word] as $synonym) {
                    $result[$synonym] = $synonym;
                }
            }
        }

        return $result;
    }

    protected function isStopword($word, $store)
    {
        $userStopwords = Mage::getSingleton('searchsphinx/config')->getStopwords();

        if (Mage::getSingleton('searchsphinx/stopword')->isStopWord($word, $store)) {
            return true;
        }

        if (in_array($word, $userStopwords)) {
            return true;
        }

        return false;
    }

    protected function longtail($word)
    {
        $expressions = Mage::getSingleton('searchindex/config')->getMergeExpressins();
        
        foreach ($expressions as $expr) {
            $matches = null;
            preg_match_all($expr['match'], $word, $matches);

            foreach ($matches[0] as $math) {
                $math = preg_replace($expr['replace'], $expr['char'], $math);
                $word = $math;
            }
        }

        return $word;
    }

    protected function _addWord(&$to, $words)
    {
        $exceptions = Mage::getSingleton('searchsphinx/config')->getWildcardExceptions();
        $wildcard   = Mage::getSingleton('searchsphinx/config')->isAllowedWildcard();
        
        if (!is_array($words)) {
            $words = array($words);
        }

        foreach ($words as $word) {
            if (!$wildcard || in_array($word, $exceptions)) {
                $word = ' '.$word.' ';
            }

            $to[$word] = $word;
        }
    }
}