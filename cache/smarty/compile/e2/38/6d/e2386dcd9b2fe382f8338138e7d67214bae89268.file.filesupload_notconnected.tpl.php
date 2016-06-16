<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:28:55
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/modules/filesupload/filesupload_notconnected.tpl" */ ?>
<?php /*%%SmartyHeaderCode:75737442057626347b0c363-25207482%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e2386dcd9b2fe382f8338138e7d67214bae89268' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/modules/filesupload/filesupload_notconnected.tpl',
      1 => 1464100418,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '75737442057626347b0c363-25207482',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'id' => 0,
    'module_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57626347b4b762_46163794',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57626347b4b762_46163794')) {function content_57626347b4b762_46163794($_smarty_tpl) {?><script type="text/javascript">
        
	$(function() {
     
            $("a#inline<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
").fancybox({
                'scrolling'		: 'no',
                'titleShow'		: false,
            });
});
	

</script>
<?php if ($_smarty_tpl->tpl_vars['id']->value==3) {?>
<!-- Block files upload module -->
<div class="block">
	<h4><?php echo smartyTranslate(array('s'=>'Upload your files','mod'=>'filesupload'),$_smarty_tpl);?>
</a></h4>
	<div class="block_content">
			<a class="activator<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" id="inline<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" href="#func<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><img align="center" src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
/img/upload.png" alt="<?php echo smartyTranslate(array('s'=>'Upload your files','mod'=>'filesupload'),$_smarty_tpl);?>
" width="48" height="48" /><?php echo smartyTranslate(array('s'=>'Upload your files','mod'=>'filesupload'),$_smarty_tpl);?>
</a>
                        <div style="display:none">
                         <form id="func<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
">
                            <div id="uploader">
                                    <p><?php echo smartyTranslate(array('s'=>'Please login to upload files','mod'=>'filesupload'),$_smarty_tpl);?>
</p>
                            </div>
                         </form>
                        </div>
        </div>
</div>
<?php } else { ?>
<div id="fuonprod<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><a class="activator<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" id="inline<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" href="#upload_form"><span id="bsub-text<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><?php echo smartyTranslate(array('s'=>'Upload your files','mod'=>'filesupload'),$_smarty_tpl);?>
</span></a></div>
<div style="display:none">
<form id="upload_form">
	<div id="uploader">
		<p><?php echo smartyTranslate(array('s'=>'You must be connected to upload files','mod'=>'filesupload'),$_smarty_tpl);?>
</p>
	</div>
</form>
</div>
<?php }?>
<?php }} ?>
