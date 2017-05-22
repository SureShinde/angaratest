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


class Mirasvit_SearchSphinx_Model_Config
{
    const XML_PATH_SEARCH_ENGINE      = 'searchsphinx/manage/search_engine';
    
    const XML_PATH_WILDCARD_EXCEPTION = 'searchsphinx/advanced/wildcard_exception';
    const XML_PATH_NOTWORDS           = 'searchsphinx/advanced/notwords';
    const XML_PATH_STOPWORDS          = 'searchsphinx/advanced/stopwords';
    const XML_PATH_SYNONYMS           = 'searchsphinx/advanced/synonyms';
    
    const XML_PATH_MATH_MODE          = 'searchsphinx/advanced/match_mode';
    const XML_PATH_RESULT_LIMIT       = 'searchsphinx/advanced/result_limit';
    const XML_PATH_SEARCH_TEMPLATE    = 'searchsphinx/dev/search_template';
    
    const XML_PATH_WILDCARD           = 'searchsphinx/dev/wildcard';

    public function getSearchEngine()
    {
        return Mage::getStoreConfig(self::XML_PATH_SEARCH_ENGINE);
    }

    public function getMatchMode()
    {
        return Mage::getStoreConfig(self::XML_PATH_MATH_MODE);
    }

    public function getResultLimit()
    {
        $limit = intval(Mage::getStoreConfig(self::XML_PATH_RESULT_LIMIT));
        if ($limit == 0) {
            $limit = 100000;
        }

        return $limit;
    }

    public function getSearchTemplate()
    {
        if (!Mage::getStoreConfig(self::XML_PATH_SEARCH_TEMPLATE)) {
            return 'and';
        }

        return Mage::getStoreConfig(self::XML_PATH_SEARCH_TEMPLATE);
    }

    public function isAllowedWildcard()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_WILDCARD);
    }

    public function getWildcardExceptions()
    {
        $result = array();
        if (Mage::getStoreConfig(self::XML_PATH_WILDCARD_EXCEPTION)) {
            $exceptions = unserialize(Mage::getStoreConfig(self::XML_PATH_WILDCARD_EXCEPTION));
            foreach ($exceptions as $value) {
                $result[] = $value['word'];
            }
        }

        return $result;
    }

    public function getNotwords()
    {
        $result = array();
        if (Mage::getStoreConfig(self::XML_PATH_NOTWORDS)) {
            $exceptions = unserialize(Mage::getStoreConfig(self::XML_PATH_NOTWORDS));
            foreach ($exceptions as $value) {
                $result[] = $value['word'];
            }
        }

        return $result;
    }

    public function getSynonyms()
    {
        $result = array();
        if (Mage::getStoreConfig(self::XML_PATH_SYNONYMS)) {
            $array = unserialize(Mage::getStoreConfig(self::XML_PATH_SYNONYMS));
            foreach ($array as $value) {
                $word = $value['word'];
                $synonyms = explode(',', $value['synonyms']);

                foreach ($synonyms as $synonym) {
                    $synonym = trim($synonym);
                    $result[$word][$synonym] = $synonym;
                    
                    $result[$synonym][$word] = $word;
                    foreach ($synonyms as $subsynonym) {
                        $subsynonym = trim($subsynonym);
                        if ($subsynonym != $synonym) {
                            $result[$synonym][$subsynonym] = $subsynonym;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function getStopwords()
    {
        $result = array();
        if (Mage::getStoreConfig(self::XML_PATH_STOPWORDS)) {
            $array = unserialize(Mage::getStoreConfig(self::XML_PATH_STOPWORDS));
            foreach ($array as $value) {
                $result[] = $value['stopword'];
            }
        }

        return $result;
    }
}