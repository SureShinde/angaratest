<?php   
class Angara_BuildYourOwn_Block_Header extends Mage_Core_Block_Template
{	
	public function getJewelryModel(){
		return Mage::registry('byoJewelryModel');
	}
	
	public function getDiamondBlock($orderNumber){
		$jewelryModel = $this->getJewelryModel();
		return '<div class="title-block col-sm-4 col-xs-4'.((!$jewelryModel->hasDiamondSelected() && ($orderNumber == 1 || ($jewelryModel->hasSettingSelected() && $orderNumber != 1)))?' active':'').'">
					<div class="box col-sm-2 num">'.$orderNumber.'</div>
					<div class="box col-sm-5 text">
					'.(
						$jewelryModel->hasDiamondSelected()?'DIAMOND<BR>
						<span class="text-light small">Selected</span>
						<a href="'.$jewelryModel->getRemoveDiamondUrl().'" class="text-light hover-link text-underline small">Change</a>'
						:
						'CHOOSE A<BR>
						DIAMOND	'
					).'
					</div>
					<div class="box col-sm-4 hidden-xs icon"></div>
					<div class="box col-sm-1 arrow"></div>
				</div>';
	}
	
	public function getDiamondPairBlock($orderNumber){
		$jewelryModel = $this->getJewelryModel();
		return '<div class="title-block col-sm-4 col-xs-4'.(($jewelryModel->hasDiamondSelected() && !$jewelryModel->hasDiamondPairSelected())?' active':'').'">
						<div class="box col-sm-2 num">'.$orderNumber.'</div>
						<div class="box col-sm-5 text">
						'.(
							$jewelryModel->hasDiamondPairSelected()?'DIAMOND PAIR<BR>
							<span class="text-light small">Selected</span>
							<a href="'.$jewelryModel->getRemoveDiamondPairUrl().'" class="text-light hover-link text-underline small">Change</a>'
							:
							'CHOOSE A<BR>
							DIAMOND	PAIR'
						).'
						</div>
						<div class="box col-sm-4 hidden-xs icon"></div>
						<div class="box col-sm-1 arrow"></div>
					</div>';
	}
	
	public function getSettingBlock($orderNumber){
		$jewelryModel = $this->getJewelryModel();
		return '<div class="title-block col-sm-4 col-xs-4'.((!$jewelryModel->hasSettingSelected() && ($orderNumber == 1 || ($jewelryModel->hasDiamondSelected() && ($orderNumber == 2 || ($orderNumber == 3 && $jewelryModel->hasDiamondSelected() && $jewelryModel->hasDiamondPairSelected())))))?' active':'').'">
						<div class="box col-sm-2 num"><span>'.$orderNumber.'</span></div>
						<div class="box col-sm-5 text">
						'.(
							$jewelryModel->hasSettingSelected()?'SETTING<BR>
							<span class="text-light small">Selected</span>
							<a href="'.$jewelryModel->getRemoveSettingUrl().'" class="text-light hover-link text-underline small">Change</a>'
							:
							'CHOOSE A<BR>
							SETTING'
						).'
						</div>
						<div class="box col-sm-4 hidden-xs icon"></div>
						<div class="box col-sm-1 arrow"></div>
					</div>';
	}
	
	public function getFinalBlock($orderNumber, $jewelryType){
		$jewelryModel = $this->getJewelryModel();
		return '<div class="title-block col-sm-4 col-xs-4'.(($jewelryModel->hasDiamondSelected() && $jewelryModel->hasSettingSelected() && ($orderNumber == 3 || $jewelryModel->hasDiamondPairSelected()))?' active':'').'">
				<div class="box col-sm-2 num"><span>'.$orderNumber.'</span></div>
				<div class="box col-sm-5 text">COMPLETE<BR>
					YOUR '.$jewelryType.'</div>
				<div class="box col-sm-4 hidden-xs icon"></div>
				<div class="box col-sm-1 arrow"></div>
			</div>
		</div>';
	}
}







