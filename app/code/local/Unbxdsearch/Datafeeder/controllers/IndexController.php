<?php 
class Unbxdsearch_Datafeeder_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
    	$this->loadLayout();
    	
    	$this->_addContent($this->getLayout()->createBlock('unbxdsearch_datafeeder/index')->setTemplate('datafeeder/bform.phtml'));
    	
    	$this->renderLayout();


    }
    public function setfixedtimeAction()
    {
    	$fixedtime=$this->getRequest()->getParam(fixedtime);
    	
    	$this->getUpdated("fixedtime", $fixedtime);
    	$this->getUpdated("schedule_type","fixed");
    }
    
    public function setintervaltimeAction()
    {
    	$intervaltime=$this->getRequest()->getParam(intervaltime);
    	$this->getUpdated("intervaltime", $intervaltime);
    	$this->getUpdated("schedule_type","interval");
    }
    
    
    
    
    public function cancelAction()
    {
    	
    	$this->getUpdated('doUpload', 'false');
    	$this->loadLayout();
    	 
    	$this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('datafeeder/aform.phtml')->setFormAction(Mage::getUrl('*/*/fullindex')));
    	 
    	 
    	$this->renderLayout();
    }
    
    public function resumeindexAction()
    {
    	
    	
    	$file = fopen("datafeeeder.lock","w+");
    	
    	
    	if (!flock($file,LOCK_EX)){
    		
    		$this->loadLayout();
    		$this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('datafeeder/success.phtml')->setFormAction(Mage::getUrl('*/*/fullindex')));
    		$this->renderLayout();
    		error_log("datafeeder locked");
    	}
    	else{
    		
    	
    	
    	$currentDate =date('Y-m-d H:i:s');
    	
    	
    
    
 			 
    	$url="http://angara.datafeeder.unbxdapi.com/datafeeder";
		
	
    	
    
    	$allowedvisibility = array(
    			array(
    					"finset" => array(3)
    			),
    			array(
    					"finset" => array(4)
    			),
    	);
    	$collection = Mage::getModel('catalog/product') ->getCollection()->addAttributeToSelect('*');
    	$ch = curl_init($url);
    	$totalsize = $collection->getSize();
    	$productnumber = $this->getValue('RecoverIndex');
    	 
    	$pageStart=$productnumber;
    	$pageSize=1;
    	$productnumber=0;
    	set_time_limit(0);
    	$this->getUpdated('doUpload', 'true');
    	
    	 
    	$vars=$this->doOperation($pageStart,$totalsize,$pageSize,$allowedvisibility,$url,$productnumber,$ch);    
    
    	 
    	 
    	
    	$this->getUpdated('Lastindex', $currentDate);
    	if($pageStart==$totalsize)
    		$this->getUpdated('RecoverIndex', '0');
    	 
    	
    	$this->getUpdated('Fullexport', 'false');
    
    	
    	$this->loadLayout();
    	$this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('datafeeder/success.phtml')->setFormAction(Mage::getUrl('*/*/fullindex')));
    	$this->renderLayout();
    	
    	
    
    	}
    
    }
    
    
    
    public function fullindexAction()
    {
    	
    	$file = fopen("datafeeeder.lock","w+");
    	 
    	 
    	if (!flock($file,LOCK_EX)){
    	
    		$this->loadLayout();
    		$this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('datafeeder/success.phtml')->setFormAction(Mage::getUrl('*/*/fullindex')));
    		$this->renderLayout();
    		error_log("datafeeder locked");
    		
    	}
    	else{
    	
    	
    	    	
    	$currentDate =date('Y-m-d H:i:s');
    	
       $url="http://angara.datafeeder.unbxdapi.com/datafeeder";


    	
    	 
    	$allowedvisibility = array(
    			array(
    					"finset" => array(3)
    			),
    			array(
    					"finset" => array(4)
    			),
    	);
    	$collection = Mage::getModel('catalog/product') ->getCollection()->addAttributeToSelect('*');
    	
    	
    	$ch = curl_init($url);
    	$totalsize = $collection->getSize();
    	$productnumber=0;
    	
    	$pageStart=0;
    	$pageSize=1;
    	set_time_limit(0);
       	$this->getUpdated('doUpload', 'true');
       	
       		
       	 $this->doOperation($pageStart,$totalsize,$pageSize,$allowedvisibility,$url,$productnumber,$ch);
       	       	
       	
    	
       	 $this->getUpdated('Lastindex', $currentDate);
    	if($pageStart==$totalsize)
    		$this->getUpdated('RecoverIndex', '0');
    	
    	$this->getUpdated('Fullexport', 'false');
    	
    	$this->loadLayout();    	
    	$this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('datafeeder/success.phtml')->setFormAction(Mage::getUrl('*/*/fullindex')));    	
    	$this->renderLayout();
    	flock($file,LOCK_UN);
    	error_log("unlocked");
    	 
    	}
    	
    	 
    }
    
    
    public function incrementalindexAction()
    {
    	
    	$currentDate =date('Y-m-d H:i:s');	
    	
    	
    	
    	 
    	
    	$url="http://angara.datafeeder.unbxdapi.com/datafeeder";
    	//$url="http://localhost:8080/ub/analytics";
    	
    	
    		
    		
    		//incremental index
    			
    		$allowedvisibility = array(
    				array(
    						"finset" => array(3)
    				),
    				array(
    						"finset" => array(4)
    				),
    		);
    		 
    		$fromdate=$this->getValue('Lastindex');

    		if($fromdate=="empty"){
    			$fromdate="1900-01-01 00:00:00";
    		}
    		$collection=Mage::getModel('catalog/product') ->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('updated_at',array('from'=>$fromdate,'to'=>$currentDate,'date'=>true,))->addAttributeToFilter('visibility',$allowedvisibility);
    		 
    	
    		$totalsize = $collection->getSize();
    		
    		 
    		
    		$pageStart=0;
    		$pageSize=1;    		
    		set_time_limit(0);
    	
    		$this->doIOperation($collection,$pageStart,$totalsize,$pageSize,$allowedvisibility,$url,$productnumber,$ch,$fromdate,$currentDate);
    							
    		
    		$this->getUpdated("Lastindex",$currentDate);
    		
    		if($pageStart==$totalsize)
    			$this->getUpdated('RecoverIndex', '0');
    		 
    	
    	
    	
    	$this->loadLayout();
    	$this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('datafeeder/success.phtml')->setFormAction(Mage::getUrl('*/*/fullindex')));
    	$this->renderLayout();	
    	 
    	
    }
    
    
    
    public function getValue($action)
    {
    
   			 $collection=Mage::getModel('uconfig/conf')->getCollection()
	    	-> addFieldToFilter('action',$action);
	    	
	    	$count=0;
	    	foreach($collection as $coll){
	    		$count++;
	    		$value=$coll->getvalue();
	    	}
	    	if($count==0)
		   	{
		   		
		   		$collection=Mage::getModel('uconfig/conf')
		   		->setAction($action)->setValue("empty")->save();
		   		$value="empty";
	    	}	
	    	
	    	return $value;
      }
    
    
    
    
    public function getUpdated($action,$value)
    {
    	$collection=Mage::getModel('uconfig/conf')->getCollection()
	    	->addFieldToFilter('action',$action);
	    	$count=0;
	    	foreach($collection as $coll){
	    		$count++;
	    		Mage::getModel('uconfig/conf')->load($coll->getId())->setValue($value)->save();
	    	}
	    	if($count==0)
		   	{
		   		
		   		$collection=Mage::getModel('uconfig/conf')
		   		->setAction($action)->setValue($value)->save();
	    	}	

    	 
    }
    
    
    public function doIOperation($collection,$pageStart,$totalsize,$pageSize,$allowedvisibility,$url,$productnumber,$ch,$fromdate,$currentDate)
    {
    	$url="http://angara.datafeeder.unbxdapi.com/datafeeder";
    	    	$_productCollection = Mage::getResourceModel('reports/product_collection')
    	->addAttributeToSelect('*')
    	->addOrderedQty()
    	->addAttributeToFilter('visibility', $allowedvisibility);
    	 
    	 
    	$pcArray=array();
    	 
    	foreach($_productCollection as $product){
    		$pcArray[$product->getId() ]=$product->ordered_qty;
    	}
    	
    	 
    	try
    	{
    		
    	for(;$pageStart<$totalsize;$pageStart=$pageStart+$pageSize)
    	{
    			$doUpload=$this->getValue("doUpload");
    			
    			
    			if($doUpload=='false'){
    				break;
    			}
    			else{
	 		   			
	    			
    	
    
    				if($totalsize-$pageStart<$pageSize)
    					$pageSize=$totalsize-$pageStart;
    	    
    				$collection=Mage::getModel('catalog/product') ->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('updated_at',array('from'=>$fromdate,'to'=>$currentDate,'date'=>true,))->addAttributeToFilter('visibility',$allowedvisibility);
    				$collection->clear()
    				->setPage($pageStart, $pageSize)
    				->load();
    				
    				$resultarray=array();
    				$resultarray["data"]=array();
    				
    				 
    				foreach($collection as $item)
    				{
    					 
    					try
    					{
    						$productnumber++;
    						$result=array();
    						 
    						foreach($item->getData('') as $columnHeader=>$columndata)
    						{
    						  
    							try
    							{
    								if($columndata=="Array"){
    									error_log($columndata);
    								}
    								if(is_array($columndata)){
    									$attributearray=array();
    									$data=$columndata;
    									foreach($data as $columndata){
    										if( !is_array($columndata) && !is_object($columndata) && !is_int($columndata) && !is_float($columndata) && !is_bool($columndata) && !is_resource($columndata) && !is_null($columndata)  && $columndata!="" )
    										{
    												
    											if($item->getAttributeText($columnHeader))
    											{
    				
    												$columndata=$item->getAttributeText($columnHeader);
    											}
    											try
    											{
    												$attributearray[]=urlencode(addslashes((string)$columndata));
    											}catch(Exception $ex)
    											{
    												
    												
    												$this->getUpdated('log',$ex->getMessage());
    											}
    												
    												
    												
    										}
    										else if(!is_null($columndata) &&(is_int($columndata) || is_float($columndata)||is_bool($columndata))){
    											$attributearray[]=$columndata;
    										}
    											
    									}
    									$result[$columnHeader]=$attributearray;
    								}
    				
    								if( !is_array($columndata) && !is_object($columndata) && !is_int($columndata) && !is_float($columndata) && !is_bool($columndata) && !is_resource($columndata) && !is_null($columndata)  && $columndata!="" )
    								{
    				
    									if($item->getAttributeText($columnHeader))
    									{
    											
    										$columndata=$item->getAttributeText($columnHeader);
    									}
    									try
    									{
    										$result[$columnHeader]=urlencode(addslashes((string)$columndata));
    									}catch(Exception $ex)
    									{
    											
    										error_log("unlocked");
    										$this->getUpdated('log',$ex->getMessage());
    									}
    				
    									 
    				
    								}
    								else if(!is_null($columndata) &&(is_int($columndata) || is_float($columndata)||is_bool($columndata))){
    									$result[$columnHeader]=$columndata;
    								}
    				
    							}
    							catch (Exception $Ex)
    							{
    				
    								error_log("unlocked");
    								$this->getUpdated('log',$Ex->getMessage());
    							}
    				
    						}
    				
    				
    						if(array_key_exists($item->getproductId() , $pcArray)){
    							$result["ordered_qty"]=$pcArray[$item->getproductId()];
    						}
    				
    						$categoryIds = $item->getCategoryIds();
    						$stocklevel = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($item)->getQty();
    						$result["stocklevel"]=$stocklevel;
    				
    						if($categoryIds){
    							$categoryarray=array();
    							$categoryurlarray=array();
    							foreach($categoryIds as $categoryId) {
    								try
    								{
    										
    									$category = Mage::getModel('catalog/category')->load($categoryId);
    									$categoryarray[]=urlencode(addslashes($category->getName()));
    									$categoryurlarray[]=urlencode(addslashes($category->getUrlPath()));
    								}
    								catch (Exception $Ex)
    								{
    				
    									$this->getUpdated('log',$Ex->getMessage());
    								}
    							}
    							$result["category"]=$categoryarray;
    							$result["categoryurl"]=$categoryurlarray;
    						}
    						$result["totalsize"]="".$totalsize;
    						$result["company"]="lootspot";
    						$result["productnumber"]="".$productnumber;
    						 
    						 
    						 
    						$result["log"]=$this->getValue('log');
    						$result["recoverindex"]="".$this->getValue('RecoverIndex');
    				
    						if(isset($item->new_arrival)){
    							$result["new_arrival"]="asda";
    						}
    						 
    						$resultarray["data"][]=$result;
    				
    					}
    					catch (Exception $Ex)
    					{
    						
    						error_log("unlocked");
    						$this->getUpdated('log',$Ex->getMessage());
    					}
    				
    				
    				
    				}
    				 
    				
    				
    				$vars = 'jsondoc=' .json_encode($resultarray);
    				 
    				 
    				error_log("asda".$url);
    				$ch = curl_init($url);
    				curl_setopt($ch, CURLOPT_POST ,1);
    				curl_setopt($ch, CURLOPT_POSTFIELDS    ,$vars);
    				curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    				curl_setopt($ch, CURLOPT_HEADER ,0);
    				curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    				$response = curl_exec($ch);
    				sleep(1);
    				
    				
    				if($response===false)
    				{
    					 error_log("in except");
    					throw new Exception(curl_error($ch));
    				}
    				
    				
    				
    				
    				$resultarray=null;
    				$categoryarray=null;
    				$categoryurlarray=null;
    				$result=null;
    				unset($categoryarray);
    				unset($result);
    				unset($categoryurlarray);
    				unset($resultarray);
    				$this->getUpdated('RecoverIndex', $pageStart);
    				sleep(1);
    				
    				
    				
    				
    				}
    				
    				}
    				$i=0;
    				$facetset=array();
    				$facetset["data"]=array();
    				$facetlist=array();
    				 
    				$filterableattributes = $this->getFilterableAttributes();
    				 
    				$i=0;
    				foreach ($filterableattributes as $attribute) {
    					$facetlist["facet".$i++]=$attribute->getName();
    				}
    				 
    				$facetlist["facetproperty"]="true";
    				$facetset["data"][]=$facetlist;
    				$vars = 'jsondoc=' .json_encode($facetset);
    				$ch = curl_init($url);
    				curl_setopt($ch, CURLOPT_POST ,1);
    				curl_setopt($ch, CURLOPT_POSTFIELDS    ,$vars);
    				curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    				curl_setopt($ch, CURLOPT_HEADER ,0);
    				curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    				$response = curl_exec($ch);
    				sleep(1);
    				 
    				$i=0;
    				$topset=array();
    				$topset["data"]=array();
    				$toplist=array();
    				
    				$_productCollection = Mage::getResourceModel('reports/product_collection')
    				->addAttributeToSelect('*')
    				->addOrderedQty()
    				->addAttributeToFilter('visibility', $allowedvisibility);
    				 
    				$pcArray=array();
    				error_log(sizeof($_productCollection));
    				foreach($_productCollection as $product){
    					$toplist[$product->getId()]=$product->ordered_qty;
    				}
    				$toplist["bestSellingproperty"]="true";
    				$topset["data"][]=$toplist;
    				$vars = 'jsondoc=' .json_encode($topset);
    				
    				curl_setopt($ch, CURLOPT_POST ,1);
    				curl_setopt($ch, CURLOPT_POSTFIELDS    ,$vars);
    				curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    				curl_setopt($ch, CURLOPT_HEADER ,0);
    				curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    				$response = curl_exec($ch);
    				error_log("ASDa");
    				sleep(1);
    				
    				
    				}
    				catch(Exception $ex)
    				{
    					
    					$this->getUpdated('log',$ex->getMessage());
    					error_log($ex->getMessage());
    					$ch = curl_init($url);
    					$productnumber=(int)$this->getValue('RecoverIndex');
    					$pageStart=(int)$this->getValue('RecoverIndex');
    					$this->doIOperation($collection,$pageStart,$totalsize,$pageSize,$allowedvisibility,$url,$productnumber,$ch,$fromdate,$currentDate);
    					sleep(1);
    				}
    				return $vars;
    				 
    				
    	}
    				    				    
    
    
    public function doOperation($pageStart,$totalsize,$pageSize,$allowedvisibility,$url,$productnumber,$ch)
    {
    	
    	$_productCollection = Mage::getResourceModel('reports/product_collection')
    	->addAttributeToSelect('*')
    	->addOrderedQty()
    	->addAttributeToFilter('visibility', $allowedvisibility);
    	 
    	$pcArray=array();
    	 
    	foreach($_productCollection as $product){
    		$pcArray[$product->getId() ]=$product->ordered_qty;
    	}
    	 
    	try
    	{
    	for(;$pageStart<$totalsize;$pageStart=$pageStart+$pageSize)
    	{
    			$doUpload=$this->getValue("doUpload");
    			
    			
    			if($doUpload=='false'){
    				break;
    			}
    			else{
	 		   			
	    			
	    	
	    			if($totalsize-$pageStart<$pageSize)
	    				$pageSize=$totalsize-$pageStart;
	    			 
	    			$collection = Mage::getModel('catalog/product') ->getCollection()->addAttributeToSelect('*');
	    			$collection->clear()
	    			->setPage($pageStart, $pageSize)
	    			->load();
	    			 
	    			$resultarray=array();
	    			$resultarray["data"]=array();
	    	
	    				
	    			foreach($collection as $item)
	    			{
	    				
						try
						{    				 
		    				$productnumber++;
		    				$result=array();
		    	
		    				foreach($item->getData('') as $columnHeader=>$columndata)
		    				{
		    	
		   						try
		   						{	
		   							if($columndata=="Array"){
		   								error_log($columndata);
		   							}
									if(is_array($columndata)){
										$attributearray=array();
										$data=$columndata; 
										foreach($data as $columndata){
											if( !is_array($columndata) && !is_object($columndata) && !is_int($columndata) && !is_float($columndata) && !is_bool($columndata) && !is_resource($columndata) && !is_null($columndata)  && $columndata!="" )
											{
											
												if($item->getAttributeText($columnHeader))
												{
														
													$columndata=$item->getAttributeText($columnHeader);
												}
												try
												{
													$attributearray[]=urlencode(addslashes((string)$columndata));
												}catch(Exception $ex)
												{
													flock($file,LOCK_UN);
													error_log("unlocked");
													$this->getUpdated('log',$ex->getMessage());
												}
											
												 
											
											}
											else if(!is_null($columndata) &&(is_int($columndata) || is_float($columndata)||is_bool($columndata))){
												$attributearray[]=$columndata;
											}
											
										}
										$result[$columnHeader]=$attributearray;
									}
		   							
			    					if( !is_array($columndata) && !is_object($columndata) && !is_int($columndata) && !is_float($columndata) && !is_bool($columndata) && !is_resource($columndata) && !is_null($columndata)  && $columndata!="" )
			    					{
			    							
			    						if($item->getAttributeText($columnHeader))
			    						{
			    							 
			    							$columndata=$item->getAttributeText($columnHeader);
			    						}
			    						 try
			    						 {
			    							$result[$columnHeader]=urlencode(addslashes((string)$columndata));
			    						 }catch(Exception $ex)
			    						 {
			    						 	
			    						 	error_log("unlocked");
			    						 	$this->getUpdated('log',$ex->getMessage());
			    						 }
			    						 
			    						
			    						 
			    					}
			    					else if(!is_null($columndata) &&(is_int($columndata) || is_float($columndata)||is_bool($columndata))){
			    						$result[$columnHeader]=$columndata;
			    					}
			    					
		   						}
			    				catch (Exception $Ex)
			    				{
			    					
			    					error_log("unlocked");
			    					$this->getUpdated('log',$Ex->getMessage());
			    				}
		    					 
		    				}
		    				
		    				
		    				if(array_key_exists($item->getproductId() , $pcArray)){
		    					$result["ordered_qty"]=$pcArray[$item->getproductId()];
		    				}
		    				
		    				$categoryIds = $item->getCategoryIds();
		    				$stocklevel = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($item)->getQty();
		    				$result["stocklevel"]=$stocklevel;
		    				
		    				if($categoryIds){
		    					$categoryarray=array();
		    					$categoryurlarray=array();
		    					foreach($categoryIds as $categoryId) {
		    						try
		    						{
		    		    	
			    						$category = Mage::getModel('catalog/category')->load($categoryId);
			    						$categoryarray[]=urlencode(addslashes($category->getName()));
			    						$categoryurlarray[]=urlencode(addslashes($category->getUrlPath()));
		    						}
		    						catch (Exception $Ex)
		    						{
		    						
		    							$this->getUpdated('log',$Ex->getMessage());
		    						}		    						
		    					}
		    					$result["category"]=$categoryarray;
		    					$result["categoryurl"]=$categoryurlarray;
		    				}
		    				$result["totalsize"]="".$totalsize;
		    				$result["company"]="lootspot";
		    				$result["productnumber"]="".$productnumber;
		    	
		    	
		    					
		    				$result["log"]=$this->getValue('log');
		    				$result["recoverindex"]="".$this->getValue('RecoverIndex');

		    				if(isset($item->new_arrival)){
				    				$result["new_arrival"]="asda";
		    				}
		    					
		    				$resultarray["data"][]=$result;
		    				
						}
						catch (Exception $Ex)
						{
							
							error_log("unlocked");
							$this->getUpdated('log',$Ex->getMessage());
						}
						
	    				 
	    				 
	    			}
	    			 
	    			
	    			
	    			$vars = 'jsondoc=' .json_encode($resultarray);  			 
	    			 
	    			 
	    			
	    			curl_setopt($ch, CURLOPT_POST ,1);
	    			curl_setopt($ch, CURLOPT_POSTFIELDS    ,$vars);
	    			curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
	    			curl_setopt($ch, CURLOPT_HEADER ,0);
	    			curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
	    			$response = curl_exec($ch);
	    			sleep(1);
	    			
	    			
	    			if($response===false)
	    			{
	    			 
	    				  throw new Exception(curl_error($ch));
	    			}
	    			
	    			
	    	
	    	
	    			$resultarray=null;
	    			$categoryarray=null;
	    			$categoryurlarray=null;
	    			$result=null;
	    			unset($categoryarray);
	    			unset($result);
	    			unset($categoryurlarray);
	    			unset($resultarray);
	    			$this->getUpdated('RecoverIndex', $pageStart);
	    			sleep(1);
	    		    	
	    	
	    		 
	    		 
	    	} 
	    	
    	}
    	$i=0;	
    	$facetset=array();
    	$facetset["data"]=array();
    	$facetlist=array();
    	
    	$filterableattributes = $this->getFilterableAttributes();
    	
    	$i=0;
    	foreach ($filterableattributes as $attribute) {
    		$facetlist["facet".$i++]=$attribute->getName();
    	}
    	
    	$facetlist["facetproperty"]="true";
    	$facetset["data"][]=$facetlist;
    	$vars = 'jsondoc=' .json_encode($facetset);
    	curl_setopt($ch, CURLOPT_POST ,1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS    ,$vars);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    	curl_setopt($ch, CURLOPT_HEADER ,0);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    	$response = curl_exec($ch);
    	sleep(1);
    	
    	$i=0;
    	$topset=array();
    	$topset["data"]=array();
    	$toplist=array();
    	 
    	$_productCollection = Mage::getResourceModel('reports/product_collection')
    	->addAttributeToSelect('*')
    	->addOrderedQty()
    	->addAttributeToFilter('visibility', $allowedvisibility);
    	
    	$pcArray=array();
    	error_log(sizeof($_productCollection));
    	foreach($_productCollection as $product){
    		    		$toplist[$product->getId()]=$product->ordered_qty;
    	}
    	$toplist["bestSellingproperty"]="true";
    	$topset["data"][]=$toplist;
    	$vars = 'jsondoc=' .json_encode($topset);
    	curl_setopt($ch, CURLOPT_POST ,1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS    ,$vars);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    	curl_setopt($ch, CURLOPT_HEADER ,0);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    	$response = curl_exec($ch);
    	sleep(1);
    	 
    	 
	   	}
	   	catch(Exception $ex)
	   	{	
	    		$this->getUpdated('log',$ex->getMessage());
	    		error_log($ex->getMessage());
	    		$ch = curl_init($url);
	    		$productnumber=(int)$this->getValue('RecoverIndex');
	    		$pageStart=(int)$this->getValue('RecoverIndex');
	    		$this->doOperation($pageStart,$totalsize,$pageSize,$allowedvisibility,$url,$productnumber,$ch);
	    		sleep(1);
	   	}
	   	return $vars;
    	
    	 
    }
    
    
    
    
    
    
    public function getProductCollection()
    {
    	$collection=Mage::getModel("catalog/product")->load();
    	return $collection;
    }
    
    public function getFilterableAttributes()
    {
    
    	$collection = Mage::getResourceModel('catalog/product_attribute_collection');
    	$collection
    	->setItemObjectClass('catalog/resource_eav_attribute')
    	->addStoreLabel(Mage::app()->getStore()->getId())
    	->setOrder('position', 'ASC');
    	$collection = $this->_prepareAttributeCollection($collection);
    	$collection->load();
    
    	return $collection;
    }
    
    protected function _prepareAttributeCollection($collection)
    {
    	$collection->addIsFilterableFilter();
    	
    	return $collection;
    }
    
    
  }

?>