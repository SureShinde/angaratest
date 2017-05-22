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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Unbxdsearch_Search_Model_Layer extends Mage_Catalog_Model_Layer
{
    const XML_PATH_DISPLAY_LAYER_COUNT = 'catalog/search/use_layered_navigation_count';

    /**
     * Get current layer product collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Resource_Product_Collection
     */
    public function getProductCollection()
    {
    	$query = Mage::helper('catalogsearch')->getQuery();
    	if(Mage::registry('collector')){
    		
    	}
    		
    	else{
    		$collector=new unbxd($query->getQueryText());
    		Mage::register('collector', $collector);
    	}
    	 if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            
            $collection=Mage::registry("collector")->getResult2();
    		$this->prepareProductCollection($collection);
    		$this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;    		
    	}
    	$this->setQueryText($collection->queryText);
    	
        return $collection;
    }

    /**
     * Prepare product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Resource_Product_Collection $collection
     * @return Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)		
    {
    	
	/*	$collection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addSearchFilter(Mage::helper('catalogsearch')->getQuery()->getQueryText())
            ->setStore(Mage::app()->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite();
*/
    	
    	
        //Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        //Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);
        
        
        return $this;
    	
    	
    	
    }
 	
    /**
     * Get layer state key
     *
     * @return string
     */
    public function getStateKey()
    {
        if ($this->_stateKey === null) {
            $this->_stateKey = 'Q_' . Mage::helper('catalogsearch')->getQuery()->getId()
                . '_'. parent::getStateKey();
        }
        return $this->_stateKey;
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = array())
    {
        $additionalTags = parent::getStateTags($additionalTags);
        $additionalTags[] = Mage_CatalogSearch_Model_Query::CACHE_TAG;
        return $additionalTags;
    }

    /**
     * Add filters to attribute collection
     *
     * @param   Mage_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection $collection
     * @return  Mage_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableInSearchFilter()->addVisibleFilter();
        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        $attribute = parent::_prepareAttribute($attribute);
        $attribute->setIsFilterable(Mage_Catalog_Model_Layer_Filter_Attribute::OPTIONS_ONLY_WITH_RESULTS);
        return $attribute;
    }
    
    public function getAggregator()
    {
    	return Mage::getSingleton('catalogindex/aggregation');
    }
    
    
    
    
    
    /**
     * Apply layer
     * Method is colling after apply all filters, can be used
     * for prepare some index data before getting information
     * about existing intexes
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function apply()
    {

    	$stateSuffix = '';
    	foreach ($this->getState()->getFilters() as $filterItem) {
    		$stateSuffix .= '_' . $filterItem->getFilter()->getRequestVar()
    		. '_' . $filterItem->getValueString();
    	}
    	if (!empty($stateSuffix)) {
    		$this->_stateKey = $this->getStateKey().$stateSuffix;
    	}
    
    	return $this;
    }
    
    /**
     * Retrieve current category model
     * If no category found in registry, the root will be taken
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
    	$category = $this->getData('current_category');
    	if (is_null($category)) {
    		if ($category = Mage::registry('current_category')) {
    			$this->setData('current_category', $category);
    		}
    		else {
    			$category = Mage::getModel('catalog/category')->load($this->getCurrentStore()->getRootCategoryId());
    			$this->setData('current_category', $category);
    		}
    	}    
    	return $category;
    }
    
    /**
     * Change current category object
     *
     * @param mixed $category
     * @return Mage_Catalog_Model_Layer
     */
    public function setCurrentCategory($category)
    {
    	
    	if (is_numeric($category)) {
    		$category = Mage::getModel('catalog/category')->load($category);
    	}
    	if (!$category instanceof Mage_Catalog_Model_Category) {
    		Mage::throwException(Mage::helper('catalog')->__('Category must be an instance of Mage_Catalog_Model_Category.'));
    	}
    	if (!$category->getId()) {
    		Mage::throwException(Mage::helper('catalog')->__('Invalid category.'));
    	}
    
    	if ($category->getId() != $this->getCurrentCategory()->getId()) {
    		$this->setData('current_category', $category);
    	}
    
    	return $this;
    }
    
    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
    	return Mage::app()->getStore();
    }
    
    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute_Collection
     */
    
    
    
    
    
    /**
     * Retrieve layer state object
     *
     * @return Mage_Catalog_Model_Layer_State
     */
    public function getState()
    {
    	$state = $this->getData('state');
    	if (is_null($state)) {
    		Varien_Profiler::start(__METHOD__);
    		$state = Mage::getModel('catalog/layer_state');
    		$this->setData('state', $state);
    		Varien_Profiler::stop(__METHOD__);
    	}
    
    	return $state;
    }
    
    /**
     * Get attribute sets identifiers of current product set
     *
     * @return array
     */
    protected function _getSetIds()
    {
    	$key = $this->getStateKey().'_SET_IDS';
    	$setIds = $this->getAggregator()->getCacheData($key);
    	
    	if ($setIds === null) {
    		$setIds = $this->getProductCollection()->getSetIds();
    		$this->getAggregator()->saveCacheData($setIds, $key, $this->getStateTags());
    	}
    	
    	
    	
    	return $setIds;
    }
    
}



class unbxd
{
	var $collection;
	var $jsondata;
	var $url;
	var $size=10;
	var $curpage=1;
	var $limit=36;
	var $queryText="";
	var $firstflag=true;

	public function __construct($query="")
	{
		$this->queryText=$query;
		$this->jsondata=$this->search($this->queryText);

		if($this->getSize()==0)
		{
			$this->queryText= $this->getSpellSuggestion($this->jsondata);
			if(isset($this->queryText)&&$this->queryText!="")
				$this->jsondata=$this->search(trim($this->queryText,"\""));
			
		}




	}

	public function getFilter()
	{
		return   Mage::helper('catalogsearch')->getFilter();
	}


	public function getResult2()
	{
		
	#$collection =new Resultcollection();
	if($this->collection==null){
		
	$allowed_entity_ids=array();
	$entity_ids=array();


	$orderString = array('CASE e.entity_id');

	/**
	* Build up a case statement to ensure the order of ids is preserved
	*/
	
		foreach($this->jsondata["response"]["docs"] as $i=>$docs)
		{		 
			$entity_ids[]=intval($docs["entity_id"]);
			$orderString[]= 'WHEN '.intval($docs["entity_id"]).' THEN '.$i;
		}
	
		$orderString[] = 'END';
		$orderString = implode(' ', $orderString);
		$allowed_entity_ids["in"]=$entity_ids;
			
		 
			$model=Mage::getModel('catalog/product');
			
		$this->collection= Mage::getModel('catalog/product')->getCollection()
		->addAttributeToSelect('*')->addAttributeToFilter('entity_id',$allowed_entity_ids);
		
		
		
		$this->collection->getNumFound($this->getSize());
		
		if($this->collection->getSize()){
			$this->collection->getSelect()->order(new Zend_Db_Expr($orderString));
		}

						
			
					
	}				 	
	 		return $this->collection;
	
	 			
	   }

	   public function getValue($action)
	   {

	$adapter=Mage::getSingleton('core/resource')->getConnection('core_write');
	$sql="select value from unbxdConf where action='".$action."'";
	$result=$adapter->query($sql);
	$value='';
	foreach($result as $res){
	$value=$res["value"];
	}

	 

	return $value;


	}
	public function search($queryText)
	{

			$address="http://angara.search.unbxdapi.com/";
	
	$ps=explode(" ",$queryText);
	$queryText=join("%20", $ps);

	if(!is_null(Mage::helper('catalogsearch')->getRequestVar('limit'))){
		$this->limit=intval(Mage::helper('catalogsearch')->getRequestVar('limit'));
		
		if($this->limit<=0)
			$this->limit=36;
		
	}
	
	if(!is_null(Mage::helper('catalogsearch')->getRequestVar('p'))){
		$this->curpage=intval(Mage::helper('catalogsearch')->getRequestVar('p'));
		if($this->curpage<=0)
			$this->curpage=1;
		
	}
	
	$url=$address."select?wt=json&semantic=true&handler=search&start=0&rows=".$this->limit*$this->curpage;
	$url=$url.'&q='.$queryText.'';

	if(Mage::helper('catalogsearch')->getCategory()){
	$catName=Mage::getSingleton('catalog/category')->load(Mage::helper('catalogsearch')->getCategory())->getName();
	$url=$url.'&fq=category:%22'.$catName.'%22';
		
	}

	$facetlist=Mage::helper('catalogsearch')->getFacetList();
	$faceturl="";
	if(!is_null($facetlist)){
		
			foreach($facetlist as $facetkey=>$facetvalue){
			$collection=Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect($facetkey)->addAttributeToFilter($facetkey,$facetvalue)->setPage(1,1);
			
			foreach($collection as $coll){
			$facetname=$coll->getAttributeText($facetkey);
			}
			$ps=explode(" ",$facetname);
			$facetname=join("%20", $ps);
			
			if($facetname!='')
			$faceturl=$faceturl."&fq=".$facetkey.':%22'.$facetname.'%22';
			}
			$url=$url.$faceturl;
		}


			if($this->getFilter())
			{
			if(!$this->statefilter)
				$this->statefilter=new state();
				
			$this->statefilter->createFilter();
			$peices=explode(",",$this->getFilter());
			$faceturl="";
			foreach($peices as $eachpeice)
			{

			$ps=explode(":",$eachpeice);
			$flag=0;
			foreach($ps as $p)
			{
			if($flag)
			{
			$item=$p;
			$item=explode(" ",$item);
			if(sizeof($item)>0)
				$item=join("%20",$item);
			}
			else
			{
				$cat=$p;
				$flag=1;


			}
			}

			$faceturl=$faceturl."&filter=".$cat.':%22'.$item.'%22';

			}
			$url=$url.$faceturl;
				
				
			}

			$this->url=$url;
			


			$results=json_decode(file_get_contents($url),true);

	
			return $results;
			}


			public function getFacets()
			{
			$facetObjects= array();

			if($this->jsondata["facet_counts"] != null  ){

			if($this->jsondata["facet_counts"]["facet_fields"] != null){
				

			foreach ($this->jsondata["facet_counts"]["facet_fields"] as $keyNames=>$value){
				
			$filt=explode(",",$this->getFilter());
			$catq=array();
			foreach($filt as $f)
			{

			$catlist=explode(":",$f);
				
			$catq[]=$catlist[0];
			}
			$flag=1;
				foreach($catq as $fq)
				{

				if($fq==$keyNames)
				{
				$flag=0;
			}
			}
			if($flag)
				{

				#					echo $keyNames;
				$facetKeyName=array();
					
				$facetFl =$this->jsondata["facet_counts"]["facet_fields"][$keyNames];
				$facetFlProp = array();

					
				for ($countFl=0; $countFl<sizeof($facetFl);$countFl++){

					if($countFl%2 == 0){
						$facetFlProp[$countFl/2] = array();
						#							$facetFlProp[$countFl/2]["name"] =
							$name= $facetFl[$countFl];

						#							$facetFlProp[$countFl/2]["index"] = $countFl/2;
						} else {

						$facetFlProp[($countFl-1)/2][$name] = $facetFl[$countFl];
						$facet=new FItem($keyNames,$name,$facetFl[$countFl]);
								
							$facetKeyName[] = $facet;
								
						}


						}
							$filter=new filter($keyNames,$facetKeyName);
							$facetObjects[]=$filter;
						}
							

						}
						}

						}
						$filternames=array();
						foreach($facetObjects as $filters)
						{
						$filternames[]=$filters->getName()."_filter";
						Mage::unregister($filters->getName()."_filter");
						Mage::register($filters->getName()."_filter",$filters);
				}
				Mage::unregister("filternames");
				Mage::register("filternames",$filternames);
				return $facetObjects;
}



public function getSpellSuggestion($jdata)
{

$suggestion1=array();
				$suggestion2=array();
				if(sizeof($jdata["spellcheck"]["suggestions"]) > 0)
							{

							$suggestion1["word"] = $jdata["spellcheck"]["suggestions"][sizeof($jdata["spellcheck"]["suggestions"]) -1 ]["result"];
							$suggestion1["numResults"] = $jdata["spellcheck"]["suggestions"][sizeof($jdata["spellcheck"]["suggestions"]) -1 ]["numRecords"];
							}
							if(sizeof($jdata["spellcheckNonTokenized"]["suggestions"]) > 0)
	{

								$suggestion2["word"] = $jdata["spellcheckNonTokenized"]["suggestions"][sizeof($jdata["spellcheckNonTokenized"]["suggestions"]) -1 ]["result"];
								$suggestion2["numResults"] = $jdata["spellcheckNonTokenized"]["suggestions"][sizeof($jdata["spellcheckNonTokenized"]["suggestions"]) -1 ]["numRecords"];
							}

							if(sizeof($suggestion1)>0 and sizeof($suggestion2)>0)
		{
		if($suggestion1["numResults"]>=$suggestion2["numResults"])
		{
		return $suggestion1["word"];
							}
							else
							{
							return $suggestion2["word"];
							}
							}
							if(sizeof($suggestion1)>0)
							{
							if($suggestion1["numResults"]>0)
							{
								return $suggestion1["word"];
							}
							}

							if(sizeof($suggestion2)>0)
							{
							if($suggestion2["numResults"]>0)
							{
							return $suggestion2["word"];
}
}
	

							}


							public function getResult()
							{

							return $this->jsondata["response"]["docs"];

}



							public function count()
							{
							if($this->getPageSize()>$this->getSize()-($this->curpage-1)*$this->limit)
								return $this->getSize()-($this->curpage-1)*$this->limit;
								else
									return $this->getPageSize();
}
public function displayResult()
{

	foreach($this->jsondata["response"]["docs"] as $docs)
	{
	foreach($docs as $row=>$value)
	{
	echo $row."=>".$value."<br>";
}
}
}

public function getSize()
{

	return (int)$this->jsondata["response"]["numFound"];;
	}

	public function setCurPage($curpageno)
	{
	$this->curpage=$curpageno;

	#	$this->jsondata=$this->search($this->queryText);
	return $this;
	}

	public function setPageSize($limit)
	{

	$this->limit=$limit;
	if($this->firstflag)
	{
	$this->jsondata=$this->search($this->queryText);

	$this->firstflag=false;
	}
	}

	public function getPageSize()
	{

	return (int)$this->limit;
}
	public function getCurPage()
	{
	return (int)$this->curpage;
	}
	public function getLastPageNumber()
	{
	return ceil($this->getSize()/$this->limit);
}

}


class product
	{
	var $doc;
	var $count;

	public function __construct($doc,$count)
	{
	$this->doc=$doc;
	$this->count=$count;
	}
	public function count()
	{
	return $this->count;
	}
	public function isSaleable()
	{
	return true;
	}
	public function getName()
	{
	return $this->doc["product_name"];
	}
	public function getproductId()
	{
	return $this->doc["entity_id"];
	}
	public function getRatingSummary()
	{
	return false;
	}
	public function getPrice()
	{
	return $this->doc["price"];
	}
	public function getProductUrl()
	{
	return "";
	}

	public function getAttributeByName($attribute)
	{
	return $this->doc[$attribute];
	}


	}

	class FItem extends Mage_Catalog_Model_Layer_Filter_Abstract
	{

	var $name;
	var $value;
	var $keyName;

		public function __construct($keyName,$name="",$value=0)
		{
		$this->name=$name;
		$this->value=$value;
		$this->keyName=$keyName;
	}

	public function getItemsCount()
	{
		return $this->value;
	}
	public function getLabel()
	{
	return $this->name;
	}
	public function getCount()
	{
	return $this->value;
	}
	public function getkeyName()
	{
	return trim($this->keyName,"_fq");
	}
	public function getUrl()
	{
	$filter=Mage::helper('catalogsearch')->getFilter();
	if($filter)
		$filter=$filter.",".$this->keyName.':'.$this->getLabel();
		else
			$filter=$this->keyName.':'.$this->getLabel();
			$query = array(
	    "filter"=>$filter,
			$this->getRequestVar()=>$this->getValue(),
			Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
			);
			return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
		}



		}

		class state
		{
			var $filterset =array();


			public function __construct()
			{


		}



			public function getLayer()
			{
			$layer = Mage::getSingleton('catalog/layer');
			return $layer;
		}

		public function createFilter()
		{
			$filterset=explode(",",Mage::helper('catalogsearch')->getFilter());


			foreach($filterset as $fq)
			{


			$pieces=explode(':',$fq);
				
			$filteritem=new FItem($pieces[0],$pieces[1],0);
			if(!in_array($pieces[1],$this->filterset))
			{
				

			$arr= array();
			$arr[]=$filteritem;
			$filterfacet=new statefilter($pieces[0],$arr);
				$p= array();
				$p[]=$filterfacet;
				#	        Mage::register('facet_filters',$filterfacet);
				$this->getLayer()->getState()->addFilter($filterfacet);
				$this->filterset[]=$pieces[1];
				}
				}
				}




				}

				class statefilter extends Mage_Core_Block_Template
				{
				var $keyName;
				var $facets;

				public function __construct($keyName="",$facets=array())
				{
				parent::__construct();
				$this->setTemplate('catalog/layer/filter.phtml');
				$this->keyName=$keyName;
				$this->facets=$facets;

				}
				public function getRemoveUrl()
				{
					$filterpeices=explode(",",Mage::helper('catalogsearch')->getFilter());
					$i=0;
					foreach( $filterpeices as $peices)
	{
	$pos = strpos($peices, $this->keyName);
					if($pos!==false)
					{
					unset($filterpeices[$i]);
					}
					$i++;
					}
					$filter=join(",",$filterpeices);
					if($filter==null)
						$filter=null;



						$query = array(
								"filter"=>$filter,
								$this->getRequestVar()=>$this->getValue(),
						Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
						);
						return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
					}
						





					public function getItemsCount()
					{
						return sizeof($this->facets);
					}

					public function getName()
					{
						return trim($this->keyName,"_fq");
					}

						public function getHtml()
						{
						return parent::_toHtml();
						}

						public function getItems()
						{
						return $this->facets;
						}
						public function shouldDisplayProductCount()
							{
							return true;
					}
					public function getFilter()
					{
					return $this->keyName;
					}
					public function getCleanValue()
					{
					return $this->facets[0]->getLabel();
					}
					public function getLabel()
					{
					return $this->facets[0]->getLabel();
					}
					}
					class filter extends Mage_Core_Block_Template
					{
					var $keyName;
					var $facets;

					public function __construct($keyName="",$facets=array())
					{
					parent::__construct();
					$this->setTemplate('catalog/layer/filter.phtml');
					$this->keyName=$keyName;
					$this->facets=$facets;
						
					}

					public function getItemsCount()
					{
					return sizeof($this->facets);
					}

					public function getName()
					{
					return trim($this->keyName,"_fq");
					}

					public function getHtml()
					{
					return parent::_toHtml();
					}

					public function getItems()
					{
					return $this->facets;
					}
					public function shouldDisplayProductCount()
					{

					return true;
					}
					public function getAttributeCode()
					{
		return $this->getName();
	}


}
