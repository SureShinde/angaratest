<?php
class Angara_Popup_Model_Returnshippinglabel extends Mage_Core_Model_Abstract
{
	public function requestReturnshippinglabel($request) {
		if( Mage::getSingleton('core/session')->getRandomNumber() != strtolower($request["code"])){
			echo '<span style="color:#f00;font:12px/18px Arial, Helvetica, sans-serif;">The recaptcha wasn\'t entered correctly. Please try again.</span>';	
		}else{
			if(trim($request["name"])!='' && trim($request["email"])!='' && trim($request["orderno"])!=''){
			$html='<table width="600" border="1" cellspacing="0" cellpadding="0" style="font:12px Arial, Helvetica, sans-serif;">
			<tr>
				<td colspan="4" height="30" style="font:bold 13px/18px Arial, Helvetica, sans-serif; padding:5px 10px;">
					<span style="font:bold 16px/30px Arial, Helvetica, sans-serif;">Hi Admin,</span><br /><br />
						This is a request mail from <span style="color:#B72629">'.ucwords(trim($request["name"])).'</span> about the return shipping label.<br />
						His/Her contact Details are as follows:
				</td>
			</tr>';
			
			$html.='<tr>
						<td height="30" valign="middle" style="font:bold 14px Arial, Helvetica, sans-serif; padding-left:10px;">Email</td>
						<td height="30" valign="middle" style="font:bold 14px Arial, Helvetica, sans-serif; padding-left:10px;">Order No.</td>
						<td height="30" valign="middle" style="font:bold 14px Arial, Helvetica, sans-serif; padding-left:10px;">Reason To Return</td>';
						
			if($request["check"] && $request["check"]=='1'){
				$html.='<td height="30" valign="middle" style="font:bold 14px Arial, Helvetica, sans-serif; padding-left:10px;">Requested For</td>';
			}
			$html.='</tr>';
					
			$html.='
			 <tr>
				<td height="30" valign="top" style="font:12px/18px Arial, Helvetica, sans-serif; padding-left:10px;padding-bottom:10px;"> '.trim($request["email"]).'</td>
				<td height="30" valign="top" style="font:12px/18px Arial, Helvetica, sans-serif; padding-left:10px;padding-bottom:10px;">'.trim($request["orderno"]).'</td>
				<td height="30" valign="top" style="font:12px/18px Arial, Helvetica, sans-serif; padding-left:10px;color:#000;padding-bottom:10px;">'.trim($request["reason"]).'</td>';
				
			if($request["check"] && $request["check"]=='1'){
				$html.='<td height="30" valign="top" style="font:12px/20px Arial, Helvetica, sans-serif; padding-left:10px;padding-bottom:10px;">
							<b>Email me a return shipping label.</b>
						</td>';
			}
			   
			$html.='</tr>';		
			$html.='</table>';
			
			$mail = Mage::getModel('core/email');
			$mail->setToName('Angara Sales');
			$mail->setToEmail('customer.support@angara.com');
			//$mail->setToEmail('techsupport@angara.com');
			//$mail->setToEmail('customer.service@angara.com;allam.ramesh@angara.com;nancy.farinas@angara.com');
			$mail->setBody($html);
			$mail->setSubject('Return Shipping Label Request - '.date('F jS, Y'));
			$mail->setFromEmail('techsupport@angara.com');
			$mail->setFromName("Angara INC");
			$mail->setType('html');// can use Html or text as Mail format
			//echo $html;
			try {
				$mail->send();
				echo '<span style="color:#070;font:12px/18px Arial, Helvetica, sans-serif;">Thank you for your request. A return shipping label will be emailed to you within 48 hours.</span>';
			}
			catch (Exception $e) {
				echo '<span style="color:#f00;font:12px/18px Arial, Helvetica, sans-serif;">Error processing your request this time.</span>';
			}
			}else{
				echo '<span style="color:#f00;font:12px/18px Arial, Helvetica, sans-serif;">Required fields are empty.</span>';	
			}
	    }
	}	
}
?>