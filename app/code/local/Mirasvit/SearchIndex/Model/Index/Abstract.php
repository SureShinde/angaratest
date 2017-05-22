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


/**
 * Represent Index model
 *
 * @category Mirasvit
 * @package  Mirasvit_SearchIndex
 */
abstract class Mirasvit_SearchIndex_Model_Index_Abstract extends Varien_Object
{
    /**
     * Represent array of matched entity ids
     *
     * @var array
     */
    protected $_matchedIds = null;

    /**
     * Is cache enabled for search resutls
     *
     * @var boolean
     */
    protected $_cache           = false;
    protected $_tmpTableCreated = false;

    /**
     * Get index identifier
     *
     * @return string
     */
    abstract public function getCode();

    /**
     * Get index primary key (entity primary key)
     *
     * @return string
     */
    abstract public function getPrimaryKey();

    /**
     * Get collection of founded items
     *
     * @return string
     */
    abstract public function getCollection();

    abstract public function getAvailableAttributes();

    /**
     * Get Indexer Model for current index
     *
     * @return Mirasvit_SearchIndex_Model_Indexer_Abstract
     */
    public function getIndexer()
    {
        return Mage::getSingleton('searchindex/type_'.$this->getCode().'_indexer');
    }

    /**
     * Get index title
     *
     * @return string
     */
    public function getTitle()
    {
        return Mage::getStoreConfig('searchindex/'.$this->getCode().'/title');
    }

    /**
     * Is index enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool) Mage::getStoreConfig('searchindex/'.$this->getCode().'/enabled');
    }

    /**
     * Get Sort Position
     *
     * @return boolean
     */
    public function getPosition()
    {
        return (int) Mage::getStoreConfig('searchindex/'.$this->getCode().'/position');
    }

    /**
     * Retrieve index attributes with attribute weight
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array();

        $attrConfig = unserialize(Mage::getStoreConfig('searchindex/'.$this->getCode().'/attribute'));
        if (!is_array($attrConfig)) {
            $attrConfig = array();
        }

        foreach ($attrConfig as $attr) {
            $attributes[$attr['attribute']] = intval($attr['value']);
        }

        return $attributes;
    }

    /**
     * Get matched ids array
     *
     * @return array
     */
    public function getMatchedIds()
    {
        if ($this->_matchedIds === null) {
            $this->_processSearch();
        }
        return $this->_matchedIds;
    }

    /**
     * Set matched ids array
     *
     * @return array
     */
    public function setMatchedIds($ids)
    {
        if (!is_array($ids)) {
            $ids = array();
        }

        $this->_matchedIds = $ids;

        return $this;
    }

    /**
     * Get catalog search query model
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    public function getQuery()
    {
        return Mage::helper('catalogsearch')->getQuery();
    }

    /**
     * Process search for current index
     *
     * @todo add alternative engine on exception
     *
     * @return Mirasvit_SearchIndex_Model_Index_Abstract
     */
    protected function _processSearch()
    {
        $ts = microtime(true);

        $engine    = Mage::helper('searchindex')->getSearchEngine();
        $query     = Mage::helper('catalogsearch')->getQuery();
        $queryText = $query->getQueryText();

        try {
            $result = $engine->query($queryText, $query->getStoreId(), $this);
            $this->setMatchedIds($result);
        } catch(Exception $e) {
            Mage::helper('mstcore/logger')->logException($this, $e, $e);
            // alternative engine (fulltext)
            try {
                $engine = Mage::getModel('searchsphinx/engine_fulltext');
                $result = $engine->query($queryText, $query->getStoreId(), $this);
                $this->setMatchedIds($result);
            } catch(Exception $e) {
                Mage::helper('mstcore/logger')->logException($this, $e, $e);
                $this->setMatchedIds(array());
            }
        }

        Mage::helper('mstcore/logger')->logPerformance($this, __FUNCTION__.' '.count($this->getMatchedIds()).' '.$queryText, round(microtime(true) - $ts, 4));

        return $this;
    }

    /**
     * Get count founded items in result collection
     *
     * @return int
     */
    public function getCountResults()
    {
        return $this->getCollection()->getSize();
    }

    /**
     * Join Matched ids with specific collection
     *
     * @todo refactoring
     *
     * @param  Varien_Collection $collection
     * @param  string            $mainTableKeyField
     * @return Mirasvit_SearchIndex_Model_Index_Abstract
     */
    public function joinMatched($collection, $mainTableKeyField = 'e.entity_id')
    {
        $matchedIds = $this->getMatchedIds();
        $this->_createTemporaryTable($matchedIds);

        $collection->getSelect()->joinLeft(
            array('tmp_table' => $this->_getTemporaryTableName()),
            '(tmp_table.entity_id='.$mainTableKeyField.')',
            array('relevance' => 'tmp_table.relevance')
        );
        if ($this->_cache) {
            $collection->getSelect()->where('tmp_table.query_id = '.$this->getQuery()->getId());
        }
        $collection->getSelect()->where('tmp_table.id IS NOT NULL');

        return $this;
    }

    public function getConnection()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    /**
     * Create temporary table if cache disabled
     * And fill table with matched ids
     *
     * @todo refactoring
     *
     * @param  array $matchedIds
     * @return Mirasvit_SearchIndex_Model_Index_Abstract
     */
    protected function _createTemporaryTable($matchedIds)
    {
        if ($this->_tmpTableCreated) {
            return $this;
        }

        $values = array();
        $queryId = $this->getQuery()->getId();

        foreach ($matchedIds as $id => $relevance) {
            $values[] = '('.$queryId.','.$id.','.$relevance.')';
        }

        $connection = $this->getConnection();

        $query = '';
        if ($this->_cache) {
            $query .= "CREATE TABLE IF NOT EXISTS `".$this->_getTemporaryTableName()."` (";
        } else {
            $query .= "CREATE TEMPORARY TABLE IF NOT EXISTS `".$this->_getTemporaryTableName()."` (";
        }
        $query .= "
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `query_id` int(11) unsigned NOT NULL,
                `entity_id` int(11) unsigned NOT NULL,
                `relevance` int(11) unsigned NOT NULL,
                PRIMARY KEY (`id`)";
        if ($this->_cache) {
            $query .= ")ENGINE=MEMORY DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            $query .= "DELETE FROM `".$this->_getTemporaryTableName()."` WHERE `query_id`=".$queryId.";";
        } else {
            $query .= ")ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
        }
        if (count($values)) {
            $query .= "INSERT INTO `".$this->_getTemporaryTableName()."` (`query_id`, `entity_id`, `relevance`)".
                "VALUES ".implode(',', $values).";";
        }

        $connection->raw_query($query);
        $this->_tmpTableCreated = true;

        return $this;
    }

    /**
     * Get Temporary table name for matched ids
     *
     * @return string
     */
    protected function _getTemporaryTableName()
    {
        return 'searchindex_result_'.$this->getCode();
    }
}