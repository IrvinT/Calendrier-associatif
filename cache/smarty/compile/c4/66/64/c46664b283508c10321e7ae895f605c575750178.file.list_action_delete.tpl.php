<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:19:36
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/admin894imyet9/themes/default/template/helpers/list/list_action_delete.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8413055757626118b371c9-11145373%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c46664b283508c10321e7ae895f605c575750178' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/admin894imyet9/themes/default/template/helpers/list/list_action_delete.tpl',
      1 => 1464082971,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8413055757626118b371c9-11145373',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'confirm' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57626118b5cf34_84168716',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57626118b5cf34_84168716')) {function content_57626118b5cf34_84168716($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
"<?php if (isset($_smarty_tpl->tpl_vars['confirm']->value)) {?> onclick="if (confirm('<?php echo $_smarty_tpl->tpl_vars['confirm']->value;?>
')){return true;}else{event.stopPropagation(); event.preventDefault();};"<?php }?> title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="delete">
	<i class="icon-trash"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
