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
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review helper
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Block_Helper extends Mage_Core_Block_Template
{
    protected $_availableTemplates = array(
        'default' => 'review/helper/summary.phtml',
        'short'   => 'review/helper/summary_short.phtml'
    );

    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        if (!$product->getRatingSummary()) {
            Mage::getModel('review/review')
               ->getEntitySummary($product, Mage::app()->getStore()->getId());
        }
        $this->setProduct($product);

        return $this->toHtml();
    }

    public function getRatingSummary()
    {
        return $this->getProduct()->getRatingSummary()->getRatingSummary();
    }

    public function getReviewsCount()
    {
		return $this->getProduct()->getRatingSummary()->getReviewsCount();
    }
	public function getCrossRating()
    {			
			$cacheHelper = Mage::helper('helloworld');
			$key = md5('CrossReview'.$this->getProduct()->getId());			
			if($row = $cacheHelper->getDataFromCache($key)){				
			}
			else{			
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');		
				//$sql        = 'select sum(reviews_count) as reviews_count, format(avg(rating_summary),0) as rating_summary from review_entity_summary as res where res.rating_summary >0 and res.store_id =1 and( (res.entity_pk_value ='.$this->getProduct()->getId().') OR res.entity_pk_value in (select ac.similar_style_id from angara_crossreviews as ac  where ac.product_id = '.$this->getProduct()->getId().'))' ;					              
				$sql =    'select reviews_count,rating_summary from CrossReviews where product_id ='.$this->getProduct()->getId();
				$row       = $connection->fetchRow($sql);						
				$cacheHelper->setKey($key)->setData($row)->saveDataInCache();
				$cachesave= array();
				if(!empty($row))
				{
					$cacheHelper->setKey($key1)->setData($row)->saveDataInCache();					
				}
				else
				{
					$cachesave['reviews_count']=0;
					$cachesave['rating_summary']=0;
					$cacheHelper->setKey($key1)->setData($cachesave)->saveDataInCache();
				}				
			}			
			if($this->getReviewsCount())
			{
				if(!empty($row))
				{
					$ReviewCount = $row['reviews_count']+$this->getReviewsCount();
					$RatingSummary = intval(($row['rating_summary']+$this->getReviewsCount()*$this->getRatingSummary())/($this->getReviewsCount()+$row['reviews_count']));
				
				}
				else
				{
					$ReviewCount =$this->getReviewsCount();
					$RatingSummary = $this->getRatingSummary();
				}
				
			}
			else
			{
				if(!empty($row))
				{
					$ReviewCount =$row['reviews_count'];
					$RatingSummary = intval(($row['rating_summary']/$row['reviews_count']));
					
				}
				else
				{
					
				}
			}
			$this->setCrossReviewsCount($ReviewCount);
			$this->setCrossRatingSummary($RatingSummary);								
    }

    public function getReviewsUrl()
    {
        return Mage::getUrl('review/product/list', array(
           'id'        => $this->getProduct()->getId(),
           'category'  => $this->getProduct()->getCategoryId()
        ));
    }

    /**
     * Add an available template by type
     *
     * It should be called before getSummaryHtml()
     *
     * @param string $type
     * @param string $template
     */
    public function addTemplate($type, $template)
    {
        $this->_availableTemplates[$type] = $template;
    }
}
