<?php
//include_once(_PS_CLASS_DIR_.'/AdminTab.php');
include_once(_PS_MODULE_DIR_.'filesupload/filesupload.php');

class tab_filesupload extends AdminTab
{
	public function display()
	{
		$module = new filesupload;
		echo $module->displayTab();
	}

        public function postProcess()
	{

		if (Tools::getValue('deleteupload') == 1){
                 $module = new filesupload;
                 $res = $module->deleteUpload(Tools::getValue('id_upload'));

                 if($res){
                     echo $module->displayDeleteOK(Tools::getValue('id_upload'));
                 }
                 
		}

        }

}
?> 
