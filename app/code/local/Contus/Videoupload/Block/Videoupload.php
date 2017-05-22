<?php
/*
 * Contus Support Pvt Ltd.
 * created by Vasanth on nov 04 2010.
 * vasanth@contus.in
 * In this page used to fetch video collections and Product collection.
 */
?>
<?php
class Contus_Videoupload_Block_Videoupload extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    /*Get Video Collection*/
    public function getVideoupload()
    {
        $product = $this->getProduct();
        $id = $this->getRequest()->getParam('id');
        $_videoCollection =Mage::getModel('catalog/product')->load($id);
        return $_videoCollection;

    }
    /*Get Pdoduct Collection*/
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {

            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }
    /*Get Action url for this module*/
    public function getFormActionUrl()
    {
        return $this->getUrl('videoupload', array('_secure' => true));
    }
	public function getThumbnail()
    {
		$product = $this->getProduct();
        $id = $this->getRequest()->getParam('id');
        $_videoCollection =Mage::getModel('catalog/product')->load($id);
		$video=explode("|",$_videoCollection['video']);
		$nameofvideos=explode(",",$video['0']);
		$cnt=count($nameofvideos);
	   $thumbPath = Mage::getBaseURL('media');
	  
       
        // where you'll save the image
       // where ffmpeg is located
		$ffmpeg = '/usr/bin/ffmpeg';
		
		for($i=0;$i<$cnt;$i++)
		{
			//video dir
			$video = $thumbPath.'productVideos/video/'.$nameofvideos[$i];
			
			//where to save the image
			$image = $thumbPath.'productVideos/thumb/'.substr($nameofvideos[$i],0,$nameofvideos[$i].length-4).".jpg";
			echo $video."-> ".$image."<br/>";
			//time to take screenshot at
			$interval = 2;
			
			//screenshot size
			$size = '640x480';
			
			//ffmpeg command
		//	$cmd = "$ffmpeg -i $video -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $image 2>&1";
		//	$return = `$cmd`;
	//		echo $return;
		}
die;  }


}