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


class Mirasvit_SearchIndex_Block_Result_Simpleforum extends Mirasvit_SearchIndex_Block_Result_Abstract
{
    public function getIndexCode()
    {
        return 'simpleforum';
    }

    public function getTopic($post)
    {
        $topic = Mage::getModel('forum/topic')->load($post->getParentId());

        return $topic;
    }

    public function getPostUrl($post)
    {
        $topic = $this->getTopic($post);

        return Mage::getUrl($topic->getUrlText());
    }

    public function getSearchContent($text)
    {
        $query   = Mage::helper('catalogsearch')->getQuery()->getQueryText();
        $pattern = '#(?!<.*)(?<!\w)(\w*)(' . $query . ')(\w*)(?!\w|[^<>]*(?:</s(?:cript|tyle))?>)#is';
        return preg_replace($pattern, '$1<strong>$2</strong>$3', $text);
    }
}