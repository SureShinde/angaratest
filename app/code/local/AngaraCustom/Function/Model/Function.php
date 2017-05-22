<?php

class AngaraCustom_Function_Model_Function extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('function/cms');
    }
	
	/**
     * Magento cron to update static blog with the recent angara blog posts
     */
    public function cron()
    {
		//	Showing recent 2 blog posts
		$xml				= new SimpleXMLElement(file_get_contents('http://blog.angara.com/feed/'));
		//$xml 				= new SimpleXmlElement($returnedContent, LIBXML_NOCDATA);
		if(isset($xml->channel)){
			$cnt = count($xml->channel->item);
			for($i=0; $i < 2; $i++){
				$url 	= $xml->channel->item[$i]->link;
				$title 	= $xml->channel->item[$i]->title;
				$desc 	= $xml->channel->item[$i]->description;
				$content.=	'<p class="low-margin-top low-margin-bottom"><a href="'.$url.'"><b>'.$title.'</b></a></p>';
				$content.=	'<ul class="footer-links"><li>"'.substr($desc,0,75).'"...<a class="text-underline" target="_blank" href="'.$url.'">read more</a></li></ul>';
			}
			//echo $content;die;
			$content	=	Mage::helper('function')->RssProperFormat($content);		//	Reformat the string
			$identifier = 'recent_blog_post';
			Mage::getModel('cms/block')
				->load($identifier, 'identifier')
				->setData('content', $content)
				->save();
			//	Generating cron log
			//Mage::log('content is ->'.$content, null, 'vaseem_cron.log');	// Creating a log file at var\log\vaseem_cron.log
			//	Send email once the static block html gets updated
			Mage::helper('function')->sendEmail('Recent Blog Post Update for Footer', $content);
		} else{
			Mage::helper('function')->sendEmail('Error - Recent Blog Post Update for Footer', 'Please check cron function.');
		}
    }
}
