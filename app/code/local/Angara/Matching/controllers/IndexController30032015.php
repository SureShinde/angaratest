
<?php
                
            class Angara_Matching_IndexController extends Mage_Core_Controller_Front_Action
            {
                
				
			
                // need to add more stone codes
                public function getStone( $sku )
                {	
                    $stonecodes = array("-A-","-AA-", "-AAA-", "-AAAA-", "-GHVS-", "-HSI2-" , "-JI2-" , "-II1-");
                    foreach($stonecodes as $stonecode)
                    {
                        if(stripos($sku, $stonecode)!==false)
                        {
                            return $stonecode ;
                            break;
                        }
                    }
                 }
                 // Need to add more metal codes
                 public function getMetal($sku )
                {	
                    $metalcodes = array("-WG-","-YG-", "-PT-", "-SL-", "-RG-", "-MT-WG", "-MT-YG", "-MT-PT", "-MT-SL");
                    foreach($metalcodes as $metalcode)
                    {
                        if(stripos($sku, $metalcode)!==false)
                        {
							$metalcode = str_replace('-MT', '', $metalcode);
							$metalcode[3] ="-";
                            return $metalcode;
                            break;
                        }
                    }
                 }
                 // this have some error (below)
                
				 
				 
				 
				 
				 
				 
				 
				 
                public function getChildSku($mastersku, $stone, $metal)
                {
					// var_dump($mastersku);
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $mastersku);				
					if($product->getTypeId()== "configurable" )
					{
						$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
						//  add this condition && $child->getVisibility() != 1
						foreach($childProducts as $child )
						{ 	
							if($child->getStatus()==1 && $child->getTypeId() == 'simple' && $child->getVisibility() != 1 ) //
							{
								$childsku = $child->getSku();	
								if(stripos($childsku, $stone)!==false && stripos($childsku, $metal)!==false)
								{
									$matchedsku =  $childsku;
									break;
								}						
							}
						}
					}
					return $matchedsku;					
                }
				
				public function getChildSkuOfMaster($mastersku, $stone, $metal)
                {
					// var_dump($mastersku);
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $mastersku);				
					if($product->getTypeId()== "configurable" )
					{
						$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
						//  
						foreach($childProducts as $child )
						{ 	
							if($child->getTypeId() == 'simple' )
							{
								$childsku = $child->getSku();	
								if(stripos($childsku, $stone)!==false && stripos($childsku, $metal)!==false)
								{
									$matchedsku =  $childsku;
									break;
								}						
							}
						}
					}
					return $matchedsku;					
                }
                
                   
					
					
					//public function get
					public function getMetalCode($metal)
					{  
						
						if($metal == '14K White Gold' )
							{ $metalcode = 'WG';}
						if($metal == '14K Yellow Gold' )
							{ $metalcode = 'YG';}
						if($metal == 'Platinum' )	
							{ $metalcode = 'PT';}
						if($metal == 'Silver' )
							{ $metalcode = 'SL';}
							return "-".$metalcode."-";
					}
					public function getMetalName($metal)
					{
						$metal=  str_replace('-', '', $metal);
						if($metal == 'WG' )
							{ $metalcode = '14K White Gold';}
						if($metal == 'RG' )
							{ $metalcode = '14K Rose Gold';}
						if($metal == 'YG' )
							{ $metalcode = '14K Yellow Gold';}
						if($metal == 'PT' )	
							{ $metalcode = 'Platinum';}
						if($metal == 'SL' )
							{ $metalcode = 'Silver';}
						return $metalcode;
					}
					public function buildUrl($url, $stone, $metal)
					{  
						$metal=  str_replace('-', '', $metal);
						if($metal == 'WG' )
							{ $metalcode = '14K White Gold';}
						if($metal == 'RG' )
							{ $metalcode = '14K Rose Gold';}
						if($metal == 'YG' )
							{ $metalcode = '14K Yellow Gold';}
						if($metal == 'PT' )	
							{ $metalcode = 'Platinum';}
						if($metal == 'SL' )
							{ $metalcode = 'Silver';}
							
							
						$stone = str_replace('-', '', $stone);
						if($stone =='A')
							{ $stone='A+-+good';}
						if($stone =='AA')
							{ $stone='AA+-+better';}
						if($stone =='AAA')
							{$stone='AAA+-+best';}
						 if($stone =='AAAA')
							{$stone='AAAA+-+best';}						
						$producturl= $url."/?metal1_type=".$metalcode."&stone1_grade=".$stone;
						return $producturl;
					}
					
					public function buildUrlWithStone($url, $stone)
					{
						$stone = str_replace('-', '', $stone);
						if($stone =='A')
							{ $stone='A+-+good';}
						if($stone =='AA')
							{ $stone='AA+-+better';}
						if($stone =='AAA')
							{$stone='AAA+-+best';}
						 if($stone =='AAAA')
							{$stone='AAAA+-+best';}	
						if($stone =='GHVS')
							{$stone='G-H VS';}	
						if($stone =='II1')
							{$stone='I I1';}	
						if($stone =='JI2')
							{$stone='J I2';}						
						$producturl= $url."/?stone1_grade=".$stone;
						return $producturl;
					}
						
						
					
						
							
							
                
                
				
				
				
				
				
				
				public function emailAction()
				{
					$params = $this->getRequest()->getParams();
					$status = $params['start'];				
					if((int)$status !=1)
					{
						echo " Error in Request";
						exit();
						
					}
					unset($params['start']);
					$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
					$sql        = "select sfo.customer_email, sfo.customer_firstname, sfo.customer_lastname, group_concat(sfoi.sku) as skus from
									 sales_flat_order as sfo left join sales_flat_order_item as sfoi on sfo.entity_id = sfoi.order_id where 
									 sfo.`status`='complete' and sfoi.updated_at < '2015-03-20 00:00:00' and sfoi.price > 0 and sfoi.sku != 'MANUAL' and sfoi.sku !='JA0050' and sfoi.sku !='OP0001SC' and sfoi.sku !='INS001'  
									 group by sfo.customer_email";					
					 $rows       = $connection->fetchAll($sql);					
					foreach($rows as $row)
					{						
						$row[skus];
						Mage::getModel('matching/matchingdata')
									->setCustomerEmail($row[customer_email])
									->setCustomerFirstname($row[customer_firstname])
									->setSkus($row[skus])									
									->save();					
					}
				}
				
				public function indexAction()
				{
					$params = $this->getRequest()->getParams();
					$status = $params['start'];				
					if((int)$status !=1)
					{
						echo " Error in Request";
						exit();
						
					}
					echo " controller is working ";
					
				}
				
				
				public function emaildataprep($checkemail)
				{
						
						
						$matching= array();
						if (($handle = fopen("app/locale/en_US/MatchingSheetConsolidated.csv", "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
								$num = count($data);
							   $suggestion= array();
								for ($c=1; $c < $num; $c++) {
									$suggestion[]= $data[$c] ;
								}
								$matching[$data[0]]=$suggestion;
							}
							fclose($handle);
						}
						
						
						$exactmatching= array();
						if (($handle = fopen("app/locale/en_US/MatchingERPSet.csv", "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
								$num = count($data);
							   $suggestion= array();
								for ($c=0; $c < $num; $c++) {
									$suggestion[]= $data[$c] ;
								}
								$exactmatching[$data[0]]=$suggestion;
								$exactmatching[$data[1]]=$suggestion;
								$exactmatching[$data[2]]=$suggestion;
							}
							fclose($handle);
						}
						
						
					$newsku= array();
					if (($handle = fopen("app/locale/en_US/OldToNewSku.csv", "r")) !== FALSE) {
						while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {								
							$newsku[$data[0]]=$data[1];
						}
						fclose($handle);
					}
					$collection =Mage::getModel('matching/matchingdata')->getCollection()->addFieldToFilter('customer_email',$checkemail);					
					//$collection =Mage::getModel('matching/matchingdata')->getCollection();
					
					foreach($collection as $orderdataofemail)
					{						
						unset($email);
						unset($ordersku);
						unset($fname);
						unset($finalorderdata);
						unset($count);
						unset($skuarray);
						unset($final);
						unset($key);
						
						
						$email = $orderdataofemail->getCustomerEmail();
						$orderskus = $orderdataofemail->getSkus();
						$fname = $orderdataofemail->getCustomerFirstname();
						
						if(empty($email) || empty($orderskus))
						{						
							continue;
						}					
						//$orderskus = "SR0404SB-WG-AAA-9x7";
						$finalorderdata = array();
						$skuarray = explode(',', $orderskus);						
						
						foreach($skuarray as $sku)
						{	
							
							unset($skuset);
							unset($mastersku);
							unset($stone);
							unset($metal);
							unset($matching1);
							unset($matching2);
							unset($output);
							unset($output1);
							unset($output2);
							unset($finalmatching);
							//var_dump($sku);								
							
							$skuset = explode('-', $sku);
							$mastersku = $skuset[0];
							if(!empty($newsku[$mastersku]))
							{
								$mastersku = $newsku[$mastersku];
							}							
								// var_dump($stone);
								 //var_dump($metal);
									$output = $this->detailsMaster($sku, $mastersku);
									$finalmatching[product] = $output;
									//var_dump($output);
									if(!empty($output[ChildSku]))
									{
										$matching1 = $matching[$mastersku][0];
										$matching2 = $matching[$mastersku][1];
										//var_dump($matching1);
										//var_dump($matching2);
										
										if(!empty($matching1))
										{
											if(!empty($newsku[$matching1]))
											{
												$matching1 = $newsku[$matching1];
											}
											$output1 = $this->details($sku, $matching1);
											//var_dump($output1);
											if(!empty($output1[ChildSku]))
											{												
												$finalmatching[matching1] = $output1;												
											}
											//var_dump($output1);										
										}
										if(!empty($matching2))
										{
											if(!empty($newsku[$matching2]))
											{
												$matching2 = $newsku[$matching2];
											}
											$output2 = $this->details($sku, $matching2);
											if(!empty($output2[ChildSku]))
											{	
												$finalmatching[matching2] = $output2;
												//$finalmatching[exactmatching] = $exactmatching[$mastersku];
											}
											//var_dump($output2);										
										}								
																											
									}
									$finalorderdata[$mastersku] = $finalmatching;
						}					
						foreach( $finalorderdata as $key =>$final)
						{
							if(!empty($final[matching1]))
							{							
								if(array_key_exists($final[matching1][MasterSku], $finalorderdata))
								{	
									
									if($final[matching1][stone] == $finalorderdata[$final[matching1][MasterSku]][product][stone] && in_array($final[matching1][MasterSku], $exactmatching[$key]))
									{
																		
										if(!empty($finalorderdata[$final[matching1][MasterSku]][product][ChildSku]))
										{
											$finalorderdata[$key][exactmatching] = $finalorderdata[$final[matching1][MasterSku]][product];	
											unset($finalorderdata[$final[matching1][MasterSku]]);
										}
										
									}									
									unset($finalorderdata[$key][matching1]);								
								}
							}
							if(!empty($final[matching2]))
							{							
								if(array_key_exists($final[matching2][MasterSku], $finalorderdata))
								{	
									
									if($final[matching2][stone] == $finalorderdata[$final[matching2][MasterSku]][product][stone] && in_array($final[matching2][MasterSku], $exactmatching[$key]))
									{
																		
										if(!empty($finalorderdata[$final[matching2][MasterSku]][product][ChildSku]))
										{
											$finalorderdata[$key][exactmatching] = $finalorderdata[$final[matching2][MasterSku]][product];												
											unset($finalorderdata[$final[matching2][MasterSku]]);
										}
										
									}									
									unset($finalorderdata[$key][matching2]);								
								}
							}
							
						}
						//var_dump($finalorderdata); exit();
						
						$count=0;						
						foreach($finalorderdata as $finalemailorder)
						{
							if(!empty($finalemailorder[product][ChildSku]) && (!empty($finalemailorder[matching1]) || !empty($finalemailorder[matching2])))
							{
								
								if(!empty($finalemailorder[exactmatching]) || !empty($finalemailorder[matching2]))
								{
									$count++;
									
									Mage::getModel('matching/right')
									->setEmailId($email)
									->setCustomerFirstname($fname)									
									->setEmailCount($count)
									->setProductMasterSku($finalemailorder[product][MasterSku])
									->setProductSku($finalemailorder[product][ChildSku])
									->setMatching1MasterSku($finalemailorder[matching1][MasterSku])
									->setMatching1Sku($finalemailorder[matching1][ChildSku])
									->setMatching1Stone($finalemailorder[matching1][stone])
									->setMatching1Metal($finalemailorder[matching1][metal])
									->setmatching2MasterSku($finalemailorder[matching2][MasterSku])
									->setmatching2Sku($finalemailorder[matching2][ChildSku])
									->setmatching2Stone($finalemailorder[matching2][stone])
									->setmatching2Metal($finalemailorder[matching2][metal])	
									->setExactmatchingMasterSku($finalemailorder[exactmatching][MasterSku])
									->setExactmatchingSku($finalemailorder[exactmatching][ChildSku])									
									->save();								
								}						
							}						
						}
						if($count > 0)
						{						
							Mage::getModel('matching/wrong')
									->setEmailId($email)
									->setCount($count)																		
									->save();	
						}
					}
				}
				
				public function dataemailAction()
				{	
				
					$params = $this->getRequest()->getParams();
					$status = $params['start'];				
					if((int)$status !=1)
					{
						echo " Error in Request";
						exit();
						
					}
					unset($params['start']);					
					 $to = date('Y-m-d H:i:s', time());					
					 $from = '2014-10-30 12:00:00';				
					 $orders = Mage::getModel('sales/order')->getCollection()    
						 ->addAttributeToSelect('customer_email')
						 ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to));							
					 $emailIdsofOrdersDone=array();
					 foreach ($orders as $order)
					 {   
						 $emailIdsofOrdersDone[]=$order->getCustomerEmail(); 
					 }
					 //var_dump($emailIdsofOrdersDone); exit();
					$unsubsinDBdata=  Mage::getModel('abandoncartmailchimp/unsubscriber')->getCollection()->getData();
					$unsubsinDB=array();				
					foreach($unsubsinDBdata as $unsubData)
					{ 
						$unsubsinDB[]=$unsubData['unsubscriber_email'];
					}											
					//$collection = Mage::getModel('matching/right')->getCollection()->addFieldToFilter('email_count','2');
					$collection = Mage::getModel('matching/right')->getCollection()->addFieldToFilter('email_count','1')->addFieldToFilter('id', array('gt' =>'4700'));
					foreach($collection as $emailcontent)
					{	
						
						unset($product);
						unset($emailsent);
						unset($fname);
						unset($productsku);
						unset($matching1MasterSku);
						unset($matchin1Stone);
						unset($matchin1Metal);
						unset($productsku);
						unset($matching2MasterSku);
						unset($matchin2Stone);
						unset($matchin2Metal);
						unset($exactmatchingSku);
						unset($mid1);
						unset($mid2);
						unset($pid);
						unset($eid);	
						unset($matching1bysku);
						unset($matching2bysku);
						unset($productbysku);
						unset($exactproductbysku);
						unset($extraParams2);
						unset($extraParams1);
						unset($exactproductbysku);
						unset($attributes2);
						unset($attributes1);
						unset($msid2);
						unset($msid1);
						$product = array();						
						$emailsent= $emailcontent->getEmailId();
						$fname = $emailcontent->getCustomerFirstname();
						$emailcount = $emailcontent->getEmailCount()+1;
						$productsku = $emailcontent->getProductSku();						
						$matching1MasterSku = $emailcontent->getMatching1MasterSku();
						$matching1Sku = $emailcontent->getmatching1Sku();
						$matchin1Stone = $emailcontent->getMatching1Stone();
						$matchin1Metal = $emailcontent->getMatching1Metal();					
						$matching2MasterSku = $emailcontent->getMatching2MasterSku();
						$matching2Sku = $emailcontent->getmatching2Sku();
						$matchin2Stone = $emailcontent->getMatching2Stone();
						$matchin2Metal = $emailcontent->getMatching2Metal();
						$exactmatchingSku = $emailcontent->getExactmatchingSku();						
						if(empty($emailsent))
						{
							continue;
						}
						if(in_array($emailsent,$unsubsinDB) ||  in_array($emailsent, $emailIdsofOrdersDone))
						{
							continue;
						}						
						if(!empty($productsku) && ((!empty($matching1Sku) && !empty($matching1MasterSku) )  || (!empty($matching2Sku) && !empty($matching2MasterSku)))) 
						{	
						
							$mid1 = Mage::getModel('catalog/product')->getIdBySku($matching1Sku);											
							if($mid1)
							{							
								$matching1bysku = Mage::getModel('catalog/product')->load($mid1);
								if($matching1bysku->getStatus()==1 && $matching1bysku->getTypeId() == 'simple' && $matching1bysku->getVisibility() != 1)
								{
									$msid1 = Mage::getModel('catalog/product')->getIdBySku($matching1MasterSku);
									$matching1bymastersku = Mage::getModel('catalog/product')->load($msid1);																
									$product[1][name]   = $matching1bysku->getShortDescription();
									$product[1][image]  = $matching1bysku->getImageUrl();
									if($matching1bymastersku->getTypeId() == 'simple')
									{
											$product[1][url]    = "http://www.angara.com/".$matching1bymastersku->getUrlPath().'?cid=em-match-matchpr-13112013-pr&code=EMATCH15&nochoose=true&utm_source=Email&utm_medium=Newsletter&utm_campaign=13112013&utm_term={EMAIL_ADDRESS}';
									}
									else
									{										
										$attributes1 = $matching1bymastersku->getTypeInstance(true)->getConfigurableAttributesAsArray($matching1bymastersku);
										$extraParams1 = array();
										foreach ($attributes1 as $attribute){
											foreach ($attribute['values'] as $value){
												$childValue = $matching1bysku->getData($attribute['attribute_code']);
												if ($value['value_index'] == $childValue){
													$extraParams1[] = $attribute['attribute_code'] .'='. $value['value_index'];
												}
											}
										}
										$extraParams1 = '&'.join('&',$extraParams1).'&cid=em-match-matchpr-13112013-pr&code=EMATCH15&nochoose=true&utm_source=Email&utm_medium=Newsletter&utm_campaign=13112013&utm_term={EMAIL_ADDRESS}';								
										$product[1][url]    = "http://www.angara.com/".$matching1bymastersku->getUrlPath().$extraParams1;	
									}
																	
									$product[1][price]  = 	Mage::helper('core')->currency($matching1bysku->getPrice(), true, false); 								
									
								}
							}
							$mid2 = Mage::getModel('catalog/product')->getIdBySku($matching2Sku);											
							if($mid2)
							{
								$matching2bysku = Mage::getModel('catalog/product')->load($mid2);
								if($matching2bysku->getStatus()==1 && $matching2bysku->getTypeId() == 'simple' && $matching2bysku->getVisibility() != 1)
								{
									$msid2 = Mage::getModel('catalog/product')->getIdBySku($matching2MasterSku);									
									$matching2bymastersku = Mage::getModel('catalog/product')->load($msid2);
									$product[2][name]   = $matching2bysku->getShortDescription();
									$product[2][image]  = $matching2bysku->getImageUrl();
									if($matching2bymastersku->getTypeId() == 'simple')
									{										
										$product[2][url]    = "http://www.angara.com/".$matching2bymastersku->getUrlPath().'?cid=em-match-matchpr-13112013-pr&code=EMATCH15&nochoose=true&utm_source=Email&utm_medium=Newsletter&utm_campaign=13112013&utm_term={EMAIL_ADDRESS}';
									}
									else
									{	
										$attributes2 = $matching2bymastersku->getTypeInstance(true)->getConfigurableAttributesAsArray($matching2bymastersku);
										$extraParams2 = array();
										foreach ($attributes2 as $attribute){
											foreach ($attribute['values'] as $value){
												$childValue = $matching2bysku->getData($attribute['attribute_code']);
												if ($value['value_index'] == $childValue){
													$extraParams2[] = $attribute['attribute_code'] .'='. $value['value_index'];
												}
											}
										}
										$extraParams2 = '&'.join('&',$extraParams2);								
										$product[2][url]    = "http://www.angara.com/".$matching2bymastersku->getUrlPath().$extraParams2.'&cid=em-match-matchpr-13112013-pr&code=EMATCH15&nochoose=true&utm_source=Email&utm_medium=Newsletter&utm_campaign=13112013&utm_term={EMAIL_ADDRESS}';	
									}
									$product[2][price]  = 	Mage::helper('core')->currency($matching2bysku->getPrice(), true, false);									
								}
							}					
							//&& $$matching1bysku->getVisibility() != 1 && $matching1bysku->getVisibility() != 2 
							//&& $$matching2bysku->getVisibility() != 1 && $matching2bysku->getVisibility() != 2 
								$pid = Mage::getModel('catalog/product')->getIdBySku($productsku);						
								if($pid)
								{
									$productbysku = Mage::getModel('catalog/product')->load($pid);
									if(($productbysku->getStatus()==1 && $productbysku->getTypeId() == 'simple'))
									{
										
										$product[0][name]   = $productbysku->getShortDescription();
										$product[0][image]  = $productbysku->getImageUrl();										
										$product[0][stone]  = str_replace('-', '', $this->getStone($productsku));
										$product[0][metal]  = $this->getMetalName($this->getMetal($productsku));
									}
								}
								$eid = Mage::getModel('catalog/product')->getIdBySku($exactmatchingSku);						
								if($eid)
								{
									$exactproductbysku = Mage::getModel('catalog/product')->load($eid);
									if(($exactproductbysku->getStatus()==1 && $exactproductbysku->getTypeId() == 'simple'))
									{
										
										$product[3][name]   = $exactproductbysku->getShortDescription();
										$product[3][image]  = $exactproductbysku->getImageUrl();
										$product[3][stone]  = str_replace('-','', $this->getStone($exactmatchingSku));
										$product[3][metal]  = $this->getMetalName($this->getMetal($exactmatchingSku));
									}
								}						
								if(!empty($product[1]) || !empty($product[2]))
								{
								
									$mandrill= new Mandrill('k93tI_1-pNTbT9bFTZjD-g');
									/*
									
									if(!empty($product[0]) && !empty($product[1]) !empty($product[2]))
									{
										$template_name = 'matching1'; 
									}
									elseif(!empty($product[0]) && !empty($product[3]) 
									{
										if(empty($product[1]))
										{
											$product[1] = $product[2]	
										}									
										$template_name = 'exactmatching';
									}
									elseif(!empty($product[0]))
									{	if(empty($product[1]))
										{
											$product[1] = $product[2];
										}
										$template_name = 'matching2';
									}
									*/
									//$email = "farukhsheikh3@gmail.com";
									if(!empty($product[1]) && !empty($product[2]))
									{
										$template_name = 'matching12';
									}
									elseif(empty($product[1]) ||  empty($product[2]))
									 
									{
										if(empty($product[1]))
										{
											$product[1] = $product[2];
										}
										$template_name = 'matching11';
									}
									//emailsent
									$message = array(									
										'to' => array(array('email' => $emailsent, 'name' => $fname)),
										'merge_vars' => array(array(
											'rcpt' => $emailsent,
											'vars' =>
											array(
												array(
													'name' => 'FIRSTNAME',
													'content' => $fname),
												array(
													'name' => 'USERSENDEMAIL',
													'content' => $emailsent),
												array(
													'name' => 'PRODUCT0NAME',
													'content' => $product[0][name]),																					
												array(
													'name' => 'PRODUCT0IMAGE',
													'content' => $product[0][image]),
												array(
													'name' => 'PRODUCT0METAL',
													'content' => $product[0][metal]),
												array(
													'name' => 'PRODUCT0STONE',
													'content' => $product[0][stone]),
												array(
													'name' => 'PRODUCT1NAME',
													'content' => $product[1][name]),
												array(
													'name' => 'PRODUCT1PRICE',
													'content' => $product[1][price]),
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
													'name' => 'PRODUCT2PRICE',
													'content' => $product[2][price]),													
												array(
													'name' => 'PRODUCT2IMAGE',
													'content' => $product[2][image]),
												array(
													'name' => 'PRODUCT2URL',
													'content' => $product[2][url]),														
											))));										
											$template_content = array();																
											$response = $mandrill->messages->sendTemplate( $template_name, $template_content, $message);
											echo " sent";						
									
								}	
						}
							
							
					}						
					
					
				}
				
				public function dataAction()
				{
					$params = $this->getRequest()->getParams();
					$status = $params['start'];				
					if((int)$status !=1)
					{
						echo " Error in Request";
						exit();
						
					}
					unset($params['start']);
						
						$matching= array();
						if (($handle = fopen("app/locale/en_US/MatchingSheetConsolidated.csv", "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
								$num = count($data);
							   $suggestion= array();
								for ($c=1; $c < $num; $c++) {
									$suggestion[]= $data[$c] ;
								}
								$matching[$data[0]]=$suggestion;
							}
							fclose($handle);
						}
						
						
						$exactmatching= array();
						if (($handle = fopen("app/locale/en_US/MatchingERPSet.csv", "r")) !== FALSE) {
							while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
								$num = count($data);
							   $suggestion= array();
								for ($c=0; $c < $num; $c++) {
									$suggestion[]= $data[$c] ;
								}
								$exactmatching[$data[0]]=$suggestion;
								$exactmatching[$data[1]]=$suggestion;
								$exactmatching[$data[2]]=$suggestion;
							}
							fclose($handle);
						}
						
						
					$newsku= array();
					if (($handle = fopen("app/locale/en_US/OldToNewSku.csv", "r")) !== FALSE) {
						while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {								
							$newsku[$data[0]]=$data[1];
						}
						fclose($handle);
					}
					//$collection =Mage::getModel('matching/matchingdata')->getCollection()->addFieldToFilter('customer_email',$checkemail);					
					$collection =Mage::getModel('matching/matchingdata')->getCollection()->addFieldToFilter('matching_id', array('from' =>'1', 'to' => '1000'));
					// count for stopping total entries 
					
					
					foreach($collection as $orderdataofemail)
					{	
						
					
						
						unset($email);
						unset($ordersku);
						unset($fname);
						unset($finalorderdata);
						unset($count);
						unset($skuarray);
						unset($final);
						unset($key);
						
						
						$email = $orderdataofemail->getCustomerEmail();
						$orderskus = $orderdataofemail->getSkus();
						$fname = $orderdataofemail->getCustomerFirstname();
						
						if(empty($email) || empty($orderskus))
						{						
							continue;
						}					
						//$orderskus = "SR0404SB-WG-AAA-9x7";
						$finalorderdata = array();
						$skuarray = explode(',', $orderskus);						
						
						foreach($skuarray as $sku)
						{	
							
							unset($skuset);
							unset($mastersku);
							unset($stone);
							unset($metal);
							unset($matching1);
							unset($matching2);
							unset($output);
							unset($output1);
							unset($output2);
							unset($finalmatching);
							//var_dump($sku);								
							
							$skuset = explode('-', $sku);
							$mastersku = $skuset[0];
							if(!empty($newsku[$mastersku]))
							{
								$mastersku = $newsku[$mastersku];
							}							
								// var_dump($stone);
								 //var_dump($metal);
									$output = $this->detailsMaster($sku, $mastersku);
									$finalmatching[product] = $output;
									//var_dump($output);
									if(!empty($output[ChildSku]))
									{
										$matching1 = $matching[$mastersku][0];
										$matching2 = $matching[$mastersku][1];
										//var_dump($matching1);
										//var_dump($matching2);
										
										if(!empty($matching1))
										{
											if(!empty($newsku[$matching1]))
											{
												$matching1 = $newsku[$matching1];
											}
											$output1 = $this->details($sku, $matching1);
											//var_dump($output1);
											if(!empty($output1[ChildSku]))
											{												
												$finalmatching[matching1] = $output1;												
											}
											//var_dump($output1);										
										}
										if(!empty($matching2))
										{
											if(!empty($newsku[$matching2]))
											{
												$matching2 = $newsku[$matching2];
											}
											$output2 = $this->details($sku, $matching2);
											if(!empty($output2[ChildSku]))
											{	
												$finalmatching[matching2] = $output2;
												//$finalmatching[exactmatching] = $exactmatching[$mastersku];
											}
											//var_dump($output2);										
										}								
																											
									}
									$finalorderdata[$mastersku] = $finalmatching;
						}					
						foreach( $finalorderdata as $key =>$final)
						{
							if(!empty($final[matching1]))
							{							
								if(array_key_exists($final[matching1][MasterSku], $finalorderdata))
								{	
									
									if($final[matching1][stone] == $finalorderdata[$final[matching1][MasterSku]][product][stone] && in_array($final[matching1][MasterSku], $exactmatching[$key]))
									{
																		
										if(!empty($finalorderdata[$final[matching1][MasterSku]][product][ChildSku]))
										{
											$finalorderdata[$key][exactmatching] = $finalorderdata[$final[matching1][MasterSku]][product];	
											unset($finalorderdata[$final[matching1][MasterSku]]);
										}
										
									}									
									unset($finalorderdata[$key][matching1]);								
								}
							}
							if(!empty($final[matching2]))
							{							
								if(array_key_exists($final[matching2][MasterSku], $finalorderdata))
								{	
									
									if($final[matching2][stone] == $finalorderdata[$final[matching2][MasterSku]][product][stone] && in_array($final[matching2][MasterSku], $exactmatching[$key]))
									{
																		
										if(!empty($finalorderdata[$final[matching2][MasterSku]][product][ChildSku]))
										{
											$finalorderdata[$key][exactmatching] = $finalorderdata[$final[matching2][MasterSku]][product];												
											unset($finalorderdata[$final[matching2][MasterSku]]);
										}
										
									}									
									unset($finalorderdata[$key][matching2]);								
								}
							}
							
						}
						//var_dump($finalorderdata); exit();
						
						$count=0;						
						foreach($finalorderdata as $finalemailorder)
						{
							if(!empty($finalemailorder[product][ChildSku]) && (!empty($finalemailorder[matching1]) || !empty($finalemailorder[matching2])))
							{
								
								if(!empty($finalemailorder[exactmatching]) || !empty($finalemailorder[matching2]))
								{
									$count++;
									
									Mage::getModel('matching/right')
									->setEmailId($email)
									->setCustomerFirstname($fname)									
									->setEmailCount($count)
									->setProductMasterSku($finalemailorder[product][MasterSku])
									->setProductSku($finalemailorder[product][ChildSku])
									->setMatching1MasterSku($finalemailorder[matching1][MasterSku])
									->setMatching1Sku($finalemailorder[matching1][ChildSku])
									->setMatching1Stone($finalemailorder[matching1][stone])
									->setMatching1Metal($finalemailorder[matching1][metal])
									->setmatching2MasterSku($finalemailorder[matching2][MasterSku])
									->setmatching2Sku($finalemailorder[matching2][ChildSku])
									->setmatching2Stone($finalemailorder[matching2][stone])
									->setmatching2Metal($finalemailorder[matching2][metal])	
									->setExactmatchingMasterSku($finalemailorder[exactmatching][MasterSku])
									->setExactmatchingSku($finalemailorder[exactmatching][ChildSku])									
									->save();								
								}						
							}						
						}
						if($count > 0)
						{						
							Mage::getModel('matching/wrong')
									->setEmailId($email)
									->setCount($count)																		
									->save();	
						}
					}
					
				}
				
				
				
				
				public function details($sku, $mastersku)
				{
							$stone = $this->getStone($sku);
							$metal = $this->getMetal($sku);								
							$id = Mage::getModel('catalog/product')->getIdBySku($mastersku);											
							if($id)
							{	$value[MasterSku] = $mastersku;							
								$product = Mage::getModel('catalog/product')->load($id);
								$defaultmetaltext = $product->getAttributeText('default_metal1_type');
								$defaultmetalcode = $this->getMetalCode($defaultmetaltext);
								$value[id] = $id;							
								if($product->getStatus() ==1)  
								{																
									if($product->getTypeId() == "configurable" && !empty($stone))
									{										
										if(!empty($metal))
										{
											$childsku = $this->getChildSku($mastersku,$stone, $metal);
											
										}
										if(empty($childsku) && !empty($defaultmetalcode))
										{ 											
											$childsku = $this->getChildSku($mastersku, $stone, $defaultmetalcode);
											$metal = $defaultmetalcode;
										}
										if(!empty($childsku))
										{
											$value[ChildSku] = $childsku;
											$value[stone] = $stone;
											$value[metal] = $metal;
											
										}									
									}
									elseif($product->getTypeId() == "simple" && empty($stone))
									{
										$value[ChildSku] = $mastersku;
									}
								}
							}
							return $value;
					
				}
				public function detailsMaster($sku, $mastersku)
				{
							$stone = $this->getStone($sku);
							$metal = $this->getMetal($sku);								
							$id = Mage::getModel('catalog/product')->getIdBySku($mastersku);											
							if($id)
							{	$value[MasterSku] = $mastersku;
								$value[Sku]      = $sku;
								$product = Mage::getModel('catalog/product')->load($id);								
								$value[id] = $id;
								if(!empty($metal))
								{
									$value[metal] = $metal;
								}
								if(!empty($stone))
								{
									$value[stone] = $stone;
								}		
									
								if($product->getTypeId() == "configurable" && !empty($stone))
								{	if(!empty($metal))
									{
										$childsku = $this->getChildSkuOfMaster($mastersku,$stone,$metal);									
										if(!empty($childsku))
										{
											$value[ChildSku] = $childsku;																				
										}
									}
								}
								elseif($product->getTypeId() == "simple")
								{
									$value[ChildSku] = $mastersku;
								}								
							}
							return $value;
					
				}
			}
				
	
               
             
             