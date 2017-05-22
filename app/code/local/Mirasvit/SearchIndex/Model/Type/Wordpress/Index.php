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
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Mirasvit_SearchIndex
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com)
 */


/**
 * Represent Index model for Aheadworks blog
 *
 * @category Mirasvit
 * @package  Mirasvit_SearchIndex
 */
class Mirasvit_SearchIndex_Model_Type_Wordpress_Index extends Mirasvit_SearchIndex_Model_Index_Abstract
{
    const INDEX_CODE = 'wordpress';

    public function getCode()
    {
        return self::INDEX_CODE;
    }

    public function getPrimaryKey()
    {
        return 'ID';
    }

    public function getAvailableAttributes()
    {
        $result = array(
            'post_title'   => Mage::helper('searchindex')->__('Post Title'),
            'post_content' => Mage::helper('searchindex')->__('Post Content'),
            'post_excerpt' => Mage::helper('searchindex')->__('Post Excerpt'),
        );

        return $result;
    }

    public function getConnection()
    {
        if (Mage::getStoreConfig('searchindex/wordpress/external_db')) {
            $connName = Mage::getStoreConfig('searchindex/wordpress/external_connection');
            return Mage::getSingleton('core/resource')->getConnection($connName);
        }

        return parent::getConnection();
    }

    public function getCollection()
    {
        $collection = Mage::getModel('searchindex/type_wordpress_collection');

        $this->joinMatched($collection, 'main_table.ID');
        return $collection;
    }
}