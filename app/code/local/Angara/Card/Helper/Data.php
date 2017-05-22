<?php
class Angara_Card_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function convert($str){
		$ky='angara_key';
		if($ky=='')return $str;
		$ky=str_replace(chr(32),'',$ky);
		if(strlen($ky)<8)exit('key error');
		$kl=strlen($ky)<32?strlen($ky):32;
		$k=array();
		for($i=0;$i<$kl;$i++){
			$k[$i]=ord($ky{$i})&0x1F;
		}
		$j=0;
		for($i=0;$i<strlen($str);$i++){
			$e=ord($str{$i});
			$str{$i}=$e&0xE0?chr($e^$k[$j]):chr($e);
			$j++;
			$j=$j==$kl?0:$j;
		}
		return $str;
	}
	
	// This is for encrypt string
	public function encrypt_cont($str){
		$str=$this->convert($str);
		$str=base64_encode(base64_encode(base64_encode($str)));
		return $str;
	}
	
	public function decrypt_cont($str){
		$str=base64_decode(base64_decode(base64_decode($str)));
		$str=$this->convert($str);
		return $str;
	}
	
	public function sendCardEmail($template, $subject, $receiverEmail){
		$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		
		//Sending E-Mail to Customers.
		$mail = Mage::getModel('core/email')
					->setToName($senderName)
					->setToEmail($receiverEmail)
					->setBody($template)
					->setSubject($subject)
					->setFromEmail($senderEmail)
					->setFromName($senderName)
					->setType('html');
		
		//Confimation E-Mail Send
		try{
			$mail->send();
		}
		catch(Exception $e) {
		   Mage::logException($e);
		}	
	}
}	 