<?php
class Studs_Diamondstud_IndexController extends Mage_Core_Controller_Front_Action
{

	public $skuarray =  Array();
	public $a_metal ;
	public $a_shape;
	public $a_category; 
    public $a_settingstyle ;
    public $a_emb_color ;
    public $a_carat_weight;
    public $a_price; 
    public $a_emb_clarity; 
	public $w_root;
	

    public function indexAction()
    {
    	$this->loadLayout();     
		$this->renderLayout();
    }
	
	
	
	
	
	public function getratelistAction()
	{
		
		  global 	
	 	 $a_metal;
	    global 	
	     $a_shape ;
	    global 
         $a_category; 
		global 
     	 $a_settingstyle ;
		global 
     	 $a_emb_color ;
		global 
     	 $a_carat_weight;
		global 
     	 $a_price;
		global  
     	 $a_emb_clarity; 
		 global
		 $w_root ;
		 $w_root ="http://www.angara.com";
		 
		$attribute_model        = Mage::getModel('eav/entity_attribute'); 
		
		$a_metal         = $attribute_model->getIdByCode('catalog_product', 'metal');
		$a_shape         = $attribute_model->getIdByCode('catalog_product', 'de_stone_shape');
		$a_emb_color         = $attribute_model->getIdByCode('catalog_product', 'emb_color1');
		$a_carat_weight         = $attribute_model->getIdByCode('catalog_product', 'emb_carat_weight1');
		$a_price         = $attribute_model->getIdByCode('catalog_product', 'price');
		$a_emb_clarity         = $attribute_model->getIdByCode('catalog_product', 'emb_clarity1');
        $a_settingstyle         = $attribute_model->getIdByCode('catalog_product', 'settingstyle');
		 $a_category='279';
		
		
		
	   $style=$_GET['style'];
	   $metal=$_GET['metal'];
	   if( $_GET['shape']=='r')
		{
			$shape=19; 
		}
		elseif( $_GET['shape']=='p')
		{
			$shape='24';
		}
		
		
		// columns Configuration Load color code clarity code and thr respective images 
		
		$clarityarr = $this->getclarity_color($metal,$shape,$style);
		$count = count($clarityarr);
		
		
		
		
		//  load Clarity Attributes code 
		$attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
        $attribute_code         = $attribute_model->getIdByCode('catalog_product', 'emb_clarity1');
        $attribute              = $attribute_model->load($attribute_code);
        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);
        	
        foreach($options as $option)
             {
				 $value = $option['value'];
				  $label= $option['label'];
				  for($i=0;$i<$count;$i++)
				  {
					if($value==$clarityarr[$i][0])
					{
						$clarityarr[$i][1]= $label;
					}  
				  }
             }

        
	    //  load Color Attributes code 
		$attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
        $attribute_code         = $attribute_model->getIdByCode('catalog_product', 'emb_color1');
        $attribute              = $attribute_model->load($attribute_code);
        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);
        	
        foreach($options as $option)
             {
				 $value = $option['value'];
				  $label= $option['label'];
				  for($i=0;$i<$count;$i++)
				  {
					if($value==$clarityarr[$i][2])
					{
						$clarityarr[$i][3]= $label;
					}  
				  }
             }

		
		// Clarity Images for color and clarity combination
		
		for($ii=0;$ii<$count;$ii++)
		 {
		 
		     if( $_GET['shape']=='r')
		     {
			    if($clarityarr[$ii][1]=="I1" && $clarityarr[$ii][3]=="G-I")
			       $clarityarr[$ii][4]="rdsi3-i1.jpg";
			    else
			    if($clarityarr[$ii][1]=="SI1-SI2" && $clarityarr[$ii][3]=="G-I")
			       $clarityarr[$ii][4]="rdsi2.jpg";
				else
				if($clarityarr[$ii][1]=="SI1" && $clarityarr[$ii][3]=="G-H")
			    	$clarityarr[$ii][4]="rdsi1.jpg";
				else
				if($clarityarr[$ii][1]=="VS1-VS2" && $clarityarr[$ii][3]=="G-H")
			   		$clarityarr[$ii][4]="rdvs2.jpg";
				else	
				if($clarityarr[$ii][1]=="I2-I3" && $clarityarr[$ii][3]=="J-M")
			    	$clarityarr[$ii][4]="rdi2.jpg";
				else
				if($clarityarr[$ii][1]=="I1" && $clarityarr[$ii][3]=="H-I")
			    	$clarityarr[$ii][4]="rdsi3-i1.jpg";
				else	
				if($clarityarr[$ii][1]=="SI1-SI2" && $clarityarr[$ii][3]=="H")
			   	 	$clarityarr[$ii][4]="rdsi2.jpg";	
				else
					 $clarityarr[$ii][4]="";	
		     }
			 elseif( $_GET['shape']=='p')
		     {
			    if($clarityarr[$ii][1]=="I2-I3" && $clarityarr[$ii][3]=="J-M")
			    	$clarityarr[$ii][4]="pi2-i3.jpg";
				else	
				if($clarityarr[$ii][1]=="I1" && $clarityarr[$ii][3]=="H-I")
			    	$clarityarr[$ii][4]="psi-3-i1.jpg";
				else	
				if($clarityarr[$ii][1]=="SI1-SI2" && $clarityarr[$ii][3]=="H")
			   	 	$clarityarr[$ii][4]="psi1.jpg";
				else	
				if($clarityarr[$ii][1]=="VS1-VS2" && $clarityarr[$ii][3]=="G-H")
			    	$clarityarr[$ii][4]="pvs2.jpg";
				else
					 $clarityarr[$ii][4]="";
			}
			
			}
		$ratearray=null;
		
		for($ii=0;$ii<$count;$ii++)
		{
			 
			$ratearray= $this->getpricebyclaritycolor($metal,$shape,$style,$clarityarr[$ii][0],$clarityarr[$ii][2],$ratearray,$ii);
		}
		 
		 //echo count($ratearray);
		 
		// print_r($ratearray);
		
		//  End of column configuration
		
		
		// Rows Configuration on basis or carats of studs
		
		 //echo count($ratearray);
		global $skuarray;
		//  End of column configuration
		// Rows Configuration on basis or carats of studs
		$carat;
		// carate wehgt
		$carat[0][0]=0.25;
		// title
		$carat[0][1]="1/4 Carat tw";
		//image path for round
		$carat[0][2]="r01";
		//image path for princess
		$carat[0][3]="01";
		//alt text
		$carat[0][4]="1/4 Carat";
		// visibility by default hidden 0 for hidden 1 for visible
		$carat[0][5]="0";
		
		//
		$carat[1][0]=0.33;
		$carat[1][1]="1/3 Carat tw";
		$carat[1][2]="r02";
		$carat[1][3]="02";
		$carat[1][4]="1/3 Carat";
		$carat[1][5]="0";
		
		$carat[2][0]=0.4;
		$carat[2][1]="3/8 Carat tw";
		$carat[2][2]="r03";
		$carat[2][3]="03";
		$carat[2][4]="3/8 Carat";
		$carat[2][5]="0";
		
		$carat[3][0]=0.5;
		$carat[3][1]="1/2 Carat tw";
		$carat[3][2]="r04";
		$carat[3][3]="04";
		$carat[3][4]="1/2 Carat";
		$carat[3][5]="0";
		
		$carat[4][0]=0.75;
		$carat[4][1]="3/4 Carat tw";
		$carat[4][2]="r05";
		$carat[4][3]="05";
		$carat[4][4]="3/4 Carat";
		$carat[4][5]="0";
		
		$carat[5][0]=1;
		$carat[5][1]="1 Carat tw";
		$carat[5][2]="r06";
		$carat[5][3]="06";
		$carat[5][4]="1 Carat";
		$carat[5][5]="0";
		
		$carat[6][0]=1.25;
		$carat[6][1]="1 1/4 Carat tw";
		$carat[6][2]="r07";
		$carat[6][3]="07";
		$carat[6][4]="1 1/4 Carat";
		$carat[6][5]="0";
		
		$carat[7][0]=1.5;
		$carat[7][1]="1 1/2 Carat tw";
		$carat[7][2]="r08";
		$carat[7][3]="08";
		$carat[7][4]="1 1/2 Carat";
		$carat[7][5]="0";
		
		$carat[8][0]=2;
		$carat[8][1]="2 Carat tw";
		$carat[8][2]="r09";
		$carat[8][3]="09";;
	    $carat[8][4]="2 Carat";
		$carat[8][5]="0";
		
		$carat = $this->getcaratevisibility($metal,$shape,$style,$carat);
		$rowcount = count($carat);
	    
	    echo "<div class='diamonclarity'><div class='sizeguide'>
		<a id='various2' href='/skin/frontend/angara/default/staticdata/sizer.html' onclick='javascript:void(0);' class='iframe cboxElement'>Diamond Studs Size Guide</a>
		<img
		 src='/skin/frontend/angara/default/images/diamondstuds/sizeguideicon.jpg' alt='Size Guide' style='padding:15px 0 0 10px;' />
		 </div>  ";     
         $evnodd=0;
		 //print_r($clarityarr);
		
		// Creating Header Coloumns 
		 for($ii=0;$ii<$count;$ii++)
		 {
			  $class=null;
			   if(($ii+1)==$count)
			      $class = 'diamondrate';
			    else
			      $class = 'diamondratelast';
			  
		         echo "<div class='$class'>
		   		<div class='clarity-img'>
		   		<img src='/skin/frontend/angara/default/images/diamondstuds/".$clarityarr[$ii][4]."' /></div>
           		<div class='clarity-txt'>".
		   		$clarityarr[$ii][1]." Clarity
		   		<br />".
		    	$clarityarr[$ii][3]." Color
		   		</div></div>";
			 
		 }
         
	      echo "<div class='clr'></div>       
         </div>";
		 
		 //end Headr columns
          $class="oddrow";
		 
	     for($i=0;$i<$rowcount;$i++)
	     {
	       if($carat[$i][5] == "1")
		   {
			
		    	 if($evnodd%2==0)
				 {
	           		$class="oddrow";
					if($_GET['shape']=='r')
			          $img =    $carat[$i][2]."D.jpg";
			        else 
			          $img =    $carat[$i][3]."D.jpg";
				 }
	        	 else
				 {
	           		$class="evenrow";
					if($_GET['shape']=='r')
			          $img =    $carat[$i][2]."L.jpg";
			        else 
			          $img =    $carat[$i][3]."L.jpg";
				 }
				 $evnodd++;
			    echo "<div class='$class'>
                <div class='diamondsize'><img src='/skin/frontend/angara/default/images/diamondstuds/".$img."' 
			    alt='".$carat[$i][4]."' /></div> <div class='diamondweight'>".$carat[$i][1]."</div>";
			
                for($j=0;$j<$count;$j++)
			    {
					global $skuarray;
					$sku=$skuarray[$i][$j];
					$url="JavaScript:void(0);";
					if($sku)
					{
					$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
                    $url =$product->getProductUrl();
					}
					
					
			     echo "<div class='diamondrate'><a href='".$url."' ><img
				 src='/skin/frontend/angara/default/images/diamondstuds/pricearrow.png' />";   
				   echo "$".round($ratearray[$i][$j]);
				 
		          echo "</a></div>";
			   }
			
			
            echo "<div class='clr'></div>       
            </div>";
	
	      }
		 }
	
}
	
	public function getclarity_color($metal,$shape,$setting)
	{
		global 	
	 	 $a_metal;
	    global 	
	     $a_shape ;
	    global 
         $a_category; 
		global 
     	 $a_settingstyle ;
		global 
     	 $a_emb_color ;
		global 
     	 $a_carat_weight;
		global 
     	 $a_price;
		global  
     	 $a_emb_clarity;
		
		$claritycolor=null;
		$query="SELECT  distinct `_table_emb_color1`.`value` AS `emb_color1`,`_table_emb_clarity1`.`value` AS `emb_clarity1` FROM 
		`catalog_product_entity` AS `e` 
		 
		 INNER JOIN `catalog_category_product_index` AS `cat_index` ON cat_index.product_id=e.entity_id
		 AND cat_index.store_id='1' AND cat_index.category_id='$a_category' 
		 
		 INNER JOIN `catalog_product_entity_int` AS `_table_metal` ON
		 (_table_metal.entity_id = e.entity_id) AND (_table_metal.attribute_id='$a_metal') AND (_table_metal.store_id=0) 
		 
		 INNER JOIN `catalog_product_entity_int` AS `_table_de_stone_shape` ON (_table_de_stone_shape.entity_id = e.entity_id) AND
	    (_table_de_stone_shape.attribute_id='$a_shape') AND (_table_de_stone_shape.store_id=0) 
		
		 INNER JOIN 
		`catalog_product_entity_int` 
	     AS `_table_settingstyle` ON (_table_settingstyle.entity_id = e.entity_id)
		 AND (_table_settingstyle.attribute_id='$a_settingstyle') 
		 AND (_table_settingstyle.store_id=0)
		 
		 INNER JOIN `catalog_product_entity_int` AS `_table_emb_color1` ON
	    (_table_emb_color1.entity_id = e.entity_id) AND (_table_emb_color1.attribute_id='$a_emb_color') AND
		 (_table_emb_color1.store_id=0)
	    
		 
		 INNER JOIN `catalog_product_entity_varchar` AS `_table_emb_carat_weight1` ON (_table_emb_carat_weight1.entity_id =   e.entity_id) AND (_table_emb_carat_weight1.attribute_id='$a_carat_weight') AND (_table_emb_carat_weight1.store_id=0)
		 
	    
		 INNER JOIN `catalog_product_entity_decimal` AS `_table_price` ON (_table_price.entity_id = e.entity_id) AND
	     (_table_price.attribute_id='$a_price') AND (_table_price.store_id=0) 
		 
		  INNER JOIN `catalog_product_entity_int` AS
	    `_table_emb_clarity1` ON (_table_emb_clarity1.entity_id = e.entity_id) AND (_table_emb_clarity1.attribute_id='$a_emb_clarity') 
	 	 AND (_table_emb_clarity1.store_id=0)
		
		 WHERE (_table_metal.value = '$metal') AND (_table_de_stone_shape.value = '$shape') AND 
				 (_table_settingstyle.value like '$setting') ";
		
				
	//echo $query;
	   $db = Mage::getSingleton('core/resource')->getConnection('core_write');
       $result = $db->query($query);
       $i=0;
       while($rows = $result->fetch(PDO::FETCH_ASSOC))
       {
		   $claritycolor[$i][0]=$rows['emb_clarity1'];
		    $claritycolor[$i][1]="";
		   $claritycolor[$i][2]=$rows['emb_color1'];
		    $claritycolor[$i][3]="";
		   $i++;
	   }	
	   return $claritycolor;
		
	}
	
	
	public function getcaratevisibility($metal,$shape,$setting,$carat)
	{
		global 	
	 	 $a_metal;
	    global 	
	     $a_shape ;
	    global 
         $a_category; 
		global 
     	 $a_settingstyle ;
		global 
     	 $a_emb_color ;
		global 
     	 $a_carat_weight;
		global 
     	 $a_price;
		global  
     	 $a_emb_clarity;
		
		$claritycolor=null;
		$query="SELECT   DISTINCT `_table_emb_carat_weight1`.`value` AS `total_carat_weight`  FROM 
		`catalog_product_entity` AS `e` INNER JOIN `catalog_category_product_index` AS `cat_index` ON cat_index.product_id=e.entity_id
		 AND cat_index.store_id='1' AND cat_index.category_id='$a_category'
		 
		  INNER JOIN `catalog_product_entity_int` AS `_table_metal`
		  ON
		 (_table_metal.entity_id = e.entity_id) AND (_table_metal.attribute_id='$a_metal') AND (_table_metal.store_id=0) 
		
		 INNER JOIN `catalog_product_entity_int` AS `_table_de_stone_shape` ON (_table_de_stone_shape.entity_id = e.entity_id) AND
	    (_table_de_stone_shape.attribute_id='$a_shape') AND (_table_de_stone_shape.store_id=0)
		
		 INNER JOIN `catalog_product_entity_int` 
	     AS `_table_settingstyle` ON (_table_settingstyle.entity_id = e.entity_id) AND
		  (_table_settingstyle.attribute_id='$a_settingstyle') 
		 AND (_table_settingstyle.store_id=0) 
		 
		 INNER JOIN `catalog_product_entity_int` AS `_table_emb_color1` ON
	    (_table_emb_color1.entity_id = e.entity_id) AND (_table_emb_color1.attribute_id='$a_emb_color') AND
		 (_table_emb_color1.store_id=0)
	    
		  INNER JOIN `catalog_product_entity_varchar` AS `_table_emb_carat_weight1` ON (_table_emb_carat_weight1.entity_id =   e.entity_id) AND (_table_emb_carat_weight1.attribute_id='$a_carat_weight') AND (_table_emb_carat_weight1.store_id=0)
		
	    
		INNER JOIN `catalog_product_entity_decimal` AS `_table_price` ON (_table_price.entity_id = e.entity_id) AND
	     (_table_price.attribute_id='$a_price') AND (_table_price.store_id=0) 
		 
		 INNER JOIN `catalog_product_entity_int` AS
	    `_table_emb_clarity1` ON (_table_emb_clarity1.entity_id = e.entity_id) AND (_table_emb_clarity1.attribute_id='$a_emb_clarity') 
	 	AND (_table_emb_clarity1.store_id=0)
				 WHERE (_table_metal.value = '$metal') AND (_table_de_stone_shape.value = '$shape') AND 
				 (_table_settingstyle.value like '$setting') order by total_carat_weight";
	
				 
	   $db = Mage::getSingleton('core/resource')->getConnection('core_write');
       $result = $db->query($query);
      
	   $countr = count($carat); 
	   
	   
       while($rows = $result->fetch(PDO::FETCH_ASSOC))
       {
		   
		   for($ii=0;$ii< $countr ;$ii++)
		   {
			  // echo $rows['total_carat_weight']."==" .$carat[$ii][0] ."<br/>";
				   
			   if($rows['total_carat_weight']==$carat[$ii][0])
			   {
				  // echo $rows['total_carat_weight']."<br/>";
				   $carat[$ii][5]=1;
			   }
		   } 
	   }	
	
		
		return $carat;
		
	}
	

public function getpricebyclaritycolor($metal,$shape,$sesstingstyle,$clarity,$color,$ratearray,$y)
    {
		
		global 	
	 	 $a_metal;
	    global 	
	     $a_shape ;
	    global 
         $a_category; 
		global 
     	 $a_settingstyle ;
		global 
     	 $a_emb_color ;
		global 
     	 $a_carat_weight;
		global 
     	 $a_price;
		global  
     	 $a_emb_clarity;
		
		
		$sql="SELECT  e.sku,
		        `_table_price`.`value` AS `price`,`_table_emb_carat_weight1`.`value` AS `total_carat_weight` 
			   
			    FROM `catalog_product_entity` AS `e` 
				
				INNER JOIN `catalog_category_product_index` AS `cat_index` ON 
				cat_index.product_id=e.entity_id AND cat_index.store_id='1'
			    AND cat_index.category_id='$a_category' 
				
				INNER JOIN `catalog_product_entity_int` AS `_table_metal` ON 
				(_table_metal.entity_id = e.entity_id) AND (_table_metal.attribute_id='$a_metal') AND (_table_metal.store_id=0) 
				
				INNER JOIN `catalog_product_entity_int` AS `_table_de_stone_shape` ON (_table_de_stone_shape.entity_id = e.entity_id)
				 AND (_table_de_stone_shape.attribute_id='$a_shape') AND (_table_de_stone_shape.store_id=0) 
				
				INNER JOIN `catalog_product_entity_int` AS `_table_settingstyle` ON (_table_settingstyle.entity_id = e.entity_id) 
				AND (_table_settingstyle.attribute_id='$a_settingstyle') AND (_table_settingstyle.store_id=0) 
				
				INNER JOIN `catalog_product_entity_int` AS `_table_emb_color1` ON (_table_emb_color1.entity_id = e.entity_id) 
				AND (_table_emb_color1.attribute_id='$a_emb_color') AND (_table_emb_color1.store_id=0)
			    
				 INNER JOIN `catalog_product_entity_varchar` AS `_table_emb_carat_weight1` ON (_table_emb_carat_weight1.entity_id =   e.entity_id) AND (_table_emb_carat_weight1.attribute_id='$a_carat_weight') AND (_table_emb_carat_weight1.store_id=0)
		
				 
				INNER JOIN `catalog_product_entity_decimal` AS `_table_price` ON (_table_price.entity_id = e.entity_id) AND
				 (_table_price.attribute_id='$a_price') AND (_table_price.store_id=0)
				 
				INNER JOIN `catalog_product_entity_int` AS
				 `_table_emb_clarity1` ON (_table_emb_clarity1.entity_id = e.entity_id) 
				 AND (_table_emb_clarity1.attribute_id='$a_emb_clarity')
				 AND (_table_emb_clarity1.store_id=0) 
				
				WHERE (_table_metal.value = '$metal') AND (_table_de_stone_shape.value = '$shape') AND 
				(_table_settingstyle.value like '$sesstingstyle') 
				 and  _table_emb_clarity1.value='$clarity'  and
				(_table_emb_color1.value like '$color')";
		
		
 	$db = Mage::getSingleton('core/resource')->getConnection('core_write');
 	$result = $db->query($sql);
	
	$kk=0;
	global $skuarray;
		
	       $ratearray[0][$y]="";
		   $ratearray[1][$y]="";
		   $ratearray[2][$y]="";
		   $ratearray[3][$y]="";
		   $ratearray[4][$y]="";
		   $ratearray[5][$y]="";
		   $ratearray[6][$y]="";
		   $ratearray[7][$y]="";
		   $ratearray[8][$y]="";
		   $skuarray[0][$y]=$rowss['sku']="";
		   $skuarray[1][$y]=$rowss['sku']="";
		   $skuarray[2][$y]=$rowss['sku']="";
		   $skuarray[3][$y]=$rowss['sku']="";
		   $skuarray[4][$y]=$rowss['sku']="";
		   $skuarray[5][$y]=$rowss['sku']="";
		   $skuarray[6][$y]=$rowss['sku']=""; 
	 	   $skuarray[7][$y]=$rowss['sku']="";
		   $skuarray[8][$y]=$rowss['sku']="";
		
 	while($rowss = $result->fetch(PDO::FETCH_ASSOC))
    	{	
		  if($rowss['total_carat_weight']==0.25)
		   {
		     $ratearray[0][$y]=$rowss['price'];
		     $skuarray[0][$y]=$rowss['sku'];
		   }
		   if($rowss['total_carat_weight']==0.33)
		   {
		     $ratearray[1][$y]=$rowss['price'];
			 $skuarray[1][$y]=$rowss['sku'];
		   }
		   if($rowss['total_carat_weight']==0.4)
		   {
		     $ratearray[2][$y]=$rowss['price'];
			 $skuarray[2][$y]=$rowss['sku'];
		   }
		   if($rowss['total_carat_weight']==0.5)
		   {
		     $ratearray[3][$y]=$rowss['price'];
			 $skuarray[3][$y]=$rowss['sku'];
		   }
		   if($rowss['total_carat_weight']==0.75)
		   {
		     $ratearray[4][$y]=$rowss['price'];
			 $skuarray[4][$y]=$rowss['sku'];
		   }
		   if($rowss['total_carat_weight']==1)
		   {
		     $ratearray[5][$y]=$rowss['price'];
			 $skuarray[5][$y]=$rowss['sku'];
		   }
		   if($rowss['total_carat_weight']==1.25)
		   {
		     $ratearray[6][$y]=$rowss['price'];
			 $skuarray[6][$y]=$rowss['sku'];
		   }
		   if($rowss['total_carat_weight']==1.5)
		   {
		     $ratearray[7][$y]=$rowss['price'];
			 $skuarray[7][$y]=$rowss['sku'];
		   }
		   if($rowss['total_carat_weight']==2)
		   {
		     $ratearray[8][$y]=$rowss['price'];
			 $skuarray[8][$y]=$rowss['sku'];
		   }
		 
	
		}
		return $ratearray;
	}


	
	
	
}