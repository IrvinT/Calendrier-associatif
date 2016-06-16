<style type="text/css">
@charset "utf-8";
/* CSS Document */
{literal}
#CCategoriePlus{padding:0}
#CCategoriePlus .CCatPlus{padding:1px}
#CCategoriePlus .CCatPlus ul{padding:0;margin:0px}
#CCategoriePlus .CCatPlus li, .swiper-slide{border:solid {/literal}{$displaysub9}{literal}px #f1f2f3;{/literal}{if $displaysub9 == 0}margin-bottom:1px;{/if}{literal}background:#{/literal}{$displaysub22}{literal};padding:0;margin-bottom:1px}
#CCategoriePlus .CCatPlus li a, .swiper-slide a{
	text-align:center;
	display:block;
	color:#{/literal}{$displaysub1}{literal};
	font-size:{/literal}{$displaysub4}{literal}px;/*Sub categories text size*/
	text-decoration:{/literal}{$displaysub5}{literal}
}
#CCategoriePlus .CCatPlus li a:hover, .swiper-slide a:hover{background-color:#{/literal}{$displaysub2}{literal};color:#{/literal}{$displaysub6}{literal};}
@media(max-width:990px){
#CCategoriePlus .CCatPlus ul li{padding:5px 8px;background:#f1f2f3;margin-bottom:1px}
#CCategoriePlus .ctitle{text-align:center}
}
#CCategoriePlus.ctitle{display:block;background:#{/literal}{$divcolor}{literal};border:solid {/literal}{$displaycatbor}{literal}px #{/literal}{$displaycatborc}{literal};border-top:0;margin: 0 0 1px 0}
#CCategoriePlus .ctitle a{color:#{/literal}{$displaysub19}{literal}}
#CCategoriePlus .ctitle a:hover{color:#{/literal}{$displaysub20}{literal}}
#CCategoriePlus .Cprod {text-align:center;display:block;border:solid {/literal}{$displayprodbor}{literal}px #{/literal}{$displayprodborc}{literal};}
#CCategoriePlus .Cprod a.button span{text-align:center;font-size:0.8em;padding:1px 3px}
#CCategoriePlus .CcategoryImage{padding:0;margin:0 auto;display:block;background:#f1f2f3;;border:solid {/literal}{$displaycatbor}{literal}px #{/literal}{$displaycatborc}{literal};border-bottom:0}
#CCategoriePlus .CCatPlus li .CcategoryImage{border:none}
#CCategoriePlus .CCatPlus a.Ctocat{color:#{/literal}{$displaysub8}{literal};}
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

.swiper-pagination-bullet-active{background:#{/literal}{$displaysub17} !important{literal}}
.swiper-slide a{color:#{/literal}{$displaysub18}{literal}}

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
{/literal}