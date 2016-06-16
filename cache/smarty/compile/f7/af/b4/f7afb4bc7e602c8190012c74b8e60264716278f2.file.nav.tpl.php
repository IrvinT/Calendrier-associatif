<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:28:56
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blockcontact/nav.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1398277321576263484a4bf0-05583347%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f7afb4bc7e602c8190012c74b8e60264716278f2' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blockcontact/nav.tpl',
      1 => 1464093795,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1398277321576263484a4bf0-05583347',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'telnumber' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_576263484c9f36_48677934',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_576263484c9f36_48677934')) {function content_576263484c9f36_48677934($_smarty_tpl) {?><div id="contact-link">
	<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Contact Us','mod'=>'blockcontact'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcontact'),$_smarty_tpl);?>
</a>
</div>
<?php if ($_smarty_tpl->tpl_vars['telnumber']->value) {?>
	<span class="shop-phone">
		<i class="fa fa-phone"></i>
        <?php echo smartyTranslate(array('s'=>'Call us now:','mod'=>'blockcontact'),$_smarty_tpl);?>
 
        <strong><?php echo $_smarty_tpl->tpl_vars['telnumber']->value;?>
</strong>
	</span>
<?php }?><?php }} ?>
