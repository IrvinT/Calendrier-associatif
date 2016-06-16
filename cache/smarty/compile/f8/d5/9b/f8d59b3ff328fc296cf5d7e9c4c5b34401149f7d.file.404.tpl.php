<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:28:56
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/404.tpl" */ ?>
<?php /*%%SmartyHeaderCode:624640399576263481dfd89-46261424%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f8d59b3ff328fc296cf5d7e9c4c5b34401149f7d' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/404.tpl',
      1 => 1464093797,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '624640399576263481dfd89-46261424',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'img_dir' => 0,
    'link' => 0,
    'base_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57626348209620_76571668',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57626348209620_76571668')) {function content_57626348209620_76571668($_smarty_tpl) {?><div class="pagenotfound">
	<div class="img-404">
    	<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
/img-404.jpg" alt="<?php echo smartyTranslate(array('s'=>'Page not found'),$_smarty_tpl);?>
" />
    </div>
	<h1><?php echo smartyTranslate(array('s'=>'This page is not available'),$_smarty_tpl);?>
</h1>
	<p>
		<?php echo smartyTranslate(array('s'=>'We\'re sorry, but the Web address you\'ve entered is no longer available.'),$_smarty_tpl);?>

	</p>
	<h3><?php echo smartyTranslate(array('s'=>'To find a product, please type its name in the field below.'),$_smarty_tpl);?>
</h3>
	
    <form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('search'), ENT_QUOTES, 'UTF-8', true);?>
" method="post" class="std">
		<fieldset>
			<div>
				<label for="search_query"><?php echo smartyTranslate(array('s'=>'Search our product catalog:'),$_smarty_tpl);?>
</label>
				<input id="search_query" name="search_query" type="text" class="form-control grey" />
                <button type="submit" name="Submit" value="OK" class="btn btn-default btn-sm"><span><?php echo smartyTranslate(array('s'=>'Ok'),$_smarty_tpl);?>
</span></button>
			</div>
		</fieldset>
	</form>
	<div class="buttons">
    	<a class="btn btn-default btn-md" href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
" title="<?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
">
    		<span>
        		<i class="fa fa-chevron-left left"></i>
        		<?php echo smartyTranslate(array('s'=>'Home page'),$_smarty_tpl);?>

     		</span>
    	</a>
	</div>
</div>
<?php }} ?>
