<?php
/**
 * rewrite by Asheesh
 */
class Angara_UtilityBackend_Block_Rewrite_Rss_Catalog_Category extends Mage_Rss_Block_Catalog_Category
{
    protected function _toHtml()
    {
        $categoryId = $this->getRequest()->getParam('cid');
		$refParam = $this->getRequest()->getParam('ref');
		
        $storeId = $this->_getStoreId();
        $rssObj = Mage::getModel('rss/rss');
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if ($category && $category->getId()) {
                $layer = Mage::getSingleton('catalog/layer')->setStore($storeId);
                //want to load all products no matter anchor or not
                $category->setIsAnchor(true);
                $newurl = $category->getUrl();
                $newurl.='?cid=afl-ls-pf&amp;utm_source=Affiliate&amp;utm_medium=LinkShare&amp;utm_campaign=LinkShare-Feed';
                
                $title = $category->getName();
                $data = array('title' => $title,
                        'description' => $title,
                        'link'        => $newurl,
                        'charset'     => 'UTF-8',
                        );

                $rssObj->_addHeader($data);

                $_collection = $category->getCollection();
                $_collection->addAttributeToSelect('url_key')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('is_anchor')
                    ->addAttributeToFilter('is_active',1)
                    ->addIdFilter($category->getChildren())
                    ->load()
                ;
                $productCollection = Mage::getModel('catalog/product')->getCollection();

                $currentCategory = $layer->setCurrentCategory($category);
                $layer->prepareProductCollection($productCollection);
                $productCollection->addCountToCategories($_collection);

                $category->getProductCollection()->setStoreId($storeId);
                /*
                only load latest 50 products
                */
                $_productCollection = $currentCategory
                    ->getProductCollection()
                    ->addAttributeToSort('updated_at','desc')
                    ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds())
                    ->setCurPage(1)
                    ->setPageSize(50)
                ;

                if ($_productCollection->getSize()>0) {
                    $args = array('rssObj' => $rssObj);

                    foreach ($_productCollection as $_product) {
                        $args['product'] = $_product;
						if($refParam){
							$args['reference'] = $refParam;
						}
                        $this->addNewItemXmlCallback($args);
                    }
                }
            }
        }
        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addNewItemXmlCallback($args)
    {  //var_dump($args);die;
		$product = $args['product'];
		$reference = $args['reference'];					//added by pankaj for bug id 498
       // echo $reference;die;
        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);

        Mage::dispatchEvent('rss_catalog_category_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            return;
        }
		
		//added by pankaj for bug id 498
		$productURL	=	$product->getProductUrl();
        //echo 'reference->'.$reference;die;
		if($reference){

			if($reference == 'ls'){
				$productURL .= '?cid=afl-ls-pf&amp;utm_source=Affiliate&amp;utm_medium=LinkShare&amp;utm_campaign=LinkShare-Feed';

			}
			elseif($reference == 'cj'){
				$productURL .= '?cid=afl-cj-pf&amp;utm_source=Affiliate&amp;utm_medium=CJ&amp;utm_campaign=CJ-Feed';
			}			
		}
		//ended by pankaj for bug id 498
    //$productURL .= '?cid=afl-ls-pf&amp;utm_source=Affiliate&amp;utm_medium=LinkShare&amp;utm_campaign=LinkShare-Feed';
        $description = '<table><tr>'
                     . '<td><a href="'.$productURL.'"><img src="'
                     . $this->helper('catalog/image')->init($product, 'thumbnail')->resize(75, 75)
                     . '" border="0" align="left" height="75" width="75"></a></td>'
                     . '<td  style="text-decoration:none;">' . $product->getDescription();

        if ($product->getAllowedPriceInRss()) {
            $description.= $this->getPriceHtml($product,true);
        }
       
        $description .= '</td></tr></table>';
        $rssObj = $args['rssObj'];
		
		$data = array(
                'title'         => $product->getShortDescription(),				//added by pankaj for bug id 498
                'link'          => $productURL,				//added by pankaj for bug id 498
                'description'   => $description,
            );
        $rssObj->_addEntry($data);
    }
}
