<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @copyright   Copyright (c) 2010 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Download method using sockets.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 */
class Artio_MTurbo_Model_DownloadMethods_Socket extends Artio_MTurbo_Model_DownloadMethods_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::getName()
	 */
	public function getName() {
		return Mage::helper('mturbo')->__('Create connection over sockets');
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::isReady()
	 */
	public function isReady() {
		$this->errorMsg = '';
		return true;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::allowParallelDownloading()
	 */
	public function allowParallelDownloading() {
		return false;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::downloadPages()
	 */
	public function downloadPages($urls) {
	
		$res = array();
	
		foreach ($urls as $url)
			$res[$url] = $this->_downloadPage($url);
	
		return $res;
	}

	
	/**
	 * Download HTML of page specified by $url and return it.
	 * 
	 * Method intialize socket and send to server GET request to
	 * page. Since 1.2.7 there is supported also "chunked-encode" output.
	 * 
	 * If $url is empty method returns empty string.
	 *
	 * @param string $url
	 * @return string contents of downloaded page
	 */
	protected function _downloadPage($url) {

		if (!$url)
			return '';
		
		/* output socket methods */
		$resultNumber=0;
	    $resultTest='';

	  	$request = preg_replace('/http(s){0,1}:\/\/[^\/]*/', '', $url);
						
		/* get host from url */
		$matches = array();
		preg_match('@^(?:http://)?([^/]+)@i', $url, $matches);
		$host = $matches[1];
	
		/* open socket */
	    $fp = @fsockopen($host, 80, $resultNumber, $errorMessage);
	    if (!$fp) {
	    	$this->errorMsg = $errorMessage;
	    } else {
	    	
	    	$header = '';
	    	$body	= '';

			/* build request */
	    	$out  = "GET $request HTTP/1.1\r\n";
	    	$out .= "Host: $host\r\n";
	   	 	$out .= "Connection: Close\r\n";
	   	 	$out .= "\r\n";

			/* post request */
	   	 	fwrite($fp, $out);

			/* get response */
	   	 	$wasHeader = false;
	    	while (!feof($fp)) {
	    		        					
				$line = fgets($fp,128);
				
            	if (!$wasHeader && $line=="\r\n")
                	$wasHeader = true;
            	
				if ($wasHeader)
					$body .= $line;
				else
					$header .= $line;
				
	    	}
	    	
	    	fclose($fp);
	    	
	    	$header = $this->_parseHTTPHeader($header);
	    	
	    	if (isset($header['Transfer-Encoding']) && $header['Transfer-Encoding'] == 'chunked')
	    		$body = $this->_decodeChunked($body);
	    			
	    	return (string) $body;		    			
	    }
		
	}
	
	
	/**
	 * Parse HTTP header.
	 * 
	 * http://stackoverflow.com/questions/10793017/how-to-easily-decode-http-chunked-encoded-string-when-making-raw-http-request
	 *
	 * @param string $str
	 * @return array
	 */
	protected function _parseHTTPHeader($str)
	{
		$lines = explode("\r\n", $str);
		$head  = array(array_shift($lines));
		foreach ($lines as $line) {

			$line = trim($line);
			
			if (!$line)
				continue;
			
			list($key, $val) = explode(':', $line, 2);
			if ($key == 'Set-Cookie') {
				$head['Set-Cookie'][] = trim($val);
			} else {
				$head[$key] = trim($val);
			}
		}
		return $head;
	}
	
	
	/**
	 * Decode chunked string.
	 * 
	 * http://stackoverflow.com/questions/10793017/how-to-easily-decode-http-chunked-encoded-string-when-making-raw-http-request
	 * 
	 * @param unknown_type $str
	 * @return string
	 */
	protected function _decodeChunked($str) {

		for ($res = ''; !empty($str); $str = trim($str)) {
			$pos = strpos($str, "\r\n");
			$len = hexdec(substr($str, 0, $pos));
			$res.= substr($str, $pos + 2, $len);
			$str = substr($str, $pos + 2 + $len);
		}
		return $res;
	}
	
	
}