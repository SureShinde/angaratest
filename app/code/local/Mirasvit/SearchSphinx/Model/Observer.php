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


class Mirasvit_SearchSphinx_Model_Observer
{
    public function reindex()
    {
        if (Mage::getSingleton('searchsphinx/config')->getSearchEngine() == 'sphinx') {
            Mage::getModel('searchsphinx/engine_sphinx')->reindex();
        }
    }

    public function reindexDelta()
    {
        if (Mage::getSingleton('searchsphinx/config')->getSearchEngine() == 'sphinx') {
            Mage::getModel('searchsphinx/engine_sphinx')->reindexDelta();
        }
    }

    public function checkDaemon()
    {
        if (Mage::getSingleton('searchsphinx/config')->getSearchEngine() == 'sphinx') {
            $engine = Mage::getModel('searchsphinx/engine_sphinx');
            if ($engine->isSearchdRunning() == false) {
                $engine->restart();
            }
        }
    }

    public function onMisspellIndexerPrepare($observer)
    {
        $obj = $observer->getObj();
        $string = '';

        $synonyms = Mage::getSingleton('searchsphinx/config')->getSynonyms();

        foreach ($synonyms as $key => $synonyms) {
            $string .= $key.' '.implode(' ', $synonyms).' ';
        }

        // foreach (Mage::getModel('searchsphinx/synonym')->getCollection() as $item) {
        //     $string .= $item->getWord().' '.$item->getSynonyms().' ';
        // }

        $obj->setSearchSphinxSynonyms($string);
    }
}