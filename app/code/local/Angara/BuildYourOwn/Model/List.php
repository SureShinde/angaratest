<?php
class Angara_BuildYourOwn_Model_List extends Mage_Core_Model_Abstract
{	
	private $_appliedFilters;
	
	public function setFilters($filters)
    {
		$this->_appliedFilters = $filters;
		return $this;
	}
	
	public function getFilters(){
		return $this->_appliedFilters;
	}

    public function getDiamonds($pageNumber = 1){
		return $this->_requestDiamondFeed("list", $pageNumber);
	}
	
	public function getDiamondDetails($diamondId){
		return $this->_requestDiamondFeed("info", $diamondId);
	}
	
	// $feedType = list|info
	// in case of "list" $feedParam = $pageNumber
	// in case of "info" $feedParam = $diamondId
	private function _requestDiamondFeed($feedType, $feedParam){
		//1 - Authenticate with TechNet. The authentication ticket will be stored in $auth_ticket. Note this MUST be HTTPS.
		if($feedType == 'list'){
			$auth_url = "https://technet.rapaport.com/HTTP/JSON/RetailFeed/GetDiamonds.aspx";
		}else if($feedType == 'info'){
			$auth_url = "https://technet.rapaport.com/HTTP/JSON/RetailFeed/GetSingleDiamond.aspx";
		}
		
		$post_string = '
			{
				"request": {
					"header": {
						"username": "59920", 
						"password": "AAng_10036"
					}, 
					"body": {
						 '.$this->_createSearchParams($feedType, $feedParam).'
					}
				}
			}
		';
		
		$response = $this->_requestCurlData($auth_url, $post_string);
		
		
		$processedResponse = $this->_processResponse($response);
		if($processedResponse['error']!=''){
			Mage::log('Error',null,'../log1/byo_error.log',true);
			// throw error $processedResponse['errorMessage']
			return false;
		}
		else{
			Mage::log('else 1',null,'../log1/byo_right.log',true);
			$result = $processedResponse['data'];
			Mage::log('else 2:'.print_r($result,true),null,'../log1/byo_right.log',true);
			if(isset($result['diamond']['total_sales_price_in_currency']) && $result['diamond']['total_sales_price_in_currency'] > 0) {
				Mage::log('else 3:',null,'../log1/byo_right.log',true);
				try{
					$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
					if(!isset($result['diamond']['currency_code'])){ 
						$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
					} else {
						$baseCurrencyCode = trim($result['diamond']['currency_code']);
					}
					$result['diamond']['total_sales_price_in_currency'] = Mage::helper('directory')->currencyConvert($result['diamond']['total_sales_price_in_currency'], $baseCurrencyCode, $currentCurrencyCode); 
					Mage::log('else 4:'.print_r($result,true),null,'../log1/byo_right.log',true);
				} catch (Exception $e) {
					Mage::log('else exp:',null,'../log1/byo_right.log',true);
				}
			}
			return $result;
		}
	}
	
	private function _requestCurlData($url, $postString){
		#@todo if key md5($post_string) is available in cache than get it (implement cache for a day) and create a mechanism to clear the old cache [IMP]
		$key = md5($postString.date('Ymd'));
		if ($response = Mage::helper('helloworld')->getDataFromCache($key)) {
			return $response;
		} 
		else {
			
			//create HTTP POST request with curl:
			$request = curl_init($url); // initiate curl object
			curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
			curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
			curl_setopt($request, CURLOPT_POSTFIELDS, $postString); // use HTTP POST to send form data
			curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
			$response = curl_exec($request); // execute curl post and store results in $auth_ticket
			curl_close ($request); // close curl object
			
			Mage::helper('helloworld')->setKey($key)->setData($response)->saveDataInCache();
		}
		
		return $response;
	}
	
	private function _processResponse($response){
		$processedResponse = array('error' => '', 'data' => array());
		if($response){
			$response = json_decode($response, true);
			if($response['response']['header']['error_code']){
				$processedResponse['error'] = 'Error['.$response['response']['header']['error_code'].']: '.$response['response']['header']['error_message'];
			}
			else{
				$processedResponse['data'] = $response['response']['body'];
			}
		}
		else{
			$processedResponse['error'] = 'Request was not completed.';
		}
		return $processedResponse;
	}
	
	private function _getFilterData($param, $defaultValue){
		$value = $this->_appliedFilters[$param];
		if(empty($value)){
			return $defaultValue;
		}
		return $value;
	}	
	
	// $feedType = list|info
	// in case of "list" $feedParam = $pageNumber
	// in case of "info" $feedParam = $diamondId
	private function _createSearchParams($feedType, $feedParam){
		switch($feedType){
			case "list":
				return '"search_type": "White", 
						"shapes": ['.$this->_getFilterData('shapes', '"Round", "Pear", "Princess", "Marquise", "Oval", "Radiant", "Emerald", "Heart", "Cushion", "Asscher"').'], 
						"size_from": "'.$this->_getFilterData('sizeFrom', 0.2).'", 
						"size_to": "'.$this->_getFilterData('sizeTo', 10.00).'", 
						"color_from": "'.$this->_getFilterData('colorFrom', "D").'", 
						"color_to": "'.$this->_getFilterData('colorTo', "J").'", 
						"clarity_from": "'.$this->_getFilterData('clarityFrom', "IF").'", 
						"clarity_to": "'.$this->_getFilterData('clarityTo', "SI2").'", 
						"cut_from": "'.$this->_getFilterData('cutFrom', "Good").'", 
						"cut_to": "'.$this->_getFilterData('cutTo', "Excellent").'",
						"depth_percent_from": "'.$this->_getFilterData('depthPercentFrom', 0).'",
						"depth_percent_to": "'.$this->_getFilterData('depthPercentTo', 100).'",
						"table_percent_from": "'.$this->_getFilterData('tablePercentFrom', 0).'",
						"table_percent_to": "'.$this->_getFilterData('tablePercentTo', 100).'",
						"polish_from": "'.$this->_getFilterData('polishFrom', "Good").'",
						"polish_to": "'.$this->_getFilterData('polishTo', "Excellent").'",
						"symmetry_from": "'.$this->_getFilterData('symmetryFrom', "Good").'",
						"symmetry_to": "'.$this->_getFilterData('symmetryTo', "Excellent").'",
						"price_total_from": '.$this->_getFilterData('priceFrom', 100).', 
						"price_total_to": '.$this->_getFilterData('priceTo', 1000000).',
						"page_number": '.$feedParam.',
						"page_size": 50, 
						"sort_by": "'.$this->_getFilterData('sortBy', "Shape").'", 
						"sort_direction": "'.$this->_getFilterData('sortDir', "Asc").'"';
			case "info":
				return '"diamond_id": '.$feedParam;
		}
	}
}