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


class Mirasvit_SearchSphinx_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getEngine()
    {
        Mage::helper('mstcore/debug')->start();

        $engine = Mage::getSingleton('searchsphinx/engine_fulltext');

        if (Mage::getStoreConfig('searchsphinx/manage/search_engine') == 'sphinx') {
            // $isSphinxRunning = false;
            // try {
            //     $isSphinxRunning = Mage::getSingleton('searchsphinx/engine_sphinx')->isSearchdRunning();
            // } catch(Exception $e) {
            //     Mage::logException($e);
            // }

            // if ($isSphinxRunning) {
            //     $engine = Mage::getSingleton('searchsphinx/engine_sphinx');
            // }

            $engine = Mage::getSingleton('searchsphinx/engine_sphinx');
        } elseif (Mage::getStoreConfig('searchsphinx/manage/search_engine') == 'sphinx_external') {
            $engine = Mage::getSingleton('searchsphinx/engine_sphinx');
        }

        return $engine;
    }

    public function isWildcardException($word)
    {
        Mage::helper('mstcore/debug')->start();
        
        $exceptions = Mage::getSingleton('searchsphinx/config')->getWildCardExceptions();

        return in_array($word, $exceptions);
    }
}