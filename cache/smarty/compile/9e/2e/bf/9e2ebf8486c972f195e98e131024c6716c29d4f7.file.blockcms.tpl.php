<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:28:55
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blockcms/blockcms.tpl" */ ?>
<?php /*%%SmartyHeaderCode:175638395057626347bda586-28094835%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9e2ebf8486c972f195e98e131024c6716c29d4f7' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blockcms/blockcms.tpl',
      1 => 1465458958,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '175638395057626347bda586-28094835',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'block' => 0,
    'cms_titles' => 0,
    'cms_key' => 0,
    'cms_title' => 0,
    'cms_page' => 0,
    'link' => 0,
    'show_price_drop' => 0,
    'PS_CATALOG_MODE' => 0,
    'show_best_sales' => 0,
    'show_contact' => 0,
    'contact_url' => 0,
    'cmslinks' => 0,
    'cmslink' => 0,
    'show_sitemap' => 0,
    'footer_text' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57626347ce7245_72079090',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57626347ce7245_72079090')) {function content_57626347ce7245_72079090($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['block']->value==1) {?>
	<!-- Block CMS module -->
	<?php  $_smarty_tpl->tpl_vars['cms_title'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cms_title']->_loop = false;
 $_smarty_tpl->tpl_vars['cms_key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cms_titles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cms_title']->key => $_smarty_tpl->tpl_vars['cms_title']->value) {
$_smarty_tpl->tpl_vars['cms_title']->_loop = true;
 $_smarty_tpl->tpl_vars['cms_key']->value = $_smarty_tpl->tpl_vars['cms_title']->key;
?>
		<section id="informations_block_left_<?php echo $_smarty_tpl->tpl_vars['cms_key']->value;?>
" class="block informations_block_left">
			<h4 class="title_block">
				<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_title']->value['category_link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php if (!empty($_smarty_tpl->tpl_vars['cms_title']->value['name'])) {?><?php echo $_smarty_tpl->tpl_vars['cms_title']->value['name'];?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['cms_title']->value['category_name'];?>
<?php }?>">
					<?php if (!empty($_smarty_tpl->tpl_vars['cms_title']->value['name'])) {?><?php echo $_smarty_tpl->tpl_vars['cms_title']->value['name'];?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['cms_title']->value['category_name'];?>
<?php }?>
				</a>
			</h4>
			<div class="block_content list-block">
				<ul>
					<?php  $_smarty_tpl->tpl_vars['cms_page'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cms_page']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cms_title']->value['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cms_page']->key => $_smarty_tpl->tpl_vars['cms_page']->value) {
$_smarty_tpl->tpl_vars['cms_page']->_loop = true;
?>
						<?php if (isset($_smarty_tpl->tpl_vars['cms_page']->value['link'])) {?>
							<li class="bullet">
								<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
">
									<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['name'], ENT_QUOTES, 'UTF-8', true);?>

								</a>
							</li>
						<?php }?>
					<?php } ?>
					<?php  $_smarty_tpl->tpl_vars['cms_page'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cms_page']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cms_title']->value['cms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cms_page']->key => $_smarty_tpl->tpl_vars['cms_page']->value) {
$_smarty_tpl->tpl_vars['cms_page']->_loop = true;
?>
						<?php if (isset($_smarty_tpl->tpl_vars['cms_page']->value['link'])) {?>
							<li>
								<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['meta_title'], ENT_QUOTES, 'UTF-8', true);?>
">
									<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['meta_title'], ENT_QUOTES, 'UTF-8', true);?>

								</a>
							</li>
						<?php }?>
					<?php } ?>
					<?php if ($_smarty_tpl->tpl_vars['cms_title']->value['display_store']) {?>
						<li>
							<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('stores'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Our stores','mod'=>'blockcms'),$_smarty_tpl);?>
">
								<?php echo smartyTranslate(array('s'=>'Our stores','mod'=>'blockcms'),$_smarty_tpl);?>

							</a>
						</li>
					<?php }?>
				</ul>
			</div>
		</section>
	<?php } ?>
	<!-- /Block CMS module -->
<?php } else { ?>
	<!-- MODULE Block footer -->
	<section class="footer-block col-xs-12 col-sm-3" id="block_various_links_footer">
		<h4><?php echo smartyTranslate(array('s'=>'Information','mod'=>'blockcms'),$_smarty_tpl);?>
</h4>
		<ul class="toggle-footer">
			<?php if ($_smarty_tpl->tpl_vars['show_price_drop']->value&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value) {?>
				<li class="item">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('prices-drop'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Specials','mod'=>'blockcms'),$_smarty_tpl);?>
">
						<?php echo smartyTranslate(array('s'=>'Specials','mod'=>'blockcms'),$_smarty_tpl);?>

					</a>
				</li>
			<?php }?>
			
			<?php if ($_smarty_tpl->tpl_vars['show_best_sales']->value&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value) {?>
				<li class="item">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('best-sales'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Top sellers','mod'=>'blockcms'),$_smarty_tpl);?>
">
						<?php echo smartyTranslate(array('s'=>'Top sellers','mod'=>'blockcms'),$_smarty_tpl);?>

					</a>
				</li>
			<?php }?>
			
			<?php if ($_smarty_tpl->tpl_vars['show_contact']->value) {?>
			<li class="item">
				<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink($_smarty_tpl->tpl_vars['contact_url']->value,true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcms'),$_smarty_tpl);?>
">
					<?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcms'),$_smarty_tpl);?>

				</a>
			</li>
			<?php }?>
			<?php  $_smarty_tpl->tpl_vars['cmslink'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cmslink']->_loop = false;
 $_smarty_tpl->tpl_vars['clef'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cmslinks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cmslink']->key => $_smarty_tpl->tpl_vars['cmslink']->value) {
$_smarty_tpl->tpl_vars['cmslink']->_loop = true;
 $_smarty_tpl->tpl_vars['clef']->value = $_smarty_tpl->tpl_vars['cmslink']->key;
?>
				<?php if ($_smarty_tpl->tpl_vars['cmslink']->value['meta_title']!=''&&$_smarty_tpl->tpl_vars['cmslink']->value['meta_title']!=="Conditions d'utilisation") {?>
					<li class="item">
						<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cmslink']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cmslink']->value['meta_title'], ENT_QUOTES, 'UTF-8', true);?>
">
							<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cmslink']->value['meta_title'], ENT_QUOTES, 'UTF-8', true);?>

						</a>
					</li>
				<?php }?>
			<?php } ?>
			<?php if ($_smarty_tpl->tpl_vars['show_sitemap']->value) {?>
			<li>
				<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('sitemap'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Sitemap','mod'=>'blockcms'),$_smarty_tpl);?>
">
					<?php echo smartyTranslate(array('s'=>'Sitemap','mod'=>'blockcms'),$_smarty_tpl);?>

				</a>
			</li>
			<?php }?>
		</ul>
		<?php echo $_smarty_tpl->tpl_vars['footer_text']->value;?>

	</section>
	
	<div class="bottom-footer col-xs-12">
		<div>
			<p>Solution e-commerce proposée par calendrier-associatif.com</p>
		</div>
	</div>
	
	<!-- /MODULE Block footer -->
<?php }?>
<?php }} ?>
