<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:19:36
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/admin894imyet9/themes/default/template/helpers/list/list_action_preview.tpl" */ ?>
<?php /*%%SmartyHeaderCode:199034207357626118a83b20-97456136%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '48b69e1de69115ba67d1af38277ac4805a0e9309' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/admin894imyet9/themes/default/template/helpers/list/list_action_preview.tpl',
      1 => 1464082973,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '199034207357626118a83b20-97456136',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57626118a99830_78216911',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57626118a99830_78216911')) {function content_57626118a99830_78216911($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank">
	<i class="icon-eye"></i> <?php echo $_smarty_tpl->tpl_vars['action']->value;?>

</a>
<?php }} ?>
