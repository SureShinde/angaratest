<?php	//	angara.com/api/review_incremental_feed.php?date=2014-12-07
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');

ini_set('memory_limit','1024M');		// increasing memory limit
ini_set('max_execution_time', 3000); 	// 300 seconds = 5 minutes

$directoryPath	=	$_SERVER['DOCUMENT_ROOT'].'/';
$fileName		=	'review_full_feed.xml';
$filePath		=	$directoryPath.$fileName;
$fp = fopen($filePath,"w");

 
//	Getting data to write in xml file
$xmlOutput =	'<?xml version="1.0" encoding="UTF-8"?>';
$xmlOutput.=	'<feed xmlns:vc="http://www.w3.org/2007/XMLSchema-versioning" ';
$xmlOutput.=	'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
$xmlOutput.=	'xsi:noNamespaceSchemaLocation="http://www.google.com/shopping/reviews/schema/product/2.0/product_reviews.xsd">';
$xmlOutput.=	'<aggregator><name>Sample Reviews Aggregator (if applicable)</name></aggregator>';
$xmlOutput.=	'<publisher><name>Angara.com</name><favicon>http://cdn.angara.com/skin/frontend/base/default/favicon.ico</favicon></publisher>';
//	Getting data to write in xml file

$xmlOutput.= printReview();
$xmlOutput.=	'</feed>';
$siteURL		=	Mage::getBaseUrl();
	
	//	Writing data to xml file
	$directoryPath	=	$_SERVER['DOCUMENT_ROOT'].'/media/reviews/';
	$todayDate	=	Mage::getModel('core/date')->date('Y-m-d');
	$fileName	=	'review_'.$todayDate.'.xml';
	$filePath	=	$directoryPath.$fileName;
	$file = fopen($filePath,"w");
	if(fwrite($file,$xmlOutput)){	
		echo '<a href="'.$siteURL.'media/reviews/'.$fileName.'"> Product Reviews XML</a> generated successfully.';
		fclose($file); 
	}else{
		echo $filePath .' is not writable.';
	}

function printReview() { 
	$inputDate	=	Mage::app()->getRequest()->getParam('date');
	$xmlOutput = '<reviews>';
	if($inputDate){
		$todayDate	=	$inputDate;
	}else{
		$todayDate	=	Mage::getModel('core/date')->date('Y-m-d');
	}
	$pastDate		=	date('Y-m-d', strtotime('-7 days'));
	//echo $todayDate;	echo '<br>'.$pastDate;die;
	
	$fromDate	=	$pastDate.' 00:00:00';
	$toDate		=	$todayDate.' 23:59:59';
	//$fromDate	=	'2014-04-01 00:00:00';
	//$toDate		=	'2014-04-01 23:59:59';
	$_reviews = Mage::getModel('review/review')
				->getCollection()
				->addStoreFilter(Mage::app()->getStore()->getId())
				->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
				->addFieldToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
				->addFieldToFilter('detail', array('nlike' => '%angara%'))
				->setDateOrder()
				//->setPageSize(5)
    			//->setCurPage(1)
				->addRateVotes()
				->load();
				//->load(1);die;
	$siteURL	=	Mage::getBaseUrl();
	
	foreach($_reviews as $review){
		//echo '<pre>';print_r($review->getData());die;
		// load the product for the corresponding review if You need to display product information
		$product 			= 	Mage::getModel('catalog/product')->load($review->getData('entity_pk_value'));
		$review_id			=	$review->getData('review_id');
		$created_at			=	$review->getData('created_at');
		$entity_id			=	$review->getData('entity_id');
		$entity_pk_value	=	$review->getData('entity_pk_value');
		$status_id			=	$review->getData('status_id');
		$detail_id			=	$review->getData('detail_id');
		$title				=	$review->getData('title');
		$detail				=	$review->getData('detail');
		$nickname			=	$review->getData('nickname');
		$customer_id		=	$review->getData('detail_id');
		//	Calculate the final rating vote/count
		$j=0;
		$cumulative = 0;
		foreach( $review->getRatingVotes() as $vote ) {
			//echo '<pre>';print_r($vote);die;
			$ratingCount =	$vote->getValue();
			$j++;
		}
	
	$xmlOutput.=	'<review>';
		$xmlOutput.=	'<review_id>'.$review_id.'</review_id>';
		$xmlOutput.=	'<reviewer><name is_anonymous="true">'.rssFormat($nickname).'</name><reviewer_id>'.$customer_id.'</reviewer_id></reviewer>';
		$xmlOutput.=	'<review_timestamp>'.date("c",strtotime($created_at)).'</review_timestamp>';
		$xmlOutput.=	'<title>'.rssFormat($title).'</title>';
		$xmlOutput.=	'<content>'.rssFormat($detail).'</content>';
		//$xmlOutput.=	'<pros><pro>Sleek design</pro><pro>Protects the tablet well</pro></pros>';
		//$xmlOutput.=	'<cons><con>Pricey</con></cons>';
		$xmlOutput.=	'<review_url type="singleton">'.$siteURL.$product->getUrlPath().'</review_url>';
		$xmlOutput.=	'<ratings><overall min="1" max="5">'.$ratingCount.'</overall></ratings>';
		$xmlOutput.=	'<products>
							<product>
								<product_ids>
									<gtins>
										<gtin>541710238425</gtin>
									</gtins>
									<mpns>
										<mpn>60101-10000</mpn>
									</mpns>
									<skus>
										<sku>'.$product->getSku().'</sku>
									</skus>
									<brands>
										<brand>Angara.com</brand>
									</brands>
								</product_ids>
								<product_name>'.rssFormat($product->getName()).'</product_name>
								<product_url>'.$siteURL.$product->getUrlPath().'</product_url>
							</product>
						</products>';
		$xmlOutput.=	'<is_spam>false</is_spam>';
		$xmlOutput.=	'<collection_method>post_fulfillment</collection_method>';
		$xmlOutput.=	'<transaction_id>fulfillment_transaction_11198373</transaction_id>';
	$xmlOutput.=	'</review>';
		//echo '<br>entity_pk_value->'.$entity_pk_value.'<br>title->'.$title.'<br>nickname->'.$nickname.'<br>detail->'.$detail.'<br>';
		
	}//close the foreach
	$xmlOutput .= '</reviews>';
	//add if there are deleted_reviews
	//$xmlOutput.=	'<deleted_reviews><review_id></review_id></deleted_reviews>';
	return $xmlOutput;
}

//	Function to produce output in proper rss compatible format
function rssFormat($string) {		
    $string = strip_tags($string);
	$string = str_replace('&', '&amp;', $string);
    return $string;
}