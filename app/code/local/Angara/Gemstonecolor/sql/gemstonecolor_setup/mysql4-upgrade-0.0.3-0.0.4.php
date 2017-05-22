 <?php
 $p=array("sp0141s_n","sr0169s","wrm_sr0919","se1040op","se0229s_n","sp0155s","sp0159s","sr0118sd","wr_sr0917","sp0169s","sd_sp0761r","sp0738opd" ,"sr0427s","se0102t","sr0101sd");

  
    try
    {
        $coreResource = Mage::getSingleton('core/resource');
        $write=$coreResource->getConnection('core_write');
        $query ="CREATE TABLE IF NOT EXISTS `ang_bestseller` (
               `id` int(11) unsigned NOT NULL auto_increment,
               `sku` varchar(255) NOT NULL default '',
               PRIMARY KEY  (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
               $write->query($query);
               if(count($p)>0)
               {
                   $write->query("truncate table ang_bestseller");
                   foreach($p as $val)
                   {
                      $query = "INSERT INTO ang_bestseller (sku) VALUES ('".$val."')";
                      $write->query($query);
                   
                   }
                }
    }
    catch(Exception $e)
    {
        echo 'Message: ' .$e->getMessage();
    }