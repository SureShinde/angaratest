<?php
class hp_hprcv_IndexController extends Mage_Core_Controller_Front_Action
{
	public $cert_quality = '';
	public $cert_metal = '';
	
	public function certAction(){		
		$productid = $this->getRequest()->getParam('certificate_id');
		$product = Mage::getModel('catalog/product')->load($productid);
		$itemid = $this->getRequest()->getParam('itemid');
		$item = Mage::getModel('sales/order_item')->load($itemid);
		$cartitemid = $item->getData('quote_item_id');
		$itemOptions = $item->getProductOptions();
		$buyRequest = $item->getBuyRequest();
		$VariationInfoToOrder = '';
		
		$img = $product->getImageUrl();
		
		if($product->getAttributeSetId() != 16){
			
			if($product->getCustomj())
			 {
				$img = Mage::getBlockSingleton('hprcv/hprcv')->getrootpath() . "cartimages/" . $cartitemid . ".png";
			 }else{
			 	$new_advrp_img = '/media/catalog/product/images/variations/'.$sku_prefix.'_'.$metal_prefix.''.$gr_prefix.'.jpg';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].''.$new_advrp_img)) {
					$img = $new_advrp_img;
				}
			 }
			if($productid != ''){
				$_SESSION['cartprodid'] = $productid;
			}		
			
			$this->getRequest()->setParam('certificate_id', $productid);
			if(isset($itemOptions['options']))
			{
				$options = $itemOptions['options'];
				for($i=0;$i<count($options);$i++)
				{
					if($options[$i]['label']=="VariationInfoToOrder")
					{
						$VariationInfoToOrder = $options[$i]['value'];
						
						$arrvariation = explode('#',$VariationInfoToOrder);
						
						$arrvariationcount = explode('!',$arrvariation[0]);
						if($arrvariationcount[1]!='')
						{
							$this->getRequest()->setParam('cert_sync_count', $arrvariationcount[1]);
						}
						$arrvariationcount = explode('!',$arrvariation[1]);
						if($arrvariationcount[1]!='')
						{
							$this->getRequest()->setParam('cert_sync_size', $arrvariationcount[1]);
						}
						$arrvariationcount = explode('!',$arrvariation[2]);
						if($arrvariationcount[1]!='')
						{
							$this->getRequest()->setParam('cert_sync_weight', $arrvariationcount[1]);
						}
						
					}
					else if($options[$i]['label'] == 'Stone Quality')
					{
						$this->getRequest()->setParam('cert_quality', $options[$i]['value']);
					}
					else if($options[$i]['label'] == 'Metal Type')
					{
						$str14k='';
						$stropmt = $options[$i]['value'];
						if($stropmt == 'Yellow Gold' || $stropmt == 'White Gold')
						{
							$str14k = '14K';
						}
						$this->getRequest()->setParam('cert_metal',$str14k ." " . $stropmt);
					}
				}
			}
			$this->getRequest()->setParam('cert_image', $img);			
			$this->backendpdfeditAction();
			
		}
		else{
			$this->motherscertAction();
		}		
	}
	
	public function backendpdfeditAction(){
		$str = $this->generatePdfHtml();
		Mage::getBlockSingleton('hprcv/hprcv')->pdfform($str);
		echo '<style type="text/css">#pagebutton{display:block !important;}</style>';
	}
		
	// Function added by Vaseem to get invoice email data and send email
	public function editinvoiceAction(){
		$invoicehtml 	= 	$this->getRequest()->getParam('invoicehtml');
		//echo '<pre>'; print_r($invoicehtml); 
		$sendorName		=	'Sales';
		$sendorEmail	=	'techsupport@angara.com';
		$receiverName	=	$this->getRequest()->getParam('customer_name');
		$receiverEmail	=	$this->getRequest()->getParam('customer_email');	//	'vaseem.ansari@angara.com';
		$emailSubject	=	$this->getRequest()->getParam('email_subject');
		$emailBody		=	$invoicehtml;
		$mailType		=	'html';
		$status			=	Mage::helper('function')->sendZendMail($sendorName, $sendorEmail, $receiverName, $receiverEmail, $emailSubject, $emailBody, $mailType);
		
		$order_id		=	$this->getRequest()->getParam('order_id');
		$invoice_id		=	$this->getRequest()->getParam('invoice_id');
		/*echo '<br>order_id->'.$order_id;
		echo '<br>invoice_id->'.$invoice_id;die;*/
		if($invoice_id!=''){
			//echo 'Existing Invoice';
			Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_invoice/view", array( 'order_id'=>$order_id, 'invoice_id'=>$invoice_id )));
		}else{
			//echo 'First Time Invoice';
			Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id'=>$order_id)));
		}
	}	
	
	public function backendpdfviewAction(){
		$str = $this->getRequest()->getParam('pdfhtml','Error');
		$sku = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('certificate_id'))->getSku();

		if($sku == ''){
			if(isset($_SESSION['cartprodid']) && !empty($_SESSION['cartprodid'])){
				$sku = Mage::getModel('catalog/product')->load($_SESSION['cartprodid'])->getSku();
				unset($_SESSION['cartprodid']);
			}
		}
		$myFile = "certificate/AngaraCert.html";
		$fh = fopen($myFile, 'w') or die("can't open file". $myFile);
		fwrite($fh, $str);
		fclose($fh);
		$url = "/certificate/dompdf.php?input_file=AngaraCert.html&sku=" . $sku;
	
		//header("Refresh:0;URL='" . $url . "'");

		//	Code Added by Vaseem to generate html certificate 
			$destinationFile 	= 'certificate/html/'.$sku.'.html'; 		// change this for relative path to your desired server location 
			$fp 	= fopen($myFile,"r"); 			//	Open the file in read mode
			$fp2 	= fopen($destinationFile, "w") or  die('Cannot open file:  '.$destinationFile);; //	Open the file in write mode
			while (!feof($fp)) { 
				$buf = fread($fp, 1024); 
				fwrite($fp2, $buf); 
			} 
			fclose($fp); 
			fclose($fp2); 
			header("Refresh:0;URL='" . Mage::getBaseUrl().$destinationFile . "'");
		//	Code Added by Vaseem to generate html certificate 
	}
		
	public function pdfAction(){
		$sku = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('certificate_id'))->getSku();
		 
		$str = $this->generatePdfHtml();
		$myFile = "certificate/AngaraCert.html";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);
		fclose($fh);
		$url = "/certificate/dompdf.php?input_file=AngaraCert.html&sku=" . $sku;
		
		//	Code Added by Vaseem to generate html certificate 
		$destinationFile 	= 'certificate/html/'.$sku.'.html'; 		// change this for relative path to your desired server location 
		
		$fp 	= fopen($myFile,"r"); 			//	Open the file in read mode
		$fp2 	= fopen($destinationFile, "w") or  die('Cannot open file:  '.$destinationFile);; //	Open the file in write mode
		while (!feof($fp)) { 
			$buf = fread($fp, 1024); 
			fwrite($fp2, $buf); 
		} 
		fclose($fp); 
		fclose($fp2); 
		header("Refresh:0;URL='" . Mage::getBaseUrl().$destinationFile . "'");
		//	Code Added by Vaseem to generate html certificate 
	}
	
	function getStoneUniqueName($product, $stoneIndex){
		//$chkVal=explode(',',$product->getSku());
		//if(count($chkVal)<5){
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
		//$chkVal=explode('-',$product->getSku());
		//if(count($chkVal)<5){
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
			   		if(strpos(' '.$stones[$stoneName]['size'], ' '.$product->getAttributeText('stone'.$i.'_size')) === false){
						$stones[$stoneName]['size'] .= ', '.$product->getAttributeText('stone'.$i.'_size');
			    	}
					if(strpos(' '.$stones[$stoneName]['setting'], ' '.$product->getAttributeText('stone'.$i.'_setting')) === false){
						$stones[$stoneName]['setting'] .= ', '.$product->getAttributeText('stone'.$i.'_setting');
					}
					$stones[$stoneName]['weight'] += $product->getData('stone'.$i.'_weight');
					$stones[$stoneName]['count'] += $product->getData('stone'.$i.'_count');
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
				if($stone['type'] == 'Gemstone' && $stone['grade'] != 'Lab Created' && $stone['grade'] != 'Classic Moissanite' && $stone['grade'] != 'Forever Brilliant'){
					$stone['grade'] = 'Natural - '.$stone['grade'];
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
						$stone['weight'] = $stone['weight'].' carat'; //number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carat';
					}
					else{
						$stone['weight'] = $stone['weight'].' carats'; //number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carats';
					}
					
					if($stone['type'] == 'Gemstone' && $stone['grade'] != 'Lab Created' && $stone['grade'] != 'Classic Moissanite' && $stone['grade'] != 'Forever Brilliant'){
						$stone['grade'] = 'Natural - '.$stone['grade'];
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
						$stone['weight'] = $stone['weight'].' carat';//number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carat';
					}
					else{
						$stone['weight'] = $stone['weight'].' carats';//number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carats';
					}
					if($stone['type'] == 'Gemstone' && $stone['grade'] != 'Lab Created' && $stone['grade'] != 'Classic Moissanite' && $stone['grade'] != 'Forever Brilliant'){
						$stone['grade'] = 'Natural - '.$stone['grade'];
					}
				}
			}
		}
		// converting associative array to numeric array
		return array_values($stones);
	} 
	
	private function generatePdfHtml() {
		$product = Mage::getModel('catalog/product');
		$_product = $product->load($this->getRequest()->getParam('certificate_id'));
		$jewelryStyles = ((is_array($product->getAttributeText('jewelry_styles')))?array_values($product->getAttributeText('jewelry_styles')):array($product->getAttributeText('jewelry_styles')));
		$masterSku=$_product->getMasterSku();
		$masterCollection = Mage::getModel('catalog/product')->loadByAttribute('sku',$masterSku);
		if($masterCollection && $masterCollection->getCategoryIds()){
			$masterCategoryIds = $masterCollection->getCategoryIds();
		}	
		$metal = $_product->getAttributeText('metal1_type');
		$_prods = $this->getStones($_product);
		$str = "<meta charset='UTF-8'/>		
			<style type='text/css'>
					.rotate { 
						z-index:-1;
						 -webkit-transform: rotate(270deg);
						-moz-transform: rotate(270deg);
						-o-transform: rotate(270deg);
						-ms-transform: rotate(270deg);
						-sand-transform: rotate(270deg);
						position:absolute;
						text-align:center;	
						height:772px;
						width:996px;					
						left:-136px;
						top:114px;						
						color:#651B0E;						
						font-weight:bold;
					}
						
					sup {
						vertical-align:middle;
						padding-right:3px;
					}
						
					sub {
						vertical-align:middle;
						padding-left:3px;
					}
					
					.detail-row {
						background:url(/skin/frontend/angara/default/images/certificate-dotted-border.jpg) 0 15px repeat-x;						
						width:649px; 						
						margin:0px;
					}
					
					.inner-box-style {
						width:655px;
						margin-left:-3px;
					}
					
					.all-detail-box-wrapper {
						margin:5px 10px 20px 10px;						
						background-color:#E5ECFC;
						padding:5px 10px;						
					}
						
					.gem-details-wrapper {
						padding-left:34px;
					}
					
					.product-img-descp {
						position:absolute;
						bottom:0px;
						left:10px;						
						right:10px;						
					}
					
					.main-wrapper-box {
						margin:0px;
						border:1px solid #ccc;
						width:750px;
						color:#651B0E;
						padding-bottom:10px;
						position:relative;
						min-height:820px;
						padding-bottom:170px;
					}
			</style>
				
			<!--[if IE]>
			<style type='text/css'>
					.rotate { 
						z-index:-1;
						 -webkit-transform: rotate(270deg);
						-moz-transform: rotate(270deg);
						-o-transform: rotate(270deg);
						-ms-transform: rotate(270deg);
						-sand-transform: rotate(270deg);
						filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);						
						position:absolute;
						text-align:center;	
						height:772px;
						width:996px;					
						left:-20px;
						top:0px;						
						color:#651B0E;						
						font-weight:bold;
					}
				</style>   
			<![endif]-->";
			
			$str = $str . "<table cellpadding='0' cellspacing='0' style='margin-left:30px'><tr><td></td><td>
			<div class='main-wrapper-box'>
			<div class='rotate rotatem'>ANGARA REPORT #".$masterSku."</div>
			
			<table cellpadding='0' cellspacing='0'><tr><td valign='top'><table style='width:750px;margin-top:5px'><tr><td valign='top'><img src='https://www.angara.com/certificate/logo.png' style='margin-left:5px;margin-top:5px' /></td><td style='width:245px'><div style='width:235px;border-left:1px solid #ccc;padding-left:5px'><b>Angara Inc.</b><br>550 South Hill Street, Suite 1015<br>Los Angeles, CA 90013<br>Phone 888-926-4272<br>Fax 201-503-8159</div></td></tr></table>";
			
			// if count > 3 then remove this div in certificate
			if(count($_prods) < 4){
				$str = $str . "
					<style type='text/css'>
					.left-title-text {
						padding-bottom:0px;
						font-size:13px;
					}
					
					.stone-heading-title {
						margin-top:15px;
						margin-bottom:5px;
						font-size:15px;
						font-weight:bold;
					}
					</style>
					
					<div style='height:40px'>&nbsp;</div>";
			}
			else{
				$str = $str . "
					<style type='text/css'>
					.left-title-text {
						padding-bottom:0px;
						font-size:11px;
					}
					
					.stone-heading-title {
						margin-top:12px;
						margin-bottom:5px;
						font-size:10px;
						font-weight:bold;
					}
					</style>
				";
			}
			
			$str = $str . "<div style='padding-left:5px'><b>CERTIFICATE OF AUTHENTICITY:</b></div>
			<div class='all-detail-box-wrapper'>";
			
			$str = $str . "<div align='center'><b>" . $_product->getShortDescription() . "</b></div><div class='stone-heading-title'>Product Details:</div><div class='gem-details-wrapper'><div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Angara Item #:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $masterSku . "</a></td></tr></table></div>";
		
				$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Metal:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $metal . "</a></td></tr></table></div>";
				
			/* Added by Saurabh For bands*/
					if(trim($_product->getBandWidth())!=''){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Width: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('band_width') . "</a></td></tr></table></div>";	
					}
					if(trim($_product->getBandHeight())!=''){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Height: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getBandHeight() . "</a></td></tr></table></div>";	
					}
					if(trim($_product->getApproximateMetalWeight())!=''){
						if($_product->getApproximateMetalWeight()=='Select Ring Size'){
							$approxWt = $_product->getApproximateMetalWeight();
						}
						else{
							$approxWt = $_product->getApproximateMetalWeight().' grams';
						}
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approx. Weight: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $approxWt . "</a></td></tr></table></div>";	
					}
					if(trim($_product->getFit())!=''){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Fit: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('fit') . "</a></td></tr></table></div>";	
					}
					/* S: Added by Pankaj */
					if(trim($_product->getLength())!=''){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Length: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('length') . "</a></td></tr></table></div>";	
					}
					if(trim($_product->getDiameter())!=''){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Diameter: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('diameter') . "</a></td></tr></table></div>";
					}
					if(trim($_product->getWidth())!=''){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Width: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('width') . "</a></td></tr></table></div>";	
					}
					if(trim($_product->getClaspType())!=''){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Clasp: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('clasp_type') . "</a></td></tr></table></div>";	
					}
					if(trim($_product->getButterfly1Type())!=''){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Backing Type: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('butterfly1_type') . "</a></td></tr></table></div>";	
					}					
					/* E: Added by Pankaj */
					$str = $str ."</div>";
					
			/* Added by Saurabh For bands end*/
			foreach($_prods as $_prod){
				if($_prod['count']>1){
					$_prodNameLastChar=substr($_prod['name'],-1);
					if($_prodNameLastChar=='y'){
						$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
						$stoneName=$_prodNameWithoutLastChar.'ies';
					}
					else if($_prodNameLastChar=='z'){
						$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
						$stoneName=$_prodNameWithoutLastChar.'zes';
					}
					else if($_prodNameLastChar=='x'){
						$_prodNameWithoutLastChar=substr_replace($_prod['name'], "", -1);
						$stoneName=$_prodNameWithoutLastChar.'xes';
					}
					else{
						$stoneName=$_prod['name'].'s';
					}
				}
				else{
					$stoneName=$_prod['name'];
				}
				
				if((trim($_prod['color']) && trim($_prod['color']) != null) || (trim($_prod['clarity']) && trim($_prod['clarity']) != null)){
					$showQualityGrade = false;
				}
				else{
					$showQualityGrade = true;
				}				
				
				$str = $str ."<div class='stone-heading-title'>" . $_prod['type'] . " Information:</div>
		<div class='gem-details-wrapper'>";
				
				if(trim($_prod['count']) != '' && trim($_prod['count']) > 0){	
				//	S:VA
				$str = $str . "<div class='detail-row'><table class='inner-box-style'>".((in_array('347', $masterCategoryIds) || in_array('Eternity', $jewelryStyles))?'':"<tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Number of ".$_prod['shape'].' '.$stoneName." :</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_prod['count'] . "</a></td></tr>")."</table></div>";
				}
				
				if(($_prod['grade'] != 'Lab Created' && $_prod['grade'] != 'Simulated' && !in_array('457',$masterCategoryIds)) || (in_array('457',$masterCategoryIds) && $_prod['type'] == 'Diamond')){
					$enhancementDetail = $this->stoneEnhancementDetail($_prod['name']);
					if($enhancementDetail != ''){
						$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Enhancement:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $enhancementDetail . "</a></td></tr></table></div>";
					}
				}	
				
				//$isSize= explode('-',$_product->getSku());
				//if(count($isSize)<5){
				if(!$_product->getDiamondColor()){
					if(trim($_prod['size']) !='' && $_prod['size'] > 0 && $_prod['type'] != 'Diamond'){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approximate Dimensions:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_prod['size'] . "</a></td></tr></table></div>";
					}
				}
			
				//if(count($isSize)<5){
				if(!$_product->getDiamondColor()){
					if(trim($_prod['weight'])!='' && $_prod['weight'] > 0){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approximate Carat Total Weight: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_prod['weight']. "</a></td></tr></table></div>";
					}
				}
				else{
					if(trim($_prod['weight'])!=''){
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approximate Carat Total Weight: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_prod['weight']. "</a></td></tr></table></div>";
					}
				}
				
				// Added by Pankaj
				if(trim($_prod['grade']) == 'Classic Moissanite'){
					$superScript = '<sup style="font-size:8px;">TM</sup>';
				}
				else if(trim($_prod['grade']) == 'Forever Brilliant'){
					$superScript = '<sup style="font-size:13px;">&reg;</sup>';
				}
				
				if($showQualityGrade == false){
					if(trim($_prod['color']) && trim($_prod['color']) != null){
						$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Color: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_prod['color'] . "</a></td></tr></table></div>";
					}	
					if(trim($_prod['clarity']) && ($_prod['clarity']) != null){
						$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Clarity: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_prod['clarity'] . "</a></td></tr></table></div>";
					}	
				}
				else{				
					$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Quality Grade: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_prod['grade'] . (($superScript != '') ? $superScript : '')."</a></td></tr></table></div>";
				}
				
				if(trim($_prod['setting'])!=''){
				$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Setting Type: </a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_prod['setting'] . "</a></td></tr></table></div></div>";	
				}
				
			$prodType[] .=$_prod['type'];
			$prodGrade[] .=$_prod['grade'];
			}
			
			$image=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/catalog/product'.$_product->getImage();
			
			if(in_array("Gemstone",$prodType) && !in_array("Diamond",$prodType) && !in_array("Lab Created",$prodGrade) && !in_array("Simulated",$prodGrade))
			{
				$gemstone=1;	
			}
			else{
				$gemstone=0;
			}
			if(!in_array("Gemstone",$prodType) && in_array("Diamond",$prodType) && !in_array("Lab Created",$prodGrade) && !in_array("Simulated",$prodGrade))
			{
				$diamond=1;	
			}
			else{
				$diamond=0;
			}
			
			if(in_array("Gemstone",$prodType) && in_array("Diamond",$prodType) && !in_array("Lab Created",$prodGrade) && !in_array("Simulated",$prodGrade))
			{
				$gemDia=1;	
			}
			else{
				$gemDia=0;
			}
			
			if(in_array("Lab Created",$prodGrade) || in_array("Simulated",$prodGrade))			{
				$lab=1;	
			}
			else{
				$lab=0;
			}
			
			if($_prods[0]['name']!=''){
				$_prodsName=$_prods[0]['name'];
			}
			else{
				$_prodsName ='gemstones';
			}
			
			if($lab==1){
				$desc = "Highly qualified and experienced Angara gemologists have tested and identified all stones featured in the jewelry on Angara.com. State of the art equipment is used which accurately identifies all characteristics of the stones.";
			}
			else if($diamond == 0 && $gemstone == 0 && $gemDia==0)
			{
				$desc = "Highly qualified and experienced Angara jewelry experts have tested and identified all jewelry featured on Angara.com. State of the art equipment is used which accurately identifies all characteristics of the jewelry.";
			}
			else if($diamond == 1 && $gemstone == 0)
			{
				if($_prodsName == 'Moissanite'){
					$desc = "Highly qualified and experienced Angara gemologists have tested and identified all stones featured in the jewelry on Angara.com. State of the art equipment is used which accurately identifies all characteristics of the stones.";
				}
				else{
					$desc = "Highly qualified and experienced Angara jewelry experts have tested and identified all stones featured in the jewelry on Angara.com. State of the art equipment is used which accurately identifies all characteristics of the diamonds. Angara guarantees all diamonds used in this jewelry are 100% natural.";
				}
			}
			else if($diamond == 0 && $gemstone == 1)
			{
				if($_prodsName == 'Moissanite'){
					$desc = "Highly qualified and experienced Angara gemologists have tested and identified all stones featured in the jewelry on Angara.com. State of the art equipment is used which accurately identifies all characteristics of the stones.";
				}
				else{
					$desc = "Highly qualified and experienced Angara gemologists have tested and identified all stones featured in the jewelry on Angara.com. State of the art equipment is used which accurately identifies all characteristics of the ".$_prodsName.". Angara guarantees all gemstones used in this jewelry are 100% natural.";
				}
			}
			else if($diamond == 0 && $gemstone == 0 && $gemDia == 1)
			{
				if($_prodsName == 'Moissanite'){
					$desc = "Highly qualified and experienced Angara gemologists have tested and identified all stones featured in the jewelry on Angara.com. State of the art equipment is used which accurately identifies all characteristics of the stones.";
				}
				else{
					$desc = "Highly qualified and experienced Angara gemologists have tested and identified all stones featured in the jewelry on Angara.com. State of the art equipment is used which accurately identifies all characteristics of the ".$_prodsName. " and the diamonds. Angara guarantees all ".$_prodsName." and diamonds used in this jewelry are 100% natural.";
				}
			}
			
			if($_prodsName == 'Moissanite'){	
				$str = $str."<div style='text-align:justify;padding-right:40px;font-size:10px;padding-top:10px'>*All moissanite weights are approximate and listed as diamond equivalent. All moissanites are Charles & Colvard created.</div>";
			}
			
			$str = $str . "<div class='product-img-descp'>				
						<div style='width:570px; float:left; margin-top:30px;'   >
							<div><b>ANGARA REPORT #" . $masterSku . "</b></div>
							<div style='text-align:justify;padding-right:40px;font-size:15px;padding-top:5px'>" . $desc . "</div>
						</div>
						<div style='float:right;'>
							<img src='" . $image . "' style='height:150px;width:150px;border:2px solid #ccc;  vertical-align:bottom;'  /> 
							<div align='center' style='margin-top:5px'>" . $masterSku . "</div>
						</div>
				
			</div></div></td></tr></table>";
			$str = $str . "</div></td></tr></table><style>body {font-family: 'Helvetica'} table{margin:0px;padding:0px}</style>";

			return $str;
	}
	
	private function stoneEnhancementDetail($stoneName){
		if($stoneName){
			if($stoneName == 'Diamond' || $stoneName == 'Peridot' || $stoneName == 'Garnet' || $stoneName == 'Opal' || $stoneName == 'Amethyst' || $stoneName == 'Citrine' || $stoneName == 'Rose Quartz' || $stoneName == 'South Sea Cultured Pearl' || $stoneName == 'Golden South Sea Cultured Pearl' || $stoneName == 'Tahitian Cultured Pearl' || $stoneName == 'Tsavorite'){
				$stoneText = 'None';
			}
			else if($stoneName == 'Turquoise'){
				$stoneText = 'Stabilized';
			}
			else if($stoneName == 'Moissanite'){
				$stoneText = 'Lab Created';
			}
			else if($stoneName == 'Enhanced Black Diamond' || $stoneName == 'Green Amethyst'){
				$stoneText = 'Irradiated';
			}
			else if($stoneName == 'Enhanced Blue Diamond' || $stoneName == 'Blue Topaz'|| $stoneName == 'Morganite'){
				$stoneText = 'Heated and Irradiated';
			}
			else if($stoneName == 'Ruby' || $stoneName == 'Blue Sapphire' || $stoneName == 'Pink Sapphire' || $stoneName == 'Tanzanite' || $stoneName == 'Aquamarine' || $stoneName == 'Pink Tourmaline' || $stoneName == 'Carnelian' || $stoneName == 'White Sapphire' || $stoneName == 'Yellow Sapphire'){
				$stoneText = 'Heated';
			}
			else if($stoneName == 'Emerald'){
				$stoneText = 'Oiling';
			}
			else if($stoneName == 'Black Onyx' || $stoneName == 'Golden Japanese Cultured Pearl'){
				$stoneText = 'Dyed';
			}
			else if($stoneName == 'Akoya Cultured Pearl' || $stoneName == 'Freshwater Cultured Pearl'){
				$stoneText = 'Bleached';
			}
			return $stoneText;			
		}
	}
	
	public function strmid($i){
		$product = Mage::getModel('catalog/product');
		$_product = $product->load($this->getRequest()->getParam('certificate_id'));
		$pro = $_product->getData();
		$count = "";
		$noof = "";
		$dim = "";
		$car = "";
		$col = "";
		$cla = "";
		$qua = "";
		$emb = "";
 		if($i==3)
		{
			$title = $_product->getAttributeText('emb_stone_name3');
			if($_product->getEmbNumberOfStones3())
			{
				$count = $_product->getEmbNumberOfStones3();
				$emb_title = $_product->getAttributeText('emb_stone_name3');
				if($_product->getData('emb_number_of_stones3') > 1):
					
					if($emb_title=="Ruby")
						{$emb_title = "Rubies";} 
					elseif($emb_title=="Lab Created Ruby")
						{$emb_title = "Lab Created Rubies";} 
					else
						{$emb_title = $emb_title ."s";}
					
				endif;
				$noof = "Number of " . $_product->getAttributeText('emb_shape3') . " " . $emb_title;
			}
			if($_product->getEmbDimension3())
			{
				$dim = $_product->getEmbDimension3();
			}
			if($_product->getEmbCaratWeight3())
			{
				$car = $_product->getEmbCaratWeight3();
			}
			if($_product->getEmbColor3())
			{
				$col = $_product->getAttributeText('emb_color3');
			}
			if($_product->getEmbClarity3())
			{
				//$cla = $_product->getEmbClarity3();
				$cla = $_product->getAttributeText('emb_clarity3');
			}
			if($_product->getEmbQualityGrade3())
			{
				$qua = $_product->getAttributeText('emb_quality_grade3');
			}
			if($_product->getEmbSettingType3())
			{
				$emb = $_product->getAttributeText('emb_setting_type3');
			}
		}
		else if($i==2)
		{
			$title = $_product->getAttributeText('emb_stone_name2');
			if($_product->getEmbNumberOfStones2())
			{
				$count = $_product->getEmbNumberOfStones2();
				$emb_title = $_product->getAttributeText('emb_stone_name2');
				if($_product->getData('emb_number_of_stones2') > 1):
					
					if($emb_title=="Ruby")
						{$emb_title = "Rubies";} 
					elseif($emb_title=="Lab Created Ruby")
						{$emb_title = "Lab Created Rubies";} 
					else
						{$emb_title = $emb_title ."s";}
					
				endif;
				$noof = "Number of " . $_product->getAttributeText('emb_shape2') . " " . $emb_title;
			}
			if($_product->getEmbDimension2())
			{
				$dim = $_product->getEmbDimension2();
			}
			if($_product->getEmbCaratWeight2())
			{
				$car = $_product->getEmbCaratWeight2();
			}
			if($_product->getEmbColor2())
			{
				$col = $_product->getAttributeText('emb_color2');
			}
			if($_product->getEmbClarity2())
			{
				//$cla = $_product->getEmbClarity2();
				$cla = $_product->getAttributeText('emb_clarity2');
			}
			if($_product->getEmbQualityGrade2())
			{
				$qua = $_product->getAttributeText('emb_quality_grade2');
			}
			if($_product->getEmbSettingType2())
			{
				$emb = $_product->getAttributeText('emb_setting_type2');
			}
		}
		else if($i==1)
		{
			$title = $_product->getAttributeText('emb_stone_name');
			if($_product->getEmbNumberOfStones1())
			{
				$count = $_product->getEmbNumberOfStones1();
				$emb_title = $_product->getAttributeText('emb_stone_name');
				if($_product->getData('emb_number_of_stones1') > 1):
					
					if($emb_title=="Ruby")
						{$emb_title = "Rubies";} 
					elseif($emb_title=="Lab Created Ruby")
						{$emb_title = "Lab Created Rubies";} 
					else
						{$emb_title = $emb_title ."s";}
					
				endif;
				$noof = "Number of " . $_product->getAttributeText('emb_shape1') . " " . $emb_title;
			}
			
			if($_product->getEmbDimension1())
			{
				$dim = $_product->getEmbDimension1();
			}
			if($_product->getEmbCaratWeight1())
			{
				$car = $_product->getEmbCaratWeight1();
			}
			if($_product->getEmbColor1())
			{
				$col = $_product->getAttributeText('emb_color1');
			}
			if($_product->getEmbClarity1())
			{
				//$cla = $_product->getEmbClarity1();
				$cla = $_product->getAttributeText('emb_clarity1');
			}
			if($_product->getEmbQualityGrade1())
			{
				$qua = $_product->getAttributeText('emb_quality_grade1');
			}
			if($_product->getEmbSettingType1())
			{
				$emb = $_product->getAttributeText('emb_setting_type1');
			}
			
		}
		
		if($this->cert_quality != '' && $qua != '')
		{
			$qua = $this->cert_quality;
		}
		if($title == "Simulated Diamond" && $qua == "Lab Created")
		{
			$qua = "Simulated";
		}
		if($title == "Diamond" || $title == "Simulated Diamond")
		{
			$title = "Diamond";
		}
		else
		{
			$title = "Gemstone";
		} 
		
		$cert_sync_count = $this->getRequest()->getParam('cert_sync_count');
		if(isset($cert_sync_count) && $cert_sync_count != '')
		{
			$arrsynccount = array();
			$strcountsync = $cert_sync_count;
			$strcountsyncarr = explode('|',$strcountsync);
			for($ico=0;$ico<count($strcountsyncarr);$ico++)
			{
				$arr_1 = explode(',',$strcountsyncarr[$ico]);
				$arrsynccount[$arr_1[0]] = $arr_1[1];
			}
			$count = $arrsynccount['Emb' . $i];
		}
		$cert_sync_size = $this->getRequest()->getParam('cert_sync_size');
		if(isset($cert_sync_size) && $cert_sync_size != '')
		{
			$arrsynccount = array();
			$strcountsync = $cert_sync_size;
			$strcountsyncarr = explode('|',$strcountsync);
			for($ico=0;$ico<count($strcountsyncarr);$ico++)
			{
				$arr_1 = explode(',',$strcountsyncarr[$ico]);
				$arrsynccount[ array_shift($arr_1)] = implode(', ',$arr_1);
			}
			$dim = $arrsynccount['Emb' . $i];
		}
		$cert_sync_weight = $this->getRequest()->getParam('cert_sync_weight');
		if(isset($cert_sync_weight) && $cert_sync_weight != '')
		{
			$arrsynccount = array();
			$strcountsync = $cert_sync_weight;
			$strcountsyncarr = explode('|',$strcountsync);
			for($ico=0;$ico<count($strcountsyncarr);$ico++)
			{
				$arr_1 = explode(',',$strcountsyncarr[$ico]);
				$arrsynccount[ array_shift($arr_1)] = implode(', ',$arr_1);
			}
			$car = $arrsynccount['Emb' . $i];
		}
		
		
		$str = "<div class='stone-heading-title'>" . $title . " Information:</div>
		<div class='gem-details-wrapper'>";
		if($noof!="")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>" . $noof . ":</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $count . "</a></td></tr></table></div>";
		}
		if($dim != "")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approximate Dimensions:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $dim . " mm</a></td></tr></table></div>";
		}
		if($car != "")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approximate Carat Weight:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $car . " cts</a></td></tr></table></div>";
		}
		if($col != "" and $qua == "")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Minimum Color:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $col . "</a></td></tr></table></div>";
		}
		if($cla != "" and $qua == "")
		{

			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Minimum Clarity:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $cla . "</a></td></tr></table></div>";
		}
		if($qua != "")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Quality Grade:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $qua . "</a></td></tr></table></div>";
		}
		if($emb != "")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Setting Type:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $emb . "</a></td></tr></table></div>";
		}
		$str = $str . "</div>";
		return $str;
	}
	
	public function strloose(){
		$product = Mage::getModel('catalog/product');
		$_product = $product->load($this->getRequest()->getParam('certificate_id'));
		$pro = $_product->getData();
		$str = "";
		if($_product->getGemstoneType())
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Gemstone Type:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('gemstone_type') . "</a></td></tr></table></div>";
		}
		if($_product->getGemstoneShape())
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Gemstone Shape:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('gemstone_shape') . "</a></td></tr></table></div>";
		}
		if($_product->getGemstoneCaratWeight())
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approximate Carat Total Weight:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getData('gemstone_carat_weight') . " cts</a></td></tr></table></div>";
		}
		if($_product->getGemstoneColor())
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Gemstone Color:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('gemstone_color') . "</a></td></tr></table></div>";
		}
		if($_product->getGemstoneClarity())
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Gemstone Clarity:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('gemstone_clarity') . "</a></td></tr></table></div>";
		}
		if($_product->getGemstoneDimension())
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approximate Dimensions:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getData('gemstone_dimension') . " mm</a></td></tr></table></div>";

		}
		if($_product->getGemstoneBrilliance())
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Gemstone Brilliance:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('gemstone_brilliance') . "</a></td></tr></table></div>";
		}
		if($_product->getGemstoneEnhancement())
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Gemstone Enhancement:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $_product->getAttributeText('gemstone_enhancement') . "</a></td></tr></table></div>";
		}
		return $str;
	}
	
	public function getimagesAction(){
		$sku = $this->getRequest()->getParam('sku');
		$metaltype = $this->getRequest()->getParam('metaltype');
		$grade = $this->getRequest()->getParam('grade');

		$id = $this->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product');
		$_product = $product->load($id);
		if($_product->getImagesku()!='')
		{
			$sku = $_product->getImagesku();
		}
		$count = 4; //Mage::getBlockSingleton('hprcv/hprcv')->getCountCustomImages($sku);
		$str = '0';
		$mainroot = $_SERVER['DOCUMENT_ROOT'];
		for($i=1;$i<=$count;$i++){
			if(!is_dir($mainroot.'/media/catalog/product/images/customj/'.$sku.'/angle_'.$i)){
				continue;	
			}
			$str = $str . "|" . Mage::getBlockSingleton('hprcv/hprcv')->returnimage($sku,$_product->getShortDescription(),$metaltype,$grade,'angle_' . $i,$_product->getAttributeText('de_stone_type'),$_product);
		}
		echo $str;
	}
	
	public function writeallcustomjimagesAction(){
		if(!(isset($_GET['accesspoint']) && $_GET['accesspoint']=="aniljain"))
		{
			echo "Permission Denied";exit;
		}
		$collection = Mage::getModel('catalog/product')->getCollection();
		$arr1 = array();
		$skufl = 0;
		if(isset($_GET['sku']) && $_GET['sku'])
		{
			$arr = explode(',',$_GET['sku']);
			for($i=0;$i<count($arr);$i++)
			{
				$arr1[] = array('attribute'=>'sku','eq'=>$arr[$i]);
				
			}
			$collection->addFieldToFilter($arr1);
			$skufl = 1;
		}
		else
		{
			$collection->addFieldToFilter(array(
        		array('attribute'=>'customj','eq'=>'1')
        	));
		}
		foreach ($collection as $product) {
			$_productobj = Mage::getModel('catalog/product');
			$_product = $_productobj->load($product->getId());
			
			if($_product->getCustomj()){
				echo "<div><h1>" . $_product->getSku() . "</h1></div><div>";
				Mage::getBlockSingleton('hprcv/hprcv')->writeAllImageCombinations($_product);
				echo "</div><div style='clear:both'></div><hr/>";
			}
		}
	}	
	
	
	/*
		This function is used to generate pdf certificate file for mothers sku only
	*/
	public function motherscertAction(){
		
		$hasDiamond 	= false;
		$lc 			= true;
		$stonePrefix 	= "LC";
		
		$productid 		= $this->getRequest()->getParam('productid');
		
		$product 		= Mage::getModel('catalog/product')->load($productid);
		
		$itemid 		= $this->getRequest()->getParam('itemid');
		$item 			= Mage::getModel('sales/order_item')->load($itemid);
		$itemOptions 	= $item->getProductOptions();
		$buyRequest 	= $item->getBuyRequest();
		
		$quoteid 		= $item->getQuoteItemId();
		
		if(!$quoteid){
			die("quote item id is not found.");
		}
		
		$sku 			= $product->getSku();
		$productOptions = array();
		foreach($itemOptions["options"] as $option){
			$productOptions[$option["label"]] = $option;
			$result[]	=	$option;		//	S:VA
		}
		
		//	S:VA
		foreach ($result as $_option){ 
			$myDetails .= '<div class="detail-row"><table class="inner-box-style"><tbody><tr><td class="left-title-text"><a style="background-color:#E5ECFC">'.$_option['label'].'</a></td><td align="right" class="left-title-text"><a style="background-color:#E5ECFC">'.$_option['value'].'</a></td></tr></tbody></table></div>';
		}
		//	E:VA
		
		if($productOptions["GenOrLC"]["value"] != "Lab Created"){
			$lc = false;
			$stonePrefix = "GN";
		}
		
		$totalStones = $productOptions["Number of stones"]["value"];
		$stones = array();
		
		for($i=1;$i<=$totalStones;$i++){
			$stone = $productOptions[$stonePrefix."-Stone".$i]["value"];
			$stones[] = $stone;
			if($stone == "Diamond"){
				$hasDiamond = true;
			}
		}
		
		$gnlcPrefix = "";
		if($lc){
			if($hasDiamond){
				$gnlcPrefix = "Lab Created or Simulated";
				$product->setQua("Lab Created or Simulated");
			}
			else{
				$gnlcPrefix = "Lab Created";
				$product->setQua("Lab Created");
			}
		}
		else{
			$product->setQua("AAA");
		}
		
		
		$product->setStones($stones);
		
		$metal = $productOptions["Metal Type"]["value"];
		$name = $product->getName()." With ".$gnlcPrefix." ".implode(", ", $stones)." in ".$metal;
		
		$strurl = Mage::getBaseUrl();
		$strurl = substr($strurl, 0 , strlen($strurl) - 1);
		//$image = $product['image'];
		//$image = $strurl."/media/catalog/product/images/mothers/cartproducts/" . $quoteid . ".png";
		//$image = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product/'.$product->getImage();
		$image = '/media/catalog/product/'.$product->getImage();
		// customised for absolute url
		if(substr($image,0,4)!="http")
		{
			$image = "http://www.angara.com/" . $image;
		}
		
		// customised for absolute url exit
		$im = @imagecreate(20, 250) or die("Cannot Initialize new GD image stream");
        $background_color = imagecolorallocate($im, 255, 255, 255);  // yellow
		$red = imagecolorallocate($im, 101, 27, 14);
		//$font = 'arial.ttf';
		//imagettftext($im, 0, 0, 0, 0, $red, $font, "");
		imagestringup($im, 5, 5, 240, "ANGARA REPORT " . $sku, $red);
        imagepng($im,"certificate/strip.png");
        imagedestroy($im);
		$myFile = "certificate/AngaraCert.html";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$str = "<body><table cellpadding='0' cellspacing='0' style='margin-left:-28px'><tr><td><img src='strip.png' /></td><td><div style='margin:0px;border:1px solid #ccc;width:750px;height:940px;color:#651B0E;padding-bottom:10px'><table cellpadding='0' cellspacing='0'><tr><td style='height:740px' valign='top'><table style='width:750px;margin-top:10px'><tr><td valign='top'><img src='https://www.angara.com/certificate/logo.gif' style='margin-left:5px;margin-top:5px' /></td><td style='width:245px'><div style='width:235px;border-left:1px solid #ccc;padding-left:5px'><b>Angara Inc.</b><br>550 South Hill Street, Suite 1015<br>Los Angeles, CA 90013<br>Phone 888 926 4272<br>Fax 201 503 8159</div></td></tr></table>";
		
		$str = $str . "<div style='height:80px'>&nbsp;</div>";
		
		$str = $str . "<div style='padding-left:5px'><b>CERTIFICATE OF AUTHENTICITY</b></div>
		<div style='margin-left:5px;margin-top:10px;width:715px;background-color:#E5ECFC;padding:20px 10px'>";
		
		$str = $str . '<style type="text/css">
		
											.rotate { 
						z-index:-1;
						 -webkit-transform: rotate(270deg);
						-moz-transform: rotate(270deg);
						-o-transform: rotate(270deg);
						-ms-transform: rotate(270deg);
						-sand-transform: rotate(270deg);
						position:absolute;
						text-align:center;	
						height:772px;
						width:996px;					
						left:-136px;
						top:114px;						
						color:#651B0E;						
						font-weight:bold;
					}
						
					sup {
						vertical-align:middle;
						padding-right:3px;
					}
						
					sub {
						vertical-align:middle;
						padding-left:3px;
					}
					
					.detail-row {
						background:url(/skin/frontend/angara/default/images/certificate-dotted-border.jpg) 0 15px repeat-x;						
						width:649px; 						
						margin:0px;
					}
					
					.inner-box-style {
						width:655px;
						margin-left:-3px;
					}
					
					.all-detail-box-wrapper {
						margin:5px 10px 20px 10px;						
						background-color:#E5ECFC;
						padding:5px 10px;						
					}
						
					.gem-details-wrapper {
						padding-left:34px;
					}
					
					.product-img-descp {
						position:absolute;
						bottom:0px;
						left:10px;						
						right:10px;						
					}
					
					.main-wrapper-box {
						margin:0px;
						border:1px solid #ccc;
						width:750px;
						color:#651B0E;
						padding-bottom:10px;
						position:relative;
						min-height:820px;
						padding-bottom:170px;
					}
					.left-title-text {
						padding-bottom:0px;
						font-size:13px;
					}
					
					.stone-heading-title {
						margin-top:15px;
						margin-bottom:5px;
						font-size:15px;
						font-weight:bold;
					}
					
					</style>';
		
		$str = $str . "<div style='width:695px;' align='center'><b>" . $name . "</b></div><div class='stone-heading-title'>Product Details:</div><div class='gem-details-wrapper'><div class='detail-row'><table class='inner-box-style'><tr><td><a style='background-color:#E5ECFC'>Angara Item #:</a></td><td align='right'><a style='background-color:#E5ECFC'>&nbsp;" . $sku . "</a></td></tr></table></div>";
		
		//$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td><a style='background-color:#E5ECFC'>Metal:</a></td><td align='right'><a style='background-color:#E5ECFC'>&nbsp;" . $metal . "</a></td></tr></table></div></div>";
		
		//$str = $str . $this->strMothersStones($product);
		$str = $str . $myDetails;		//	S:VA
		$desc = "Highly qualified and experienced Angara gemologists have tested and identified all stones featured
in the jewelry on Angara.com. State of the art equipment is used which accurately identifies all
characteristics of the stones.";
		
		
		$str = $str . "</div></td></tr><tr><td style='height:180px' valign='top'><div style='margin-top:0px;margin-left:10px'><table style='width:732px' cellpadding='0' cellspacing='0'><tr><td style='width:570px'><div><b>ANGARA REPORT " . $sku . "</b></div><div style='text-align:justify;padding-right:40px;font-size:15px;padding-top:5px'>" . $desc . "</div></td><td valign='top'><img src='" . $image . "' style='height:150px;width:150px;border:2px solid #ccc'  /> <div align='center' style='margin-top:5px'>" . $sku . "</div></td></tr></table></div></td></tr></table>";
		$str = $str . "</div></td></tr></table><style>body {font-family: 'Helvetica'} table{margin:0px;padding:0px}</style></body>";
		fwrite($fh, $str);
		fclose($fh);
		$str = "/certificate/dompdf.php?input_file=AngaraCert.html&sku=" . $sku;
		//$str = "../../../pdf/dompdf/dompdf.php?input_file=hprahipdf.html&paper=A4";
		header("Refresh:0;URL='" . $str . "'");	
		
	}
	
	public function strMothersStones($product){
		
		$dim = ($product->getData('gemstone_dimension')) ? $product->getData('gemstone_dimension') : '';

		$car = "";	// future use
		$qua = $product->getQua();
		$emb = "";
		$stones = $product->getStones();
		$shape = $product->getData('gemstone_shape')!=''?$product->getData('gemstone_shape'):'';
		
		$str = "<div class='stone-heading-title'>Gemstone Information:</div>
		<div class='gem-details-wrapper'>";
		
		foreach($stones as $stone){
			if($stone == "Diamond" && $qua == "Lab Created"){
				$qua = "Lab Created or Simulated";
			}
			$noof = 'Number of '.$shape.' '. $stone;
			if($noof!="")
			{
				$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>" . $noof . ":</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;1</a></td></tr></table></div>";
				
				
			}
		}
		
		if($dim != "")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approximate Dimensions:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $dim . " mm</a></td></tr></table></div>";
		}
		if($car != "")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Approximate Carat Total Weight:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $car . " cts</a></td></tr></table></div>";
		}
		if($qua != "")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Quality Grade:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $qua . "</a></td></tr></table></div>";
		}
		if($emb != "")
		{
			$str = $str . "<div class='detail-row'><table class='inner-box-style'><tr><td class='left-title-text'><a style='background-color:#E5ECFC'>Setting Type:</a></td><td align='right' class='left-title-text'><a style='background-color:#E5ECFC'>&nbsp;" . $emb . "</a></td></tr></table></div>";
		}
		$str = $str . "</div>";
		
		return $str;
	}	
} ?>