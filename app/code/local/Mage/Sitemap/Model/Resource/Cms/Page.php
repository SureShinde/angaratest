<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sitemap cms page collection model
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sitemap_Model_Resource_Cms_Page extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init resource model (catalog/category)
     *
     */
    protected function _construct()
    {
        $this->_init('cms/page', 'page_id');
    }

    /**
     * Retrieve cms page collection array
     *
     * @param unknown_type $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $pages = array();

        $select = $this->_getWriteAdapter()->select()
            ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName(), 'identifier AS url'))
            ->join(
                array('store_table' => $this->getTable('cms/page_store')),
                'main_table.page_id=store_table.page_id',
                array()
            )
            ->where('main_table.is_active=1')
            ->where('store_table.store_id IN(?)', array(0, $storeId));
        $query = $this->_getWriteAdapter()->query($select);
        while ($row = $query->fetch()) {
            if ($row['url'] == Mage_Cms_Model_Page::NOROUTE_PAGE_ID) {
                continue;
            }
			// Angara Modification Start
			// start skip more about pages from sitemap - anil jain - 27-12-2011					
			$pos_new1 = stripos($row['url'], 'more-about');
			$pos_new2 = stripos($row['url'], 'home');
			$pos_new3 = stripos($row['url'], 'no-route');
			$pos_new4 = stripos($row['url'], 'about-magento-demo-store');
			$pos_new5 = stripos($row['url'], 'enable-cookies');
			$pos_new6 = stripos($row['url'], 'featured');
			$pos_new7 = stripos($row['url'], 'earrings/diamondstud');
			if ($pos_new1 !== false || $pos_new2 !== false || $pos_new3 !== false || $pos_new4 !== false || $pos_new5 !== false || $pos_new6 !== false || $pos_new7 !== false) {
				continue;
			}	
			// end skip more about pages from sitemap - anil jain - 27-12-2011
			// Angara Modification End
            $page = $this->_prepareObject($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    /**
     * Prepare page object
     *
     * @param array $data
     * @return Varien_Object
     */
    protected function _prepareObject(array $data)
    {
        $object = new Varien_Object();
        $object->setId($data[$this->getIdFieldName()]);
        $object->setUrl($data['url']);

        return $object;
    }
}
