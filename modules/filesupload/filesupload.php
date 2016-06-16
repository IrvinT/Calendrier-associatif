<?php


class filesupload extends Module
{
	private $_dossierExport = "/upload/";
        private $_headContent = '';
        private $_postErrors = array();
        private $_html = '';
	
	public function __construct()
	{
	 	$this->name = 'filesupload';
	 	$this->tab = 'front_office_features';
	 	$this->version = '1.6';
                $this->author = 'Rioo.fr';
                $this->module_key = '5d6a62723177f0201c7eda4fa1c696cc';
                if(floatval(Tools::substr(_PS_VERSION_,0,3))<1.5){
                  $this->id_parent_tab = 2; 
                }else{
                    $this->id_parent_tab = 11; 
                }

	 	parent::__construct();

                $this->displayName = $this->l('Files Upload');
                $this->description = $this->l('Upload files');
		$this->confirmUninstall = $this->l('Are you sure you want to delete all your details ?');
	}
	
	public function install()
	{
	 	if (
	 		!parent::install()
                        OR !$this->registerHook('header')
                        OR !$this->registerHook('top')
                        OR !$this->registerHook('extraRight')
                        OR !$this->registerHook('rightColumn')
	 		OR !$this->installModuleTab('tab_filesupload', $this->l('Files Upload'), $this->id_parent_tab, 99)
	 		OR !$this->installDb()
	 		OR !Configuration::updateValue('FU_MAX_FILE_SIZE', 100)
                        OR !Configuration::updateValue('FU_EXT_ALLOWED', 'jpg,gif,png,zip')
	 		)
	 		return false;
		return true;
	}
	/* Création d'un onglet dans l'admin */
	private function installModuleTab($tabClass, $tabName, $idTabParent, $position)
	{
		@copy(_PS_MODULE_DIR_.$this->name.'/logo.gif', _PS_IMG_DIR_.'t/'.$tabClass.'.gif');
		$tab = new Tab();
                $tab->active = 1;
                $tab->class_name = $tabClass;
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang)
			$tab->name[$lang['id_lang']] = 'Files Upload';		
		$tab->module = $this->name;
		$tab->id_parent = $idTabParent;
                $tab->position = $position;
		if(!$tab->add())
			return false;
		return true;
	}

      
	/* Supression de l'onglet */
	private function uninstallModuleTab($tabClass)
	{
		$idTab = Tab::getIdFromClassName($tabClass);
		if($idTab != 0) {
			$tab = new Tab($idTab);
			$tab->delete();
			return true;
		}
		return false;
	}	
	public function uninstall()
	{
	 	if (!parent::uninstall() 
	 		OR !$this->uninstallModuleTab('tab_filesupload')
	 	   	OR Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'files_upload`')
	 	   	OR !Configuration::deleteByName('FU_MAX_FILE_SIZE')
                        OR !Configuration::deleteByName('FU_EXT_ALLOWED')
	 	   )
	 		return false;
	 	return true;
	}

	public function displayTab()
        {
	  
   
            $tab = '';

            $selectedUploads = $this->selectActiveUploads();

                $tab .= '
                        <table cellspacing="0" cellpadding="0" class="table space" width="98%" align="center">
                                        <tbody>
                                                <tr>
                                                        <th>
                                                                '.$this->l('Upload id').'
                                                        </th>
                                                        <th>
                                                                '.$this->l('Name').'
                                                        </th>
                                                        <th>
                                                                '.$this->l('First Name').'
                                                        </th>
                                                        <th>
                                                                '.$this->l('Email').'
                                                        </th>
                                                        <th>
                                                                '.$this->l('Date').'
                                                        </th>
                                                        <th>
                                                                '.$this->l('File').'
                                                        </th>
                                                        <th>
                                                                '.$this->l('Order').'
                                                        </th>
                                                         <th>
                                                                '.$this->l('Actions').'
                                                        </th>
                                                </tr>';

                                        foreach($selectedUploads as $upload){

                                                if($upload['id_order'] == 'free_up'){
                                                    $link = $this->l('Free upload');
                                                }else{
                                                    $link = '<a href="index.php?tab=AdminOrders&id_order='.$upload['id_order'].'&vieworder&token='.Tools::getAdminTokenLite('AdminOrders').'" target="_blank" title="'.$this->l('Voir').'">'.$this->l('Id order').': '.$upload['id_order'].' <img border="0" alt="'.$this->l('Voir').'" src="../img/admin/details.gif"></a>';
                                                }

                                                $tab .= '<tr>';
        		     				$tab .= '<td>'.$upload['id_upload'].'</td>';
                                                        $tab .= '<td>'.$upload['lastname'].'</td>';
                                                        $tab .= '<td>'.$upload['firstname'].'</td>';
                                                        $tab .= '<td>'.$upload['email'].'</td>';
                                                        $tab .= '<td>'.Tools::displayDate($upload['upload_date']).'</td>';
                                                        $tab .= '<td><a href="'.__PS_BASE_URI__.'modules/filesupload/uploads/file_download.php?name='.$upload['file_name'].'">'.$upload['file_name'].'</a></td>';
                                                        $tab .= '<td>'.$link.'</td>';
                                                        $tab .= '<td>';
                                                        $tab .= '<a href="index.php?tab=AdminCustomers&id_customer='.$upload['id_customer'].'&viewcustomer&token='.Tools::getAdminTokenLite('AdminCustomers').'" target="_blank" title="'.$this->l('Voir').'"><img border="0" alt="'.$this->l('Voir').'" src="../img/admin/details.gif"></a>';
                                                        $tab .= '<a href="index.php?tab=tab_filesupload&id_upload='.$upload['id_upload'].'&deleteupload=1&token='.Tools::getAdminTokenLite('tab_filesupload').'" title="'.$this->l('Supprimer').'"><img border="0" alt="'.$this->l('Supprimer').'" src="../img/admin/delete.gif"></a>';
                                                        $tab .= '</td>';
                                                        
                                                $tab .= '<tr>';
                                        }
                                $tab .= '
                                        </tbody>
                                </table>';

	    return $this->_html = $tab;
	
	}
	
	
	

        public function hookHeader($params)
	{
                //ISO Langue du client
                $iso_lang = Language::getIsoById($params['cookie']->id_lang);
                
                if(floatval(Tools::substr(_PS_VERSION_,0,3))<1.5){
                    $css = '<link href="'.__PS_BASE_URI__.'css/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" media="all"/>';
                    $js = '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery.fancybox-1.3.4.js"></script>';
                }else{
                    $css = '<link href="'.__PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" media="all"/>';
                    $js = '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/plugins/fancybox/jquery.fancybox.js"></script>';
                }

                //Fichiers CSS
		$this->headContent .= $css.'
		<link href="'.__PS_BASE_URI__.'modules/'.$this->name.'/js/jquery.plupload.queue/css/jquery.plupload.queue.css" rel="stylesheet" type="text/css" media="all"/>
                <link href="'.__PS_BASE_URI__.'modules/'.$this->name.'/css/filesupload.css" rel="stylesheet" type="text/css" media="all"/>';

		
                //Fichiers JS
                $this->headContent .= $js.'
                <script type="text/javascript" src="'.__PS_BASE_URI__.'modules/'.$this->name.'/js/plupload.full.min.js"></script>
                ';

                if($iso_lang != 'en'){
                    $this->headContent .='

                    <script type="text/javascript" src="'.__PS_BASE_URI__.'modules/'.$this->name.'/js/i18n/'.$iso_lang.'.js"></script>';
                }

                $this->headContent .='
                <script type="text/javascript" src="'.__PS_BASE_URI__.'modules/'.$this->name.'/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>';

		return $this->headContent;

	}

        public function hookTop($params)
	{

		//Si l'utilisateur n'est pas connecté
                if (@!$params['cookie']->isLogged()){
                         $this->smarty->assign(array(
                            'id' => '1'
                        ));
			return $this->display(__FILE__, 'filesupload_notconnected.tpl');
                }else{
                        $this->smarty->assign(array(
                            'id' => '1',
                            'id_lang' => $params['cookie']->id_lang,
                            'max_file_size' => Configuration::get('FU_MAX_FILE_SIZE'),
                            'ext_allowed' => Configuration::get('FU_EXT_ALLOWED'),
                            'user_orders' => Order::getCustomerOrders($params['cookie']->id_customer),
                            'id_client' => $params['cookie']->id_customer
                        ));
                        return $this->display(__FILE__, 'filesupload.tpl');
                }

		

	}

        public function hookExtraRight($params)
	{
      

		//Si l'utilisateur n'est pas connecté
                if (!$params['cookie']->isLogged()){
                        $this->smarty->assign(array(
                            'id' => '2'
                        ));
			return $this->display(__FILE__, 'filesupload_notconnected.tpl');
                }else{
                        $this->smarty->assign(array(
                            'id' => '2',
                            'id_lang' => $params['cookie']->id_lang,
                            'max_file_size' => Configuration::get('FU_MAX_FILE_SIZE'),
                            'ext_allowed' => Configuration::get('FU_EXT_ALLOWED'),
                            'user_orders' => Order::getCustomerOrders($params['cookie']->id_customer),
                            'id_client' => $params['cookie']->id_customer
                        ));
                        return $this->display(__FILE__, 'filesupload.tpl');
                };
	}

        public function hookRightColumn($params){
           

		//Si l'utilisateur n'est pas connecté
                //Attention Deprecated en 1.5.X
            
                if (@!$params['cookie']->isLogged()){
                        $this->smarty->assign(array(
                            'id' => '3'
                        ));
			return $this->display(__FILE__, 'filesupload_notconnected.tpl');
                }else{
                        $this->smarty->assign(array(
                            'id' => '3',
                            'id_lang' => $params['cookie']->id_lang,
                            'max_file_size' => Configuration::get('FU_MAX_FILE_SIZE'),
                            'ext_allowed' => Configuration::get('FU_EXT_ALLOWED'),
                            'user_orders' => Order::getCustomerOrders($params['cookie']->id_customer),
                            'id_client' => $params['cookie']->id_customer
                        ));
                        return $this->display(__FILE__, 'filesupload.tpl');
                };
        }

        public function installDb()
	{
                Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'files_upload`');
                Db::getInstance()->Execute('

                        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'files_upload` (
                              `id_upload` int(11) NOT NULL AUTO_INCREMENT,
                              `id_customer` int(11) NOT NULL,
                              `id_order` text NOT NULL,
                              `file_name` text NOT NULL,
                              `statut` int(1) NOT NULL,
                              `upload_date` datetime NOT NULL,
                              PRIMARY KEY (`id_upload`)
                            )');

		return true;

	}

        /******************************************/
        /* Ajouter le lien associé à l'utilisateur*/
        /* Envoyer un mail à l'uploadeer          */
        /******************************************/
	public function addCustomerUpload($id_customer, $id_lang, $file_name, $id_order)
	{
		$exist = Db::getInstance()->ExecuteS('
                    SELECT COUNT(*) as count
                    FROM '._DB_PREFIX_.'files_upload WHERE file_name = "'.$file_name.'"');

                //Si pas d'entrée -> INSERT
                if ($exist[0]['count'] == 0){

                    Db::getInstance()->autoExecute(

                    _DB_PREFIX_.'files_upload',

                    array('id_customer' => $id_customer, 'id_order' => $id_order, 'file_name' => $file_name, 'statut' => 1, 'upload_date' => date('Y-m-d H:i:s')), 'INSERT');

                    $this->alertUploaderAndAdmin($id_customer, $id_lang);
                }
	}

	/* Liens actifs par utilisateur*/
        public function selectActiveUploads()
	{
		$uploads = Db::getInstance()->ExecuteS('
                    SELECT c.`id_customer`, c.`firstname`, c.`lastname`, c.`email`, fu.`id_order`, fu.`file_name`, fu.`id_upload`, fu.`upload_date`
                    FROM '._DB_PREFIX_.'files_upload fu
                    LEFT JOIN '._DB_PREFIX_.'customer c ON (fu.`id_customer`= c.`id_customer`)
                    WHERE fu.`statut` = "1"
                    ORDER BY fu.`id_upload` DESC');

                return $uploads;
	}

        public function deleteUpload($id_upload)
	{		
                
                $filename = Db::getInstance()->ExecuteS('SELECT fu.`file_name`
                    FROM '._DB_PREFIX_.'files_upload fu
                    WHERE fu.`statut` = "1" AND fu.`id_upload` = "'.$id_upload.'"');
               
                //Supprimer physiquement le fichier
                if (file_exists(_PS_MODULE_DIR_.$this->name.'/uploads/'.$filename[0]['file_name']))
			unlink(_PS_MODULE_DIR_.$this->name.'/uploads/'.$filename[0]['file_name']);
		else
			return false;

                $delete = Db::getInstance()->delete(_DB_PREFIX_.'files_upload', 'id_upload='.(int)$id_upload);
                return $delete;
	}


        public function getContent()
	{
		if (Tools::getValue('submitFilesUpload'))
		{
			if (!Tools::getValue('max_file_size'))
				$this->_postErrors[] = $this->l('Max file size is required.');
			if (!Tools::getValue('ext_allowed'))
				$this->_postErrors[] = $this->l('Files extensions are required.');                  
			if (!sizeof($this->_postErrors))
			{
				Configuration::updateValue('FU_MAX_FILE_SIZE', Tools::getValue('max_file_size'));
                                Configuration::updateValue('FU_EXT_ALLOWED', preg_replace(array('#;#', '# #'), array(',', ''), trim(Tools::getValue('ext_allowed'))));
        
				$this->displayConf();
			}
			else
				$this->displayErrors();
		}

		$this->displayFilesUpload();
		$this->displayFormSettings();
		return $this->_html;
	}


        public function displayConf()
	{
		$this->_html .= '
		<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
			'.$this->l('Settings updated').'
		</div>';
	}

        public function displayErrors()
	{
		$nbErrors = sizeof($this->_postErrors);
		$this->_html .= '
		<div class="alert error">
			<h3>'.($nbErrors > 1 ? $this->l('There are') : $this->l('There is')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors') : $this->l('error')).'</h3>
			<ol>';
		foreach ($this->_postErrors AS $error)
			$this->_html .= '<li>'.$error.'</li>';
		$this->_html .= '
			</ol>
		</div>';
	}

        public function displayDeleteOK($id_upload)
	{
		$this->_html .= '
		<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
			'.$this->l('Delete').' '.$id_upload.' OK
		</div>';

                return $this->_html;
	}
        
        public function displayFilesUpload()
	{
		$this->_html .= '
		<img src="../modules/filesupload/img/filesupload.png" style="float:left; margin-right:15px;margin-bottom:10px;" />
		<p style="padding-top:20px;"><b>'.$this->l('This module allows your connected clients to upload files.').'</b></p><br /><br />
               	<div style="clear:both;">&nbsp;</div>';
	}
	public function displayFormSettings()
	{
                $conf = Configuration::getMultiple(array('FU_MAX_FILE_SIZE', 'FU_EXT_ALLOWED'));

		$max_file_size = array_key_exists('FU_MAX_FILE_SIZE', $conf) ? $conf['FU_MAX_FILE_SIZE'] : '';
                $ext_allowed = array_key_exists('FU_EXT_ALLOWED', $conf) ? $conf['FU_EXT_ALLOWED'] : '';
                        
		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="clear: both;">
		<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Settings').'</legend>
			<label>'.$this->l('Max file size').'</label>
			<div class="margin-form"><input type="text" size="4" name="max_file_size" value="'.htmlentities($max_file_size, ENT_COMPAT, 'UTF-8').'" /> Mo
			<p class="hint clear" style="display: block; width: 501px;">'.$this->l('Max upload file size in Mb').'</p></div><br /><br /><br />
                        <label>'.$this->l('Extensions allowed').'</label>
			<div class="margin-form"><input type="text" size="70" name="ext_allowed" value="'.htmlentities($ext_allowed, ENT_COMPAT, 'UTF-8').'" />
			<p class="hint clear" style="display: block; width: 501px;">'.$this->l('The customer will be allowed to upload only files with this extensions<br>Separated by comas. Ex: jpg,zip,rar').'</p></div>                          
                        <br />
			<br /><center><input type="submit" name="submitFilesUpload" value="'.$this->l('Update settings').'" class="button" /></center>
		</fieldset>
		</form><br /><br />
                <div class="path_bar"><img src="../img/t/AdminModules.gif" />&nbsp;<a href="http://www.rioo.fr" target="_blank">Module développé par la société RIOO</a><br/>
                </div>';
	}

	
	/* Envoi de l'email pour avertir le client que le bien est uploadé */
	public function alertUploaderAndAdmin($id_client, $id_lang){
             
                $uploader = new Customer(intval($id_client));
		
		$templateVars = array(			
			'{firstname}' => $uploader->firstname,
			'{lastname}' => $uploader->lastname
		);

                //Alert uploader
		Mail::Send(intval($id_lang), 'files_uploaded', Mail::l('Your files are uploaded'), $templateVars, $uploader->email, $uploader->firstname.' '.$uploader->lastname, NULL, NULL, NULL, NULL, dirname(__FILE__).'/mails/');
                //Alert admin
                Mail::Send(intval($id_lang), 'files_uploaded_admin', Mail::l('Someone uploaded files'), $templateVars,  Configuration::get('PS_SHOP_EMAIL'), NULL, Configuration::get('PS_SHOP_EMAIL'), NULL, NULL, NULL, dirname(__FILE__).'/mails/');

	
	}

}
?>