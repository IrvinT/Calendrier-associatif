<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:28:55
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blocktopmenu/blocktopmenu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:160003809157626347a392d1-81156541%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1f0ef31bfd67f7dba867d855f5ab6b20a9786a66' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blocktopmenu/blocktopmenu.tpl',
      1 => 1465377206,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '160003809157626347a392d1-81156541',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MENU' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57626347a4c096_27288882',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57626347a4c096_27288882')) {function content_57626347a4c096_27288882($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['MENU']->value!='') {?>
	<!-- Menu -->
	<div id="block_top_menu" class="sf-contener clearfix col-lg-12">
		<div class="cat-title"><?php echo smartyTranslate(array('s'=>"Categories",'mod'=>"blocktopmenu"),$_smarty_tpl);?>
</div>
        <ul class="sf-menu clearfix menu-content">
            <?php echo $_smarty_tpl->tpl_vars['MENU']->value;?>

           
        </ul>
	</div>
	<!--/ Menu -->
<?php }?><?php }} ?>
