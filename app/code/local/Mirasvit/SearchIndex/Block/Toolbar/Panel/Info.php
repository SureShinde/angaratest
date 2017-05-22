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
 * @version   2.2.8
 * @revision  334
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_SearchIndex_Block_Toolbar_Panel_Info extends Mirasvit_MstCore_Block_Toolbar_Panel
{
    public function _construct()
    {
        parent::_construct(); 
    }

    public function _prepareLayout()
    {
        $this->setTemplate('searchindex/toolbar/panel/info.phtml');
    }

    public function getIdentifier()
    {
        return 'searchindex';
    }

    public function getName()
    {
        return 'SearchIndex';
    }

    public function getResults()
    {
        $indexes = Mage::helper('searchindex/index')->getIndexes(true);

        foreach ($indexes as $code => $index) {
            $indexer    = $index->getIndexer();
            $matchedIds = $index->getMatchedIds();
            $table      = $indexer->getTableName();
            $pk         = $indexer->getPrimaryKey();

            $resource   = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_read');

            if (count($matchedIds) > 0) {
                $keys = array_keys($matchedIds);
                $result = $connection->fetchAll('SELECT * FROM '.$table.' WHERE '.$pk.' IN ('.implode(', ', $keys).')');
                foreach ($result as $indx => $item) {
                    $result[$indx]['relevancy'] = $matchedIds[$item[$pk]];
                }
                $columns = array_keys($result[0]);
                $index->setInfoResults($result);
                $index->setInfoColumns($columns);
            } else {
                $index->setInfoResults(array());
            }
        }

        return $indexes;
    }
}