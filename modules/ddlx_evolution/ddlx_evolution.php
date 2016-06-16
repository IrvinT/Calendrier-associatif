<?php

/**
 * DDLX EVOLUTION X LITE
 * Date : 20/04/2016
 * Author : SARL DDLX Multimedia
 */
class ddlx_evolution extends Module
{
	const NBPARAMS = 50;

	private $_html;

	private $errors;

	private $message;

	private $currentProfileID;

	private $currentProfileName;

	private $imgFolder;

	private $id_shop;

	private $realpath;

	private $connectionString;

	private $dbi;
	const DDLX_EVOLUTIONX_CSS_TYPE_CSSTIPS = '1';
	const DDLX_EVOLUTIONX_CSS_TYPE_IMG_LOGO_NAME = '2';
	const DDLX_EVOLUTIONX_INMEMORY_CSS_DDLX = 1;
	const DDLX_EVOLUTIONX_INMEMORY_CSS_DDLX2 = 2;
	const DDLX_EVOLUTIONX_INMEMORY_CSS_USER = 3;
	const DDLX_EVOLUTIONX_INMEMORY_CSS_LOGOURL = 4;
	const DDLX_EVOLUTIONX_INMEMORY_CSS_VIDEO = 5;

	function __construct()
	{
		$this->name = 'ddlx_evolution';
		$this->tab = 'DDLX modules';
		$this->version = "1.0.0";
		$this->author = 'DDLX multimédia';
		$this->bootstrap = true;
		
		parent::__construct();
		
		$this->displayName = $this->l('DDLX Evolution X');
		$this->description = $this->l('Template editor for Prestashop 1.6');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');
		
		$this->dbi = Db::getInstance();
		
		$this->id_shop = $this->context->shop->id;
		
		$shop = new Shop((int) $this->id_shop);
		
		$serverTabSplit = preg_split('/:/', _DB_SERVER_);
		$this->connectionString = 'mysql:dbname=' . _DB_NAME_ . ';host=' . $serverTabSplit [0];
		
		if ( $serverTabSplit [1] != null )
		{
			$this->connectionString .= ";port=" . $serverTabSplit [1];
		}
		

		if ( strlen($shop->physical_uri) == 1 )
		{
			$this->realpath = '/modules/' . $this->name . '/';
		}
		else
		{
			$this->realpath = $shop->physical_uri . 'modules/' . $this->name . '/';
		}
	}

	function install()
	{
		if ( ! parent::install() || ! $this->registerTableBdd() || ! $this->registerHook('header') )
			return false;
		return true;
	}

	function uninstall()
	{
		return ( parent::uninstall() && $this->deleteTableBdd() );
	}

	private function registerTableBdd()
	{
				
		$res = $this->dbi->Execute(
				"CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ddlx_evolution_css` (
			`id` int(10) NOT NULL auto_increment,
			`id_profile` int(10) NOT NULL,
			`id_type` int(2) NOT NULL,
			`active` BOOLEAN,
			`data` TEXT,
			PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
		
		$res .= $this->dbi->Execute(
				"
				INSERT INTO `" . _DB_PREFIX_ . "ddlx_evolution_css` 
				(`id`, `id_profile`, `id_type`, `active`, `data`) VALUES
				(1, 1, " . ddlx_evolution::DDLX_EVOLUTIONX_CSS_TYPE_CSSTIPS . ", 1, ''),
				(2, 2, " . ddlx_evolution::DDLX_EVOLUTIONX_CSS_TYPE_CSSTIPS . ", 1, '/* CSS */'),
				(3, 3, " . ddlx_evolution::DDLX_EVOLUTIONX_CSS_TYPE_CSSTIPS . ", 1, '')
				");
		
		$res .= $this->dbi->Execute(
				"CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ddlx_evolution_profiles` (
			`id` int(10) NOT NULL auto_increment,
			`id_shop` int(10),
			`name` varchar(80) NOT NULL,
			`active` BOOLEAN NOT NULL,
			PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
		
		$res .= $this->dbi->Execute(
				"INSERT INTO `" . _DB_PREFIX_ . "ddlx_evolution_profiles` (`id`, `id_shop`, `name`, `active`) VALUES
		(1," . $this->id_shop . ",  'exdefault', 0),
		(2," . $this->id_shop . ",  'exdefault2', 1),
		(3," . $this->id_shop . ",  'expresta', 0)
		;");
		
		$parameters = ' ';
		for( $i = 1; $i <= ddlx_evolution::NBPARAMS; $i ++ )
		{
			$parameters .= ' `param' . $i . '` char(32), ';
		}
		
		$res .= $this->dbi->Execute(
				"CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ddlx_evolution_elements` (
				`id` int(10) NOT NULL auto_increment,
				`id_profile` int(10) NOT NULL,
				`element` char(32) NOT NULL,
				" . $parameters . "
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
		
		$paramsList = $this->getParamListForSQL();
		
		$res .= $this->dbi->Execute(
				"INSERT INTO `" . _DB_PREFIX_ . "ddlx_evolution_elements` 
(`id`, `id_profile`, `element`," . $paramsList . ") VALUES

(1, 1, 'header', 'logo.png', '', '5', '0', '', '1', 'rgba(255, 255, 255, 0.66)', 'rgba(255, 255, 255, 0.62)', 'repeat', 'right bottom', '320', 'fond.png', 'rgb(63, 63, 63)', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'rgb(63, 63, 63)', '27', '0', 'rgb(255, 255, 255)', 'rgb(63, 63, 63)', 'rgb(0, 0, 0)', 'rgb(63, 63, 63)', 'rgb(28, 28, 28)', 'rgb(63, 63, 63)', 'rgb(63, 63, 63)', 'right', 'center', 'rgb(249, 249, 249)', 'rgb(244, 244, 244)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(2, 1, 'product', 'logo07.png', '', '1', 'center', 'center', 'repeat', 'fixed', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'bottom', 'rgb(0, 0, 0)', 'rgb(63, 63, 63)', 'rgb(255, 0, 0)', 'rgb(147, 176, 255)', 'rgb(0, 0, 0)', 'rgb(80, 120, 255)', 'rgb(44, 122, 51)', '', '', 'bottom', 'rgba(13, 254, 27, 0)', 'rgba(255, 90, 90, 0)', 'rgb(0, 0, 0)', 'rgb(72, 72, 72)', 'right bottom', 'rgb(141, 141, 141)', 'rgb(159, 159, 159)', 'rgb(47, 47, 47)', 'Arial', '18', 'rgb(0, 0, 0)', '', 'bottom', 'rgb(121, 121, 121)', 'rgb(111, 109, 109)', 'rgb(0, 0, 0)', 'lucida console', 'rgb(255, 255, 255)', 'rgba(135, 104, 104, 0)', '0', '0', '0', '0', '16', '', '', '', '', '', ''),
(3, 1, 'footer', 'Chat_epic_2.png', '', '1', 'center', 'center', 'repeat', 'fixed', 'rgb(0, 0, 0)', 'rgb(0, 0, 0)', 'bottom', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(4, 1, 'block', 'bottom', 'rgb(63, 63, 63)', 'rgb(34, 34, 34)', '0', 'solid', 'rgb(160, 160, 160)', '0', '0', '0', '0', 'Arial', '18', 'rgb(255, 255, 255)', '12', 'rgb(185, 185, 185)', 'bottom', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', '1', 'solid', 'rgb(173, 173, 173)', '5', '5', '5', '5', 'Arial', '13', 'rgb(0, 0, 0)', '15', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(5, 1, 'background', 'texture04.jpg', '', 'center', 'center', 'repeat', 'fixed', 'bottom', 'rgb(255, 255, 255)', 'rgba(142, 142, 142, 0)', '', '', '', '', '', '', 'fond.png', '1', 'center', 'center', 'repeat', 'scroll', 'bottom', 'rgba(255, 255, 255, 0.9)', 'rgba(255, 255, 255, 0.9)', '', '', '', '', '', 'H.jpg', '1', '1', 'left', 'center', 'repeat-y', 'bottom', 'rgb(255, 255, 255)', 'rgb(0, 0, 0)', 'fixed', '', '', '', '', '', '', '', '', '', '', ''),
(6, 1, 'navigation', 'rgb(63, 63, 63)', 'rgb(37, 37, 37)', 'bottom', '0', 'solid', 'rgb(252, 252, 252)', '0', '0', '0', '0', '1', '70', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'rgb(95, 95, 95)', '18', 'bottom', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'rgb(0, 0, 0)', 'rgb(52, 52, 52)', 'rgb(89, 89, 89)', 'rgb(0, 0, 0)', 'Arial', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(7, 1, 'general', 'rgb(63, 63, 63)', 'rgb(46, 46, 46)', 'rgb(76, 76, 76)', 'rgb(76, 76, 76)', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'rgb(246, 246, 246)', 'rgb(200, 200, 200)', 'rgb(0, 0, 0)', 'rgb(90, 90, 90)', 'rgb(69, 69, 69)', 'rgb(227, 227, 227)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),

(8, 2, 'general', 'rgb(255, 181, 0)', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'rgb(255, 181, 0)', 'rgb(255, 148, 0)', 'rgb(50, 49, 49)', 'rgb(246, 246, 246)', 'rgb(200, 200, 200)', 'rgb(0, 0, 0)', 'rgb(90, 90, 90)', 'rgb(69, 69, 69)', 'rgb(227, 227, 227)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(9, 2, 'navigation', 'rgb(79, 194, 255)', 'rgb(17, 141, 209)', 'bottom', '0', 'solid', 'rgb(252, 252, 252)', '0', '0', '0', '0', '1', '70', 'rgb(255, 255, 255)', 'rgb(0, 0, 0)', 'rgb(255, 181, 0)', '18', 'bottom', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', '', 'rgb(255, 181, 0)', 'rgb(65, 65, 65)', 'rgb(17, 141, 209)', 'Arial', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(10, 2, 'background', 'texture04.jpg', '1', 'center', 'center', 'repeat', 'fixed', 'bottom', 'rgb(233, 233, 233)', 'rgb(233, 233, 233)', '', '', '', '', '', '1', 'fond.png', '1', 'center', 'center', 'repeat', 'scroll', 'bottom', 'rgba(255, 255, 255, 0.9)', 'rgba(255, 255, 255, 0.9)', '', '', '', '', '', 'H.jpg', '1', '1', 'left', 'center', 'repeat-y', 'bottom', 'rgb(255, 255, 255)', 'rgb(0, 0, 0)', 'fixed', '', '', '', '', '', '', '', '', '', '', ''),
(11, 2, 'block', 'bottom', 'rgb(63, 63, 63)', 'rgb(34, 34, 34)', '0', 'solid', 'rgb(160, 160, 160)', '0', '0', '0', '0', 'Arial', '18', 'rgb(255, 255, 255)', '0', 'rgb(46, 165, 230)', 'bottom', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', '1', 'solid', 'rgb(173, 173, 173)', '0', '0', '0', '0', 'Arial', '13', 'rgb(0, 0, 0)', '15', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(12, 2, 'footer', 'Chat_epic_2.png', '', '1', 'center', 'center', 'repeat', 'fixed', 'rgb(30, 30, 30)', 'rgb(30, 30, 30)', 'bottom', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(13, 2, 'product', 'logo07.png', '', '1', 'center', 'center', 'repeat', 'fixed', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'bottom', 'rgb(0, 0, 0)', 'rgb(63, 63, 63)', 'rgb(255, 0, 0)', 'rgb(147, 176, 255)', 'rgb(0, 0, 0)', 'rgb(80, 120, 255)', 'rgb(44, 122, 51)', '', '', 'bottom', 'rgba(13, 254, 27, 0)', 'rgba(255, 90, 90, 0)', 'rgb(0, 0, 0)', 'rgb(72, 72, 72)', '', 'rgb(141, 141, 141)', 'rgb(159, 159, 159)', 'rgb(47, 47, 47)', 'Arial', '18', 'rgb(0, 0, 0)', '', 'bottom', 'rgba(255, 255, 255, 0)', 'rgba(255, 255, 255, 0)', 'rgb(0, 0, 0)', 'lucida console', 'rgb(46, 165, 230)', 'rgba(135, 104, 104, 0)', '0', '0', '0', '0', '18', '', '', '', '', '', ''),
(14, 2, 'header', 'logo.png', '', '5', '0', '', '', 'rgba(255, 255, 255, 0.66)', 'rgba(255, 255, 255, 0.62)', 'repeat-x', 'bottom', '320', 'header.jpg', 'rgb(46, 165, 230)', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'rgb(0, 83, 128)', '27', '0', 'rgb(228, 228, 228)', 'rgba(162, 109, 109, 0)', 'rgba(0, 0, 0, 0)', 'rgb(46, 165, 230)', 'rgb(63, 63, 63)', 'rgb(46, 165, 230)', 'rgb(63, 63, 63)', 'center', 'bottom', 'rgb(249, 249, 249)', 'rgb(244, 244, 244)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),

(15, 3, 'header', 'logo.jpg', '', '5', '10', '1', '1', 'rgba(255, 255, 255, 0.66)', 'rgba(255, 255, 255, 0.62)', 'repeat', 'right bottom', '340', 'fond.png', 'rgb(51, 51, 51)', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'rgb(63, 63, 63)', '27', '0', 'rgb(255, 255, 255)', 'rgb(51, 51, 51)', 'rgb(43, 43, 43)', 'rgb(51, 51, 51)', 'rgb(72, 72, 72)', 'rgb(61, 61, 61)', 'rgb(51, 51, 51)', 'right', 'center', 'rgb(249, 249, 249)', 'rgb(244, 244, 244)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(16, 3, 'product', 'logo07.png', '', '1', 'center', 'center', 'repeat', 'fixed', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', 'bottom', '', 'rgb(63, 63, 63)', 'rgb(255, 0, 0)', 'rgb(147, 176, 255)', 'rgb(0, 0, 0)', 'rgb(80, 120, 255)', 'rgb(44, 122, 51)', '', '', 'bottom', 'rgba(13, 254, 27, 0)', 'rgba(255, 90, 90, 0)', 'rgb(0, 0, 0)', 'rgb(72, 72, 72)', 'bottom', 'rgb(141, 141, 141)', 'rgb(159, 159, 159)', 'rgb(47, 47, 47)', 'Arial', '18', 'rgb(0, 0, 0)', '', 'bottom', 'rgba(255, 255, 255, 0)', 'rgba(255, 255, 255, 0)', 'rgb(0, 0, 0)', 'lucida console', 'rgb(255, 255, 255)', 'rgb(44, 44, 44)', '0', '0', '0', '0', '18', '', '', '', '', '', ''),
(17, 3, 'footer', 'Chat_epic_2.png', '', '1', 'center', 'center', 'repeat', 'fixed', 'rgb(0, 0, 0)', 'rgb(0, 0, 0)', 'bottom', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(18, 3, 'block', 'bottom', 'rgb(222, 222, 222)', 'rgb(229, 229, 229)', '0', 'solid', 'rgb(44, 44, 44)', '0', '0', '0', '0', 'Arial', '18', 'rgb(0, 0, 0)', '15', 'rgb(185, 185, 185)', 'bottom', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', '0', 'solid', 'rgb(173, 173, 173)', '0', '0', '0', '0', 'Arial', '13', 'rgb(0, 0, 0)', '15', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(19, 3, 'background', 'texture04.jpg', '1', 'center', 'center', 'repeat', 'fixed', 'bottom', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', '', '', '', '', '', '1', 'fond.png', '1', 'center', 'center', 'repeat', 'scroll', 'bottom', 'rgba(255, 255, 255, 0.9)', 'rgba(255, 255, 255, 0.9)', '', '', '', '', '', 'H.jpg', '1', '1', 'left', 'center', 'repeat-y', 'bottom', 'rgb(255, 255, 255)', '', 'fixed', '', '', '', '', '', '', '', '', '', '', ''),
(20, 3, 'navigation', 'rgb(246, 246, 246)', 'rgb(246, 246, 246)', 'bottom', '0', 'solid', 'rgb(252, 252, 252)', '0', '0', '0', '0', '1', '60', 'rgb(72, 72, 72)', 'rgb(255, 255, 255)', 'rgb(51, 51, 51)', '18', 'bottom', 'rgb(255, 255, 255)', 'rgb(255, 255, 255)', '', 'rgb(59, 59, 59)', 'rgb(95, 95, 95)', 'rgb(0, 0, 0)', 'Arial', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(21, 3, 'general', 'rgb(0, 132, 191)', 'rgb(0, 104, 151)', 'rgb(255, 255, 255)', 'rgb(67, 177, 85)', 'rgb(71, 162, 86)', 'rgb(255, 255, 255)', 'rgb(246, 246, 246)', 'rgb(200, 200, 200)', 'rgb(0, 0, 0)', 'rgb(90, 90, 90)', 'rgb(69, 69, 69)', 'rgb(227, 227, 227)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
						
		");
		
		$res .= $this->dbi->execute(
				'
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ddlx_evolution_generatedcss` (
			`id_profile` int(10) unsigned,
			`id_type` int (2) unsigned,
			`css` VARCHAR(21800)
			)
			ENGINE=MEMORY DEFAULT CHARSET=utf8');
		
		return $res;
	}

	/**
	 * Ne supprime pas les tables, en cas d'erreur de manipulation, pas de pertes.
	 *
	 * @return boolean
	 */
	private function deleteTableBdd()
	{
		$this->dbi->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ddlx_evolution_profiles`;');
		$this->dbi->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ddlx_evolution_elements`;');
		$this->dbi->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ddlx_evolution_css`;');
		$this->dbi->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ddlx_evolution_generatedcss`;');
		Configuration::deleteByName('DDLX_EVOLUTIONX_REGISTER');
		return true;
	}

	private function deleteExportedTheme()
	{
		$exportDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR;
		
		$files = glob($exportDirectory . "*");
		
		foreach ( $files as $file )
		{
			if ( is_dir($file) )
			{
				$this->rrmdir($file);
			}
		}
		if ( @fopen($exportDirectory . 'Config.xml', 'r') )
		{
			unlink($exportDirectory . 'Config.xml');
		}
	}

	public function getContent()
	{
		$this->context->controller->addJqueryUI('ui.dialog');
		
		// $this->deleteExportedTheme();
		$this->checkCurrentProfile(); // verify current profile and store his values
		
		if ( Tools::isSubmit('form') )
		{
			$this->postProcess();
		}
		
		$this->_html .= '<div class="toolbar-placeholder">
							<div class="toolbarBox ">
								<div class="pageTitle">
									<table width="100%">
							            <tr>
							                <td><a href="http://shop.ddlx.org" target="_blank"><img src="' .
				 $this->realpath . 'img/logo_ddlx.png"/>  ' . $this->l('Skins for prestashop') .
				 '</a></td>
				  <td style="text-align:right;"><a href="http://www.evolution-x.fr/produit/evolution-x-editeur-de-theme-pour-prestashop-1-6/" target="_blank"><img src="' .
				 $this->realpath . 'img/data/unlock.png"/>  ' . $this->l('Unlock all option Get pro version') . '</a></td>
							                <td style="text-align:right;"><a href="http://www.ddlx.org/les-forums/forum/evolution-x/" target="_blank"><img src="' .
				 $this->realpath . 'img/data/help.png"/>  ' . $this->l('Module help') . '</a></td>
				 
				 
							            </tr>
									</table>
								</div>
							</div>
						</div>';
		

		$this->_html .= $this->displayPanel();
		
		return $this->_html; // finaly display all
	}


	/**
	 * post datas management function
	 */
	private function postProcess()
	{
		switch ( $_POST ['form'] )
		{
			
			case "profile" :
				$this->_postProfile();
				break;
			case "navigation" :
				$this->_postNavigation();
				break;
			case "product" :
				$this->_postProducts();
				break;
			case "block" :
				$this->_postBlocks();
				break;
			case "background" :
				$this->_postBackground();
				break;
			case "header" :
				$this->_postHeader();
				break;
			case "footer" :
				$this->_postFooter();
				break;
			case "css" :
				$this->_postCss();
				break;
			case "profile" :
				$this->_postProfile();
				break;
			case "general" :
				$this->_postGeneral();
				break;
		}
		
		// update css
		if ( $_POST ['form'] !== "profile" )
		{
			$this->updateCSSInfosBD();
		}
		
		// display messages
		if ( $this->errors != '' )
		{
			$this->_html .= $this->displayError($this->errors);
		}
		elseif ( $this->message != '' )
		{
			$this->_html .= $this->displayConfirmation($this->message);
		}
		else
		{
			$this->_html .= $this->displayConfirmation($this->l('Configuration updated') . '<br />');
		}
	}
	

	// ####################### POST MANAGEMENT ##################################
	// ####################### CONFIG GENERAL
	private function _postGeneral()
	{
		$updateparam = '';
		
		for( $i = 1; $i <= ddlx_evolution::NBPARAMS; $i ++ )
		{
			${"param" . $i} = ( isset($_POST ['param' . $i]) ) ? $_POST ['param' . $i] : '';
			
			if ( $i == ddlx_evolution::NBPARAMS )
			{
				$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "'";
			}
			else
			{
				$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
			}
		}
		
		// UPDATING CURRENT TEMPLATE ***************************************************************************************************
		$sql = "UPDATE " . _DB_PREFIX_ . "ddlx_evolution_elements SET " . $updateparam . "
			WHERE `id_profile` = '" . $this->currentProfileID . "' 
			AND `element` = 'general'";
		
		// AND `id_shop` = ' . $this->id_shop . '
		
		if ( ! $result = $this->dbi->Execute($sql) )
		{
			$this->errors .= $this->l('Error while saving profile datas.') . ' (' . $this->dbi->getMsgError() . ')<br />';
		}
	}
	
	// ####################### CONFIG HEADER
	private function _postHeader()
	{
		// param1: image url, param2: bool noimage, param3: posX, param4: posY, param5: , param6:imageheaderbg
		// param7:header bgcolor , param5: header transparent, param9: bg repeat, param10: no image
		$updateparam = '';
		
		for( $i = 1; $i <= ddlx_evolution::NBPARAMS; $i ++ )
		{
			
			// gestion upload image
			if ( $i === 1 )
			{
				if ( isset($_FILES ['imagelogo']) && ! empty($_FILES ['imagelogo'] ['name']) )
				{
					${"param" . $i} = $this->upload($_FILES ['imagelogo']);
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
			
			else if ( $i === 12 )
			{
				if ( isset($_FILES ['imageheaderbg']) && ! empty($_FILES ['imageheaderbg'] ['name']) )
				{
					${"param" . $i} = $this->upload($_FILES ['imageheaderbg']);
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
			// autres params
			else
			{
				${"param" . $i} = ( isset($_POST ['param' . $i]) ) ? $_POST ['param' . $i] : '';
				
				if ( $i == ddlx_evolution::NBPARAMS )
				{
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "'";
				}
				else
				{
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
		}
		

		// UPDATING CURRENT TEMPLATE ***************************************************************************************************
		$sql = 'UPDATE ' . _DB_PREFIX_ . 'ddlx_evolution_elements SET ' . $updateparam . '
			WHERE `id_profile` = ' . $this->currentProfileID . '
			AND `element` = "header"  LIMIT 1';
		
		// AND `id_shop` = ' . $this->id_shop . '
		
		if ( ! $result = $this->dbi->Execute($sql) )
		{
			$this->errors .= $this->l('Error while saving profile datas.') . ' (' . $this->dbi->getMsgError() . ')<br />';
		}
	}
	
	// ####################### CONFIG BACKGROUND ##############################
	private function _postBackground()
	{
		$updateparam = '';
		
		for( $i = 1; $i <= ddlx_evolution::NBPARAMS; $i ++ )
		{
			// gestion upload image
			if ( $i === 1 )
			{
				if ( isset($_FILES ['imagebg']) && ! empty($_FILES ['imagebg'] ['name']) )
				{
					${"param" . $i} = $this->upload($_FILES ['imagebg']);
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
			else if ( $i === 16 )
			{
				if ( isset($_FILES ['container_bg_image']) && ! empty($_FILES ['container_bg_image'] ['name']) )
				{
					${"param" . $i} = $this->upload($_FILES ['container_bg_image']);
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
			else if ( $i === 30 )
			{
				if ( isset($_FILES ['backgroundcolumns_image']) && ! empty($_FILES ['backgroundcolumns_image'] ['name']) )
				{
					${"param" . $i} = $this->upload($_FILES ['backgroundcolumns_image']);
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
			else
			{
				${"param" . $i} = ( isset($_POST ['param' . $i]) ) ? $_POST ['param' . $i] : '';
				
				if ( $i == ddlx_evolution::NBPARAMS )
				{
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "'";
				}
				else
				{
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
		}
		

		$sql = ' UPDATE ' . _DB_PREFIX_ . 'ddlx_evolution_elements SET ' . $updateparam . '
            WHERE `id_profile` = \'' . $this->currentProfileID . '\' 
            AND `element` = "background" LIMIT 1 ;';
		
		// AND `id_shop` = ' . $this->id_shop . '
		
		if ( ! $this->dbi->Execute($sql) )
		{
			$this->errors .= $this->l('Error while saving profile datas.') . ' (' . $this->dbi->getMsgError() . ')<br />';
		}
	}
	
	// ####################### CONFIG BLOC ##############################
	private function _postBlocks()
	{
		$updateparam = '';
		
		for( $i = 1; $i <= ddlx_evolution::NBPARAMS; $i ++ )
		{
			${"param" . $i} = ( isset($_POST ['param' . $i]) ) ? $_POST ['param' . $i] : '';
			
			if ( $i == ddlx_evolution::NBPARAMS )
			{
				$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "'";
			}
			else
			{
				$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
			}
		}
		
		$sql = ' UPDATE ' . _DB_PREFIX_ . 'ddlx_evolution_elements SET ' . $updateparam . '
            WHERE `id_profile` = \'' . $this->currentProfileID . '\' 
            AND `element` = "block" LIMIT 1 ;';
		
		// AND `id_shop` = ' . $this->id_shop . '
		
		if ( ! $this->dbi->Execute($sql) )
		{
			$this->errors .= $this->l('Error while saving profile datas.') . ' (' . $this->dbi->getMsgError() . ')<br />';
		}
	}
	

	// ####################### CONFIG FOOTER ##############################
	private function _postFooter()
	{
		$updateparam = '';
		
		for( $i = 1; $i <= ddlx_evolution::NBPARAMS; $i ++ )
		{
			// gestion upload image
			if ( $i === 1 )
			{
				if ( isset($_FILES ['imagefooterbg']) && ! empty($_FILES ['imagefooterbg'] ['name']) )
				{
					${"param" . $i} = $this->upload($_FILES ['imagefooterbg']);
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
			else
			{
				${"param" . $i} = ( isset($_POST ['param' . $i]) ) ? $_POST ['param' . $i] : '';
				
				if ( $i == ddlx_evolution::NBPARAMS )
				{
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "'";
				}
				else
				{
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
		}
		

		$sql = ' UPDATE ' . _DB_PREFIX_ . 'ddlx_evolution_elements SET ' . $updateparam . '
            WHERE `id_profile` = \'' . $this->currentProfileID . '\'   
            AND `element` = "footer" LIMIT 1 ;';
		
		// AND `id_shop` = ' . $this->id_shop . '
		
		if ( ! $this->dbi->Execute($sql) )
		{
			$this->errors .= $this->l('Error while saving profile datas.') . ' (' . $this->dbi->getMsgError() . ')<br />';
		}
	}
	

	// ####################### CONFIG PRODUCT ##############################
	private function _postProducts()
	{
		$updateparam = '';
		
		for( $i = 1; $i <= ddlx_evolution::NBPARAMS; $i ++ )
		{
			// gestion upload image
			if ( $i === 1 )
			{
				if ( isset($_FILES ['productbackground_image']) && ! empty($_FILES ['productbackground_image'] ['name']) )
				{
					${"param" . $i} = $this->upload($_FILES ['productbackground_image']);
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
			else
			{
				${"param" . $i} = ( isset($_POST ['param' . $i]) ) ? $_POST ['param' . $i] : '';
				
				if ( $i == ddlx_evolution::NBPARAMS )
				{
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "'";
				}
				else
				{
					$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
				}
			}
		}
		

		$sql = ' UPDATE ' . _DB_PREFIX_ . 'ddlx_evolution_elements SET ' . $updateparam . '
            WHERE `id_profile` = \'' . $this->currentProfileID . '\'     
            AND `element` = "product" LIMIT 1 ;';
		
		// AND `id_shop` = ' . $this->id_shop . '
		
		if ( ! $this->dbi->Execute($sql) )
		{
			$this->errors .= $this->l('Error while saving profile datas.') . ' (' . $this->dbi->getMsgError() . ')<br />';
		}
	}
	

	// ####################### CONFIG NAVIGATION / MENU ##############################
	private function _postNavigation()
	{
		$updateparam = '';
		
		for( $i = 1; $i <= ddlx_evolution::NBPARAMS; $i ++ )
		{
			
			${"param" . $i} = ( isset($_POST ['param' . $i]) ) ? $_POST ['param' . $i] : '';
			
			if ( $i == ddlx_evolution::NBPARAMS )
			{
				$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "'";
			}
			else
			{
				$updateparam .= "`param" . $i . "` = '" . ${"param" . $i} . "',";
			}
		}
		

		$sql = ' UPDATE ' . _DB_PREFIX_ . 'ddlx_evolution_elements SET ' . $updateparam . '
            WHERE `id_profile` = \'' . $this->currentProfileID . '\'             
            AND `element` = "navigation" LIMIT 1 ;';
		
		// AND `id_shop` = ' . $this->id_shop . '
		
		if ( ! $this->dbi->Execute($sql) )
		{
			$this->errors .= $this->l('Error while saving profile datas.') . ' (' . $this->dbi->getMsgError() . ')<br />';
		}
	}
	

	// ####################### CONFIG CSS ##################################
	public function _escape($str)
	{
		$search = array (
				"\\",
				"\0",
				"\n",
				"\r",
				"\x1a",
				"'",
				'"' 
		);
		$replace = array (
				"\\\\",
				"\\0",
				"\\n",
				"\\r",
				"\Z",
				"\'",
				'\"' 
		);
		return str_replace($search, $replace, $str);
	}

	private function _postCss()
	{
		
	}
	
	// ####################### CONFIG PROFILE ##################################
	private function _postProfile()
	{
		// create a profile
		if ( $_POST ['profileToCopy'] && $_POST ['newName'] && isset($_POST ['new_template']) )
		{
			return $this->copyProfile(intVal($_POST ['profileToCopy']), $_POST ['newName']);
		}
		// activating a profile
		if ( isset($_POST ['activate']) && $_POST ['activate'] != $this->currentProfileID )
		{
			return $this->setProfile(intval($_POST ['activate']));
		}
		// delete a profile
		if ( isset($_POST ['deleteProfile']) )
		{
			return $this->deleteProfile(intval($_POST ['deleteProfile']));
		}
		
		// import a profile
		if ( ! empty($_FILES ['importFile'] ['name']) && isset($_POST ['import']) )
		{
			return $this->importProfile();
		}
		
	}
	
	// ####################### PROFILES MANAGEMENT ##################################
	private function rrmdir($dir)
	{
		if ( is_dir($dir) )
		{
			$objects = scandir($dir);
			foreach ( $objects as $object )
			{
				if ( $object != "." && $object != ".." )
				{
					if ( filetype($dir . "/" . $object) == "dir" )
						$this->rrmdir($dir . "/" . $object);
					else
						unlink($dir . "/" . $object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

		

	private function getParamListForSQL()
	{
		$updateparam = '';
		for( $i = 1; $i <= ddlx_evolution::NBPARAMS; $i ++ )
		{
			if ( $i == ddlx_evolution::NBPARAMS )
			{
				$updateparam .= "`param" . $i . "`";
			}
			else
			{
				$updateparam .= "`param" . $i . "`,";
			}
		}
		return $updateparam;
	}
	
	/**
	 *
	 * @return boolean
	 */
	private function importProfile()
	{
		/*
		 * soon available : sql dump mysql_query(file_get_contents('fichier_dump'));
		 */
		require_once ( _PS_TOOL_DIR_ . 'tar/Archive_Tar.php' );
		
		$dir = dirname(__FILE__) . '/img/';
		
		$zipFile = $this->upload($_FILES ['importFile'], '', array (
				'gzip' 
		), $dir);
		
		$name = substr($zipFile, 0, strrpos($zipFile, '.gzip')); // strip last .gzip occurence
		                                                         
		// avoiding folder overwriting
		if ( is_dir($dir . $name) )
		{
			$this->errors .= $this->l('Directory ') . ' : "' . $name . '", ' . $this->l(' already exists, please delete it before importing this profile.') . '<br/>';
			return false;
		}
		
		$archive = new Archive_Tar($dir . $zipFile);
		
		if ( $archive->extract(_PS_ROOT_DIR_) )
		{
			// set Chmod to all files
			exec('chmod -R 755 ' . $dir . $name);
			
			// avoiding SQL overwriting
			if ( $result = $this->dbi->ExecuteS("SELECT id FROM " . _DB_PREFIX_ . "ddlx_evolution_profiles WHERE name = '" . $name . "'  LIMIT 0 , 1") )
			{
				$this->errors .= $this->l('A profile named ') . ' : "' . $name . '", ' . $this->l('already exists, please delete it before importing this profile.') . '<br/>';
				return false;
			}
			
			// create profile
			if ( $fd = fopen($dir . $name . "/data.log", "r") )
			{
				$datas = array ();
				while ( ( $buffer = fgets($fd) ) !== false )
				{
					$line = explode(";", $buffer);
					$datas [$line [0]] = $line;
				}
				
				// important no need for id_shop there, all profiles accessible, only needed when set active
				$sql = "INSERT INTO " . _DB_PREFIX_ . "ddlx_evolution_profiles (`id`, `name`) 
						VALUES (NULL, '" . $name . "' )";
				
				$this->dbi->Execute($sql);
				
				$id_templateInserted = $this->dbi->Insert_ID();
				
				// insert in BD , need to handle css/tips data
				$cssRow = count($datas);
				$row_number = 1;
				
				foreach ( $datas as $val )
				{
					$result;
					
					// last line of daata in csv is for css tips
					if ( $row_number === 8 )
					{
						$db = new PDO($this->connectionString, _DB_USER_, _DB_PASSWD_);
						$statement = $db->prepare(
								"INSERT INTO  " . _DB_PREFIX_ . "ddlx_evolution_css (id_profile, id_type, active, data )
													values (:id_templateInserted, :id_type, :active, :data)");
						$result = $statement->execute(
								array (
										':id_templateInserted' => $id_templateInserted,
										':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_CSS_TYPE_CSSTIPS,
										':active' => $val [0],
										':data' => $val [1] 
								));
						
						$db = null;
					}
					else if ( $row_number < 8 )
					{
						$paramsList = $this->getParamListForSQL();
						
						$valSQL = '';
						for( $i = 0; $i < self::NBPARAMS + 1; $i ++ )
						{
							$valSQL .= "'" . $val [$i] . "',";
						}
						
						$valSQL = substr($valSQL, 0, strlen($valSQL) - 1);
						

						$sql = "INSERT INTO `" . _DB_PREFIX_ . "ddlx_evolution_elements` (id_profile,  element," . $paramsList . ")
								VALUES ( " . $id_templateInserted . " ,  " . $valSQL . " );";
						
						$result = $this->dbi->Execute($sql);
					}
					
					if ( ! $result )
					{
						$this->errors .= $this->l('Impossible to store this profile in database!');
					}
					
					$row_number ++;
				}
				
				fclose($fd);
				unlink($dir . $zipFile);
				unlink($dir . $name . "/data.log");
				$this->message .= $this->l('Congratulation, your profile has been imported') . '<br/>'; // . 'count: '.count($datas);
			}
			else
			{
				$this->errors .= $this->l('Error while trying to access the new files, (archive must not be renamed).') . '<br />';
			}
		}
		else
		{
			$this->errors .= $this->l('Error while decompressing the file.') . '<br />';
		}
	}

	
	/**
	 * This function create a new profile from another.
	 *
	 * @param int $idProfile
	 *        	the id of the profile to duplicate
	 * @param string $newName
	 *        	the name of the new profile
	 * @return true if correctly done
	 */
	private function copyProfile($idProfile, $newName)
	{
		$newName = $this->isValidName($newName);
		
		// verify if already exists
		if ( $result = $this->dbi->ExecuteS("SELECT id FROM " . _DB_PREFIX_ . "ddlx_evolution_profiles
													WHERE name = '" . $newName . "'  LIMIT 0 , 1") )
		{
			$this->errors .= $this->l('A profile named ') . ' : "' . $newName . '", ' . $this->l('already exists, please try another name.') . '<br/>';
			
			return false;
		}
		else
		{
			$profileList = $this->getProfileList();
			$pathToCopy = _PS_ROOT_DIR_ . "/modules/" . $this->name . DIRECTORY_SEPARATOR . "img/" . $profileList [$idProfile] ['name'] . "/";
			$newFolder = _PS_ROOT_DIR_ . "/modules/" . $this->name . DIRECTORY_SEPARATOR . "img/" . $newName . "/";
			
			// create sql profile, no need for id_shop on export, only on import !!
			
			$sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ddlx_evolution_profiles (`id`, `name`, `active`) 
					VALUES (NULL,\'' . $newName . '\', 0)';
			
			if ( ! $this->dbi->Execute($sql) )
			{
				$this->errors .= $this->l('impossible to update lines in DB. l 1355');
			}
			
			// get the id of inserted template
			$id_templateInserted = $this->dbi->Insert_ID();
			
			$paramsList = $this->getParamListForSQL();
			
			$db [0] = 'INSERT INTO `' .
					 _DB_PREFIX_ . 'ddlx_evolution_elements` (id_profile, element, ' . $paramsList . ') SELECT \'' . $id_templateInserted . '\', `element`, ' . $paramsList .
					 ' FROM `' . _DB_PREFIX_ . 'ddlx_evolution_elements` WHERE `element` = \'header\' AND `id_profile` = \'' . $profileList [$idProfile] ['id'] . '\';';
			$db [1] = 'INSERT INTO `' .
					 _DB_PREFIX_ . 'ddlx_evolution_elements` (id_profile, element, ' . $paramsList . ') SELECT \'' . $id_templateInserted . '\', `element`, ' . $paramsList .
					 ' FROM `' . _DB_PREFIX_ . 'ddlx_evolution_elements` WHERE `element` = \'product\' AND `id_profile` = \'' . $profileList [$idProfile] ['id'] . '\';';
			$db [2] = 'INSERT INTO `' .
					 _DB_PREFIX_ . 'ddlx_evolution_elements` (id_profile, element, ' . $paramsList . ') SELECT \'' . $id_templateInserted . '\', `element`, ' . $paramsList .
					 ' FROM `' . _DB_PREFIX_ . 'ddlx_evolution_elements` WHERE `element` = \'footer\' AND `id_profile` = \'' . $profileList [$idProfile] ['id'] . '\';';
			$db [3] = 'INSERT INTO `' .
					 _DB_PREFIX_ . 'ddlx_evolution_elements` (id_profile, element, ' . $paramsList . ') SELECT \'' . $id_templateInserted . '\', `element`, ' . $paramsList .
					 ' FROM `' . _DB_PREFIX_ . 'ddlx_evolution_elements` WHERE `element` = \'block\' AND `id_profile` = \'' . $profileList [$idProfile] ['id'] . '\';';
			$db [4] = 'INSERT INTO `' .
					 _DB_PREFIX_ . 'ddlx_evolution_elements` (id_profile, element, ' . $paramsList . ') SELECT \'' . $id_templateInserted . '\', `element`, ' . $paramsList .
					 ' FROM `' . _DB_PREFIX_ . 'ddlx_evolution_elements` WHERE `element` = \'background\' AND `id_profile` = \'' . $profileList [$idProfile] ['id'] . '\';';
			$db [5] = 'INSERT INTO `' .
					 _DB_PREFIX_ . 'ddlx_evolution_elements` (id_profile, element, ' . $paramsList . ') SELECT \'' . $id_templateInserted . '\', `element`, ' . $paramsList .
					 ' FROM `' . _DB_PREFIX_ . 'ddlx_evolution_elements` WHERE `element` = \'navigation\' AND `id_profile` = \'' . $profileList [$idProfile] ['id'] . '\';';
			$db [6] = 'INSERT INTO `' .
					 _DB_PREFIX_ . 'ddlx_evolution_elements` (id_profile, element, ' . $paramsList . ') SELECT \'' . $id_templateInserted . '\', `element`, ' . $paramsList .
					 ' FROM `' . _DB_PREFIX_ . 'ddlx_evolution_elements` WHERE `element` = \'general\' AND `id_profile` = \'' . $profileList [$idProfile] ['id'] . '\';';
			
			// `id` int(10) NOT NULL auto_increment,
			// `id_profile` int(10) NOT NULL,
			// `id_type` int(2) NOT NULL,
			// `active` BOOLEAN NOT NULL,
			// `data` TEXT,
			
			$db [7] = "INSERT INTO `" . _DB_PREFIX_ . "ddlx_evolution_css` (id_profile, id_type, active, data ) 
						SELECT '" . $id_templateInserted . "', '" .
					 ddlx_evolution::DDLX_EVOLUTIONX_CSS_TYPE_CSSTIPS . "', active, data
						FROM `" . _DB_PREFIX_ . "ddlx_evolution_css` 
						WHERE `id_profile` = '" . $profileList [$idProfile] ['id'] . "';";
			
			for( $i = 0; $i < count($db); $i ++ )
			{
				if ( ! $this->dbi->Execute($db [$i]) )
				{
					$this->errors .= $this->l('Error while copying the database') . '<br />(' . $this->dbi->getMsgError() . ')<br />';
					return false;
				}
			}
			
			if ( ! $this->copy_dir($pathToCopy, $newFolder) )
			{
				$this->errors = $this->l('Impossible to copy directories') . ' (' . $pathToCopy . ' -> ' . $newFolder . ')';
			}
			
			$this->message .= $profileList [$idProfile] ['name'] . ' ' . $this->l('successfuly duplicated') . '<br />';
			return true;
		}
	}

	/**
	 * This function delete a profile and its physic folders
	 *
	 * @param
	 *        	$profileID
	 * @return nothing
	 */
	private function deleteProfile($profileID)
	{
		$listProfile = $this->getProfileList();
		
		$name = $listProfile [$profileID] ['name'];
		
		if ( $listProfile [$profileID] ['active'] != 0 )
		{
			$this->errors .= $this->l('You can\'t delete active profiles, it may be an active profile of another shop.') . '<br/>';
		}
		else
		{
			$delDir = _PS_ROOT_DIR_ . "/modules/" . $this->name . DIRECTORY_SEPARATOR . "img/" . $name . "/";
			
			// activating default profile
			if ( $this->currentProfileID == $profileID )
				$this->setProfile();
			
			$sql = "DELETE elem,profile,css 
					FROM " . _DB_PREFIX_ . "ddlx_evolution_profiles profile 
					LEFT JOIN " . _DB_PREFIX_ . "ddlx_evolution_elements elem 
					ON profile.id = elem.id_profile
					LEFT JOIN " . _DB_PREFIX_ . "ddlx_evolution_css css 
					ON profile.id = css.id_profile 
					WHERE profile.id = " . $profileID;
			
			if ( $this->dbi->Execute($sql) )
			{
				if ( $name && is_dir($delDir) )
				{
					$this->delete_dir($delDir);
					$this->message .= $this->l('profile successfully deleted') . '<br/>';
					return true;
				}
			}
			else
			{
				$this->errors .= $this->l('Error while deleting profile.') . '<br />';
				return false;
			}
		}
	}

	/**
	 * This function generate a color panel of the selected profile
	 *
	 * @param int $profileID
	 *        	the profile id to search and display
	 * @return string a html visual, to show
	 */
	private function getProfileColors($profileID)
	{
		$colors = '';
		$params = $this->getProfileParams($profileID);
		foreach ( $params as $val )
		{
			if ( is_array($val) )
			{
				foreach ( $val as $subval )
				{
					if ( substr($subval, 0, 3) === 'rgb' )
						$colors .= '<span style="background-color:' . $subval . ';padding:3px 3px;float:left;">&nbsp;</span>';
				}
			}
		}
		return $colors;
	}

	/**
	 * this function get all params for a profile
	 *
	 * @param int $profileID
	 *        	the profile id requested
	 * @return array an associative array containing the profile parameters
	 */
	public function getProfileParams($profileID, $isExport = false)
	{
		$output = array ();
		
		$sql = "SELECT * FROM " . _DB_PREFIX_ . "ddlx_evolution_elements 
				WHERE id_profile = '" . $profileID . "' 
				ORDER BY id 
				LIMIT 0,10  ";
		
		if ( ! $result = $this->dbi->ExecuteS($sql) )
		{
			$this->errors .= $this->l('Can\'t get the profile with ') . ' ID = ' . $profileID . '<br/>';
		}
		else
		{
			foreach ( $result as $raw )
			{
				$output [$raw ['element']] = $raw;
			}
		}
		
		// css in table apart
		$sql2 = "SELECT active, data FROM " . _DB_PREFIX_ . "ddlx_evolution_css
				WHERE id_profile = " . $profileID;
		
		if ( ! $result2 = $this->dbi->ExecuteS($sql2) )
		{
			$this->errors .= $this->l('Can\'t get the custom css from profile with ') . ' ID = ' . $profileID . '<br/>';
		}
		else
		{
			if ( $isExport )
			{
				$result2 [0] ["data"] = $this->css_strip_whitespace($result2 [0] ["data"], true);
				
				$output ['css'] = $result2 [0];
			}
			else
			{
				$output ['css'] = $result2 [0];
			}
		}
		
		return $output;
	}

	/**
	 * this function return all templates in an associative array
	 *
	 * @return array containing all profiles
	 */
	private function getProfileList()
	{
		$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'ddlx_evolution_profiles;';
		
		$profiles = array ();
		
		if ( ! $profileList = $this->dbi->ExecuteS($sql) )
		{
			$this->errors .= $this->l('Error while saving profile datas') . ' (' . $this->dbi->getMsgError() . ')<br />';
			return '';
		}
		else
		{
			foreach ( $profileList as $profile )
			{
				$profiles [$profile ['id']] = $profile;
			}
		}
		return $profiles;
	}

	/**
	 *
	 * @param int $profileID        	
	 * @return string profile
	 */
	private function getProfileByID($profileID)
	{
		$db = new PDO($this->connectionString, _DB_USER_, _DB_PASSWD_);
		
		$statement = $db->prepare("SELECT * 
									FROM " . _DB_PREFIX_ . "ddlx_evolution_profiles
									WHERE id = :id_profile");
		
		$res = $statement->execute(array (
				':id_profile' => $profileID 
		));
		$row = $statement->fetch();
		
		$db = null;
		
		if ( ! $res || count($row) < 1 )
		{
			$this->errors .= $this->l('Error while saving profile datas') . ' (' . $this->dbi->getMsgError() . ')<br />';
			return '';
		}
		else
		{
			return $row;
		}
	}

	/**
	 * this function verify if any profile is activated and store its name and id,
	 * else it restore the default,
	 */
	private function checkCurrentProfile()
	{
		$sql = 'SELECT id, name FROM ' . _DB_PREFIX_ . 'ddlx_evolution_profiles 
				WHERE  `active` = 1 
				AND id_shop = ' . $this->id_shop . '
				LIMIT 1';
		
		if ( ! $templatelist = $this->dbi->ExecuteS($sql) )
		{
			$this->setProfile();
			$this->errors .= $this->l('Error while loading profile, restauring default profile') . ' (' . $this->dbi->getMsgError() . ')<br />';
			return;
		}
		else
		{
			$this->currentProfileID = $templatelist [0] ['id'];
			$this->currentProfileName = $templatelist [0] ['name'];
		}
	}

	/**
	 * this function set the given profile as active
	 * $profileId != null ONLY when activating profile
	 *
	 * @param int $profileId
	 *        	the id of the wounded profile
	 * @return bool return true if the loading is successful
	 */
	private function setProfile($profileId = Null)
	{
		// disable all templates
		$this->dbi->Execute('UPDATE ' . _DB_PREFIX_ . 'ddlx_evolution_profiles SET active=0, id_shop = NULL  
									WHERE id_shop = ' . $this->id_shop);
		
		$where = '';
		if ( $profileId == null )
		{
			$where = 'id_shop IS NULL';
		}
		else
		{
			$where = 'id = ' . $profileId;
		}
		

		$sql = 'UPDATE ' . _DB_PREFIX_ . 'ddlx_evolution_profiles
				SET	active = 1, id_shop = ' . $this->id_shop . '  
				WHERE ' . $where . '				
				LIMIT 1 ;';
		

		if ( ! $this->dbi->Execute($sql) )
		{
			$this->errors .= $this->l('error while loading profile') . ' (' . $this->dbi->getMsgError() . ')<br />';
			return false;
		}
		else
		{
			$this->checkCurrentProfile();
			$this->message .= $this->l('profile loaded') . ' : ' . $this->currentProfileName . '<br />';
		}
		
		return true;
	}
	
	// ####################### TOOLS ##################################
	
	/**
	 * this function will return
	 *
	 * @param bool $param        	
	 * @return string checked or not
	 */
	private function check($param)
	{
		return $param ? 'checked="checked"' : '';
	}

	/**
	 * This function return a secure nameFile replacing accents and spaces
	 *
	 * @param string $fileName        	
	 * @return mixed string
	 */
	private function isValidName($fileName)
	{
		$separator = "_"; // Définition du séparateur
		
		$info = pathinfo($fileName);
		if ( isset($info ['extension']) )
		{
			$file_name = basename($fileName, '.' . $info ['extension']);
		}
		else
		{
			$file_name = $fileName;
		}
		
		// accents
		$tofind = "ÀÁÂÃÄÅàáâãäåÇçÒÓÔÕÖØòóôõöøÈÉÊËèéêëÌÍÎÏìíîïÙÚÛÜùúûü¾ÝÿýÑñ";
		$replac = "AAAAAAaaaaaaCcOOOOOOooooooEEEEeeeeIIIIiiiiUUUUuuuuYYyyNn";
		$file_name = strtr(utf8_decode($file_name), utf8_decode($tofind), $replac);
		
		// replace non alpha excepted '.', '_', '-' by $separator.
		$file_name = preg_replace("#([^a-zA-Z0-9._-])#", $separator, $file_name);
		
		// delete twins $separator
		while ( strstr($file_name, $separator . $separator) )
			$file_name = str_replace($separator . $separator, $separator, $file_name);
			
			// delete first and last caracter if is $separator
		$file_name = preg_replace("#(" . $separator . "$)|(^" . $separator . ")|(\.php$)#", "", $file_name);
		
		if ( isset($info ['extension']) )
		{
			return $file_name . '.' . $info ['extension'];
		}
		else
		{
			return $file_name;
		}
	}

	/**
	 * this function copy a folder and all his contents
	 *
	 * @param string $dir2copy
	 *        	the name of the folder to delete
	 * @param string $newDir
	 *        	the name of the new directory folder
	 * @return bool return true if the folder is correctly duplicated
	 */
	private function copy_dir($dir2copy, $newDir)
	{
		if ( is_dir($dir2copy) )
		{
			if ( $dh = opendir($dir2copy) )
			{
				while ( ( $file = readdir($dh) ) !== false )
				{
					if ( ! is_dir($newDir) )
					{
						mkdir($newDir, 0755);
					}
					if ( ! chmod($newDir, 0755) )
					{
						$this->errors .= $this->l('Error while giving rights to the new folder');
						return false;
					}
					if ( is_dir($dir2copy . $file) && $file != '..' && $file != '.' )
					{
						$this->copy_dir($dir2copy . $file . '/', $newDir . $file . '/');
					}
					elseif ( $file != '..' && $file != '.' )
					{
						copy($dir2copy . $file, $newDir . $file);
					}
				}
				closedir($dh);
				return true;
			}
		}
		$this->message .= $dir2copy . ' ' . $this->l('is not a directory') . '<br/>';
		return false;
	}

	/**
	 *
	 * @param array $file
	 *        	the file $_POST format
	 * @param string $newName
	 *        	will keep current file name if not defined
	 * @param array $extAllowed
	 *        	defined for image by default
	 * @param string $destination
	 *        	will use current profile folder if not defined
	 * @return bool string the name of the new file or false if upload have fail
	 */
	private function upload($file, $newName = '', $extAllowed = array('png', 'gif', 'jpg', 'jpeg','PNG', 'GIF', 'JPG', 'JPEG'), $destination = '')
	{
		if ( $file ['error'] === 0 )
		{
			$extension = explode(".", $file ['name']);
			// $destination = $destination ? $destination : __DIR__.'/img/'. $this->currentProfileName .'/' ;
			$destination = $destination ? $destination : dirname(__FILE__) . '/img/' . $this->currentProfileName . '/';
			$newName = $newName ? $newName . '.' . $extension [count($extension) - 1] : $file ['name'];
			$newName = $this->isValidName($newName);
			
			if ( ! in_array($extension [count($extension) - 1], $extAllowed) )
			{
				$this->errors .= $this->l('File type incorrect') . '<br />';
				return false;
			}
			
			if ( is_file($destination . $newName) )
			{
				$this->message .= $this->l('The file') . ' ' . $newName . ' ' . $this->l('already exists and has been replaced.') . '<br/>';
			}
			
			if ( ! move_uploaded_file($file ['tmp_name'], $destination . $newName) )
			{
				$this->errors .= $this->l('Can\'t move file, destination could not be found.') . '<br />';
				return false;
			}
			
			return $newName;
		}
		else
		{
			switch ( $file ['error'] )
			{
				case 1 :
					$this->errors .= $this->l('The file exceeds the limit allowed by the server (php.ini)!') . '<br />';
					break; // UPLOAD_ERR_INI_SIZE
				case 2 :
					$this->errors .= $this->l('The file exceeds the limit allowed in the HTML form!') . '<br />';
					break; // UPLOAD_ERR_FORM_SIZE
				case 3 :
					$this->errors .= $this->l('Sending mail file has been interrupted during the transfer!') . '<br />';
					break; // UPLOAD_ERR_PARTIAL
				case 4 :
					$this->errors .= $this->l('The file you sent has a null size!') . '<br />';
					break; // UPLOAD_ERR_NO_FILE
			}
			return false;
		}
	}

	/**
	 * this function delete a folder and all his contents
	 *
	 * @param string $path
	 *        	the name of the folder to delete
	 * @return true if the folder is correctly deleted
	 */
	private function delete_dir($path)
	{
		if ( $path [strlen($path) - 1] != '/' )
			$path .= '/';
		if ( is_dir($path) )
		{
			$sq = opendir($path);
			while ( $f = readdir($sq) )
			{
				if ( $f != '.' && $f != '..' )
				{
					$fichier = $path . $f;
					if ( is_dir($fichier) )
						$this->delete_dir($fichier);
					else
						@unlink($fichier);
				}
			}
			closedir($sq);
			if ( $path != $this->realpath . 'img/' && rmdir($path) )
			{
				return true;
			}
		}
		else
		{
			@unlink($path);
		}
	}

	/**
	 * return #ffffff if the given string is not a color
	 *
	 * @param string $color
	 *        	an hexadecimal color (#ccc of #ae45f0)
	 * @return string
	 */
	private function isColor($color)
	{
		if ( preg_match('/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i', $color) )
			return $color;
		else
			return '#ffffff';
	}

	function css_strip_whitespace($css, $isExport = false)
	{
		if ( $isExport )
		{
			$replace = array (
					"#\s\s+#" => " "  // Strip excess whitespace.
						);
		}
		else
		{
			$replace = array (
					"#/\*.*?\*/#s" => "", // Strip C style comments.
					"#\s\s+#" => " "  // Strip excess whitespace.
						);
		}
		
		$search = array_keys($replace);
		$css = preg_replace($search, $replace, $css);
		
		$replace = array (
				": " => ":",
				"; " => ";",
				" {" => "{",
				" }" => "}",
				", " => ",",
				"{ " => "{",
				";}" => "}", // Strip optional semicolons.
				",\n" => ",", // Don't wrap multiple selectors.
				"\n}" => "}", // Don't wrap closing braces.
				"} " => "}\n"  // Put each rule on it's own line.
				);
		$search = array_keys($replace);
		$css = str_replace($search, $replace, $css);
		
		return trim($css);
	}
	// ####################### CSS GENERATION ##################################
	/**
	 * return a table with all design infos for front Office.
	 *
	 * @param Int $profileId        	
	 * @return multitype:string unknown
	 */
	private function generateCSSGlobal($profileId)
	{
		$this->imgFolder = $this->realpath . 'img/' . $this->currentProfileName . '/';
		
		$tab_design_infos = Array ();
		
		$params = $this->getProfileParams($profileId);
		
		$css = $this->generateCSSHacks();
		
		/* CSS header **************************************** */
		
		$css .= $this->generateCSSHeader($params);
		
		/* CSS Background **************************************** */
		
		$css .= $this->generateCSSBackground($params);
		
		/* CSS Bloc **************************************** */
		
		$css .= $this->generateCSSBloc($params);
		
		/* CSS FOOTER **************************************** */
		
		$css .= $this->generateCSSFooter($params);
		
		/* CSS Product **************************************** */
		
		$css2 = $this->generateCSSProduct($params);
		
		/* CSS button **************************************** */
		
		$css2 .= $this->generateCSSButton($params);
		
		/* CSS Navigation **************************************** */
		
		$css2 .= $this->generateCSSNavigation($params);
		
		// ---- Custom CSS
		
		$tab_design_infos ['css'] = $this->css_strip_whitespace($css);
		$tab_design_infos ['css2'] = $this->css_strip_whitespace($css2);
		
		$tab_design_infos ['logourl'] = $this->realpath . 'img/' . $this->currentProfileName . '/' . $params ['header'] ['param1'];
		
		$tab_design_infos ['video'] ['url'] = $params ['background'] ['param40'];
		$tab_design_infos ['video'] ['repeat'] = $params ['background'] ['param41'];
		$tab_design_infos ['video'] ['volume'] = $params ['background'] ['param42'];
		
		return $tab_design_infos;
	}

	
	/**
	 *
	 * @param int $profileId        	
	 * @return Ambigous <multitype:string, multitype:string unknown >
	 */
	private function insertCSSInfosBD($profileId)
	{
		// cusstom, css, logourl
		$tab_design_infos = $this->generateCSSGlobal($profileId);
		
		$db = new PDO($this->connectionString, _DB_USER_, _DB_PASSWD_);
		$statement = $db->prepare("INSERT INTO  " . _DB_PREFIX_ . "ddlx_evolution_generatedcss (css, id_type, id_profile)  values (:css, :id_type, :id_profile)");
		$res = $statement->execute(
				array (
						':css' => serialize($tab_design_infos ['css']),
						':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_DDLX,
						':id_profile' => $profileId 
				));
		
		if ( ! isset($tab_design_infos ['user']) )
		{
			$tab_design_infos ['user'] = '';
		}
		$res .= $statement->execute(
				array (
						':css' => serialize($tab_design_infos ['user']),
						':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_USER,
						':id_profile' => $profileId 
				));
		
		$res .= $statement->execute(
				array (
						':css' => serialize($tab_design_infos ['logourl']),
						':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_LOGOURL,
						':id_profile' => $profileId 
				));
		
		$res .= $statement->execute(
				array (
						':css' => serialize($tab_design_infos ['css2']),
						':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_DDLX2,
						':id_profile' => $profileId 
				));
		
		$res .= $statement->execute(
				array (
						':css' => serialize($tab_design_infos ['video']),
						':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_VIDEO,
						':id_profile' => $profileId 
				));
		
		$db = null;
		
		if ( ! $res )
		{
			$this->errors .= $this->l('Unable to save CSS in in-memory table. The design of your store shall be broken.');
		}
		
		return $tab_design_infos;
	}

	/**
	 */
	private function updateCSSInfosBD()
	{
		$result = $this->dbi->executeS("SELECT css FROM " . _DB_PREFIX_ . "ddlx_evolution_generatedcss
								WHERE id_profile = " . $this->currentProfileID);
		
		// if inmemory css exists => update
		if ( ! empty($result) )
		{
			
			// if ( count($result) !== 4 )
			// {
			// $this->dbi->execute("DELETE FROM " . _DB_PREFIX_ . "ddlx_evolution_generatedcss
			// WHERE id_profile = " . $this->currentProfileID);
			// $this->insertCSSInfosBD($this->currentProfileID);
			// return;
			// }
			
			$db = new PDO($this->connectionString, _DB_USER_, _DB_PASSWD_);
			

			$tab_design_infos = $this->generateCSSGlobal($this->currentProfileID);
			

			$statement = $db->prepare(
					"UPDATE  " . _DB_PREFIX_ . "ddlx_evolution_generatedcss
									SET css=:css
									WHERE id_profile=:id_profile
									AND id_type = :id_type");
			
			$res = $statement->execute(
					array (
							':css' => serialize($tab_design_infos ['css']),
							':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_DDLX,
							':id_profile' => $this->currentProfileID 
					));
			
			$res .= $statement->execute(
					array (
							':css' => serialize($tab_design_infos ['css2']),
							':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_DDLX2,
							':id_profile' => $this->currentProfileID 
					));
			
			if ( isset($tab_design_infos ['user']) )
			{
				$res .= $statement->execute(
						array (
								':css' => serialize($tab_design_infos ['user']),
								':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_USER,
								':id_profile' => $this->currentProfileID 
						));
			}
			else
			{
				$res .= $statement->execute(
						array (
								':css' => serialize(''),
								':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_USER,
								':id_profile' => $this->currentProfileID 
						));
			}
			
			$res .= $statement->execute(
					array (
							':css' => serialize($tab_design_infos ['logourl']),
							':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_LOGOURL,
							':id_profile' => $this->currentProfileID 
					));
			
			$res .= $statement->execute(
					array (
							':css' => serialize($tab_design_infos ['video']),
							':id_type' => ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_VIDEO,
							':id_profile' => $this->currentProfileID 
					));
			
			$db = null;
			if ( ! $res )
			{
				$this->errors .= $this->l('Unable to save CSS in in-memory table. The design of your store shall be broken.');
			}
		}
		else
		
		{
			$this->insertCSSInfosBD($this->currentProfileID);
		}
	}

	private function css_constructor($profileId = 0)
	{
		$result = $this->dbi->executeS("SELECT css FROM " . _DB_PREFIX_ . "ddlx_evolution_generatedcss
								WHERE id_profile = " . $profileId . " 
								ORDER BY id_type");
		
		$tab_design_infos;
		
		// if inmemory css exists
		if ( ! empty($result) && isset($result [ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_VIDEO - 1]) )
		{
			$tab_design_infos ['css'] = unserialize($result [ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_DDLX - 1] ['css']);
			$tab_design_infos ['css2'] = unserialize($result [ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_DDLX2 - 1] ['css']);
			$tab_design_infos ['user'] = unserialize($result [ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_USER - 1] ['css']);
			$tab_design_infos ['logourl'] = unserialize($result [ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_LOGOURL - 1] ['css']);
			$tab_design_infos ['video'] = unserialize($result [ddlx_evolution::DDLX_EVOLUTIONX_INMEMORY_CSS_VIDEO - 1] ['css']);
		}
		else
		{
			$result = $this->dbi->execute("DELETE  FROM " . _DB_PREFIX_ . "ddlx_evolution_generatedcss
								WHERE id_profile = " . $profileId);
			$tab_design_infos = $this->insertCSSInfosBD($profileId);
		}
		
		return $tab_design_infos;
	}

	private function generateCSSHacks()
	{
		$css = '#header #search_block_top .btn.button-search:hover{ opacity: 0.7;}
				#center_column.col-sm-9 .col-md-3 .box-info-product #quantity_wanted_p,.box-cart-bottom p{	padding:0!important;}

				.product_attributes, .box-cart-bottom { padding: 10px;}
				
				.box-info-product .exclusive span,#center_column.col-sm-9 .col-md-3 .box-info-product .exclusive span {border-color:#9b9d9a}
				
				#columns{padding: 0px 7px;}

				.block .title_block, .block h4 {background:transparent; border-top:0;}

				body, .columns-container,
				.footer-container,
				.header-container,
				ul.product_list.grid > li .product-container
				{
					background: transparent;
				}
				ul.product_list.grid > li .product-container .product-image-container,
				ul.product_list.list > li .product-image-container {border:none;}

				.top-hr {   display:none }
				
				ul.product_list .color-list-container ul li a:hover {opacity:0.7}
				
				#product #center_column{ padding-top:10px;}
				#product #columns > .row, .primary_block,#home-page-tabs { margin:0}
				
				#left_column > div:first-child, #right_column > div:first-child{ margin-top: -18px!important; }
				.breadcrumb {   margin-top: 5px; }

				.col-sm-9 { width: 74%;	}				
				.primary_block{ margin-bottom: 40px !important;}
				#block_top_menu{ margin-bottom: 15px; }
				
				#layer_cart{z-index:150; }
				
				@media (max-width: 767px){
					header .row #header_logo img
					{
					margin: 0 auto;
					height: 70px;
					}
					header .row #header_logo 
					{
						padding-bottom: 15px;
					}
				}
			';
		
		return $css;
	}

	private function generateCSSButton(&$params)
	{
		// Button add to cart
		$css = '
		.button.ajax_add_to_cart_button,
		#add_to_cart .exclusive,
		#center_column.col-sm-9 .col-md-3 .box-info-product .exclusive
		{
			background:transparent;
			' .
				 $this->generateCSSAttribute_Background(null, self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM, $params ['general'] ['param1'], $params ['general'] ['param2']) . '
			border:none;
			color: ' . $params ['general'] ['param3'] . ';
		}

		#center_column.col-sm-9 .col-md-3 .box-info-product .exclusive span,
		.box-info-product .exclusive:before,#center_column.col-sm-9 .col-md-3 .box-info-product .exclusive:before
		{color:' . $params ['general'] ['param3'] . ' }
					

		.button.ajax_add_to_cart_button:hover ,
		#add_to_cart .exclusive:hover ,
		.button-container .button.ajax_add_to_cart_button:hover,
		.box-info-product .exclusive:hover,#center_column.col-sm-9 .col-md-3 .box-info-product .exclusive:hover 
		{
			background: none repeat scroll 0 0 ' . $params ['general'] ['param1'] . ';
		}
	
		.button.ajax_add_to_cart_button:hover ,
		#add_to_cart  .exclusive:before,
		#center_column.col-sm-9 .col-md-3 .box-info-product .exclusive:before
		{
			border-color: #9b9d9a #959895 #b8bab7;
		}
		
		.box-info-product .exclusive:after,#center_column.col-sm-9 .col-md-3 .box-info-product .exclusive:after {background:none;}
		';
		
		
		// Button detail
		$css .= "
		.button.lnk_view
		{
			background:transparent;
			border:none;
			text-shadow: none;
		}

		.button.lnk_view span
		{
			background:transparent;
			" .
				 $this->generateCSSAttribute_Background(null, self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM, $params ['general'] ['param7'], $params ['general'] ['param8']) . "
			color:" . $params ['general'] ['param9'] . ";
			border:none;
			text-shadow: none;
		}

		.button.lnk_view span:hover
		{
			background: none repeat scroll 0 0 " . $params ['general'] ['param7'] . ";
			border:none;
		}
		.button.lnk_view:hover
		{
			color:" . $params ['general'] ['param9'] . ";
		}
		";
		

		// Button COMPARE
		$css .= ".button.button-medium.bt_compare 
				{
					background:transparent;
					" .
				 $this->generateCSSAttribute_Background(null, self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM, $params ['general'] ['param10'], $params ['general'] ['param11']) . "

					color:" . $params ['general'] ['param12'] . ";
					border:none;
				}

				.button.button-medium.bt_compare:hover
				{
					background: none repeat scroll 0 0 " . $params ['general'] ['param10'] . ";
					border:none;
					color:" . $params ['general'] ['param12'] . ";
				}
		
				.button.button-medium.bt_compare .icon-chevron-right:before {color:" . $params ['general'] ['param12'] . ";}
		
				.button.button-medium.bt_compare span
				{
					border: 0 !important;
				}";
		
		return $css;
	}

	private function generateCSSHeader(&$params)
	{
		// LOGO
		// image param1 est une variable assignée smarty ds le tpl header.
		$css = '
			  #header_logo .logo  {
				z-index: 100;
				position:absolute;
				margin-left: ' . $params ['header'] ['param3'] . 'px;
				margin-top:' . $params ['header'] ['param4'] . 'px;
			}';
		
		$css .= "#header{  height:" . $params ['header'] ['param11'] . "px; }";
		
		// HEADER transparent, rien du tout
		if ( $params ['header'] ['param5'] == '1' )
		{
			$css .= '
				#header, .header-container
				{
					background:transparent;
				}';
		}
		// header image
		else if ( $params ['header'] ['param6'] == '' )
		{
			$body__bg_img = 'url("' . $this->imgFolder . $params ['header'] ['param12'] . '")';
			
			$css .= '
			.header-container
			{
				' .
					 $this->generateCSSAttribute_Background(null, $params ['header'] ['param10'], $params ['header'] ['param7'], $params ['header'] ['param8']) .
					 '
			}
			#header
			{
				background:url("' .
					 $this->imgFolder . $params ['header'] ['param12'] . '") ' . $params ['header'] ['param9'] . ' scroll ' . $params ['header'] ['param26'] . ' ' .
					 $params ['header'] ['param27'] . ';
			}';
		}
		// header no image, juste bg
		else
		{
			$css .= '
			.header-container
			{
				' .
					 $this->generateCSSAttribute_Background(null, $params ['header'] ['param10'], $params ['header'] ['param7'], $params ['header'] ['param8']) . '
			}
			#header {background:transparent;}
			';
		} 
				
		return $css;
	}

	private function generateCSSBackground(&$params)
	{ // body
		$css = '
				html body
				{
					' .
				 $this->generateCSSAttribute_BackgroundPRA($params ['background'] ['param3'], 
						$params ['background'] ['param4'], 
						$params ['background'] ['param5'], 
						$params ['background'] ['param6']) . '
	        	}';
		

		if ( $params ['background'] ['param2'] == '' )
		{
			$body__bg_img = 'url("' . $this->imgFolder . $params ['background'] ['param1'] . '")';
			$css .= '
					html body
					{
						' .
					 $this->generateCSSAttribute_Background($body__bg_img, $params ['background'] ['param7'], $params ['background'] ['param8'], $params ['background'] ['param9']) . '
					}';
		}
		// ---- Background CSS gradient
		else
		{
			
			$css .= 'html body 
				{
					' .
					 $this->generateCSSAttribute_Background(null, $params ['background'] ['param7'], $params ['background'] ['param8'], $params ['background'] ['param9']) . '
				}	';
		}
		
		// container
		
		if ( $params ['background'] ['param15'] != '' )
		{
			$css .= '.columns-container {background:transparent;}';
		}
		else
		{
			$css .= '
					.columns-container 
					{
						' .
					 $this->generateCSSAttribute_BackgroundPRA($params ['background'] ['param18'], 
							$params ['background'] ['param19'], 
							$params ['background'] ['param20'], 
							$params ['background'] ['param21']) . '
		        	}';
			

			if ( $params ['background'] ['param17'] == '' )
			{
				$container__bg_img = 'url("' . $this->imgFolder . $params ['background'] ['param16'] . '")';
				$css .= '
						.columns-container 
						{
							' .
						 $this->generateCSSAttribute_Background($container__bg_img, 
								$params ['background'] ['param22'], 
								$params ['background'] ['param23'], 
								$params ['background'] ['param24']) . '
						}';
			}
			// ---- Background CSS gradient
			else
			{
				$css .= '.columns-container 
						{
							' .
						 $this->generateCSSAttribute_Background(null, $params ['background'] ['param22'], $params ['background'] ['param23'], $params ['background'] ['param24']) . '
						}';
			}
		}
		
		// columns
		
		if ( $params ['background'] ['param31'] != '' )
		{
			$css .= '
			#columns {background:transparent;}';
		}
		else
		{
			$css .= '
					#columns
					{
						' .
					 $this->generateCSSAttribute_BackgroundPRA($params ['background'] ['param33'], 
							$params ['background'] ['param34'], 
							$params ['background'] ['param35'], 
							$params ['background'] ['param39']) . '
		        	}';
			

			if ( $params ['background'] ['param32'] == '' )
			{
				$container__bg_img = 'url("' . $this->imgFolder . $params ['background'] ['param30'] . '")';
				$css .= '
						#columns 
						{
							' .
						 $this->generateCSSAttribute_Background($container__bg_img, 
								$params ['background'] ['param36'], 
								$params ['background'] ['param37'], 
								$params ['background'] ['param38']) . '
						}';
			}
			// ---- Background CSS gradient
			else
			{
				$css .= '
						#columns
						{						
							' .
						 $this->generateCSSAttribute_Background(null, $params ['background'] ['param36'], $params ['background'] ['param37'], $params ['background'] ['param38']) . '
						}';
			}
		}
		
		return $css;
	}

	private function generateCSSBloc(&$params)
	{
		// header
		$css = '.block .title_block, .block h4 
				{
					' .
				 $this->generateCSSAttribute_Background(null, $params ['block'] ['param1'], $params ['block'] ['param2'], $params ['block'] ['param3']) . '
							
					border : ' . $params ['block'] ['param4'] . 'px ' .
				 $params ['block'] ['param5'] . ' ' . $params ['block'] ['param6'] . ';
	
					' .
				 $this->generateCSSAttribute_BorderRadius($params ['block'] ['param7'], $params ['block'] ['param8'], $params ['block'] ['param9'], $params ['block'] ['param10']) . '

					margin-bottom: ' . $params ['block'] ['param14'] . 'px;
					display: block;
				}';
	
		$css .= '.block .title_block, .block .title_block a, .block .title_block_myaccount a, div.block h4 a, div.exclusive h4 a, div.tags_block h4, div.products_block h4, div.block h4, #cart_block h4, div.myaccount h4 {
					font-family: ' . $params ['block'] ['param11'] . ';
					font-size: ' . $params ['block'] ['param12'] . 'px;
					color : ' . $params ['block'] ['param13'] . ';
				}
				.block .title_block a:hover
				{
					color : ' . $params ['block'] ['param15'] . ';
				}';
		
		return $css;
	}

	private function generateCSSFooter(&$params)
	{
		$css = '';
		if ( $params ['footer'] ['param2'] != '' )
		{
			$css .= '
			.footer-container {background:transparent;}';
		}
		else
		{
			$css .= '
			.footer-container
			{
					' .
					 $this->generateCSSAttribute_BackgroundPRA($params ['footer'] ['param4'], 
							$params ['footer'] ['param5'], 
							$params ['footer'] ['param6'], 
							$params ['footer'] ['param7']) . '
			}';
			
			if ( $params ['footer'] ['param3'] == '' )
			{
				$container_bg_img = 'url("' . $this->imgFolder . $params ['footer'] ['param1'] . '")';
				
				$css .= '
				.footer-container
				{
					' .
						 $this->generateCSSAttribute_Background($container_bg_img, $params ['footer'] ['param10'], $params ['footer'] ['param8'], $params ['footer'] ['param9']) . '
				}';
			}
			// ---- Background CSS gradient
			else
			{
				$css .= '
				.footer-container
				{
					' .
						 $this->generateCSSAttribute_Background(null, $params ['footer'] ['param10'], $params ['footer'] ['param8'], $params ['footer'] ['param9']) . '
				}';
			}
		}
		
		return $css;
	}

	private function generateCSSProduct(&$params)
	{
		$css = 'ul.product_list.list > li .product-container, ul.product_list.grid > li .product-container  { border-width:5px; }';
		if ( $params ['product'] ['param2'] != '' )
		{
			$css .= '
			ul.product_list.list > li .product-container, ul.product_list.grid > li .product-container {background:transparent;}';
		}
		else
		{
			$css .= '
			ul.product_list.list > li .product-container, ul.product_list.grid > li .product-container
			{
				' .
					 $this->generateCSSAttribute_BackgroundPRA($params ['product'] ['param4'], 
							$params ['product'] ['param5'], 
							$params ['product'] ['param6'], 
							$params ['product'] ['param7']) . '
        	}';
			
			// if img
			if ( $params ['product'] ['param3'] == '' )
			{
				$container_bg_img = 'url("' . $this->imgFolder . $params ['product'] ['param1'] . '")';
				
				$css .= '
				ul.product_list.list > li .product-container, ul.product_list.grid > li .product-container
				{
					' .
						 $this->generateCSSAttribute_Background($container_bg_img, $params ['product'] ['param10'], $params ['product'] ['param8'], $params ['product'] ['param9']) . '
				}';
			}
			// ---- Background CSS gradient
			else
			{
				$css .= '
				ul.product_list.list > li .product-container, ul.product_list.grid > li .product-container
				{
					' .
						 $this->generateCSSAttribute_Background(null, $params ['product'] ['param10'], $params ['product'] ['param8'], $params ['product'] ['param9']) . '
				}';
			}
		}
		
		$css .= '
				 .product-name{color:' . $params ['product'] ['param11'] . '}
				
				 .price.product-price{color:' . $params ['product'] ['param12'] . '}

				 ul.product_list .functional-buttons div.wishlist a, ul.product_list .functional-buttons div.wishlist label ,
				 ul.product_list .functional-buttons div.wishlist a:before
				 {color:' . $params ['product'] ['param13'] . '}

				 ul.product_list .functional-buttons div.wishlist a:hover, ul.product_list .functional-buttons div.wishlist label:hover,
				 ul.product_list .functional-buttons div.wishlist a:hover:before
				 {
				 	color:' . $params ['product'] ['param14'] . '
				 }
				 ul.product_list .functional-buttons div.compare a, ul.product_list .functional-buttons div.compare label ,
				 ul.product_list .functional-buttons div.compare a:before
				 {
				 	color:' . $params ['product'] ['param15'] . '
				 }
				 ul.product_list .functional-buttons div.compare a:hover, ul.product_list .functional-buttons div.compare label:hover ,
				 ul.product_list .functional-buttons div.compare a:hover:before
				 {
				 	color:' . $params ['product'] ['param16'] . '
				}
				.old-price.product-price
				{
					color:' . $params ['product'] ['param17'] . '
				}
		';
		
		// product page
		$css .= '
				#product #center_column
				{
				' .
				 $this->generateCSSAttribute_Background(null, $params ['product'] ['param20'], $params ['product'] ['param21'], $params ['product'] ['param22']) . '
				}
						
				.primary_block .pb-center-column h1,
				.primary_block .pb-center-column #product_reference span,
				.primary_block .pb-center-column label,
				#pQuantityAvailable span
				{
					color :' . $params ['product'] ['param23'] . ';
				}
				
				.page-product-box table
				{
					color:#333;
				}			
							
				.primary_block .pb-center-column,
				.pb-center-column #short_description_block
				{
					color :' . $params ['product'] ['param24'] . ';
				}
				.page-product-box
				{
					color:' . $params ['product'] ['param31'] . '  ;
				}
				h3.page-product-heading 
				{
					background:transparent;
				}
				h3.page-product-heading 
				{
					' .
				 $this->generateCSSAttribute_Background(null, $params ['product'] ['param25'], $params ['product'] ['param26'], $params ['product'] ['param27']) . '
					color:' . $params ['product'] ['param28'] . '  ;
					font-family: ' . $params ['product'] ['param29'] . ';
					font-size: ' . $params ['product'] ['param30'] . ';
					border:none;
				}

		';
		
		$css .= '		
		#home-page-tabs 
		{
			background: transparent;
			' .
				 $this->generateCSSAttribute_Background(null, $params ['product'] ['param33'], $params ['product'] ['param34'], $params ['product'] ['param35']) .
				 '
			' .
				 $this->generateCSSAttribute_BorderRadius($params ['product'] ['param40'], 
						$params ['product'] ['param41'], 
						$params ['product'] ['param42'], 
						$params ['product'] ['param43']) . '
		}
		#home-page-tabs > li  
		{
			border-left: 1px solid ' . $params ['product'] ['param36'] . ';
		}
		#home-page-tabs > li a 
		{
		    color: ' . $params ['product'] ['param36'] . ';
		    font-family: ' . $params ['product'] ['param37'] . ';
		    font-size: ' . $params ['product'] ['param44'] . 'px; 
		}

		#home-page-tabs > li.active a, #home-page-tabs > li a:hover 
		{
			color:' . $params ['product'] ['param38'] . '  ;
			background:' . $params ['product'] ['param39'] . '  ;
		}
		
		';
		
		return $css;
	}

	private function generateCSSNavigation(&$params)
	{
		$css = '
				#header .sf-menu
				{
					background:transparent;
					' .
				 $this->generateCSSAttribute_Background(null, $params ['navigation'] ['param3'], $params ['navigation'] ['param1'], $params ['navigation'] ['param2']) . '
					border: ' . $params ['navigation'] ['param4'] .
				 'px ' . $params ['navigation'] ['param5'] . ' ' . $params ['navigation'] ['param6'] .
				 ';
					' .
				 $this->generateCSSAttribute_BorderRadius($params ['navigation'] ['param7'], 
						$params ['navigation'] ['param8'], 
						$params ['navigation'] ['param9'], 
						$params ['navigation'] ['param10']) . '
				}
				#header .sf-menu ul li:first-child
				{
					border-left:none;
				}
				#header .sf-menu > li:first-child > a
				{
					border-radius: ' . $params ['navigation'] ['param7'] . 'px 0 0 ' .
				 $params ['navigation'] ['param10'] . 'px;
					border-bottom:none;
				}


				';
		
		if ( $params ['navigation'] ['param11'] )
		{
			$css .= '
				#header .sf-menu > li:last-child > a
				{
					border-radius: 0 ' . $params ['navigation'] ['param8'] . 'px ' .
					 $params ['navigation'] ['param9'] . 'px 0;
					border-bottom:none;
				}';
		}
		// main menu
		$css .= '#header #block_top_menu
				{
					padding-top:' . $params ['navigation'] ['param12'] . 'px;
				}
				#header .sf-menu > li > a
				{
					border:none;
					color:' . $params ['navigation'] ['param13'] . ';
					font-family:' . $params ['navigation'] ['param24'] . ';
					font-size: ' . $params ['navigation'] ['param16'] . 'px; 
				}
				#header .sf-menu > li.sfHover > a, .sf-menu > li > a:hover, .sf-menu > li.sfHoverForce > a,
				#header .sf-menu > li > a:hover,sf-menu > li.sfHover > a:hover,sf-menu > li.sfHoverForce > a:hover
				{
					color:' . $params ['navigation'] ['param14'] . '!important;
					background: ' . $params ['navigation'] ['param15'] . ';
				}
				';
		
		$css .= '
				.sf-menu > li > ul
				{
					background:transparent;
					' .
				 $this->generateCSSAttribute_Background(null, $params ['navigation'] ['param17'], $params ['navigation'] ['param18'], $params ['navigation'] ['param19']) . '	
				}
				#header .sf-menu > li > ul > li > a
				{
					color:' . $params ['navigation'] ['param20'] . ';
				}
				#header .sf-menu > li > ul > li > a:hover
				{
					color:' . $params ['navigation'] ['param21'] . ';
				}
				#header .sf-menu li li li a:before, #header .sf-menu li li li a
				{
					color:' . $params ['navigation'] ['param22'] . ';
				}
				#header .sf-menu li li li a:hover:before, #header .sf-menu li li li a:hover
				{
					color:' . $params ['navigation'] ['param23'] . ';
				}
				';
		
		return $css;
	}

	/**
	 * Generate code css for pos, repeat, attach
	 *
	 * @param string $positionX        	
	 * @param string $positionY        	
	 * @param string $repeat        	
	 * @param string $attachment        	
	 * @return string
	 */
	private function generateCSSAttribute_BackgroundPRA($positionX = null, $positionY = null, $repeat = null, $attachment = null)
	{
		$positionX = ( $positionX == null ) ? 'center' : $positionX;
		$positionY = ( $positionY == null ) ? 'center' : $positionY;
		$repeat = ( $repeat == null ) ? 'unset' : $repeat;
		$attachment = ( $attachment == null ) ? 'unset' : $attachment;
		
		return 'background-position :' . $positionX . ' ' . $positionY . ';
		background-repeat :' . $repeat . ';
		background-attachment:' . $attachment . ';';
	}

	private function generateCSSAttribute_Background($URL = null, $sens = null, $gradient1 = null, $gradient2 = null)
	{
		$URL = ( $URL == null ) ? '' : $URL . ',';
		$sens = ( $sens == null ) ? 'bottom' : $sens;
		$gradient1 = ( $gradient1 == null ) ? 'rgba(255,255,255,0)' : $gradient1;
		$gradient2 = ( $gradient2 == null ) ? 'rgba(255,255,255,0)' : $gradient2;
		
		return 'background-image: ' .
				 $URL . ' linear-gradient( to ' . $sens . ', ' . $gradient2 . ' 19%, ' . $gradient1 . ' 74%);
		background-image: ' . $URL . ' -o-linear-gradient(' . $sens . ', ' . $gradient2 .
				 ' 19%, ' . $gradient1 . ' 74%);
		background-image: ' . $URL . ' -moz-linear-gradient(' . $sens . ', ' . $gradient2 .
				 ' 19%, ' . $gradient1 . ' 74%);
		background-image: ' . $URL . ' -webkit-linear-gradient(' . $sens . ', ' .
				 $gradient2 . ' 19%, ' . $gradient1 . ' 74%);
		background-image: ' . $URL . ' -ms-linear-gradient(' . $sens . ', ' . $gradient2 .
				 ' 19%, ' . $gradient1 . ' 74%);';
	}

	private function generateCSSAttribute_BorderRadius($top_left, $top_right, $bottom_right, $bottom_left)
	{
		return '
			-webkit-border-top-left-radius: ' . $top_left . 'px;
			-webkit-border-top-right-radius: ' . $top_right . 'px;
			-webkit-border-bottom-right-radius: ' . $bottom_right . 'px;
			-webkit-border-bottom-left-radius: ' . $bottom_left . 'px;
			-moz-border-radius-topleft: ' . $top_left . 'px;
			-moz-border-radius-topright: ' . $top_right . 'px;
			-moz-border-radius-bottomright: ' . $bottom_right . 'px;
			-moz-border-radius-bottomleft: ' . $bottom_left . 'px;
			border-top-left-radius: ' . $top_left . 'px;
			border-top-right-radius: ' . $top_right . 'px;
			border-bottom-right-radius: ' . $bottom_right . 'px;
			border-bottom-left-radius: ' . $bottom_left . 'px;';
	}

	function hookHeader($params)
	{
		global $smarty;
		
		$this->checkCurrentProfile();
		
		$params = $this->css_constructor($this->currentProfileID);
		
		$css = '<link rel="stylesheet" media="all" href="' . $this->realpath . 'css/ddlx.css" />
				<style type="text/css" media="all">' . $params ['css'] . $params ['css2'] . $params ['user'] . '</style>';
		
		if ( isset($params ['video']) && $params ['video'] ['url'] != '' )
		{
			$this->context->controller->addJs($this->realpath . 'js/okvideo.js');
			
			$ddlx_video = '

<script type="text/javascript">
    $(document).ready( function(){
        $.okvideo({ source: "' . $params ['video'] ['url'] . '",
					autoplay:true,
                    volume: ' . $params ['video'] ['volume'] . ',
                    loop: ' . $params ['video'] ['repeat'] .
					 ',
                    hd:true,
                    adproof: true,
                    annotations: false,
                    onFinished: function() { console.log("finished") },
                    unstarted: function() { console.log("unstarted") },
                    onReady: function() { console.log("onready") },
                    onPlay: function() { console.log("onplay") },
                    onPause: function() { console.log("pause") },
                    buffering: function() { console.log("buffering") },
                    cued: function() { console.log("cued") },
					/*target: $("#container"),*/
                 });
    });
</script>
<style>@media screen and (-webkit-min-device-pixel-ratio:0) { ul.product_list.list > li .product-container,ul.product_list.grid > li .product-container{ background-attachment: scroll; } }</style>';
		}
		else
		{
			$ddlx_video = '';
		}
		
		$this->context->smarty->assign(array (
				'ddlx_css' => $css,
				'ddlx_video' => $ddlx_video,
				'ddlx_logo_url' => $params ['logourl'] 
		));
	}
	

	// /////////////////////// DISPLAY FUNCTIONS ////////////////////////////
	const DDLX_EVOLUTIONX_BORDER_SOLID = 'solid';
	const DDLX_EVOLUTIONX_BORDER_DASHED = 'dashed';
	const DDLX_EVOLUTIONX_BORDER_HIDDEN = 'hidden';
	const DDLX_EVOLUTIONX_BORDER_NONE = 'none';
	const DDLX_EVOLUTIONX_BORDER_DOTTED = 'dotted';
	const DDLX_EVOLUTIONX_BORDER_DOUBLE = 'double';
	const DDLX_EVOLUTIONX_BORDER_GROOVE = 'groove';
	const DDLX_EVOLUTIONX_BORDER_RIDGE = 'ridge';
	const DDLX_EVOLUTIONX_BORDER_INSET = 'inset';
	const DDLX_EVOLUTIONX_BORDER_OUTSET = 'outset';
	const DDLX_EVOLUTIONX_FONT_COMIC = 'Comic Sans MS';
	const DDLX_EVOLUTIONX_FONT_TIMES = 'Times New Roman';
	const DDLX_EVOLUTIONX_FONT_ARIALB = 'Arial black';
	const DDLX_EVOLUTIONX_FONT_ARIAL = 'Arial';
	const DDLX_EVOLUTIONX_FONT_VERDANA = 'Verdana';
	const DDLX_EVOLUTIONX_FONT_LUCIDA = 'lucida console';
	const DDLX_EVOLUTIONX_FONT_HELVETICA = 'helvetica';
	const DDLX_EVOLUTIONX_FONT_CENTURYGOTHIC = 'century gothic';
	const DDLX_EVOLUTIONX_FONT_SANSSERIF = 'sans-serif';
	const DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM = 'bottom';
	const DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM_LEFT = 'left bottom';
	const DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM_RIGHT = 'right bottom';
	const DDLX_EVOLUTIONX_FONT_GRADIENT_RIGHT = 'right';

	function displayPanel()
	{
		$param = $this->getProfileParams($this->currentProfileID);
		
		$onglet_actif = Tools::getValue("onglet", "onglet_1");
		
		// load scripts and css
		$contentToReturn = '
		<link rel=stylesheet type="text/css" href="' . $this->realpath . 'js/jquery-mini/jquery.miniColors.css" />';
		
		// CodeMirror, gestion onglet CSS, ne pas enlever !
		
		$contentToReturn .= '
		<link rel="stylesheet" href="' . $this->realpath . 'js/CodeMirror/theme/eclipse.css" type="text/css"/>
		<link rel="stylesheet" href="' . $this->realpath . 'js/CodeMirror/lib/codemirror.css" type="text/css"/>


		<script type="text/javascript" src="' . $this->realpath . 'js/CodeMirror/lib/codemirror.js"></script>
		<script type="text/javascript" src="' . $this->realpath . 'js/CodeMirror/mode/javascript/javascript.js"></script>
		<style>.CodeMirror {
			background: #F9F9F9;
			width: 770px;
		 }
		 </style>';
		
		// # END CodeMirror
		
		// Color picker
		$contentToReturn .= '
		<script src="' . $this->realpath . 'js/jquery-mini/jquery.miniColors.js" type="text/javascript"></script>
		<script src="' . $this->realpath . 'js/ddlx.js" type="text/javascript"></script>

		<link rel="Stylesheet" type="text/css" href="' . $this->realpath . 'css/spectrum.css">
		<script type="text/javascript" src="' . $this->realpath . 'js/spectrum.js"></script>


		<script type="text/javascript">

			$(document).ready( function(){
			    $(".jquery_mini").miniColors();
			    $("input.delete").click(function(){
					if(confirm("' . $this->l('Are you sure to delete this design ?') . '")) return true;
					else return false;
				});
            });

	    </script>
		';
		
		$contentToReturn .= '
    <link rel="Stylesheet" type="text/css" href="' . $this->realpath . 'ddlxadmin.css">
	<br />
	<h3 style="text-align:center;">' . $this->l('Current active profile :') . ' <i>' .
				 $this->currentProfileName . '</i> </h3><br />



<div class="systeme_onglets">

        <div class="onglets">
            <span class="onglet tab_inactif" id="onglet_1">' . $this->l('Header') . '</span>
            <span class="onglet tab_inactif" id="onglet_2">' . $this->l('Navigation') . '</span>
            <span class="onglet tab_inactif" id="onglet_3">' . $this->l('Background') . '</span>
            <span class="onglet tab_inactif" id="onglet_4">' . $this->l('Blocs') . '</span>
            <span class="onglet tab_inactif" id="onglet_5">' . $this->l('Footer') . '</span>
            <span class="onglet tab_inactif" id="onglet_6">' . $this->l('Products') . '</span>
			<span class="onglet tab_inactif" id="onglet_7">' . $this->l('Button') . '</span>
			<span class="onglet tab_inactif" id="onglet_8">' . $this->l('Css & tips') . '</span>
			<span class="onglet tab_inactif" id="onglet_9">' . $this->l('Profiles') . '</span>
			<span class="onglet tab_inactif" id="onglet_10">' . $this->l('Information') . '</span>
        </div>

	<div class="contenu_onglets">

            <div class="contenu_onglet" id="cont_onglet_1">
            	' . $this->configHeader($param ['header'], '#1') . '
            </div>

			<div class="contenu_onglet" id="cont_onglet_2">
           		' . $this->configNavigation($param ['navigation'], '#2') . '
            </div>

           	<div class="contenu_onglet" id="cont_onglet_3">
           		' . $this->configBackground($param ['background'], '#3') . '
           	</div>

           	<div class="contenu_onglet" id="cont_onglet_4">
           		' . $this->configBlock($param ['block'], '#4') . '
           	</div>

           	<div class="contenu_onglet" id="cont_onglet_5">
           		' . $this->configFooter($param ['footer'], '#5') . '
           	</div>
			<div class="contenu_onglet" id="cont_onglet_6">
           		' . $this->configProduct($param ['product'], '#6') . '
           	</div>
			<div class="contenu_onglet" id="cont_onglet_7">
           		' . $this->configButton($param ['general'], '#7') . '
           	</div>
           				
           				
           ';
		
		if ( ! isset($param ['css']) )
		{
			$param ['css'] = Array ();
		}
		$contentToReturn .= '

		  <div class="contenu_onglet" id="cont_onglet_8"> ' . $this->configCss($param ['css'], '#8') . ' </div>
		  		
		  <div class="contenu_onglet" id="cont_onglet_9"> ' . $this->configProfile('#9') . ' </div>
		  		
		  <div class="contenu_onglet" id="cont_onglet_10"> ' . $this->configLicence('#10') . ' </div>';
		

		$contentToReturn .= ' </div>
    </div>


    <script type="text/javascript">
    $( ".tab_inactif" ).click(function()
      {
         $(".onglet").removeClass( "tab_actif" ).addClass( "tab_inactif" );
         $(".contenu_onglet").hide();
         $( "#cont_" + $(this).attr("id") ).show();
         $(this).removeClass( "tab_inactif" ).addClass( "tab_actif" );
      }
	 );';
		
		$contentToReturn .= '
     $( document ).ready(function() {
    	$("#cont_' . $onglet_actif . '").show();
        $("#' . $onglet_actif . '").removeClass( "tab_inactif" ).addClass( "tab_actif" );

        $("#cont_onglet_8").show();';
		
		if ( $onglet_actif != "onglet_8" )
		{
			$contentToReturn .= '$("#cont_onglet_8").hide();';
		}
		
		$contentToReturn .= '});
	</script>					
	';
		

		$contentToReturn .= $this->addJavascripts();
		
		return $contentToReturn;
	}
	
	// TODO
	private function addJavascripts()
	{
		$content = "
	<script type=\"text/javascript\">

		function generateGradientCode( sens, color_end, color_start )
		{
			content = 'linear-gradient(to ' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%);	background-image: -o-linear-gradient(' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%);	background-image: -moz-linear-gradient(' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%);	background-image: -webkit-linear-gradient(' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%);	background-image: -ms-linear-gradient(' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%); ';	
			return content;
		}
		
		function generateTransparentCode()
		{
			content = 'background-image:url(" . $this->realpath .
				 "img/data/blank.gif);';
			return content;
		}
					
		function generateGradientOnlyCode( sens, color_end, color_start )
		{
			content = 'background-image: ' + generateGradientCode( sens, color_end, color_start );
			return content;
		}

		function generateImgAndGradientCode( sens, color_end, color_start, imgName )
		{
			content = 'background-image:url(" .
				 $this->realpath . "img/" . $this->currentProfileName .
				 "/' + imgName + '), linear-gradient(to ' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%);	background-image:url(" . $this->realpath . "img/" .
				 $this->currentProfileName . "/' + imgName + '), -o-linear-gradient(' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%);	background-image:url(" .
				 $this->realpath . "img/" . $this->currentProfileName .
				 "/' + imgName + '), -moz-linear-gradient(' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%);	background-image:url(" . $this->realpath . "img/" .
				 $this->currentProfileName . "/' + imgName + '), -webkit-linear-gradient(' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%);	background-image:url(" .
				 $this->realpath . "img/" . $this->currentProfileName . "/' + imgName + '), -ms-linear-gradient(' + sens + ', ' + color_start + ' 19%, ' + color_end + ' 74%); '; 
			return content;
		}
					
		(function($){

		    $.fn.extend({ 
		
		        addTemporaryClass: function(className, duration) {
		            var elements = this;
		            setTimeout(function() {
		                elements.removeClass(className);
		            }, duration);
		
		            return this.each(function() {
		                $(this).addClass(className);
		            });
		        }
		    });
		
		})(jQuery);			

		</script>";
		

		return $content;
	}

	
	/**
	 *
	 * @param String $paramGradientSensNumber        	
	 * @param Array $tabParam        	
	 * @return string
	 */
	private function getGradientInputForHTML($paramGradientSensNumber, &$tabParam)
	{
		$tobottom = ( $tabParam [$paramGradientSensNumber] == self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM ) ? 'checked="checked"' : '';
		$tobottomright = ( $tabParam [$paramGradientSensNumber] == self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM_RIGHT ) ? 'checked="checked"' : '';
		$tobottomleft = ( $tabParam [$paramGradientSensNumber] == self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM_LEFT ) ? 'checked="checked"' : '';
		$toright = ( $tabParam [$paramGradientSensNumber] == self::DDLX_EVOLUTIONX_FONT_GRADIENT_RIGHT ) ? 'checked="checked"' : '';
		
		return '
		<label><img src="' . $this->realpath . 'img/vertical.png"/> 
			<input type="radio" name="' . $paramGradientSensNumber .
				 '" value="' . self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM . '" ' . $tobottom . '/>
			 
		</label> &nbsp; |
		<label><img src="' . $this->realpath . 'img/tlbr.png"/> 
			<input type="radio" name="' . $paramGradientSensNumber .
				 '"value="' . self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM_RIGHT . '" ' . $tobottomright . '/>
			
		</label>&nbsp; |
		<label><img src="' . $this->realpath . 'img/trbl.png"/> 
			<input type="radio" name="' . $paramGradientSensNumber .
				 '" value="' . self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM_LEFT . '" ' . $tobottomleft . '/>
			
		</label>&nbsp; |
		<label> <img src="' . $this->realpath . 'img/horizontal.png"/> 
			<input type="radio" name="' . $paramGradientSensNumber . '" value="' .
				 self::DDLX_EVOLUTIONX_FONT_GRADIENT_RIGHT . '" ' . $toright . '/>
			
		</label>';
	}

	private function getBorderStyleInputForHTML($paramBorderStyle, &$tabParam)
	{
		$border_none = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_NONE ) ? 'selected' : '';
		$border_dashed = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_DASHED ) ? 'selected' : '';
		$border_dotted = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_DOTTED ) ? 'selected' : '';
		$border_double = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_DOUBLE ) ? 'selected' : '';
		$border_groove = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_GROOVE ) ? 'selected' : '';
		$border_hidden = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_HIDDEN ) ? 'selected' : '';
		$border_inset = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_INSET ) ? 'selected' : '';
		$border_outset = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_OUTSET ) ? 'selected' : '';
		$border_ridge = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_RIDGE ) ? 'selected' : '';
		$border_solid = ( $tabParam [$paramBorderStyle] == self::DDLX_EVOLUTIONX_BORDER_SOLID ) ? 'selected' : '';
		return '
			<option value="' . self::DDLX_EVOLUTIONX_BORDER_NONE . '" ' .
				 $border_none . '>' . self::DDLX_EVOLUTIONX_BORDER_NONE . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_BORDER_DASHED .
				 '" ' . $border_dashed . '>' . self::DDLX_EVOLUTIONX_BORDER_DASHED . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_BORDER_DOTTED .
				 '" ' . $border_dotted . '>' . self::DDLX_EVOLUTIONX_BORDER_DOTTED . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_BORDER_DOUBLE .
				 '" ' . $border_double . '>' . self::DDLX_EVOLUTIONX_BORDER_DOUBLE . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_BORDER_GROOVE .
				 '" ' . $border_groove . '>' . self::DDLX_EVOLUTIONX_BORDER_GROOVE . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_BORDER_HIDDEN .
				 '" ' . $border_hidden . '>' . self::DDLX_EVOLUTIONX_BORDER_HIDDEN . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_BORDER_INSET .
				 '" ' . $border_inset . '>' . self::DDLX_EVOLUTIONX_BORDER_INSET . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_BORDER_OUTSET .
				 '" ' . $border_outset . '>' . self::DDLX_EVOLUTIONX_BORDER_OUTSET . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_BORDER_RIDGE .
				 '" ' . $border_ridge . '>' . self::DDLX_EVOLUTIONX_BORDER_RIDGE . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_BORDER_SOLID .
				 '" ' . $border_solid . '>' . self::DDLX_EVOLUTIONX_BORDER_SOLID . '</option>
 	  		';
	}

	private function getFontFamilyInputForHTML($paramFontFamily, &$tabParam)
	{
		$font_arial = ( $tabParam [$paramFontFamily] == self::DDLX_EVOLUTIONX_FONT_ARIAL ) ? 'selected' : '';
		$font_arialb = ( $tabParam [$paramFontFamily] == self::DDLX_EVOLUTIONX_FONT_ARIALB ) ? 'selected' : '';
		$font_century = ( $tabParam [$paramFontFamily] == self::DDLX_EVOLUTIONX_FONT_CENTURYGOTHIC ) ? 'selected' : '';
		$font_comic = ( $tabParam [$paramFontFamily] == self::DDLX_EVOLUTIONX_FONT_COMIC ) ? 'selected' : '';
		$font_helvetica = ( $tabParam [$paramFontFamily] == self::DDLX_EVOLUTIONX_FONT_HELVETICA ) ? 'selected' : '';
		$font_lucida = ( $tabParam [$paramFontFamily] == self::DDLX_EVOLUTIONX_FONT_LUCIDA ) ? 'selected' : '';
		$font_sanserif = ( $tabParam [$paramFontFamily] == self::DDLX_EVOLUTIONX_FONT_SANSSERIF ) ? 'selected' : '';
		$font_times = ( $tabParam [$paramFontFamily] == self::DDLX_EVOLUTIONX_FONT_TIMES ) ? 'selected' : '';
		$font_verdana = ( $tabParam [$paramFontFamily] == self::DDLX_EVOLUTIONX_FONT_VERDANA ) ? 'selected' : '';
		
		return '
			<option value="' . self::DDLX_EVOLUTIONX_FONT_ARIAL . '" ' .
				 $font_arial . '>' . self::DDLX_EVOLUTIONX_FONT_ARIAL . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_FONT_ARIALB . '" ' .
				 $font_arialb . '>' . self::DDLX_EVOLUTIONX_FONT_ARIALB . '</option>
 	  		<option value="' .
				 self::DDLX_EVOLUTIONX_FONT_CENTURYGOTHIC . '" ' . $font_century . '>' . self::DDLX_EVOLUTIONX_FONT_CENTURYGOTHIC . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_FONT_COMIC . '" ' .
				 $font_comic . '>' . self::DDLX_EVOLUTIONX_FONT_COMIC . '</option>
 	  		<option value="' .
				 self::DDLX_EVOLUTIONX_FONT_HELVETICA . '" ' . $font_helvetica . '>' . self::DDLX_EVOLUTIONX_FONT_HELVETICA . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_FONT_LUCIDA . '" ' .
				 $font_lucida . '>' . self::DDLX_EVOLUTIONX_FONT_LUCIDA . '</option>
 	  		<option value="' .
				 self::DDLX_EVOLUTIONX_FONT_SANSSERIF . '" ' . $font_sanserif . '>' . self::DDLX_EVOLUTIONX_FONT_SANSSERIF . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_FONT_TIMES . '" ' .
				 $font_times . '>' . self::DDLX_EVOLUTIONX_FONT_TIMES . '</option>
 	  		<option value="' . self::DDLX_EVOLUTIONX_FONT_VERDANA .
				 '" ' . $font_verdana . '>' . self::DDLX_EVOLUTIONX_FONT_VERDANA . '</option>
			';
	}

	/**
	 *
	 * @param String $paramBackgroundRepeatNumber        	
	 * @param Array $tabParam        	
	 * @return string
	 */
	private function getBackgroundRepeatInputForHTML($paramBackgroundRepeatNumber, &$tabParam)
	{
		$no = ( $tabParam [$paramBackgroundRepeatNumber] == 'no-repeat' ) ? 'checked="checked"' : '';
		$x = ( $tabParam [$paramBackgroundRepeatNumber] == 'repeat-x' ) ? 'checked="checked"' : '';
		$y = ( $tabParam [$paramBackgroundRepeatNumber] == 'repeat-y' ) ? 'checked="checked"' : '';
		$yes = ( $tabParam [$paramBackgroundRepeatNumber] == 'repeat' ) ? 'checked="checked"' : '';
		
		return '
		<label>
			<input type="radio" name="' . $paramBackgroundRepeatNumber . '" value="no-repeat" ' . $no . '/>
 			' . $this->l('none') . '
 		</label>
 		<label>
 			<input type="radio" name="' . $paramBackgroundRepeatNumber . '" value="repeat-x" ' . $x . '/>
			' . $this->l('horizontaly') . '
		</label>
		<label>
			<input type="radio" name="' . $paramBackgroundRepeatNumber . '" value="repeat" ' . $yes . '/>
			' . $this->l('both directions') . '
		</label>
		<label>
			<input type="radio" name="' . $paramBackgroundRepeatNumber . '" value="repeat-y" ' . $y . '/>
			' . $this->l('verticaly') . '
		</label>';
	}

	private function getBackgroundPosHorizontalInputForHTML($paramBackgroundPosHorizontalNumber, &$tabParam)
	{
		$left = ( $tabParam [$paramBackgroundPosHorizontalNumber] == 'left' ) ? 'checked="checked"' : '';
		$center = ( $tabParam [$paramBackgroundPosHorizontalNumber] == 'center' ) ? 'checked="checked"' : '';
		$right = ( $tabParam [$paramBackgroundPosHorizontalNumber] == 'right' ) ? 'checked="checked"' : '';
		
		return '<img src="' . $this->realpath . 'img/horizontal.png"/> ' . $this->l('- Horizontal') . ' :
		
		<label>
			<input type="radio" name="' . $paramBackgroundPosHorizontalNumber . '" value="left" ' . $left . '/>
			' . $this->l('left') . '
		</label>
		<label>
			<input type="radio" name="' . $paramBackgroundPosHorizontalNumber . '" value="center" ' . $center . '/>
			' . $this->l('center') . '
		</label>
		<label>
			<input type="radio" name="' . $paramBackgroundPosHorizontalNumber . '" value="right" ' . $right . '/>
			' . $this->l('right') . '
		</label>';
	}

	private function getBackgroundPosVerticalInputForHTML($paramBackgroundPosVerticalNumber, &$tabParam)
	{
		$top = ( $tabParam [$paramBackgroundPosVerticalNumber] == 'top' ) ? 'checked="checked"' : '';
		$center = ( $tabParam [$paramBackgroundPosVerticalNumber] == 'center' ) ? 'checked="checked"' : '';
		$bottom = ( $tabParam [$paramBackgroundPosVerticalNumber] == 'bottom' ) ? 'checked="checked"' : '';
		
		return '<img src="' . $this->realpath . 'img/vertical.png"/> ' . $this->l('- Vertical') . ' :
	
	<label>
		<input type="radio" name="' . $paramBackgroundPosVerticalNumber . '" value="top" ' . $top . '/>
		' . $this->l('top') . '
	</label>
	<label>
		<input type="radio" name="' . $paramBackgroundPosVerticalNumber . '" value="center" ' . $center . '/>
		' . $this->l('center') . '
	</label>
	<label>
		<input type="radio" name="' . $paramBackgroundPosVerticalNumber . '" value="bottom" ' . $bottom . '/>
		' . $this->l('bottom') . '
	</label>';
	}

	private function getSpectrumJSForHTML($selector, $paramcolor, $jsMethodToCall)
	{
		return "$( '" . $selector . "' ).spectrum(
		{
			allowEmpty: false,
			preferredFormat: 'rgb',
			showAlpha: true,
			showInput: true,
			clickoutFiresChange: true,
			chooseText:'" . $this->l('Validate') . "',
			cancelText:'" . $this->l('Cancel') . "',

			move: function(color)
			{
				" . $paramcolor . " = color.toRgbString();
				" . $jsMethodToCall . "();
			},
			change: function(color)
			{
			    " . $paramcolor . " = color.toRgbString();
				" . $jsMethodToCall . "();
			}

		} );";
	}
	// ####################### CONFIG BOUTON ##############################
	private function configButton($generalArr, $idTab)
	{
		$output = '
<div class="container-fluid">
			<form name="configButton" action="' . $_SERVER ['REQUEST_URI'] . $idTab . '" method="post" enctype="multipart/form-data">
			<input type="hidden" name="form" value="general" />
			<input type="hidden" name="onglet" value="onglet_7" />

			<h1>' . $this->l('Button configuration') . '</h1>
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
			<hr>';
		
		$output .= $this->cfgButtonAddToCart($generalArr) . '<hr>';
		
		$output .= $this->cfgButtonCommand($generalArr) . '<hr>';
		
		$output .= $this->cfgButtonDetail($generalArr) . '<hr>';
		
		$output .= '<div class="row">' . $this->cfgButtonCompare($generalArr) . '</div><hr>

			
			<br><br><input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />

		</form>
</div>';
		return $output;
	}

	private function cfgButtonAddToCart(&$generalArr)
	{
		// add to cart
		$gradientcolor1 = $generalArr ['param1'] == null ? '#FFF' : $generalArr ['param1'];
		$gradientcolor2 = $generalArr ['param2'] == null ? '#FFF' : $generalArr ['param2'];
		$textcolor = $generalArr ['param3'] == null ? '#FFF' : $generalArr ['param3'];
		

		$content = '
		<div class="row">
			<div class="col-md-12">
				<div class="button-container" style="">
					<a title="' . $this->l("Add to cart") . '" rel="nofollow" class="button ajax_add_to_cart_button btn btn-default">
						<span>' . $this->l("Add to cart") . '</span>
					</a>
				</div>
			</div>
		</div><br/>';
		
		$content .= '
		<div class="row">
			<div class="col-md-1">
				<h3 style="margin:0 15px 0 0">' . $this->l('Gradient: ') . '</h3>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('color 1: ') . '</label>
				<input name="param1" id="button_addtocart_gc1" type="text" value="' . $gradientcolor1 . '"/>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('color 2: ') . '</label>
				<input name="param2" id="button_addtocart_gc2" type="text" value="' . $gradientcolor2 . '"/>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('text color: ') . '</label>
				<input name="param3" id="button_textcolor" type="text" value="' . $textcolor . '"/>
			</div>
		</div>';
		

		$content .= "<script>
		var button_addtocart_preview = $('.button.ajax_add_to_cart_button span');
		var button_addtocart_gc1 = 	$( '#button_addtocart_gc1' ).val();
		var button_addtocart_gc2 = 	$( '#button_addtocart_gc2' ).val();
		var button_textcolor = $( '#button_textcolor' ).val();

		$(document).ready( applyStyle_button_addtocart );		
		
		function applyStyle_button_addtocart()
		{
			button_addtocart_preview.attr('style', 'border:none;color: ' + button_textcolor + ';' + generateGradientOnlyCode('" .
				 self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM . "', button_addtocart_gc1 , button_addtocart_gc2)  );
		}

		" .
				 $this->getSpectrumJSForHTML('#button_addtocart_gc1', 'button_addtocart_gc1', 'applyStyle_button_addtocart') . "
		
		" .
				 $this->getSpectrumJSForHTML('#button_addtocart_gc2', 'button_addtocart_gc2', 'applyStyle_button_addtocart') . "

		" .
				 $this->getSpectrumJSForHTML('#button_textcolor', 'button_textcolor', 'applyStyle_button_addtocart') . "

		$('.button.ajax_add_to_cart_button span').hover(
			function ()
			{
			    $(this).css('background', button_addtocart_gc1);
			 },
			 function ()
			 {
				applyStyle_button_addtocart();
			 }
		);
		</script>
";
		
		return $content;
	}

	private function cfgButtonCommand(&$generalArr)
	{
		$commandgc1 = $generalArr ['param4'] == null ? '#FFF' : $generalArr ['param4'];
		$commandgc2 = $generalArr ['param5'] == null ? '#FFF' : $generalArr ['param5'];
		$commandtext = $generalArr ['param6'] == null ? '#FFF' : $generalArr ['param6'];
		
		$content = '
		
		<div class="ddlxaviable">' . $this->l('Only available in pro edition : ') . '  <a href="http://www.evolution-x.fr" target="_blank">' . $this->l('get pro now') . ' </a></div>  

				
		<div class="row">
			<div class="col-md-12">
				<div class="cart_block block exclusive">
					<div class="block_content">
						<div class="cart_block_list">
							<p class="cart-buttons">
								<a rel="nofollow" title="' . $this->l('Command') . '" class="btn btn-default button button-small button_order_cart">
									<span>' . $this->l('Command') . '<i class="icon-chevron-right right"></i></span>
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>';
		
		$content .= '
		<div class="row">
			<div class="col-md-1">
				<h3 style="margin:0 15px 0 0">' . $this->l('Gradient: ') . '</h3>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('color 1: ') . '</label>
				<input name="param4" id="button_command_gc1" type="text" value="' . $commandgc1 . '"/>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('color 2: ') . '</label>
				<input name="param5" id="button_command_gc2" type="text" value="' . $commandgc2 . '"/>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('text color: ') . '</label>
				<input name="param6" id="button_command_textcolor" type="text" value="' . $commandtext . '"/>
			</div>
		</div>';
		

		$content .= "<script>
		var button_command_preview = $('.cart_block .cart-buttons a');
		var button_command_gc1 = $( '#button_command_gc1' ).val();
		var button_command_gc2 = $( '#button_command_gc2' ).val();
		var button_command_textcolor = $( '#button_command_textcolor' ).val();	
				
		$(document).ready( applyStyle_button_command );		
		
		function applyStyle_button_command()
		{
			button_command_preview.attr('style', 'border:none; color: ' + button_command_textcolor + ';background:transparent;' + generateGradientOnlyCode('" .
				 self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM . "', button_command_gc1 , button_command_gc2)  );
			$('.cart_block .cart-buttons a.button_order_cart span').css('color', button_command_textcolor);

			$('.commandtext_destroyme').each(function() {
		       $(this).remove();
		    });

		    $('#footer').append('<style class =\'commandtext_destroyme\'> .button_order_cart .icon-chevron-right:before{ color:' + button_command_textcolor + '; }</style>');		
		}		
				
		" .
				 $this->getSpectrumJSForHTML('#button_command_gc1', 'button_command_gc1', 'applyStyle_button_command') . "		
		

		" .
				 $this->getSpectrumJSForHTML('#button_command_gc2', 'button_command_gc2', 'applyStyle_button_command') . "

		" .
				 $this->getSpectrumJSForHTML('#button_command_textcolor', 'button_command_textcolor', 'applyStyle_button_command') . "

		$('.button_order_cart').hover(
			function ()
			{
			    $(this).css('background', button_command_gc1);
			 },
			 function ()
			 {
				applyStyle_button_command();
			 }
		);
		</script>

		";
		
		return $content;
	}

	private function cfgButtonDetail(&$generalArr)
	{
		$button_detail_gc1 = $generalArr ['param7'] == null ? '#FFF' : $generalArr ['param7'];
		$button_detail_gc2 = $generalArr ['param8'] == null ? '#FFF' : $generalArr ['param8'];
		$button_detail_textcolor = $generalArr ['param9'] == null ? '#FFF' : $generalArr ['param9'];
		
		$content = '
		<div class="row">
			<div class="col-md-12">
				<div class="button-container" style="">
					<a title="' . $this->l("Details") . '" rel="nofollow"  class="button lnk_view btn btn-default">
						<span>' . $this->l("Details") . '</span>
					</a>
				</div>
			</div>
		</div>';
		
		$content .= '
		<div class="row">
			<div class="col-md-1">
				<h3 style="margin:0 15px 0 0">' . $this->l('Gradient: ') . '</h3>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('color 1: ') . '</label>
				<input name="param7" id="button_detail_gc1" type="text" value="' . $button_detail_gc1 . '"/>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('color 2: ') . '</label>
				<input name="param8" id="button_detail_gc2" type="text" value="' . $button_detail_gc2 . '"/>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('text color: ') . '</label>
				<input name="param9" id="button_detail_textcolor" type="text" value="' . $button_detail_textcolor . '"/>
			</div>
		</div>';
		

		$content .= "<script>
		var button_detail_preview = $('.button.lnk_view span');

		var button_detail_gc1 = $( '#button_detail_gc1' ).val();
		var button_detail_gc2 = $( '#button_detail_gc2' ).val();
		var button_detail_textcolor = $( '#button_detail_textcolor' ).val();				

		$(document).ready( applyStyle_button_detail );
				
		function applyStyle_button_detail()
		{
			button_detail_preview.attr('style',  'border:none;color: ' + button_detail_textcolor + ';background:transparent;' + generateGradientOnlyCode('" .
				 self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM . "', button_detail_gc1 , button_detail_gc2)  );
			
		}
		
		" .
				 $this->getSpectrumJSForHTML('#button_detail_gc1', 'button_detail_gc1', 'applyStyle_button_detail') . "
		

		" .
				 $this->getSpectrumJSForHTML('#button_detail_gc2', 'button_detail_gc2', 'applyStyle_button_detail') . "
				

		" .
				 $this->getSpectrumJSForHTML('#button_detail_textcolor', 'button_detail_textcolor', 'applyStyle_button_detail') . "

		button_detail_preview.hover(
			function ()
			{
			    $(this).css('background', button_detail_gc1);
			 },
			 function ()
			 {
				applyStyle_button_detail();
			 }
		);
		</script>
";
		
		return $content;
	}

	private function cfgButtonCompare(&$generalArr)
	{
		$params ['general'] ['param10'] = $generalArr ['param10'] == null ? '#FFF' : $generalArr ['param10'];
		$params ['general'] ['param11'] = $generalArr ['param11'] == null ? '#FFF' : $generalArr ['param11'];
		$params ['general'] ['param12'] = $generalArr ['param12'] == null ? '#FFF' : $generalArr ['param12'];
		
		$content = '
		<div class="row">
			<div class="col-md-12">
				<button class="btn btn-default button button-medium bt_compare" >
					<span>' . $this->l('Compare') . ' (<strong class="total-compare-val">X</strong>)
						<i class="icon-chevron-right right"></i>
					</span>
				</button>
			</div>
		</div><br/>';
		
		$content .= '
		<div class="row">
			<div class="col-md-1">
				<h3 style="margin:0 15px 0 0">' . $this->l('Gradient: ') . '</h3>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('color 1: ') . '</label>
				<input name="param10" id="button_compare_gc1" type="text" value="' . $params ['general'] ['param10'] . '"/>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('color 2: ') . '</label>
				<input name="param11" id="button_compare_gc2" type="text" value="' . $params ['general'] ['param11'] . '"/>
			</div>

			<div class="col-md-2">
				<label>' . $this->l('text color: ') . '</label>
				<input name="param12" id="button_compare_textcolor" type="text" value="' . $params ['general'] ['param12'] . '"/>
			</div>
		</div>';
		

		$content .= "<script>
				
		var button_compare_preview = $('.button.button-medium.bt_compare');

		var button_compare_gc1 = $( '#button_compare_gc1' ).val();
		var button_compare_gc2 = $( '#button_compare_gc2' ).val();
		var button_compare_textcolor = $( '#button_compare_textcolor' ).val();				

		$(document).ready( applyStyle_button_compare );
				
		function applyStyle_button_compare()
		{
			button_compare_preview.attr('style',  'border:none; color: ' + button_compare_textcolor + ';background:transparent;' + generateGradientOnlyCode('" .
				 self::DDLX_EVOLUTIONX_FONT_GRADIENT_BOTTOM . "', button_compare_gc1 , button_compare_gc2)  );
			$('.comparetext_destroyme').each(function() 
			{
		       $(this).remove();
		    });

		    $('#footer').append('<style class =\'comparetext_destroyme\'> .bt_compare .icon-chevron-right:before{ color:' + button_compare_textcolor + '!important; }</style>');
					
		}		
				
		" .
				 $this->getSpectrumJSForHTML('#button_compare_gc1', 'button_compare_gc1', 'applyStyle_button_compare') . "		

		" .
				 $this->getSpectrumJSForHTML('#button_compare_gc2', 'button_compare_gc2', 'applyStyle_button_compare') . "
		
		" .
				 $this->getSpectrumJSForHTML('#button_compare_textcolor', 'button_compare_textcolor', 'applyStyle_button_compare') . "

		$('.button.button-medium.bt_compare').hover(
			function ()
			{
			    $(this).css('background', button_compare_gc1);
			 },
			 function ()
			 {
				applyStyle_button_compare();
			 }
		);
		</script>
";
		
		return $content;
	}
	
	// ####################### CONFIG HEADER ##############################
	private function configHeader(&$headerArr, $idTab)
	{
		$output = '
	<div class="container-fluid">
		<form name="configHeader" action="' .
				 $_SERVER ['REQUEST_URI'] . $idTab . '" method="post" enctype="multipart/form-data" role="form" class="form-inline">
			<input type="hidden" name="onglet" value="onglet_1" />
			<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
			<input type="hidden" name="form" value="header" />
			<h1>' . $this->l('Header configuration') . '</h1>
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
			<hr>';
		

		$output .= $this->cfgHeaderLogo($headerArr);
		
		$output .= $this->cfgHeaderBG($headerArr);
		
		$output .= $this->cfgHeaderSearchBlock($headerArr);
		
		$output .= $this->cfgHeaderNavigationLink($headerArr);
		
		$output .= $this->cfgHeaderCartBlock($headerArr);
		
		$output .= '
		</form>
	</div>
';
		return $output;
	}

	private function cfgHeaderLogo(&$headerArr)
	{
		$content = '
<div class="row">
	<h2>' . $this->l('Logo configuration') . '</h2>
			
	<div class="col-md-6">

		<label>' . $this->l('Preview') . ' :</label>
		<br />
		<img class="previewheader" src="' . $this->realpath . 'img/' .
				 $this->currentProfileName . '/' . $headerArr ['param1'] . '" />
		<br/>

		<div class="form-group">
			<label>' . $this->l('Choose picture') . ' :</label>
			<input type="file" name="imagelogo" size="5">
		</div>
		<br/>

		<span class="advise">' . $this->l('Recommended max width') .
				 ' : 340px / 220px <br /> ' . $this->l('Authorized extension') . ' : .png </span>
		<br/>
	</div>
				
	<div class="col-md-6">
		<H3>' . $this->l('Logo position') . ' : </H3>
		<br/>
		<div class="form-group">
			<label>Margin left : </label>
			<input type="text" name="param3" size="5" value="' . $headerArr ['param3'] . '"> px
		</div>
		<br/>
		<div class="form-group">
			<label>Margin top : </label>
			<input type="text" name="param4" size="5" value="' . $headerArr ['param4'] . '"> px
		</div>

	</div>

</div>
<hr>';
		
		return $content;
	}

	private function cfgHeaderBG(&$headerArr)
	{
		$bgposbottom = ( $headerArr ['param27'] == 'bottom' ) ? 'checked="checked"' : '';
		$bgpostop = ( $headerArr ['param27'] == 'top' ) ? 'checked="checked"' : '';
		$bgposvcenter = ( $headerArr ['param27'] == 'center' ) ? 'checked="checked"' : '';
		
		$content = '
<div class="row">
	<h2><img src="' . $this->realpath . 'img/back_header.png" />' .
				 $this->l('header\'s background') . '</h2>

	<div class="col-md-6">

		<div class="form-group">
			<label>' . $this->l('Transparent header') . ' :</label>
	 		<input id="headerbackground_trans" type="checkbox" name="param5" value="1" ' . $this->check($headerArr ['param5']) . '/>
	 		<br />
	 		<span class="advise">' .
				 $this->l('If checked, the element will be transparent, no background nor image.') . ' </span>
	 	</div>
		<br/>

	 	<div class="form-group">
			<label>' . $this->l('No image') . ' :</label>
			<input id="headerbackground_noimg" type="checkbox" name="param6" value="1" ' . $this->check($headerArr ['param6']) . '/>
			<br />
			<span class="advise">' . $this->l('If checked, the image will not be displayed') . ' </span>
		</div>

		<br />
		<label>' . $this->l('Preview') . ' :</label>
		<br />


		<div class="headerbackground_preview" ></div>
		<br />


	 	<div class="form-group">
		  	<label> ' . $this->l('Choose background picture') . ' :</label>
		  	<input type="file" name="imageheaderbg" size="3">
		  	<br />
		 	<span class="advise">' . $this->l('Recomended width') .
				 ' : 1000 px , ' . $this->l('Authorized extension') . ' : .png / .jpg / .gif</span>

		</div>
	</div>

	<div class="col-md-6">
		<div class="form-group">
			<h4>' . $this->l('Background position') . ' :</h4>
			' . $this->getBackgroundPosHorizontalInputForHTML('param26', $headerArr) . '
			
		</div>
		<br/>

		<div class="form-group">
			' . $this->getBackgroundPosVerticalInputForHTML('param27', $headerArr) . '
		</div>

	 	<br />
		<div class="form-group">
	 		<h4>' . $this->l('Background repeat') . ' :</h4>
	 		' . $this->getBackgroundRepeatInputForHTML('param9', $headerArr) . '
	 	</div>

		<br/>
		<h4>' . $this->l('Background gradient') . ' :</h4>
	 	<div class="form-group">
	 	 	<label>' . $this->l('Gradient color 1') . ' :
	 	  	<input type="text"  name="param7" value="' . $headerArr ['param7'] . '" id="headerbackground_gradientcolor1"/>
	 	</div>

		&nbsp;&nbsp;
	 	<div class="form-group">
	 	 	<label>' . $this->l('Gradient color 2') . ' :
	 	  	<input type="text" name="param8" value="' . $headerArr ['param8'] . '" id="headerbackground_gradientcolor2"/>
	 	</div>

	 	<br />
		<div class="form-group" id="headerbackground_gradientradio">
	 		<label>' . $this->l('Gradient type') . ' :</label>
	 		' . $this->getGradientInputForHTML('param10', $headerArr) . '
	 	</div>
		<br/>
	 				
		<div class="form-group">
			<label>' . $this->l('Header\'s height') . ' :</label>
			<input name="param11" value="' . $headerArr ['param11'] . '" type="text" size="4" /> px
		</div>


	</div>
</div>
<hr>';
		

		$content .= "<script>
		
		var headerbackground_preview = $('.headerbackground_preview');
		var headerbackground_trans = $('#headerbackground_trans');
		var headerbackground_noimg = $('#headerbackground_noimg');


		var headerbackground_gradientsens = $('#headerbackground_gradientradio input:radio:checked').val();
		var headerbackground_gradientcolor1 = $( '#headerbackground_gradientcolor1' ).val();
		var headerbackground_gradientcolor2 = $( '#headerbackground_gradientcolor2' ).val();

		$( document ).ready( applyStyle_headerbackground );
		
		function applyStyle_headerbackground()
		{
			if( headerbackground_trans.prop('checked'))
			{
				headerbackground_preview.attr('style', generateTransparentCode() );
			}
			else if ( headerbackground_noimg.prop('checked'))
			{
				headerbackground_preview.attr('style', generateGradientOnlyCode( headerbackground_gradientsens, headerbackground_gradientcolor1, headerbackground_gradientcolor2 ) );
			}
			else
			{
				headerbackground_preview.attr('style', generateImgAndGradientCode( headerbackground_gradientsens, headerbackground_gradientcolor1, headerbackground_gradientcolor2, '" .
				 $headerArr ['param12'] . "' ) );
			}			
			
		}

		" .
				 $this->getSpectrumJSForHTML('#headerbackground_gradientcolor1', 'headerbackground_gradientcolor1', 'applyStyle_headerbackground') . "
		
		" .
				 $this->getSpectrumJSForHTML('#headerbackground_gradientcolor2', 'headerbackground_gradientcolor2', 'applyStyle_headerbackground') . "

		$('#headerbackground_gradientradio input:radio').change(
		    function()
		    {
				console.log('rrrrrrrrrrrrrrrr');
			    headerbackground_gradientsens = $(this).val();
				applyStyle_headerbackground();
			}
		);

		headerbackground_trans.change(
		    function()
		    {		
				applyStyle_headerbackground();
			}
		);

		headerbackground_noimg.change(
			function()
			{
				applyStyle_headerbackground();
			}
		);

		</script>";
		
		return $content;
	}

	private function cfgHeaderSearchBlock(&$headerArr)
	{
		$content = '
		

<h2>' . $this->l('Search block') . '</h2> 
<div class="ddlxaviable">' . $this->l('Only available in pro edition : ') . '  <a href="http://www.evolution-x.fr" target="_blank">' . $this->l('get pro now') . ' </a></div>  

<div class="row">
	<div class="col-md-6">

		<div class="form-group">
			<label>' . $this->l('Preview') . ' :</label>


			<div id="search_block_top">
				<div id="searchbox">
					<input type="text" value="" placeholder="Rechercher" name="search_query" id="search_query_top"	class="search_query form-control ac_input" autocomplete="off">
					<button class="btn btn-default button-search" onClick="event.preventDefault();" />
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">

		<div class="form-group">
			<label>' . $this->l('Background icon color') . ' :</label>
			<input type="text" name="param13" value="' . $headerArr ['param13'] . '" id="searchbgicon"/>
		</div>
		&nbsp;&nbsp;

		<div class="form-group">
			<label>' . $this->l('Search icon color') . ' :</label>
			<input type="text" name="param14" value="' . $headerArr ['param14'] . '" id="searchiconcolor"/>
		</div>
		<br/><br/>

		<div class="form-group">
			<label>' . $this->l('Background text color') . ' :</label>
			<input type="text" name="param15" value="' . $headerArr ['param15'] . '" id="searchbgtext"/>
		</div>
		&nbsp;&nbsp;

		<div class="form-group">
			<label>' . $this->l('Search text color') . ' :</label>
			<input type="text" name="param16" value="' . $headerArr ['param16'] . '" id="searchtextcolor"/>
		</div>
		<br/>

		<label>' . $this->l('Margin') . ':</label>
		<br/>

		<div class="form-group">
			<label>left :</label>
			<input type="text" name="param17" size="5" value="' . $headerArr ['param17'] . '"> px
		</div>
		<br/>
		<div class="form-group">
			<label>top :</label>
			<input type="text" name="param18" size="5" value="' . $headerArr ['param18'] . '"> px
		</div>
	</div>
</div>
<hr>';
		$content .= "<script>
		var searchbgicon_preview = $('#search_block_top .btn.button-search');
				
		var searchbgicon = 	$( '#searchbgicon' ).val();	
		var searchiconcolor = $( '#searchiconcolor' ).val();
		
		var searchtext_preview = $( '#search_block_top  #search_query_top' );
		var searchbgtext = $( '#searchbgtext' ).val();
		var searchtextcolor = $( '#searchtextcolor' ).val();
		
		$(document).ready( applyStyle_header_btnsearch );
			
		function applyStyle_header_btnsearch()
		{
			searchbgicon_preview.attr('style','background: ' + searchbgicon );
			searchtext_preview.attr('style','background: ' + searchbgtext +' ;color: ' + searchtextcolor);

			//gestion icone search
			$('.searchiconcolordestroyme').each(function() {
		    	$(this).remove();
		    });

			$('#footer').append('<style class =\'searchiconcolordestroyme\'> #search_block_top .btn.button-search:before{ color:' + searchiconcolor + '; }    #search_query_top:focus ,#search_query_top input[type=\'text\']:focus	{border-color: ' + searchbgicon + ' ; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px ' + searchbgicon + '; outline: 0 none;}</style>');
						
		}
		
		" .
				 $this->getSpectrumJSForHTML('#searchbgicon', 'searchbgicon', 'applyStyle_header_btnsearch') . "
				
		" .
				 $this->getSpectrumJSForHTML('#searchiconcolor', 'searchiconcolor', 'applyStyle_header_btnsearch') . "
		
		" .
				 $this->getSpectrumJSForHTML('#searchbgtext', 'searchbgtext', 'applyStyle_header_btnsearch') . "
		
		" .
				 $this->getSpectrumJSForHTML('#searchtextcolor', 'searchtextcolor', 'applyStyle_header_btnsearch') . "

		</script>
		";
		
		return $content;
	}

	private function cfgHeaderNavigationLink(&$headerArr)
	{
		$content = '
	<div class="row">
 		<h2>' . $this->l('Navigation link') . ' </h2>
		<div class="ddlxaviable">' . $this->l('Only available in pro edition : ') . '  <a href="http://www.evolution-x.fr" target="_blank">' . $this->l('get pro now') . ' </a></div>
		<div class="col-md-6">
 		
			<label>' . $this->l('Preview') . ' :</label>
			<br />
			<div class="headernavigationlink_preview">
				<div class="col-md-6" style="float:left;padding:10px" >
					' . $this->l('Call us : 0123-456-789') . '					
				</div>
				<div class="col-md-6">
					<ul>
						<li>' . $this->l('Link 1') . '</li>
						<li>' . $this->l('Link 2') . '</li>
						<li>' . $this->l('Link 3') . '</li>
					</ul>		
				</div>
			</div>
		</div>

		<div class="col-md-6">			
			<div class="form-group">
				<label>' . $this->l('Text color') . ' :</label>
				<input type="text" name="param19" value="' . $headerArr ['param19'] . '" id="headernavigationlink_textcolor"/>

			&nbsp;&nbsp;
			
				<label>' . $this->l('Background color') . ' :</label>
				<input type="text" name="param20" size="5" value="' . $headerArr ['param20'] . '" id="headernavigationlink_bg">

			<br/><br/>

				<label>' . $this->l('Background hover color') . ' :</label>
				<input type="text" name="param21" size="5" value="' . $headerArr ['param21'] . '" id="headernavigationlink_bghover">
			</div>
		</div>

	</div>
	<hr>';
		
		$content .= "
<script>
		var headernavigationlink_preview = $( '.headernavigationlink_preview' );
		var headernavigationlink_textcolor = $( '#headernavigationlink_textcolor' ).val();
		var headernavigationlink_bg = $( '#headernavigationlink_bg' ).val();
		var headernavigationlink_bghover =	$( '#headernavigationlink_bghover' ).val();

		$( document ).ready( applyStyle_headernavigationlink );
				
		function applyStyle_headernavigationlink()
		{
			headernavigationlink_preview.css('background', headernavigationlink_bg).css('color', headernavigationlink_textcolor);
		}

		$( '.headernavigationlink_preview li' ).hover(
			function()
			{
				$(this).css('background', headernavigationlink_bghover);
			},
			function ()
			{
				$(this).css('background', 'transparent');
			}
		);
		
		" .
				 $this->getSpectrumJSForHTML('#headernavigationlink_textcolor', 'headernavigationlink_textcolor', 'applyStyle_headernavigationlink') . "
		
		" .
				 $this->getSpectrumJSForHTML('#headernavigationlink_bg', 'headernavigationlink_bg', 'applyStyle_headernavigationlink') . "
		
		" .
				 $this->getSpectrumJSForHTML('#headernavigationlink_bghover', 'headernavigationlink_bghover', 'applyStyle_headernavigationlink') . "
		
</script>";
		
		return $content;
	}

	private function cfgHeaderCartBlock(&$headerArr)
	{
		$content = '
<div class="row">
	<h2>' . $this->l('Block cart') . '</h2>
	<div class="ddlxaviable">' . $this->l('Only available in pro edition : ') . '  <a href="http://www.evolution-x.fr" target="_blank">' . $this->l('get pro now') . ' </a></div> 

	<div class="col-md-6">
 		
		<label>' . $this->l('Preview') . ' :</label>
		<br />
		<div class="headercartblock_preview">

				<p class="header_cartblock_row1">' . $this->l('Cart') .
				 ' <span class="header_cartblock_color2">  3 ' . $this->l('Products') . '</span></p>

				<p class="header_cartblock_row2">
					<img src ="' . $this->realpath . 'img/data/blouse.jpg" style="float:left;margin-right:40px;"/>
					<span class="header_cartblock_color2">1 x </span>
					' . $this->l('Blouse') . '  </br>
					<span class="header_cartblock_color2">' . $this->l('Black') . ', S </span>
					</br>  32,40 € </p>

				<p class="header_cartblock_row3">
					' . $this->l('Shipping') . '
					' . $this->l('Shipping') . '      </br> --------------------- </br>   
					Total <span style="float:right; margin-right:10px">94,80 €</span>

				</p>

				<div class="header_cartblock_row4"> 

					<div class="cart_block block exclusive">
						<div class="block_content">
							<div class="cart_block_list">
								<p class="cart-buttons">
									<a rel="nofollow" title="' . $this->l('Command') . '" class="btn btn-default button button-small button_order_cart">
										<span>' . $this->l('Command') . '<i class="icon-chevron-right right"></i></span>
									</a>
								</p>
							</div>
						</div>
				</div>		
			</div>

		</div>
 	</div>

	<div class="col-md-6">
		<div class="form-group">
			<label>' . $this->l('Cart background when reduced') . ' :
			<input type="text" name="param22" value="' . $headerArr ['param22'] . '" id="cartblock_reducebg"/></label>
		</div>
		<br/>

		<div class="form-group">
			<label>' . $this->l('Cart product section background') . '
			<input type="text" name="param23" size="5" value="' . $headerArr ['param23'] . '" id="cartblock_product"></label>
		</div>
		<br/>

		<div class="form-group">
			<label>' . $this->l('Total cost section background') . '
			<input type="text" name="param24" size="5" value="' . $headerArr ['param24'] . '" id="cartblock_cost"></label>
		</div>
		<br/>

		<div class="form-group">
			<label>' . $this->l('Command section background') . '
			<input type="text" name="param25" size="5" value="' . $headerArr ['param25'] . '" id="cartblock_command"></label>
		</div>
		<br/>

		<div class="form-group">
			<label>' . $this->l('Cart text color 1') . '
			<input type="text" name="param28" size="5" value="' . $headerArr ['param28'] . '" id="cartblock_text1"></label>
		</div>
		<br/>

		<div class="form-group">
			<label>' . $this->l('Cart text color 2') . '
			<input type="text" name="param29" size="5" value="' . $headerArr ['param29'] . '" id="cartblock_text2"></label>
		</div>

	</div>
</div>
<hr>';
		
		$content .= "
	<script>
		var headercartblock_preview = $( '#headercartblock_preview' );
		
		var cartblock_reducebg = $( '#cartblock_reducebg' ).val();
		var cartblock_product = $( '#cartblock_product' ).val();	
		var cartblock_cost = $( '#cartblock_cost' ).val();
		var cartblock_command = $( '#cartblock_command' ).val();
		var cartblock_text1 = $( '#cartblock_text1' ).val();
		var cartblock_text2 = $( '#cartblock_text2' ).val();

		$(document).ready( applyStyle_cartblock );
		
		function applyStyle_cartblock()
		{
			$('.header_cartblock_row1, .header_cartblock_row2, .header_cartblock_row3').css('color', cartblock_text1 );
			$('.header_cartblock_color2').css('color', cartblock_text2 );
			$('.header_cartblock_row1').css('background', cartblock_reducebg )
			$('.header_cartblock_row2').css('background', cartblock_product )
			$('.header_cartblock_row3').css('background', cartblock_cost );
			$('.header_cartblock_row4').css('background', cartblock_command );	
		}
			
		" .
				 $this->getSpectrumJSForHTML('#cartblock_reducebg', 'cartblock_reducebg', 'applyStyle_cartblock') . "
		" .
				 $this->getSpectrumJSForHTML('#cartblock_product', 'cartblock_product', 'applyStyle_cartblock') . "
		" .
				 $this->getSpectrumJSForHTML('#cartblock_cost', 'cartblock_cost', 'applyStyle_cartblock') . "
		" .
				 $this->getSpectrumJSForHTML('#cartblock_command', 'cartblock_command', 'applyStyle_cartblock') . "
		" .
				 $this->getSpectrumJSForHTML('#cartblock_text1', 'cartblock_text1', 'applyStyle_cartblock') . "
		" .
				 $this->getSpectrumJSForHTML('#cartblock_text2', 'cartblock_text2', 'applyStyle_cartblock') . "
</script>
";
		
		$content .= '<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />';
		
		return $content;
	}
	

	// ####################### CONFIG BACKGROUND
	private function configBackground(&$backgroundArr, $idTab)
	{
		$output = '
	<div class="container-fluid">
		<form name="configHeader" action="' .
				 $_SERVER ['REQUEST_URI'] . $idTab . '" method="post" enctype="multipart/form-data" role="form" class="form-inline">
			<input type="hidden" name="onglet" value="onglet_3" />
			<input type="hidden" name="form" value="background" />
			<input type="hidden" name="MAX_FILE_SIZE" value="3000000">

			<h1>' . $this->l('Background configuration') . '</h1>
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
			<hr>';
		

		$output .= $this->cfgBackgroundBody($backgroundArr);
		
		$output .= $this->cfgBackgroundContainer($backgroundArr);
		
		$output .= $this->cfgBackgroundColumns($backgroundArr);
		
		$output .= '
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
		</form>
	</div>
';
		return $output;
	}

	private function cfgBackgroundBody(&$backgroundArr)
	{
		$fixed = ( $backgroundArr ['param6'] == 'fixed' ) ? 'checked="checked"' : '';
		$scroll = ( $backgroundArr ['param6'] == 'scroll' ) ? 'checked="checked"' : '';
		$video_loop_no = ( $backgroundArr ['param41'] == 'false' ) ? 'checked="checked"' : '';
		$video_loop_yes = ( $backgroundArr ['param41'] == 'true' ) ? 'checked="checked"' : '';
		
		$video_volume_0 = ( $backgroundArr ['param42'] == '0' ) ? 'checked="checked"' : '';
		$video_volume_50 = ( $backgroundArr ['param42'] == '50' ) ? 'checked="checked"' : '';
		$video_volume_100 = ( $backgroundArr ['param42'] == '100' ) ? 'checked="checked"' : '';
		

		// TODO ' . $this->getGradientInputForHTML('param7', $gradientPosChecked) . '
		$content = '
<div class="row" style="background:rgba(117, 235, 241, 0.10)">
	<h2><img src="' . $this->realpath . 'img/back_body.png" />' . $this->l('Body Background') . ' </h2>

	<div class="col-md-6">

		<div class="form-group">
			<label>' . $this->l('No image') . ' :</label>
			<input type="checkbox" name="param2" value="1" ' . $this->check($backgroundArr ['param2']) . ' id="backgroundbody_noimage"/>
			<br />
			<span class="advise">' . $this->l('If checked, the image will not be displayed') . ' </span>
		</div>
		<br />

		<label>' . $this->l('Preview') . ' :</label>
		<br />
				
		<div class="backgroundbody_preview"></div>
		<br/>

		<div class="form-group">
			<label>' . $this->l('Select picture') . '</label>
			<input type="file" name="imagebg">
		</div>
		</br>

		 <div class="form-group">
			<h4> ' . $this->l('Video as background') . ' :</h4>
					
			<input type="text" name="param40" style="width:100px" value="' . $backgroundArr ['param40'] . '" />
			<label>' .
				 $this->l('ID of your youtube or vimeo movie (expl: "7Y3dsAY_a_I" or "5673767")') . '</label>
			
					
			<h4> ' . $this->l('Repeat the video') . ' :</h4>
			<input type="radio" name="param41" value="true" ' . $video_loop_yes . '/>
	        <label>' . $this->l('yes') . '</label>
			<input type="radio" name="param41" value="false" ' . $video_loop_no . '/>
	        <label>' . $this->l('no') . '</label>
	        		
	        <h4> ' . $this->l('Sound volume of the video') . ' :</h4>
			<input type="radio" name="param42" value="0" ' . $video_volume_0 . '/>
	        <label>' . $this->l('none') . '</label>
			<input type="radio" name="param42" value="50" ' . $video_volume_50 . '/>
	        <label>' . $this->l('50') . '</label>
	        <input type="radio" name="param42" value="100" ' . $video_volume_100 . '/>
	        <label>' . $this->l('100') . '</label>			
        </div>
	        		
	</div>

	<div class="col-md-6">

		<div class="form-group">
			<h4>' . $this->l('Background position') . ' : </h4>
			' . $this->getBackgroundPosHorizontalInputForHTML('param3', $backgroundArr) . '
		</div>
		<br />

		<div class="form-group">
			' . $this->getBackgroundPosVerticalInputForHTML('param4', $backgroundArr) . '
		</div>
		<br />

		<h4>' . $this->l('Background repeat') . ' : </h4>
		<div class="form-group"> 
			' . $this->getBackgroundRepeatInputForHTML('param5', $backgroundArr) . '
        </div>
		<br />

	    <div class="form-group">
			<h4> ' . $this->l('Background attachement') . ' :</h4>
			<input type="radio" name="param6" value="fixed" ' . $fixed . '/>
			<label>' . $this->l('fixed') . '</label>
			<input type="radio" name="param6" value="scroll" ' . $scroll . '/>
	        <label>' . $this->l('scroll') . '</label>

        </div>
		<br />


		<h4>' . $this->l('Background gradient') . ' : </h4>

		<div class="form-group">

			<label>' . $this->l('Gradient color 1') . ' :
	 	  	<input type="text"  name="param8" value="' . $backgroundArr ['param8'] . '" id="backgroundbody_gradient1"/>

			&nbsp;&nbsp;
	 	 	<label>' . $this->l('Gradient color 2') . ' :
	 	  	<input type="text" name="param9" value="' . $backgroundArr ['param9'] . '" id="backgroundbody_gradient2"/>
	 	</div>

		<br />
		<div class="form-group" id="backgroundbody_gradientsens">
 			<label>' . $this->l('Gradient type') . ' :</label>
 			' . $this->getGradientInputForHTML('param7', $backgroundArr) . '
 		</div>

	</div>
</div>
<hr>';
		

		$content .= "
<script>
		var imgName = '" . $backgroundArr ['param1'] . "';
		var backgroundbody_preview =  $( '.backgroundbody_preview' );
						
		var backgroundbody_gradientsens = $( '#backgroundbody_gradientsens input:radio:checked' ).val();
		
				
		var backgroundbody_gradient1 =  $( '#backgroundbody_gradient1' ).val();
		var backgroundbody_gradient2 =  $( '#backgroundbody_gradient2' ).val();
				
		$(document).ready( applyStyle_backgroundbody );

		function applyStyle_backgroundbody()
		{
			if ($('#backgroundbody_noimage').prop('checked'))
			{
				backgroundbody_preview.attr('style', generateGradientOnlyCode( backgroundbody_gradientsens, backgroundbody_gradient1, backgroundbody_gradient2 ) );
			}
			else
			{
				backgroundbody_preview.attr('style', generateImgAndGradientCode( backgroundbody_gradientsens, backgroundbody_gradient1, backgroundbody_gradient2, imgName ) );
			}
		}
		
		" .
				 $this->getSpectrumJSForHTML('#backgroundbody_gradient1', 'backgroundbody_gradient1', 'applyStyle_backgroundbody') . "
		
		" .
				 $this->getSpectrumJSForHTML('#backgroundbody_gradient2', 'backgroundbody_gradient2', 'applyStyle_backgroundbody') . "

		$('#backgroundbody_gradientsens input:radio').change(
		    function()
		    {
		        backgroundbody_gradientsens = $(this).val();
				applyStyle_backgroundbody();
			}
		);

		$('#backgroundbody_noimage').change(
			function()
			{
				applyStyle_backgroundbody();
			}
		  );

</script>
";
		return $content;
	}

	private function cfgBackgroundContainer(&$backgroundArr)
	{
		$fixed = ( $backgroundArr ['param21'] == 'fixed' ) ? 'checked="checked"' : '';
		$scroll = ( $backgroundArr ['param21'] == 'scroll' ) ? 'checked="checked"' : '';
		
		$noimage = ( $backgroundArr ['param17'] == '1' ) ? 'checked="checked"' : '';
		$transparent = ( $backgroundArr ['param15'] == '1' ) ? 'checked="checked"' : '';
		
		$content = '

<div class="row" style="background:rgba(252, 252, 252, 0.30)">
	<h2><img src="' . $this->realpath . 'img/back_container.png" />' .
				 $this->l('Container Background') . ' </h2>
	<div class="col-md-6">

		<div class="form-group">

			<label>' . $this->l('Transparent') . ' :</label>
			<input type="checkbox" name="param15" value="1" ' . $this->check($backgroundArr ['param15']) . ' id="background_container_trans"/>
			<br />
	 		<span class="advise">' .
				 $this->l('If checked, the element will be transparent, no background nor image.') . ' </span>
		
	 		<br/>
			<label>' . $this->l('No image') . ' :</label>
			<input type="checkbox" name="param17" value="1" ' . $this->check($backgroundArr ['param17']) . ' id="background_container_noimg"/>
			<br />
			<span class="advise">' . $this->l('If checked, the image will not be displayed') . ' </span>
		</div>
		<br/>

		<label>' . $this->l('Preview') . ' :</label>
		<br />
		
		<div class="background_container_preview"></div>
				
		<br/>
		<div class="form-group">
			<label>' . $this->l('Select picture') . '</label>
			<input type="file" name="container_bg_image">
		</div>
		</br>

	</div>

	<div class="col-md-6">

		<div class="form-group">
			<h4>' . $this->l('Background position') . ' : </h4>
			' . $this->getBackgroundPosHorizontalInputForHTML('param18', $backgroundArr) . '
		</div>
		<br />

		<div class="form-group">
			' . $this->getBackgroundPosVerticalInputForHTML('param19', $backgroundArr) . '
		</div>
		<br />

		<h4>' . $this->l('Background repeat') . ' : </h4>
		<div class="form-group">
			' . $this->getBackgroundRepeatInputForHTML('param20', $backgroundArr) . ';
        </div>
		<br />

	    <div class="form-group">
			<h4> ' . $this->l('Background attachement') . ' :</h4>
			<input type="radio" name="param21" value="fixed" ' . $fixed . '/>
			<label>' . $this->l('fixed') . '</label>
			<input type="radio" name="param21" value="scroll" ' . $scroll . '/>
	        <label>' . $this->l('scroll') . '</label>
        </div>
		<br />


		<h4>' . $this->l('Background gradient') . ' : </h4>
		<div class="form-group">
			<label>' . $this->l('Gradient color 1') . ' :
	 	  	<input type="text"  name="param23" value="' . $backgroundArr ['param23'] . '" id="background_container_gradient1"/>

			&nbsp;&nbsp;
	 	 	<label>' . $this->l('Gradient color 2') . ' :
	 	  	<input type="text" name="param24" value="' . $backgroundArr ['param24'] . '" id="background_container_gradient2"/>
	 	</div>
		<br />

		 <div class="form-group" id="background_container_gradientsens">
	 		<label>' . $this->l('Gradient type') . ' :</label>
			' . $this->getGradientInputForHTML('param22', $backgroundArr) . '
		</div>
		<br />

	</div>
</div>
<hr>';
		
		$content .= "
<script>
		var background_container_gradientsens = $('#background_container_gradientsens input:radio:checked').val();
		var background_container_preview = $( '.background_container_preview' );
		var background_container_trans = $( '#background_container_trans' );
		var background_container_noimg = $( '#background_container_noimg' );
				
				
		var background_container_gradient1 = $( '#background_container_gradient1' ).val();
		var background_container_gradient2 = $( '#background_container_gradient2' ).val();
		
		$( document ).ready( applyStyle_backgroundContainer );
				
		function applyStyle_backgroundContainer()
		{
			if( background_container_trans.prop('checked'))
			{
				background_container_preview.attr('style', generateTransparentCode() );
			}
			else if ( background_container_noimg.prop('checked'))
			{
				background_container_preview.attr('style', generateGradientOnlyCode( background_container_gradientsens, background_container_gradient1, background_container_gradient2 ) );
			}
			else
			{
				background_container_preview.attr('style', generateImgAndGradientCode( background_container_gradientsens, background_container_gradient1, background_container_gradient2, '" .
				 $backgroundArr ['param16'] . "' ) );
			}				
		}

		" .
				 $this->getSpectrumJSForHTML('#background_container_gradient1', 'background_container_gradient1', 'applyStyle_backgroundContainer') . "
				
		" .
				 $this->getSpectrumJSForHTML('#background_container_gradient2', 'background_container_gradient2', 'applyStyle_backgroundContainer') . "


		$('#background_container_gradientsens input:radio').change(
		    function()
		    {
				background_container_gradientsens = $(this).val()
				applyStyle_backgroundContainer();
			}
		);

		background_container_noimg.change(
			function()
			{
				applyStyle_backgroundContainer();
			}
		  );

		background_container_trans.change(
			function()
			{
				applyStyle_backgroundContainer();
			}
		);


</script>
";
		return $content;
	}

	private function cfgBackgroundColumns(&$backgroundArr)
	{
		$fixed = ( $backgroundArr ['param39'] == 'fixed' ) ? 'checked="checked"' : '';
		$scroll = ( $backgroundArr ['param39'] == 'scroll' ) ? 'checked="checked"' : '';
		
		$content = '
<div class="row" style="background:rgba(228, 93, 81, 0.10)">

	<h2><img src="' . $this->realpath . 'img/back_column.png" />' .
				 $this->l('Columns background') . '</h2>

	<div class="col-md-6">

		<div class="form-group">
			<label>' . $this->l('Transparent') . ' :</label>
	 		<input id="background_columns_trans" type="checkbox" name="param31" value="1" ' . $this->check($backgroundArr ['param31']) . '/>
	 		<br />
	 		<span class="advise">' .
				 $this->l('If checked, the element will be transparent, no background nor image.') . ' </span>
	 	</div>
		<br/>

	 	<div class="form-group">
			<label>' . $this->l('No image') . ' :</label>
			<input id="background_columns_noimg" type="checkbox" name="param32" value="1" ' . $this->check($backgroundArr ['param32']) . '/>
			<br />
			<span class="advise">' . $this->l('If checked, the image will not be displayed') . ' </span>
		</div>

		<br />
		<label>' . $this->l('Preview') . ' :</label>
		<br />
		
		<div class="background_columns_preview"></div>
		
		<br />
	 	<div class="form-group">
		  	<label> ' . $this->l('Choose background picture') . ' :</label>
		  	<input type="file" name="backgroundcolumns_image" size="3">
		  	<br />
		 	<span class="advise">' . $this->l('Recomended width') .
				 ' : 1000 px , ' . $this->l('Authorized extension') . ' : .png / .jpg / .gif</span>

		</div>
	</div>
	<div class="col-md-6">
		<br />
		<div class="form-group">
	 		<h4>' . $this->l('Background position') . ' :</h4>
	  		' . $this->getBackgroundPosHorizontalInputForHTML('param33', $backgroundArr) . '
		</div>
		<br/>

		<div class="form-group">
			' . $this->getBackgroundPosVerticalInputForHTML('param34', $backgroundArr) . '
		</div>

	 	<br />

		<div class="form-group">
			<h4>' . $this->l('Background repeat') . ' : </h4>
	 		' . $this->getBackgroundRepeatInputForHTML('param35', $backgroundArr) . '
	 	</div>

		<br />
		<div class="form-group">
			<h4> ' . $this->l('Background attachement') . ' :</h4>
			<input type="radio" name="param39" value="fixed" ' . $fixed . '/>
			<label>' . $this->l('fixed') . '</label>
			<input type="radio" name="param39" value="scroll" ' . $scroll . '/>
	        <label>' . $this->l('scroll') . '</label>
        </div>

	 
		<br/>
		<h4>' . $this->l('Background gradient') . ' : </h4>
	 	<div class="form-group">
	 	 	<label>' . $this->l('Gradient color 1') . ' :</label>
	 	  	<input type="text"  name="param37" value="' . $backgroundArr ['param37'] . '" id="background_columns_gc1"/>
	 	</div>

		&nbsp;&nbsp;
	 	<div class="form-group">
	 	 	<label>' . $this->l('Gradient color 2') . ' :</label>
	 	  	<input type="text" name="param38" value="' . $backgroundArr ['param38'] . '" id="background_columns_gc2"/>
	 	</div>

	 	<br />
		<div class="form-group" id="background_columns_gradientsens">
	 		<label>' . $this->l('Gradient type') . ' :</label>
			' . $this->getGradientInputForHTML('param36', $backgroundArr) . '
	 	</div>

	</div>
</div>
<hr>';
		
		$content .= "
	<script>
		var background_columns_gradientsens = $('#background_columns_gradientsens input:radio:checked').val();
		var background_columns_preview = $('.background_columns_preview');
		var background_columns_trans = $('#background_columns_trans');
		var background_columns_noimg = $('#background_columns_noimg');

		var background_columns_gc1 = $( '#background_columns_gc1' ).val();
		var background_columns_gc2 = $( '#background_columns_gc2' ).val();

		$( document ).ready( applyStyle_backgroundcolumns );

		function applyStyle_backgroundcolumns()
		{				
			if( background_columns_trans.prop('checked'))
			{
				background_columns_preview.attr('style', generateTransparentCode() );
			}
			else if ( background_columns_noimg.prop('checked'))
			{
				background_columns_preview.attr('style', generateGradientOnlyCode( background_columns_gradientsens, background_columns_gc1, background_columns_gc2 ) );
			}
			else
			{
				background_columns_preview.attr('style', generateImgAndGradientCode( background_columns_gradientsens, background_columns_gc1, background_columns_gc2, '" .
				 $backgroundArr ['param30'] . "' ) );
			}	
		}

		" .
				 $this->getSpectrumJSForHTML('#background_columns_gc1', 'background_columns_gc1', 'applyStyle_backgroundcolumns') . "

		" .
				 $this->getSpectrumJSForHTML('#background_columns_gc2', 'background_columns_gc2', 'applyStyle_backgroundcolumns') . "

		$('#background_columns_gradientsens input:radio').change(
		    function()
		    {
				background_columns_gradientsens = $(this).val();
				applyStyle_backgroundcolumns();
			}
		);

		$('#background_columns_trans').change(
		    function()
		    {
				applyStyle_backgroundcolumns();
			}
		);

		$('#background_columns_noimg').change(
		   function()
		   {
				applyStyle_backgroundcolumns();
			}
		);

		</script>";
		
		return $content;
	}
	
	// ####################### CONFIG BLOC #######################
	private function configBlock($blockArr, $idTab)
	{
		$output = '
	<div class="container-fluid">
		<form name="configHeader" action="' .
				 $_SERVER ['REQUEST_URI'] . $idTab . '" method="post" enctype="multipart/form-data" role="form" class="form-inline">
			<input type="hidden" name="onglet" value="onglet_4" />
			<input type="hidden" name="form" value="block" />

			<h1>' . $this->l('Block configuration') . '</h1>
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
			<hr>';
		

		$output .= $this->cfgBlockHeader($blockArr);
		
		$output .= $this->cfgBlockBody($blockArr);
		
		$output .= '
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
		</form>
	</div>
';
		return $output;
	}

	private function cfgBlockHeader(&$blockArr)
	{
		$content = '
<div class="row">
	<h2>' . $this->l('Block header Background') . ' </h2>

	<div class="col-md-6">

		<label>' . $this->l('Preview') . ' :</label>
		<br />

		<h2 class="blockheader_background_preview"> Text title	</h2>
		<h2 class="blockbody_background_preview"> Lorem ipsum dolor sit amet,
			<br/> consectetur adipisicing elit, <p>sed do eiusmod tempor incididunt </p>
			<p>ut labore et dolore magna aliqua.</p>
		</h2>


		<br/>
	</div>

	<div class="col-md-6">


	    <div class="form-group" id="blocheader_bggradientsens">
			<label>' . $this->l('margin bottom') . ' :
				<input type="text" name="param14" value="' . $blockArr ['param14'] . '" id="blocheader_marginbottom"/> px
			</label>
			<br />

	 	  	<div id="blocheader_marginbottom_slider" class="blocheader_borderradius"></div>
	 		<br />

	 		<label>' . $this->l('Gradient type') . ' :</label>
				' . $this->getGradientInputForHTML('param1', $blockArr) . '
			

	 	</div>
		<br />

		<div class="form-group">
			<h4>' . $this->l('Background gradient color') . ' : </h4>

			<label>' . $this->l('Gradient color 1') . ' :
	 	  		<input type="text"  name="param2" value="' . $blockArr ['param2'] . '" id="blocheader_bodygradient1"/>
			</label>
			&nbsp;&nbsp;
	 	 	<label>' . $this->l('Gradient color 2') . ' :
	 	  		<input type="text" name="param3" value="' . $blockArr ['param3'] . '" id="blocheader_bodygradient2"/>
	 	  	</label>
	 	</div>
		<br />

	 <div class="col-md-6">
		<div class="form-group">
	 	  	<h4>' . $this->l('Border Parameters') . ' : </h4>

			<label>' . $this->l('Border width') . ' :
	 	  		<input type="text"  name="param4" value="' . $blockArr ['param4'] . '" id="blocheader_borderwidth"/> px
	 	  	</label>
	 	  	<br />

	 	  	<label>' . $this->l('Border style') . ' :</label>
	 	  	<select name="param5" id="blocheader_borderstyle">
	 	  		' . $this->getBorderStyleInputForHTML('param5', $blockArr) . '
	 	  	</select>
	 	  	<br />

	 	  	<label>' . $this->l('Border color') . ' :
	 	  		<input type="text"  name="param6" value="' . $blockArr ['param6'] . '" id="blocheader_bordercolor"/>
	 	  	</label>
	 	  	<br />

	 	  	<label>' . $this->l('Border radius top left') . ' :
	 	  		<input type="text"  name="param7" value="' . $blockArr ['param7'] . '" id="blocheader_borderradius1"/>
	 	  	</label>
	 	  	<br />

	 	  	<div id="blocheader_borderradius1_slider" class="blocheader_borderradius"></div>

	 	  	<label>' . $this->l('Border top right') . ' :
	 	  		<input type="text"  name="param8" value="' . $blockArr ['param8'] . '" id="blocheader_borderradius2"/>
	 	  	</label>
	 	  	<br />

	 	  	<div id="blocheader_borderradius2_slider" class="blocheader_borderradius"></div>
	 	
	 	  	<label>' . $this->l('Border bottom right') . ' :
	 	  		<input type="text"  name="param9" value="' . $blockArr ['param9'] . '" id="blocheader_borderradius3"/>
	 	  	</label>
	 	  	<br />

	 	  	<div id="blocheader_borderradius3_slider" class="blocheader_borderradius"></div>

	 	  	<label>' . $this->l('Border bottom left') . ' :
	 	  		<input type="text"  name="param10" value="' . $blockArr ['param10'] . '" id="blocheader_borderradius4"/>
	 	  	</label>
	 	  	<br />

	 	  	<div id="blocheader_borderradius4_slider" class="blocheader_borderradius"></div>

	 	</div>
	 </div>
	  <div class="col-md-6">
		<div class="form-group">
	 	  	<h4>' . $this->l('Text parameters') . ' : </h4>

			<label>' . $this->l('Font family') . ' :</label>

			<select name="param11" id="blocheader_fontfamily">
	 	  		' . $this->getFontFamilyInputForHTML('param11', $blockArr) . '
	 	  	</select>
		 	&nbsp;

	 	  	<label>' . $this->l('Font size') . ' :
	 	  		<input type="text"  name="param12" value="' . $blockArr ['param12'] . '" id="blocheader_fontsize"/> px
	 	  	</label>
	 	  	<br />

	 	  	<label>' . $this->l('Font color') . ' :
	 	  		<input type="text"  name="param13" value="' . $blockArr ['param13'] . '" id="blocheader_fontcolor"/>
	 	  	</label>
	 	  	<br />
	 	  	
	 	  	<label>' . $this->l('Font color hover') . ' :
	 	  		<input type="text"  name="param15" value="' . $blockArr ['param15'] . '" id="blocheader_fontcolorhover"/>
	 	  	</label>
	 	  	<br />
	 	</div>

	 </div>
	 	 
	</div>
</div>
<hr>';
		

		return $content;
	}

	private function cfgBlockBody(&$blockArr)
	{
		$content = '
<div class="row">
	<h2>' . $this->l('Block body Background') . ' </h2>
	<div class="ddlxaviable">' . $this->l('Only available in pro edition : ') . '  <a href="http://www.evolution-x.fr" target="_blank">' . $this->l('get pro now') . ' </a></div> 

	<div class="col-md-6">

		<label>' . $this->l('Preview') . ' :</label>
		<br />

		<h2 class="blockheader_background_preview"> Text title	</h2>
		<h2 class="blockbody_background_preview"> Lorem ipsum dolor sit amet,
			<br/> consectetur adipisicing elit, <p>sed do eiusmod tempor incididunt </p>
			<p>ut labore et dolore magna aliqua.</p>
		</h2>

		<br/>
	</div>

	<div class="col-md-6">


	    <div class="form-group" id="blocbody_bggradientsens">

			<label>' . $this->l('Padding') . ' :
	 	  		<input type="text"  name="param29" value="' . $blockArr ['param29'] . '" id="blocbody_padding"/>
			</label>
	 	  	<br />

	 		<label>' . $this->l('Gradient type') . ' :</label>
			' . $this->getGradientInputForHTML('param16', $blockArr) . '

	 	</div>
		<br />

		<div class="form-group">
			<h4>' . $this->l('Background gradient color') . ' : </h4>

			<label>' . $this->l('Gradient color 1') . ' :
	 	  		<input type="text"  name="param17" value="' . $blockArr ['param17'] . '" id="blocbody_bodygradient1"/>
	 	  	</label>

			&nbsp;&nbsp;
	 	 	<label>' . $this->l('Gradient color 2') . ' :
	 	  		<input type="text" name="param18" value="' . $blockArr ['param18'] . '" id="blocbody_bodygradient2"/>
	 	  	</label>
	 	</div>
		<br />

	 <div class="col-md-6">
		<div class="form-group">
	 	  	<h4>' . $this->l('Border Parameters') . ' : </h4>

			<label>' . $this->l('Border width') . ' :
	 	  		<input type="text"  name="param19" value="' . $blockArr ['param19'] . '" id="blocbody_borderwidth"/>
	 	  	</label>
	 	  	<br />

	 	  	<label>' . $this->l('Border style') . ' :</label>
	 	  	<select name="param20" id="blocbody_borderstyle">
	 	  		' . $this->getBorderStyleInputForHTML('param20', $blockArr) . '
	 	  	</select>
	 	  	<br />

	 	  	<label>' . $this->l('Border color') . ' :
	 	  		<input type="text"  name="param21" value="' . $blockArr ['param21'] . '" id="blocbody_bordercolor"/>
	 	  	</label>
	 	  	<br />

	 	  	<label>' . $this->l('Border radius top left') . ' :
	 	  		<input type="text"  name="param22" value="' . $blockArr ['param22'] . '" id="blocbody_borderradius1"/>
	 	  	</label>
	 	  	<br />

	 	  	<div id="blocbody_borderradius1_slider" class="blocbody_borderradius"></div>

	 	  	<label>' . $this->l('Border top right') . ' :
	 	  		<input type="text"  name="param23" value="' . $blockArr ['param23'] . '" id="blocbody_borderradius2"/>
	 	  	</label>
	 	  	<br />

	 	  	<div id="blocbody_borderradius2_slider" class="blocbody_borderradius"></div>

	 	  	<label>' . $this->l('Border bottom right') . ' :
	 	  		<input type="text"  name="param24" value="' . $blockArr ['param24'] . '" id="blocbody_borderradius3"/>
	 	  	</label>
	 	  	<br />

	 	  	<div id="blocbody_borderradius3_slider" class="blocbody_borderradius"></div>

	 	  	<label>' . $this->l('Border bottom left') . ' :
	 	  		<input type="text"  name="param25" value="' . $blockArr ['param25'] . '" id="blocbody_borderradius4"/>
	 	  	</label>
	 	  	<br />

	 	  	<div id="blocbody_borderradius4_slider" class="blocbody_borderradius"></div>

	 	</div>
	 </div>
	  <div class="col-md-6">
		<div class="form-group">
	 	  	<h4>' . $this->l('Text parameters') . ' : </h4>

			<label>' . $this->l('Font family') . ' :</label>

			<select name="param26" id="blocbody_fontfamily">
	 	  		' . $this->getFontFamilyInputForHTML('param26', $blockArr) . '
	 	  	</select>
	 	  	&nbsp;

	 	  	<label>' . $this->l('Font size') . ' :
	 	  		<input type="text"  name="param27" value="' . $blockArr ['param27'] . '" id="blocbody_fontsize"/> px
	 	  	</label>
	 	  	<br />

	 	  	<label>' . $this->l('Font color') . ' :
	 	  		<input type="text"  name="param28" value="' . $blockArr ['param28'] . '" id="blocbody_fontcolor"/>
	 	  	</label>
	 	  	<br />
	 	</div>

	 </div>

	</div>
</div>
<hr>';
		
		$content .= "
<script>
		var blocbody_padding = $('#blocbody_padding').val();
		var blocbody_bggradientsens =  $('#blocbody_bggradientsens input:radio:checked').val();
		var blocbody_bodygradient1 = $('#blocbody_bodygradient1').val();
		var blocbody_bodygradient2 = $('#blocbody_bodygradient2').val();
		var blocbody_background_preview = $('.blockbody_background_preview');

		var blocbody_bordercolor = $('#blocbody_bordercolor').val();
		var blocbody_borderstyle = $('#blocbody_borderstyle').val();
		var blocbody_borderwidth = $('#blocbody_borderwidth').val();
		var blocbody_borderradius1 = $('#blocbody_borderradius1').val();
		var blocbody_borderradius2 = $('#blocbody_borderradius2').val();
		var blocbody_borderradius3 = $('#blocbody_borderradius3').val();
		var blocbody_borderradius4 = $('#blocbody_borderradius4').val();

		var blocbody_fontfamily = $('#blocbody_fontfamily').val();
		var blocbody_fontsize = $('#blocbody_fontsize').val();
		var blocbody_fontcolor = $('#blocbody_fontcolor').val();
		var blocbody_borderradius1_slider = $('#blocbody_borderradius1_slider').val();

		$( document ).ready( applyStyle_blocbody );

		function applyStyle_blocbody()
		{
			blocbody_background_preview.attr('style', 'padding:' + blocbody_padding + 'px;' + 'border:' + blocbody_borderwidth + 'px'  + ' ' + blocbody_borderstyle + ' ' + blocbody_bordercolor +
												';border-radius:' + blocbody_borderradius1 + 'px ' + blocbody_borderradius2 + 'px ' + blocbody_borderradius3 + 'px ' + blocbody_borderradius4 + 'px ' +
												';-webkit-border-top-left-radius:' + blocbody_borderradius1 + 'px;' +	'-webkit-border-top-right-radius:' + blocbody_borderradius2 + 'px;' + '-webkit-border-bottom-right-radius:' + blocbody_borderradius3 + 'px;' +'-webkit-border-bottom-left-radius:' + blocbody_borderradius4 + 'px;' +
												';-moz-border-radius-topleft:' + blocbody_borderradius1 + 'px;' +	'-moz-border-radius-topright:' + blocbody_borderradius2 + 'px;' + '-moz-border-radius-bottomright:' + blocbody_borderradius3 + 'px;' +'-moz-border-radius-bottomleft:' + blocbody_borderradius4 + 'px;' +
												generateGradientOnlyCode( blocbody_bggradientsens, blocbody_bodygradient1, blocbody_bodygradient2 ) +
												';font-family:' + blocbody_fontfamily + ';font-size:' + blocbody_fontsize +'px;' + 'color:' + blocbody_fontcolor  + ';' );
		}


		$('#blocbody_padding').on('change paste keyup',
		    function()
		    {
		        blocbody_padding = $(this).val();
				applyStyle_blocbody();
			}
		);

		$('#blocbody_borderradius1_slider').slider();
		$('#blocbody_borderradius1_slider').slider( 'option',{max:100,min:0,step:1,value: blocbody_borderradius1});
		$('#blocbody_borderradius1_slider').on( 'slide',
			function( event, ui )
			{
				blocbody_borderradius1 = ui.value;
				$('#blocbody_borderradius1').val(ui.value);
				applyStyle_blocbody();
			} );

		$('#blocbody_borderradius2_slider').slider();
		$('#blocbody_borderradius2_slider').slider( 'option',{max:100,min:0,step:1,value: blocbody_borderradius2});
		$('#blocbody_borderradius2_slider').on( 'slide',
			function( event, ui )
			{
				blocbody_borderradius2 = ui.value;
				$('#blocbody_borderradius2').val(ui.value);
				applyStyle_blocbody();
			} );

		$('#blocbody_borderradius3_slider').slider();
		$('#blocbody_borderradius3_slider').slider( 'option',{max:100,min:0,step:1,value: blocbody_borderradius3});
		$('#blocbody_borderradius3_slider').on( 'slide',
			function( event, ui )
			{
				blocbody_borderradius3 = ui.value;
				$('#blocbody_borderradius3').val(ui.value);
				applyStyle_blocbody();
			} );


		$('#blocbody_borderradius4_slider').slider();
		$('#blocbody_borderradius4_slider').slider( 'option',{max:100,min:0,step:1,value: blocbody_borderradius4});
		$('#blocbody_borderradius4_slider').on( 'slide',
			function( event, ui )
			{
				blocbody_borderradius4 = ui.value;
				$('#blocbody_borderradius4').val(ui.value);
				applyStyle_blocbody();
			} );

		$('#blocbody_fontfamily').on('change',
		    function()
		    {
		        blocbody_fontfamily = $(this).val();
				applyStyle_blocbody();
			}
		);

		$('#blocbody_fontsize').on('change paste keyup',
		    function()
		    {
		        blocbody_fontsize = $(this).val();
				applyStyle_blocbody();
			}
		);

		" .
				 $this->getSpectrumJSForHTML('#blocbody_fontcolor', 'blocbody_fontcolor', 'applyStyle_blocbody') . "
		
		
		$('#blocbody_borderwidth').on('change paste keyup',
		    function()
		    {
		        blocbody_borderwidth = $(this).val();
				applyStyle_blocbody();
			}
		);

		$('#blocbody_borderradius1').on('change paste keyup',
		    function()
		    {
		        blocbody_borderradius1 = $(this).val();
				$('#blocbody_borderradius1_slider').slider( 'option', 'value', blocbody_borderradius1 );
				applyStyle_blocbody();
			}
		);

		$('#blocbody_borderradius2').on('change paste keyup',
		    function()
		    {
		        blocbody_borderradius2 = $(this).val();
				$('#blocbody_borderradius2_slider').slider( 'option', 'value', blocbody_borderradius2 );
				applyStyle_blocbody();
			}
		);

		$('#blocbody_borderradius3').on('change paste keyup',
		    function()
		    {
		        blocbody_borderradius3 = $(this).val();
				$('#blocbody_borderradius3_slider').slider( 'option', 'value', blocbody_borderradius3 );
				applyStyle_blocbody();
			}
		);

		$('#blocbody_borderradius4').on('change paste keyup',
		    function()
		    {
		        blocbody_borderradius4 = $(this).val();
				$('#blocbody_borderradius4_slider').slider( 'option', 'value', blocbody_borderradius4 );
				applyStyle_blocbody();
			}
		);

		$('#blocbody_borderstyle').change(
		    function()
		    {
		        blocbody_borderstyle = $(this).val();
				applyStyle_blocbody();
			}
		);

		" .
				 $this->getSpectrumJSForHTML('#blocbody_bordercolor', 'blocbody_bordercolor', 'applyStyle_blocbody') . "
		
		" .
				 $this->getSpectrumJSForHTML('#blocbody_bodygradient1', 'blocbody_bodygradient1', 'applyStyle_blocbody') . "

		" .
				 $this->getSpectrumJSForHTML('#blocbody_bodygradient2', 'blocbody_bodygradient2', 'applyStyle_blocbody') . "

		$('#blocbody_bggradientsens input:radio').change(
		    function()
		    {
		        blocbody_bggradientsens = $(this).val();
				applyStyle_blocbody();
			}
		);


</script>
";
		
		$content .= "
<script>

		var blocheader_bggradientsens = $('#blocheader_bggradientsens input:radio:checked').val();
		var blocheader_marginbottom = $('#blocheader_marginbottom').val();
		var blocheader_bodygradient1 = $('#blocheader_bodygradient1').val();
		var blocheader_bodygradient2 = $('#blocheader_bodygradient2').val();
		var blocheader_background_preview = $('.blockheader_background_preview');

		var blocheader_bordercolor = $('#blocheader_bordercolor').val();
		var blocheader_borderstyle = $('#blocheader_borderstyle').val();
		var blocheader_borderwidth = $('#blocheader_borderwidth').val();
		var blocheader_borderradius1 = $('#blocheader_borderradius1').val();
		var blocheader_borderradius2 = $('#blocheader_borderradius2').val();
		var blocheader_borderradius3 = $('#blocheader_borderradius3').val();
		var blocheader_borderradius4 = $('#blocheader_borderradius4').val();

		var blocheader_fontfamily = $('#blocheader_fontfamily').val();
		var blocheader_fontsize = $('#blocheader_fontsize').val();
		var blocheader_fontcolor = $('#blocheader_fontcolor').val();
		var blocheader_fontcolorhover = $('#blocheader_fontcolorhover').val();
		var blocheader_borderradius1_slider = $('#blocheader_borderradius1_slider').val();


		$( document ).ready( applyStyle_blocheader );

		function applyStyle_blocheader()
		{
			blocheader_background_preview.attr('style', 'border:' + blocheader_borderwidth + 'px'  + ' ' + blocheader_borderstyle + ' ' + blocheader_bordercolor +
												';border-radius:' + blocheader_borderradius1 + 'px ' + blocheader_borderradius2 + 'px ' + blocheader_borderradius3 + 'px ' + blocheader_borderradius4 + 'px ' +
												';-webkit-border-top-left-radius:' + blocheader_borderradius1 + 'px;' +	'-webkit-border-top-right-radius:' + blocheader_borderradius2 + 'px;' + '-webkit-border-bottom-right-radius:' + blocheader_borderradius3 + 'px;' +'-webkit-border-bottom-left-radius:' + blocheader_borderradius4 + 'px;' +
												';-moz-border-radius-topleft:' + blocheader_borderradius1 + 'px;' +	'-moz-border-radius-topright:' + blocheader_borderradius2 + 'px;' + '-moz-border-radius-bottomright:' + blocheader_borderradius3 + 'px;' +'-moz-border-radius-bottomleft:' + blocheader_borderradius4 + 'px;' +
												generateGradientOnlyCode( blocheader_bggradientsens, blocheader_bodygradient1, blocheader_bodygradient2 ) +
												';font-family:' + blocheader_fontfamily + ';font-size:' + blocheader_fontsize +'px;' + 'color:' + blocheader_fontcolor  +
												';margin-bottom:' + blocheader_marginbottom + 'px!important;');
		}

		$('#blocheader_marginbottom_slider').slider();
		$('#blocheader_marginbottom_slider').slider( 'option',{max:50,min:-30,step:1,value: blocheader_marginbottom});
		$('#blocheader_marginbottom_slider').on( 'slide',
			function( event, ui )
			{
				blocheader_marginbottom = ui.value;
				$('#blocheader_marginbottom').val(ui.value);
				applyStyle_blocheader();
			} );

		$('#blocheader_marginbottom').on('change paste keyup',
		    function()
		    {
		        blocheader_marginbottom = $(this).val();
				$('#blocheader_marginbottom_slider').slider( 'option', 'value', blocheader_marginbottom );
				applyStyle_blocheader();
			}
		);


		$('#blocheader_borderradius1_slider').slider();
		$('#blocheader_borderradius1_slider').slider( 'option',{max:100,min:0,step:1,value: blocheader_borderradius1});
		$('#blocheader_borderradius1_slider').on( 'slide',
			function( event, ui )
			{
				blocheader_borderradius1 = ui.value;
				$('#blocheader_borderradius1').val(ui.value);
				applyStyle_blocheader();
			} );

		$('#blocheader_borderradius1').on('change paste keyup',
		    function()
		    {
		        blocheader_borderradius1 = $(this).val();
				$('#blocheader_borderradius1_slider').slider( 'option', 'value', blocheader_borderradius1 );
				applyStyle_blocheader();
			}
		);

		$('#blocheader_borderradius2_slider').slider();
		$('#blocheader_borderradius2_slider').slider( 'option',{max:100,min:0,step:1,value: blocheader_borderradius2});
		$('#blocheader_borderradius2_slider').on( 'slide',
			function( event, ui )
			{
				blocheader_borderradius2 = ui.value;
				$('#blocheader_borderradius2').val(ui.value);
				applyStyle_blocheader();
			} );

		$('#blocheader_borderradius3_slider').slider();
		$('#blocheader_borderradius3_slider').slider( 'option',{max:100,min:0,step:1,value: blocheader_borderradius3});
		$('#blocheader_borderradius3_slider').on( 'slide',
			function( event, ui )
			{
				blocheader_borderradius3 = ui.value;
				$('#blocheader_borderradius3').val(ui.value);
				applyStyle_blocheader();
			} );


		$('#blocheader_borderradius4_slider').slider();
		$('#blocheader_borderradius4_slider').slider( 'option',{max:100,min:0,step:1,value: blocheader_borderradius4});
		$('#blocheader_borderradius4_slider').on( 'slide',
			function( event, ui )
			{
				blocheader_borderradius4 = ui.value;
				$('#blocheader_borderradius4').val(ui.value);
				applyStyle_blocheader();
			} );



		$('#blocheader_borderradius2').on('change paste keyup',
		    function()
		    {
		        blocheader_borderradius2 = $(this).val();
				$('#blocheader_borderradius2_slider').slider( 'option', 'value', blocheader_borderradius2 );
				applyStyle_blocheader();
			}
		);

		$('#blocheader_borderradius3').on('change paste keyup',
		    function()
		    {
		        blocheader_borderradius3 = $(this).val();
				$('#blocheader_borderradius3_slider').slider( 'option', 'value', blocheader_borderradius3 );
				applyStyle_blocheader();
			}
		);

		$('#blocheader_borderradius4').on('change paste keyup',
		    function()
		    {
		        blocheader_borderradius4 = $(this).val();
				$('#blocheader_borderradius4_slider').slider( 'option', 'value', blocheader_borderradius4 );
				applyStyle_blocheader();
			}
		);

		$('#blocheader_fontfamily').change(
		    function()
		    {
		        blocheader_fontfamily = $(this).val();
				applyStyle_blocheader();
			}
		);

		$('#blocheader_fontsize').on('change paste keyup',
		    function()
		    {
		        blocheader_fontsize = $(this).val();
				applyStyle_blocheader();
			}
		);

		" .
				 $this->getSpectrumJSForHTML('#blocheader_fontcolor', 'blocheader_fontcolor', 'applyStyle_blocheader') . "
		
		" .
				 $this->getSpectrumJSForHTML('#blocheader_fontcolorhover', 'blocheader_fontcolorhover', 'applyStyle_blocheader') . "
				
		" .
				 $this->getSpectrumJSForHTML('#blocheader_bordercolor', 'blocheader_bordercolor', 'applyStyle_blocheader') . "
				
		" .
				 $this->getSpectrumJSForHTML('#blocheader_bodygradient1', 'blocheader_bodygradient1', 'applyStyle_blocheader') . "
		
		" .
				 $this->getSpectrumJSForHTML('#blocheader_bodygradient2', 'blocheader_bodygradient2', 'applyStyle_blocheader') . "

		$('#blocheader_borderwidth').on('change paste keyup',
		    function()
		    {
		        blocheader_borderwidth = $(this).val();
				applyStyle_blocheader();
			}
		);

		$('#blocheader_borderstyle').change(
		    function()
		    {
		        blocheader_borderstyle = $(this).val();
				applyStyle_blocheader();
			}
		);

		$('#blocheader_bggradientsens input:radio').change(
		    function()
		    {
		        blocheader_bggradientsens = $(this).val();
				applyStyle_blocheader();
			}
		);

		$('.blockheader_background_preview').hover(
		    function()
		    {
		        $(this).css('color', blocheader_fontcolorhover );
			},
			function()
		    {
				applyStyle_blocheader();
			}	
		);


</script>
";
		return $content;
	}
	
	// ####################### CONFIG FOOTER #######################
	private function configFooter($footerArr, $idTab)
	{
		$output = '
	<div class="container-fluid">
		<form name="configHeader" action="' .
				 $_SERVER ['REQUEST_URI'] . $idTab . '" method="post" enctype="multipart/form-data" role="form" class="form-inline">
			<input type="hidden" name="onglet" value="onglet_5" />
                <input type="hidden" name="form" value="footer" />
            	<input type="hidden" name="MAX_FILE_SIZE" value="3000000">

			<h1>' . $this->l('Footer configuration') . '</h1>
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
			<hr>';
		

		$output .= $this->cfgFooterBackground($footerArr);
		

		$output .= '
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
		</form>
	</div>
';
		return $output;
	}

	private function cfgFooterBackground(&$footerArr)
	{
		$fixed = ( $footerArr ['param7'] == 'fixed' ) ? 'checked="checked"' : '';
		$scroll = ( $footerArr ['param7'] == 'scroll' ) ? 'checked="checked"' : '';
		
		$content = '
<div class="row">
	<h2><img src="' . $this->realpath . 'img/back_footer.png" />' .
				 $this->l('Footer\'s background') . '</h2>

	<div class="col-md-6">
		<div class="form-group">
			<label>' . $this->l('Transparent footer') . ' :</label>
	 		<input id="footerbg_trans" type="checkbox" name="param2" value="1" ' . $this->check($footerArr ['param2']) . '/>
	 		<br />
	 		<span class="advise">' .
				 $this->l('If checked, the element will be transparent, no background nor image.') . ' </span>
	 	</div>
		<br/>

	 	<div class="form-group">
			<label>' . $this->l('No image') . ' :</label>
			<input id="footerbg_noimg" type="checkbox" name="param3" value="1" ' . $this->check($footerArr ['param3']) . '/>
			<br />
			<span class="advise">' . $this->l('If checked, the image will not be displayed') . ' </span>
		</div>

		<br />
		<label>' . $this->l('Preview') . ' :</label>
		<br />
				
		<div class="footerbg_preview" ></div>
		<br />
				
				
	 	<div class="form-group">
		  	<label> ' . $this->l('Choose background picture') . ' :</label>
		  	<input type="file" name="imagefooterbg" size="3">
		  	<br />
		 	<span class="advise">' . $this->l('Recomended width') .
				 ' : 1000 px , ' . $this->l('Authorized extension') . ' : .png / .jpg / .gif</span>

		</div>
	</div>
	<div class="col-md-6">
		<br />
		<div class="form-group">
	 		<h4>' . $this->l('Background position') . ' :</h4>
	 		' . $this->getBackgroundPosHorizontalInputForHTML('param4', $footerArr) . '
		</div>
		<br/>

		<div class="form-group">
			' . $this->getBackgroundPosVerticalInputForHTML('param5', $footerArr) . '
		</div>

	 	<br />
		<div class="form-group">
	 		<h4>' . $this->l('Background repeat') . ' :</h4>
	 		' . $this->getBackgroundRepeatInputForHTML('param6', $footerArr) . '
	 	</div>

		<br />
		<div class="form-group">
			<h4> ' . $this->l('Background attachement') . ' :</h4>
			<input type="radio" name="param7" value="fixed" ' . $fixed . '/>
			<label>' . $this->l('fixed') . '</label>
			<input type="radio" name="param7" value="scroll" ' . $scroll . '/>
	        <label>' . $this->l('scroll') . '</label>
        </div>


		<br/>
		<h4>' . $this->l('Background gradient') . ' :</h4>
	 	<div class="form-group">
	 	 	<label>' . $this->l('Gradient color 1') . ' :
	 	  	<input type="text"  name="param8" value="' . $footerArr ['param8'] . '" id="footerbg_gc1"/>
	 	</div>

		&nbsp;&nbsp;
	 	<div class="form-group">
	 	 	<label>' . $this->l('Gradient color 2') . ' :
	 	  	<input type="text" name="param9" value="' . $footerArr ['param9'] . '" id="footerbg_gc2"/>
	 	</div>

	 	<br />
		<div class="form-group" id="footerbg_gradientradio">
	 		<label>' . $this->l('Gradient type') . ' :</label>
			' . $this->getGradientInputForHTML('param10', $footerArr) . '

	 	</div>

	</div>
</div>
<hr>';
		
		$content .= "<script>
		var footerbg_gradientsens = $('#footerbg_gradientradio input:radio:checked').val();
		var footerbg_preview = $('.footerbg_preview');

		var footerbg_gc1 = $( '#footerbg_gc1' ).val();
		var footerbg_gc2 = $( '#footerbg_gc2' ).val();

		$( document ).ready( applyStyle_footerbg );

		function applyStyle_footerbg()
		{
			if($('#footerbg_trans').prop('checked'))
			{
				footerbg_preview.attr('style', generateTransparentCode() );
			}
			else if ( $('#footerbg_noimg').prop('checked'))
			{
				footerbg_preview.attr('style', generateGradientOnlyCode( footerbg_gradientsens, footerbg_gc1, footerbg_gc2 ) );
			}
			else
			{
				footerbg_preview.attr('style', generateImgAndGradientCode( footerbg_gradientsens, footerbg_gc1, footerbg_gc2, '" . $footerArr ['param1'] . "' ) );
			}
		}

		" . $this->getSpectrumJSForHTML('#footerbg_gc1', 'footerbg_gc1', 'applyStyle_footerbg') . "

		" . $this->getSpectrumJSForHTML('#footerbg_gc2', 'footerbg_gc2', 'applyStyle_footerbg') . "

		$('#footerbg_gradientradio input:radio').change(
		    function()
		    {
				footerbg_gradientsens = $(this).val();
				applyStyle_footerbg();
			}
		);

		$('#footerbg_trans').change(
		    function()
		    {
				applyStyle_footerbg();
			}
		);

		$('#footerbg_noimg').change(
		   function()
		   {
				applyStyle_footerbg();
			}
		);

		</script>";
		
		return $content;
	}
	
	// ####################### CONFIG PRODUCT
	private function configProduct($productArr, $idTab)
	{
		$output = '
	<div class="container-fluid">
		<form name="configHeader" action="' .
				 $_SERVER ['REQUEST_URI'] . $idTab . '" method="post" enctype="multipart/form-data" role="form" class="form-inline">
			<input type="hidden" name="onglet" value="onglet_6" />
                <input type="hidden" name="form" value="product" />
            	<input type="hidden" name="MAX_FILE_SIZE" value="3000000">

			<h1>' . $this->l('Product configuration') . '</h1>
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
			<hr>';
		

		$output .= $this->cfgProductList($productArr);
		
		$output .= $this->cfgProductPage($productArr);
		
		$output .= $this->cfgProductDescription($productArr);
		
		$output .= $this->cfgProductHomePageModule($productArr);
		
		$output .= '
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
		</form>
	</div>
';
		return $output;
	}

	private function cfgProductList(&$productArr)
	{
		$fixed = ( $productArr ['param7'] == 'fixed' ) ? 'checked="checked"' : '';
		$scroll = ( $productArr ['param7'] == 'scroll' ) ? 'checked="checked"' : '';
		
		$content = '
<div class="row">
	<h2>' . $this->l('Product list') . '</h2>
			
	<div class="col-md-6">
		<div class="form-group">
			<label>' . $this->l('Transparent') . ' :</label>
	 		<input id="productbackground_trans" type="checkbox" name="param2" value="1" ' . $this->check($productArr ['param2']) . '/>
	 		<br />
	 		<span class="advise">' .
				 $this->l('If checked, the element will be transparent, no background nor image.') . ' </span>
	 	</div>
		<br/>

	 	<div class="form-group">
			<label>' . $this->l('No image') . ' :</label>
			<input id="productbackground_noimg" type="checkbox" name="param3" value="1" ' . $this->check($productArr ['param3']) . '/>
			<br />
			<span class="advise">' . $this->l('If checked, the image will not be displayed') . ' </span>
		</div>

		<br />
		<label>' . $this->l('Preview') . ' :</label>
		<br />

		<div class="productbackground_preview">
	
				
				
			
<div class="row">
	<div class="left-block col-xs-4 col-xs-5 col-md-4">
		<div class="product-image-container">
			<a class="product_img_link"	title="Robe imprimée" itemprop="url"> 
				<img class="replace-2x img-responsive"
				src="' . $this->realpath . 'img/data/T-shirts-a-manches-courtes-delaves.jpg"
				alt="Robe imprimée" title="Robe imprimée" itemprop="image"
				height="250" width="250">
			</a>	
		</div>
	</div>


	<div class="center-block col-xs-4 col-xs-7 col-md-4">
		<div class="product-flags"></div>
		<h5 itemprop="name">
			<a class="product-name"	title="Robe imprimée" itemprop="url">' . $this->l("Product Title") . '</a>
		</h5>
		<p class="product-desc">lorem ipsum</p>
		<div class="color-list-container">
			<ul class="color_to_pick_list clearfix">
				<li><a	id="color_13" class="color_pick" style="background: #F39C11;"> </a>
				</li>
			</ul>
		</div>
		<span class="availability"> <span class="available-now">
				<link itemprop="availability" href="http://schema.org/InStock">En
				stock
		</span>
		</span>
	</div>
	<div class="right-block col-xs-4 col-xs-12 col-md-4">
		<div class="right-block-content row">
			<div class="content_price col-xs-5 col-md-12">
				
				<span itemprop="price" class="price product-price">
					34,78 €	
				</span>
				<meta itemprop="priceCurrency" content="EUR">				
				<span class="old-price product-price">
					36,61 €
				</span>
				<span class="price-percent-reduction">-5%</span>
			</div>
			<div class="button-container col-xs-7 col-md-12">
				<a class="button ajax_add_to_cart_button btn btn-default"	title="' . $this->l("Add to cart") .
				 '"> <span>' . $this->l("Add to cart") . '</span></a>
				<br/>
				<a  class="button lnk_view btn btn-default"	title="' . $this->l("Details") . '"> <span>' .
				 $this->l("Details") . '</span>	</a>
			</div>
			<div class="functional-buttons clearfix col-xs-7 col-md-12">

				<div class="wishlist">
					<a class="addToWishlist wishlistProd_3">' . $this->l("Add to my wishlist") . '</a>
				</div>
				<div class="compare">
					<a class="add_to_compare">' . $this->l("Add to compare") . '</a>
				</div>
			</div>
		</div>
	</div>
</div>


	
		</div>

		<div class="productbackground_preview2">
			<div class="productbackground_preview_list">

					<div class="row1">
						<img class="imgm" src="' . $this->realpath . 'img/data/T-shirts-a-manches-courtes-delaves.jpg">
					</div>
					<div class="row2">
					31,20€
					</div>

					<div class="row3">
						<div class="title" >' . $this->l("Product Title") . '</div>

						<div class="button-container" style="margin:0 auto;text-align:center;">
							<a title="' . $this->l("Add to cart") . '" rel="nofollow" class="button ajax_add_to_cart_button btn btn-default">
								<span>' . $this->l("Add to cart") . '</span>
							</a>
						</div> 


						<div style="margin:0 auto;text-align:center;">
							<div class="button-container" style="margin:0 auto;text-align:center;">
								<a title="' . $this->l("Details") . '" rel="nofollow"  class="button lnk_view btn btn-default">
									<span>' . $this->l("Details") . '</span>
								</a>
							</div> 
						</div>

						<div class="prices">
							<span class="price"> 19,81 €  </span>
							<span class="old-price"> 29,81 €  </span>
						</div>

					</div>

					<div class="row4"> 
						<div class="wishlist">
							<a class="addToWishlist wishlistProd_3">' . $this->l("Add to my wishlist") . '</a>
						</div>
						<div class="compare">
							<a class="add_to_compare">' . $this->l("Add to compare") . '</a>
						</div>
					</div>
			  
			</div>
		</div>



		<br />
	 	<div class="form-group">
		  	<label> ' . $this->l('Choose background picture') . ' :</label>
		  	<input type="file" name="productbackground_image" size="3">
		  	<br />
		 	<span class="advise">' . $this->l('Recomended width') .
				 ' : 1000 px , ' . $this->l('Authorized extension') . ' : .png / .jpg / .gif</span>

		</div>
	</div>
	<div class="col-md-6">
		<br />
		<div class="form-group" id="productbackground_positionH">
	 		<h4>' . $this->l('Background position') . ' :</h4>
	 		' . $this->getBackgroundPosHorizontalInputForHTML('param5', $productArr) . '		

		</div>
		<br/>

		<div class="form-group" id="productbackground_positionV">
			' . $this->getBackgroundPosVerticalInputForHTML('param4', $productArr) . '	
		</div>

	 	<br />

		<div class="form-group" id="productbackground_repeat">
			<h4>' . $this->l('Background repeat') . ' : </h4>
	 		' . $this->getBackgroundRepeatInputForHTML('param6', $productArr) . '
	 	</div>

		<br />
		<div class="form-group" id="productbackground_attachement">
			<h4> ' . $this->l('Background attachement') . ' :</h4>
			<input type="radio" name="param7" value="fixed" ' . $fixed . '/>
			<label>' . $this->l('fixed') . '</label>
			<input type="radio" name="param7" value="scroll" ' . $scroll . '/>
	        <label>' . $this->l('scroll') . '</label>
        </div>


		<br/>
		<h4>' . $this->l('Background gradient') . ' : </h4>
	 	<div class="form-group">
	 	 	<label>' . $this->l('Gradient color 1') . ' :</label>
	 	  	<input type="text"  name="param8" value="' . $productArr ['param8'] . '" id="productbackground_gc1"/>
	 	</div>

		&nbsp;&nbsp;
	 	<div class="form-group">
	 	 	<label>' . $this->l('Gradient color 2') . ' :</label>
	 	  	<input type="text" name="param9" value="' . $productArr ['param9'] . '" id="productbackground_gc2"/>
	 	</div>

	 	<br />
		<div class="form-group" id="productbackground_gradientradio">
	 		<label>' . $this->l('Gradient type') . ' :</label>
			' . $this->getGradientInputForHTML('param10', $productArr) . '
	 	</div>

		
		<br/>
		<h4>' . $this->l('Text colors') . ' : </h4>
		<div class="form-group">
	 	 	<label>' . $this->l('Product title') . ' :</label>
	 	  	<input type="text"  name="param11" value="' . $productArr ['param11'] . '" id="productbackground_title"/>
	 	</div>
	 	<br/>

		<div class="col-md-6">
			<div class="form-group">
		 	 	<label>' . $this->l('Product price') . ' :</label>
		 	  	<input type="text"  name="param12" value="' . $productArr ['param12'] . '" id="productbackground_price"/>
		 	
		 	  	<br/>
		 	 	<label>' . $this->l('Product add to gift') . ' :</label>
		 	  	<input type="text"  name="param13" value="' . $productArr ['param13'] . '" id="productbackground_addgift"/>
		 	
		 	  	<br/>
		 	 	<label>' . $this->l('Product add to comparator') . ' :</label>
		 	  	<input type="text"  name="param15" value="' . $productArr ['param15'] . '" id="productbackground_addcompare"/>
	 	  	</div>

	 	</div>
	 	<div class="col-md-6">
			<div class="form-group">
		 	 	<label>' . $this->l('Product old price') . ' :</label>
		 	  	<input type="text"  name="param17" value="' . $productArr ['param17'] . '" id="productbackground_oldprice"/>
		 
		 	  	<br/>
		 	 	<label>' . $this->l('Product add to gift hover') . ' :</label>
		 	  	<input type="text"  name="param14" value="' . $productArr ['param14'] . '" id="productbackground_addgifthover"/>
		 	 
		 	  	<br/>
		 	 	<label>' . $this->l('Product add to comparator hover') . ' :</label>
		 	  	<input type="text"  name="param16" value="' . $productArr ['param16'] . '" id="productbackground_addcomparehover"/>
			</div>
	 	</div>
	</div>
</div>
<hr>';
		
		$content .= "<script>
		var productbackground_gradientsens =  $( '#productbackground_gradientradio input:radio:checked' ).val();
		var productbackground_preview = $('.productbackground_preview');
		var productbackground_preview2 = $('.productbackground_preview2');
		var productbackground_trans = $('#productbackground_trans');
		var productbackground_noimg = $('#productbackground_noimg');

		var productbackground_gc1 = $( '#productbackground_gc1' ).val();
		var productbackground_gc2 = $( '#productbackground_gc2' ).val();
				
		var productbackground_title = $( '#productbackground_title' ).val();
		var productbackground_price = $( '#productbackground_price' ).val();
		var productbackground_oldprice = $( '#productbackground_oldprice' ).val();
				
		var productbackground_addgift = $( '#productbackground_addgift' ).val();
		var productbackground_addgifthover = $( '#productbackground_addgifthover' ).val();
				
		var productbackground_addcompare = $( '#productbackground_addcompare' ).val();
		var productbackground_addcomparehover = $( '#productbackground_addcomparehover' ).val();
				
		var productbackground_attachement = $('#productbackground_attachement input:radio').val();
		var productbackground_repeat = $('#productbackground_repeat input:radio').val();
		var productbackground_positionH = $('#productbackground_positionH input:radio').val();
		var productbackground_positionV = $('#productbackground_positionV input:radio').val();

		$( document ).ready( function() {
		     applyStyle_productbackground();
			 applyStyle_productbackgroundText();
		});

		function applyStyle_productbackground()
		{
			if ( productbackground_trans.prop('checked'))
			{
				productbackground_preview.attr('style', generateTransparentCode() );
				productbackground_preview2.attr('style', generateTransparentCode() );
			}
			else if ( productbackground_noimg.prop('checked') )
			{
				productbackground_preview.attr('style', generateGradientOnlyCode( productbackground_gradientsens, productbackground_gc1, productbackground_gc2 ) );
				productbackground_preview2.attr('style', generateGradientOnlyCode( productbackground_gradientsens, productbackground_gc1, productbackground_gc2 ) );
			}
			else
			{
				productbackground_preview.attr('style',  generateImgAndGradientCode( productbackground_gradientsens, productbackground_gc1, productbackground_gc2, '" .
				 $productArr ['param1'] .
				 "' ) );
				productbackground_preview2.attr('style',  generateImgAndGradientCode( productbackground_gradientsens, productbackground_gc1, productbackground_gc2, '" .
				 $productArr ['param1'] . "' ) );
			}
		}				
					
		function applyStyle_productbackgroundText()
		{
			$('.product-name, .row3 .title').css('color',  productbackground_title );
			$('.price.product-price, .prices .price').css('color', productbackground_price );
			$('.old-price').css('color', productbackground_oldprice );
			$('.addToWishlist').css('color', productbackground_addgift );
			$('.add_to_compare').css('color', productbackground_addcompare );			 
		}

		$('.addToWishlist').hover(
			function()
			{
				$(this).css('color', productbackground_addgifthover );
			},
			function()
			{
				applyStyle_productbackgroundText();
			}
		);
		$('.add_to_compare').hover(
			function()
			{
				$(this).css('color', productbackground_addcomparehover );
			},
			function()
			{
				applyStyle_productbackgroundText();
			}
		);
		
		" .
				 $this->getSpectrumJSForHTML('#productbackground_gc1', 'productbackground_gc1', 'applyStyle_productbackground') . "
		
		" .
				 $this->getSpectrumJSForHTML('#productbackground_gc2', 'productbackground_gc2', 'applyStyle_productbackground') . "

		$('#productbackground_gradientradio input:radio').change(
		    function()
		    {
				productbackground_gradientsens = $(this).val();
				applyStyle_productbackground();
			}
		);

		$('#productbackground_attachement input:radio').change(
		    function()
		   	{
				productbackground_attachement = $(this).val();
				applyStyle_productbackground();
			}
		);
		$('#productbackground_repeat input:radio').change(
		    function()
		   	{
				productbackground_repeat = $(this).val();
				applyStyle_productbackground();
			}
		);

		$('#productbackground_positionH input:radio').change(
		    function()
		   	{
				productbackground_positionH = $(this).val();
				applyStyle_productbackground();
			}
		);

		$('#productbackground_positionV input:radio').change(
		    function()
		   	{
				productbackground_positionV = $(this).val();
				applyStyle_productbackground();
			}
		);

		$('#productbackground_trans').change(
		    function()
		    {
				applyStyle_productbackground();
			}
		);

		$('#productbackground_noimg').change(
		   function()
		   {
				applyStyle_productbackground();
			}
		);

		// TEXT

		" .
				 $this->getSpectrumJSForHTML('#productbackground_title', 'productbackground_title', 'applyStyle_productbackgroundText') . "

		" .
				 $this->getSpectrumJSForHTML('#productbackground_price', 'productbackground_price', 'applyStyle_productbackgroundText') . "

		" .
				 $this->getSpectrumJSForHTML('#productbackground_addgift', 'productbackground_addgift', 'applyStyle_productbackgroundText') . "

		" .
				 $this->getSpectrumJSForHTML('#productbackground_addgifthover', 'productbackground_addgifthover', 'applyStyle_productbackgroundText') . "

		" .
				 $this->getSpectrumJSForHTML('#productbackground_addcompare', 'productbackground_addcompare', 'applyStyle_productbackgroundText') . "

		" .
				 $this->getSpectrumJSForHTML('#productbackground_addcomparehover', 'productbackground_addcomparehover', 'applyStyle_productbackgroundText') . "		

		" .
				 $this->getSpectrumJSForHTML('#productbackground_oldprice', 'productbackground_oldprice', 'applyStyle_productbackgroundText') . "
		
		</script>";
		
		return $content;
	}

	private function cfgProductPage(&$productArr)
	{
		$content = '
	<div class="row">
		<h2>' . $this->l('Product page') . '</h2>

		<div class="col-md-6">
			

			<label>' . $this->l('Preview') . ' :</label>
			<br />
			<div class="productpage_preview">
				<div class="col-md-6">
					<img class="img-responsive" src="' . $this->realpath . 'img/imgproduct.jpg" />
				</div>
				<div class="col-md-6">
					<h2>Lorem ipsum</h2>
					<br/>
					<strong>Hac ita persuasione</strong>
					<br/>
					<p>reducti intra moenia bellatores obseratis </p>
				</div>
			</div>
		</div>';
		

		$content .= '		
		<div class="col-md-6">

			<h4>' . $this->l('Background gradient') . ' : </h4>
		 	<div class="form-group">
		 	 	<label>' . $this->l('Gradient color 1') . ' :</label>
		 	  	<input type="text"  name="param21" value="' . $productArr ['param21'] . '" id="productpage_gc1"/>
		 	</div>

			&nbsp;&nbsp;
		 	<div class="form-group">
		 	 	<label>' . $this->l('Gradient color 2') . ' :</label>
		 	  	<input type="text" name="param22" value="' . $productArr ['param22'] . '" id="productpage_gc2"/>
		 	</div>

		 	<br />
			<div class="form-group" id="productpage_gradientradio">
		 		<label>' . $this->l('Gradient type') . ' :</label>
				' . $this->getGradientInputForHTML('param20', $productArr) . '
		 	</div>
						
			<br/>
			<h4>' . $this->l('Text colors') . ' : </h4>
			<div class="form-group">
		 	 	<label>' . $this->l('Titles') . ' :</label>
		 	  	<input type="text"  name="param23" value="' . $productArr ['param23'] . '" id="productpage_important"/>
		 	  	<br/>
		 	  	<label>' . $this->l('Contents') . ' :</label>
		 	  	<input type="text"  name="param24" value="' . $productArr ['param24'] . '" id="productpage_others"/>
		 	</div>
		 	<br/>

		 	
		</div>
	</div>
	<hr>';
		
		return $content;
	}

	private function cfgProductDescription(&$productArr)
	{
		$content = '
	<div class="row">
		<h2>' . $this->l('Product description') . '</h2>

		<div class="col-md-6">
			

			<label>' . $this->l('Preview') . ' :</label>
			<br />
			<div class="productpage_preview">
				<div class="col-md-6">
					<img class="img-responsive" src="' . $this->realpath . 'img/imgproduct.jpg" />
				</div>
				<div class="col-md-6">
					<h2>Lorem ipsum</h2>
					<br/>
					<strong>Hac ita persuasione</strong>
					<br/>
					<p>reducti intra moenia bellatores obseratis </p>
				</div>
				
				<div class="col-md-12" style="margin-top: 25px;">
					<div class="productdescription_preview">
						' . $this->l('Title') . ' 
					</div>
					<br />
					<div class="rte productdescription_preview_text">
						Lorem ipsum dolor sit amet,
						<br/> consectetur adipisicing elit, <p>sed do eiusmod tempor incididunt </p>
						<p>ut labore et dolore magna aliqua.</p>
					</div>
				</div>
			</div>
		</div>';
		

		$content .= '
		<div class="col-md-6">

			<h4>' . $this->l('Background gradient') . ' : </h4>
		 	<div class="form-group">
		 	 	<label>' . $this->l('Gradient color 1') . ' :</label>
		 	  	<input type="text"  name="param26" value="' . $productArr ['param26'] . '" id="productdescription_gc1"/>
		 	</div>

			&nbsp;&nbsp;
		 	<div class="form-group">
		 	 	<label>' . $this->l('Gradient color 2') . ' :</label>
		 	  	<input type="text" name="param27" value="' . $productArr ['param27'] . '" id="productdescription_gc2"/>
		 	</div>

		 	<br />
			<div class="form-group" id="productdescription_gradientradio">
		 		<label>' . $this->l('Gradient type') . ' :</label>
				' . $this->getGradientInputForHTML('param25', $productArr) . '
		 	</div>
			<br/>


			<h4>' . $this->l('Text colors') . ' : </h4>
			<div class="form-group">
		 	 	<label>' . $this->l('Titles') . ' :</label>
		 	  	<input type="text"  name="param28" value="' . $productArr ['param28'] . '" id="productdescription_titles"/>
		 	  	<br/>
		 	  	
		 	  			
		 	  	<label>' . $this->l('Font family') . ' :</label>

				<select name="param29" id="productdescription_fontfamily">
		 	  		' . $this->getFontFamilyInputForHTML('param29', $productArr) . '
		 	  	</select>
	 	
	 	  		&nbsp;

		 	  	<label>' . $this->l('Font size') . ' :</label>
		 	  	<input type="text"  name="param30" value="' . $productArr ['param30'] . '" id="productdescription_fontsize"/> px
		 	  	<br />
		 	  			
		 	  	<label>' . $this->l('Contents') . ' :</label>
		 	  	<input type="text"  name="param31" value="' . $productArr ['param31'] . '" id="productdescription_others"/>
		 	</div>
		 	<br/>

		</div>
	</div>
	<hr>';
		

		$content .= "
		<script>
			var productdescription_gradientsens = $('#productdescription_gradientradio input:radio:checked').val();
			var productdescription_preview = $('.productdescription_preview');
			var productdescription_preview_text = $('.productdescription_preview_text');

			var productdescription_gc1 = $( '#productdescription_gc1' ).val();
			var productdescription_gc2 = $( '#productdescription_gc2' ).val();
			var productdescription_titles = $( '#productdescription_titles' ).val();
			var productdescription_others = $( '#productdescription_others' ).val();
			var productdescription_fontfamily = $('#productdescription_fontfamily').val();
			var productdescription_fontsize = $('#blocheader_fontsize').val();


			$( document ).ready( applyStyle_productdescription );

			function applyStyle_productdescription()
			{
				productdescription_preview.attr('style', generateGradientOnlyCode( productdescription_gradientsens, productdescription_gc1, productdescription_gc2 ) +
												'font-family:' + productdescription_fontfamily + ';font-size: ' + productdescription_fontsize + 'px;' + 'color:' + productdescription_titles + ';');
				
				productdescription_preview_text.attr('style', 'color:' + productdescription_others );
			}

			$('#productdescription_fontsize').on('change paste keyup',
			    function()
			    {
			        productdescription_fontsize = $(this).val();
					applyStyle_productdescription();
				}
			);

			$('#productdescription_fontfamily').on('change',
			    function()
			    {
			        productdescription_fontfamily = $(this).val();
					applyStyle_productdescription();
				}
			);	

			" .
				 $this->getSpectrumJSForHTML('#productdescription_gc1', 'productdescription_gc1', 'applyStyle_productdescription') . "
			" .
				 $this->getSpectrumJSForHTML('#productdescription_gc2', 'productdescription_gc2', 'applyStyle_productdescription') . "


			$('#productdescription_gradientradio input:radio').change(
			    function()
			    {
					productdescription_gradientsens = $(this).val();
					applyStyle_productdescription();
				}
			);

			// TEXT
			" .
				 $this->getSpectrumJSForHTML('#productdescription_titles', 'productdescription_titles', 'applyStyle_productdescription') . "
			
			" .
				 $this->getSpectrumJSForHTML('#productdescription_others', 'productdescription_others', 'applyStyle_productdescription') . "

			</script>";
		
		$content .= "<script>
			var productpage_gradientsens = $('#productpage_gradientradio input:radio:checked').val();
			var productpage_preview = $('.productpage_preview');
	
	
			var productpage_gc1 = $( '#productpage_gc1' ).val();
			var productpage_gc2 = $( '#productpage_gc2' ).val();
			var productpage_important = $( '#productpage_important' ).val();
			var productpage_others = $( '#productpage_others' ).val();
	
			$( document ).ready( applyStyle_productpage );
	
			function applyStyle_productpage()
			{
				productpage_preview.attr('style', generateGradientOnlyCode( productpage_gradientsens, productpage_gc1, productpage_gc2 ) +
											'color:' + productpage_others );
				$('.productpage_preview h2, .productpage_preview strong').attr('style', 'color:' + productpage_important);
				
			}


			" .
				 $this->getSpectrumJSForHTML('#productpage_gc1', 'productpage_gc1', 'applyStyle_productpage') . "

			" .
				 $this->getSpectrumJSForHTML('#productpage_gc2', 'productpage_gc2', 'applyStyle_productpage') . "
	
			$('#productpage_gradientradio input:radio').change(
			    function()
			    {
					productpage_gradientsens = $(this).val();
					applyStyle_productpage();
				}
			);
	
			// TEXT
			" .
				 $this->getSpectrumJSForHTML('#productpage_important', 'productpage_important', 'applyStyle_productpage') . "
			" .
				 $this->getSpectrumJSForHTML('#productpage_others', 'productpage_others', 'applyStyle_productpage') . "

			</script>";
		

		return $content;
	}

	private function cfgProductHomePageModule(&$productArr)
	{
		$content = '
	<div class="row">
 		<h2>' . $this->l('Product Homepage module') . '</h2>

		<div class="col-md-6">

			<label>' . $this->l('Preview') . ' :</label>
			<br />
			<div class="producthomepagemodule_preview">
				<ul>
					<li><span>' . $this->l('Title') . '</span></li>
					<li><span>' . $this->l('Title 2') . '</span></li>
				</ul>
			</div>
		</div>';
		

		$content .= '
		<div class="col-md-6">

			<h4>' . $this->l('Background gradient') . ' : </h4>
		 	<div class="form-group">
		 	 	<label>' . $this->l('Gradient color 1') . ' :
		 	  		<input type="text"  name="param34" value="' . $productArr ['param34'] . '" id="producthomepagemodule_gc1"/>
		 	  	</label>
		 	</div>

			&nbsp;&nbsp;
		 	<div class="form-group">
		 	 	<label>' . $this->l('Gradient color 2') . ' :
		 	  		<input type="text" name="param35" value="' . $productArr ['param35'] . '" id="producthomepagemodule_gc2"/>
		 	  	</label>
		 	</div> 
		 	<br />

			<div class="form-group" id="producthomepagemodule_gradientradio">
		 		<label>' . $this->l('Gradient type') . ' :</label>
 				' . $this->getGradientInputForHTML('param33', $productArr) . '
		 	</div>
			<br/>

			<h4>' . $this->l('Text colors') . ' : </h4>
			<div class="form-group">
		 	 	<label>' . $this->l('Titles') . ' :
		 	  		<input type="text"  name="param36" value="' . $productArr ['param36'] . '" id="producthomepagemodule_titles"/>
		 	  	</label>
		 	  	<br/>

		 	  	<label>' . $this->l('Font family') . ' :</label>

				<select name="param37" id="producthomepagemodule_fontfamily">
		 	  		' . $this->getFontFamilyInputForHTML('param37', $productArr) . '
		 	  	</select>
		 	  	&nbsp;
		 	  				
		 	  	<label>' . $this->l('Font size') . ' :
		 	  		<input type="text"  name="param44" value="' . $productArr ['param44'] . '" id="producthomepagemodule_fontheight"/> px
		 	  	</label>
		 	</div>
		 	<br/>

		 	<h4>' . $this->l('Text colors hover') . ' : </h4>
			<div class="form-group">
		 	 	<label>' . $this->l('Titles hover') . ' :
		 	  		<input type="text"  name="param38" value="' . $productArr ['param38'] . '" id="producthomepagemodule_titleshover"/>
		 	  	</label>
		 	  	<br/>
		 	  	<label>' . $this->l('Titles background hover') . ' :
		 	  		<input type="text"  name="param39" value="' . $productArr ['param39'] . '" id="producthomepagemodule_titlesbghover"/>
		 	  	</label>
		 	  	<br/>
		 	 </div>

		 	 <h4>' . $this->l('Borders') . ' : </h4>
		 	 <div class="form-group"> 			
		 	  	<label>' . $this->l('Border radius top left') . ' :</label>
		 	  	<input type="text"  name="param40" value="' . $productArr ['param40'] . '" id="producthomepagemodule_borderradius1"/>

		 	  	<div id="producthomepagemodule_borderradius1_slider" class="producthomepagemodule_borderradius"></div>
				<br />

		 	  	<label>' . $this->l('Border top right') . ' :</label>
		 	  	<input type="text"  name="param41" value="' . $productArr ['param41'] . '" id="producthomepagemodule_borderradius2"/>


		 	  	<div id="producthomepagemodule_borderradius2_slider" class="producthomepagemodule_borderradius"></div>
				<br />
	
		 	  	<label>' . $this->l('Border bottom right') . ' :</label>
		 	  	<input type="text"  name="param42" value="' . $productArr ['param42'] . '" id="producthomepagemodule_borderradius3"/>
		 	  	<br />

		 	  	<div id="producthomepagemodule_borderradius3_slider" class="producthomepagemodule_borderradius"></div>
				<br />

		 	  	<label>' . $this->l('Border bottom left') . ' :</label>
		 	  	<input type="text"  name="param43" value="' . $productArr ['param43'] . '" id="producthomepagemodule_borderradius4"/>
		 	  	<br />
				<div id="producthomepagemodule_borderradius4_slider" class="producthomepagemodule_borderradius"></div>
			 </div>
			
		</div>
	</div>
	<hr>';
		
		$content .= "
 		<script>
			var producthomepagemodule_gradientsens = $('#producthomepagemodule_gradientradio input:radio:checked').val();
			var producthomepagemodule_preview = $('.producthomepagemodule_preview');

			var producthomepagemodule_gc1 = $( '#producthomepagemodule_gc1' ).val();
			var producthomepagemodule_gc2 = $( '#producthomepagemodule_gc2' ).val();

			var producthomepagemodule_titles = $( '#producthomepagemodule_titles' ).val();
 			var producthomepagemodule_fontfamily = $( '#producthomepagemodule_fontfamily' ).val();
			var producthomepagemodule_fontheight = $( '#producthomepagemodule_fontheight' ).val();
 			var producthomepagemodule_titleshover = $( '#producthomepagemodule_titleshover' ).val();
			var producthomepagemodule_titlesbghover = $( '#producthomepagemodule_titlesbghover' ).val();

 			var producthomepagemodule_borderradius1 = $('#producthomepagemodule_borderradius1').val();
			var producthomepagemodule_borderradius2 = $('#producthomepagemodule_borderradius2').val();
			var producthomepagemodule_borderradius3 = $('#producthomepagemodule_borderradius3').val();
			var producthomepagemodule_borderradius4 = $('#producthomepagemodule_borderradius4').val();


			$( document ).ready( applyStyle_producthomepagemodule );
 
			function applyStyle_producthomepagemodule()
			{
				producthomepagemodule_preview.attr('style', generateGradientOnlyCode( producthomepagemodule_gradientsens, producthomepagemodule_gc1, producthomepagemodule_gc2 ) +
											'color:' + producthomepagemodule_titles +';font-family:' + producthomepagemodule_fontfamily +';font-size:' + producthomepagemodule_fontheight + 'px' +
											';border-radius:' + producthomepagemodule_borderradius1 + 'px ' + producthomepagemodule_borderradius2 + 'px ' + producthomepagemodule_borderradius3 + 'px ' + producthomepagemodule_borderradius4 + 'px;' 
												);

				$('.producthomepagemodule_preview li span').css('background', 'transparent').css('color', 'inherit');
				
				$('.producthomepagemodule_preview li:first-child span').css('background', producthomepagemodule_titlesbghover).css('color', producthomepagemodule_titleshover);
				$('.producthomepagemodule_preview ul li').css('border-left', '1px solid ' + producthomepagemodule_titles);
				$('.producthomepagemodule_preview ul li:first-child').css('border-left', 'none');
 			}

			" .
				 $this->getSpectrumJSForHTML('#producthomepagemodule_gc1', 'producthomepagemodule_gc1', 'applyStyle_producthomepagemodule') . "

			" .
				 $this->getSpectrumJSForHTML('#producthomepagemodule_gc2', 'producthomepagemodule_gc2', 'applyStyle_producthomepagemodule') . "

			$('#producthomepagemodule_gradientradio input:radio').change(
			    function()
			    {
					producthomepagemodule_gradientsens = $(this).val();
					applyStyle_producthomepagemodule();
				}
			);

			// TEXT
			" .
				 $this->getSpectrumJSForHTML('#producthomepagemodule_titles', 'producthomepagemodule_titles', 'applyStyle_producthomepagemodule') . "


			$('#producthomepagemodule_fontfamily').on('change',
			    function()
			    {
			        producthomepagemodule_fontfamily = $(this).val();
					applyStyle_producthomepagemodule();
				}
			);

			$('#producthomepagemodule_fontheight').on('change paste keyup',
			    function()
			    {
			        producthomepagemodule_fontheight = $(this).val();
					applyStyle_producthomepagemodule();
				}
			);


			" .
				 $this->getSpectrumJSForHTML('#producthomepagemodule_titleshover', 'producthomepagemodule_titleshover', 'applyStyle_producthomepagemodule') . "

			" .
				 $this->getSpectrumJSForHTML('#producthomepagemodule_titlesbghover', 'producthomepagemodule_titlesbghover', 'applyStyle_producthomepagemodule') . "			


			$('.producthomepagemodule_preview li span').hover(
				function ()
				{
				    $(this).css('background', producthomepagemodule_titlesbghover).css('color', producthomepagemodule_titleshover);
				 },
				 function ()
				 {
					 applyStyle_producthomepagemodule();
				 }
			);
	
			$('#producthomepagemodule_borderradius1_slider').slider();
			$('#producthomepagemodule_borderradius1_slider').slider( 'option',{max:100,min:0,step:1,value: producthomepagemodule_borderradius1});
			$('#producthomepagemodule_borderradius1_slider').on( 'slide',
				function( event, ui )
				{
					producthomepagemodule_borderradius1 = ui.value;
					$('#producthomepagemodule_borderradius1').val(ui.value);
					applyStyle_producthomepagemodule();
				} 
			);

			$('#producthomepagemodule_borderradius1').on('change paste keyup',
			    function()
			    {
			        producthomepagemodule_borderradius1 = $(this).val();
					$('#producthomepagemodule_borderradius1_slider').slider( 'option', 'value', producthomepagemodule_borderradius1 );
					applyStyle_producthomepagemodule();
				}
			);

			$('#producthomepagemodule_borderradius2_slider').slider();
			$('#producthomepagemodule_borderradius2_slider').slider( 'option',{max:100,min:0,step:1,value: producthomepagemodule_borderradius2});
			$('#producthomepagemodule_borderradius2_slider').on( 'slide',
				function( event, ui )
				{
					producthomepagemodule_borderradius2 = ui.value;
					$('#producthomepagemodule_borderradius2').val(ui.value);
					applyStyle_producthomepagemodule();
				} );
			$('#producthomepagemodule_borderradius2').on('change paste keyup',
			    function()
			    {
			        producthomepagemodule_borderradius2 = $(this).val();
					$('#producthomepagemodule_borderradius2_slider').slider( 'option', 'value', producthomepagemodule_borderradius2 );
					applyStyle_producthomepagemodule();
				}
			);

			$('#producthomepagemodule_borderradius3_slider').slider();
			$('#producthomepagemodule_borderradius3_slider').slider( 'option',{max:100,min:0,step:1,value: producthomepagemodule_borderradius3});
			$('#producthomepagemodule_borderradius3_slider').on( 'slide',
				function( event, ui )
				{
					producthomepagemodule_borderradius3 = ui.value;
					$('#producthomepagemodule_borderradius3').val(ui.value);
					applyStyle_producthomepagemodule();
				}
			);
			$('#producthomepagemodule_borderradius3').on('change paste keyup',
			    function()
			    {
			        producthomepagemodule_borderradius3 = $(this).val();
					$('#producthomepagemodule_borderradius3_slider').slider( 'option', 'value', producthomepagemodule_borderradius3 );
					applyStyle_producthomepagemodule();
				}
			);

			$('#producthomepagemodule_borderradius4_slider').slider();
			$('#producthomepagemodule_borderradius4_slider').slider( 'option',{max:100,min:0,step:1,value: producthomepagemodule_borderradius4});
			$('#producthomepagemodule_borderradius4_slider').on( 'slide',
				function( event, ui )
				{
					producthomepagemodule_borderradius4 = ui.value;
					$('#producthomepagemodule_borderradius4').val(ui.value);
					applyStyle_producthomepagemodule();
				}
			);
			$('#producthomepagemodule_borderradius4').on('change paste keyup',
			    function()
			    {
			        producthomepagemodule_borderradius4 = $(this).val();
					$('#producthomepagemodule_borderradius4_slider').slider( 'option', 'value', producthomepagemodule_borderradius4 );
					applyStyle_producthomepagemodule();
				}
			);

			</script>";
		

		return $content;
	}
	
	// ####################### CONFIG MENU / NAVIGATION
	private function configNavigation($navArr, $idTab)
	{
		$output = '
	<div class="container-fluid">
		<form name="configNavigation" action="' .
				 $_SERVER ['REQUEST_URI'] . $idTab . '" method="post" enctype="multipart/form-data" role="form" class="form-inline">
           	<input type="hidden" name="onglet" value="onglet_2" />
			<input type="hidden" name="form" value="navigation" />

			<h1>' . $this->l('Principal Navigation') . '</h1>
			
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
			<hr>';
		

		$output .= $this->cfgNavigationMain($navArr);
		
		$output .= $this->cfgNavigationSubmenu($navArr);
		
		$output .= '
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
		</form>
	</div>
';
		

		return $output;
	}

	private function cfgNavigationMain($navArr)
	{
		$content = '
	<div class="row">
 		<h2>' . $this->l('Main menu') . '</h2>
		
		<div class="col-md-6" style="height:450px;"> 	
			<label>' . $this->l('Preview') . ' :</label>
			<br />
			<div class="navigationmain_preview">	
					
					
<div class="sf-contener clearfix col-lg-12" id="block_top_menu">
	<ul class="sf-menu clearfix menu-content">
		<li class="sfHoverForce">
			<a title="Femme" class="sf-with-ul">Femme</a>
					
			
			<ul style="display: block;"
				class="submenu-container clearfix first-in-line-xs">
				<li><a title="Hauts" class="sf-with-ul">Hauts</a>
					<ul style="display: none;">
						<li><a title="T-shirts">T-shirts</a>
						
						<li><a title="Blouses">Blouses</a>
					
					</ul>
				</li>
				<li><a title="Robes" class="sf-with-ul">Robes</a>
					<ul>
						<li><a title="Robes simples">Robes simples</a></li>
						<li><a title="Robes de soirée">Robes de soirée</a></li>
						<li><a title="Robes d\'été">Robes d\'été</a></li>
					</ul>
				</li>
				<li id="category-thumbnail">
					<div>
						<img class="imgm" title="Femme" alt="Femme"
							src="' . $this->realpath . 'img/3-0_thumb.jpg">					
					</div>
					<div>
						<img class="imgm" title="Femme" alt="Femme"
							src="' . $this->realpath . 'img/3-1_thumb.jpg">
					</div>
				</li>
			</ul>
		</li>

		<li class=""><a title="Robes" class="sf-with-ul">Robes</a>
		<li><a title="T-shirts">T-shirts</a></li>
		<li><a title="Blog">Blog</a></li>
	</ul>
					
					
</div>
					
					
			</div>
		</div>';
		

		$content .= '
		<div class="col-md-6">
 	
			<h4>' . $this->l('Background gradient') . ' : </h4>
		 	<div class="form-group">
		 	 	<label>' . $this->l('Gradient color 1') . ' :
		 	  	<input type="text"  name="param1" value="' . $navArr ['param1'] . '" id="navigationmain_gc1"/>
		 	  	</label>
		 	</div>
 	
			&nbsp;&nbsp;
		 	<div class="form-group">
		 	 	<label>' . $this->l('Gradient color 2') . ' :
		 	  	<input type="text" name="param2" value="' . $navArr ['param2'] . '" id="navigationmain_gc2"/>
		 	  	</label>
		 	</div>
 	
		 	<br />
			<div class="form-group" id="navigationmain_gradientradio">
		 		<label>' . $this->l('Gradient type') . ' :</label>
 				' . $this->getGradientInputForHTML('param3', $navArr) . '
 				
		 	</div>
			<br/>
						
			<h4>' . $this->l('Menu right border Parameters') . ' : </h4>
					
			<div class="form-group" id="navigationmain_gradientradio">
				<label>' . $this->l('Border width') . ' :
		 	  		<input type="text"  name="param4" value="' . $navArr ['param4'] . '" id="navigationmain_borderwidth"/> px
		 	  	</label>
		 	  		&nbsp;&nbsp;
	
		 	  	<label>' . $this->l('Border style') . ' :
			 	  	<select name="param5" id="navigationmain_borderstyle">
			 	  		' . $this->getBorderStyleInputForHTML('param5', $navArr) . '
			 	  	</select>
		 	  	</label>
		 	  	&nbsp;
	
		 	  	<label>' . $this->l('Border color') . ' :
		 	  		<input type="text"  name="param6" value="' . $navArr ['param6'] . '" id="navigationmain_bordercolor"/>
				</label>
		 	 </div>
			 <br/>

		 	 <h4>' . $this->l('Border radius') . ' : </h4>
		 	 <div class="form-group">
		 	  	<label>' . $this->l('Border radius top left') . ' :
		 		  	<input type="text"  name="param7" value="' . $navArr ['param7'] . '" id="navigationmain_borderradius1"/>
		 	  	</label>
		 	  	<br />
		
		 	  	<div id="navigationmain_borderradius1_slider" class="navigationmain_borderradius"></div>
				<br />
		 	  			
		 	  	<label>' . $this->l('Border top right') . ' :
		 	  		<input type="text"  name="param8" value="' . $navArr ['param8'] . '" id="navigationmain_borderradius2"/>
		 	  	</label>
		 	  	<br />
		
		 	  	<div id="navigationmain_borderradius2_slider" class="navigationmain_borderradius"></div>
				<br />

		 	  	<label>' . $this->l('Border bottom right') . ' :
		 	 	 	<input type="text"  name="param9" value="' . $navArr ['param9'] . '" id="navigationmain_borderradius3"/>
		 	  	</label>
		 	 	<br />
		
		 	  	<div id="navigationmain_borderradius3_slider" class="navigationmain_borderradius"></div>
				<br />

		 	  	<label>' . $this->l('Border bottom left') . ' :
		 	  		<input type="text"  name="param10" value="' . $navArr ['param10'] . '" id="navigationmain_borderradius4"/>
		 	  	</label>
		 	  	<br />

				<div id="navigationmain_borderradius4_slider" class="navigationmain_borderradius"></div>
		 	  	<br />	
	
	 	  		<label>' . $this->l('Activate radius for last menu element') . ' :
		 	  		<input type="checkbox" name="param11" ' .
				 $this->check($navArr ['param11']) . ' value="1" id="navigationmain_activateradius_lastmenu_element"/>
		 	  	</label>		
			 </div>
			

			<h4>' . $this->l('Position') . ' : </h4>
			<div class="form-group">
		 	 	<label>' . $this->l('Padding top') . ' :
		 	  		<input type="text"  name="param12" value="' . $navArr ['param12'] . '" id="navigationmain_paddingtop"/>  px
		 	  	</label>
		 	</div>
			<br/>

			<h4>' . $this->l('Titles') . ' : </h4>
					
			<div class="form-group">
				<label>' . $this->l('Font family') . ' :
 	
					<select name="param24" id="navigationmain_fontfamily">
			 	  		' . $this->getFontFamilyInputForHTML('param24', $navArr) . '
			 	  	</select>
		 	  	</label>
			 	&nbsp;
		 	 	<label>' . $this->l('Titles') . ' :
		 	  		<input type="text"  name="param13" value="' . $navArr ['param13'] . '" id="navigationmain_titles"/>
		 	  	</label>
		 	  	&nbsp;
		 	 	<label>' . $this->l('Titles hover') . ' :
		 	  		<input type="text"  name="param14" value="' . $navArr ['param14'] . '" id="navigationmain_titleshover"/>
		 	  	</label>
		 	  	&nbsp;
		 	  	<label>' . $this->l('Titles background hover') . ' :
		 	  		<input type="text"  name="param15" value="' . $navArr ['param15'] . '" id="navigationmain_titlesbghover"/>
		 	  	</label>		 	  			
		 	  	&nbsp;
		 	  	<label>' . $this->l('Titles size') . ' :
		 	  		<input type="text"  name="param16" value="' . $navArr ['param16'] . '" id="navigationmain_fontsize"/>  px
		 	  	</label>
		 	 </div>

		 	<h2>' . $this->l('Sub menu') . '</h2>
			<div class="form-group">
		 		<label>' . $this->l('Titles submenu gradient 1') . ' :
		 	  		<input type="text"  name="param18" value="' . $navArr ['param18'] . '" id="navigationsub_bg_gc1"/>
		 	  	</label>
		 	  	&nbsp;
		 	  	<label>' . $this->l('Titles submenu gradient 1') . ' :
		 	  		<input type="text"  name="param19" value="' . $navArr ['param19'] . '" id="navigationsub_bg_gc2"/>
		 	  	</label>
		 	  	<br />

				<div class="form-group" id="navigationsub_bg_gradientsens">
			 		<label>' . $this->l('Gradient type') . ' :</label>
	 					' . $this->getGradientInputForHTML('param17', $navArr) . '
	 				
			 	</div>
	 			<br />

		 	  	<label>' . $this->l('Titles submenu') . ' :
		 	  		<input type="text"  name="param20" value="' . $navArr ['param20'] . '" id="navigationsub_titles_cat1"/>
		 	  	</label>
		 	  	&nbsp;
		 	  	<label>' . $this->l('Titles submenu hover') . ' :
		 	  		<input type="text"  name="param21" value="' . $navArr ['param21'] . '" id="navigationsub_titles_cat1hover"/>
		 	  	</label>
		 	  	&nbsp;
		 	  	<label>' . $this->l('Titles submenu 2') . ' :
		 	  		<input type="text"  name="param22" value="' . $navArr ['param22'] . '" id="navigationsub_titles_cat2"/>
		 	  	</label>
		 	  	&nbsp;
		 	  	<label>' . $this->l('Titles submenu 2 hover') . ' :
		 	  		<input type="text"  name="param23" value="' . $navArr ['param23'] . '" id="navigationsub_titles_cat2hover"/>
		 	  	</label>
		 	  	&nbsp;

		 	</div>  				
		 	  				
		</div>
	</div>
	<hr>';
		

		$content .= "
 	<script>
		var navigationmain_gradientsens = $('#navigationmain_gradientradio input:radio:checked').val();
		var sf_menu = $('.sf-menu');
		
		var navigationmain_gc1 = $( '#navigationmain_gc1' ).val();
		var navigationmain_gc2 = $( '#navigationmain_gc2' ).val();

		var navigationmain_borderradius1 = $('#navigationmain_borderradius1').val();
		var navigationmain_borderradius2 = $('#navigationmain_borderradius2').val();
		var navigationmain_borderradius3 = $('#navigationmain_borderradius3').val();
		var navigationmain_borderradius4 = $('#navigationmain_borderradius4').val();			
		var navigationmain_bordercolor = $('#navigationmain_bordercolor').val();
		var navigationmain_borderstyle = $('#navigationmain_borderstyle').val();
		var navigationmain_borderwidth = $('#navigationmain_borderwidth').val();
			
		var navigationmain_paddingtop = $( '#navigationmain_paddingtop' ).val();			
 		var navigationmain_menuborderright = $( '#navigationmain_menuborderright' ).val();
 			
		var navigationmain_titles = $( '#navigationmain_titles' ).val();
 		var navigationmain_titleshover = $( '#navigationmain_titleshover' ).val();
		var navigationmain_titlesbghover = $( '#navigationmain_titlesbghover' ).val();
		
 			
		var navigationmain_fontfamily = $('#navigationmain_fontfamily').val();
		var navigationmain_fontsize = $('#navigationmain_fontsize').val();
		var navigationmain_fontcolor = $('#navigationmain_fontcolor').val();
		
		//submenu
		var navigationsub_bg_gradientsens = $('#navigationsub_bg_gradientsens input:radio:checked').val();
		var navigationsub_bg_gc1 = $('#navigationsub_bg_gc1').val();
		var navigationsub_bg_gc2 = $('#navigationsub_bg_gc2').val();
				
				
		var navigationsub_titles_cat1 = $('#navigationsub_titles_cat1').val();
		var navigationsub_titles_cat1hover = $('#navigationsub_titles_cat1hover').val();
		var navigationsub_titles_cat2 = $('#navigationsub_titles_cat2').val();
		var navigationsub_titles_cat2hover = $('#navigationsub_titles_cat2hover').val();

		$( document ).ready( applyStyle_navigationmain );
		$( document ).ready( applyStyle_navigationsub );


		function applyStyle_navigationmain()
		{
			sf_menu.attr('style','background:transparent; ' + generateGradientOnlyCode( navigationmain_gradientsens, navigationmain_gc1, navigationmain_gc2 ) +
								'color:' + navigationmain_titles +';font-family:' + navigationmain_fontfamily + ';'
								+ 'border-radius:' + navigationmain_borderradius1 + 'px ' + navigationmain_borderradius2 + 'px ' + navigationmain_borderradius3 + 'px ' + navigationmain_borderradius4 + 'px;'
						);
		
			
			// Internal borders
			$('ul.sf-menu > li').css('border-right',' 1px solid ' + navigationmain_titles );
			$('.sf-menu ul li:first-child').css('border-left', 'none');

			// External borders
			sf_menu.css('border', navigationmain_borderwidth + 'px ' + navigationmain_borderstyle + ' ' + navigationmain_bordercolor);
			//Borderradius
			$('.sf-menu > li:first-child > a').attr('style', 'border-radius:' + navigationmain_borderradius1 + 'px 0 0 ' + navigationmain_borderradius4 + 'px;');
			
			if( $('#navigationmain_activateradius_lastmenu_element').prop('checked') )
			{
				$('.sf-menu > li:last-child > a').attr('style', 'border-radius: 0 ' + navigationmain_borderradius2 + 'px ' + navigationmain_borderradius3 + 'px; 0');
			}
			else
			{
				$('.sf-menu > li:last-child > a').attr('style', '');
			}
			
			//TEXT
			
			$('.sf-menu > li > a').css('color', navigationmain_titles).css( 'font-family' , navigationmain_fontfamily ).css( 'font-size' , navigationmain_fontsize +'px' );
			$('.sf-menu > li:first-child > a').css('background', navigationmain_titlesbghover).css('color', navigationmain_titleshover).css('border-bottom',' 3px solid '+ navigationmain_titleshover  );
			$('.sf-with-ul').css( 'font-family' , navigationmain_fontfamily );
				
			
 		}
				
		$('.sf-menu > li > a').hover(
			function()
			{
				$(this).css('color' , navigationmain_titleshover ).css('background', navigationmain_titlesbghover  ).css('border-bottom',' 3px solid '+ navigationmain_titleshover  );
			},
			function()
			{
				$(this).css('color' , navigationmain_titles ).css('background','transparent' ).css('border-bottom',' 3px solid #e9e9e9'  );
				applyStyle_navigationmain();
			}
		);

		" .
				 $this->getSpectrumJSForHTML('#navigationmain_bordercolor', 'navigationmain_bordercolor', 'applyStyle_navigationmain') . "

		" .
				 $this->getSpectrumJSForHTML('#navigationmain_gc1', 'navigationmain_gc1', 'applyStyle_navigationmain') . "
		
		" .
				 $this->getSpectrumJSForHTML('#navigationmain_gc2', 'navigationmain_gc2', 'applyStyle_navigationmain') . "
	
		$('#navigationmain_gradientradio input:radio').change(
		    function()
		    {
				navigationmain_gradientsens = $(this).val();
				applyStyle_navigationmain();
			}
		);
	
		// TEXT
		" .
				 $this->getSpectrumJSForHTML('#navigationmain_titles', 'navigationmain_titles', 'applyStyle_navigationmain') . "
	
		
		$('#navigationmain_fontsize').on('change',
		    function()
		    {
		        navigationmain_fontsize = $(this).val();
				applyStyle_navigationmain();
			}
		);
		$('#navigationmain_fontfamily').on('change',
		    function()
		    {
		        navigationmain_fontfamily = $(this).val();
				applyStyle_navigationmain();
			}
		);
				
		" .
				 $this->getSpectrumJSForHTML('#navigationmain_titleshover', 'navigationmain_titleshover', 'applyStyle_navigationmain') . "

		" .
				 $this->getSpectrumJSForHTML('#navigationmain_titlesbghover', 'navigationmain_titlesbghover', 'applyStyle_navigationmain') . "
		
		$('.sf_menu li span').hover(
			function ()
			{
			    $(this).css('background', navigationmain_titlesbghover).css('color', navigationmain_titleshover);
			 },
			 function ()
			 {
				 applyStyle_navigationmain();
			 }
		);
		
		$('#navigationmain_borderradius1_slider').slider();
		$('#navigationmain_borderradius1_slider').slider( 'option',{max:100,min:0,step:1,value: navigationmain_borderradius1});
		$('#navigationmain_borderradius1_slider').on( 'slide',
			function( event, ui )
			{
				navigationmain_borderradius1 = ui.value;
				$('#navigationmain_borderradius1').val(ui.value);
				applyStyle_navigationmain();
			} );
		
		$('#navigationmain_borderradius2_slider').slider();
		$('#navigationmain_borderradius2_slider').slider( 'option',{max:100,min:0,step:1,value: navigationmain_borderradius2});
		$('#navigationmain_borderradius2_slider').on( 'slide',
			function( event, ui )
			{
				navigationmain_borderradius2 = ui.value;
				$('#navigationmain_borderradius2').val(ui.value);
				applyStyle_navigationmain();
			} );
		
		$('#navigationmain_borderradius3_slider').slider();
		$('#navigationmain_borderradius3_slider').slider( 'option',{max:100,min:0,step:1,value: navigationmain_borderradius3});
		$('#navigationmain_borderradius3_slider').on( 'slide',
			function( event, ui )
			{
				navigationmain_borderradius3 = ui.value;
				$('#navigationmain_borderradius3').val(ui.value);
				applyStyle_navigationmain();
			} );
		
	
		$('#navigationmain_borderradius4_slider').slider();
		$('#navigationmain_borderradius4_slider').slider( 'option',{max:100,min:0,step:1,value: navigationmain_borderradius4});
		$('#navigationmain_borderradius4_slider').on( 'slide',
		function( event, ui )
		{
			navigationmain_borderradius4 = ui.value;
			$('#navigationmain_borderradius4').val(ui.value);
			applyStyle_navigationmain();
		} );
	
		$('#navigationmain_borderradius1').on('change paste keyup',
		    function()
		    {
		        navigationmain_borderradius1 = $(this).val();
				$('#navigationmain_borderradius1_slider').slider( 'option', 'value', navigationmain_borderradius1 );
				applyStyle_navigationmain();
			}
		);

		$('#navigationmain_borderradius2').on('change paste keyup',
		    function()
		    {
		        navigationmain_borderradius2 = $(this).val();
				$('#navigationmain_borderradius2_slider').slider( 'option', 'value', navigationmain_borderradius2 );
				applyStyle_navigationmain();
			}
		);

		$('#navigationmain_borderradius3').on('change paste keyup',
		    function()
		    {
		        navigationmain_borderradius3 = $(this).val();
				$('#navigationmain_borderradius3_slider').slider( 'option', 'value', navigationmain_borderradius3 );
				applyStyle_navigationmain();
			}
		);

		$('#navigationmain_borderradius4').on('change paste keyup',
		    function()
		    {
		        navigationmain_borderradius4 = $(this).val();
				$('#navigationmain_borderradius4_slider').slider( 'option', 'value', navigationmain_borderradius4 );
				applyStyle_navigationmain();
			}
		);

		$('#navigationmain_activateradius_lastmenu_element').change(
		    function()
		    {
				$('.sf-menu > li:last-child').addTemporaryClass('sfHoverForce', 2000);					
				applyStyle_navigationmain();
			}
		);
					
		$('#navigationmain_borderwidth').on('change paste keyup',
		    function()
		    {
		        navigationmain_borderwidth = $(this).val();
				applyStyle_navigationmain();
			}
		);			
		
		$('#navigationmain_borderstyle').change(
		    function()
		    {
		        navigationmain_borderstyle = $(this).val();
				applyStyle_navigationmain();
			}
		);

		$('#navigationmain_paddingtop').on('change paste keyup',
		    function()
		    {
		       $('#block_top_menu').css('padding-top', $(this).val() + 'px' );
			}
		);	
		

		// SUBMENU
		function applyStyle_navigationsub()
		{
			$('.sf-menu > li > ul.submenu-container ').attr('style', 'display:block;background:transparent; ' + generateGradientOnlyCode( navigationsub_bg_gradientsens, navigationsub_bg_gc1, navigationsub_bg_gc2 ) );
			$('.sf-menu > li > ul > li > a ').css('color', navigationsub_titles_cat1  );
			$('.sf-menu li li li a ').css('color', navigationsub_titles_cat2  );
			
 		}
				
		$('#navigationsub_bg_gradientsens input:radio').change(
		    function()
		    {
				navigationsub_bg_gradientsens = $(this).val();
				applyStyle_navigationsub();
			}
		);

		" .
				 $this->getSpectrumJSForHTML('#navigationsub_bg_gc1', 'navigationsub_bg_gc1', 'applyStyle_navigationsub') . "
		" .
				 $this->getSpectrumJSForHTML('#navigationsub_bg_gc2', 'navigationsub_bg_gc2', 'applyStyle_navigationsub') . "
		" .
				 $this->getSpectrumJSForHTML('#navigationsub_titles_cat1', 'navigationsub_titles_cat1', 'applyStyle_navigationsub') . "
		" .
				 $this->getSpectrumJSForHTML('#navigationsub_titles_cat2', 'navigationsub_titles_cat2', 'applyStyle_navigationsub') . "
		" .
				 $this->getSpectrumJSForHTML('#navigationsub_titles_cat1hover', 'navigationsub_titles_cat1hover', 'applyStyle_navigationsub') . "
		" .
				 $this->getSpectrumJSForHTML('#navigationsub_titles_cat2hover', 'navigationsub_titles_cat2hover', 'applyStyle_navigationsub') . "

		$('.sf-menu > li > ul > li > a ').hover(
			function ()
			{
			    $(this).css('color', navigationsub_titles_cat1hover);
			 },
			 function ()
			 {
				 applyStyle_navigationsub();
			 }
		);
		$('.sf-menu li li li a ').hover(
			function ()
			{
			    $(this).css('color', navigationsub_titles_cat2hover);
			 },
			 function ()
			 {
				 applyStyle_navigationsub();
			 }
		);
					
					
		</script>";
		return $content;
	}

	private function cfgNavigationSubmenu($navArr)
	{
	} 
	
	// ####################### CONFIG CSS ##############################
	private function configCss($cssArr, $idTab)
	{
		if ( empty($cssArr) )
		{
			$cssArr ['active'] = '';
			$cssArr ['data'] = '';
		}
		
		$output = '
	<div class="container-fluid">
        <form name="configCss" action="' .
				 $_SERVER ['REQUEST_URI'] . $idTab . '" method="post" enctype="multipart/form-data" role="form" class="form-inline">
			<input type="hidden" name="onglet" value="onglet_8" />
			<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
			<input type="hidden" name="form" value="css" />
			
			<div class="ddlxaviable">' . $this->l('Only available in pro edition : ') . '  <a href="http://www.evolution-x.fr" target="_blank">' . $this->l('get pro now') . ' </a></div> 
 	
        		
			<h1>' . $this->l('Add your custom css') . '</h1>
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
			<hr>
			
			<a href="http://www.evolution-x.fr/tips-astuces/">' . $this->l('Link to custom CSS tips !') . '</a>
			<br/>
			<div class="form-group">
				<label> ' . $this->l('Enable your custom css') . ' :</label>
				<input type="checkbox" name="param1" ' . $this->check($cssArr ['active']) . ' value="1" />
			</div>
			<br/>		
			<strong>' . $this->l('Here you can add your own Css code') . ' : </strong>			

			<textarea  width="770px" id="css" name="param2">' . $cssArr ['data'] . '</textarea>

			<br/>
			<div class="form-group">
				<label>' . $this->l('Here you can upload a picture') .
				 ' :</label>
				<input style="width: 155px;" type="file" name="upimage"/></br>
			</div>

			<br/>
			<mark>' .
				 $this->l('Path of your image in CSS :') . '  modules/' . $this->name . DIRECTORY_SEPARATOR . 'img/' . $this->currentProfileName .
				 $this->l('/image_name.jpg or .png') . '</mark>

			<hr>	
			<input type="submit" name="submit" class="btn btn-primary" value="' . $this->l('save modification') . '" />
		</form>
	</div>';
		
		$output .= '
			<script type="text/javascript">
				$(document).ready( function()
				{
					var editor = CodeMirror.fromTextArea(document.getElementById("css"), 
					{
					"theme" : "eclipse",
					"autofocus" : true,
					tabMode: "indent",
					lineNumbers: true
					});

					//setTimeout( editor.refresh, 5000 );
	
				});
			</script>';
		
		return $output;
	}
	

	// ####################### CONFIG PROFILE ##################################
	private function configProfile($idTab)
	{
		$output = '';
		$profileList = '';
		$profileDisplay = '';
		
		$profileListFromBd = $this->getProfileList();
		
		$sql = 'SELECT id_shop, name FROM ' . _DB_PREFIX_ . 'shop
				WHERE 1';
		
		$result = $this->dbi->ExecuteS($sql);
		

		foreach ( $profileListFromBd as $id => $profile )
		{
			$profileList .= '<option value="' . $id . '">' . $profile ['name'] . '</option>';
			
			$active = ( $profile ['active'] == 1 ) ? 'active' : 'inactive';
			
			$profileName = $profile ['name'];
			
			$profileColor = $this->getProfileColors($id);
			

			$profileEditActivate = '<input class="' . $active . '" type="submit" name="activate" value="' . $id . '" title="' . $this->l('activate or edit this profile') . '"/>';
			
			if ( $profile ['active'] == 1 )
			{
				if ( ! empty($result) )
				{
					foreach ( $result as $row )
					{
						if ( (int) $row ['id_shop'] === (int) $profile ['id_shop'] )
						{
							$profileEditActivate .= 'shop : ' . $row ['name'];
						}
					}
				}
			}
			
			$profileDelete = '<input class="delete" type="submit" name="deleteProfile" value="' . $id . '" title="' . $this->l('delete this profile') . '"  />';
			
			$profileExport = '<input class="export" type="submit" name="export" value="' . $id . '" title="' . $this->l('export this profile') . '" />';
			
			$profileExportTheme = '<input class="export" type="submit" name="export_theme" value="' . $id . '" title="' . $this->l('export as theme') . '" />';
			
			$profileDisplay .= '
							<tr>
								<td>' . $profileName . '</td>
								<td>' . $profileColor . '</td>
								<td>' . $profileEditActivate . '</td>
								<td>' . $profileDelete . '</td>
								<td>' . $profileExport . '</td>
								<td>' . $profileExportTheme . '</td>
							</tr>';
		}
		
		$output .= '
	<div class="container-fluid">
		<form action="' . $_SERVER ['REQUEST_URI'] . $idTab . '" method="post" enctype="multipart/form-data" role="form" class="form-inline">
			<input type="hidden" name="onglet" value="onglet_9" />
            <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
            <input type="hidden" name="form" value="profile" />

			<div class="ddlxaviable">' . $this->l('Only available in pro edition : ') . '  <a href="http://www.evolution-x.fr" target="_blank">' . $this->l('get pro now') . ' </a></div> 

			
			<h1>' . $this->l('Profiles manager') . '</h1>
			
			<div class="row">
	        	<div class="col-md-12">
					<h4>' . $this->l('create a new copy') . '</h4>
					' . $this->l('from') . ' : <select name="profileToCopy">' . $profileList . '</select>
		
					<div class="form-group">
						' . $this->l('to') . '
			            <input type="text" id="profile_newName" name="newName" value="' . $this->l('newname') . '"/>
			            <input type="submit" class="btn btn-primary" type="submit" name="new_template" value="' . $this->l('duplicate') . '" />
			        </div>
			        <br />
					
					<span class="advise">' . $this->l('no spaces nor special characters') . '</span>
					<br />
					<hr/>
							
					<h2>' . $this->l('Import a profile') . '</h2>
					' . $this->l('upload a .gzip file from disk') . ' : <br />
					<input type="file" name="importFile" size="3">
					<input type="submit" class="btn btn-primary" type="submit" name="import" value="' . $this->l('send file') . '" />
					<hr/>
							
					<h2>' . $this->l('Profile List') . '</h2>							
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th class="">' . $this->l('Profile Name') . ' </th>
								<th class="">' . $this->l('Palette preview') . ' </th>
								<th class="">' . $this->l('Edit/Activate') . ' </th>
								<th class="">' . $this->l('Delete') . ' </th>
								<th class="">' . $this->l('Export as profile') . ' </th>
								<th class="">' . $this->l('Export as theme') . ' </th>
							</tr>
						</thead>
						<tbody>
							' . $profileDisplay . '
						</tbody>
					</table>
					<hr/>

				</div>
			</div>
		</form>
	</div>';
		return $output;
	}
	

	// ####################### CONFIG LICENCE ##############################
	private function configLicence($idTab)
	{
		$output = '<h2>' .
				 $this->l('INFORMATIONS') .
				 '</h2>
				<div id="fred">
				
					<div class="photofred">
						<img src="http://www.ddlx.org/wp-content/uploads/sites/9/2016/02/avatar2016.jpg"  width="100px" />
						
						
						
					</div>
					<div class"fredtexte">Bonjour </br> 
									
					
					Je suis <a href="http://woofrance.fr/expert/frederic-p/" target="_blank"> Frédéric Puech</a>, acteur du web & passionné depuis 2002. <a href="http://woofrance.fr/expert/frederic-p/" target="_blank"> (Voir mon CV)</a></br>  
					- Expert création site web de présentation et Ecomemrce</br>  
					- Expert Prestashop depuis 2009 </br>  
					- Expert Wordpress / WooCommerce </br> 
					- Expert référencement SEO </br> </br> 
					- Fondateur de la société <a href="http://ddlx.org" target="_blank"> DDLX </a></br> 
					- Fondtateur du <a href="http://leswebmastersdumidi.fr/" target="_blank"> regroupement des webmasters du midi</a></br> 
					- Fondtateur du site communautaire WooCommerce<a href="http://woofrance.fr/" target="_blank">  WooFrance.fr</a></br> </br> 
					
					Si vous avez besion de mes services pour la mise en place de votre site Web qu\'il sagisse d\'un site de présentation ou d\'un site E-comemrce n\'hésitez pas a <a href="http://www.ddlx.org/contact-et-support/" target="_blank">  me contacter </a> 
					
					
					</div>
				<br>
						
						<a href="http://www.ddlx.org/plaquettes-pdf-ddlx/" target="_blank"><img src="' .
				 $this->realpath . 'img/data/pdf.png"/>  ' . $this->l('Téléchargez notre plaquette PDF') . '</a>
				 &nbsp;
				 </div>

                  <br/><br/>
				   ';
		
		return $output;
	}

}
?>