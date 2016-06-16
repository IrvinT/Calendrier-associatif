<?php /* Smarty version Smarty-3.1.19, created on 2016-06-16 10:28:55
         compiled from "/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/modules/homecategoriesplus/css/catpluscss.tpl" */ ?>
<?php /*%%SmartyHeaderCode:149147221576263472a14e6-47218648%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '72fe91ee5101b5506986b7aff3a45281dcc9c1ae' => 
    array (
      0 => '/srv/data/web/vhosts/test.calendrier-associatif.com/htdocs/prestashop/modules/homecategoriesplus/css/catpluscss.tpl',
      1 => 1465302707,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '149147221576263472a14e6-47218648',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'displaysub9' => 0,
    'displaysub22' => 0,
    'displaysub1' => 0,
    'displaysub4' => 0,
    'displaysub5' => 0,
    'displaysub2' => 0,
    'displaysub6' => 0,
    'divcolor' => 0,
    'displaycatbor' => 0,
    'displaycatborc' => 0,
    'displaysub19' => 0,
    'displaysub20' => 0,
    'displayprodbor' => 0,
    'displayprodborc' => 0,
    'displaysub8' => 0,
    'displaysub17' => 0,
    'displaysub18' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57626347315de2_56204450',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57626347315de2_56204450')) {function content_57626347315de2_56204450($_smarty_tpl) {?><style type="text/css">
@charset "utf-8";
/* CSS Document */

#CCategoriePlus{padding:0}
#CCategoriePlus .CCatPlus{padding:1px}
#CCategoriePlus .CCatPlus ul{padding:0;margin:0px}
#CCategoriePlus .CCatPlus li, .swiper-slide{border:solid <?php echo $_smarty_tpl->tpl_vars['displaysub9']->value;?>
px #f1f2f3;<?php if ($_smarty_tpl->tpl_vars['displaysub9']->value==0) {?>margin-bottom:1px;<?php }?>background:#<?php echo $_smarty_tpl->tpl_vars['displaysub22']->value;?>
;padding:0;margin-bottom:1px}
#CCategoriePlus .CCatPlus li a, .swiper-slide a{
	text-align:center;
	display:block;
	color:#<?php echo $_smarty_tpl->tpl_vars['displaysub1']->value;?>
;
	font-size:<?php echo $_smarty_tpl->tpl_vars['displaysub4']->value;?>
px;/*Sub categories text size*/
	text-decoration:<?php echo $_smarty_tpl->tpl_vars['displaysub5']->value;?>

}
#CCategoriePlus .CCatPlus li a:hover, .swiper-slide a:hover{background-color:#<?php echo $_smarty_tpl->tpl_vars['displaysub2']->value;?>
;color:#<?php echo $_smarty_tpl->tpl_vars['displaysub6']->value;?>
;}
@media(max-width:990px){
#CCategoriePlus .CCatPlus ul li{padding:5px 8px;background:#f1f2f3;margin-bottom:1px}
#CCategoriePlus .ctitle{text-align:center}
}
#CCategoriePlus.ctitle{display:block;background:#<?php echo $_smarty_tpl->tpl_vars['divcolor']->value;?>
;border:solid <?php echo $_smarty_tpl->tpl_vars['displaycatbor']->value;?>
px #<?php echo $_smarty_tpl->tpl_vars['displaycatborc']->value;?>
;border-top:0;margin: 0 0 1px 0}
#CCategoriePlus .ctitle a{color:#<?php echo $_smarty_tpl->tpl_vars['displaysub19']->value;?>
}
#CCategoriePlus .ctitle a:hover{color:#<?php echo $_smarty_tpl->tpl_vars['displaysub20']->value;?>
}
#CCategoriePlus .Cprod {text-align:center;display:block;border:solid <?php echo $_smarty_tpl->tpl_vars['displayprodbor']->value;?>
px #<?php echo $_smarty_tpl->tpl_vars['displayprodborc']->value;?>
;}
#CCategoriePlus .Cprod a.button span{text-align:center;font-size:0.8em;padding:1px 3px}
#CCategoriePlus .CcategoryImage{padding:0;margin:0 auto;display:block;background:#f1f2f3;;border:solid <?php echo $_smarty_tpl->tpl_vars['displaycatbor']->value;?>
px #<?php echo $_smarty_tpl->tpl_vars['displaycatborc']->value;?>
;border-bottom:0}
#CCategoriePlus .CCatPlus li .CcategoryImage{border:none}
#CCategoriePlus .CCatPlus a.Ctocat{color:#<?php echo $_smarty_tpl->tpl_vars['displaysub8']->value;?>
;}
#CCategoriePlus small{margin-top:3px}

.swiper-container {
    width: 100%;
    height: 100%}
.swiper-slide {
    text-align: center;
    font-size: 18px;
    background: #fff;
    /* Center slide text vertically */
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    -webkit-justify-content: center;
    justify-content: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center;
    align-items: center}

.swiper-pagination-bullet-active{background:#<?php echo $_smarty_tpl->tpl_vars['displaysub17']->value;?>
 !important}
.swiper-slide a{color:#<?php echo $_smarty_tpl->tpl_vars['displaysub18']->value;?>
}

  /* Hack pour le bug du float sur les listes avec des div plus courtes */
@media (min-width:767px){

  /* Column clear fix */
 #CCategoriePlus .col-lg-1:nth-child(12n+1),
 #CCategoriePlus .col-lg-2:nth-child(6n+1),
 #CCategoriePlus .col-lg-3:nth-child(4n+1),
 #CCategoriePlus .col-lg-4:nth-child(3n+1),
 #CCategoriePlus .col-lg-6:nth-child(2n+1),
 #CCategoriePlus .col-md-1:nth-child(12n+1),
 #CCategoriePlus .col-md-2:nth-child(6n+1),
 #CCategoriePlus .col-md-3:nth-child(4n+1),
 #CCategoriePlus .col-md-4:nth-child(3n+1),
 #CCategoriePlus .col-md-6:nth-child(2n+1){
    clear: none;
  }
 #CCategoriePlus .col-sm-1:nth-child(12n+1),
 #CCategoriePlus .col-sm-2:nth-child(6n+1),
 #CCategoriePlus .col-sm-3:nth-child(4n+1),
 #CCategoriePlus .col-sm-4:nth-child(3n+1),
 #CCategoriePlus .col-sm-6:nth-child(2n+1){
    clear: left;
  }
}
/*  Medium Desktop  */
@media (min-width:992px){

  /* Column clear fix */
 #CCategoriePlus .col-lg-1:nth-child(12n+1),
 #CCategoriePlus .col-lg-2:nth-child(6n+1),
 #CCategoriePlus .col-lg-3:nth-child(4n+1),
 #CCategoriePlus .col-lg-4:nth-child(3n+1),
 #CCategoriePlus .col-lg-6:nth-child(2n+1),
 #CCategoriePlus .col-sm-1:nth-child(12n+1),
 #CCategoriePlus .col-sm-2:nth-child(6n+1),
 #CCategoriePlus .col-sm-3:nth-child(4n+1),
 #CCategoriePlus .col-sm-4:nth-child(3n+1),
 #CCategoriePlus .col-sm-6:nth-child(2n+1),
 #CCategoriePlus .col-xs-1:nth-child(12n+1),
 #CCategoriePlus .col-xs-2:nth-child(6n+1),
 #CCategoriePlus .col-xs-3:nth-child(4n+1),
 #CCategoriePlus .col-xs-4:nth-child(3n+1),
 #CCategoriePlus .col-xs-6:nth-child(2n+1){
    clear: none;
  }
 #CCategoriePlus .col-md-1:nth-child(12n+1),
 #CCategoriePlus .col-md-2:nth-child(6n+1),
 #CCategoriePlus .col-md-3:nth-child(4n+1),
 #CCategoriePlus .col-md-4:nth-child(3n+1),
 #CCategoriePlus .col-md-6:nth-child(2n+1),
 #CCategoriePlus .col-xs-1:nth-child(12n+1),
 #CCategoriePlus .col-xs-2:nth-child(6n+1),
 #CCategoriePlus .col-xs-3:nth-child(4n+1),
 #CCategoriePlus .col-xs-4:nth-child(3n+1),
 #CCategoriePlus .col-xs-6:nth-child(2n+1){
    clear: left;
  }
}
/*  Large Desktop  */
@media (min-width:1200px){

  /* Column clear fix */
 #CCategoriePlus .col-md-1:nth-child(12n+1),
 #CCategoriePlus .col-md-2:nth-child(6n+1),
 #CCategoriePlus .col-md-3:nth-child(4n+1),
 #CCategoriePlus .col-md-4:nth-child(3n+1),
 #CCategoriePlus .col-md-6:nth-child(2n+1),
 #CCategoriePlus .col-sm-1:nth-child(12n+1),
 #CCategoriePlus .col-sm-2:nth-child(6n+1),
 #CCategoriePlus .col-sm-3:nth-child(4n+1),
 #CCategoriePlus .col-sm-4:nth-child(3n+1),
 #CCategoriePlus .col-sm-6:nth-child(2n+1)
 #CCategoriePlus .col-xs-1:nth-child(12n+1),
 #CCategoriePlus .col-xs-2:nth-child(6n+1),
 #CCategoriePlus .col-xs-3:nth-child(4n+1),
 #CCategoriePlus .col-xs-4:nth-child(3n+1),
 #CCategoriePlus .col-xs-6:nth-child(2n+1){{
    clear: none;
  }
 #CCategoriePlus .col-lg-1:nth-child(12n+1),
 #CCategoriePlus .col-lg-2:nth-child(6n+1),
 #CCategoriePlus .col-lg-3:nth-child(4n+1),
 #CCategoriePlus .col-lg-4:nth-child(3n+1),
 #CCategoriePlus .col-lg-6:nth-child(2n+1){
    clear: left;
  }
}
</style>
<?php }} ?>
