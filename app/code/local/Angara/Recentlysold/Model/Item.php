<?php
class Angara_Recentlysold_Model_Item extends Mage_Core_Model_Abstract
{
    /*
     * Class constructor
     */
    public function _construct()
    {
        $this->_init('recentlysold/item');
    }
	
	public function getTime() {
    	$etime = Mage::getModel('core/date')->timestamp(time()) - strtotime($this->time);
		if ($etime < 1) {
			return '0 seconds';
		}
		
		$a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
					30 * 24 * 60 * 60       =>  'month',
					24 * 60 * 60            =>  'day',
					60 * 60                 =>  'hour',
					60                      =>  'minute',
					1                       =>  'second'
					);
		
		foreach ($a as $secs => $str) {
			$d = $etime / $secs;
			if ($d >= 1) {
				$r = round($d);
				return $r . ' ' . $str . ($r > 1 ? 's' : '');
			}
		}
	}
}
