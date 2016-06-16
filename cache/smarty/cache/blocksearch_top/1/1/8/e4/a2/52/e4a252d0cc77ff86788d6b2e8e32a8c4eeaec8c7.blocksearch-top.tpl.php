<?php /*%%SmartyHeaderCode:294745039574b7a24c3e430-81565197%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e4a252d0cc77ff86788d6b2e8e32a8c4eeaec8c7' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blocksearch/blocksearch-top.tpl',
      1 => 1464093794,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '294745039574b7a24c3e430-81565197',
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_574b7f07bc6e94_28334763',
  'has_nocache_code' => false,
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_574b7f07bc6e94_28334763')) {function content_574b7f07bc6e94_28334763($_smarty_tpl) {?><!-- Block search module TOP -->
<div id="search_block_top" class="clearfix">
	<form id="searchbox" method="get" action="http://test.calendrier-associatif.com/prestashop/recherche" >
		<input type="hidden" name="controller" value="search" />
		<input type="hidden" name="orderby" value="position" />
		<input type="hidden" name="orderway" value="desc" />
		<input class="search_query form-control" type="text" id="search_query_top" name="search_query" placeholder="Rechercher" value="" />
		<button type="submit" name="submit_search" class="btn btn-default button-search">
			<span>Rechercher</span>
		</button>
	</form>
</div>
<!-- /Block search module TOP --><?php }} ?>
