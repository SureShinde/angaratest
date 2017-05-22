<?php
class Angara_Digger_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
   protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('digger/set_time')
                ->_addBreadcrumb('synonym Manager','synonym Manager');
       return $this;
     }
      public function indexAction()
      {
         $this->_initAction();
         $this->renderLayout();
      }
      public function editAction()
      {
           $synonymId = $this->getRequest()->getParam('id');
           $synonymModel = Mage::getModel('digger/synonym')->load($synonymId);
           if ($synonymModel->getId() || $synonymId == 0)
           {
             Mage::register('synonym_data', $synonymModel);
             $this->loadLayout();
             $this->_setActiveMenu('digger/set_time');
             $this->_addBreadcrumb('synonym Manager', 'synonym Manager');
             $this->_addBreadcrumb('Synonym Description', 'Synonym Description');
             $this->getLayout()->getBlock('head')
                  ->setCanLoadExtJs(true);
             $this->_addContent($this->getLayout()
                  ->createBlock('digger/adminhtml_synonym_edit'))
                  ->_addLeft($this->getLayout()
                  ->createBlock('digger/adminhtml_synonym_edit_tabs')
              );
             $this->renderLayout();
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')
                       ->addError('Synonym does not exist');
                 $this->_redirect('*/*/');
            }
       }
       public function newAction()
       {
          $this->_forward('edit');
       }
       public function saveAction()
       {
         if ($this->getRequest()->getPost())
         {
           try {
                 $postData = $this->getRequest()->getPost();
                 $synonymModel = Mage::getModel('digger/synonym');
               if( $this->getRequest()->getParam('id') <= 0 )
                  $synonymModel->setCreatedTime(
                     Mage::getSingleton('core/date')
                            ->gmtDate()
                    );
                  $synonymModel
                    ->addData($postData)
                    ->setUpdateTime(
                             Mage::getSingleton('core/date')
                             ->gmtDate())
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();
                 Mage::getSingleton('adminhtml/session')
                               ->addSuccess('successfully saved');
                 Mage::getSingleton('adminhtml/session')
                                ->setsynonymData(false);
                 $this->_redirect('*/*/');
                return;
          } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')
                                  ->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')
                 ->setsynonymData($this->getRequest()
                                    ->getPost()
                );
                $this->_redirect('*/*/edit',
                            array('id' => $this->getRequest()
                                                ->getParam('id')));
                return;
                }
              }
              $this->_redirect('*/*/');
            }
          public function deleteAction()
          {
              if($this->getRequest()->getParam('id') > 0)
              {
                try
                {
                    $synonymModel = Mage::getModel('digger/synonym');
                    $synonymModel->setId($this->getRequest()
                                        ->getParam('id'))
                              ->delete();
                    Mage::getSingleton('adminhtml/session')
                               ->addSuccess('successfully deleted');
                    $this->_redirect('*/*/');
                 }
                 catch (Exception $e)
                  {
                           Mage::getSingleton('adminhtml/session')
                                ->addError($e->getMessage());
                           $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                  }
             }
            $this->_redirect('*/*/');
       }
	   
	   
	/*
		S:VA	Remove Item(s)
	*/  
	public function massRemoveAction()
	{
		try {
			$ids = $this->getRequest()->getPost('ids', array());
			foreach ($ids as $id) {
				  $model = Mage::getModel('digger/synonym');
				  $model->setId($id)->delete();
			}
			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}
	
	/**
	 * Export order grid to CSV format
	*/
	public function exportCsvAction()
	{
		$fileName   = 'synonym.csv';
		$grid       = $this->getLayout()->createBlock('digger/adminhtml_synonym_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	} 
	/**
	 *  Export order grid to Excel XML format
	*/
	public function exportExcelAction()
	{
		$fileName   = 'synonym.xml';
		$grid       = $this->getLayout()->createBlock('digger/adminhtml_synonym_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
	
	
	
	
	public function importAction(){
		$data	=	$this->getRequest()->getPost();	//$this->getRequest()->getParams();
		if($data){
			if(isset($_FILES['input_file']['name']) && (file_exists($_FILES['input_file']['tmp_name'])) ){
				try {
					$todayDateTime	=	date('d-m-Y-h_i_s');										//	append the time to file name to make the every file having different name
					$temp 			= 	explode(".", $_FILES["input_file"]["name"]);
					//$newfilename 	= 	$temp[0].'-'.round(microtime(true)) . '.' . end($temp);		//	rename the file before uploading
					$newfilename 	= 	$temp[0].'-'.$todayDateTime.'.'.end($temp);					//	rename the file before uploading
					
					$uploader = new Varien_File_Uploader('input_file');
					$uploader->setAllowedExtensions(array('csv'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(false);
					$fileUploadPath = Mage::getBaseDir('media') . DS . 'importcsv' . DS ;
					//$uploader->save($fileUploadPath, $_FILES['input_file']['name'] );		//	file uploaded
					$uploader->save($fileUploadPath, $newfilename );		//	file uploaded
					//echo '<br>File uploaded successfully.';
					//$file =  $_FILES['input_file']['name'];//.date('d-m-Y_h:i:s');
					$filepath 	= $fileUploadPath.$newfilename;
					$i = 0;
					if(($handle = fopen("$filepath", "r")) !== FALSE) {
						$height	=	$_POST['height'];
						$width	=	$_POST['width'];
						while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){ 
							if($i > 0 && count($data)>1){		//	don't consider the header row
								$this->updateData($data);
							}
							$i++;
						}
					}
					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Record(s) imported successfully."));
					$this->_redirect("*/*/");
					return;
				}catch(Exception $e) {
					echo $e->getMessage();
				}
			}
		}else{
			$this->loadLayout();
			$this->renderLayout();
		}
	}
	
	public function updateData( $data) {
		$data 	= 	array( 'master_keyword' => $data[0], 'synonym' => $data[1] ); 
		//echo '<pre>';print_r($data);
		$model 	= 	Mage::getModel('digger/synonym')->setData($data);
        try {
            $insertId = $model->save()->getId();
            //echo "Data successfully inserted. Insert ID: " . $insertId;
        } catch (Exception $e) {
            echo $e->getMessage();die;
        }
	}
	
}
?>
    