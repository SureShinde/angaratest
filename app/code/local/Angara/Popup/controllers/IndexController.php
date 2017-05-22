<?php
class Angara_Popup_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * If true, authentication in this controller (popup) could be skipped
     *
     * @var bool
     */
	 
    protected $_skipAuthentication = true;	
	
	public function indexAction(){	
		$this->loadLayout();    
  		$this->renderLayout();
    }
	
	public function openringsizerAction(){ 
		$this->loadLayout();
			echo $this->getLayout()->getBlock('ringsizerform')->toHtml();
	}
	
	public function requestringsizerAction(){
		$params = $this->getRequest()->getParams();
		// send mail for request ring sizer
		$model = Mage::getModel('popup/ringsizer')->requestRingsizer($params);		
	}
	
	public function productmediaAction(){
			
		$productId  = (int) $this->getRequest()->getParam('id');
		
		$model = Mage::getModel('catalog/product'); //getting product model 
		$_product = $model->load($productId); //getting product
		
		$cids = $_product->getCategoryIds();
		$pinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$_product->getUrlPath();
		$productTinyURLPinit = rawurlencode($pinUrl.'?utm_source=Pinit&utm_medium=Website&utm_campaign=SOCIAL'); 
		
		$productImages = $_product->getMediaGalleryImages();
		
		if(count($productImages)>0){
			foreach ($productImages as $_image)	{ 
				$image_arr[] = $_image->getUrl();
			}
		} 
		
		/*if($_product->getCustomj()=='1'){
			$image_arr = Mage::getBlockSingleton('hprcv/hprcv')->fetchAllCustomJImageCombinations($_product);
		}
		else{	
			// build-your-own
			if(in_array(277,$cids)){
				$image_arr[] = Mage::helper('catalog/image')->init($_product, 'image'); 
			}
			elseif(in_array(99,$cids) || in_array(271,$cids) || in_array(272,$cids) || in_array(273,$cids) || in_array(274,$cids) || in_array(275,$cids) || in_array(276,$cids)){
				// ADVRP
				if(in_array(229,$cids)){
					
					$image_arr = array();
							 
					if($_product->getWhiteGoodImage()!= 'no_selection' && $_product->getWhiteGoodImage()){
						$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getWhiteGoodImage();
					}
					if($_product->getWhiteBetterImage()!= 'no_selection' && $_product->getWhiteBetterImage()){
						$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getWhiteBetterImage();
					}
					if($_product->getWhiteBestImage()!= 'no_selection' && $_product->getWhiteBestImage()){
						$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getWhiteBestImage();
					}
					if($_product->getWhiteHeirloomImage()!= 'no_selection' && $_product->getWhiteHeirloomImage()){
						$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getWhiteHeirloomImage();
					}
					if($_product->getYellowGoodImage()!= 'no_selection' && $_product->getYellowGoodImage()){
						$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getYellowGoodImage();
					}
					if($_product->getYellowBetterImage()!= 'no_selection' && $_product->getYellowBetterImage()){
						$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getYellowBetterImage();
					}
					if($_product->getYellowBestImage()!= 'no_selection' && $_product->getYellowBestImage()){
						$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getYellowBestImage();
					}
					if($_product->getYellowHeirloomImage()!= 'no_selection' && $_product->getYellowHeirloomImage()){
						$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getYellowHeirloomImage();
					}
					
					if(count($image_arr)==0){
						$image_arr[] = Mage::helper('catalog/image')->init($_product, 'image');
						
						if(count($image_arr)==0){
							$image_arr[] = Mage::helper('catalog/image')->init($_product, 'image');
							
							$tmp_main_img_path = 'media/catalog/product'.$_product->getImage();
							
							$tmp_main_img_dir = dirname($tmp_main_img_path);
							
							if($tmp_main_img_path!=''){
								$main_img_name = basename($tmp_main_img_path);
								list($imname,$ext) = explode('.',$main_img_name);
								
								for($x=1;$x<=6;$x++){			
									$new_img_name = $imname.'_'.$x.'.'.$ext;
									if (in_array($new_img_name, $moreimg_file_arr)) {
										continue;
									}
									if(@file_exists(Mage::getBaseDir().'/'.$tmp_main_img_dir.'/'.$new_img_name)){
										$image_arr[].= 'http://'.$_SERVER['HTTP_HOST'].'/'.$tmp_main_img_dir.'/'.$new_img_name;
									}
								}
							}															
						}
					}
				}
				else{ 
					// mothers
					$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/images/mothers/'.$_product->getSku().'.png'; 			
				}
			}
			elseif(in_array(279,$cids) || in_array(94,$cids)){ 
				$image_arr[] = Mage::helper('catalog/image')->init($_product, 'image'); // default	
			}
			else{
				// ADVRP
				$image_arr = array();	
				
				if($_product->getWhiteGoodImage()!= 'no_selection' && $_product->getWhiteGoodImage()){
					$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getWhiteGoodImage();
				}
				if($_product->getWhiteBetterImage()!= 'no_selection' && $_product->getWhiteBetterImage()){
					$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getWhiteBetterImage();
				}
				if($_product->getWhiteBestImage()!= 'no_selection' && $_product->getWhiteBestImage()){
					$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getWhiteBestImage();
				}
				if($_product->getWhiteHeirloomImage()!= 'no_selection' && $_product->getWhiteHeirloomImage()){
					$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getWhiteHeirloomImage();
				}
				if($_product->getYellowGoodImage()!= 'no_selection' && $_product->getYellowGoodImage()){
					$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getYellowGoodImage();
				}
				if($_product->getYellowBetterImage()!= 'no_selection' && $_product->getYellowBetterImage()){
					$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getYellowBetterImage();
				}
				if($_product->getYellowBestImage()!= 'no_selection' && $_product->getYellowBestImage()){
					$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getYellowBestImage();
				}
				if($_product->getYellowHeirloomImage()!= 'no_selection' && $_product->getYellowHeirloomImage()){
					$image_arr[] = 'http://'.$_SERVER['HTTP_HOST'].'/media/catalog/product/'.$_product->getYellowHeirloomImage();
				}
				
				if(count($image_arr)==0){
					$image_arr[] = Mage::helper('catalog/image')->init($_product, 'image');
					$tmp_main_img_path = 'media/catalog/product'.$_product->getImage();
					$tmp_main_img_dir = dirname($tmp_main_img_path);
					
					if($tmp_main_img_path!=''){
						$main_img_name = basename($tmp_main_img_path);
						list($imname,$ext) = explode('.',$main_img_name);
						
						for($x=1;$x<=6;$x++){			
							$new_img_name = $imname.'_'.$x.'.'.$ext;
							if (in_array($new_img_name, $moreimg_file_arr)) {
								continue;
							}
							if(@file_exists(Mage::getBaseDir().'/'.$tmp_main_img_dir.'/'.$new_img_name)){
								$image_arr[].= 'http://'.$_SERVER['HTTP_HOST'].'/'.$tmp_main_img_dir.'/'.$new_img_name;
							}
						}
					}														
				}				
			}	
		}*/?>
        <div class="page-title title-buttons">
            <h1><?php echo $this->__('Product Gallery') ?></h1>    
        </div>
        <div>
            <ul style="list-style: none;">
            <?php 
            foreach($image_arr as $imgurl){
                if($_product->getCustomj()=='1'){	
                    if (stripos($imgurl, '-SL-') !== false || stripos($imgurl, '-PT-') !== false || stripos($imgurl, '-A-') !== false){
                        continue;
                    }
                }?>
                <li style="float: left; padding: 5px; list-style: none;display: inline-block;">			
                    <span><img src="<?php echo $imgurl; ?>" width="150"></span>
                    <span class="communiteebtnpin">
                        <a title="Pin this piece" alt="Pin this piece"  href="http://pinterest.com/pin/create/button/?url=<?php echo $productTinyURLPinit; ?>&media=<?php echo $imgurl; ?>&description=<?php echo trim($_product->getShortDescription()).': Angara'; ?>" class="pin-it-button" count-layout="none" ><img border="0" src="http://assets.pinterest.com/images/PinExt.png" /></a>		
                    </span>
                </li> 
            <?php
            }?>
            </ul>
            <script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
        </div>
		<style>
			.rw-wrapper{
				display:none !important;
			}
			.rw-wrapper-hidden{
				display:none !important;
			}
		</style>
<?php   		
    }
	
	public function openreturnshippinglabelAction(){
		$this->loadLayout();
			echo $this->getLayout()->getBlock('returnshippinglabelform')->toHtml();
	}
	
	public function returnShippingLabelAction(){
		$this->loadLayout();    
  		$this->renderLayout();
	}
	
	public function requestreturnshippinglabelAction(){
		$params = $this->getRequest()->getParams();
		// send mail for request return shipping label
		$model = Mage::getModel('popup/returnshippinglabel')->requestReturnshippinglabel($params);
	}
	public function countryfetchAction(){
		//echo 'false';exit;
		$ipinfoKey = 'a0b66c9e4f681ea3b8c945975e4b85b43f818ea2c61b36e4c3836cb8f6a4a100';
		/* get ip*/
		$ip = Mage::helper('core/http')->getRemoteAddr();
		if($ip!='UNKNOWN') {
			  $ch = curl_init();
			  $timeout = 5;
			  //$ip='175.45.65.94';  // canada - 206.53.55.211 , Australia - 175.45.65.94, UK - 80.193.214.235
			  $url="http://api.ipinfodb.com/v3/ip-city/?key=". $ipinfoKey."&ip=". $ip . "&format=xml";
			  curl_setopt($ch,CURLOPT_URL,$url);
			  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			  $data = curl_exec($ch);
			  $data = $data . "\n";
				curl_close($ch);
			 try
			 {
				$xml = new SimpleXMLElement($data); 	
				
				if($xml->statusCode=='OK' AND $xml->countryCode!='-') {
					$countryCode = (string)$xml->countryCode;
					//echo  "hhhh===".$countryCode;exit;
					if(strtoupper($countryCode)=='GB' || strtoupper($countryCode)=='AU' || strtoupper($countryCode)=='CA' || strtoupper($countryCode)=='UK'){
						if(strtoupper($countryCode)=='UK' || strtoupper($countryCode)=='GB'){
							$countryCode = 'GB';
						}
						//$countryCode ='';
						if($countryCode!=''){
							Mage::register('countrycode', $countryCode);
							//Mage::getModel('countrymapping/country')->getCountryParamName($countryCode);
							echo $this->getLayout()->createBlock('popup/popup')->setTemplate('popup/international_shipping_popup.phtml')->toHTML();
						}
						else{
							echo 'false';
						}
					}
					else{
						echo 'false';
					}
				} else {
					echo 'false';
				}		
			 }catch(Exception $e)
			 {
				echo 'false';
			 }

		} else {
			echo 'false';
		}
	}
	
	public function showPhoneIconPopupAction(){
		$this->loadLayout();
		echo $this->getLayout()->createBlock('popup/popup')->setTemplate('popup/phoneicon.phtml')->toHTML();
	}
	
	public function generateCaptchaAction(){
		echo $this->getLayout()->createBlock('popup/popup')->setTemplate('popup/captcha.phtml')->toHtml();
	}
}
