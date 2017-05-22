<?php
class Angara_UtilityBackend_Helper_Shortner extends Mage_Core_Helper_Abstract{
    protected $apiURL = 'https://www.googleapis.com/urlshortener/v1/url';
     
    public function __construct(){
        $this->GoogleURLAPI();
    }
	
    // Constructor
    protected function GoogleURLAPI() {
        // Keep the API Url
        $key = 'AIzaSyC44dBA-QCwB0jKFWCARyDCM4As8FgVkrI';
        $apiURL = $this->apiURL;
        $this->apiURL = $apiURL.'?key='.$key;
    }
     
    // Shorten a URL
    public function shorten($url) {
        // Send information along
        $response = $this->send($url);//print_r($response);
		
        // Return the result
        return isset($response['id']) ? $response['id'] : false;
    }
     
    // Expand a URL
    public function expand($url) {
        // Send information along
        $response = $this->send($url,false);
        // Return the result
        return isset($response['longUrl']) ? $response['longUrl'] : false;
    }
     
    // Send information to Google
    public function send($url,$shorten = true) {
        // Create cURL
        $curlObj = curl_init();
		
        // If we're shortening a URL...
        if($shorten) {			
            curl_setopt($curlObj, CURLOPT_URL, $this->apiURL);
			curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curlObj, CURLOPT_HEADER, 0);
			curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
			curl_setopt($curlObj, CURLOPT_POST, 1);
			curl_setopt($curlObj, CURLOPT_POSTFIELDS, json_encode(array("longUrl"=>$url)));
        }
        else {
            curl_setopt($curlObj,CURLOPT_URL,$this->apiURL.'&shortUrl='.$url);
        }
		
        curl_setopt($curlObj,CURLOPT_RETURNTRANSFER,1);
         
        try{
			// Execute the post
			$result = json_decode(curl_exec($curlObj), true);
			
			// Close the connection
			curl_close($curlObj);
        }
		catch(Exception $e){
            echo $e->getMessage();
        }
		
        // Return the result
        //return $result->id;
		return $result;
    }
}?>