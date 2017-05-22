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



require_once 'abstract.php';

class Mirasvit_Shell_SearchTest extends Mage_Shell_Abstract
{
    public function run()
    {
        echo '<pre>';
        Mage::app()->setCurrentStore(3);
        $collection = Mage::getModel('catalogsearch/query')->getCollection()->setPageSize(100)->setCurPage($_GET['p']);
        foreach ($collection as $query) {
            Mage::unregister('');
            $query = $query->getQueryText();;
            try {
                Mage::app()->getRequest()->setParam('q', $query);
                echo $i++.' '.$query."<br>";
                $indexes = Mage::helper('searchindex/index')->getIndexes(true);

                foreach ($indexes as $code => $index) {
                    $indexer    = $index->getIndexer();
                    $matchedIds = $index->getMatchedIds();
                    $table      = $indexer->getTableName();
                    $pk         = $indexer->getPrimaryKey();

                    if ($index->getCode() == 'catalog') {
                        $products = Mage::getModel('catalogsearch/layer')->getProductCollection()
                            ->addAttributeToSelect('name');
                        $products->getSelect()->order('relevance desc');

                        foreach ($products as $product) {
                            echo '      '.$matchedIds[$product->getId()].' '.$product->getName()."<br>";
                        }
                    }
                }
            } catch (Exception $e) {
                echo $e;
            }

            echo "<hr>";
        }  
    }

    public function _validate()
    {

    }
}

$shell = new Mirasvit_Shell_SearchTest();
$shell->run();
