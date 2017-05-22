<?php
class hp_hprcv_QualitycompareController extends Mage_Core_Controller_Front_Action
{
	
	public function getAction(){
		?>
		<div class="loading-indicator hide text-center"><div class="modal-backdrop fade in"></div>
	<div class="loader fa fa-spinner fa-spin fa-5x max-margin-top"></div>
    </div>
		<div class="container max-margin-top max-padding-top text-center fontsize-type2">
        	<div class="showcase-bg-white block-inline-display padding-type-15 max-margin-top">
			<div class="clearfix"><i class="fa fa-times close" data-dismiss="modal"></i></div>
            
		<?php
		Mage::getModel('hprcv/hprcv')->getqualitygardecomparisonhtml();
		?>
		</div>
        </div>
		<?php
	}
	public function getweightchartAction(){
		?>
		<div class="loading-indicator hide text-center"><div class="modal-backdrop fade in"></div>
	<div class="loader fa fa-spinner fa-spin fa-5x max-margin-top"></div></div>
		<div class="container max-margin-top max-padding-top text-center fontsize-type2">
        	<div class="showcase-bg-white block-inline-display padding-type-15 max-margin-top">
			<div class="clearfix"><i class="fa fa-times close" data-dismiss="modal"></i></div>
		<?php
		Mage::getModel('hprcv/hprcv')->getweightcharthtml();
		?>
		</div>
        </div>
		<?php
	}
}
?>