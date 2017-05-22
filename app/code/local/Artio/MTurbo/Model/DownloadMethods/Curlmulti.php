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
 * @copyright   Copyright (c) 2013 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Download method for parallel downloading by MultiCURL.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 * @since		1.2.7
 */
class Artio_MTurbo_Model_DownloadMethods_Curlmulti extends Artio_MTurbo_Model_DownloadMethods_Abstract
{


	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::getName()
	 */
	public function getName() {
		return Mage::helper('mturbo')->__('Using cURL (multi requests)');
	}


	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::isReady()
	 */
	public function isReady() {
		if (in_array('curl', get_loaded_extensions())) {
			$this->errorMsg = '';
			return true;
		} else {
			$this->errorMsg = Mage::helper('mturbo')->__('CURL is not installed');
			return false;
		}
	}


	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::allowParallelDownloading()
	 */
	public function allowParallelDownloading() {
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::downloadPages()
	 */
	public function downloadPages($urls) {

		$result = array();

		$chunks = array_chunk($urls, $this->batchSize);

		foreach ($chunks as $chunk)
			$result = array_merge($result, $this->_downloadPages($chunk));

		return $result;
	}


	/**
	 * Download HTML of pages specified by $urls and return them.
	 *
	 * Method uses cURL extension (multi request).
	 *
	 * @param string $url
	 * @return string contents of downloaded page
	 */
	protected function _downloadPages($urls) {

		// filter empty urls
		$urls = array_filter($urls);

		// if it is not ready return empty
		// content for each url
		if (!$this->isReady())
			return array_flip($urls);

		// if there is no url then there is
		// not content
		if(count($urls) <= 0)
        	return array();

    	// set default options
        $options = array(
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
        );

        $handles = array();

    	// initialize culr for each url
	    foreach($urls as $url)
	    {
	        $ch{$url} = curl_init();
	        $options[CURLOPT_URL] = $url;
	        curl_setopt_array($ch{$url}, $options);
	        $handles[$url] = $ch{$url};
	    }

	    // initialize multi curl
    	$mh = curl_multi_init();

    	// add each handle to mutli curl
    	foreach($handles as $handle)
        	curl_multi_add_handle($mh, $handle);

    	$running_handles = null;

    	//execute the handles
    	do {
        	$status_cme = curl_multi_exec($mh, $running_handles);
    	} while ($status_cme == CURLM_CALL_MULTI_PERFORM);

    	while ($running_handles && $status_cme == CURLM_OK) {

        	if (curl_multi_select($mh) != -1) {
            	do {
                	$status_cme = curl_multi_exec($mh, $running_handles);
            	} while ($status_cme == CURLM_CALL_MULTI_PERFORM);
        	}

    	}

    	$res = array();

    	foreach($urls as $url)
    	{
        	$error  = curl_error($handles[$url]);

        	if (!empty($error)) {
            	$res[$url] = ''; // empty content
        	} else {
            	$res[$url] = curl_multi_getcontent($handles[$url]); // html content
        	}

        	// close current handler
        	curl_multi_remove_handle($mh, $handles[$url] );
    	}

    	curl_multi_close($mh);

    	// return responses
    	return $res;

	}

}