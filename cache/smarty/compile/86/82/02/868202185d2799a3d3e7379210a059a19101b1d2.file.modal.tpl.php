<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:27:41
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/admin894imyet9/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2050741007576262fdb6e454-41741947%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '868202185d2799a3d3e7379210a059a19101b1d2' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/admin894imyet9/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1464082980,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2050741007576262fdb6e454-41741947',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_576262fdb74381_81353710',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_576262fdb74381_81353710')) {function content_576262fdb74381_81353710($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title"><?php echo smartyTranslate(array('s'=>'Recommended Modules and Services'),$_smarty_tpl);?>
</h3>
			</div>
			<div class="modal-body">
				<div id="modules_list_container_tab_modal" style="display:none;"></div>
				<div id="modules_list_loader"><i class="icon-refresh icon-spin"></i></div>
			</div>
		</div>
	</div>
</div>
<?php }} ?>
