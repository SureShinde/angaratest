
<?php

class Angara_Abandoncartmailchimp_IndexController extends Mage_Core_Controller_Front_Action
{







	public function followAction()
	{
	 
	 				
	
		try
		{
	
	
		
			$recommendation= array();
			if (($handle = fopen("app/locale/en_US/Followupemail_recommended.csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
				   $suggestion= array();
					for ($c=1; $c < $num; $c++) {
						$suggestion[]= $data[$c] ;
					}
					$recommendation[$data[0]]=$suggestion;
				}
				fclose($handle);
			}
	   
			$mandrill= new Mandrill('k93tI_1-pNTbT9bFTZjD-g');	
			 
	 
			
		   $timefirst = time()-561600;
		   $tofirst = date('Y-m-d H:i:s', $timefirst); // 6.5 days 
		   $time = time();
		   $to = date('Y-m-d H:i:s', $time); // current time 
		   $lastTime = $time - 648000;
		   $from = date('Y-m-d H:i:s', $lastTime); // 7.5 days
		   $orders = Mage::getModel('sales/order')->getCollection()    
					 ->addAttributeToSelect('customer_email')
					 ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
					 ->load()  ;	
			 $emailIdsofOrdersDone=array();
			 foreach ($orders as $order)
			  {   
			   $emailIdsofOrdersDone[]=$order->getCustomerEmail(); 
			  }
			$unsubsinDBdata=  Mage::getModel('abandoncartmailchimp/unsubscriber')->getCollection()->getData();
			$unsubsinDB=array();				
			foreach($unsubsinDBdata as $unsubData)
					{ 
						 $unsubsinDB[]=$unsubData['unsubscriber_email'];
					}		
	
						
			$mailchimpsentabeforesevendays=  Mage::getModel('abandoncartmailchimp/sent')->getCollection()
												->addFieldToFilter('email_count','1')											
												->addFieldToFilter('created_at', array('from' => $from, 'to' => $tofirst))
												->load();
												
				//var_dump(		$mailchimpsentabeforesevendays->getData()); exit();						
												
			foreach($mailchimpsentabeforesevendays as $mailchimpsentabeforesevenday)
			  {
			  
			  
			  
				  unset($userquoteid);
				  unset($email);
				  unset($abandonCart);
				  unset($product);
				  unset($productsku);
				  unset($i);
				  unset($j);
				  unset($k);
				  unset($cartitem);
				  unset($suggestionsku);
				  unset($firstname);
				$userquoteid= $mailchimpsentabeforesevenday->getQuoteId();
				$email= 	$mailchimpsentabeforesevenday->getSentEmail(); 
				$firstname=$mailchimpsentabeforesevenday->getVisitorFirstname();
				
				
				if(!in_array($email,$unsubsinDB) &&  !in_array($email, $emailIdsofOrdersDone) && stripos($email, "angara.com") !==false)
				{
			 
					$abandonCart= Mage::getModel('abandoncartmailchimp/visitor')->getCollection()->addFieldToFilter('quote_id',$userquoteid)->setOrder('product_price','DESC')->load();
				
				
				
				 
										$product = array();									
										$productsku=array();
										 
										 
									$i=0;
									foreach( $abandonCart as $cartitem)
									{
									  $sku=$cartitem->getProductSku();									  
									  $sku = explode('-', $sku);
									  $sku = $sku[0];								 
									 if($sku[0] != 'F' && !in_array($sku, $productsku)	)								  
									  {  $i=$i+1;
										 $couponcode=$cartitem->getCouponCode();
										 $product[$i][name]    =$cartitem->getProductName();									
										 $product[$i][sku]     =$cartitem->getProductSku();	
										
										 $product[$i][image]   =$cartitem->getProductImage();									 
										 $product[$i][url]     =$cartitem->getProductUrl();									
										 
										 $product[$i][ring_size]   =$cartitem->getRingSize();
										 $product[$i][band_width]  =$cartitem->getBandWidth();
										 $product[$i][stone_size]  =$cartitem->getStoneSize();
										 $product[$i][metal_type]  =$cartitem->getMetalType();
										 $product[$i][stone_grade] =$cartitem->getStoneGrade();
										 
										 if($product[$i][stone_grade] =='A')
										 {$product[$i][stone_grade]='A+-+good';}
										 if($product[$i][stone_grade] =='AA')
										 {$product[$i][stone_grade]='AA+-+better';}
										 if($product[$i][stone_grade] =='AAA')
											{$product[$i][stone_grade]='AAA+-+best';}
										 if($product[$i][stone_grade] =='AAAA')
											{$product[$i][stone_grade]='AAAA+-+best';}								 
										 $product[$i][url] = $product[$i][url]."/?ring_size=".$product[$i][ring_size]."&band_width=".$product[$i][band_width]."&metal1_type=".$product[$i][metal_type]."&stone1_size=".$product[$i][stone_size]."&stone1_grade=".$product[$i][stone_grade];
										$partsofsku = explode('-', $product[$i][sku]);	
										$parentsku							=$partsofsku[0];		
										$productsku[]		  			 	=$parentsku;									
										$product[$i][recommendation] 		=$recommendation[$parentsku];
										
										 
									}
								}
										 
										 
										 /// run only if $i is less than 6
										if($i < 6 )
										{  
								 
										  $suggestionsku = array();
										  
											 for($j=0;$j<5;$j++)
											 {
												for($k=1 ; $k<=$i ; $k++)
												{	if(isset($product[$k][recommendation][$j]))
													{
														$suggestionsku[] = $product[$k][recommendation][$j];
													}
												}
											}
											
											foreach($recommendation[others] as $othersuggestion)
											{
												$suggestionsku[] = $othersuggestion;
											}
											
											
											 $suggestionsku = array_unique($suggestionsku);		
																				 
											foreach($product as $skuproduct)
											{
												$suggestionsku=array_diff($suggestionsku, $productsku);
											}
											
											
												foreach($suggestionsku as $sku)
												{
													$productbysku = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);													
													if(isset($productbysku )&& $productbysku->getVisibility() !=1 && $productbysku->getStatus()==1)
													{
														$i=$i+1;
														$product[$i][name]   = $productbysku->getShortDescription();
														$product[$i][image]  = $productbysku->getImageUrl();
														$product[$i][url]    = $productbysku->getProductUrl();
														if($i==6)
														{ break;}
														
													}
											  }
									}
									
									
									
									
									
									if(!empty($product ))							
									{
									
									
									
									   $message = array(									
										'to' => array(array('email' => $email, 'name' => $fname)),
										'merge_vars' => array(array(
											'rcpt' => $email,
											'vars' =>
											array(
												array(
													'name' => 'FIRSTNAME',
													'content' => $firstname),					
													
												array(
													'name' => 'PRODUCT1NAME',
													'content' => $product[1][name]),																					
												array(
													'name' => 'PRODUCT1IMAGE',
													'content' => $product[1][image]),
												array(
													'name' => 'PRODUCT1URL',
													'content' => $product[1][url]),												
												array(
													'name' => 'PRODUCT2NAME',
													'content' => $product[2][name]),
																						
												array(
													'name' => 'PRODUCT2IMAGE',
													'content' => $product[2][image]),
												array(
													'name' => 'PRODUCT2URL',
													'content' => $product[2][url]),	
											
												array(
													'name' => 'PRODUCT3NAME',
													'content' => $product[3][name]),
																						
												array(
													'name' => 'PRODUCT3IMAGE',
													'content' => $product[3][image]),
												array(
													'name' => 'PRODUCT3URL',
													'content' => $product[3][url]),	
												
												array(
													'name' => 'PRODUCT4NAME',
													'content' => $product[4][name]),
																					
												array(
													'name' => 'PRODUCT4IMAGE',
													'content' => $product[4][image]),
												array(
													'name' => 'PRODUCT4URL',
													'content' => $product[4][url]),	
												
												array(
													'name' => 'PRODUCT5NAME',
													'content' => $product[5][name]),
																					
												array(
													'name' => 'PRODUCT5IMAGE',
													'content' => $product[5][image]),
												array(
													'name' => 'PRODUCT5URL',
													'content' => $product[5][url]),	
												
												array(
													'name' => 'PRODUCT6NAME',
													'content' => $product[6][name]),
																							
												array(
													'name' => 'PRODUCT6IMAGE',
													'content' => $product[6][image]),
												array(
													'name' => 'PRODUCT6URL',
													'content' => $product[6][url]),	
													
												
										))));
										
										//var_dump($message); exit();
										$template_content = array();					       
										$template_name = 'cart_followup'; 		
																
										$response = $mandrill->messages->sendTemplate( $template_name, $template_content, $message);
										var_dump($response);
											
										if($response[0][status] =='sent')
										  {
										   $mailchimpsentabeforesevenday->setEmailCount(2)->save();
									
										 }
									
										else 
										 {
										
											$mailchimpsentabeforesevenday->setEmailCount(3)->save();
										}					
													
								}
				}
			}
			
		}
		
		catch(Exception $e) { 
				Mage::logException($e); 
		}
			
	}				 
	 

	public function indexAction ()

	{

 		

	try{

	   $mandrill= new Mandrill('k93tI_1-pNTbT9bFTZjD-g');	   
	   $time = time();
	   $to = date('Y-m-d H:i:s', $time);
	   $lastTime = $time - 84600;
	   $from = date('Y-m-d H:i:s', $lastTime);
	   $lastTimemail= $time-1296000;
	   $frommail = date('Y-m-d H:i:s', $lastTimemail);
	 
		$orders = Mage::getModel('sales/order')->getCollection()    
				 ->addAttributeToSelect('customer_email')
				 ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
				 ->load()  ;	
		 $emailIdsofOrdersDone=array();
		 foreach ($orders as $order)
		  {   
		   $emailIdsofOrdersDone[]=$order->getCustomerEmail(); 
		  }
		  
		$unsubsinDBdata=  Mage::getModel('abandoncartmailchimp/unsubscriber')->getCollection()->getData();
		$unsubsinDB=array();				
		foreach($unsubsinDBdata as $unsubData)
			{ 
				 $unsubsinDB[]=$unsubData['unsubscriber_email'];
			}
		  
		  
		  $mailchimpsent= Mage::getModel('abandoncartmailchimp/sent')->getCollection()
						  ->addFieldToFilter('email_count','1')
						  ->addFieldToFilter('created_at', array('from' => $frommail, 'to' => $to))
						  ->load()->getData();
						  
		  $oncesentemail=array();				
		foreach($mailchimpsent as $sent)
			{ 
				 $oncesentemail[]=$sent['sent_email'];
			}
	  // add field to filter for resuming days for resending emails to same email Id to whom we have sent email once.
	  
	  

		 $mailchimpsessions=  Mage::getModel('abandoncartmailchimp/session')->getCollection()
											->addFieldToFilter('email_count','0')
											->addFieldToFilter('check_count','0')
											->addFieldToFilter('created_at', array('from' => $from, 'to' => $to))
											->load();
											
     
		foreach($mailchimpsessions as $mailchimpsession)
		  {
								   	 unset($product);
									 unset($freeproduct);
									 unset($detail);								
									 unset($count);
									 unset($totalvote);									
									 unset($vote);
									 unset($voteurl);									
									 unset($productUrl);								 
									 unset($sessionidarray);
									 unset($highestvote);
									 unset($ratings);
									 unset($reviews);
									 unset($rating);
									 unset($latestquoteid);
									 unset($email);
									 unset($fname);
									 unset($lname);
									 unset($abandonCart);
									 unset($visitortotalsessioncollection);
									 unset($vistorsessionallquote);
									 unset($latestquotedata);
									 unset($i);
									 unset($j);
									 unset($k);
									 unset($freeproduct);
									 unset($total);
									 unset($item);
									 
									 
									 // add other variables also
		   
		  $emailcount=$mailchimpsession->getEmailCount(); 
		  $checkcount =$mailchimpsession->getCheckCount(); 
		  if($checkcount==0 &&$emailcount==0)
		  {
		        $email=$mailchimpsession->getVisitorEmail();					
				$fname=$mailchimpsession->getVisitorFirstname();
				$email=$mailchimpsession->getVisitorEmail();	
				$lname=$mailchimpsession->getVisitorLastname();
				
				 $sessionsdata= Mage::getModel('abandoncartmailchimp/session')->getCollection()
											->addFieldToFilter('email_count','0')
											->addFieldToFilter('check_count','0')
											->addFieldToFilter('created_at', array('from' => $from, 'to' => $to))
											->addFieldToFilter('visitor_email',$email)->load();
						 
			    foreach($mailchimpsessions as $mailchimpsession)
				  { if($mailchimpsession->getVisitorEmail()==$email)
				  { 	$mailchimpsession->setCheckCount(1)->save(); 
				  }
				  }
				 
						
					if(!in_array($email,$unsubsinDB) &&  !in_array($email, $emailIdsofOrdersDone) && !in_array($email, $oncesentemail) && stripos($email, "angara.com") !==false)
					{  	
                       				
					    
						$sessionidarray =array();
						foreach($sessionsdata as $data)
						{ $sessionidarray[] = $data->getSessionId();
						}					
						 if(!empty($sessionidarray))
						 {
						 $visitortotalsessioncollection=Mage::getModel('abandoncartmailchimp/visitor')->getCollection();
						 $vistorsessionallquote=$visitortotalsessioncollection->addFieldToFilter('session_id',$sessionidarray)->addFieldToFilter('created_at', array('from' => $from, 'to' => $to))->load();
						 $latestquotedata=end($vistorsessionallquote->getData());
						 $latestquoteid=$latestquotedata['quote_id'];	
							if( isset($latestquoteid))
							 {
								 	 $abandonCart= Mage::getModel('abandoncartmailchimp/visitor')->getCollection()->addFieldToFilter('quote_id',$latestquoteid)->setOrder('quote_item_id','ASC')->load();						 

									 
									 
									$product = array();
									$freeproduct;
									 
									 
								$i=0; //counting of product in cart
								$j=0; // counting of gift products
								foreach( $abandonCart as $cartitem)
								{
								  $sku=$cartitem->getProductSku();
					              if($sku[0] != 'F')								  
								  {  $i=$i+1;
									 $couponcode=$cartitem->getCouponCode();
								     $product[$i][name]    =$cartitem->getProductName();
									 $product[$i][qty]     =$cartitem->getQuantity();
									 $product[$i][sku]     =$cartitem->getProductSku();	
									 $product[$i][symbol]  =$cartitem->getCurrencySymbol();									 
									 $product[$i][price]   =$cartitem->getProductPrice(); 
									
									
									 
									 $product[$i][image]   =$cartitem->getProductImage();
									 $product[$i][id]      =$cartitem->getProductId();
									 $product[$i][url]     =$cartitem->getProductUrl();
									 
									 
									 $product[$i][ring_size]   =$cartitem->getRingSize();
									 $product[$i][band_width]  =$cartitem->getBandWidth();
									 $product[$i][stone_size]  =$cartitem->getStoneSize();
									 $product[$i][metal_type]  =$cartitem->getMetalType();
									 $product[$i][stone_grade] =$cartitem->getStoneGrade();
									 
									 if($product[$i][stone_grade] =='A')
									 {$product[$i][stone_grade]='A+-+good';}
									 if($product[$i][stone_grade] =='AA')
									 {$product[$i][stone_grade]='AA+-+better';}
									 if($product[$i][stone_grade] =='AAA')
										{$product[$i][stone_grade]='AAA+-+best';}
									 if($product[$i][stone_grade] =='AAAA')
										{$product[$i][stone_grade]='AAAA+-+best';}								 
									 $product[$i][url] = $product[$i][url]."/?ring_size=".$product[$i][ring_size]."&band_width=".$product[$i][band_width]."&metal1_type=".$product[$i][metal_type]."&stone1_size=".$product[$i][stone_size]."&stone1_grade=".$product[$i][stone_grade];
									  
															
											if(isset($product[$i][id]))
											{

												$reviews = Mage::getModel('review/review')
												->getResourceCollection()
												->addStoreFilter(Mage::app()->getStore()->getId()) 
												->addEntityFilter('product', $product[$i][id])
												->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
												->setDateOrder()
												->addRateVotes();

												$ratings = array();
												if (count($reviews) > 0) 
												{
												foreach ($reviews->getItems() as $review) {
												foreach( $review->getRatingVotes() as $vote ) {
												$ratings[] = $vote->getPercent();
												}
												}
												}
												arsort($ratings);

												$highestvote = reset($ratings);
												if(isset($highestvote)) 
												{
												foreach ($reviews->getItems() as $review) 
												{  
												foreach( $review->getRatingVotes() as $vote ) {

												$rating = $vote->getPercent();
												 
												}
												
												 if($rating==$highestvote)
												{
												 $detail= $review->getDetail();												 
												 $detail= Mage::helper('core/string')->truncate( $detail, $length = 175, $etc = '...');
												 $product[$i][reviewdetail]=$detail;	
											    
												 $product[$i][reviewtitle] = $review->getTitle();
												 $product[$i][reviewnickname]= $review->getNickname();
												  
												 $count= count($ratings);
												 $totalvote= array_sum($ratings);
												 $votereal= ($totalvote/$count)/20;
												 $vote= number_format((float)$votereal, 1, '.', '');
												 $product[$i][reviewvote]=$vote;
												 $product[$i][reviewvoteurl]=str_replace('.','-',$vote);											 
												  break; 
												 }
												}
												}
												}
									}
									else								
									{ 
										if($j==0)
										{
											$freeproduct = $cartitem->getProductName();
											$j++;
										}
										else
										{	
											$freeproduct =  $freeproduct." + ".$cartitem->getProductName();
											$j++;
										}
										
								
									}
									
								}
											$freegiftqty= $j;
											$freegiftprice= $product[1][symbol].'0';
											
											
                      
				if(!empty($product ))
							{
							 $k=0;
							foreach($product as $item)
							{
							if($k < 12)
							{
							$itemprice=str_replace($item[symbol],'',$item[price]);
							$itemprice=str_replace( ',', '', $itemprice );					
							$total=	$total+(float)$itemprice;	
							
							$k=$k+1;
							}
							
							}
							$total=$product[1][symbol].$total;
							
							
							
											

							
								
								$message = array(									
									'to' => array(array('email' => $email, 'name' => $fname)),
									'merge_vars' => array(array(
										'rcpt' => $email,
										'vars' =>
										array(
											array(
												'name' => 'FIRSTNAME',
												'content' => $fname),	
											array(
												'name' => 'COUPON',
												'content' => $couponcode),
											array(
												'name' => 'TOTAL',
												'content' => $total),
											array(
												'name' => 'FREEGIFTNAMES',
												'content' => $freeproduct),
											array(
												'name' => 'FREEGIFTPRICE',
												'content' => $freegiftprice),
											array(
												'name' => 'FREEGIFTQTY',
												'content' => $freegiftqty),
											array(
												'name' => 'PRODUCT1NAME',
												'content' => $product[1][name]),
											array(
												'name' => 'PRODUCT1QTY',
												'content' => $product[1][qty]),												
										    array(
												'name' => 'PRODUCT1IMAGE',
												'content' => $product[1][image]),
											array(
												'name' => 'PRODUCT1URL',
												'content' => $product[1][url]),	
											array(
												'name' => 'PRODUCT1PRICE',
												'content' => $product[1][price]),										
											array(
												'name' => 'PRODUCT1REVIEWVOTE',
												'content' => $product[1][reviewvote]),
											array(
												'name' => 'PRODUCT1REVIEWVOTEURL',
												'content' => $product[1][reviewvoteurl]),
											array(
												'name' => 'PRODUCT2NAME',
												'content' => $product[2][name]),
											array(
												'name' => 'PRODUCT2QTY',
												'content' => $product[2][qty]),												
										    array(
												'name' => 'PRODUCT2IMAGE',
												'content' => $product[2][image]),
											array(
												'name' => 'PRODUCT2URL',
												'content' => $product[2][url]),	
											array(
												'name' => 'PRODUCT2PRICE',
												'content' => $product[2][price]),										
											array(
												'name' => 'PRODUCT2REVIEWVOTE',
												'content' => $product[2][reviewvote]),
											array(
												'name' => 'PRODUCT2REVIEWVOTEURL',
												'content' => $product[2][reviewvoteurl]),
											array(
												'name' => 'PRODUCT3NAME',
												'content' => $product[3][name]),
											array(
												'name' => 'PRODUCT3QTY',
												'content' => $product[3][qty]),												
										    array(
												'name' => 'PRODUCT3IMAGE',
												'content' => $product[3][image]),
											array(
												'name' => 'PRODUCT3URL',
												'content' => $product[3][url]),	
											array(
												'name' => 'PRODUCT3PRICE',
												'content' => $product[3][price]),										
											array(
												'name' => 'PRODUCT3REVIEWVOTE',
												'content' => $product[3][reviewvote]),
											array(
												'name' => 'PRODUCT3REVIEWVOTEURL',
												'content' => $product[3][reviewvoteurl]),
											array(
												'name' => 'PRODUCT4NAME',
												'content' => $product[4][name]),
											array(
												'name' => 'PRODUCT4QTY',
												'content' => $product[4][qty]),												
										    array(
												'name' => 'PRODUCT4IMAGE',
												'content' => $product[4][image]),
											array(
												'name' => 'PRODUCT4URL',
												'content' => $product[4][url]),	
											array(
												'name' => 'PRODUCT4PRICE',
												'content' => $product[4][price]),										
											array(
												'name' => 'PRODUCT4REVIEWVOTE',
												'content' => $product[4][reviewvote]),
											array(
												'name' => 'PRODUCT4REVIEWVOTEURL',
												'content' => $product[4][reviewvoteurl]),
											array(
												'name' => 'PRODUCT5NAME',
												'content' => $product[5][name]),
											array(
												'name' => 'PRODUCT5QTY',
												'content' => $product[5][qty]),												
										    array(
												'name' => 'PRODUCT5IMAGE',
												'content' => $product[5][image]),
											array(
												'name' => 'PRODUCT5URL',
												'content' => $product[5][url]),	
											array(
												'name' => 'PRODUCT5PRICE',
												'content' => $product[5][price]),										
											array(
												'name' => 'PRODUCT5REVIEWVOTE',
												'content' => $product[5][reviewvote]),
											array(
												'name' => 'PRODUCT5REVIEWVOTEURL',
												'content' => $product[5][reviewvoteurl]),
											array(
												'name' => 'PRODUCT6NAME',
												'content' => $product[6][name]),
											array(
												'name' => 'PRODUCT6QTY',
												'content' => $product[6][qty]),												
										    array(
												'name' => 'PRODUCT6IMAGE',
												'content' => $product[6][image]),
											array(
												'name' => 'PRODUCT6URL',
												'content' => $product[6][url]),	
											array(
												'name' => 'PRODUCT6PRICE',
												'content' => $product[6][price]),										
											array(
												'name' => 'PRODUCT6REVIEWVOTE',
												'content' => $product[6][reviewvote]),
											array(
												'name' => 'PRODUCT6REVIEWVOTEURL',
												'content' => $product[6][reviewvoteurl]),
											array(
												'name' => 'PRODUCT7NAME',
												'content' => $product[7][name]),
											array(
												'name' => 'PRODUCT7QTY',
												'content' => $product[7][qty]),												
										    array(
												'name' => 'PRODUCT7IMAGE',
												'content' => $product[7][image]),
											array(
												'name' => 'PRODUCT7URL',
												'content' => $product[7][url]),	
											array(
												'name' => 'PRODUCT7PRICE',
												'content' => $product[7][price]),										
											array(
												'name' => 'PRODUCT7REVIEWVOTE',
												'content' => $product[7][reviewvote]),
											array(
												'name' => 'PRODUCT7REVIEWVOTEURL',
												'content' => $product[7][reviewvoteurl]),
											array(
												'name' => 'PRODUCT8NAME',
												'content' => $product[8][name]),
											array(
												'name' => 'PRODUCT8QTY',
												'content' => $product[8][qty]),												
										    array(
												'name' => 'PRODUCT8IMAGE',
												'content' => $product[8][image]),
											array(
												'name' => 'PRODUCT8URL',
												'content' => $product[8][url]),	
											array(
												'name' => 'PRODUCT8PRICE',
												'content' => $product[8][price]),										
											array(
												'name' => 'PRODUCT8REVIEWVOTE',
												'content' => $product[8][reviewvote]),
											array(
												'name' => 'PRODUCT8REVIEWVOTEURL',
												'content' => $product[8][reviewvoteurl]),
											array(
												'name' => 'PRODUCT9NAME',
												'content' => $product[9][name]),
											array(
												'name' => 'PRODUCT9QTY',
												'content' => $product[9][qty]),												
										    array(
												'name' => 'PRODUCT9IMAGE',
												'content' => $product[9][image]),
											array(
												'name' => 'PRODUCT9URL',
												'content' => $product[9][url]),	
											array(
												'name' => 'PRODUCT9PRICE',
												'content' => $product[9][price]),										
											array(
												'name' => 'PRODUCT9REVIEWVOTE',
												'content' => $product[9][reviewvote]),
											array(
												'name' => 'PRODUCT9REVIEWVOTEURL',
												'content' => $product[9][reviewvoteurl]),
											array(
												'name' => 'PRODUCT10NAME',
												'content' => $product[10][name]),
											array(
												'name' => 'PRODUCT10QTY',
												'content' => $product[10][qty]),												
										    array(
												'name' => 'PRODUCT10IMAGE',
												'content' => $product[10][image]),
											array(
												'name' => 'PRODUCT10URL',
												'content' => $product[10][url]),	
											array(
												'name' => 'PRODUCT10PRICE',
												'content' => $product[10][price]),										
											array(
												'name' => 'PRODUCT10REVIEWVOTE',
												'content' => $product[10][reviewvote]),
											array(
												'name' => 'PRODUCT10REVIEWVOTEURL',
												'content' => $product[10][reviewvoteurl]),
											array(
												'name' => 'PRODUCT11NAME',
												'content' => $product[11][name]),
											array(
												'name' => 'PRODUCT11QTY',
												'content' => $product[11][qty]),												
										    array(
												'name' => 'PRODUCT11IMAGE',
												'content' => $product[11][image]),
											array(
												'name' => 'PRODUCT11URL',
												'content' => $product[11][url]),	
											array(
												'name' => 'PRODUCT11PRICE',
												'content' => $product[11][price]),										
											array(
												'name' => 'PRODUCT11REVIEWVOTE',
												'content' => $product[11][reviewvote]),
											array(
												'name' => 'PRODUCT11REVIEWVOTEURL',
												'content' => $product[11][reviewvoteurl]),
											array(
												'name' => 'PRODUCT12NAME',
												'content' => $product[12][name]),
											array(
												'name' => 'PRODUCT12QTY',
												'content' => $product[12][qty]),												
										    array(
												'name' => 'PRODUCT12IMAGE',
												'content' => $product[12][image]),
											array(
												'name' => 'PRODUCT12URL',
												'content' => $product[12][url]),	
											array(
												'name' => 'PRODUCT12PRICE',
												'content' => $product[12][price]),										
											array(
												'name' => 'PRODUCT12REVIEWVOTE',
												'content' => $product[12][reviewvote]),
											array(
												'name' => 'PRODUCT12REVIEWVOTEURL',
												'content' => $product[12][reviewvoteurl]),
											
											
												
											
									))));
									
									$template_content = array();					       
									$template_name = 'cart_abandoned1'; 									
									$response = $mandrill->messages->sendTemplate( $template_name, $template_content, $message);
									var_dump($response);							
									if($response[0][status] =='sent')
						              {
									   Mage::getModel('abandoncartmailchimp/sent')   	
										->setSentEmail($email)
										->setEmailCount(1)
										->setVisitorFirstname($fname)
										->setQuoteId($latestquoteid)
										->setCreatedAt(now())								
										->save(); 
								
								     }
								
									else 
									 {
									
										Mage::getModel('abandoncartmailchimp/sent')   	
											->setSentEmail($email)
											->setEmailCount(0)
											->setVisitorFirstname($fname)
											->setQuoteId($latestquoteid)
											->setCreatedAt(now())								
											->save(); 
											}
								
								}				
					
							  }
					     }
					}
					
	          }
		 }
		}
			catch(Exception $e) { 
			Mage::logException($e); 
			}
		
	}
		
		
		

	public function unsubAction ()
	{
		
		try
			{
			   $api= new Angara_Abandoncartmailchimp_Model_MCAPI('d493d52c7900896ab9d824f88de117fc-us8');
			   $campaigns=$api->campaigns();   
			   $data=$campaigns['data'];			   
			   $campaignsIds=array();			   
			   foreach($data as $member)
				   { 
					array_push($campaignsIds, $member['id']);
				   }
			   $unsubscribers = array(); 
			   foreach($campaignsIds as $campaignId)
					{
					  $unsubs=$api->campaignUnsubscribes($campaignId); 				  
					  $unsubdata=$unsubs['data'];  
					  foreach($unsubdata as $unsubemail)
						{
							$unsubscribers[]= $unsubemail['email'];
						}				   
					}		  
				$unsubsinDBdata=  Mage::getModel('abandoncartmailchimp/unsubscriber')->getCollection()->getData();
				$unsubsinDB=array();
				foreach($unsubsinDBdata as $unsubData)
					{ 
					 $unsubsinDB[]=$unsubData['unsubscriber_email'];
					}
				  
				  foreach($unsubscribers as $apiunsubscriber)
				  {  
					  if(!in_array($apiunsubscriber, $unsubsinDB))
					  {
						   Mage::getModel('abandoncartmailchimp/unsubscriber')
						   ->setUnsubscriberEmail($apiunsubscriber)								
						   ->save(); 					  
					  }
				  }
				  var_dump('done');
			}
		   catch(Exception $e)
		   { 
				Mage::logException($e); 
		   }
	   
	   
	   
    }
	
}
	?>