<?php
/**
 * Adminhtml sales orders creation process controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Angara.com
 */
class Mage_Adminhtml_Sales_Order_OrdersheetController extends Mage_Adminhtml_Controller_Action
{
	public $qtcustom = '';
	public $mainstone = 2;
	public $mainstonesize = '';
	public $stonetext = '';
	public $sidestone = '';
	public $engraving = '';
	public $installmentflag = '0';
	public $VariationInfoToOrder = '';	
	
	// Code Added by Saurabh Starts		
	function getStoneUniqueName($product, $stoneIndex){
		if(!$product->getDiamondColor()){
			if($product->getData('stone'.$stoneIndex.'_weight') / $product->getData('stone'.$stoneIndex.'_count') > .24){
			   return $product->getAttributeText('stone'.$stoneIndex.'_shape').'-'.$product->getAttributeText('stone'.$stoneIndex.'_name').'-'.$product->getAttributeText('stone'.$stoneIndex.'_grade').$stoneIndex;
			}
			else{
			  return $product->getAttributeText('stone'.$stoneIndex.'_shape').'-'.$product->getAttributeText('stone'.$stoneIndex.'_name').'-'.$product->getAttributeText('stone'.$stoneIndex.'_grade');
			}
		}
		else{
			$settingStyle = $product->getAttributeText('setting_style');
		  	if($settingStyle=='Margarita'){
			 	if($product->getAttributeText('stud_weight') / $product->getData('stone'.$stoneIndex.'_count') > .24){
				   return $product->getAttributeText('stone'.$stoneIndex.'_shape').'-'.$product->getAttributeText('stone'.$stoneIndex.'_name').'-'.$product->getAttributeText('stone'.$stoneIndex.'_grade').$stoneIndex;
				}
				else{
				  return $product->getAttributeText('stone'.$stoneIndex.'_shape').'-'.$product->getAttributeText('stone'.$stoneIndex.'_name').'-'.$product->getAttributeText('stone'.$stoneIndex.'_grade');
				} 
			}
			else{
				if($product->getAttributeText('stud_weight') / $product->getData('stone1_count') > .24){
			   		return $product->getAttributeText('stone1_shape').'-'.$product->getAttributeText('stone1_name').'-'.$product->getAttributeText('stone1_grade').'1';
				}
				else{
				  return $product->getAttributeText('stone1_shape').'-'.$product->getAttributeText('stone1_name').'-'.$product->getAttributeText('stone1_grade');
				}	
			}
		}
	}
		 
 	function getStones($product){	
		if(!$product->getDiamondColor()){
		  $stones = array();
		  for($i = 1; $i <= $product->getStoneVariationCount(); $i++){
		   $stoneName = $this->getStoneUniqueName($product, $i);
		   if(!isset($stones[$stoneName])){
			$stones[$stoneName] = array(
			 'name'  => $product->getAttributeText('stone'.$i.'_name'),
			 'shape'  => $product->getAttributeText('stone'.$i.'_shape'),
			 'size'  => $product->getAttributeText('stone'.$i.'_size'),
			 'grade'  => $product->getAttributeText('stone'.$i.'_grade'),
			 'type'  => $product->getAttributeText('stone'.$i.'_type'),
			 'cut'  => $product->getAttributeText('stone'.$i.'_cut'),
			 'weight' => $product->getData('stone'.$i.'_weight'),
			 'count'  => $product->getData('stone'.$i.'_count'),
			 'setting'  => $product->getAttributeText('stone'.$i.'_setting'),
			 'color'   => $product->getAttributeText('stone'.$i.'_color'),
			 'clarity'   => $product->getAttributeText('stone'.$i.'_clarity')
			);
		   }
		   else{
			   	//if(strpos(' '.$stones[$stoneName]['size'], ' '.$product->getAttributeText('stone'.$i.'_size')) === false){
					$stones[$stoneName]['size'] .= ', '.$product->getAttributeText('stone'.$i.'_size');
			   	//}
				if(strpos(' '.$stones[$stoneName]['setting'], ' '.$product->getAttributeText('stone'.$i.'_setting')) === false){
					$stones[$stoneName]['setting'] .= ', '.$product->getAttributeText('stone'.$i.'_setting');
				}
				$stones[$stoneName]['weight'] += $product->getData('stone'.$i.'_weight');
				$stones[$stoneName]['count'] .= ', '.$product->getData('stone'.$i.'_count');
		   }
		  }
		  foreach($stones as &$stone){
			if($stone['weight'] > 0){
				if($stone['weight'] == 1){
					$stone['weight'] = number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carat';
				}
				else{
					$stone['weight'] = number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carats';
				}
			}
		 }
		}
		else{
		  $stones = array();
		  $settingStyle = $product->getAttributeText('setting_style');
		  if($settingStyle=='Margarita'){
			 for($i = 1; $i <= 2; $i++){
			   $stoneName = $this->getStoneUniqueName($product, $i);
			   if(!isset($stones[$stoneName])){
				$stones[$stoneName] = array(
				 'name'  => $product->getAttributeText('stone'.$i.'_name'),
				 'shape'  => $product->getAttributeText('stone'.$i.'_shape'),
				 //'size'  => $product->getAttributeText('stone'.$i.'_size'),
				 'grade'  => $product->getAttributeText('stone'.$i.'_grade'),
				 'type'  => $product->getAttributeText('stone'.$i.'_type'),
				 'cut'  => $product->getAttributeText('stone'.$i.'_cut'),
				 'weight' => $product->getAttributeText('stud_weight'),
				 'count'  => $product->getData('stone'.$i.'_count'),
				 'setting'  => $product->getAttributeText('stone'.$i.'_setting'),
				 'color'   => $product->getAttributeText('stone'.$i.'_color'),
				 'clarity'   => $product->getAttributeText('stone'.$i.'_clarity')
				);
			   }
			   else{
				    $stones[$stoneName]['setting'] .= ', '.$product->getAttributeText('stone'.$i.'_setting');
					$stones[$stoneName]['weight'] = $product->getAttributeText('stud_weight');
					$stones[$stoneName]['count'] += $product->getData('stone'.$i.'_count');
			   }
		  	}
			  foreach($stones as &$stone){
				 	 if($stone['weight'] <= 1){
						$stone['weight'] = $stone['weight'].' carat';
					}
					else{
						$stone['weight'] = $stone['weight'].' carats';
					}
			 }
					
				}
			else{	
				   $stoneName = $this->getStoneUniqueName($product, 1);
				   if(!isset($stones[$stoneName])){
					$stones[$stoneName] = array(
					 'name'  => $product->getAttributeText('stone1_name'),
					 'shape'  => $product->getAttributeText('stone1_shape'),
					 //'size'  => $product->getAttributeText('stone'.$i.'_size'),
					 'grade'  => $product->getAttributeText('stone1_grade'),
					 'type'  => $product->getAttributeText('stone1_type'),
					 'cut'  => $product->getAttributeText('stone1_cut'),
					 'weight' => $product->getAttributeText('stud_weight'),
					 'count'  => $product->getData('stone1_count'),
					 'setting'  => $product->getAttributeText('stone1_setting'),
					 'color'   => $product->getAttributeText('stone'.$i.'_color'),
					 'clarity'   => $product->getAttributeText('stone'.$i.'_clarity')
					);
				   }
			   	else{
				   	$stones[$stoneName]['setting'] = ', '.$product->getAttributeText('stone1_setting');
					$stones[$stoneName]['weight'] = $product->getAttributeText('stud_weight');
					$stones[$stoneName]['count'] = $product->getData('stone1_count');
			   }
			  foreach($stones as &$stone){
				if($stone['weight'] <= 1){
					$stone['weight'] = $stone['weight'].' carat';
				}
				else{
					$stone['weight'] = $stone['weight'].' carats';
				}
			  }
			}
		}
		// converting associative array to numeric array
		
		// add 's' or 'ies' in case stone count > 1
		foreach($stones as &$stone){
			if($stone['count'] > 1){
				// replace 'y' with 'ies'
				if(substr($stone['name'], -1) == 'y'){
					$stone['name'] = substr_replace($stone['name'], "ies", -1);
				}
				else if(substr($stone['name'], -1) == 'z'){
					$stone['name'] = substr_replace($stone['name'], "zes", -1);
				}
				else if(substr($stone['name'], -1) == 'x'){
					$stone['name'] = substr_replace($stone['name'], "xes", -1);
				}
				else{
					$stone['name'] = $stone['name']."s";
				}
			}
			else{
				$stone['name'] = $stone['name'];
			}
		}	
		
		  return array_values($stones);	
	}
	
	public function orderhtmlAction(){
		$orderid = trim($_POST['orderid']);
		$itemid = trim($_POST['itemid']);
		
		// Added regarding redirect of action to old or new sheets as per order no. <= 79321
		$ordcountitem = trim($_POST['ordcountitem']);		
		$_order = Mage::getModel('sales/order')->load($orderid);
		$_order_createdat = $_order->getCreatedAt();
		$incrementid = $_order->getIncrementId();
		$date = strtotime('2013-05-23 11:18:36');
		if(strtotime($_order_createdat) <= $date){
			if($ordcountitem > 1){
				$url = '/media/sales/ordersheets/'.$incrementid.'_'.$itemid.'.html';	
			}
			else{
				$url = '/media/sales/ordersheets/'.$incrementid.'.html';
			}			
			$this->_redirectUrl($url);
		}
		else{
			// Added regarding redirect of action to old or new sheets as per order no. <= 79321		
			$billingaddress = $_order->getBillingAddress();
			$shippingaddress = $_order->getShippingAddress();			
			$billingname = $billingaddress->getData('firstname') . " " . $billingaddress->getData('lastname');
			$shippingname = $shippingaddress->getData('firstname') . " " . $shippingaddress->getData('lastname');			
			$ship_address1 = trim($shippingaddress->getData('street'));
			$ship_city = trim($shippingaddress->getData('city'));
			$ship_state =trim($shippingaddress->getData('region'));
			$ship_zip = trim($shippingaddress->getData('postcode'));
			$ship_country = trim($shippingaddress->getData('country_id'));
			$ship_phone = trim($shippingaddress->getData('telephone'));			
			$ship_detail_address = '';
			$ship_detail_address .= $ship_address1.'<br>';
			$ship_detail_address .= $ship_city.', ';
			$ship_detail_address .= $ship_state.', ';
			$ship_detail_address .= $ship_zip.'<br>';
			$ship_detail_address .= $ship_country;
			//$ship_detail_address .= $ship_phone;					
			$shippingdesc = $_order->getData('shipping_description');
			$countryid = $shippingaddress->getData('country_id');
			$orderno = $incrementid;//$_order->getData('increment_id');
			$orderStoreDate = Mage::helper('sales')->formatDate($_order->getCreatedAtStoreDate(), 'medium', true);					
			if($countryid == 'US'){
				$shippingAt = "Domestic Shipping";
			}
			else{
				$shippingAt = "International Shipping";
			}			
			$itemqty = 1;
			$item = Mage::getModel('sales/order_item')->load($itemid);
						
			$mothersSku = strtolower(substr($item->getSku(),0,2));
			if($mothersSku != 'am'){
				$flag = '1';	
				$order_prod_sku = $item->getSku();
				$itemqty = $item->getQtyOrdered();
			}
			else{
				$flag = '2';
				$order_prod_sku = $item->getSku();
				$itemqty = $item->getQtyOrdered();
			}			
			$product = Mage::getModel('catalog/product');
			if($flag==1){							
				if(strstr($order_prod_sku,'-engraving')){
					$order_prod_skus=explode("-engraving",$order_prod_sku);
					if(strtolower(substr($order_prod_skus[0],0,2))=='am'){
						$order_prod_sku .= $order_prod_skus[0].''.$order_prod_skus[1];
					}
					else{
						$order_prod_sku = $order_prod_skus[0];
					}
				}
				else{
					$order_prod_sku = $order_prod_sku;
				}
				
				if(strstr($order_prod_sku,'-ja0050')){
					$order_prod_sku = str_replace('-ja0050','',$order_prod_sku);
				}
				
				//	Fix Added by Vaseem
				if(strstr($order_prod_sku,'--')){
					$order_prod_sku		=	str_replace('--','-',$order_prod_sku);
				}
				
				$checkProductId = $product->getIdBySku($order_prod_sku);
				
				if ($checkProductId){
					$_product = $product->load($checkProductId);
				}
				else{
					echo 'Sorry! This product does not exist or got deleted mistakenly.'; die;
				}
			}
			else{
				$productid = $_POST['productid'];
				$_product = $product->load($productid);
			}		
			$pro = $_product->getData();
			if($flag==2){
				$sku = $pro['sku'];
				$this->order_prod_sku = $sku;
				$name = str_ireplace("\n", "<br>", $pro['short_description']);
			}			
			$metal = '';
			$type1 = 0;
			$type2 = 0;
			$type3 = 0;
			$qg = '';
			$strmid = '';
			if($flag==1){
				$name = str_ireplace("\n", "<br>", $pro['short_description']);
				if(isset($pro['metal1_type'])){
					$metal = $_product->getAttributeText('metal1_type');
				}	
			}
			else{
				if(strpos($order_prod_sku,'-engraving')!==false){
					$metalTy = substr($order_prod_sku,-12);
					if($metalTy =='SL-engraving'){
						$metal = 'Silver';
					}
					else if($metalTy =='WG-engraving'){
						$metal = 'White Gold';
					}
					else if($metalTy =='YG-engraving'){
						$metal = 'Yellow Gold';
					}
					else if($metalTy =='PT-engraving'){
						$metal = 'Platinum';
					}
					else if($metalTy =='RG-engraving'){
						$metal = 'Rose Gold';
					}
					else{
						$metal = '';
					}
				}
				else{
					$metalTy = substr($order_prod_sku,-2);
					if($metalTy =='SL'){
						$metal = 'Silver';
					}
					else if($metalTy =='WG'){
						$metal = 'White Gold';
					}
					else if($metalTy =='YG'){
						$metal = 'Yellow Gold';
					}
					else if($metalTy =='PT'){
						$metal = 'Platinum';
					}
					else if($metalTy =='RG'){
						$metal = 'Rose Gold';
					}
					else{
						$metal = '';
					}
				}
				if($_product->getEmbStoneName3()){
					$type3 = 1;
				}
				if($_product->getEmbStoneName2()){
					$type2 = 1;
				}
				if($_product->getEmbStoneName()){
					$type1 = 1;
				}
				if($type2 != 0){
					if($_product->getEmbQualityGrade2()){
						$qg = $_product->getAttributeText('emb_quality_grade2');
					}
				}
				else{
					$this->mainstone = 1;
					if($_product->getEmbQualityGrade1()){
						$qg = $_product->getAttributeText('emb_quality_grade1');
					}
				}
			}					
			$items = $_order->getItemsCollection();
			$itm = '';
			$cartitemid='';
			if($flag==1){
				foreach($items as $item){
					$itemarr = $item->getData();
					if($itemarr['item_id'] == $itemid){
						$cartitemid = $itemarr['quote_item_id'];
						$itm = $item;
						$sku = $item->getSku();
						$options = $item->getProductOptions();
					}
				}
			}
			else{
				foreach($items as $item){
					$itemarr = $item->getData();
					if($itemarr['item_id'] == $itemid){
						$cartitemid = $itemarr['quote_item_id'];
						$itm = $item;
						$options = $item->getProductOptions();
						$xx = $item->getProductOptions();
						$mother_opt_arr = array();
						$py = 0;
						foreach($xx['options'] as $kk=>$vv){					
							$mother_opt_arr[$py]['label'] = $vv['label'];
							$mother_opt_arr[$py]['value'] = $vv['value'];	
							$py++;
						}
					}
				}
			}
			// s: diamond & sellor details in ordersheet by pankaj
			if(!empty($options["info_buyRequest"]["options"])){
				$optionDetail = $options["info_buyRequest"]["options"];
				if($optionDetail){
					$diamondId = $optionDetail["diamond"]["diamond_id"]; 
					$shape = $optionDetail["diamond"]["shape"];
					$size = $optionDetail["diamond"]["size"]; 
					$color = $optionDetail["diamond"]["color"];
					$clarity = $optionDetail["diamond"]["clarity"]; 
					$cut = $optionDetail["diamond"]["cut"];
					$country = $optionDetail["diamond"]["country"];
					$city = $optionDetail["diamond"]["city"]; 
					$stockNum = $optionDetail['diamond']["stock_num"];					
			
					$sellerId = $optionDetail["seller"]["account_id"];
					$sellerCompany = $optionDetail["seller"]["company"];
					$sellerName = $optionDetail["seller"]["name"];
					$sellerEmail = $optionDetail["seller"]["email"];
					$sellerPhone = $optionDetail["seller"]["phone"];
					$sellerCountry = $optionDetail["seller"]["country"];
					$sellerState = $optionDetail["seller"]["state"];
					$sellerCity = $optionDetail["seller"]["city"];
				}
			}
			// e: diamond & sellor details in ordersheet by pankaj
			
			$ringsize = '';
			if($flag==1){
				// Added by Pankaj
				if(isset($options['options'])){
					$options = $options['options'];
					$countOptions = count($options);
					for($i=0;$i<$countOptions;$i++){
						if($options[$i]['label']=='Engraving' || $options[$i]['label']=='Engrave this Product'){
							$engravingValue	= $options[$i]['value'];
						}
						
						if($options[$i]['label']=='Ring Size'){
							if(!$_product->getRingSize()){
								$ringsize = $options[$i]['value'];
							}
							else{
								$ringsize = $_product->getAttributeText('ring_size');
							}
						}
						
						// Added by Pankaj
						if($options[$i]['label']=='Free Jewelry Appraisal' || $options[$i]['label']=='Jewelry Appraisal'){
							$appraisaltext = $options[$i]['label'];
						}
					}
				}
				else{
					if(!$_product->getRingSize()){
						if($options['options'][0]['label']=='Ring Size'){
								$ringsize=$options['options'][0]['value'];
						}
					}
					else{
						$ringsize=$_product->getAttributeText('ring_size');
					}
				}
				
				if($ringsize == ''){
					if(!$_product->getRingSize()){
						if($options['options'][0]['label']=='Ring Size'){
								$ringsize=$options['options'][0]['value'];
						}
					}
					else{
						$ringsize=$_product->getAttributeText('ring_size');
					}
				}
			}
			
			if($flag==2){
				if(isset($options['options'])){
					$options = $options['options'];
					for($i=0;$i<count($options);$i++){
						if($options[$i]['label'] == 'Ring Size'){
							$ringsize = $options[$i]['value'];
						}
						else if($options[$i]['label'] == 'Stone 1 Quality'){
							$qg = $options[$i]['value'];
							$this->qtcustom = $qg;
						}
						else if($options[$i]['label'] == 'Metal 1 Type'){
							$str14k='';
							$stropmt = $options[$i]['value'];
							if($stropmt == 'Yellow Gold' || $stropmt == 'White Gold'){
								$str14k = '14 K';
							}
							$metal = $str14k ." " . $stropmt;
						}
						else if($options[$i]['label'] == 'Side Stone'){
							$this->sidestone = $options[$i]['value'];
						}
						else if($options[$i]['label'] == 'Engraving'){
							$this->engraving = $options[$i]['value'];
						}
						else if($options[$i]['label'] == 'Stone Size'){
							$stonesizestr = $options[$i]['value'];
							if(!strrpos($stonesizestr,"mm")){
								$stonesizestr = $stonesizestr . " mm";
							}
							$this->mainstonesize = $stonesizestr;
						}
						else if($options[$i]['label']=="VariationInfoToOrder"){
							$this->VariationInfoToOrder = $options[$i]['value'];
						}
						else if($options[$i]['label']=='Engraving' || $options[$i]['label']=='Engrave this Product'){
							$engravingValue	= 	$options[$i]['value'];				//	Added by Vaseem
						}
						else if($options[$i]['label']=='Free Jewelry Appraisal' || $options[$i]['label']=='Jewelry Appraisal'){
							$appraisaltext	= 	$options[$i]['label'];				//	Added by Pankaj
						}
					}
				}
			
				$strqg = '';
				if($qg != ''){
					$strqg = "<span style='padding-left:5px'>SQ: <a style='font-size:12px;color:green;font-weight:bold'>".$qg."</a></span>";
				}
			}			
			$strmt = '';
			if($metal != ''){
				$strmt = "<span style='padding-left:5px'><a style='font-size:12px;color:green;'>".$metal."</a></span>";
				$strmetal = "<tr>
								<td style='text-align:right;width:200px'>Metal:</td>
								<td style='padding-left:5px'>".$metal."</td>
							</tr>";
			}			
			if(trim($_product->getLength()) != ''){
				$strlength = "<tr>
								<td style='text-align:right;width:200px'>Length:</td>
								<td style='padding-left:5px'>".trim($_product->getAttributeText('length'))."</td>
							</tr>";
			}			
			if(trim($_product->getDiameter()) != ''){
				$strdiameter = "<tr>
								<td style='text-align:right;width:200px'>Diameter:</td>
								<td style='padding-left:5px'>".trim($_product->getAttributeText('diameter'))."</td>
							</tr>";
			}			
			if(trim($_product->getWidth()) != ''){
				$strwidth = "<tr>
								<td style='text-align:right;width:200px'>Width:</td>
								<td style='padding-left:5px'>".trim($_product->getAttributeText('width'))."</td>
							</tr>";
			}			
			if(trim($_product->getClaspType()) != ''){
				$strclasp = "<tr>
								<td style='text-align:right;width:200px'>Clasp Type:</td>
								<td style='padding-left:5px'>" . trim($_product->getAttributeText('clasp_type')) . "</td>
							</tr>";
			}			
			if(trim($_product->getButterfly1Type()) != ''){
				$strclasp = "<tr>
								<td style='text-align:right;width:200px'>Backing Type:</td>
								<td style='padding-left:5px'>".trim($_product->getAttributeText('butterfly1_type'))."</td>
							</tr>";
			}			
			if(trim($_product->getFit()) != ''){
				$strfit = "<tr>
								<td style='text-align:right;width:200px'>Fit:</td>
								<td style='padding-left:5px'>".trim($_product->getAttributeText('fit'))."</td>
							</tr>";
			}			
			if(trim($_product->getBandWidth()) != ''){
				$strbandwidth = "<tr>
								<td style='text-align:right;width:200px'>Width:</td>
								<td style='padding-left:5px'>".trim($_product->getAttributeText('band_width'))."</td>
							</tr>";
			}			
			if(trim($_product->getBandHeight()) != ''){
				$strbandheight = "<tr>
								<td style='text-align:right;width:200px'>Height:</td>
								<td style='padding-left:5px'>".trim($_product->getBandHeight())."</td>
							</tr>";
			}			
			if(trim($_product->getApproximateMetalWeight()) != ''){
				if($_product->getApproximateMetalWeight() == 'Select Ring Size'){
					$approxWt = $_product->getApproximateMetalWeight();
				}
				else{
					$approxWt = $_product->getApproximateMetalWeight().' grams';
				}
				$strapproxweight = "<tr>
					<td style='text-align:right;width:200px'>Approx Weight:</td>
					<td style='padding-left:5px'>".trim($approxWt)."</td>
				</tr>";	
			}			
			$strringsize = '';
			if($ringsize != ''){
				$strringsize = "<span style='padding-left:5px'>Size: <a style='font-size:12px;color:green;'>".$ringsize."</a></span>";
			}
			if($flag==2){
				$strloose = '';
				if($metal != ''){
					$strloose = $this->strloose($_product);
				}
			}
			$strgift = $this->getgiftmsg($_order);			
			$paypalexpress = $_order->getPayment()->getAdditionalInformation();
			$strpaypal = "";
			if(isset($paypalexpress['paypal_payer_id'])){
				$trnx = $_order->getPayment()->getData('last_trans_id');
				$strpaypal ="Paypal#: " . $trnx . "<br>";
			}		
			$img = $_product->getImageUrl();			
			if($flag==2){
				if($_product->getEmbQualityGrade1()){
					$gr_prefix = $_product->getAttributeText('emb_quality_grade1');
				}
				if($_product->getEmbQualityGrade2()){
					$gr_prefix = $_product->getAttributeText('emb_quality_grade2');
				}
				if($_product->getEmbQualityGrade3()){
					$gr_prefix = $_product->getAttributeText('emb_quality_grade3');
				}
				
				if(stripos($metal, 'Yellow') !== false) {
					$metal_prefix = 'Y';
				}else{
					$metal_prefix = 'W';
				}
				
				if(substr($sku, -1)=='G' || substr($sku, -1)=='B' || substr($sku, -1)=='H'){
					$sku_prefix = substr($sku,0,strlen($sku)-1);
				}else{
					$sku_prefix = $sku;
				}
			}			
			$mothersflag = 0;
			$customjflag = 0;
			$categoryIds = $_product->getCategoryIds();
			if($flag==2){
				$this->mothersflagval = 0;
				if(strtolower(substr($order_prod_sku, 0, 2)) == 'am'){
					$this->mothersflagval = 1;
				}			
			}
			foreach($categoryIds as $categoryId){
				$category = Mage::getModel('catalog/category')->load($categoryId);
				$url = $category->getUrlPath();
				if($url == "mothers-jewelry.html"){
					$mothersflag = 1;
				}
			 }
			 if($mothersflag == 1){
				 $customjflag = 1;
				 $img = "/media/catalog/product/images/mothers/cartproducts/" . $cartitemid . ".png";
			 }
			 else{
				 if($diamondId){
					$img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/ress/default/images/buildyourown/diamond/'.$shape.'_top_diamond.jpg';
				 }
				 else{
					 if($flag==2){
						 $img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product/'.$_product->getImage();
					 }
					 else{
						$img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$_product->getImage();
					 }	
				 }
			}
			if($flag==2){
				if(strtolower(substr($order_prod_sku, 0, 2)) == 'am'){
					$this->mothersflagval = 1;
				}
				
				if($this->mothersflagval == 1){
					$this->stonetext = '';
					foreach($this->mother_prod_opt_arr as $kkk => $vvv){
						$this->stonetext .= $vvv['label'].': '.$vvv['value'].'<br>';	
					}
				}	
			}
			if($flag==2){
				if($_product->getEmbStoneName3()){
					$strmid = $strmid . $this->strmid('3',$_product);
				}
				if($_product->getEmbStoneName2()){
					$strmid = $strmid . $this->strmid('2',$_product);
				}
				if($_product->getEmbStoneName()){
					$strmid = $strmid . $this->strmid('1',$_product);
				}	
			}			
			if($flag==2){
				if($_product->getEmbStoneName3()){
					$strmid = $strmid . $this->strmid('3',$_product);
				}
				if($_product->getEmbStoneName2()){
					$strmid = $strmid . $this->strmid('2',$_product);
				}
				if($_product->getEmbStoneName()){
					$strmid = $strmid . $this->strmid('1',$_product);
				}
			}			
			$stroktoship = $this->getOkToShipDiv($orderid);
			$strextracomment = $this->getExtraCommentDiv($orderid);
			$strprice = $this->getStrPrice($_order);	
			$strinstallments = "";			
			if($this->installmentflag == 1){
				$strinstallments = $this->getinstallmentstr($_order);
			}			
			//	Sku fix for mothers
			if( strtolower(substr($sku,0,2)) == 'am' ){
				$sku	=	$order_prod_sku;
			}
			
			$checkFreeProduct = Mage::helper('function')->checkFreeProductBySku($_product);
			if(!$checkFreeProduct ){
				$itemProd = Mage::getModel('sales/order_item')->load($itemid);
				$itemLeadTime = $itemProd->getVendorLeadTime();
				$leadTimeAddons = 0;
				$leadTimeAddons = Mage::helper('function')->checkProductForLeadTimeAdminSheet($itemProd);
				$orderCompleteDate  = Mage::helper('sales')->formatDate($_order->getCreatedAtStoreDate(), 'medium', true);
				$shippingMethodCode = $_order->getShippingMethod();
				$shippingMethodShortForm = Mage::helper('function')->shippingShortForm($shippingMethodCode);
				$leadTimeShippingMethod	= Mage::helper('function')->getShippingDays($shippingMethodShortForm);
				//$scheduleLeadTime = Mage::helper('function')->scheduleLeadTime($orderCompleteDate);
				$leadTimeDateRules = Mage::helper('function')->getLeadTimeDateRules($shippingMethodCode);
				$leadTime =	$itemLeadTime + $leadTimeAddons + $leadTimeShippingMethod /*+ $scheduleLeadTime*/ + $leadTimeDateRules;
				$estimatedArrivalDate = Mage::helper('function')->shipDateAdmin($orderCompleteDate, $leadTime);
			}

			if($flag==1){
				$strmids=$this->getStones($_product);
				
				foreach($strmids as $strProdData){
					if(($strProdData['color'] && $strProdData['color'] != null) || ($strProdData['clarity'] && $strProdData['clarity'] != null)){
						$showQualityGrade = false;
					}
					else{
						$showQualityGrade = true;
					}
					$strmid = $strmid . "<div><div class='heading '>" . $strProdData['type'] . " Detail:</div>"; 
					$strmid = $strmid . "<div style='padding:5px 0;'><table>";
					
					$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Number of " . $strProdData['shape']." ".$strProdData['name'].":</td><td style='padding-left:5px' valign='top'>" . $strProdData['count'] . "</td></tr>";
					$isSize= explode('-',$_product->getSku());
					if(count($isSize)<5 && trim($strProdData['size']) != ''){
						$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Approximate Dimensions:</td><td style='padding-left:5px'>" . $strProdData['size'] . "</td></tr>";
					}
					$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Approximate Carat Weight:</td><td style='padding-left:5px'>" . $strProdData['weight'] . "</td></tr>";
					if($showQualityGrade){
						$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Quality Grade:</td><td style='padding-left:5px'>" . $strProdData['grade'] . "</td></tr>";
					}
					else{
						if($strProdData['color'] && $strProdData['color'] != null){
							$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Color:</td><td style='padding-left:5px'>" . $strProdData['color'] . "</td></tr>";
						}
						if($strProdData['clarity'] && $strProdData['clarity'] != null){	
							$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Clarity:</td><td style='padding-left:5px'>" . $strProdData['clarity'] . "</td></tr>";
						}	
					}
					$strmid= $strmid . "<tr><td style='text-align:right;width:200px'>Setting Type:</td><td style='padding-left:5px'>" . $strProdData['setting'] . "</td></tr>";
					$strmid = $strmid . "</table></div></div>";			
				}
			}		
			if($flag==1){
				$strstonetext = (($_product->getAttributeText('stone1_size')) ? $_product->getAttributeText('stone1_size').', ': '').(($_product->getAttributeText('stone1_shape')) ? $_product->getAttributeText('stone1_shape').', ' : '').(($_product->getAttributeText('stone1_grade')) ? $_product->getAttributeText('stone1_grade') : '');//.(($_product->getAttributeText('stone1_name')) ? $_product->getAttributeText('stone1_name'): '');
			}
			else{
				$this->stonetext;
			}
			$str = "<html><head><meta charset='UTF-8'/></head><body>";
			$str = $str."<div style='margin:0px; width:760px; border:1px #ccc solid;'>";
			$str = $str."<div>
							<table style='width:760px; border-bottom:1px #ccc solid;'>
							<tr>
								<td style='width:190px; border-right:1px solid #ccc; padding-left:5px;' valign='top'>
									Billing: <span class='heading' style='border:none;'>".$billingname."</span><br>
									Shipping Address: <br><span>".$shippingname."</span><br>
									<span>".$ship_detail_address."</span><br>
									<span>T: ".$ship_phone."</span>
								</td>
								<td style='border-right:1px solid #ccc; padding-left:5px; box-sizing:border-box;' valign='top'>
									<a class='heading' style='border-top:none'>".$shippingAt."</a><br>
									<div style='padding-left: 5px;'>Ship Date:</div>
								</td>
								<td valign='top' style='padding-left:5px; box-sizing:border-box;'>
									".$strpaypal."
									Order No #: <a style='color:green; font-size:15px'>".$orderno."</a><br>
									Order Date: ".$orderStoreDate;
			/*$str = $str."<br>
									<a style='color:green;'>Est. Ship Date: ".$expdate."</a>";*/
			$str = $str."<br>
									<a style='color:green;'>Est. Delivery Date: ".$estimatedArrivalDate."</a>
								</td>
							</tr>
							</table>
						</div>
						<div>
							<table cellpadding='0' cellspacing='0' style='width:760px; border-bottom:1px #ccc solid;'>
								<tr>
									<td style='width:190px; border-right:1px solid #ccc;' valign='top'>
										<div style='width:193px; border-bottom: 1px #ccc solid;padding-left:5px;padding-top:5px;'>
											<span>SKU# ".$order_prod_sku."</span>";
			/*$str = $str."<br>
											<span style=''>
												<a style='font-size:12px; color:green;'>".$strstonetext."</a><br />
												<span style='color:green;'>".$name."</span>
											</span>";*/
											
			$str = $str."<div style='text-align:center;'><img src='".$img."' style='width:150px;' /></div>
											<table cellpadding='0' cellspacing='0' style='width:100%'><tr><td>".$strmt."</td><td style='text-align:right; padding-right:5px; width:67px; font-size:12px'>".$strringsize."</td></tr></table>
										</div>
										<div style='margin-top:5px;border-bottom: 1px solid #cccccc;padding-bottom:5px;padding-left:5px'>
											<span>Qty:</span>
											<span style='padding:5px 0 5px 50px; color:#FF0000; text-align:center; width:160px; font-size:12px;'>													
												<strong>".$this->numberToWords(round($itemqty,0))."</strong>
											</span>
										</div>
										<div style='margin-top:15px;'>
											<div class='heading' style='border-top:none; padding-left:5px;'>F.C. STATUS:</div>
											<div style='padding-left:5px;'>".$stroktoship."</div>
										</div>
										<div style='margin-top:15px'>
											<div class='heading' style='border-top: 1px solid #ccc; padding-left:5px;border-bottom: 1px solid #cccccc;'>
												COGS:
											</div>
											<div style='padding:5px 0;'>
												<table style='width:100%; text-align:left;'>
													<tr>
														<td style='width:62%; padding-left:5px;'>Vendor 1</td>
													</tr>";
				/*$str = $str."						<tr>
														<td style='width:62%; padding-left:5px;'>LOT#</td>
													</tr>";*/
				$str = $str."						<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
													<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
													<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
													<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
													<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
													<tr>
														<td style='width:100%;border-top: 1px solid #cccccc; padding-left:5px;'>Vendor 2</td>
													</tr>
													<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
													<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
													<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
													<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
													<tr>	
														<td>&nbsp;</td>
														<td></td>
													</tr>
												</table>
											</div>
											<div style='padding:5px 0;'>
												<table style='width:100%; text-align:left;border-collapse: collapse;'>
												
													<tr>
														<td style='width:62%;border-top: 1px solid #ccc;border-right: 1px solid #ccc;padding-left: 5px;'>Mounting Wt.</td>
														<td style='width:62%;border-top: 1px solid #ccc;padding-left: 5px;'>Gross Wt. </td>
													
													</tr>
													<tr>
														<td style='border-right: 1px solid #ccc;'>&nbsp;</td>
														<td></td>
													</tr>
													<tr>
														<td style='border-right: 1px solid #ccc;'>&nbsp;</td>
														<td></td>
													</tr>
													<tr>
														<td style='border-right: 1px solid #ccc;'>&nbsp;</td>
														<td></td>
													</tr>
													<tr>	
														<td style='text-align:right;border-right: 1px solid #ccc;'>Gm</td>
														<td style='text-align:right;'>Gm</td>
													</tr>
													<tr>	
														<td style='border-bottom: 1px solid #cccccc;border-top: 1px solid #cccccc;padding: 8px 0; vertical-align:top; padding-bottom:18px;'>CAD</td>
														<td style='border-bottom: 1px solid #cccccc;border-top: 1px solid #cccccc;'></td>
													</tr>
													<tr>	
														<td style='border-bottom: 1px solid #cccccc;padding: 8px 0; vertical-align:top; padding-bottom:18px;'>Mold Order</td>
														<td style='border-bottom: 1px solid #cccccc;'></td>
													</tr>
													<tr>	
														<td style='border-bottom: 1px solid #cccccc;padding: 8px 0; vertical-align:top; padding-bottom:18px;'>Casting Order</td>
														<td style='border-bottom: 1px solid #cccccc;'></td>
													</tr>";
													/*$str = $str."<tr>	
														<td style='border-bottom: 1px solid #cccccc;padding-left: 5px;'>Engraving Order</td>
														<td style='border-bottom: 1px solid #cccccc;'></td>
													</tr>
													<tr>	
														<td style='border-bottom: 1px solid #cccccc;padding-left: 5px;'>Appraisal Order</td>
														<td style='border-bottom: 1px solid #cccccc;'></td>
													</tr>";*/
												$str = $str."</table>
											</div>
										</div>
									</td>
									<td style='border-right: 1px #ccc solid;' valign='top'>
										<div>
											<div class='heading' style='border-bottom: 1px solid #ccc; border-top:none; padding-left:5px;'>Shipping Type: <span style='font-size:12px; padding:5px 0; text-align:center; color:green'>".$shippingdesc."</span></div>
										</div>
										<div>
											<div style='border-top:none; padding-left:5px;'>Revised: <span style='font-size:12px; padding:5px 0; text-align:center; color:green'></span></div>
										</div>";
										/*$str = $str."<div>
											<div class='heading' style='border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; text-align:center;'>PRODUCT DETAILS:</div>
											<div style='padding:5px 0;'>
												<table>
													<tr>
														<td style='text-align:right; width:200px; vertical-align:top;'>Angara Item #:</td>
														<td style='padding-left:5px; vertical-align:top;'>".$order_prod_sku."</td>
													</tr>
													" . $strmetal . "
													" . $strloose . "
													" . $strlength . "
													" . $strdiameter . "
													" . $strwidth . "
													" . $strclasp . "
													" . $strbandwidth . "
													" . $strbandheight . "															
													" . $strapproxweight . "
													" . $strfit . "
												</table>
											</div>
										</div>";*/
										$str = $str."<div>
											<div class='heading' style='border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; text-align:center;'>PRODUCTION LOCATION:</div>
											<div>
												<table>
													<tr>
														<td style='text-align:right; width:225px; vertical-align:top;'>".(($_product->getAttributeText('production_status')) ? ucfirst(strtolower($_product->getAttributeText('production_status'))):'')."</td>
													</tr>
												</table>
											</div>
										</div>";	
										
										if($diamondId){
											$str = $str . "<div>
												<div class='heading' style='border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;'>
													DIAMOND DETAIL:
												</div>
												<div style='padding:5px 0;'>
													<table>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Diamond Id #:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$diamondId."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Shape:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$shape."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Size:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$size."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Color:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$color."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Clarity:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$clarity."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Cut:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$cut."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Country:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$country."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>City:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$city."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Stock Number:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$stockNum."</td>
														</tr>
													</table>
												</div>
												<div class='heading' style='border-top:1px solid #ccc; border-bottom:1px solid #ccc;'>
													SELLER DETAIL:
												</div>
												<div style='padding:5px 0;'>
													<table>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Seller Id #:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$sellerId."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Seller Company:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$sellerCompany."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Seller Name:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$sellerName."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Seller Email:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$sellerEmail."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Seller Phone:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$sellerPhone."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Seller Country:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$sellerCountry."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Seller State:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$sellerState."</td>
														</tr>
														<tr>
															<td style='text-align:right; width:200px; vertical-align:top;'>Seller City:</td>
															<td style='padding-left:5px; vertical-align:top;'>".$sellerCity."</td>
														</tr>
													</table>
												</div>
											</div>";
										}
										$str = $str.$strmid."
										<div style='padding-top:20px;text-align:center;font-weight: bold;'>
											<div class='heading'>SPECIAL INSTRUCTIONS:</div><div style='padding:5px 0;'>".$strextracomment."</div>
											<br> <br> <br>
											
										</div>".$strgift."
										<div style='border-bottom: 1px solid #cccccc;text-align:center;padding-top: 5px;padding-bottom: 5px;'>REMARK FOR QC</div>
										<div style='height:80px;border-bottom:1px solid #ccc; width:100%;'></div>
										<div style='border-bottom: 1px solid #cccccc;text-align:center;padding-top: 5px;padding-bottom: 5px;'>LIST OF ITEMS TO BE PACKED</div>
										<div>
											<ul style='list-style:none; font:12px Arial; color:#00a222;padding:0px 20px;' >";
											$i=1;
											$k=1;
											if($flag==1){
												foreach ($_order->getAllVisibleItems() as $item){														
													$orderProdSku=$item->getSku();
													if(strstr($orderProdSku,'-engraving')){
														$order_prod_skus=explode("-engraving",$orderProdSku);
														$order_prod_sku = $order_prod_skus[0];
													}
													else{
														$order_prod_sku = $orderProdSku;
													}
													if(strstr($order_prod_sku,'-ja0050')){
														$order_prod_sku = str_replace('-ja0050','',$order_prod_sku);
													}
													$checkProductId = Mage::getModel('catalog/product')->getIdBySku($order_prod_sku);
													if ($checkProductId){
														$_listProd=Mage::getModel('catalog/product')->loadByAttribute('sku',$order_prod_sku);
														for($j=1;$j<=round($item->getQtyOrdered());$j++){
															if($_listProd->getShortDescription())
																$prodName[] = $_listProd->getShortDescription();
														}														
													}
												$i++;
												}
												
												$engravingtext	= $engravingValue;		//	Added by Vaseem		Modified by Pankaj	
												if($engravingtext!=''){
													if($flag==1){
														$engravingtext	=	'Engraving: '.$engravingtext;
													}else{
														$engravingtext	=	"Engraving: ".$engravingtext;
													}
													
													$engravingtext		=	str_replace('(SnellRoundhand','(Font Style: SnellRoundhand',$engravingtext);
													$engravingtext		=	str_replace('(Helvetica ','(Font Style: Helvetica ',$engravingtext);
													$prodName[] = $engravingtext;
												}
												
												if($appraisaltext!=''){
													$prodName[] = $appraisaltext;
												}
												
												foreach($prodName as $prod){
													if($prod){
														$str = $str ."<li style='padding-bottom:5px;'>".$k.'. '.$prod."</li>";
														$k++;
													}
												}
											}
				$str = $str . "				</ul>	
										</div>
									</td>
									<td style='width:190px;' valign='top'>
										<div>
											<table cellpadding='0' cellspacing='0' style='width:100%'>
												<tr>
													<td style='border-bottom:1px solid #ccc; width:50%;'><div class='heading' style='width:40px; border-top:none;'>SKU</div></td>
													<td style='width:50%; border-bottom:1px solid #ccc;'><div class='heading' style='width:55px; border-top:none; border-left:1px solid #ccc;'>PRICE</div></td>
												</tr>
												".$strprice."
											</table>
										</div>	
										".$strinstallments."											
										<div style='margin-top:15px'>
											<div class='heading' style='text-align:center;color:green;font-weight: bold;padding-top: 5px;padding-bottom:5px;border-bottom: 1px solid #cccccc;'>QC</div>											
										</div>";
				/*$str = $str . "<div style='margin-top:15px'>
											<div class='heading' style='border-bottom: 1px solid #cccccc;color:green;'>CASTING</div>
											<div style='padding-left:5px;border-bottom: 1px solid #cccccc;color:green;'>FINAL PRODUCT</div>
										</div>";*/
								$str = $str . "<div style='padding:5px 0;'><div style='height:100px;border-bottom: 1px solid #cccccc;'></div>";
								$str = $str . "<div>
									<div style='text-align:center;padding-top: 5px;padding-bottom:5px;border-bottom: 1px solid #cccccc;'>Setter</div>											
								</div><div style='padding:5px 0;'><div style='height:100px;border-bottom: 1px solid #cccccc;'></div>";
								$str = $str . "<div>
									<div style='text-align:center;padding-top: 5px;padding-bottom:5px;border-bottom: 1px solid #cccccc;'>Engraving Order</div>											
								</div><div style='padding:5px 0;'><div style='height:50px;border-bottom: 1px solid #cccccc;'></div>";
								$str = $str . "<div>
									<div style='text-align:center;padding-top: 5px;padding-bottom:5px;border-bottom: 1px solid #cccccc;'>Appraisal Order</div>											
								</div><div><div style='height:50px;'></div>";				
								//$str = $str . "<table style='width:100%; text-align:left;border-collapse: collapse;'>";
								/*$str = $str . "<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Aesthetics</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Prongs</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Stone Security</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Gallery Design</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Metal Finish</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Stamping</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Ring Size</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Pair Matching</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Post</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Screw Back</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>PushBack</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Bale</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Jump Ring</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>	
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Flexibility</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>	
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Lock</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>	
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Others</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>	
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Engraving</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>
												<tr>	
													<td style='border-bottom: 1px solid #cccccc;padding-left:5px;'>Appraisal</td>
													<td style='border-bottom: 1px solid #cccccc;'></td>
												</tr>";*/
												/*$str = $str . "				<tr>	
													<td style='padding-left:5px;'>Sign</td>
													<td></td>
												</tr>
												<tr>	
													<td>&nbsp;</td>
													<td></td>
												</tr>
												<tr>	
													<td>&nbsp;</td>
													<td></td>
												</tr>
											</table>";*/
								$str = $str . "</div>
										<div>
											<div style='border-bottom: 1px solid #cccccc;border-top: 1px solid #cccccc;padding:8px 5px;'>Sales Person:</div>
											<div style='border-bottom: 1px solid #cccccc;padding:8px 5px;'>Shipping Cost:</div>
											<div style='border-bottom: 1px solid #cccccc;padding:8px 5px;'>Tracking#</div>
										</div>											
									</td>
								</tr>
							</table>							
						</div>";
			$str = $str . "</div>
				<style>
					.heading{font-size:12px;  border-top:1px solid #cccccc; padding-left:5px;padding-top:5px;text-transform: uppercase;padding-bottom:5px;}
					.border{border:1px solid #ccc;padding-left:5px}
					body,div,table,td,tr{font-family:Arial;font-size:12px}
				</style>
				<script>
					window.onload = abc;
					function abc()
					{
						window.print();
					}
				</script>
			";
			$str = $str . "</body></html>";
			$myFile = "media/catalog/product/cache/ordersheet.html";
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, $str);
			fclose($fh);
			header("Refresh:0;URL='/" . $myFile . "'");	
		}
	}
	
	// Code Added by Pankaj
	public function orderhtmlOtherDetailsAction(){
		$orderid = trim($_POST['orderid']);
		$itemid = trim($_POST['itemid']);
		$ordcountitem = trim($_POST['ordcountitem']);		
		$_order = Mage::getModel('sales/order')->load($orderid);
		$_order_createdat = $_order->getCreatedAt();
		$incrementid = $_order->getIncrementId();
		$date = strtotime('2013-05-23 11:18:36');
		if(strtotime($_order_createdat) <= $date){
			if($ordcountitem > 1){
				$url = '/media/sales/ordersheets/'.$incrementid.'_'.$itemid.'.html';	
			}
			else{
				$url = '/media/sales/ordersheets/'.$incrementid.'.html';
			}
			
			$this->_redirectUrl($url);
		}
		else{
			$billingaddress = $_order->getBillingAddress();
			$shippingaddress = $_order->getShippingAddress();
			$billingname = $billingaddress->getData('firstname') . " " . $billingaddress->getData('lastname');
			$shippingname = $shippingaddress->getData('firstname') . " " . $shippingaddress->getData('lastname');
			
			$ship_address1 = trim($shippingaddress->getData('street'));
			$ship_city = trim($shippingaddress->getData('city'));
			$ship_state =trim($shippingaddress->getData('region'));
			$ship_zip = trim($shippingaddress->getData('postcode'));
			$ship_country = trim($shippingaddress->getData('country_id'));
			$ship_phone = trim($shippingaddress->getData('telephone'));
			
			$ship_detail_address = '';
			$ship_detail_address .= $ship_address1.'<br>';
			$ship_detail_address .= $ship_city.', ';
			$ship_detail_address .= $ship_state.', ';
			$ship_detail_address .= $ship_zip.'<br>';
			$ship_detail_address .= $ship_country.'<br>';
			$ship_detail_address .= $ship_phone;
					
			$shippingdesc = $_order->getData('shipping_description');
			$countryid = $shippingaddress->getData('country_id');
			$orderno = $_order->getData('increment_id');
			$orderStoreDate = Mage::helper('sales')->formatDate($_order->getCreatedAtStoreDate(), 'medium', true);
					
			if($countryid == 'US'){
				$shippingAt = "Domestic Shipping";
			}
			else{
				$shippingAt = "International Shipping";
			}
			
			$itemqty = 1;
			$item=Mage::getModel('sales/order_item')->load($itemid);
			$mothersSku=strtolower(substr($item->getSku(),0,2));
			if($mothersSku != 'am'){
				$flag='1';	
				$order_prod_sku = $item->getSku();
				$itemqty = $item->getQtyOrdered();
			}
			else{
				$flag='2';
				$order_prod_sku = $item->getSku();
				$itemqty = $item->getQtyOrdered();
			}
			
			if($flag==1){
				$product = Mage::getModel('catalog/product');				
				if(strstr($order_prod_sku,'-engraving')){
					$order_prod_skus=explode("-engraving",$order_prod_sku);
					if(strtolower(substr($order_prod_skus[0],0,2))=='am'){
						$order_prod_sku .= $order_prod_skus[0].''.$order_prod_skus[1];
					}
					else{
						$order_prod_sku = $order_prod_skus[0];
					}
				}
				else{
						$order_prod_sku = $order_prod_sku;
				}
				
				if(strstr($order_prod_sku,'-ja0050')){
					$order_prod_sku = str_replace('-ja0050','',$order_prod_sku);
				}
				
				//	Fix Added by Vaseem
				if(strstr($order_prod_sku,'--')){
					$order_prod_sku		=	str_replace('--','-',$order_prod_sku);
				}
							
				$checkProductId = $product->getIdBySku($order_prod_sku);
				
				if ($checkProductId){
					$_product = $product->load($checkProductId);
				}
				else{
					echo 'Sorry! This product does not exist or got deleted mistakenly.'; die;
				}
			}
			else{
				$productid = $_POST['productid'];
				$product = Mage::getModel('catalog/product');
				$_product = $product->load($productid);
			}
		
			$pro = $_product->getData();
			if($flag==2){
				$sku = $pro['sku'];
				$this->order_prod_sku = $sku;
				$name = str_ireplace("\n", "<br>", $pro['short_description']);
			}
			$metal = '';
			$type1 = 0;
			$type2 = 0;
			$type3 = 0;
			$qg = '';
			$strmid = '';
			$strmidSetter = '';
			if($flag==1){
				$name = str_ireplace("\n", "<br>", $pro['short_description']);
				if(isset($pro['metal1_type'])){
					$metal = $_product->getAttributeText('metal1_type');
				}	
			}
			else{
				if(strpos($order_prod_sku,'-engraving')!==false){
					$metalTy = substr($order_prod_sku,-12);
					if($metalTy =='SL-engraving'){
							$metal = 'Silver';
					}
					else if($metalTy =='WG-engraving'){
							$metal = 'White Gold';
					}
					else if($metalTy =='YG-engraving'){
							$metal = 'Yellow Gold';
					}
					else if($metalTy =='PT-engraving'){
							$metal = 'Platinum';
					}
					else{
							$metal = '';
					}
				}
				else{
					$metalTy = substr($order_prod_sku,-2);
					if($metalTy =='SL'){
							$metal = 'Silver';
					}
					else if($metalTy =='WG'){
							$metal = 'White Gold';
					}
					else if($metalTy =='YG'){
							$metal = 'Yellow Gold';
					}
					else if($metalTy =='PT'){
							$metal = 'Platinum';
					}
					else{
							$metal = '';
					}
				}
				if($_product->getEmbStoneName3()){
					$type3 = 1;
				}
				if($_product->getEmbStoneName2()){
					$type2 = 1;
				}
				if($_product->getEmbStoneName()){
					$type1 = 1;
				}
				if($type2 != 0){
					if($_product->getEmbQualityGrade2()){
						$qg = $_product->getAttributeText('emb_quality_grade2');
					}
				}
				else{
					$this->mainstone = 1;
					if($_product->getEmbQualityGrade1()){
						$qg = $_product->getAttributeText('emb_quality_grade1');
					}
				}
			}
					
			$items = $_order->getItemsCollection();
			$itm = '';
			$cartitemid='';
			if($flag==1){
				foreach($items as $item){
					$itemarr = $item->getData();
					if($itemarr['item_id'] == $itemid){
						$cartitemid = $itemarr['quote_item_id'];
						$itm = $item;
						$sku = $item->getSku();
						$options = $item->getProductOptions();
					}
				}
			}
			else{
				foreach($items as $item){
					$itemarr = $item->getData();
					if($itemarr['item_id'] == $itemid){
						$cartitemid = $itemarr['quote_item_id'];
						$itm = $item;
						$options = $item->getProductOptions();
						$xx = $item->getProductOptions();
						$mother_opt_arr = array();
						$py = 0;
						foreach($xx['options'] as $kk=>$vv){					
							$mother_opt_arr[$py]['label'] = $vv['label'];
							$mother_opt_arr[$py]['value'] = $vv['value'];	
							$py++;
						}
					}
				}
			}
			
			$optionsArray = $options;
			// s: diamond & sellor details in ordersheet by pankaj
			if(!empty($options["info_buyRequest"]["options"])){
				$optionDetail = $options["info_buyRequest"]["options"];
				if($optionDetail){
					$diamondId = $optionDetail["diamond"]["diamond_id"]; 
					$shape = $optionDetail["diamond"]["shape"];
					$size = $optionDetail["diamond"]["size"]; 
					$color = $optionDetail["diamond"]["color"];
					$clarity = $optionDetail["diamond"]["clarity"]; 
					$cut = $optionDetail["diamond"]["cut"];
					$country = $optionDetail["diamond"]["country"];
					$city = $optionDetail["diamond"]["city"]; 
					$stockNum = $optionDetail['diamond']["stock_num"];					
			
					$sellerId = $optionDetail["seller"]["account_id"];
					$sellerCompany = $optionDetail["seller"]["company"];
					$sellerName = $optionDetail["seller"]["name"];
					$sellerEmail = $optionDetail["seller"]["email"];
					$sellerPhone = $optionDetail["seller"]["phone"];
					$sellerCountry = $optionDetail["seller"]["country"];
					$sellerState = $optionDetail["seller"]["state"];
					$sellerCity = $optionDetail["seller"]["city"];
				}
			}
			// e: diamond & sellor details in ordersheet by pankaj
			
			$ringsize = '';
			if($flag==1){
				// Added by Pankaj
				if(isset($options['options'])){
					$options = $options['options'];
					$countOptions = count($options);
					for($i=0;$i<$countOptions;$i++){
						if($options[$i]['label']=='Engraving' || $options[$i]['label']=='Engrave this Product'){
							$engravingValue	= $options[$i]['value'];
						}
						
						if($options[$i]['label']=='Ring Size'){
							if(!$_product->getRingSize()){
								$ringsize = $options[$i]['value'];
							}
							else{
								$ringsize = $_product->getAttributeText('ring_size');
							}
						}
						
						// Added by Pankaj
						if($options[$i]['label']=='Free Jewelry Appraisal' || $options[$i]['label']=='Jewelry Appraisal'){
							$appraisaltext = $options[$i]['label'];
						}
					}
				}
				else{
					if(!$_product->getRingSize()){
						if($options['options'][0]['label']=='Ring Size'){
								$ringsize=$options['options'][0]['value'];
						}
					}
					else{
						$ringsize=$_product->getAttributeText('ring_size');
					}
				}
				
				if($ringsize == ''){
					if(!$_product->getRingSize()){
						if($options['options'][0]['label']=='Ring Size'){
							$ringsize=$options['options'][0]['value'];
						}
					}
					else{
						$ringsize=$_product->getAttributeText('ring_size');
					}
				}
			}
			
			if($flag==2){
				if(isset($options['options'])){
					$options = $options['options'];
					for($i=0;$i<count($options);$i++){
						if($options[$i]['label'] == 'Ring Size'){
							$ringsize = $options[$i]['value'];
						}
						else if($options[$i]['label'] == 'Stone 1 Quality'){
							$qg = $options[$i]['value'];
							$this->qtcustom = $qg;
						}
						else if($options[$i]['label'] == 'Metal 1 Type'){
							$str14k='';
							$stropmt = $options[$i]['value'];
							if($stropmt == 'Yellow Gold' || $stropmt == 'White Gold'){
								$str14k = '14 K';
							}
							$metal = $str14k ." " . $stropmt;
						}
						else if($options[$i]['label'] == 'Side Stone'){
							$this->sidestone = $options[$i]['value'];
						}
						else if($options[$i]['label'] == 'Engraving'){
							$this->engraving = $options[$i]['value'];
						}
						else if($options[$i]['label'] == 'Stone Size'){
							$stonesizestr = $options[$i]['value'];
							if(!strrpos($stonesizestr,"mm")){
								$stonesizestr = $stonesizestr . " mm";
							}
							$this->mainstonesize = $stonesizestr;
						}
						else if($options[$i]['label']=="VariationInfoToOrder"){
							$this->VariationInfoToOrder = $options[$i]['value'];
						}
						else if($options[$i]['label']=='Engraving' || $options[$i]['label']=='Engrave this Product'){
							$engravingValue	= 	$options[$i]['value'];				//	Added by Vaseem
						}
						else if($options[$i]['label']=='Free Jewelry Appraisal' || $options[$i]['label']=='Jewelry Appraisal'){
							$appraisaltext	= 	$options[$i]['label'];				//	Added by Pankaj
						}
					}
				}
			}
			
			$hasJewelAppraisal = 0;
			if($appraisaltext == ''){			
				if(isset($optionsArray["info_buyRequest"])){
					if($optionsArray["info_buyRequest"]['appraisal'] == 'on'){					
						$hasJewelAppraisal = 1;
					}			
				}
			}
			
			if($flag==2){
				$strqg = '';
				if($qg != ''){
					$strqg = "<span style='padding-left:5px'>SQ: <a style='font-size:14px;color:green;font-weight:bold'>" . $qg . "</a></span>";
				}
			}
			
			$strmt = '';
			if($metal != ''){
				$strmt = "<span><a style='font-size:14px;color:green;padding-right:30px;'>" . $metal . "</a></span>";
				$strmetal = "<tr>
								<td style='text-align:right;width:200px'>Metal:</td>
								<td style='padding-left:5px'>" . $metal . "</td>
							</tr>";
			}
			
			if(trim($_product->getLength()) != ''){
				$strlength = "<tr>
								<td style='text-align:right;width:200px'>Length:</td>
								<td style='padding-left:5px'>" . trim($_product->getAttributeText('length')) . "</td>
							</tr>";
			}
			
			if(trim($_product->getDiameter()) != ''){
				$strdiameter = "<tr>
								<td style='text-align:right;width:200px'>Diameter:</td>
								<td style='padding-left:5px'>" . trim($_product->getAttributeText('diameter')) . "</td>
							</tr>";
			}
			
			if(trim($_product->getWidth()) != ''){
				$strwidth = "<tr>
								<td style='text-align:right;width:200px'>Width:</td>
								<td style='padding-left:5px'>" . trim($_product->getAttributeText('width')) . "</td>
							</tr>";
			}
			
			if(trim($_product->getClaspType()) != ''){
				$strclasp = "<tr>
								<td style='text-align:right;width:200px'>Clasp Type:</td>
								<td style='padding-left:5px'>" . trim($_product->getAttributeText('clasp_type')) . "</td>
							</tr>";
			}
			
			if(trim($_product->getButterfly1Type()) != ''){
				$strclasp = "<tr>
								<td style='text-align:right;width:200px'>Backing Type:</td>
								<td style='padding-left:5px'>" . trim($_product->getAttributeText('butterfly1_type')) . "</td>
							</tr>";
			}
			
			if(trim($_product->getFit()) != ''){
				$strfit = "<tr>
								<td style='text-align:right;width:200px'>Fit:</td>
								<td style='padding-left:5px'>" . trim($_product->getAttributeText('fit')) . "</td>
							</tr>";
			}
			
			if(trim($_product->getBandWidth()) != ''){
				$strbandwidth = "<tr>
								<td style='text-align:right;width:200px'>Width:</td>
								<td style='padding-left:5px'>" . trim($_product->getAttributeText('band_width')) . "</td>
							</tr>";
			}
			
			if(trim($_product->getBandHeight()) != ''){
				$strbandheight = "<tr>
								<td style='text-align:right;width:200px'>Height:</td>
								<td style='padding-left:5px'>" . trim($_product->getBandHeight()) . "</td>
							</tr>";
			}
			
			if(trim($_product->getApproximateMetalWeight()) != ''){
				if($_product->getApproximateMetalWeight() == 'Select Ring Size'){
					$approxWt = $_product->getApproximateMetalWeight();
				}
				else{
					$approxWt = $_product->getApproximateMetalWeight().' grams';
				}
				$strapproxweight = "<tr>
								<td style='text-align:right;width:200px'>Approx Weight:</td>
								<td style='padding-left:5px'>" . trim($approxWt) . "</td>
							</tr>";	
			}			
			
			$strringsize = '';
			if($ringsize != ''){
				$strringsize = "<span style='padding-left:5px'>Size: <a style='font-size:14px;color:green;'>" . $ringsize . "</a></span>";
			}
			if($flag==2){
				$strloose = '';
				if($metal != ''){
					$strloose = $this->strloose($_product);
				}
			}
			$strgift = $this->getgiftmsg($_order);
			
			$paypalexpress = $_order->getPayment()->getAdditionalInformation();
			$strpaypal = "";
			if(isset($paypalexpress['paypal_payer_id'])){
				$trnx = $_order->getPayment()->getData('last_trans_id');
				$strpaypal ="Paypal#: " . $trnx . "<br>";
			}
		
			$img = $_product->getImageUrl();
			
			if($flag==2){
				if($_product->getEmbQualityGrade1()){
					$gr_prefix = $_product->getAttributeText('emb_quality_grade1');
				}
				if($_product->getEmbQualityGrade2()){
					$gr_prefix = $_product->getAttributeText('emb_quality_grade2');
				}
				if($_product->getEmbQualityGrade3()){
					$gr_prefix = $_product->getAttributeText('emb_quality_grade3');
				}
				
				if(stripos($metal, 'Yellow') !== false) {
					$metal_prefix = 'Y';
				}else{
					$metal_prefix = 'W';
				}
				
				if(substr($sku, -1)=='G' || substr($sku, -1)=='B' || substr($sku, -1)=='H'){
					$sku_prefix = substr($sku,0,strlen($sku)-1);
				}else{
					$sku_prefix = $sku;
				}
			}
			
			$mothersflag = 0;
			$customjflag = 0;
			$categoryIds = $_product->getCategoryIds();
			if($flag==2){
				$this->mothersflagval = 0;
				if(strtolower(substr($order_prod_sku, 0, 2)) == 'am'){
					$this->mothersflagval = 1;
				}			
			}
			
			foreach($categoryIds as $categoryId){
				$category = Mage::getModel('catalog/category')->load($categoryId);
				$url = $category->getUrlPath();
				if($url == "mothers-jewelry.html"){
					$mothersflag = 1;
				}
			 }
			 
			 if($mothersflag == 1){
				 $customjflag = 1;
				 $img = "/media/catalog/product/images/mothers/cartproducts/" . $cartitemid . ".png";
			 }
			 else{
				 if($diamondId){
					$img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/ress/default/images/buildyourown/diamond/'.$shape.'_top_diamond.jpg';
				 }
				 else{
					 if($flag==2){
						 $img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product/'.$_product->getImage();
					 }
					 else{
						$img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$_product->getImage();
					 }
				 	
				 }
			}
			
			if($flag==2){
				if(strtolower(substr($order_prod_sku, 0, 2)) == 'am'){
					$this->mothersflagval = 1;
				}
				
				if($this->mothersflagval == 1){
					$this->stonetext = '';
					foreach($this->mother_prod_opt_arr as $kkk => $vvv){
						$this->stonetext .= $vvv['label'].': '.$vvv['value'].'<br>';	
					}
				}	
			}
			if($flag==2){
				if($_product->getEmbStoneName3()){
					$strmid = $strmid . $this->strmid('3',$_product);
					$strmidSetter = $strmidSetter . $this->strmid('3',$_product);
				}
				if($_product->getEmbStoneName2()){
					$strmid = $strmid . $this->strmid('2',$_product);
					$strmidSetter = $strmidSetter . $this->strmid('2',$_product);
				}
				if($_product->getEmbStoneName()){
					$strmid = $strmid . $this->strmid('1',$_product);
					$strmidSetter = $strmidSetter . $this->strmid('1',$_product);
				}	
			}
			
			$stroktoship = $this->getOkToShipDiv($orderid);
			$strextracomment = $this->getExtraCommentDiv($orderid);
			$strprice = $this->getStrPrice($_order, $hasAppraisal = 1);
			$strinstallments = "";
			
			if($this->installmentflag == 1){
				$strinstallments = $this->getinstallmentstr($_order);
			}
			
			//$expdate = "";
			
			//	Sku fix for mothers
			if( strtolower(substr($sku,0,2)) == 'am' ){
				$sku	=	$order_prod_sku;
			}
			//	S:VA	Estimated Ship Date fixed
			/*if($leadtime != '')	{
				$tempdate 	= 	Mage::helper('sales')->formatDate($_order->getCreatedAtStoreDate(), 'medium', true);
				$expdate	=	Mage::helper('function')->estimatedShipDateAdmin($tempdate,$leadtime);
			}*/
			
			if($flag==1){
				$strmids=$this->getStones($_product);
				
				foreach($strmids as $strProdData){
					if(($strProdData['color'] && $strProdData['color'] != null) || ($strProdData['clarity'] && $strProdData['clarity'] != null)){
						$showQualityGrade = false;
					}	
					else{
						$showQualityGrade = true;
					}
					$strmidSetter = $strmidSetter."<div><div class='heading border'>".$strProdData['type']." Detail:</div>";	
					$strmidSetter = $strmidSetter . "<div style='padding:5px 0;'><table>";
								
					$strmid = $strmid . "<div><div class='heading border'>" . $strProdData['type'] . " Detail:</div>"; 
					$strmid = $strmid . "<div style='padding:5px 0;'><table>";
					$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Number of " . $strProdData['shape']." ".$strProdData['name'].":</td><td style='padding-left:5px'>" . $strProdData['count'] . "</td></tr>";
					//if($strProdData['type'] == 'Diamond'){
						$strmidSetter = $strmidSetter . "<tr><td style='text-align:right;width:200px'>Number of " . $strProdData['shape']." ".$strProdData['name'].":</td><td style='padding-left:5px'>" . $strProdData['count'] . "</td></tr>";
					//}
					$isSize= explode('-',$_product->getSku());
					if(count($isSize)<5 && trim($strProdData['size']) != ''){
						$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Approximate Dimensions:</td><td style='padding-left:5px'>" . $strProdData['size'] . "</td></tr>";
						$strmidSetter = $strmidSetter . "<tr><td style='text-align:right;width:200px'>Approximate Dimensions:</td><td style='padding-left:5px'>" . $strProdData['size'] . "</td></tr>";
					}
					$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Approximate Carat Weight:</td><td style='padding-left:5px'>" . $strProdData['weight'] . "</td></tr>";
					if($showQualityGrade){
						$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Quality Grade:</td><td style='padding-left:5px'>" . $strProdData['grade'] . "</td></tr>";
					}
					else{
						if($strProdData['color'] && $strProdData['color'] != null){
							$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Color:</td><td style='padding-left:5px'>" . $strProdData['color'] . "</td></tr>";
						}
						if($strProdData['clarity'] && $strProdData['clarity'] != null){	
							$strmid = $strmid . "<tr><td style='text-align:right;width:200px'>Clarity:</td><td style='padding-left:5px'>" . $strProdData['clarity'] . "</td></tr>";
						}	
					}					
					//$strmid= $strmid . "<tr><td style='text-align:right;width:200px'>Setting Type:</td><td style='padding-left:5px'>" . $strProdData['setting'] . "</td></tr>";
					$strmidSetter= $strmidSetter . "<tr><td style='text-align:right;width:200px'>Setting Type:</td><td style='padding-left:5px'>" . $strProdData['setting'] . "</td></tr>";
					$strmidSetter = $strmidSetter . "</table></div></div>";
					$strmid = $strmid . "</table></div></div>";
				}
			}
					
			if($flag==1){
				$strstonetext = (($_product->getAttributeText('stone1_size')) ? $_product->getAttributeText('stone1_size').', ': '').(($_product->getAttributeText('stone1_shape')) ? $_product->getAttributeText('stone1_shape').', ' : '').(($_product->getAttributeText('stone1_grade')) ? $_product->getAttributeText('stone1_grade').', ' : '').(($_product->getAttributeText('stone1_name')) ? $_product->getAttributeText('stone1_name'): '');
			}
			else{
				$this->stonetext;
			}
			
			$str = "<html><head><meta charset='UTF-8'/></head><body>";
			$str = "<div style='margin:0px;border:1px solid #ccc;width:780px;margin-bottom:30px;'>";
			$str = $str."<div>
							<table style='width:780px;border-bottom:1px #ccc solid'>
								<tr>
									<td valign='top' style='color:#22b14c;font-size:18px;width:30%;text-align:center;'>
										Setter Order Sheet
									</td>
									<td valign='top' style='width:70%;text-align:right;'>
										Order No #: <a style='color:green;font-size:15px;'>" . $orderno . "</a>
									</td>
									<td valign='top'>&nbsp;</td>
									<td valign='top'>&nbsp;</td>
									<td valign='top'>&nbsp;</td>
									<td valign='top'>&nbsp;</td>
								</tr>
							</table>
					</div>
					<div style='margin-top:5px'>
						<table cellpadding='0' cellspacing='0' style='width:780px;'>
							<tr>
								<td style='width:220px;border-right:1px #ccc solid;padding-right:5px;padding-left:5px;' valign='top'>
									<div style='width:210px;border:1px #ccc solid'>".(($strstonetext)?"<span style='padding-left:5px'><a style='font-size:13px;color:green;'>".$strstonetext."</a></span>":'');
									$str = $str."<div style='padding-left:5px;padding-top:5px'>".$order_prod_sku."</div><div style='text-align:center;'><img src='" . $img . "' style='width:150px;'></div>
										<table cellpadding='0' cellspacing='0' style='width:100%;padding: 5px 0px 5px 5px;'><tr><td>" . $strmt . "</td><td style='text-align:right;padding-right:5px;width:67px;font-size:14px'>" . $strringsize . "</td></tr></table>
									</div>											
								</td>
								<td style='border-right:1px #ccc solid;padding:0 5px' valign='top'>";									
									if($diamondId){
										$str = $str . "<div>
											<div class='heading border'>
												DIAMOND DETAIL:
											</div>
											<div style='padding:5px 0;'>
												<table>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Diamond Id #:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $diamondId . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Shape:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $shape . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Size:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $size . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Color:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $color . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Clarity:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $clarity . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Cut:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $cut . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Country:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $country . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>City:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $city . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Stock Number:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $stockNum . "</td>
													</tr>
												</table>
											</div>
											<div class='heading border'>
												SELLER DETAIL:
											</div>
											<div style='padding:5px 0;'>
												<table>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Seller Id #:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $sellerId . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Seller Company:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $sellerCompany . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Seller Name:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $sellerName . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Seller Email:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $sellerEmail . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Seller Phone:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $sellerPhone . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Seller Country:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $sellerCountry . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Seller State:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $sellerState . "</td>
													</tr>
													<tr>
														<td style='text-align:right;min-width:100px;vertical-align:top;'>Seller City:</td>
														<td style='padding-left:5px;vertical-align:top;'>" . $sellerCity . "</td>
													</tr>
												</table>
											</div>
										</div>";
									}
									$str = $str . $strmidSetter . "
								</td>
								<td style='width:190px;padding:0 5px' valign='top'>
									<div>
										<div class='heading border'>
											SETTER:
										</div>
										<div style='padding:5px 0;height:60px'>
											
										</div>
									</div>												
								</td>
							</tr>
						</table>
					</div>";
			$str = $str . "</div>";
			if($hasJewelAppraisal){	
				$str = $str . "<div style='margin:0px;border:1px solid #ccc;width:780px;'>";
				$str = $str . "<div>
									<table style='width:780px;border-bottom:1px #ccc solid'>
										<tr>
											<td valign='top' style='color:#22b14c;font-size:18px;width:30%;text-align:center;'>Appraisal Order Sheet</td>
											<td valign='top' style='width:70%;text-align:right;'>" . $strpaypal . "
												Order No #: <a style='color:green;font-size:15px'>" . $orderno . "</a>
											</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
									</table>
								</div>
								<div style='margin-top:5px'>
									<table cellpadding='0' cellspacing='0' style='width:780px;'>
										<tr>
											<td style='width:220px;border-right:1px #ccc solid;padding-right:5px;padding-left:5px;' valign='top'>
												<div style='width:210px;border:1px #ccc solid'>".(($strstonetext)?"<span style='padding-left:5px'><a style='font-size:13px;color:green;'>".$strstonetext."</a></span>":'');
										$str = $str."<div style='text-align:center;'><img src='" . $img . "' style='width:150px;'></div>
													<table cellpadding='0' cellspacing='0' style='width:100%; padding:5px 0px 5px 5px;'><tr><td>" . $strmt . "</td><td style='text-align:right;padding-right:5px;width:67px;font-size:14px'>" . $strringsize . "</td></tr></table>
												</div>
											</td>
											<td style='border-right:1px #ccc solid;padding:0 5px' valign='top'>";											
												if($diamondId){
													$str = $str . "<div>
														<div class='heading border'>
															DIAMOND DETAIL:
														</div>
														<div style='padding:5px 0;'>
															<table>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Diamond Id #:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $diamondId . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Shape:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $shape . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Size:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $size . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Color:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $color . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Clarity:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $clarity . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Cut:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $cut . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Country:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $country . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>City:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $city . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Stock Number:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $stockNum . "</td>
																</tr>
															</table>
														</div>
														<div class='heading border'>
															SELLER DETAIL:
														</div>
														<div style='padding:5px 0;'>
															<table>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Seller Id #:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $sellerId . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Seller Company:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $sellerCompany . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Seller Name:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $sellerName . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Seller Email:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $sellerEmail . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Seller Phone:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $sellerPhone . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Seller Country:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $sellerCountry . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Seller State:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $sellerState . "</td>
																</tr>
																<tr>
																	<td style='text-align:right;width:200px;vertical-align:top;'>Seller City:</td>
																	<td style='padding-left:5px;vertical-align:top;'>" . $sellerCity . "</td>
																</tr>
															</table>
														</div>
													</div>";
												}
												$str = $str . $strmid . "												
											</td>
											<td style='width:190px;padding:0 5px' valign='top'>
												<div>
													<table cellpadding='0' cellspacing='0' style='width:100%'>
														<tr>
															<td><div class='heading border' style='width:40px'>SKU</div></td>
															<td style='width:60px'><div class='heading border' style='width:55px'>PRICE</div></td>
														</tr>
														" . $strprice . "
													</table>
												</div>										
											</td>
										</tr>
									</table>
								</div></div>";
			}
			if($engravingValue){				
				$str = $str."<div style='margin:0px;border:1px solid #ccc;width:780px;margin-top:30px;'>";
				$str = $str."<div>
								<table style='width:780px;'>
									<tr>
										<td valign='top' style='color:#22b14c;font-size:18px;width:35%;text-align:center'>Engraving Order Sheet</td>
										<td valign='top' style='text-align:right;padding-right:40px;'>
											Order No #: <a style='color:green;font-size:15px'>" . $orderno . "</a><br>
											<span>". $strmt ."</span>
										</td>
									</tr>
								</table>
							</div>
							<div>
								<table cellpadding='0' cellspacing='0' style='width:780px;'>
									<tr>
										<td style='width:190px;' valign='top'>
											<div style='width:250px;margin:0 auto;'>";
											$str = $str."<div style='text-align:center;'><img src='" . $img . "' style='width:150px;text-align:center;'></div>
											<div style='text-align:center;font-size:14px;line-height:15px;width:250px;'>";
											$i=1;
											$k=1;
											if($flag==1){													
												$engravingtext	= $engravingValue;		//	Added by Vaseem		Modified by Pankaj	
												if($engravingtext!=''){
													if($flag==1){
														$engravingtext	=	'Engraving: '.$engravingtext;
													}else{
														$engravingtext	=	"Engraving: ".$engravingtext;
													}
													
													$engravingtext		=	str_replace('(SnellRoundhand','<br />(Font: SnellRoundhand',$engravingtext);
													$engravingtext		=	str_replace('(Helvetica ','<br />(Font: Helvetica ',$engravingtext);
													$prodName[] = $engravingtext;
												}
												
												foreach($prodName as $prod){
													if($prod){
														$str = $str .$prod;
														$k++;
													}
												}
											}"
											</div>
										</td>											
									</tr>
								</table>									
							</div>";
				$str = $str."</div>";
			}
			$str = $str . "</div>
				<style>
					.heading{font-size:16px;}
					.border{border:1px solid #ccc;padding-left:5px}
					body,div,table,td,tr{font-family:Arial;font-size:12px}
				</style>
				<script>
					window.onload = abc;
					function abc()
					{
						window.print();
					}
				</script>
			";
			$str = $str . "</body></html>";
			$myFile = "media/catalog/product/cache/ordersheet.html";
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, $str);
			fclose($fh);
			header("Refresh:0;URL='/" . $myFile . "'");	
		}
	}
	// Code Ended By Pankaj
	
	public function getStrPrice($_order, $hasAppraisal){
		$itemid = trim($_POST['itemid']);
		$ord = $_order->getData();
		$items = $_order->getAllVisibleItems();		
		$stritem = "<tr><td>&nbsp;</td></tr><tr>";
		$i=1;
		$itemCount = count($items);
		
		// S: Added by Pankaj for Free Jewelry Appraisal
		foreach($items as $key=>$item){
			if($hasAppraisal){
				if($item->getId() == $itemid){
					$itemval = $item->getData();
					$product = Mage::getModel('catalog/product');
					$_product = $product->load($itemval['product_id']);	
					for($j=1;$j<=round($item->getQtyOrdered());$j++){
						$prodSku[] = $item->getSku();
						$prodPrice[] = $item->getBasePrice();
						$prodOriginalPrice[] = $item->getOriginalPrice();
					}
				}	
			}
			elseif(!$hasAppraisal){				
				$itemval = $item->getData();
				$product = Mage::getModel('catalog/product');
				$_product = $product->load($itemval['product_id']);	
				for($j=1;$j<=round($item->getQtyOrdered());$j++){
					$prodSku[] = $item->getSku();
					$prodPrice[] = $item->getBasePrice();
					$prodOriginalPrice[] = $item->getOriginalPrice();
				}			
			}
			$i++;
		}
		
		$k=1;
		foreach($prodSku as $key=>$sku){
			$price = $prodPrice[$key];
			$originalprice = $prodOriginalPrice[$key];
			if($price != $originalprice){
				$this->installmentflag = 1;
				$cnt = round($originalprice / $price);
				$stritem = $stritem . "
								<td><table><tr>
									<td style='vertical-align:top'>" . $k . ".</td><td>" . $sku . " [I]</td>
								</tr></table></td>
								<td style='text-align:right;padding-right:5px;'>
									" . Mage::helper('core')->currency( $this->roundNo($prodPrice[$key],2) ) . "
								</td>
							</tr>";
			}
			else{
				$stritem = $stritem . "
								<td><table><tr>
									<td style='vertical-align:top'>" . $k . ".</td><td>" . $sku . "</td>
								</tr></table></td>
								<td style='text-align:right;padding-right:5px;'>
									" . Mage::helper('core')->currency( $this->roundNo($prodPrice[$key],2) )  . "
								</td>
							</tr>";
			}
		$k++;				
		}
		// E: Added by Pankaj for Free Jewelry Appraisal
		if($hasAppraisal!=1)	{	
			$str = $stritem . "<tr><td>&nbsp;</td></tr><tr>
				<td style='padding-left: 5px;'>
					Subtotal
				</td>
				<td style='text-align:right;padding-right:5px;'>
					" . Mage::helper('core')->currency( $this->roundNo($ord['base_subtotal'],2) ). "
				</td>
			</tr>
			<tr>
				<td style='padding-left: 5px;'>
					Shipping
				</td>
				<td style='text-align:right;padding-right:5px;'>
					" . Mage::helper('core')->currency( $this->roundNo($ord['base_shipping_amount'],2) ) . "
				</td>
			</tr>
			<tr>
				<td style='padding-left: 5px;'>
					Discount
				</td>
				<td style='text-align:right;padding-right:5px;'>
					" . Mage::helper('core')->currency( $this->roundNo($ord['base_discount_amount'],2) ) . "
				</td>
			</tr>";
			
			//S:VA	
			if($ord['base_tax_amount']>0){
				$str = $str . "<tr>
					<td style='padding-left: 5px;'>		Tax		</td>
					<td style='text-align:right;padding-right:5px;'>		" . Mage::helper('core')->currency( $this->roundNo($ord['base_tax_amount'],2) ) . "		</td>
				</tr>";
			}
			
			if($ord['base_fee_amount']<0){
				$str = $str . "<tr>
					<td style='padding-left: 5px;'>		Installment Pending Amount			</td>
					<td style='text-align:right;padding-right:5px;'>		" . Mage::helper('core')->currency( $this->roundNo($ord['base_fee_amount'],2) ) . "		</td>
				</tr>";
			}
			//	E:VA
			$str = $str . "<tr>
				<td style='padding-left: 5px;'>
					Grand Total
				</td>
				<td style='text-align:right;padding-right:5px;'>
					" . Mage::helper('core')->currency( $this->roundNo($ord['base_grand_total'],2) ). "
				</td>
			</tr>";
		}
		else{
			$str = $stritem;
		}
		return $str;
	}	
	// Code Added by Saurabh Ends	
	
	// New function added by Vaseem to generate backup files for sales order sheet before single template code goes live
	public function generatesalessheetAction(){
		$resource 	= 	Mage::getSingleton('core/resource');
		$read 		= 	$resource->getConnection('core_read');		// reading the database resource
		$orderItemQuery	=	"SELECT `sales_flat_order_item`.item_id,order_id,product_id FROM `sales_flat_order_item` order by order_id desc";
		//$orderItemQuery	=	"SELECT `sales_flat_order_item`.item_id,order_id,product_id FROM `sales_flat_order_item`  WHERE (`order_id`='4552') order by order_id desc";
		$orderItemResults		= $read->fetchAll($orderItemQuery);
		//echo '<pre>'; print_r($orderItemResults); die;
		if(count($orderItemResults) > 0){
			foreach($orderItemResults as $orderItem){
				//echo '<pre>'; print_r($orderItem); die;
				$this->orderhtmlOldAction($orderItem['product_id'],$orderItem['order_id'],$orderItem['item_id']);
			}
		}
	}	
	
	public function orderhtmlOldAction($productid,$orderid,$itemid){
		// Code modified by Vaseem
		if($productid==''){
			$productid = $_POST['productid'];
			$orderid = $_POST['orderid'];
			$itemid = $_POST['itemid'];
		}
		// Code modified by Vaseem
		$product = Mage::getModel('catalog/product');
		$_product = $product->load($productid);
		$_order = Mage::getModel('sales/order')->load($orderid);		
		$billingaddress = $_order->getBillingAddress();
		$shippingaddress = $_order->getShippingAddress();
		$billingname = $billingaddress->getData('firstname') . " " . $billingaddress->getData('lastname');
		$shippingname = $shippingaddress->getData('firstname') . " " . $shippingaddress->getData('lastname');
		//var_dump($shippingaddress->getData());exit;
		$ship_address1 = trim($shippingaddress->getData('street'));
		$ship_city = trim($shippingaddress->getData('city'));
		$ship_state =trim($shippingaddress->getData('region'));
		$ship_zip = trim($shippingaddress->getData('postcode'));
		$ship_country = trim($shippingaddress->getData('country_id'));
		$ship_phone = trim($shippingaddress->getData('telephone'));
		
		$ship_detail_address = '';
		$ship_detail_address .= $ship_address1.'<br>';
		$ship_detail_address .= $ship_city.', ';
		$ship_detail_address .= $ship_state.', ';
		$ship_detail_address .= $ship_zip.'<br>';
		$ship_detail_address .= $ship_country.'<br>';
		$ship_detail_address .= $ship_phone;
				
		$shippingdesc = $_order->getData('shipping_description');
		$countryid = $shippingaddress->getData('country_id');
		$orderno = $_order->getData('increment_id');
		$orderStoreDate = Mage::helper('sales')->formatDate($_order->getCreatedAtStoreDate(), 'medium', true);
				
		if($countryid == 'US'){
			$shippingAt = "Domestic Shipping";
		}
		else{
			$shippingAt = "International Shipping";
		}
		
		$pro = $_product->getData();
		$sku = $pro['sku'];
		$order_prod_sku = $sku;
		$this->order_prod_sku = $order_prod_sku;
		$itemqty = 1;
		foreach ($_order->getAllItems() as $item){
			if($productid == $item->getProductId()){	
				//var_dump($item);
				$order_prod_sku = $item->getSku();
				$itemqty = $item->getQtyOrdered();
				break;
			}
		}
		$name = str_ireplace("\n", "<br>", $pro['short_description']);
		$metal = '';
		$type1 = 0;
		$type2 = 0;
		$type3 = 0;
		$qg = '';
		$strmid = '';
		if(isset($pro['metal'])){
			$metal = $_product->getAttributeText('metal');
		}
		if($_product->getEmbStoneName3()){
			$type3 = 1;
		}
		if($_product->getEmbStoneName2()){
			$type2 = 1;
		}
		if($_product->getEmbStoneName()){
			$type1 = 1;
		}
		if($type2 != 0){
			if($_product->getEmbQualityGrade2()){
				$qg = $_product->getAttributeText('emb_quality_grade2');
			}
		}
		else{
			$this->mainstone = 1;
			if($_product->getEmbQualityGrade1()){
				$qg = $_product->getAttributeText('emb_quality_grade1');
			}
		}
		
		$items = $_order->getItemsCollection();
		$itm = '';
		$cartitemid='';
		foreach($items as $item){
			$itemarr = $item->getData();
			if($itemarr['item_id'] == $itemid){
				$cartitemid = $itemarr['quote_item_id'];
				$itm = $item;
				//$sku = $item->getSku();
				$options = $item->getProductOptions();
				$xx = $item->getProductOptions();
				//echo '<pre>';print_r($xx['options']);echo '</pre>';exit;
				$mother_opt_arr = array();
				$py = 0;
				foreach($xx['options'] as $kk=>$vv){					
					$mother_opt_arr[$py]['label'] = $vv['label'];
					$mother_opt_arr[$py]['value'] = $vv['value'];	
					$py++;
				}
			}
		}
		//echo '<pre>';print_r($mother_opt_arr);echo '</pre>';exit;		
		$ringsize = '';
		if(isset($options['options'])){
			$options = $options['options'];
			//echo "<pre>";print_r($options);echo "</pre>";exit;
			for($i=0;$i<count($options);$i++){
				if($options[$i]['label'] == 'Ring Size'){
					$ringsize = $options[$i]['value'];
				}
				else if($options[$i]['label'] == 'Stone Quality'){
					$qg = $options[$i]['value'];
					$this->qtcustom = $qg;
				}
				else if($options[$i]['label'] == 'Metal Type'){
					$str14k='';
					$stropmt = $options[$i]['value'];
					if($stropmt == 'Yellow Gold' || $stropmt == 'White Gold'){
						$str14k = '14 K';
					}
					$metal = $str14k ." " . $stropmt;
				}
				else if($options[$i]['label'] == 'Side Stone'){
					$this->sidestone = $options[$i]['value'];
				}
				// Fixed by Vaseem for mothers engraving sales order sheet issue reported by vinod
				else if($options[$i]['label'] == 'Engraving' || $options[$i]['label'] == 'Engrave this Product'){
					$this->engraving = $options[$i]['value'];
				}
				else if($options[$i]['label'] == 'Stone Size'){
					$stonesizestr = $options[$i]['value'];
					if(!strrpos($stonesizestr,"mm")){
						$stonesizestr = $stonesizestr . " mm";
					}
					$this->mainstonesize = $stonesizestr;
				}
				else if($options[$i]['label']=="VariationInfoToOrder"){
					$this->VariationInfoToOrder = $options[$i]['value'];
				}
			}
		}		
		
		$strqg = '';
		if($qg != ''){
			$strqg = "<span style='padding-left:5px'>SQ: <a style='font-size:14px;color:green;font-weight:bold'>" . $qg . "</a></span>";
		}
		$strmt = '';
		if($metal != ''){
			$strmt = "<span style='padding-left:5px'><a style='font-size:14px;color:green;'>" . $metal . "</a></span>";
			$strmetal = "<tr>
							<td style='text-align:right;width:200px'>Metal:</td>
							<td style='padding-left:5px'>" . $metal . "</td>
						</tr>";
		}
		$strringsize = '';
		if($ringsize != ''){
			$strringsize = "<span style='padding-left:5px'>Size: <a style='font-size:14px;color:green;'>" . $ringsize . "</a></span>";
		}
		$strloose = '';
		if($metal != ''){
			$strloose = $this->strloose($_product);
		}
		
		$strgift = $this->getgiftmsg($_order);
		
		$paypalexpress = $_order->getPayment()->getAdditionalInformation();
		$strpaypal = "";
		if(isset($paypalexpress['paypal_payer_id'])){
			$trnx = $_order->getPayment()->getData('last_trans_id');
			$strpaypal ="Paypal#: " . $trnx . "<br>";
		}
		
		//echo '<pre>'; print_r($_order->getData());echo '</pre>';		
		$img = $_product->getImageUrl();
		
		if($_product->getEmbQualityGrade1()){
			$gr_prefix = $_product->getAttributeText('emb_quality_grade1');
		}
		if($_product->getEmbQualityGrade2()){
			$gr_prefix = $_product->getAttributeText('emb_quality_grade2');
		}
		if($_product->getEmbQualityGrade3()){
			$gr_prefix = $_product->getAttributeText('emb_quality_grade3');
		}
		
		if(stripos($metal, 'Yellow') !== false){
			$metal_prefix = 'Y';
		}else{
			$metal_prefix = 'W';
		}
		
		if(substr($sku, -1)=='G' || substr($sku, -1)=='B' || substr($sku, -1)=='H'){
			$sku_prefix = substr($sku,0,strlen($sku)-1);
		}else{
			$sku_prefix = $sku;
		}
		
		//http://localhost/media/catalog/product/images/variations/SR0133R_WAAAA.jpg
		$mothersflag = 0;
		$customjflag = 0;
		$categoryIds = $_product->getCategoryIds();
		$this->mothersflagval = 0;
		//echo $order_prod_sku;exit;
		if(strtolower(substr($order_prod_sku, 0, 2)) == 'am'){
			$this->mothersflagval = 1;
		}
		//echo $this->mothersflagval;exit;		
		foreach($categoryIds as $categoryId){
			$category = Mage::getModel('catalog/category')->load($categoryId);
			$url = $category->getUrlPath();			
			if($url == "mothers-jewelry.html"){
				$mothersflag = 1;				
			}
		 }
		 if($mothersflag == 1){
			 $customjflag = 1;			 
			 $this->mother_prod_opt_arr = $mother_opt_arr;
			 $img = "/media/catalog/product/images/mothers/cartproducts/" . $cartitemid . ".png";
		 }
		 else{
			 if($_product->getCustomj()){
				$img = Mage::getBlockSingleton('hprcv/hprcv')->getrootpath() . "cartimages/" . $cartitemid . ".png";
			 }else{
			 	$new_advrp_img = '/media/catalog/product/images/variations/'.$sku_prefix.'_'.$metal_prefix.''.$gr_prefix.'.jpg';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].''.$new_advrp_img)) {
					$img = $new_advrp_img;
				}
			 }
		}
		if(strtolower(substr($order_prod_sku, 0, 2)) == 'am'){
			$this->mothersflagval = 1;
		}
		//echo 'final: '.$this->mothersflagval;exit;
		if($this->mothersflagval == 1){
			$this->stonetext = '';
			foreach($this->mother_prod_opt_arr as $kkk => $vvv)	{
				$this->stonetext .= $vvv['label'].': '.$vvv['value'].'<br>';	
			}
		}		
		if($_product->getEmbStoneName3())
		{
			$strmid = $strmid . $this->strmid('3',$_product);
		}
		if($_product->getEmbStoneName2()){
			$strmid = $strmid . $this->strmid('2',$_product);
		}
		if($_product->getEmbStoneName()){
			$strmid = $strmid . $this->strmid('1',$_product);
		}
		$stroktoship = $this->getOkToShipDiv($orderid);
		$strextracomment = $this->getExtraCommentDiv($orderid);
		$strprice = $this->getStrPriceOld($_order);
		$strinstallments = "";
		//if($this->installmentflag == 1){
			$strinstallments = $this->getinstallmentstr($_order);
		//}
		$expdate = "";
		$leadtime = $pro['vendor_lead_time'];
		if($leadtime != ''){
			$tempdate = Mage::helper('sales')->formatDate($_order->getCreatedAtStoreDate(), 'medium', true);
			//date_default_timezone_set('EST');
			$j=$_product->getVendorLeadTime();
			$dat = strtotime($tempdate);
			$dat2 = strtotime(date("Y-m-d", $dat) . " +" . $j . " day");
			
			if(date('l', $dat2) == 'Saturday'){
				$dat2 = strtotime(date("Y-m-d", $dat2) . " +" . 2 . " day");
			}
			else if(date('l', $dat2) == 'Sunday'){
				$dat2 = strtotime(date("Y-m-d", $dat2) . " +" . 2 . " day");
			}
			else if(date('l', $dat2) == 'Monday'){
				$dat2 = strtotime(date("Y-m-d", $dat2) . " +" . 1 . " day");
			}
			$expdate = date('M j, Y', $dat2);			
		}
				
		$strstonetext = $this->stonetext;
		$engravingtext = $this->getengravingtext();
		//$str = $str . "";
		$str = "<html><head></head><body>";
		
		$str = $str . "<div style='margin:0px;border:1px solid #ccc;width:760px;padding:5px;'>";
		$str = $str . "		<div>
								<table style='width:760px;border-bottom:1px #ccc solid'>
									<tr>
										<td style='border-right:1px solid #ccc;' valign='top'>
											<strong>Billing Name:</strong> <span class='heading'>" . $billingname . "</span><br><br>
											<strong>Shipping Address:</strong> <br><span>" . $shippingname . "</span><br>
											<span>".$ship_detail_address."</span>
										</td>
										<td style='border-right:1px solid #ccc' valign='top'>
											<a class='heading'>" . $shippingAt . "</a><br>
											<div>
												Ship Date:
											</div>
										</td>
										<td valign='top'>
											" . $strpaypal . "
											Order No #: <a style='color:green;font-size:15px'>" . $orderno . "</a><br>
											Order Date: " . $orderStoreDate ;
		/*$str = $str . "<br>
											<a style='color:green;'>Estimated ship date: " . $expdate . "</a>";*/
		$str = $str . "									
										</td>
									</tr>
								</table>
							</div>
							<div style='margin-top:5px'>
								<table cellpadding='0' cellspacing='0' style='width:760px;'>
									<tr>
										<td style='width:190px;border-right:1px #ccc solid;padding-right:5px' valign='top'>
											<div style='width:183px;border:1px #ccc solid'>
												<span style='padding-left:5px'><a style='font-size:14px;color:green;'>" . $strstonetext . "</a></span>
												<img src='" . $img . "' style='width:183px;'>
												<table cellpadding='0' cellspacing='0' style='width:100%'><tr><td>" . $strmt . "</td><td style='text-align:right;padding-right:5px;width:67px;font-size:14px'>" . $strringsize . "</td></tr></table>
											</div>
											<div style='font-size:15px;margin-top:5px'>
												" . $name . "
											</div>
											<div style='margin-top:15px'>
												<div class='heading border'>
													F.C. STATUS:
												</div>
												<div style='padding:5px 0;'>
													" . $stroktoship . "
												</div>
											</div>
											<div style='margin-top:15px'>
												<div class='heading border'>
													COGS:
												</div>
												<div style='padding:5px 0;'>
													<div class='heading'>
														Product Details
													</div>
													<table  style='width:100%;text-align:right'>
														<tr>
															<td style='width:60%;font-weight:bold'>
																A: Vendor Name 1:
															</td>
															<td>
															</td>
														</tr>
														<tr>	
															<td>
																&nbsp;
															</td>
															<td>
															</td>
														</tr>
														<tr>	
															<td>
																&nbsp;
															</td>
															<td>
															</td>
														</tr>
														<tr>	
															<td>
																&nbsp;
															</td>
															<td>
															</td>
														</tr>
														<tr>	
															<td>
																&nbsp;
															</td>
															<td>
																
															</td>
														</tr>
														<tr>
															<td style='font-weight:bold'>
																B: Vendor Name 2:
															</td>
															<td>
															</td>
														</tr>
														<tr>	
															<td>
																&nbsp;
															</td>
															<td>
															</td>
														</tr>
														<tr>	
															<td>
																&nbsp;
															</td>
															<td>
															</td>
														</tr>";
			/*$str = $str . "											<tr>	
															<td>
																&nbsp;
															</td>
															<td>
															</td>
														</tr>
														<tr>	
															<td>
																&nbsp;
															</td>
															<td>
																
															</td>
														</tr>";*/
			$str = $str . "											<tr>	
															<td>
																&nbsp;
															</td>
															<td>
																
															</td>
														</tr>
														<tr>	
															<td>
																&nbsp;
															</td>
															<td>
																
															</td>
														</tr>
													</table>
												</div>
											</div>
										</td>
										<td style='border-right:1px #ccc solid;padding:0 5px' valign='top'>
											<div>
												<div class='heading border'>
													SHIPPING TYPE:
												</div>
												<div style='font-size:15px;padding:5px 0;text-align:center;color:green'>
													" . $shippingdesc . "
												</div>
											</div>
											<div>
												<div class='heading border'>
													PRODUCT DETAIL:
												</div>
												<div style='padding:5px 0;'>
													<table>
														<tr>
															<td style='text-align:right;width:200px;vertical-align:top;'>Angara Item #:</td>
															<td style='padding-left:5px;vertical-align:top;'>" . $order_prod_sku . "</td>
														</tr>
														" . $strmetal . "
														" . $strloose . "
													</table>
												</div>
											</div>";
											if($diamondId){
												$str = $str . "<div>
													<div class='heading border'>
														DIAMOND DETAIL:
													</div>
													<div style='padding:5px 0;'>
														<table>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Diamond Id #:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $diamondId . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Shape:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $shape . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Size:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $size . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Color:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $color . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Clarity:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $clarity . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Cut:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $cut . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Country:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $country . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>City:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $city . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Stock Number:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $stockNum . "</td>
															</tr>
														</table>
													</div>
													<div class='heading border'>
														SELLER DETAIL:
													</div>
													<div style='padding:5px 0;'>
														<table>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Seller Id #:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $sellerId . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Seller Company:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $sellerCompany . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Seller Name:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $sellerName . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Seller Email:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $sellerEmail . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Seller Phone:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $sellerPhone . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Seller Country:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $sellerCountry . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Seller State:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $sellerState . "</td>
															</tr>
															<tr>
																<td style='text-align:right;width:200px;vertical-align:top;'>Seller City:</td>
																<td style='padding-left:5px;vertical-align:top;'>" . $sellerCity . "</td>
															</tr>
														</table>
													</div>		
												</div>";
											}
											$str = $str . $strmid . "
											" . $strgift . "
											" . $strextracomment . "
										</td>
										<td style='width:190px;padding:0 5px' valign='top'>
											<div>
												<table cellpadding='0' cellspacing='0' style='width:100%'>
													<tr>
														<td><div class='heading border' style='width:40px'>SKU</div></td>
														<td style='width:60px'><div class='heading border' style='width:55px'>PRICE</div></td>
													</tr>
													" . $strprice . "
												</table>
											</div>	
											" . $strinstallments . "
											
											<div style='margin-top:15px'>
												<div class='heading border'>
													<strong>QUANTITY:</strong>
												</div>
												<div style='padding:5px 0;color:#FF0000;text-align:center;width:160px;'>													
													<strong>" . round($itemqty,0) . "</strong>
												</div>
											</div>
											
											<div style='margin-top:15px'>
												<div class='heading border'>

													E-MAIL & PHONE:
												</div>
												<div style='padding:5px 0;'>
													Email: " . $billingaddress->getEmail() . "<br>
													Phone: " . $billingaddress->getTelephone() . "
												</div>
											</div>
											<div style='margin-top:15px'>
												<div class='heading border'>
													SETTER:
												</div>
												<div style='padding:5px 0;height:60px'>
													
												</div>
											</div>
											<div style='margin-top:15px'>
												<div class='heading border'>
													SALES PERSON:
												</div>
												<div style='padding:5px 0;height:30px'>
													
												</div>
											</div>
											<div style='margin-top:15px'>
												<div class='heading border'>
													SHIPPING:
												</div>
												<div style='padding:5px 0;height:60px'>
													Shipping Cost:<br>
													Tracking Number:
												</div>
											</div>
											" . $engravingtext . "
										</td>
									</tr>
								</table>
							</div>";
		$str = $str . "</div>
			<style>
				.heading{font-size:16px;}
				.border{border:1px solid #ccc;padding-left:5px}
				body,div,table,td,tr{font-family:Arial;font-size:12px}
			</style>
			<script>
				window.onload = abc;
				function abc()
				{
					window.print();
				}
			</script>
		";
		$str = $str . "</body></html>";
		$linkSalesSheet		=	Mage::helper('function')->singleTemplate();
		if($linkSalesSheet==1 || count($_REQUEST)>0){
			$myFile = "media/catalog/product/cache/ordersheet.html";
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, $str);
			fclose($fh);
			header("Refresh:0;URL='/" . $myFile . "'");		
		}else{
			// Writing data to new backup file by Vaseem
			$resource 	= 	Mage::getSingleton('core/resource');
			$read 		= 	$resource->getConnection('core_read');		// reading the database resource
			$orderItemQuery	=	"SELECT `sales_flat_order_item`.* FROM `sales_flat_order_item` WHERE (`order_id`='".$orderid."')";
			//$orderItemQuery	=	"SELECT `sales_flat_order_item`.* FROM `sales_flat_order_item` WHERE (`order_id`='4552')";
			$orderItemResults		= $read->fetchAll($orderItemQuery);
			//echo '<pre>'; print_r($orderItemResults); die;
			if(count($orderItemResults) > 1){
				foreach($orderItemResults as $orderItem){
					//echo '<pre>'; print_r($orderItem); die;
					$itemidArray[] 	= $orderItem['item_id'];
				}
				//echo $itemid;
				//echo '<pre>'; print_r($itemidArray); die;
				$key = array_search($itemid, $itemidArray)+1;
				$ordernoFile	=	$orderno.'_'.$key;
			}else{
				$ordernoFile	=	$orderno;	
			}
			//echo $ordernoFile; die;
			//$backupfile = "media/sales/orders/".$ordernoFile.'_'."..".".html";
			$backupfile = "media/sales/orders/".$ordernoFile.".html";
			//echo $backupfile ;die;
			$fileData = fopen($backupfile, 'w') or  die('Cannot open file:  '.$backupfile);
			fwrite($fileData,$str);
			fclose($fileData);
			// Writing data to new backup file by Vaseem	
		}
	}
	
	public function getStrPriceOld($_order){
		$ord = $_order->getData();
		//echo "<pre>";print_r($ord);echo "</pre>";
		$items = $_order->getItemsCollection();
		$stritem = "<tr><td>&nbsp;</td></tr><tr>";
		$i=0;
		foreach($items as $item){
			$i = $i + 1;
			$itemval = $item->getData();
			$product = Mage::getModel('catalog/product');
			$_product = $product->load($itemval['product_id']);
			$sku = $_product->getSku();
			$price = $itemval['price'];
			$originalprice = $itemval['original_price'];
			if($price != $originalprice){
				$this->installmentflag = 1;
				$cnt = round($originalprice / $price);
				$stritem = $stritem . "
								<td>
									" . $i . ". " . $sku . " [I]
								</td>
								<td>
									" . $this->roundNo($itemval['price'],2) . "
								</td>
							</tr>";
			}
			else{
				$stritem = $stritem . "
								<td>
									" . $i . ". " . $sku . "
								</td>
								<td>
									" .  $this->roundNo($itemval['price'],2) . "
								</td>
							</tr>";
			}
		}
		//$sku = $item->getSku();
		//$originalprice = $item->getOriginalPrice();
		//$originalprice = round($originalprice, 2);
		$str = $stritem . "<tr><td>&nbsp;</td></tr><tr>
					<td>
						Subtotal
					</td>
					<td>
						" . $this->roundNo($ord['base_subtotal'],2) . "
					</td>
				</tr>
				<tr>
					<td>
						Shipping
					</td>
					<td>
						" . $this->roundNo($ord['base_shipping_amount'],2) . "
					</td>
				</tr>
				<tr>
					<td>
						Discount
					</td>
					<td>
						" . $this->roundNo($ord['base_discount_amount'],2) . "
					</td>
				</tr>
				<tr>
					<td>
						Grand Total
					</td>
					<td>
						" . $this->roundNo($ord['base_grand_total'],2) . "
					</td>
				</tr>
				";
		return $str;
	}
	
	public function getinstallmentstr($_order){
		$ord = $_order->getData();
		$items = $_order->getItemsCollection();
		$stritem = "";
		$i = 0;
		foreach($items as $item){
			$i = $i + 1;
			$itemval = $item->getData();
			//echo "<pre>";print_r($itemval);echo "</pre>";exit;
			$product = Mage::getModel('catalog/product');
			$_product = $product->load($itemval['product_id']);
			$sku = $_product->getSku();
			$price = $itemval['price'];
			$originalprice = $itemval['original_price'];
			$discount = $itemval['discount_amount'];
			$taxamount = $itemval['tax_amount'];
			$rowtotal = $price - $discount + $taxamount;
			
			$quote_item_id = $item->getQuoteItemId();
			$no_of_inst = Mage::getBlockSingleton('checkout/cart')->getNoOfInstallmentByQuoteItemId($quote_item_id);
			$instAmt = round((($price - $discount)/$no_of_inst),2);
			
			if($no_of_inst > 1){	
				$stritem = $stritem . "<div>" . $i . ". " . $sku . " : " . $instAmt . " x " . $no_of_inst . " = " . ($instAmt * $no_of_inst) . "</div>";
			}
			/*if($price != $originalprice){
				$this->installmentflag = 1;
				$cnt = round($originalprice / $price);
				$tot = $rowtotal * $cnt;
				$tot = $this->roundNo($tot, 2);
				$stritem = $stritem . "<div>" . $i . ". " . $sku . " : " . $rowtotal . " x " . $cnt . " = " . $tot . "</div>";
			}*/
		}
		if($stritem != ''){
			$str = "<div style='margin-top:15px'>
					<div class='heading border'>
						INSTALLMENTS:
					</div>
					<div style='padding:5px 0;'>
						" . $stritem . "
					</div>
				</div>";
		}			
		return $str;				
	}
	
	public function roundNo($no,$len){
		$no = round($no,$len);
		if(strrpos($no,'.')){
			$str0='';
			$str = substr($no , strrpos($no,'.') + 1);
			$req = $len - strlen($str);
			for($i=0;$i<$req;$i++){
				$str0 = $str0 . '0';
			}
			$no = $no . $str0;
		}
		else{
			$str0='';
			for($i=0;$i<$len;$i++){
				$str0 = $str0 . '0';
			}
			$no = $no . '.' . $str0;
		}
		return $no;
	}
	
	public function strmid($i,$prod){
		$_product = $prod;
		$pro = $_product->getData();
		$count = "";
		$shape = '';
		$stone = '';
		$noof = "";
		$dim = "";
		$car = "";
		$col = "";
		$cla = "";
		$qua = "";
		$emb = "";
 		if($i==3){
			$title = $_product->getAttributeText('emb_stone_name3');
			if($_product->getEmbNumberOfStones3()){
				$count = $_product->getEmbNumberOfStones3();
				$noof = "Number of " . $_product->getAttributeText('emb_shape3') . " " . $_product->getAttributeText('emb_stone_name3');
				$shape = $_product->getAttributeText('emb_shape3');
				$stone = $_product->getAttributeText('emb_stone_name3');
			}
			if($_product->getEmbDimension3()){
				$dim = $_product->getEmbDimension3() . " mm";
			}
			if($_product->getEmbCaratWeight3()){
				$car = $_product->getEmbCaratWeight3();
			}
			if($_product->getEmbColor3()){
				$col = $_product->getAttributeText('emb_color3');
			}
			if($_product->getEmbClarity3()){
				//$cla = $_product->getEmbClarity3();
				$cla = $_product->getAttributeText('emb_clarity3');
			}
			if($_product->getEmbQualityGrade3()){
				$qua = $_product->getAttributeText('emb_quality_grade3');
			}
			if($_product->getEmbSettingType3()){
				$emb = $_product->getAttributeText('emb_setting_type3');
			}
		}
		else if($i==2){
			$title = $_product->getAttributeText('emb_stone_name2');
			if($_product->getEmbNumberOfStones2()){
				$count = $_product->getEmbNumberOfStones2();
				$noof = "Number of " . $_product->getAttributeText('emb_shape2') . " " . $_product->getAttributeText('emb_stone_name2');
				$shape = $_product->getAttributeText('emb_shape2');
				$stone = $_product->getAttributeText('emb_stone_name2');
			}
			if($_product->getEmbDimension2()){
				$dim = $_product->getEmbDimension2() . " mm";
			}
			if($_product->getEmbCaratWeight2()){
				$car = $_product->getEmbCaratWeight2();
			}
			if($_product->getEmbColor2()){
				$col = $_product->getAttributeText('emb_color2');
			}
			if($_product->getEmbClarity2()){
				//$cla = $_product->getEmbClarity2();
				$cla = $_product->getAttributeText('emb_clarity2');
			}
			if($_product->getEmbQualityGrade2()){
				$qua = $_product->getAttributeText('emb_quality_grade2');
			}
			if($_product->getEmbSettingType2()){
				$emb = $_product->getAttributeText('emb_setting_type2');
			}
		}
		else if($i==1){
			$title = $_product->getAttributeText('emb_stone_name');
			if($_product->getEmbNumberOfStones1())
			{
				$count = $_product->getEmbNumberOfStones1();
				$noof = "Number of " . $_product->getAttributeText('emb_shape1') . " " . $_product->getAttributeText('emb_stone_name');
				$shape = $_product->getAttributeText('emb_shape1');
				$stone = $_product->getAttributeText('emb_stone_name');
			}
			
			if($_product->getEmbDimension1()){
				$dim = $_product->getEmbDimension1() . " mm";
			}
			if($_product->getEmbCaratWeight1()){
				$car = $_product->getEmbCaratWeight1();
			}
			if($_product->getEmbColor1()){
				$col = $_product->getAttributeText('emb_color1');
			}
			if($_product->getEmbClarity1()){
				//$cla = $_product->getEmbClarity1();
				$cla = $_product->getAttributeText('emb_clarity1');
			}
			if($_product->getEmbQualityGrade1()){
				$qua = $_product->getAttributeText('emb_quality_grade1');
			}
			if($_product->getEmbSettingType1()){
				$emb = $_product->getAttributeText('emb_setting_type1');
			}
		}
		if($title == "Diamond"){
			$title = "Diamond";
		}
		else{
			$title = "Gemstone";
		}
		
		if($this->qtcustom != ''){
			$qua = $this->qtcustom;
		}
		if($this->mainstone == $i && $this->mainstonesize != ''){
			$dim = $this->mainstonesize;
		}
		if($this->mainstone == $i){			
			$this->stonetext = $dim . ", " . $shape . ", " . $qua . ", " . $stone;//anil								
		}
					
		if($this->sidestone != '' && $i == 1){
			if($this->sidestone == "Diamond"){
				$title = "Diamond";
			}
			else{
				$title = "Gemstone";
			}
			$noof = "Number of " . $_product->getAttributeText('emb_shape1') . " " . $this->sidestone;
			$car = '';
		}
		
		if($this->VariationInfoToOrder != ''){
			$arrvariation = explode('#',$this->VariationInfoToOrder);
			$arrvariationcount = explode('!',$arrvariation[0]);
			if($arrvariationcount[1]!=''){
				$arrsynccount = array();
				$strcountsync = $arrvariationcount[1];
				$strcountsyncarr = explode('|',$strcountsync);
				for($ico=0;$ico<count($strcountsyncarr);$ico++){
					$arr_1 = explode(',',$strcountsyncarr[$ico]);
					$arrsynccount[$arr_1[0]] = $arr_1[1];
				}
				$count = $arrsynccount['Emb' . $i];
			}
			$arrvariationsize = explode('!',$arrvariation[1]);
			if($arrvariationsize[1]!=''){
				$arrsynccount = array();
				$strcountsync = $arrvariationsize[1];
				$strcountsyncarr = explode('|',$strcountsync);
				for($ico=0;$ico<count($strcountsyncarr);$ico++){
					$arr_1 = explode(',',$strcountsyncarr[$ico]);
					$arrsynccount[ array_shift($arr_1)] = implode(', ',$arr_1);
				}
				$dim = $arrsynccount['Emb' . $i] . " mm";
			}
			$arrvariationweight = explode('!',$arrvariation[2]);
			if($arrvariationweight[1]!=''){
				$arrsynccount = array();
				$strcountsync = $arrvariationweight[1];
				$strcountsyncarr = explode('|',$strcountsync);
				for($ico=0;$ico<count($strcountsyncarr);$ico++){
					$arr_1 = explode(',',$strcountsyncarr[$ico]);
					$arrsynccount[$arr_1[0]] = $arr_1[1];
				}
				$car = $arrsynccount['Emb' . $i] . " cts";
			}
		}		
		
		$str = "<div><div class='heading border'>" . $title . " Detail:</div>"; 
		$str = $str . "<div style='padding:5px 0;'><table>";
		if($noof!=""){
			$str = $str . "<tr><td style='text-align:right;width:200px'>" . $noof . ":</td><td style='padding-left:5px'>" . $count . "</td></tr>";
		}
		if($dim != ""){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Approximate Dimensions:</td><td style='padding-left:5px'>" . $dim . "</td></tr>";
		}
		if($car != ""){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Approximate Carat Weight:</td><td style='padding-left:5px'>" . $car . "</td></tr>";
		}
		if($col != "" and $qua == ""){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Minimum Color:</td><td style='padding-left:5px'>" . $col . "</td></tr>";
		}
		if($cla != "" and $qua == ""){

			$str = $str . "<tr><td style='text-align:right;width:200px'>Minimum Clarity:</td><td style='padding-left:5px'>" . $cla . "</td></tr>";
		}
		if($qua != ""){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Quality Grade:</td><td style='padding-left:5px'>" . $qua . "</td></tr>";
		}
		if($emb != ""){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Setting Type:</td><td style='padding-left:5px'>" . $emb . "</td></tr>";
		}
		$str = $str . "</table></div></div>";
		return $str;
	}
	
	public function strloose($prod){
		$_product = $prod;
		$pro = $_product->getData();
		$str = "";
		$dim = "";
		$shape = "";
		$stone = "";
		if($_product->getGemstoneType()){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Gemstone Type:</td><td style='padding-left:5px'>" . $_product->getAttributeText('gemstone_type') . "</td></tr>";
			$stone = $_product->getAttributeText('gemstone_type');
		}
		if($_product->getGemstoneShape()){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Gemstone Shape:</td><td style='padding-left:5px'>" . $_product->getAttributeText('gemstone_shape') . "</td></tr>";
			$shape = $_product->getAttributeText('gemstone_shape');
		}
		if($_product->getGemstoneCaratWeight()){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Approximate Carat Total Weight:</td><td style='padding-left:5px'>" . $_product->getData('gemstone_carat_weight') . " cts</td></tr>";
		}
		if($_product->getGemstoneColor()){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Gemstone Color:</td><td style='padding-left:5px'>" . $_product->getAttributeText('gemstone_color') . "</td></tr>";
		}
		if($_product->getGemstoneClarity()){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Gemstone Clarity:</td><td style='padding-left:5px'>" . $_product->getAttributeText('gemstone_clarity') . "</td></tr>";
		}
		if($_product->getGemstoneDimension()){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Approximate Dimensions:</td><td style='padding-left:5px'>" . $_product->getData('gemstone_dimension') . " mm</td></tr>";
			$dim = $_product->getData('gemstone_dimension');
		}
		if($_product->getGemstoneBrilliance()){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Gemstone Brilliance:</td><td style='padding-left:5px'>" . $_product->getAttributeText('gemstone_brilliance') . "</td></tr>";
		}
		if($_product->getGemstoneEnhancement()){
			$str = $str . "<tr><td style='text-align:right;width:200px'>Gemstone Enhancement:</td><td style='padding-left:5px'>" . $_product->getAttributeText('gemstone_enhancement') . "</td></tr>";
		}
		 
		$this->stonetext = $dim . ", " . $shape . ", " . $stone;
		
		return $str;
	}
	
	public function getgiftmsg($_order){
		$_giftMessage = Mage::helper('giftmessage/message')->getGiftMessage($_order->getGiftMessageId());
		$gift = $_giftMessage->getData();
		$sender = '';
		$reciever = '';
		$giftmsg = '';
		if(isset($gift['sender'])){
			$sender = $gift['sender'];
		}
		if(isset($gift['recipient'])){
			$reciever = $gift['recipient'];
		}
		if(isset($gift['message'])){
			$giftmsg = $gift['message'];
		}
		$strgift = "<div style='border-bottom: 1px solid #cccccc;text-align:center;'><div class='heading'>GIFT MESSAGE:</div><div style='padding:5px 0;'>";
		if($giftmsg !=''){
			$strgift = $strgift . "<div>".$giftmsg."</div>";
		}
		$strgift = $strgift . "</div></div>";
		return $strgift;
	}
	
	public function getOkToShipDiv($orderid){
		$res = Mage_Adminhtml_Block_Sales_Order_View_Oktoship::getOkToShipInfo($orderid);
		if($res == "NA"){
			return "Under Review";
		}
		$ok_ok = "0";
		$ok_comment = "";
		$str = "";
		$ok_comment = $res['comment'];
		$ok_ok = $res['oktoship'];
		if($ok_ok==1){
			$str = $str . "OK TO SHIP";
		}
		else{
			$str = $str . "Under Review";
		}
		//$str = $str . "<br />Comment: " . $ok_comment;
		return $str;
	}
	
	public function getExtraCommentDiv($orderid){
		$res = Mage_Adminhtml_Block_Sales_Order_View_Extracomment::getEtraCommentInfo($orderid);
		if(!$res){
			return "";
		}
		$comment = "";
		$str = "<div><div class='heading border'>EXTRA COMMENT:</div><div style='padding:5px 0;'>";
		$comment = $res['comment'];
		$str = $str . "Comment: " . $comment . "</div></div>";
		
		return $str;
	}
	
	public function getengravingtext(){
		if($this->engraving != ''){
			$str = "<div style='margin-top:15px'>
						<div class='heading border'>
							ENGRAVING:
						</div>
						<div style='padding:5px 0;height:60px'>
							" . $this->engraving . "
						</div>
					</div>";
			return $str;
		}
		else{
			return "";
		}
	}
	
	public function numberToWords ($x){
		 $nwords = array("", "one", "two", "three", "four", "five", "six","seven", "eight", "nine", "ten", "eleven", "twelve", "thirteen","fourteen", "fifteen", "sixteen", "seventeen", "eightteen","nineteen", "twenty", 30 => "thirty", 40 => "fourty",50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eigthy",90 => "ninety");
		 if(!is_numeric($x)){
			 $w = '#';
		 }
		 else if(fmod($x, 1) != 0){
			 $w = '#';
		 }
		 else{
			 if($x < 0){
				 $w = 'minus ';
				 $x = -$x;
			 }
			 else{
				 $w = '';
			 }
			 
			 if($x < 21){
				 $w .= $nwords[$x];
			 }
			 else if($x < 100){
				 $w .= $nwords[10 * floor($x/10)];
				 $r = fmod($x, 10);
				 if($r > 0){
					 $w .= ' '. $nwords[$r];
				 }
			 } 
			 else if($x < 1000){			
					$w .= $nwords[floor($x/100)] .' hundred';
				 $r = fmod($x, 100);
				 if($r > 0){
					 $w .= ' '. $this->numberToWords($r);
				 }
			 } 
			 else if($x < 1000000){
				$w .= $this->numberToWords(floor($x/1000)) .' thousand';
				 $r = fmod($x, 1000);
				 if($r > 0){
					 $w .= ' ';
					 if($r < 100){
						 $w .= ' ';
					 }
					 $w .= $this->numberToWords($r);
				 }
			 } 
			 else {
				 $w .= $this->numberToWords(floor($x/1000000)) .' million';
				 $r = fmod($x, 1000000);
				 if($r > 0){
					 $w .= ' ';
					 if($r < 100){
						 $word .= ' ';
					 }
					 $w .= $this->numberToWords($r);
				 }
			 }
		 }
		 return strtoupper($w);
	}
} ?>