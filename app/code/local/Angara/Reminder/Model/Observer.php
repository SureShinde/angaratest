<?php
class Angara_Reminder_Model_Observer
{ 
    public function sendReminderEmail()
    {
        //this collection get all users which have reminder on today
        $collection = Mage::getModel("reminder/reminder")->getCollection();
        
		$collection->getSelect()->joinLeft('customer_entity', 'customer_entity.entity_id=main_table.customer_id');
		$collection->getSelect()->Where("datediff(main_table.occasion_date, CURDATE())=15 OR datediff(main_table.occasion_date,CURDATE())=7");
		//$collection->addFieldToFilter('main_table.occasion_date', array('like' => '%'.date("m").'-'.date("d")));
		$collection->getSelect()->group('main_table.reminder_id');
		
        $items = $collection->getItems();
		foreach($items as $item)
        {			
			$customer_data = Mage::getModel('customer/customer')->load($item->getCustomerId());
			$msgBody = $this->getReminderEmailBodyHtml($customer_data,$item);
			$subject = 'Angara.com: Reminder for your '.$item->getRelationship().' \'s '.$item->getOccasion();
			//echo $msgBody;exit;			
			$mail = Mage::getModel('core/email');
			$email = $item->getEmail();//'anil.jain@angara.com';
			$mail->setToName($sendToName);
			$mail->setToEmail($email);
			$mail->setBody($msgBody);
			$mail->setSubject($subject);
			$mail->setFromEmail("customer.service@angara.com");
			$mail->setFromName("Angara.com");
			$mail->setType('html');
			try {
				$mail->send();
				Mage::log('Reminder Email Sent successfully to :'.$item->getEmail(), null, 'reminder_email.log');
			}
			catch (Exception $e) {
				Mage::logException($e);
			}			
        }		
        return $this;
    }
	
	public function getReminderEmailBodyHtml($customer_data=NULL, $item=NULL){
		$ddate = $item->getOccasionDate();
		list($yy,$mm,$dd) = explode('-',$ddate);
		$OccasionDate = date("l, F jS, Y", mktime(0, 0, 0, $mm, $dd, $yy));

		$reminder_body = '<body style="margin:0;padding:0; width:100% !important;font-size:11px; font-family: Arial, Helvetica, sans-serif">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		  <tr>
			<td align="center"><table width="600" cellpadding="0" cellspacing="0" border="0">
				<tr>
				  <td><table cellpadding="0" cellspacing="0" width="600">
					  <tr>
						<td align="left" valign="middle" width="289"><a href="http://www.angara.com?utm_source=Mx&utm_medium=Email&utm_campaign=INVOICE&utm_term=[EMAIL_ID]" target="_blank"><img src="http://www.angara.com/skin/frontend/angara/default/images/logo.gif" border="0" align="" alt="Angara.com - The Gemstone Destination", title="Angara.com - The Gemstone Destination"></a></td>
						<td width="200" align="right"><br>
						  <br>
						  <p style="margin:0;font-size:14px"><a href="https://server.iad.liveperson.net/hc/609151/?cmd=file&file=visitorWantsToChat&site=609151&byhref=1&imageUrl=https://server.iad.liveperson.nethttp://www.angara.comhttp://www.angara.com/store/images/lp/?track=head" style="color:#d0471a;font-family:Arial,sans-serif;" title="Live Chat" target="_blank"><b>Live Chat</b></a> | <a href="http://www.angara.com/contact-us.html?utm_source=Mx&utm_medium=Email&utm_campaign=INVOICE&utm_term=[EMAIL_ID]" style="color:#d0471a;font-family:Arial,sans-serif;" title="1-888-926-4272" target="_blank"><b>1-888-926-4272</b></a> &nbsp;<a href="http://www.facebook.com/angaradotcom" style="outline:none;border:none"><img src="http://www.angara.com/skin/frontend/angara/default/images/facebook.png" title="Angara on Facebook" alt="Angara on Facebook" border="0" ></a>&nbsp; <a href="http://www.twitter.com/Angara" title="Follow us on Twiiter" style="outline:none;border:none"><img src="http://www.angara.com/skin/frontend/angara/default/images/twitter.png" title="Follow us on Twiiter" alt="Follow us on Twiiter" border="0"></a><br>
							<br>
						</td>
					  </tr>
					  <tr>
						<td colspan="2" style="background:#E8E8E8;height:26px"><table cellpadding="0" cellspacing="0" width="600" style="font-size:12px">
							<tr>
							  <td align="center"><a href="http://www.angara.com/jewelry.html?utm_source=Mx&utm_medium=Email&utm_campaign=INVOICE&utm_term=[EMAIL_ID]" style="color:#555; text-decoration: none;" title="Gemstones" target="_blank">Gemstones</a></td>
							  <td>|</td>
							  <td align="center"><a href="http://www.angara.com/rings.html?utm_source=Mx&utm_medium=Email&utm_campaign=INVOICE&utm_term=[EMAIL_ID]" style="color:#555; text-decoration: none;" title="Rings" target="_blank">Rings</a></td>
							  <td>|</td>
							  <td align="center"><a href="http://www.angara.com/engagement-rings.html?utm_source=Mx&utm_medium=Email&utm_campaign=INVOICE&utm_term=[EMAIL_ID]" style="color:#555; text-decoration: none;" title="Engagement" target="_blank">Engagement</a></td>
							  <td>|</td>
							  <td align="center"><a href="http://www.angara.com/pendants.html?utm_source=Mx&utm_medium=Email&utm_campaign=INVOICE&utm_term=[EMAIL_ID]" style="color:#555; text-decoration: none;" title="Pendants" target="_blank">Pendants</a></td>
							  <td>|</td>
							  <td align="center"><a href="http://www.angara.com/earrings.html?utm_source=Mx&utm_medium=Email&utm_campaign=INVOICE&utm_term=[EMAIL_ID]" style="color:#555; text-decoration: none;" title="Earrings" target="_blank">Earrings</a></td>
							  <td>|</td>
							  <td align="center"><a href="http://www.angara.com/jewelry-gifts.html?utm_source=Mx&utm_medium=Email&utm_campaign=INVOICE&utm_term=[EMAIL_ID]" style="color:#555; text-decoration: none;" title="Gifts &amp; More" target="_blank">Gifts &amp; More</a></td>
							  <td>|</td>
							  <td align="center"><a href="http://www.angara.com/jewelry-guide.html?utm_source=Mx&utm_medium=Email&utm_campaign=INVOICE&utm_term=[EMAIL_ID]" style="color:#555; text-decoration: none;" title="Jewelry Guide" target="_blank">Jewelry Guide</a></td>
							</tr>
						  </table></td>
					  </tr>
					  <tr>
						<td colspan="2"><table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="600" style="border:1px solid #E0E0E0;">
							<!-- [ middle starts here] -->
							<tr>
							  	<td valign="top">
									<h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;">
							  		Hello '.ucwords($customer_data->getFirstname().' '.$customer_data->getLastname()).',</h1>
									<p style="font-size:12px; line-height:16px; margin:0 0 10px 0;"> 
									Your '.$item->getRelationship().' \'s '.$item->getOccasion().' is coming on '.$OccasionDate.'
									
									<!--reminder message goes here-->
									
									</p>
								</td>
							</tr>							
						  </table></td>
					  </tr>
					  <tr>
						<td colspan="2" style="border:1px solid #d3d3d3;padding:10px 0;border-top:none" align="center"><table cellpadding="0" cellspacing="0" width="580" style="font-size:13px;" align="center">
							<tr>
							  <td align="left"><span style="line-height:115%; font-family:Arial,sans-serif; font-size:8.0pt; color:#868686; ">&copy; 2012 Angara INC. All rights reserved.  Office: 550 South Hill St, Suite 1625, Los Angeles, CA 90013</span></td>
							  <td width="42">&nbsp;</td>
							</tr>
						  </table></td>
					  </tr>
					</table></td>
				</tr>
			  </table></td>
		  </tr>
		</table>
		</body>';
		return $reminder_body;
	}
}
?>