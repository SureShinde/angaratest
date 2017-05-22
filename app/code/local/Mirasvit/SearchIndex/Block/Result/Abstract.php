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
 * Abstract block for render search results
 *
 * @category Mirasvit
 * @package  Mirasvit_SearchIndex
 */
abstract class Mirasvit_SearchIndex_Block_Result_Abstract extends Mage_Core_Block_Template
{
    protected $_collection = null;
    protected $_isVisible  = true;

    abstract public function getIndexCode();

    public function getIsVisible()
    {
        return $this->_isVisible;
    }

    public function setIsVisible($flag)
    {
        $this->_isVisible = $flag;

        return $this;
    }

    public function _toHtml()
    {
        if (Mage::helper('searchindex/index')->getIndex($this->getIndexCode())
            && Mage::helper('searchindex/index')->getIndex($this->getIndexCode())->isEnabled()) {
            return parent::_toHtml();
        }
    }

    public function getCollection()
    {
        if ($this->_collection == null) {
            $this->_collection = Mage::helper('searchindex/index')->getIndex($this->getIndexCode())
                ->getCollection();
        }

        return $this->_collection;
    }


    /**
     * Return pager html for current collection
     * @return html
     */
    public function getPager()
    {
        $pager = $this->getChild('pager');
        if (!$pager->getCollection()) {
            $pager->setCollection($this->getCollection());
        }
        return $pager->toHtml();
    }
}