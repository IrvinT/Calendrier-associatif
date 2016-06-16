<?php
if (!defined('_PS_VERSION_'))
	exit;

class HomecategoriesPlus extends Module
{
	public function __construct()
	{
		$this->name = 'homecategoriesplus';
		$this->tab = 'front_office_features';
		$this->version = 0.8;
		$this->author = 'ckarone';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();  // The parent construct 

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Homepage Category Plus');
		$this->description = 'Display categories, sub-categories & product(s) on your homepage.';
		$this->confirmUninstall = $this->l('Do you realy want to uninstal Homecategoriesplus?');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}
	
	public function install()
	{
		include(dirname(__FILE__).'/sql/install.php');
		return parent::install() &&
			$this->installFixtures(Language::getLanguages(true)) &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('displayHome');
	}

	protected function installFixture($id_shop, $nbr, $nbr_sub,  $nbr_size,  $nbr_size_h,  $nbr_size_p, $nbr_size_l, $nbr_marg_p, $nbr_mw, $product, $product_odr, $product_btn, $product_price, $product_b, $product_bc, $cat, $cat_img, $subcat_img, $blockb,  $blockbc, $sub1, $sub2, $sub3, $sub4, $sub5, $sub6, $sub7, $sub8, $sub9, $sub10, $sub11, $sub12, $sub13, $sub14, $sub15, $sub16, $sub17, $sub18, $sub19, $sub20, $sub21, $sub22, $sub23, $sub24, $sub25)
	{
		$result = true;

		include(dirname(__FILE__).'/sql/fixture.php');

		return $result;
	}

	public function installFixtures($languages = null)
	{	
		$shop_ids = Shop::getShops(true, null, true);
		$result = true;

		foreach ($shop_ids as $id_shop)
				$result &= $this->installFixture($id_shop, 6, 4, 2, 1, 0, 'f1f2f3', 1, 3, 1, 1, 0,	0, 1, 'f1f2f3', 1, 1, 1, 1, '000000', 'eb12eb', '13131c', 50, 12, 0, '5fd624', 1, 'e8910e',	1, 1, 1, 0,	1, 1, 1, 0,	'000000', '000000', 'fc0ffc', '1421cf', 0, 0, 50, 1, 1);
			
			return $result;
	}

	public function uninstall()
	{
		include(dirname(__FILE__).'/sql/uninstall.php');
		return parent::uninstall();
	}


	public function getContent()
	{
		$output ='';
		if (Tools::isSubmit('submitHomecategoriesplus'))
		{
			$this->_clearCache('homecategoriesplus.tpl');

			if (!Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'homcatplus` SET
					nbr = "'.Tools::getValue('nbrp').'",
					nbr_sub = "'.Tools::getValue('nbrp_sub').'",
					nbr_size = "'.Tools::getValue('nbrp_size').'",
					nbr_size_h = "'.Tools::getValue('nbrp_size_h').'",
					nbr_size_l = "'.Tools::getValue('nbrp_size_l').'",
					nbr_marg_p = "'.Tools::getValue('nbrp_marge_p').'",
					product = "'.Tools::getValue('nbrp_prod').'",
					cat = "'.Tools::getValue('nbrp_cat').'",
					blockb = "'.Tools::getValue('nbrp_blockb').'",
					blockbc = "'.Tools::getValue('nbrp_blockbc').'",
					cat_img = "'.Tools::getValue('nbrp_cat_img').'",
					subcat_img = "'.Tools::getValue('nbrp_subcat_img').'",
					product_odr = "'.Tools::getValue('nbrp_prod_ord').'",
					product_btn = "'.Tools::getValue('nbrp_prod_btn').'",
					product_price = "'.Tools::getValue('nbrp_prod_price').'",
					product_b = "'.Tools::getValue('nbrp_prod_bord').'",
					product_bc = "'.Tools::getValue('nbrp_prod_bordc').'",
					sub1 = "'.Tools::getValue('nbrp_sub1').'",
					sub2 = "'.Tools::getValue('nbrp_sub2').'",
					sub3 = "'.Tools::getValue('nbrp_sub3').'",
					sub4 = "'.Tools::getValue('nbrp_sub4').'",
					sub5 = "'.Tools::getValue('nbrp_sub5').'",
					sub6 = "'.Tools::getValue('nbrp_sub6').'",
					sub7 = "'.Tools::getValue('nbrp_sub7').'",
					sub8 = "'.Tools::getValue('nbrp_sub8').'",
					sub9 = "'.Tools::getValue('nbrp_sub9').'",
					sub10 = "'.Tools::getValue('nbrp_sub10').'",
					sub11 = "'.Tools::getValue('nbrp_sub11').'",
					sub12 = "'.Tools::getValue('nbrp_sub12').'",
					sub13 = "'.Tools::getValue('nbrp_sub13').'",
					sub14 = "'.Tools::getValue('nbrp_sub14').'",
					sub15 = "'.Tools::getValue('nbrp_sub15').'",
					sub16 = "'.Tools::getValue('nbrp_sub16').'",
					sub17 = "'.Tools::getValue('nbrp_sub17').'",
					sub18 = "'.Tools::getValue('nbrp_sub18').'",
					sub19 = "'.Tools::getValue('nbrp_sub19').'",
					sub20 = "'.Tools::getValue('nbrp_sub20').'",
					sub21 = "'.Tools::getValue('nbrp_sub21').'",
					sub22 = "'.Tools::getValue('nbrp_sub22').'",
					sub23 = "'.Tools::getValue('nbrp_sub23').'",
					sub24 = "'.Tools::getValue('nbrp_sub24').'",
					sub25 = "'.Tools::getValue('nbrp_sub25').'",
					nbr_mw = "'.Tools::getValue('nbrp_mw').'"
				WHERE id_item = '.(int)Tools::getValue('id_item').' AND id_shop = '.$this->context->shop->id.'')) 
				$output .=$this->displayConfirmation($this->l('An error occurred while saving data'));
				else
				$output .=$this->displayConfirmation($this->l('Settings updated'));
		}	
		return $output.$this->_displayForm();
	}

	public function getInfos($id_shop)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'homcatplus` 
			WHERE `id_shop` = '.$id_shop;

		return Db::getInstance()->getRow($sql);
	}

	private function _displayForm()
	{
		$shop_ids = Shop::getShops(true, null, true);

		$results=$this->getInfos($this->context->shop->id);

		if($results['sub13'] == 1){
			$hideprod ='style="display:none"';
			$hideother ='';
		}else {
			$hideprod ='';
			$hideother ='style="display:none"';
		}

				 $output = '<link rel="stylesheet" media="screen" type="text/css" href="'.$this->_path.'css/colorpicker.css" />
							<script type="text/javascript" src="'.$this->_path.'js/colorpicker.js"></script>
							<style type="text/css">
							.box{border-bottom:solid 1px #ccc}
							</style> 
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post" id="module_form" class="defaultForm form-horizontal blockcms">
					<div class="col-lg-6">
					<div class="panel" id="fieldset_0">
						<div class="panel-heading">
							<i class="icon-cogs"></i> Settings
						</div>
						<div class="form-wrapper">
						<h2> '.$this->l('General').'</h2>
						<div '.$hideprod.' id="hidit2">
							<div class="form-group">
									<label class="control-label col-sm-5">'.$this->l('Number of cat.').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-xs" type="text" name="nbrp" value="'.Tools::getValue('nbrp', $results['nbr']).'" /></div>
							</div>	
							<div class="form-group">	
									<label class="control-label col-sm-5">'.$this->l('Hidden categories').'*:</label>	
									<div class="col-sm-7"><input class="form-control fixed-width-lg" type="text" name="nbrp_sub21" value="'.Tools::getValue('nbrp_sub21', $results['sub21']).'" />
									<p class="help-block">'.$this->l('Categories id\'s with comma separated').'</span></div>				
							</div>
						</div>	
							<div class="form-group">	
									<label class="control-label col-sm-5">'.$this->l('Animation').':</label>
											<div class="col-sm-7">
												<span class="switch prestashop-switch fixed-width-sm">
												<input type="radio" name="nbrp_sub13" id="nbrp_sub13_on" '.(Tools::getValue('nbrp_sub13', $results['sub13'])==1  ? 'checked="checked"' : '').'value="1">
												<label for="nbrp_sub13_on">Oui</label>
												<input type="radio" name="nbrp_sub13" id="nbrp_sub13_off" '.(Tools::getValue('nbrp_sub13', $results['sub13'])==0  ? 'checked="checked"' : '').'value="0">
												<label for="nbrp_sub13_off">Non</label>
												<a class="slide-button btn"></a>
												</span>
												<p class="help-block">'.$this->l('Jquery animation').'</span>
											</div>
							</div>
							<hr />
							<div '.$hideother.' id="hidit">
							<h2>'.$this->l('Animation').'</h2>

							<div class="form-group">
									<label class="control-label col-lg-5">'.$this->l('Animation version').':</label>
										<div class="col-sm-7"><select class="form-control fixed-width-sm" name="nbrp_sub14">
										<option value="1" '.(Tools::getValue('nbrp_sub14', $results['sub14'])== 1 ? 'selected': '').'>1</option>	
										<option value="2" '.(Tools::getValue('nbrp_sub14', $results['sub14'])== 2 ? 'selected': '').'>2</option>	
										</select></div>
							</div>
							<div class="form-group">	
									<label class="control-label col-sm-5">'.$this->l('Display cat. name').':</label>
											<div class="col-sm-7">
												<span class="switch prestashop-switch fixed-width-sm">
												<input type="radio" name="nbrp_sub15" id="nbrp_sub15_on" '.(Tools::getValue('nbrp_sub15', $results['sub15'])==1  ? 'checked="checked"' : '').'value="1">
												<label for="nbrp_sub15_on">Oui</label>
												<input type="radio" name="nbrp_sub15" id="nbrp_sub15_off" '.(Tools::getValue('nbrp_sub15', $results['sub15'])==0  ? 'checked="checked"' : '').'value="0">
												<label for="nbrp_sub15_off">Non</label>
												<a class="slide-button btn"></a>
												</span>
											</div>
							</div>
							<div class="form-group">	
									<label class="control-label col-lg-5">'.$this->l('Color cat. name').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" id="colorpickerField99" name="nbrp_sub18" value="'.Tools::getValue('nbrp_sub18', $results['sub18']).'" /></div>
							</div>							

							<div class="form-group">	
									<label class="control-label col-sm-5">'.$this->l('Display pagination').':</label>
											<div class="col-sm-7">
												<span class="switch prestashop-switch fixed-width-sm">
												<input type="radio" name="nbrp_sub16" id="nbrp_sub16_on" '.(Tools::getValue('nbrp_sub16', $results['sub16'])==1  ? 'checked="checked"' : '').'value="1">
												<label for="nbrp_sub16_on">Oui</label>
												<input type="radio" name="nbrp_sub16" id="nbrp_sub16_off" '.(Tools::getValue('nbrp_sub16', $results['sub16'])==0  ? 'checked="checked"' : '').'value="0">
												<label for="nbrp_sub16_off">Non</label>
												<a class="slide-button btn"></a>
												</span>
											</div>
							</div>
							<div class="form-group">										
									<label class="control-label col-lg-5">'.$this->l('Pagination color').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" id="colorpickerField9" name="nbrp_sub17" value="'.Tools::getValue('nbrp_sub17', $results['sub17']).'" /></div>
							</div>	
							<div class="form-group">	
									<label class="control-label col-sm-5">'.$this->l('Auto play').':</label>
											<div class="col-sm-7">
												<span class="switch prestashop-switch fixed-width-sm">
												<input type="radio" name="nbrp_sub25" id="nbrp_sub25_on" '.(Tools::getValue('nbrp_sub25', $results['sub25'])==1  ? 'checked="checked"' : '').'value="1">
												<label for="nbrp_sub25_on">Oui</label>
												<input type="radio" name="nbrp_sub25" id="nbrp_sub25_off" '.(Tools::getValue('nbrp_sub25', $results['sub25'])==0  ? 'checked="checked"' : '').'value="0">
												<label for="nbrp_sub25_off">Non</label>
												<a class="slide-button btn"></a>
												</span>
											</div>
							</div>

							<hr />
							</div><!--hide animation-->	
							<h2>'.$this->l('Top categories').'</h2>
							<div '.$hideprod.' id="hidit4">
							<div class="form-group">	
									<label class="control-label col-lg-5">'.$this->l('Categories per line on PC').':</label>				
										<div class="col-sm-7">
											<select name="nbrp_mw" class="form-control fixed-width-sm">	
												<option value="1" '.(Tools::getValue('nbrp_mw', $results['nbr_mw'])== 1 ? 'selected': '').'>'.$this->l('1').'</option>	
												<option value="2" '.(Tools::getValue('nbrp_mw', $results['nbr_mw'])== 2 ? 'selected': '').'>'.$this->l('2').'</option>	
												<option value="3" '.(Tools::getValue('nbrp_mw', $results['nbr_mw'])== 3 ? 'selected': '').'>'.$this->l('3').'</option>	
												<option value="4" '.(Tools::getValue('nbrp_mw', $results['nbr_mw'])== 4 ? 'selected': '').'>'.$this->l('4').'</option>	
											</select>
										</div>
							</div>
							<div class="form-group">
									<label class="control-label col-lg-5">'.$this->l('Categories per line on tablet').':</label>				
										<div class="col-sm-7">
											<select name="nbrp_size" class="form-control fixed-width-sm">	
												<option value="1" '.(Tools::getValue('nbrp_size', $results['nbr_size'])== 1 ? 'selected': '').'>'.$this->l('1').'</option>	
												<option value="2" '.(Tools::getValue('nbrp_size', $results['nbr_size'])== 2 ? 'selected': '').'>'.$this->l('2').'</option>	
												<option value="3" '.(Tools::getValue('nbrp_size', $results['nbr_size'])== 3 ? 'selected': '').'>'.$this->l('3').'</option>	
												<option value="4" '.(Tools::getValue('nbrp_size', $results['nbr_size'])== 4 ? 'selected': '').'>'.$this->l('4').'</option>	
											</select>
										</div>
							</div>
							<div class="form-group">
									<label class="control-label col-lg-5">'.$this->l('Categories per line on smartphone').':</label>							
									<div class="col-sm-7">
										<select name="nbrp_size_h" class="form-control fixed-width-sm">	
											<option value="1" '.(Tools::getValue('nbrp_size_h', $results['nbr_size_h'])== 1 ? 'selected': '').'>'.$this->l('1').'</option>	
											<option value="2" '.(Tools::getValue('nbrp_size_h', $results['nbr_size_h'])== 2 ? 'selected': '').'>'.$this->l('2').'</option>	
										</select>
									</div>
							</div>
							</div>
							<div class="form-group">									
									<label class="control-label col-lg-5">'.$this->l('Border (in px)').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-xs" type="text"name="nbrp_blockb" value="'.Tools::getValue('nbrp_blockb', $results['blockb']).'" /></div>
							</div>
							<div class="form-group">										
									<label class="control-label col-lg-5">'.$this->l('Border color').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" name="nbrp_blockbc" id="colorpickerField2" value="'.Tools::getValue('nbrp_blockbc', $results['blockbc']).'" /></div>
							</div>
							<div class="form-group">										
									<label class="control-label col-lg-5">'.$this->l('Link color').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" id="colorpickerField8" name="nbrp_sub19" value="'.Tools::getValue('nbrp_sub19', $results['sub19']).'" /></div>
							</div>
							<div class="form-group">										
									<label class="control-label col-lg-5">'.$this->l('Link color on hover').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" id="colorpickerField10" name="nbrp_sub20" value="'.Tools::getValue('nbrp_sub20', $results['sub20']).'" /></div>
							</div>
							<div class="form-group">										
									<label class="control-label col-lg-5">'.$this->l('Title background color').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" name="nbrp_size_l" id="colorpickerField1" value="'.Tools::getValue('nbrp_size_l', $results['nbr_size_l']).'" /></div>
							</div>
							<div '.$hideprod.'id="hidit5">
							<div class="form-group">										
									<label class="control-label col-lg-5">'.$this->l('Display image').':</label>				
									<div class="col-sm-7">
										<span class="switch prestashop-switch fixed-width-sm">
										<input type="radio" name="nbrp_cat_img" id="nbrp_cat_img_on" '.(Tools::getValue('nbrp_cat_img', $results['cat_img'])==1  ? 'checked="checked"' : '').'value="1">
										<label for="nbrp_cat_img_on">Oui</label>
										<input type="radio" name="nbrp_cat_img" id="nbrp_cat_img_off" '.(Tools::getValue('nbrp_cat_img', $results['cat_img'])==0  ? 'checked="checked"' : '').'value="0">
										<label for="nbrp_cat_img_off">Non</label>
										<a class="slide-button btn"></a>
										</span>
									</div>
							</div>
							<hr />
							</div>
							
							<div '.$hideprod.' id="hidit3">
							<h2>'.$this->l('Product').'</h2>
							<div class="form-group">
								<label class="control-label col-lg-5">'.$this->l('Display product').':</label>				
									<div class="col-sm-7">
										<span class="switch prestashop-switch fixed-width-sm">
										<input type="radio" name="nbrp_prod" id="nbrp_prod_on" '.(Tools::getValue('nbrp_prod', $results['product'])==1  ? 'checked="checked"' : '').'value="1">
										<label for="nbrp_prod_on">Oui</label>
										<input type="radio" name="nbrp_prod" id="nbrp_prod_off" '.(Tools::getValue('nbrp_prod', $results['product'])==0  ? 'checked="checked"' : '').'value="0">
										<label for="nbrp_prod_off">Non</label>
										<a class="slide-button btn"></a>
										</span>
									</div>
							</div>
							<div class="form-group">	
									<label class="control-label col-sm-5">'.$this->l('Product quantity').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-xs" type="text" name="nbrp_sub10" value="'.Tools::getValue('nbrp_sub10', $results['sub10']).'" /></div>
							</div>			
							<div class="form-group">
								<label class="control-label col-lg-5">'.$this->l('Show product on smartphone').':</label>
								<div class="col-sm-7">
										<span class="switch prestashop-switch fixed-width-sm">
										<input type="radio" name="nbrp_sub11" id="nbrp_sub11_on" '.(Tools::getValue('nbrp_sub11', $results['sub11'])==1  ? 'checked="checked"' : '').'value="1">
										<label for="nbrp_sub11_on">Oui</label>
										<input type="radio" name="nbrp_sub11" id="nbrp_sub11_off" '.(Tools::getValue('nbrp_sub11', $results['sub11'])==0  ? 'checked="checked"' : '').'value="0">
										<label for="nbrp_sub11_off">Non</label>
										<a class="slide-button btn"></a>
										</span>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-5">'.$this->l('Show product on tablet').':</label>
									<div class="col-sm-7">
										<span class="switch prestashop-switch fixed-width-sm">
										<input type="radio" name="nbrp_sub12" id="nbrp_sub12_on" '.(Tools::getValue('nbrp_sub12', $results['sub12'])==1  ? 'checked="checked"' : '').'value="1">
										<label for="nbrp_sub12_on">Oui</label>
										<input type="radio" name="nbrp_sub12" id="nbrp_sub12_off" '.(Tools::getValue('nbrp_sub12', $results['sub12'])==0  ? 'checked="checked"' : '').'value="0">
										<label for="nbrp_sub12_off">Non</label>
										<a class="slide-button btn"></a>
										</span>
									</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-5">'.$this->l('Display by').':</label>
									<div class="col-sm-7"><select class="form-control fixed-width-sm" name="nbrp_prod_ord">
									<option value="1" '.(Tools::getValue('nbrp_prod_ord', $results['product_odr'])== 1 ? 'selected': '').'>'.$this->l('New').'</option>	
									<option value="2" '.(Tools::getValue('nbrp_prod_ord', $results['product_odr'])== 2 ? 'selected': '').'>'.$this->l('Position').'</option>	
									<option value="3" '.(Tools::getValue('nbrp_prod_ord', $results['product_odr'])== 3 ? 'selected': '').'>'.$this->l('Shuffle').'</option>	
									<option value="4" '.(Tools::getValue('nbrp_prod_ord', $results['product_odr'])== 4 ? 'selected': '').'>'.$this->l('Update').'</option>	
									</select></div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-5">'.$this->l('Display "add to cart"').':</label>				
									<div class="col-sm-7">
										<span class="switch prestashop-switch fixed-width-sm">
										<input type="radio" name="nbrp_prod_btn" id="nbrp_prod_btn_on" '.(Tools::getValue('nbrp_prod_btn', $results['product_btn'])==1  ? 'checked="checked"' : '').'value="1">
										<label for="nbrp_prod_btn_on">Oui</label>
										<input type="radio" name="nbrp_prod_btn" id="nbrp_prod_btn_off" '.(Tools::getValue('nbrp_prod_btn', $results['product_btn'])==0  ? 'checked="checked"' : '').'value="0">
										<label for="nbrp_prod_btn_off">Non</label>
										<a class="slide-button btn"></a>
										</span>
									</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-5">'.$this->l('Display price').':</label>
									<div class="col-sm-7">
										<span class="switch prestashop-switch fixed-width-sm">
										<input type="radio" name="nbrp_prod_price" id="nbrp_prod_price_on" '.(Tools::getValue('nbrp_prod_price', $results['product_price'])==1  ? 'checked="checked"' : '').'value="1">
										<label for="nbrp_prod_price_on">Oui</label>
										<input type="radio" name="nbrp_prod_price" id="nbrp_prod_price_off" '.(Tools::getValue('nbrp_prod_price', $results['product_price'])==0  ? 'checked="checked"' : '').'value="0">
										<label for="nbrp_prod_price_off">Non</label>
										<a class="slide-button btn"></a>
										</span>
									</div>
							</div>
							<div class="form-group">										
								<label class="control-label col-lg-5">'.$this->l('Border (in px)').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-xs"type="text" name="nbrp_prod_bord" value="'.Tools::getValue('nbrp_prod_bord', $results['product_b']).'" /></div>
							</div>
							<div class="form-group">										
								<label class="control-label col-lg-5">'.$this->l('Border color').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-sm"type="text" name="nbrp_prod_bordc" id="colorpickerField4" value="'.Tools::getValue('nbrp_prod_bordc', $results['product_bc']).'" /></div>
							</div>
							<div class="form-group">
									<label class="control-label col-lg-5">'.$this->l('Product description').':</label>	
									<div class="col-sm-7">
										<span class="switch prestashop-switch fixed-width-sm">
										<input type="radio" name="nbrp_marge_p" id="nbrp_marge_p_on" '.(Tools::getValue('nbrp_marge_p', $results['nbr_marg_p'])==1  ? 'checked="checked"' : '').'value="1">
										<label for="nbrp_marge_p_on">Oui</label>
										<input type="radio" name="nbrp_marge_p" id="nbrp_marge_p_off" '.(Tools::getValue('nbrp_marge_p', $results['nbr_marg_p'])==0  ? 'checked="checked"' : '').'value="0">
										<label for="nbrp_marge_p_off">Non</label>
										<a class="slide-button btn"></a>
										</span>
									</div>
							</div>								
							<div class="form-group">										
								<label class="control-label col-lg-5">'.$this->l('Descrition length').':</label>				
									<div class="col-sm-7"><input class="form-control fixed-width-xs"type="text" name="nbrp_sub23" value="'.Tools::getValue('nbrp_sub23', $results['sub23']).'" /></div>
							</div>
			  				<hr />
							<div>
							<h2>'.$this->l('Subcategories').'</h2>
							<div class="form-group">
									<label class="control-label col-lg-5">'.$this->l('Display subcategories').':</label>	
									<div class="col-sm-7">
										<span class="switch prestashop-switch fixed-width-sm">
										<input type="radio" name="nbrp_cat" id="nbrp_cat_on" '.(Tools::getValue('nbrp_cat', $results['cat'])==1  ? 'checked="checked"' : '').'value="1">
										<label for="nbrp_cat_on">Oui</label>
										<input type="radio" name="nbrp_cat" id="nbrp_cat_off" '.(Tools::getValue('nbrp_cat', $results['cat'])==0  ? 'checked="checked"' : '').'value="0">
										<label for="nbrp_cat_off">Non</label>
										<a class="slide-button btn"></a>
										</span>
									</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-5">'.$this->l('Nb of sub cat.').':</label>				
								<div class="col-sm-7"><input class="form-control fixed-width-xs" type="text" name="nbrp_sub" value="'.Tools::getValue('nbrp_sub', $results['nbr_sub']).'" /></div>
							</div>									
							<div class="form-group">
								<label class="control-label col-lg-5">'.$this->l('Background color').':</label>
								<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" name="nbrp_sub22" id="colorpickerField77" value="'.Tools::getValue('nbrp_sub22', $results['sub22']).'" /></div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-5">'.$this->l('Link color').':</label>				
								<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" name="nbrp_sub1" id="colorpickerField3" value="'.Tools::getValue('nbrp_sub1', $results['sub1']).'" /></div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Background hover effect').':</label>				
								<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" name="nbrp_sub2" id="colorpickerField5" value="'.Tools::getValue('nbrp_sub2', $results['sub2']).'" /></div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Link hover effect').':</label>				
								<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" name="nbrp_sub6" id="colorpickerField6" value="'.Tools::getValue('nbrp_sub6',$results['sub6']).'" /></div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Subcategories name').':</label>				
								<div class="col-sm-7">
									<span class="switch prestashop-switch fixed-width-sm">
									<input type="radio" name="nbrp_sub24" id="nbrp_sub24_on" '.(Tools::getValue('nbrp_sub24', $results['sub24'])==1  ? 'checked="checked"' : '').'value="1">
									<label for="nbrp_sub24_on">Oui</label>
									<input type="radio" name="nbrp_sub24" id="nbrp_sub24_off" '.(Tools::getValue('nbrp_sub24',$results['sub24'])==0  ? 'checked="checked"' : '').'value="0">
									<label for="nbrp_sub24_off">Non</label>
									<a class="slide-button btn"></a>
									</span>
								</div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Subategorie name length').':</label>				
								<div class="col-sm-7"><input class="form-control fixed-width-xs" type="text" name="nbrp_sub3" value="'.Tools::getValue('nbrp_sub3', $results['sub3']).'" /></div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Border (in px)').':</label>				
								<div class="col-sm-7"><input class="form-control fixed-width-xs" type="text" name="nbrp_sub9" value="'.Tools::getValue('nbrp_sub9', $results['sub9']).'" /></div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Font size').':</label>				
								<div class="col-sm-7"><input class="form-control fixed-width-xs" type="text" name="nbrp_sub4" value="'.Tools::getValue('nbrp_sub4', $results['sub4']).'" /></div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Link underline').':</label>				
								<div class="col-sm-7">
									<span class="switch prestashop-switch fixed-width-sm">
									<input type="radio" name="nbrp_sub5" id="nbrp_sub5_on" '.(Tools::getValue('nbrp_sub5', $results['sub5'])==1  ? 'checked="checked"' : '').'value="1">
									<label for="nbrp_sub5_on">Oui</label>
									<input type="radio" name="nbrp_sub5" id="nbrp_sub5_off" '.(Tools::getValue('nbrp_sub5',$results['sub5'])==0  ? 'checked="checked"' : '').'value="0">
									<label for="nbrp_sub5_off">Non</label>
									<a class="slide-button btn"></a>
									</span>
								</div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Display top category link').':</label>	
								<div class="col-sm-7">
									<span class="switch prestashop-switch fixed-width-sm">
									<input type="radio" name="nbrp_sub7" id="nbrp_sub7_on" '.(Tools::getValue('nbrp_sub7', $results['sub7'])==1  ? 'checked="checked"' : '').'value="1">
									<label for="nbrp_sub7_on">Oui</label>
									<input type="radio" name="nbrp_sub7" id="nbrp_sub7_off" '.(Tools::getValue('nbrp_sub7', $results['sub7'])==0  ? 'checked="checked"' : '').'value="0">
									<label for="nbrp_sub7_off">Non</label>
									<a class="slide-button btn"></a>
									</span>
								</div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Category color link').':</label>				
								<div class="col-sm-7"><input class="form-control fixed-width-sm" type="text" name="nbrp_sub8" id="colorpickerField7" value="'.Tools::getValue('nbrp_sub8', $results['sub8']).'" /></div>
							</div>
							<div class="form-group">									
								<label class="control-label col-lg-5">'.$this->l('Display sub-cat images').':</label>	
								<div class="col-sm-7">
									<span class="switch prestashop-switch fixed-width-sm">
									<input type="radio" name="nbrp_subcat_img" id="nbrp_subcat_img_on" '.(Tools::getValue('nbrp_subcat_img', $results['subcat_img'])==1  ? 'checked="checked"' : '').'value="1">
									<label for="nbrp_subcat_img_on">Oui</label>
									<input type="radio" name="nbrp_subcat_img" id="nbrp_subcat_img_off" '.(Tools::getValue('nbrp_subcat_img', $results['subcat_img'])==0  ? 'checked="checked"' : '').'value="0">
									<label for="nbrp_subcat_img_off">Non</label>
									<a class="slide-button btn"></a>
									</span>
								</div>
							</div>
							</div>
							</div>
									<input type="hidden" name="id_item" value="'.Tools::getValue('id_item', $results['id_item']).'" />
							</div><!--fin form-wrapper--> 	
						 	
							<div class="panel-footer">
										<button type="submit" value="1" id="module_form_submit_btn" name="submitHomecategoriesplus" class="btn btn-default pull-right">
											<i class="process-icon-save"></i> '.$this->l('Save configuration').'
										</button>
							</div><!--fin panel-footer--> 
					</div><!--fin panel--> 	
					</div><!--fin col-lg-6-->	
			</form>';		
									$output .='<div class="col-lg-6"><div class="panel">';
									$output .= $this->_displayDon();
									$output .= $this->_displayCredits();
									$output .='</div></div>		

					<script type="text/javascript">
						$(\'#colorpickerField1, #colorpickerField2, #colorpickerField3, #colorpickerField4, #colorpickerField5, #colorpickerField6, #colorpickerField7, #colorpickerField77, #colorpickerField8, #colorpickerField9, #colorpickerField10, #colorpickerField99\').ColorPicker({
						onSubmit: function(hsb, hex, rgb, el) {
						$(el).val(hex);
						$(el).ColorPickerHide();
						},
						onBeforeShow: function () {
						$(this).ColorPickerSetColor(this.value);
						}
						})
						.bind(\'keyup\', function(){
						$(this).ColorPickerSetColor(this.value);
						});
					</script>
					<br style="clear:both" />
					<script type="text/javascript">
						$(document).ready(function () {
						    $(\'#nbrp_sub13_off\').click(function () {
						        $(\'#hidit\').hide(\'slow\');
						        $(\'#hidit2\').show(\'slow\');
						        $(\'#hidit3\').show(\'slow\');
						        $(\'#hidit4\').show(\'slow\');
						        $(\'#hidit5\').show(\'slow\');
						    });
						    $(\'#nbrp_sub13_on\').click(function () {
						        $(\'#hidit2\').hide(\'slow\');
						        $(\'#hidit3\').hide(\'slow\');
						        $(\'#hidit4\').hide(\'slow\');
						        $(\'#hidit5\').hide(\'slow\');
						        $(\'#hidit\').show(\'slow\');
						    });
						});
					</script>';
		return $output;
	}

  	private function _displayDon()
	{
	$output  = '
		<div>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<h2>'.$this->l('Donnation').'</h2>
					<p>'.$this->l('If you like and use the module, Buy me a beer!').'</p>
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="5K4EKEZCH86DC">
					<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
					<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
					
				</form>
			<hr />	
		</div>';			
		return $output;			
	}

	private function _displayCredits()
	{
		$output = '<div>
						<h2>'.$this->l('Credits').'</h2>
							<p>Homecategoriesplus '.$this->l('by').' <strong>Ckarone</strong></p>
							<p>
							<a href="http://www.prestashop.com/forums/topic/208581-module-gratuit-homecategoriesplus-affichez-vos-categories-et-sous-categories-sur-la-home/">
									'.$this->l('FR post.').'</a>
								<a href="http://www.prestashop.com/forums/topic/210495-free-module-homecategoryplus-display-your-categories-and-subcategories-on-the-home/">
									'.$this->l('UK post.').'</a>
							</p>

					</div>';
		return $output;
	}

	public function hookHome($params)
    {
        global $smarty;
        $results=$this->getInfos((int)$this->context->shop->id);
		$level = 2;
		$ordre_cat = "ORDER BY cs.`position` ASC";
		$ordre_join = "LEFT JOIN "._DB_PREFIX_."category_shop cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = ".(int)$this->context->shop->id.")";
		
		$id_customer = (int)$this->context->customer->id;
		$id_group = $id_customer ? Customer::getDefaultGroupId($id_customer) : _PS_DEFAULT_CUSTOMER_GROUP_;
		$id_lang = (int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl');
		//Set the date
	  	$now = date('Y-m-d H:i:s');
		//Looking for hidden categories (can be set in the BO)
		if($results['sub21'] && $results['sub21']!='' && $results['sub21']!=0){
			$hiddencat = explode(',' ,$results['sub21']);
			$filtrecat='';
			foreach($hiddencat AS $val => $valeur)
	  		{
	  		$filtrecat.= "AND c.`id_category` !=".$valeur." ";
			}
		}else{
		$filtrecat='';
		}
		
 		$mycategories = array();
		//Looking for the top categories with a limit (can be set in the BO)
		$result_1 = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."category c
		INNER JOIN `"._DB_PREFIX_."category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = ".(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').")
		INNER JOIN `"._DB_PREFIX_."category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = ".(int)$this->context->shop->id.")
		WHERE (c.`active` = 1 OR c.`id_category` = ".(int)Configuration::get('PS_HOME_CATEGORY').")
		AND c.level_depth = ".$level."
		AND c.id_category IN (
				SELECT id_category
				FROM `"._DB_PREFIX_."category_group`
				WHERE `id_group` IN (".pSQL(implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id))).")
			)
		".$filtrecat."
		".$ordre_cat."
		LIMIT ".(int)$results['nbr']."");
		foreach ($result_1 as $cat)
		{
			$caTg = array();
			$caTg['id'] = $cat['id_category'];
			$caTg['titre'] = $cat['name'];
			$caTg['link_rewrite'] = $cat['link_rewrite'];
			
			//Looking for the latest product category
			if($results['product_odr'] == 1)
			$order_prod ='p.date_add DESC';
			else if ($results['product_odr'] == 2)
			$order_prod ='cp.position ASC';
			else if ($results['product_odr'] == 3)
			$order_prod ='RAND()';
			else if ($results['product_odr'] == 4)
			$order_prod ='p.date_upd DESC';
			$result_3 = Db::getInstance()->ExecuteS("SELECT p.*, product_shop.*, p.id_product, pi.id_image, pl.name, pl.link_rewrite,pl.id_product, pl.id_lang, pl.description_short, (p.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice 
			FROM "._DB_PREFIX_."category_product cp
			LEFT JOIN "._DB_PREFIX_."product p ON (p.`id_product` = cp.`id_product`)
			".Shop::addSqlAssociation('product', 'p')." 
			LEFT JOIN `"._DB_PREFIX_."product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ".(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').")
			LEFT JOIN "._DB_PREFIX_."category c ON (c.`id_category` = cp.`id_category`)
			LEFT JOIN "._DB_PREFIX_."image pi ON (p.`id_product` =  pi.`id_product` AND pi.`cover` = 1)
			LEFT JOIN "._DB_PREFIX_."tax t ON t.`id_tax` = p.`id_tax_rules_group`
			LEFT JOIN "._DB_PREFIX_."tax_lang tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '".(int)$id_lang.Shop::addSqlRestrictionOnLang('pl')."')
			WHERE cp.`id_category` = ".$cat['id_category']." OR c.nleft >= ".$cat['nleft']." AND c.nright <= ".$cat['nright']."
			AND product_shop.`id_shop` = ".(int)$this->context->shop->id."
					AND cp.`id_category` = ".$cat['id_category']."
					AND product_shop.`visibility` IN ('both', 'catalog')
			ORDER BY ".$order_prod."
			LIMIT 0,".$results['sub10']."");
			$myproduct = array();
				foreach ($result_3 as $pro)
				{
					$product = array();
					$product['id'] = $pro['id_product'];
					$product['titre'] = $pro['name'];
					$product['link_rewrite'] = $pro['link_rewrite'];
					$product['id_image'] = $pro['id_product'].'-'.$pro['id_image'];
					$product['price'] = $pro['orderprice'];
					$product['quantity'] = $pro['quantity'];
					$product['on_sale'] = $pro['on_sale'];
					$product['show_price'] = $pro['show_price'];
					$product['online_only'] = $pro['online_only'];
					$product['available_for_order'] = $pro['available_for_order'];
					$product['price_tax_exc'] = Product::getPriceStatic($product['id'],false);
					$product['price'] = Product::getPriceStatic($product['id'],true);
					$product['desc'] = $pro['description_short'];

						//Looking for price reduction
						if ( (isset($pro['id_product'])) && ($pro['id_product'] <> '')){
						$product['new_price'] = '';
						$result_4 = Db::getInstance()->ExecuteS('SELECT * 
						FROM '._DB_PREFIX_.'specific_price
						WHERE id_product = '.$pro['id_product'].' 
						AND (`from` = \'0000-00-00 00:00:00\' OR (\''.$now.'\' >= `from` AND \''.$now.'\' <= `to`)) 
						LIMIT 1');
							//If a price reduction exist
							if($result_4 <> 0){
							$product['new_price'] = '';
								foreach ($result_4 as $rows){
								$product['reduction_price'] = $rows['reduction'];
								$product['reduction_type'] = $rows['reduction_type'];
									
									//Product price with reduction
									if($product['reduction_type'] == 'percentage'){
										$product['new_price'] = round($product['price']-($product['price']*$product['reduction_price']), 2);	
										$product['reduction_price'] =  $product['reduction_price']*100;
									}else{
										$product['new_price'] = round($product['price']-$product['reduction_price'],0);
										$product['new_price'] = Product::getPriceStatic($pro['id_product'],true);
									}	
								}
							}
						}
					    
					array_push($myproduct,$product);
					 $caTg['prod'] = $myproduct;
				}

						$result_2 = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."category c
						INNER JOIN `"._DB_PREFIX_."category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = ".(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').")
						INNER JOIN `"._DB_PREFIX_."category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = ".(int)$this->context->shop->id.")
						WHERE c.`id_parent` = '".$cat['id_category']."' 
						AND c.id_category IN (
								SELECT id_category
								FROM `"._DB_PREFIX_."category_group`
								WHERE `id_group` IN (".pSQL(implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id))).")
							)
						AND c.`active` = 1 ".$filtrecat."
						".$ordre_cat."
						LIMIT ".(int)$results['nbr_sub']."");

						$mysubcategories = array();
						foreach ($result_2 as $for)
						{
							$caTs = array();
							$caTs['id'] = $for['id_category'];
							$caTs['titre'] = substr(preg_replace('/^[0-9]+\./', '', $for['name']),0,(int)$results['sub3']); 
							$caTs['link_rewrite'] = $for['link_rewrite'];
							array_push($mysubcategories,$caTs);
							$caTg['subs'] = $mysubcategories;
						}
			array_push($mycategories,$caTg);
		}

		if (!$this->isCached('homecategoriesplus.tpl', $this->getCacheId()))
		{		
			 $this->context->smarty->assign(array(
			 'allow_buy_when_out_of_stock' => Configuration::get('PS_ORDER_OUT_OF_STOCK', false),
			 'categories' => $mycategories,
			 'prod_marg' => $results['nbr_marg_p'],
			 'displayprod' => (int)$results['product'],
			 'displaycat' => (int)$results['cat'],
			 'displaybtn' => (int)$results['product_btn'],
			 'displayprice' => (int)$results['product_price'],
			 'displaycatimg' => (int)$results['cat_img'],
			 'displaysubcatimg' => (int)$results['subcat_img'],
			 'displaysub7' => (int)$results['sub7'],
			 'jquery' => (int)$results['sub13'],
			 'version' => (int)$results['sub14'],
			 'nbrp_sub11' => (int)$results['sub11'],
			 'nbrp_sub12' => (int)$results['sub12'],
			 'nbrp_sub15' => (int)$results['sub15'],
			 'nbrp_sub16' => $results['sub16'],
			 'nbrp_sub24' => (int)$results['sub24'],
			 'nbrp_sub25' => (int)$results['sub25']
			 ));
		}

		return $this->display(__FILE__, 'homecategoriesplus.tpl', $this->getCacheId());

	}
	//Add the CSS to the header
		public function hookHeader($params)
	{
  		global $smarty;

		$results=$this->getInfos((int)$this->context->shop->id);
		if($results['sub5'] ==1)
			$displaysub5 = 'underline';
		else 
			$displaysub5 = 'none';

		$this->context->smarty->assign(array(
			'divsize' => $results['nbr_size'],
			'divsizeh' => $results['nbr_size_h'],
			'divcolor' => $results['nbr_size_l'],
			'displaycatbor' => $results['blockb'],
			'displaycatborc' => $results['blockbc'],
			'displayprodbor' => $results['product_b'],
			'displayprodborc' => $results['product_bc'],
			'displaysub1' => $results['sub1'],
			'displaysub2' => $results['sub2'],
			'displaysub4' => $results['sub4'],
			'displaysub5' => $displaysub5,
			'displaysub6' => $results['sub6'],
			'displaysub8' => $results['sub8'],
			'displaysub9' => $results['sub9'],
			'displaysub11' => $results['sub11'],
			'displaysub12' => $results['sub12'],
			'displaysub17' => $results['sub17'],
			'displaysub18' => $results['sub18'],
			'displaysub19' => $results['sub19'],
			'displaysub20' => $results['sub20'],
			'displaysub22' => $results['sub22'],
			'displaysub23' => $results['sub23'],
			'modulewidth' => $results['nbr_mw']
		));
			if($results['sub13'] == 1){
				$this->context->controller->addCSS(($this->_path).'css/swiper.min.css');
				$this->context->controller->addJS(($this->_path).'js/swiper.min.js');
			}
			return $this->display(__FILE__, 'css/catpluscss.tpl');
	}
}
?>