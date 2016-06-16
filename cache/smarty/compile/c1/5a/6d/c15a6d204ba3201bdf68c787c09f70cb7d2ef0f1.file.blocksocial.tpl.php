<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:28:56
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blocksocial/blocksocial.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1913480582576263480cedc3-39524664%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c15a6d204ba3201bdf68c787c09f70cb7d2ef0f1' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/themes/theme1098/modules/blocksocial/blocksocial.tpl',
      1 => 1465313069,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1913480582576263480cedc3-39524664',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'facebook_url' => 0,
    'twitter_url' => 0,
    'rss_url' => 0,
    'youtube_url' => 0,
    'google_plus_url' => 0,
    'pinterest_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5762634813fec0_93797611',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5762634813fec0_93797611')) {function content_5762634813fec0_93797611($_smarty_tpl) {?><section id="social_block" class="footer-block">
	<h4><?php echo smartyTranslate(array('s'=>'Follow us','mod'=>'blocksocial'),$_smarty_tpl);?>
</h4>
	<ul class="toggle-footer">
		<?php if ($_smarty_tpl->tpl_vars['facebook_url']->value!='') {?>
			<li class="facebook">
				<a target="_blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['facebook_url']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Facebook','mod'=>'blocksocial'),$_smarty_tpl);?>
">
					
					
					

					<?php echo '<?xml';?> version="1.0" encoding="utf-8"<?php echo '?>';?>

					<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
					<svg class="col-xs-12" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">
						<path fill="#FFFFFF" d="M398.14,50.5H117.98c-36.408,0-68.48,26.452-68.48,62.86v280.16c0,36.408,32.072,68.98,68.48,68.98h173.466
							c-0.325-54,0.077-114.134-0.185-166.387c-11.064-0.112-22.138-0.684-33.202-0.854c0.041-18.467,0.017-37.317,0.024-55.781
							c11.057-0.137,22.121-0.163,33.178-0.268c0.338-17.957-0.338-36.025,0.354-53.966c1.103-14.205,6.519-28.563,17.14-38.377
							c12.859-12.239,31.142-16.397,48.387-16.912c18.233-0.163,36.468-0.076,54.71-0.068c0.072,19.24,0.072,38.482-0.008,57.722
							c-11.789-0.02-23.585,0.023-35.374-0.025c-7.468-0.467-15.145,5.198-16.504,12.609c-0.177,12.875-0.064,25.757-0.057,38.628
							c17.285,0.073,34.577-0.02,51.862,0.044c-1.264,18.629-3.581,37.168-6.285,55.637c-15.272,0.137-30.554,1.514-45.818,1.602
							c-0.129,52.236,0.04,112.395-0.093,166.395h38.564c36.408,0,63.36-32.572,63.36-68.98V113.36C461.5,76.952,434.548,50.5,398.14,50.5
							z"/>
					</svg>

				</a>
			</li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['twitter_url']->value!='') {?>
			<li class="twitter">
				<a target="_blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['twitter_url']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Twitter','mod'=>'blocksocial'),$_smarty_tpl);?>
">
					<span><?php echo smartyTranslate(array('s'=>'Twitter','mod'=>'blocksocial'),$_smarty_tpl);?>
</span>
				</a>
			</li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['rss_url']->value!='') {?>
			<li class="rss">
				<a target="_blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['rss_url']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'RSS','mod'=>'blocksocial'),$_smarty_tpl);?>
">
					<span><?php echo smartyTranslate(array('s'=>'RSS','mod'=>'blocksocial'),$_smarty_tpl);?>
</span>
				</a>
			</li>
		<?php }?>
        <?php if ($_smarty_tpl->tpl_vars['youtube_url']->value!='') {?>
        	<li class="youtube">
        		<a target="_blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['youtube_url']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Youtube','mod'=>'blocksocial'),$_smarty_tpl);?>
">
        			<span><?php echo smartyTranslate(array('s'=>'Youtube','mod'=>'blocksocial'),$_smarty_tpl);?>
</span>
        		</a>
        	</li>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['google_plus_url']->value!='') {?>
        	<li class="google-plus">
        		<a target="_blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['google_plus_url']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Google Plus','mod'=>'blocksocial'),$_smarty_tpl);?>
">
        			<span><?php echo smartyTranslate(array('s'=>'Google Plus','mod'=>'blocksocial'),$_smarty_tpl);?>
</span>
        		</a>
        	</li>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['pinterest_url']->value!='') {?>
        	<li class="pinterest">
        		<a target="_blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['pinterest_url']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Pinterest','mod'=>'blocksocial'),$_smarty_tpl);?>
">
        			<span><?php echo smartyTranslate(array('s'=>'Pinterest','mod'=>'blocksocial'),$_smarty_tpl);?>
</span>
        		</a>
        	</li>
        <?php }?>
	</ul>
</section><?php }} ?>
