/*ideal payment compatibility start*/

1. Open the file located at app/code/core/Mage/Ideal/Model/Basic.php

2. Search for code that reads "$this->appendHash($fields)"  

paste the below code above the line 

 /* code to integrate runa salelift promotions with ideal begin */
        $idealCart = new Varien_Object();
        $idealCart->setFields($fields);
        $idealCart->setSalesEntity($order);
        $idealCart->setTotalItemIncrement($i);
        mage::dispatchEvent('ideal_basic_collect_totals_after', array('ideal_cart' => $idealCart));
        $fields = $idealCart->getFields();
/* code to integrate runa salelift promotions with end */

/*ideal payment compatibility end*/