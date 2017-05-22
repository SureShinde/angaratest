<?php
class Angara_Abandoncartmailchimp_Model_Cron{	

	/*
		S:VA
		This function runs using cron to send first abandon cart email to those newsletter subscribers who have left the cart
		It runs in every 15 mins(time interval set for cron to run periodically)
	*/
	public function processAutoEmailJobs(){
		try{
			$mandrillApiKey	=	Mage::helper('utilitybackend')->getMandrillApiKey();	//	S:VA
			$mandrill		= 	new Mandrill($mandrillApiKey);
			Mage::log('AC1- currentServerDateTime-> '.Mage::helper('function')->getCurrentServerDateTime()."\n\r", null, 'cron_abandon1.log', true);
		   	$currentServerDateTime	=	date("Y-m-d H:i:s", strtotime("-480 minutes", strtotime(Mage::helper('function')->getCurrentServerDateTime())));
			$to 			= 	$currentServerDateTime;				//	date('Y-m-d H:i:s', $time);		//	todays date
			$lastTime 		= 	strtotime($to) - (86400 * 2);		//	$time - 86400;					//	time diff in 1 day
			$from 			= 	date('Y-m-d H:i:s', $lastTime);		//	yesterday date (date before 24 hours from current time)
			$lastTimemail	= 	strtotime($to) - 1296000;			//	time diff in 15 day
			$frommail 		= 	date('Y-m-d H:i:s', $lastTimemail);	//	get 15 days older date
			
			Mage::log('AC1- started with values '.'currentServerDateTime-> '.$currentServerDateTime."\n\r", null, 'cron_abandon1.log', true);
			//Mage::log('AC1- started with values '.'currentServerDateTime-> '.$currentServerDateTime.' to-> '.$to.' lastTime-> '.$lastTime.' from-> '.$from.' lastTimemail-> '.$lastTimemail.' frommail-> '.$frommail."\n\r", null, 'cron_abandon1.log', true);
			
			/*
				Check if we have abandon cart data in table mailchimpsession
			*/
			$toBufferTime		=	date("Y-m-d H:i:s", strtotime("-240 minutes", strtotime($to)));		//	 4 hours before time to give delay in sending abandon cart email to users
			//$toBufferTime		=	$to;		//	To do testing instantly
			$mailchimpsessions	=  Mage::getModel('abandoncartmailchimp/session')->getCollection()
											->addFieldToFilter('email_count','0')
											->addFieldToFilter('check_count','0')
											->addFieldToFilter('created_at', array('from' => $from, 'to' => $toBufferTime))
											->load();
											//->load(1);die;
			
			$mailchimpsessionsQuery	=	(string)$mailchimpsessions->getSelect();
			Mage::log('AC1 - Check who abandoned cart '.$mailchimpsessionsQuery."\n\r", null, 'cron_abandon1.log', true);
			if($mailchimpsessions->getSize() == 0){
				Mage::log('AC1 - Nobody abandon cart from '.$from.' to '.$to ."\n\r", null, 'cron_abandon1.log', true); 	
				return false;
			}
			
			/*
				Getting customers email who placed orders in last 1 day
			*/
			$toAheadBufferTime	=	date("Y-m-d H:i:s", strtotime("+240 minutes", strtotime($to)));		//	4 hours ahead time for getting orders placed
			$orders 			= 	Mage::getModel('sales/order')->getCollection()    
										 ->addAttributeToSelect('customer_email')
										 ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $toAheadBufferTime))
										 ->load();
			$ordersQuery	=	(string)$orders->getSelect();
			Mage::log('AC1 - Get the 1 day old orders '.$ordersQuery."\n\r", null, 'cron_abandon1.log', true);
			
			$emailIdsofOrdersDone	=	array();
			foreach ($orders as $order){   
				$emailIdsofOrdersDone[]	=	$order->getCustomerEmail(); 
			}
			
			/*
				Getting emails of users who have unsubscribed 
				SELECT `main_table`.* FROM `mailchimp_unsubscribers` AS `main_table`;
			*/
			$unsubsinDBdata			=  	Mage::getModel('abandoncartmailchimp/unsubscriber')->getCollection()->getData();
			$unsubsinDBdataQuery	=	'SELECT `main_table`.* FROM `mailchimp_unsubscribers` AS `main_table`';
			Mage::log('AC1 - Get unsubscribers data '.$unsubsinDBdataQuery."\n\r", null, 'cron_abandon1.log', true);

			$unsubsinDB				=	array();				
			foreach($unsubsinDBdata as $unsubData){ 
				 $unsubsinDB[]		=	$unsubData['unsubscriber_email'];
			}
		  	
			/*
				Getting data of those who have received first email in last 15 days from todays date
			*/
		  	$mailchimpsent	= 	Mage::getModel('abandoncartmailchimp/sent')->getCollection()
								  ->addFieldToFilter('email_count','1')
								  ->addFieldToFilter('created_at', array('from' => $frommail, 'to' => $to))
								  //->load(1);die;
								  ->load();
								  
			$mailchimpsentQuery	=	(string)$mailchimpsent->getSelect();
			Mage::log('AC1 - Check who received first email '.$mailchimpsentQuery."\n\r", null, 'cron_abandon1.log', true);
			
			$mailchimpsent->getData(); 
			$oncesentemail	=	array();				
			foreach($mailchimpsent as $sent){ 
				 $oncesentemail[]		=	$sent['sent_email'];
			}
	 	 // add field to filter for resuming days for resending emails to same email Id to whom we have sent email once.
	  
			if($mailchimpsessions->getSize() > 0){					
				foreach($mailchimpsessions as $mailchimpsession){
				unset($product); unset($freeproduct); unset($detail); unset($count); unset($totalvote);	unset($vote); unset($voteurl); unset($productUrl); unset($sessionidarray); unset($highestvote); unset($ratings); unset($reviews); unset($rating); unset($latestquoteid); unset($email); unset($fname); unset($lname); unset($abandonCart); unset($visitortotalsessioncollection); unset($vistorsessionallquote); unset($latestquotedata); unset($i); unset($j); unset($k); unset($freeproduct); unset($total); unset($item);									 
									 
			  	$emailcount		=	$mailchimpsession->getEmailCount(); 
			  	$checkcount		=	$mailchimpsession->getCheckCount(); 
			  	if($checkcount==0 && $emailcount==0) {
					$email		=	$mailchimpsession->getVisitorEmail();					
					$fname		=	$mailchimpsession->getVisitorFirstname();
					$lname		=	$mailchimpsession->getVisitorLastname();
				
				 	$sessionsdata	= Mage::getModel('abandoncartmailchimp/session')->getCollection()
											->addFieldToFilter('email_count','0')
											->addFieldToFilter('check_count','0')
											->addFieldToFilter('created_at', array('from' => $from, 'to' => $to))
											->addFieldToFilter('visitor_email',$email)
											->load();
											//->load(1);die;
					
					$sessionsdataQuery	=	(string)$sessionsdata->getSelect();
					Mage::log('AC1 - Check if '.$email.' already received first email  '.$sessionsdataQuery."\n\r", null, 'cron_abandon1.log', true);
				 
					foreach($mailchimpsessions as $mailchimpsession) { 
						if($mailchimpsession->getVisitorEmail()== $email){ 	
							Mage::log('AC1 - Setting check_count to 1 for email '.$email."\n\r", null, 'cron_abandon1.log', true);
							$mailchimpsession->setCheckCount(1)->save(); 		//	Setting check_count to 1 
						}
					}
					
					/*
						if email is not in unsubscribed list
									not in placed orders list
									not in once list of those who have received first email
					*/
					Mage::log('AC1 - Email in queue is '.$email.' and previous orders emails are '. print_r($emailIdsofOrdersDone, true)."\n\r", null, 'cron_abandon1.log', true); 
					if(!in_array($email,$unsubsinDB) &&  !in_array($email, $emailIdsofOrdersDone) && !in_array($email, $oncesentemail)){ 
						Mage::log('AC1 - Sending first abandon email started for email '.$email."\n\r", null, 'cron_abandon1.log', true); 
						
						/*$sessionsdata	= Mage::getModel('abandoncartmailchimp/session')->getCollection()
												->addFieldToFilter('email_count','0')
												->addFieldToFilter('check_count','0')
												->addFieldToFilter('created_at', array('from' => $from, 'to' => $to))
												->addFieldToFilter('visitor_email',$email)
												->load();
												//->load(1);die;
						
						$sessionsdataQuery	=	(string)$sessionsdata->getSelect();
						Mage::log('AC1 - Check if '.$email.' already received first email  '.$sessionsdataQuery."\n\r", null, 'cron_abandon1.log', true);*/
							
						$sessionidarray =array();
						foreach($sessionsdata as $data){ 
							$sessionidarray[] = $data->getSessionId();
						}		
						Mage::log('AC1 - Checking session data for email '.$email. print_r($sessionidarray, true)."\n\r", null, 'cron_abandon1.log', true); 
								
						if(!empty($sessionidarray)){
							Mage::log('AC1 - Session exist for '.$email."\n\r", null, 'cron_abandon1.log', true); 	
							$visitortotalsessioncollection	=	Mage::getModel('abandoncartmailchimp/visitor')->getCollection();
							$vistorsessionallquote			=	$visitortotalsessioncollection->addFieldToFilter('session_id',$sessionidarray)
																	->addFieldToFilter('created_at', array('from' => $from, 'to' => $to))
																	->load();
							
							$vistorsessionallquoteQuery	=	(string)$vistorsessionallquote->getSelect();
							Mage::log('AC1 - Check all the sessions '.$vistorsessionallquoteQuery."\n\r", null, 'cron_abandon1.log', true);
					
							$latestquotedata				=	end($vistorsessionallquote->getData());
							$latestquoteid					=	$latestquotedata['quote_id'];	
							if( isset($latestquoteid)){
								Mage::log('AC1 - Quote id is '.$latestquoteid.' for '.$email."\n\r", null, 'cron_abandon1.log', true); 	
								$abandonCart				= 	Mage::getModel('abandoncartmailchimp/visitor')->getCollection()
																	->addFieldToFilter('quote_id',$latestquoteid)
																	->setOrder('quote_item_id','ASC')
																	->load();						 
								$product = array();
								$freeproduct;
								$i=0; //counting of product in cart
								$j=0; // counting of gift products
								foreach( $abandonCart as $cartitem)	{
									$sku	=	$cartitem->getProductSku();
									if($sku[0] != 'F'){ 
										$i	=	$i+1;
										$couponcode					=	$cartitem->getCouponCode();
										$product[$i][name]    		=	$cartitem->getProductName();
										$product[$i][qty]     		=	$cartitem->getQuantity();
										$product[$i][sku]     		=	$cartitem->getProductSku();	
										$product[$i][symbol]  		=	$cartitem->getCurrencySymbol();	
										$product[$i][original_price]=	$cartitem->getProductOriginalPrice(); 		//	S:VA								 
										$product[$i][price]   		=	$cartitem->getProductPrice(); 
										 
										$product[$i][image]   		=	$cartitem->getProductImage();
										$product[$i][id]      		=	$cartitem->getProductId();
										$product[$i][url]     		=	$cartitem->getProductUrl();
										 
										$product[$i][ring_size]   	=	$cartitem->getRingSize();
										$product[$i][band_width]  	=	$cartitem->getBandWidth();
										$product[$i][stone_size]  	=	$cartitem->getStoneSize();
										$product[$i][metal_type]  	=	$cartitem->getMetalType();
										$product[$i][stone_grade] 	=	$cartitem->getStoneGrade();
										$abandon_email_url          =   $cartitem->getShareUrl();
										if($product[$i][stone_grade] =='A') {
											 $product[$i][stone_grade]='A+-+good';
										}
										if($product[$i][stone_grade] =='AA'){
											$product[$i][stone_grade]='AA+-+better';
										}
										if($product[$i][stone_grade] =='AAA'){
											$product[$i][stone_grade]='AAA+-+best';
										}
										if($product[$i][stone_grade] =='AAAA'){
											$product[$i][stone_grade]='AAAA+-+best';
										}								 
										$product[$i][url] = $product[$i][url]."/?ring_size=".$product[$i][ring_size]."&band_width=".$product[$i][band_width]."&metal1_type=".$product[$i][metal_type]."&stone1_size=".$product[$i][stone_size]."&stone1_grade=".$product[$i][stone_grade];
										
										if(isset($product[$i][id])){
											$reviews = Mage::getModel('review/review')
															->getResourceCollection()
															->addStoreFilter(Mage::app()->getStore()->getId()) 
															->addEntityFilter('product', $product[$i][id])
															->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
															->setDateOrder()
															->addRateVotes();
	
											$ratings = array();
											if (count($reviews) > 0) {
													foreach ($reviews->getItems() as $review) {
														foreach( $review->getRatingVotes() as $vote ) {
															$ratings[] = $vote->getPercent();
														}
													}
											}
											arsort($ratings);
											
											$highestvote = reset($ratings);
											if(isset($highestvote)) {
												foreach ($reviews->getItems() as $review) {  
													foreach( $review->getRatingVotes() as $vote ) {
														$rating = $vote->getPercent();
													}
													if($rating==$highestvote){
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
													 }	//	end if
												}	//	 end foreach
											}
										}
									}else{ 
										if($j==0){
											$freeproduct = $cartitem->getProductName();
											$j++;
										}else{	
											$freeproduct =  $freeproduct." + ".$cartitem->getProductName();
											$j++;
										}								
									}
										
								}	//	 end foreach
								$freegiftqty	= 	$j;
								$freegiftprice	= 	$product[1][symbol].'0';
										
								if(!empty($product )){
									$k=0;
									foreach($product as $item){
										if($k < 12){
											$itemprice=str_replace($item[symbol],'',$item[price]);
											$itemprice=str_replace( ',', '', $itemprice );					
											$total=	$total+(float)$itemprice;	
											
											//	S:VA
											$itemOriginalPrice		=	str_replace( ',', '', str_replace($item[symbol],'',$item[original_price]));
											$totalDiscountAmount	=	$totalDiscountAmount + (float)$itemOriginalPrice;
											$k=$k+1;
										}							
									}
									$total	=	$product[1][symbol].$total;
									//	S:VA
									$totalDiscountAmount	=	$product[1][symbol].$totalDiscountAmount;						
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
												//	S:VA
												array(
													'name' => 'TOTAL_DISCOUNT_AMOUNT',
													'content' => $totalDiscountAmount),
												//	E:VA
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
												//	S:VA
												array(
													'name' => 'PRODUCT1ORIGINALPRICE',
													'content' => $product[1][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT2ORIGINALPRICE',
													'content' => $product[2][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT3ORIGINALPRICE',
													'content' => $product[3][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT4ORIGINALPRICE',
													'content' => $product[4][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT5ORIGINALPRICE',
													'content' => $product[5][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT6ORIGINALPRICE',
													'content' => $product[6][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT7ORIGINALPRICE',
													'content' => $product[7][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT8ORIGINALPRICE',
													'content' => $product[8][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT9ORIGINALPRICE',
													'content' => $product[9][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT10ORIGINALPRICE',
													'content' => $product[10][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT11ORIGINALPRICE',
													'content' => $product[11][original_price]),
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
												//	S:VA
												array(
													'name' => 'PRODUCT12ORIGINALPRICE',
													'content' => $product[12][original_price]),
												array(
													'name' => 'PRODUCT12PRICE',
													'content' => $product[12][price]),										
												array(
													'name' => 'PRODUCT12REVIEWVOTE',
													'content' => $product[12][reviewvote]),
												array(
													'name' => 'PRODUCT12REVIEWVOTEURL',
													'content' => $product[12][reviewvoteurl]),
												array(
													'name' => 'ABANDONEMAILURL',
													'content' => $abandon_email_url),
												
												
													
												
										))));
									$template_content = array();					       
									$template_name = 'cart_abandoned1';
									Mage::log($message,null,"messag0.log",true); 									
									$response = $mandrill->messages->sendTemplate( $template_name, $template_content, $message);
									//var_dump($response);							
									if($response[0][status] =='sent') {
									   Mage::getModel('abandoncartmailchimp/sent')   	
											->setSentEmail($email)
											->setEmailCount(1)
											->setVisitorFirstname($fname)
											->setQuoteId($latestquoteid)
											->setCreatedAt(now())								
											->save(); 
										Mage::log('AC1- First abandon email sent to email '.$email."\n\r", null, 'cron_abandon1.log', true); 
									 }else {									
										Mage::getModel('abandoncartmailchimp/sent')   	
											->setSentEmail($email)
											->setEmailCount(0)
											->setVisitorFirstname($fname)
											->setQuoteId($latestquoteid)
											->setCreatedAt(now())								
											->save(); 
										Mage::log('AC1 - Email not sent to '.$email.' due to error from MANDRILL'."\n\r", null, 'cron_abandon1.log', true); 
									}
								}else{
									Mage::log('AC1 - Email not sent as empty product for '.$email."\n\r", null, 'cron_abandon1.log', true); 	
								}							
							}else{
								Mage::log('AC1 - Email not sent because latestquoteid is blank for '.$email."\n\r", null, 'cron_abandon1.log', true); 	
							}
						}else{
							Mage::log('AC1 - Email not sent because sessionidarray is empty for '.$email."\n\r", null, 'cron_abandon1.log', true); 		
						}
					}else{
						Mage::log('AC1 - Email not sent because email is in unsubscribed list OR not in 1 day old placed orders list OR not in once sent email list '.$email."\n\r", null, 'cron_abandon1.log', true); 
					}
	         	}	//	 end if checkcount
		 	}	//	end foreach
			}else{
				Mage::log('AC1 - Nobody abandon cart from '.$from.' to '.$to ."\n\r", null, 'cron_abandon1.log', true); 	
			}
		}catch(Exception $e) { 
			Mage::logException($e); 
			Mage::log('AC1 - Exception for abandon email '.$e->getMessage()."\n\r", null, 'cron_abandon1.log', true); 
		}
	}
	
	
	/*
		S:VA
		This function runs using cron to send second abandon cart email to those newsletter subscribers who have left the cart
		It runs in every 15 mins(time interval set for cron to run periodically)
	*/
	public function processFollowEmailJobs(){
		//	Let the cron run only twice a day
		if(Mage::helper('function')->canRunCron('abandon2') === false){
			return false;
		}
		try{
			$currentServerDateTime	=	Mage::helper('function')->getCurrentServerDateTime();
			$mandrillApiKey	=	Mage::helper('utilitybackend')->getMandrillApiKey();	//	S:VA
			$mandrill		= 	new Mandrill($mandrillApiKey);
			 
			$timefirst 		= 	time()-129600;
		   	$tofirst 		= 	date('Y-m-d H:i:s', $timefirst); // 6.5 days 
		   	$time 			= 	time();
		   	//$to 			= 	date('Y-m-d H:i:s', $time); // current time 
		   	$lastTime 		= 	$time - 216000;
			
			
			$to 			= 	$currentServerDateTime;				//	date('Y-m-d H:i:s', $time);		//	todays date
			$lastTime 		= 	strtotime($to) - (86400 * 2);		//	$time - 86400;					//	time diff in 1 day
		  	$from 			= 	date('Y-m-d H:i:s', $lastTime); 
			$toOneDayOldTime= 	date('Y-m-d H:i:s', strtotime($currentServerDateTime) - (86400));
		   	$orders 		= 	Mage::getModel('sales/order')->getCollection()    
								 ->addAttributeToSelect('customer_email')
								 ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
								 ->load();	
			
			$ordersQuery	=	(string)$orders->getSelect();
			Mage::log('AC2 - ordersQuery '.$ordersQuery."\n\r", null, 'cron_abandon2.log', true);
			
			$emailIdsofOrdersDone=array();
			foreach ($orders as $order){   
				$emailIdsofOrdersDone[]=$order->getCustomerEmail(); 
			}
			$unsubsinDBdata		=  	Mage::getModel('abandoncartmailchimp/unsubscriber')->getCollection()->getData();
			$unsubsinDB			=	array();				
			foreach($unsubsinDBdata as $unsubData){ 
				$unsubsinDB[]	=	$unsubData['unsubscriber_email'];
			}		
	
			$mailchimpsentabeforesevendays		=  Mage::getModel('abandoncartmailchimp/sent')->getCollection()
														->addFieldToFilter('email_count','1')											
														->addFieldToFilter('created_at', array('from' => $from, 'to' => $toOneDayOldTime))
														->load();
			
			$mailchimpsentabeforesevendaysQuery	=	(string)$mailchimpsentabeforesevendays->getSelect();
			Mage::log('AC2 - Check who received first email '.$mailchimpsentabeforesevendaysQuery."\n\r", null, 'cron_abandon2.log', true);
			
			if($mailchimpsentabeforesevendays->getSize() > 0){
				foreach($mailchimpsentabeforesevendays as $mailchimpsentabeforesevenday){		  
					unset($userquoteid);unset($email);unset($abandonCart);unset($product);unset($productsku);unset($i);unset($j);unset($k);unset($cartitem);unset($firstname);unset($freeproduct);unset($detail);unset($count);unset($totalvote);unset($vote);unset($voteurl);unset($productUrl); unset($sessionidarray);unset($highestvote);unset($ratings);unset($reviews);unset($rating);unset($latestquoteid);unset($lname);unset($visitortotalsessioncollection);unset($vistorsessionallquote);unset($latestquotedata);unset($freeproduct);unset($total);unset($item);

					$userquoteid	= 	$mailchimpsentabeforesevenday->getQuoteId();
					$email			= 	$mailchimpsentabeforesevenday->getSentEmail(); 
					$firstname		=	$mailchimpsentabeforesevenday->getVisitorFirstname();
					
					if(!in_array($email,$unsubsinDB) &&  !in_array($email, $emailIdsofOrdersDone) ){		 
						$abandonCart= Mage::getModel('abandoncartmailchimp/visitor')->getCollection()->addFieldToFilter('quote_id',$userquoteid)->setOrder('product_price','DESC')->load();			 
						$product = array();									
						$productsku=array();
						$i=0;
						foreach($abandonCart as $cartitem){
							$sku=$cartitem->getProductSku();									  
							$sku = explode('-', $sku);
							$sku = $sku[0];								 
							if($sku[0] != 'F' && !in_array($sku, $productsku)){  
								$i=$i+1;
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
								
								$product[$i][qty]     =$cartitem->getQuantity();								
								$product[$i][symbol]  =$cartitem->getCurrencySymbol();	
								$product[$i][original_price]   =$cartitem->getProductOriginalPrice(); 																		
								$product[$i][price]   =$cartitem->getProductPrice();							
								$product[$i][id]      =$cartitem->getProductId();
								$abandon_email_url          =   $cartitem->getShareUrl();
								if($product[$i][stone_grade] =='A'){
									$product[$i][stone_grade]='A+-+good';
								}
								if($product[$i][stone_grade] =='AA'){
									$product[$i][stone_grade]='AA+-+better';
								}
								if($product[$i][stone_grade] =='AAA'){
									$product[$i][stone_grade]='AAA+-+best';
								}
								if($product[$i][stone_grade] =='AAAA'){
									$product[$i][stone_grade]='AAAA+-+best';
								}
								$product[$i][url] = $product[$i][url]."/?ring_size=".$product[$i][ring_size]."&band_width=".$product[$i][band_width]."&metal1_type=".$product[$i][metal_type]."&stone1_size=".$product[$i][stone_size]."&stone1_grade=".$product[$i][stone_grade];
								
								if(isset($product[$i][id])){
									$reviews = Mage::getModel('review/review')
													->getResourceCollection()
													->addStoreFilter(Mage::app()->getStore()->getId()) 
													->addEntityFilter('product', $product[$i][id])
													->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
													->setDateOrder()
													->addRateVotes();
	
									$ratings = array();
									if(count($reviews) > 0){
										foreach($reviews->getItems() as $review) {
											foreach($review->getRatingVotes() as $vote){
												$ratings[] = $vote->getPercent();
											}
										}
									}
									arsort($ratings);
									$highestvote = reset($ratings);
									
									if(isset($highestvote)){
										foreach($reviews->getItems() as $review){  
											foreach($review->getRatingVotes() as $vote){
												$rating = $vote->getPercent();
											}
											
											if($rating==$highestvote){
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
												$product[$i][reviewvoteurl]=str_replace('.','-',$vote);											 														break; 
											}
										}
									}
								}
								
								$partsofsku = explode('-', $product[$i][sku]);	
								$parentsku	= $partsofsku[0];		
								$productsku[] = $parentsku;							 
							}
							else{ 
								if($j==0){
									$freeproduct = $cartitem->getProductName();
									$j++;
								}
								else{	
									$freeproduct =  $freeproduct." + ".$cartitem->getProductName();
									$j++;
								}							
							}
						}								 
										
						if(!empty($product )){
							$freegiftqty= $j;
							$freegiftprice= $product[1][symbol].'0';
							
							$k=0;
							foreach($product as $item){
								if($k < 12){
									$itemprice=str_replace($item[symbol],'',$item[price]);
									$itemprice=str_replace( ',', '', $itemprice );					
									$total=	$total+(float)$itemprice;
									$itemOriginalPrice		=	str_replace( ',', '', str_replace($item[symbol],'',$item[original_price]));
									$totalDiscountAmount	=	$totalDiscountAmount + (float)$itemOriginalPrice;		
									$k=$k+1;
								}
							}
							
							$total=$product[1][symbol].$total;
							$totalDiscountAmount	=	$product[1][symbol].$totalDiscountAmount;
							
							$message = array(									
								'to' => array(array('email' => $email, 'name' => $fname)),
								'merge_vars' => array(array(
									'rcpt' => $email,
									'vars' => array(
										array(
											'name' => 'FIRSTNAME',
											'content' => $firstname),
										array(
											'name' => 'DATE2',
											'content' => date('M-d-Y', time() + 2*24*60*60)),		
										array(
											'name' => 'COUPON',
											'content' => $couponcode),
										array(
											'name' => 'TOTAL',
											'content' => $total),
										array(
											'name' => 'TOTAL_DISCOUNT_AMOUNT',
											'content' => $totalDiscountAmount),	
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
											'name' => 'PRODUCT1ORIGINALPRICE',
											'content' => $product[1][original_price]),
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
											'name' => 'PRODUCT2ORIGINALPRICE',
											'content' => $product[2][original_price]),
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
											'name' => 'PRODUCT3ORIGINALPRICE',
											'content' => $product[3][original_price]),
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
											'name' => 'PRODUCT4ORIGINALPRICE',
											'content' => $product[4][original_price]),
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
											'name' => 'PRODUCT5ORIGINALPRICE',
											'content' => $product[5][original_price]),
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
											'name' => 'PRODUCT6ORIGINALPRICE',
											'content' => $product[6][original_price]),
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
											'name' => 'PRODUCT7ORIGINALPRICE',
											'content' => $product[7][original_price]),
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
											'name' => 'PRODUCT8ORIGINALPRICE',
											'content' => $product[8][original_price]),
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
											'name' => 'PRODUCT9ORIGINALPRICE',
											'content' => $product[9][original_price]),
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
											'name' => 'PRODUCT10ORIGINALPRICE',
											'content' => $product[10][original_price]),
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
											'name' => 'PRODUCT11ORIGINALPRICE',
											'content' => $product[11][original_price]),
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
											'name' => 'PRODUCT12ORIGINALPRICE',
											'content' => $product[12][original_price]),
										array(
											'name' => 'PRODUCT12PRICE',
											'content' => $product[12][price]),								
										array(
											'name' => 'PRODUCT12REVIEWVOTE',
											'content' => $product[12][reviewvote]),
										array(
											'name' => 'PRODUCT12REVIEWVOTEURL',
											'content' => $product[12][reviewvoteurl]),
										array(
											'name' => 'ABANDONEMAILURL',
											'content' => $abandon_email_url),										
									)
								))
							);
							
							$template_content = array();	
							if(stripos($mailchimpsentabeforesevenday->getSentEmail(),'@angara.com') !== false){		
								$template_name = 'cart_followup_angara';
							}
							else{
								$template_name = 'cart_followup';
							}							
							Mage::log($message,null,"messag1.log",true);						
							$response = $mandrill->messages->sendTemplate( $template_name, $template_content, $message);
							if($response[0][status] =='sent'){
								$mailchimpsentabeforesevenday->setEmailCount(2)->save();
								Mage::log('AC2 - Email sent to '.$email."\n\r", null, 'cron_abandon2.log', true);
							}else{
								$mailchimpsentabeforesevenday->setEmailCount(3)->save();
								Mage::log('AC2 - Error in sending email to '.$email."\n\r", null, 'cron_abandon2.log', true);
							}										
						}
					}	else{
						//	Email address found in already placed order list of unsubscribed list so lets set the count = 2 so that the code don't include this email address next time
						Mage::log('AC2 - Email in queue is '.$email.' and previous orders emails are '. print_r($emailIdsofOrdersDone, true)."\n\r", null, 'cron_abandon2.log', true); 
						if(in_array($email, $emailIdsofOrdersDone)){
							Mage::log('AC2 - Email found in already placed order list. So lets set the email_count=2 for email '.$email."\n\r", null, 'cron_abandon2.log', true); 
							$mailchimpsentabeforesevenday->setEmailCount(2)->save();
						}else{
							Mage::log('AC2 - Check this case for email '.$email."\n\r", null, 'cron_abandon2.log', true); 
						}
						Mage::log('AC2 - Email not sent because email is in unsubscribed list OR not in 1 day old placed orders list OR not in once sent email list '.$email."\n\r", null, 'cron_abandon2.log', true); 
					}
				}
			}else{
				Mage::log('AC2 - Nobody abandon cart from '.$from.' to '.$toOneDayOldTime ."\n\r", null, 'cron_abandon2.log', true); 	
			}
		}
		catch(Exception $e)
		{ 
			Mage::logException($e); 
		}
	}   
   
   
   	/*
	
	*/
    public function unsubsynchronization(){
		try{
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
				 // var_dump('done');
			}catch(Exception $e){ 
			Mage::logException($e); 
	   	}
    }
}?>