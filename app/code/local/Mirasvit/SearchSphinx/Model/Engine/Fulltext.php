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


class Mirasvit_SearchSphinx_Model_Engine_Fulltext extends Mirasvit_SearchIndex_Model_Engine
{
    public function query($query, $store, $index)
    {
        $uid = Mage::helper('mstcore/debug')->start();

        $connection = $this->_getReadAdapter();
        $table      = $index->getIndexer()->getTableName();
        $attributes = $this->_getAttributes($index);
        $pk         = $index->getIndexer()->getPrimaryKey();

        $select = $connection->select();
        $select->from(array('s' => $table), array($pk));

        $arQuery = Mage::helper('searchsphinx/query')->buildQuery($query, $store);

        if (count($arQuery) == 0 || count($attributes) == 0) {
            return array();
        }

        Mage::helper('mstcore/debug')->dump($uid, $arQuery);

        $caseCondition = $this->_getCaseCondition($query, $arQuery, $attributes);
        $whereCondition = $this->_getWhereCondition($arQuery, $attributes);

        $select->where('s.store_id = ?', (int) $store);
        
        if ($whereCondition != '') {
            $select->where($whereCondition);
        }

        $select->columns(array('relevance' => $caseCondition));
        $select->columns('searchindex_weight');

        $select->limit(Mage::getSingleton('searchsphinx/config')->getResultLimit());
      
        Mage::helper('mstcore/debug')->dump($uid, $select->__toString());

        $result = array();
        $weight = array();

        $stmt = $connection->query($select);
        while ($row = $stmt->fetch(Zend_Db::FETCH_NUM)) {
            $result[$row[0]] = $row[1];
            $weight[$row[0]] = $row[2];
        }

        $result = $this->_normalize($result);

        foreach ($result as $key => $value) {
            $result[$key] += $weight[$key];
        }

        Mage::helper('mstcore/debug')->end($uid, $result);

        return $result;
    }

    protected function _getCaseCondition($query, $arQuery, $attributes)
    {
        Mage::helper('mstcore/debug')->start();
        $select    = '';
        $cases     = array();
        $fullCases = array();
        if (isset($arQuery['like']['and'])) {
            $words = array_keys($arQuery['like']['and']);   
        } else {
            $words = array_keys($arQuery['like']['or']);
        }

        foreach ($attributes as $attr => $weight) {
            if ($weight == 0) {
                continue;
            }

            $cases[$weight * 4][] = $this->getCILike('s.'.$attr, $query);
            $cases[$weight * 3][] = $this->getCILike('s.'.$attr, $query, array('position' => 'any'));
            $cases[$weight * 2][] = $this->getCILike('s.'.$attr, implode('%', $words), array('position' => 'any', 'allow_string_mask' => true));
        }

        foreach ($words as $word) {
            foreach ($attributes as $attr => $weight) {
                $w = intval($weight / count($arQuery));
                if ($w == 0) {
                    continue;
                }

                $cases[$w][] = $this->getCILike('s.'.$attr, $word, array('position' => 'any'));
                $cases[$w + 1][] = $this->getCILike('s.'.$attr, ' '.$word.' ', array('position' => 'any'));
            }
        }
        
        foreach ($cases as $weight => $conds) {
            foreach ($conds as $cond) {
                $fullCases[] = 'CASE WHEN '.$cond.' THEN '.$weight.' ELSE 0 END';
            }
        }

        if (count($fullCases)) {
            $select = '('.implode('+', $fullCases).')';
        } else {
            $select = new Zend_Db_Expr('0');
        }

        return $select;
    }

    protected function _getWhereCondition($arWords, $attributes)
    {
        foreach ($arWords as $key => $array) {
            $result[] = $this->_buildWhere($key, $array);
        }

        $where = '(' . join(' AND ', $result) . ')';

        return $where;
    }

    protected function _buildWhere($type, $array)
    {
        if (!is_array($array)) {
            return $this->getCILike('s.data_index', $array, array('position' => 'any'), $type);
        }

        foreach ($array as $key => $subarray)
        {
            if ($key == 'or') {
                $array[$key] = $this->_buildWhere($type, $subarray);
                if (is_array($array[$key])) {
                    $array = '('.implode(' OR ', $array[$key]).')';
                }
            } elseif ($key == 'and') {
                $array[$key] = $this->_buildWhere($type, $subarray);
                if (is_array($array[$key])) {
                    $array = '('.implode(' AND ', $array[$key]).')';
                }
            } else {
                $array[$key] = $this->_buildWhere($type, $subarray);
            }
        }

        return $array;

    }

    /**
     * Retrieve attributes and merge with existing columns
     *
     * @param  Mirasvit_SearchIndex_Model_Index_Abstract $index
     * @return array
     */
    protected function _getAttributes($index)
    {
        Mage::helper('mstcore/debug')->start();

        $attributes = $index->getAttributes(true);
        $columns    = $this->_getTableColumns($index->getIndexer()->getTableName());

        foreach ($attributes as $attr => $weight) {
            if (!in_array($attr, $columns)) {
                unset($attributes[$attr]);
            }
        }
        foreach ($columns as $column) {
            if (!in_array($column, array($index->getIndexer()->getPrimaryKey(), 'store_id', 'updated'))
                && !isset($attributes[$column])) {
                $attributes[$column] = 0;
            }
        }

        return $attributes;
    }

    public function getCILike($field, $value, $options = array(), $type = 'LIKE')
    {
        $quotedField = $this->_getReadAdapter()->quoteIdentifier($field);
        return new Zend_Db_Expr($quotedField . ' '.$type.' "' . $this->escapeLikeValue($value, $options).'"');
    }

    public function escapeLikeValue($value, $options = array())
    {
        $value = addslashes($value);

        $from = array();
        $to = array();
        if (empty($options['allow_symbol_mask'])) {
            $from[] = '_';
            $to[] = '\_';
        }
        if (empty($options['allow_string_mask'])) {
            $from[] = '%';
            $to[] = '\%';
        }
        if ($from) {
            $value = str_replace($from, $to, $value);
        }

        if (isset($options['position'])) {
            switch ($options['position']) {
                case 'any':
                    $value = '%' . $value . '%';
                    break;
                case 'start':
                    $value = $value . '%';
                    break;
                case 'end':
                    $value = '%' . $value;
                    break;
            }
        }

        return $value;
    }

    protected function _getTableColumns($tableName)
    {
        Mage::helper('mstcore/debug')->start();

        $columns = array_keys($this->_getReadAdapter()->describeTable($tableName));

        return $columns;
    }
}
