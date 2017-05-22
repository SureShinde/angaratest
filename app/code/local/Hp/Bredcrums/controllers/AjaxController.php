<?php
class Hp_Bredcrums_AjaxController extends Mage_Core_Controller_Front_Action
{
	public function bredAction()
	{
		$cat = $_POST['category'];
		$_product = Mage::getModel('catalog/product')->load($_POST['product']);
		$categoryIds = $_product->getCategoryIds();
		foreach($categoryIds as $categoryId) {
			$category = Mage::getModel('catalog/category')->load($categoryId);
			$url = $category->getUrlPath();
			if(strpos($_POST['backurl'],"/" . $url) > -1)
			{
				$cat = $categoryId;
				break;
			}
		 }

		$crumbs  = Mage::helper('catalog')->getBreadcrumbPathHp($cat,$_POST['product']);
		$cnt = count($crumbs);
		$i = 0;
?>
	<div class="breadcrumbs">
        <ul>
            <?php foreach($crumbs as $_crumbName=>$_crumbInfo): $i=$i+1; ?>
                <li class="crumb<?php echo $i;?>">
                <?php if(isset($_crumbInfo['link'])):  ?>
                    <a href="<?php echo $_crumbInfo['link'] ?>" title="<?php if(isset($_crumbInfo['title'])){echo $_crumbInfo['title'];} ?>"><?php echo $_crumbInfo['label'] ?></a>
                <?php elseif($i == $cnt): ?>
                    <a><b><?php echo $_crumbInfo['label']; ?></b></a><li class="crumbblank"  style="list-style:none;"><a></a></li>
                <?php else: ?>
                    <?php echo $_crumbInfo['label'] ?>
                <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php
		
	}
}
?>