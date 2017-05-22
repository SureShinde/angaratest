<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//set_time_limit(0);
ini_set('memory_limit','1024M');                // increasing memory limit
ini_set('max_execution_time', 3000);    // 300 seconds = 5 minutes


if( isset($_POST['error_code']) ){
        try {
                $error_code     =       $_POST['error_code'];
                updateData($error_code);
        }catch(Exception $e) {
                echo $e->getMessage();
        }
        echo '<br><a href="">Do you wanna do it once again</a>';
}else{ ?>

<!DOCTYPE html>
<html lang="en">
    <div class="row">
        <form role="form" method="post" enctype="multipart/form-data" name="frm" action="">
          <div class="form-group">
            Error Code  <input name="error_code" type="text" value="108155-342-1-4156">
            <button name="submit" type="submit" class="btn btn-default">Submit</button>
          </div>
        </form>
    </div>
</html>
<?php }

function updateData( $error_code ) {
        if($error_code){
                $data   =       explode('-',$error_code);
                //var_dump($data);
                $productId                      =       $data[0];
                $attributeID            =       $data[1];
                $storeId                        =       $data[2];
                $attributeOptionID      =       $data[3];

                $attributeName          =       getAttributeName($attributeID);
                $currentAttributeOptionValue    =       getAttributeValues($attributeID, $attributeOptionID);
                echo '<br>attributeName->'.$attributeName;
                echo '<br>currentAttributeOptionValue->'.$currentAttributeOptionValue;
                //      Saving product again to resolve the foreign key issue in db
                try {
                        $action                 =       Mage::getModel('catalog/resource_product_action');
                        $action->updateAttributes(array($productId), array(
                                $attributeName => $currentAttributeOptionValue
                        ), $storeId);
                        echo 'Product is saved again. Please run indexing again using ssh';
                } catch (Exception $e) {
                        echo '<br>'.$e->getMessage();
                }
        }else{
                echo '<br>Please provide a error code.';
        }
}


function getAttributeName($attributeID){
        $resource       =       Mage::getSingleton('core/resource');
        $read           =       $resource->getConnection('core_read');
        $table1         =       $resource->getTableName('eav/attribute');

        $query          =       "select attribute_code from $table1 where attribute_id='$attributeID'";
        //echo $query;
        $result         =       $read->fetchOne($query);
        return $result;
}

function getAttributeValues($attributeId, $attributeOptionID){
        $resource       =       Mage::getSingleton('core/resource');
        $read           =       $resource->getConnection('core_read');
        $table2         =       $resource->getTableName('eav/attribute_option_value');

        $query          =       "SELECT value from $table2 where option_id ='4157'";
        //echo $query;
        $result         =       $read->fetchOne($query);
        return $result;
}
?>
