<?php /*%%SmartyHeaderCode:610285165574befa946d976-13424021%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aff061e1fdececefc1c18a9328746f468957c74b' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blockcategories/blockcategories.tpl',
      1 => 1464093795,
      2 => 'file',
    ),
    '55be02f2ff7383b901759594a211016a33898037' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blockcategories/category-tree-branch.tpl',
      1 => 1464093795,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '610285165574befa946d976-13424021',
  'variables' => 
  array (
    'blockCategTree' => 0,
    'currentCategory' => 0,
    'isDhtml' => 0,
    'child' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_574befa95a7125_10015450',
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_574befa95a7125_10015450')) {function content_574befa95a7125_10015450($_smarty_tpl) {?><!-- Block categories module -->
<section id="categories_block_left" class="block">
	<h4 class="title_block">
					Catégories
			</h4>
	<div class="block_content">
		<ul class="tree dhtml">
												<li >
	<a 	href="http://test.calendrier-associatif.com/prestashop/12-formats-speciaux" title="Soyez originale grace à votre formats spécifique d&#039;impression">
		Formats Spéciaux
	</a>
	</li>

																<li >
	<a 	href="http://test.calendrier-associatif.com/prestashop/13-formats-a4" title="">
		Formats A4
	</a>
	</li>

																<li >
	<a 	href="http://test.calendrier-associatif.com/prestashop/14-formats-a3" title="">
		Formats A3
	</a>
	</li>

									</ul>
	</div>
</section>
<!-- /Block categories module -->
<?php }} ?>
