<?php

class Atwix_Recentlyviewed_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function panelstateAction() {
        $rw_help = Mage::helper('recentlyviewed/data');
        $state = isset($_POST['state'])? $_POST['state'] : 'show';
        $rw_help->savePanelState($state);
    }
	public function deleterecentviewedAction() {
		$rw_help = Mage::helper('recentlyviewed/data');
        $id = $_POST['id'];
        $rw_help->removeViewedItem($id);
	}
	public function gethtmlAction(){
		$urlrcv = Mage::getUrl('*/*');
		$indx = strpos($urlrcv, "/checkout");
		$rw_items= array();
		if($indx == "")
		{
			$module_status = Mage::getStoreConfig('recentlyviewed/general/enablemodule');
			$rw_items = array();
			if ($module_status) {
				$cookieval = Mage::getModel('core/cookie');				
				$rw_items_cookie = $cookieval->get('RecView');
				$rw_items = explode('|',$rw_items_cookie);				
			}
			//$rw_items = $this->CollectRecentlyViewed();
		}
		$cookieval = Mage::getModel('core/cookie');				
		$state_cookie = $cookieval->get('RVIpanelstate');
		$state = explode('|',$state_cookie);		
        if (empty($state))
            $state = 'show';
		$panel_state = $state;
		if (is_array($rw_items) && count($rw_items) > 0) :
			$prodmodel = Mage::getModel('catalog/product');
			$imagemodel = Mage::helper('catalog/image')->init($prodmodel, 'small_image');
			?>
		
			<script type="text/javascript">
			  var atwix_base_url = '<?php echo Mage::getBaseUrl();?>';
			</script>
		
			<div <?php echo (($panel_state == 'show')? '' : 'style="display: none;"');?> class="rw-wrapper" id="rvi_panel">
			
			<div class="rvi_btn btn_hide"></div>
			
			<div class="rvi-head"></div>
				<table border="0" width="100%">
					<tr>
						<td width="100%" valign="middle">
								<div class="rw-block" id="rcvrwblock">
								
								<?php foreach ($rw_items as $rw_item) :
									  $prodmodel = Mage::getModel('catalog/product'); 
									  $prodmodel->load($rw_item);
									  $imagemodel->init($prodmodel, 'small_image')->resize(70); ?>
									  <a id='rec_prod_<?php echo $rw_item;?>' href="<?php echo $prodmodel->getProductUrl();?>"><img align="middle" class="rw-thumb" src="<?php echo $imagemodel?>" prod_id="<?php echo $rw_item;?>" alt="" /></a> <a class="resVclose" id='1_rec_prod_<?php echo $rw_item;?>' href="javascript:void(0)" onclick="deleteRecentViewed(<?php echo $rw_item;?>)">X</a>
									  <?php $imagemodel->init($prodmodel, 'small_image')->resize(170); ?>
									  <span id='2_rec_prod_<?php echo $rw_item;?>' class="rw-preview-wrapper" prod_id="<?php echo $rw_item;?>">
										  <img class="rw-preview" src="<?php echo $imagemodel;?>" alt="" />
									  </span>
								<?php endforeach; ?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="rw-wrapper-hidden" <?php echo (($panel_state != 'show')? '' : 'style="display: none;"');?> id = "rvi_panel2">
				<div class="rvi_btn btn_show"></div>
				<div class="rvi-head"></div>
			</div>
		
		<?php endif;
		if($_POST['id']!='')
		{
			Mage::getBlockSingleton('recentlyviewed/collect')->addItem($_POST['id']);
		}
	}
	public function gethtmlrecentviewedAction(){
		echo $this->getLayout()->createBlock('recentlyviewed/view')->setTemplate('recentlyviewed/view.phtml')->toHTML();
	}
}
?>
