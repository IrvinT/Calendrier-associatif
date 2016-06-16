{if $jquery == 0} 
		{if $categories} 
				  <div class="col-xs-12 list-group" id="CCategoriePlus">
				  {foreach from=$categories item=categorie name=cats}
				  {assign var='categoryLink' value=$link->getcategoryLink($categorie.id, $categorie.link_rewrite)}
				       <div class="col-xs-{if $divsizeh==1}12{elseif $divsizeh==2}6{/if} col-sm-{if $divsize==1}12{elseif $divsize==2}6{elseif $divsize==3}4{elseif $divsize==4}3{/if} col-md-{if $modulewidth==1}12{elseif $modulewidth==2}6{elseif $modulewidth==3}4{elseif $modulewidth==4}3{/if} CCatPlus list-group-item-heading" {if $smarty.foreach.cats.last}id="last_item"{/if}>
				          
		                  {if $displaycatimg == 1}<span class="col-xs-12 CcategoryImage"><a href="{$categoryLink}">
		                  <img src="{$img_cat_dir}{$categorie.id}-category.jpg" alt="{$categorie.titre}" title="{$categorie.titre}" class="img-responsive" /></a></span>{/if}
						<h4 class="col-xs-12 ctitle"><a href="{$categoryLink}">{$categorie.titre}</a> {if $displaysub7 == 1}<small class="pull-right"><a href="{$categoryLink}" class="Ctocat">{l s='See all categories' mod='homecategoriesplus'} >></a></small>{/if} </h4>

				                  {if isset($categorie.subs) && $displaycat == 1}
				                      <ul class="list-group col-xs-12 {if $displayprod neq 0}col-sm-12 col-md-5{/if} ">
				                          {foreach from=$categorie.subs item=subcategorie key=i name=foo}
				                          {assign var='scategoryLink' value=$link->getcategoryLink($subcategorie.id, $subcategorie.link_rewrite)}
				                              <li class="list-group-item"><a href="{$scategoryLink}">{if $nbrp_sub24 == 1}{$subcategorie.titre}{/if}
		                                     {if $displaysubcatimg == 1}<img src="{$img_cat_dir}{$subcategorie.id}-medium.jpg" alt="{$subcategorie.titre}" title="{$subcategorie.titre}" class="img-responsive CcategoryImage" />{/if}
		                                     </a></li>
				                          {foreachelse}
				                              
				                          {/foreach}
				                      </ul>
				                  {/if}
		                {if $categorie.prod && $displayprod == 1} 
		                  {foreach from=$categorie.prod item=produc}
				              {assign var='getproductLink' value=$link->getproductLink($produc.id, $produc.link_rewrite)}
				                  <span class="cols-xs-12 {if $displaycat neq 0}col-sm-12 col-md-7{/if}{if $nbrp_sub11 == 0} hidden-xs{/if}{if $nbrp_sub12 == 0} hidden-sm{/if} Cprod">
				                          <a href="{$getproductLink}"><img src="{$link->getImageLink($produc.link_rewrite, $produc.id_image, 'medium')}" alt="{$produc.titre}" width="80" height="80" /></a><br />
				                          
				                          <a href="{$getproductLink}">{$produc.titre}</a><br />
				                     	{if $prod_marg ==1}{$produc.desc|substr:0:$displaysub23}{/if}
				                        {if $displayprice == 1} <span class="price">{l s='Price' mod='homecategoriesplus'}:{convertPrice price=$produc.price} {if $priceDisplay == 2} {l s='+Tx' mod='homecategoriesplus'} HT{l s='-Tx' mod='homecategoriesplus'}{/if}</span><br />{/if}
				                     	{if $displaybtn == 1}
		                                {if $allow_buy_when_out_of_stock OR ($produc.quantity AND $produc.quantity > 0)}
		                                 {if isset($static_token)}
									<a class="button ajax_add_to_cart_button btn btn-default hidden-xs" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$produc.id|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$produc.id|intval}">
										<span>{l s='Add to cart' mod='homecategoriesplus'}</span>
									</a>
								{else}
									<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' }" data-id-product="{$product.id_product|intval}">
										<span>{l s='Add to cart'}</span>
									</a>
								{/if}
		                                 {/if}
		                                {/if}
				                     </span>
				           {/foreach}
		                   {/if} 
		                      
				       </div>

				  {foreachelse}
				  	{l s='Price' mod='Ooooopss, no category'}
				  {/foreach}
				  </div>
				 
		{/if}		  
{else}
	{if $version == 1}
			{if $categories}
			<div class="swiper-container">
				<div class="swiper-wrapper">
			       	{foreach from=$categories item=categorie name=cats}
			          	{assign var='categoryLink' value=$link->getcategoryLink($categorie.id, $categorie.link_rewrite)}
			            	<div class="swiper-slide">
			            		{if $displaycatimg == 1}
			                  <a href="{$categoryLink}">
			                  	{if $nbrp_sub15 == 1}{$categorie.titre}{/if}
			                  	<img src="{$img_cat_dir}{$categorie.id}-category.jpg" alt="{$categorie.titre}" title="{$categorie.titre}" class="img-responsive" /></a>{/if}
							</div>
			        {/foreach}
				</div>
					{if $nbrp_sub16 == 1 OR $version == 3}
					<div class="swiper-pagination"></div>	
					{/if}
			</div>
				<!-- Initialize Swiper -->
			    <script>
			    var swiper = new Swiper('.swiper-container', {
			    	autoplay:{if $nbrp_sub25 == 1} 2500{else} 0,{/if},
			    	paginationClickable: true,
			    	{if $nbrp_sub16 == 1}pagination: '.swiper-pagination',{/if}
			    	mousewheelControl: true
   			    });
			    </script>  
		   {/if}

	{else if $version == 2 }
			{if $categories}
			
				{foreach from=$categories item=categorie name=cats}
			          {assign var='categoryLink' value=$link->getcategoryLink($categorie.id, $categorie.link_rewrite)}
			            
			            <div class="swiper-container swiper{$smarty.foreach.cats.iteration}">
			            	{if $nbrp_sub15 == 1}<a href="{$categoryLink}">{$categorie.titre}</a>{/if}								
								 {if isset($categorie.subs) && $displaycat == 1}
							<div class="swiper-wrapper">
								
									{foreach from=$categorie.subs item=subcategorie key=i name=foo}
									{assign var='scategoryLink' value=$link->getcategoryLink($subcategorie.id, $subcategorie.link_rewrite)}
											<div class="swiper-slide">
													<a href="{$scategoryLink}">{if $nbrp_sub24 == 1}{$subcategorie.titre}{/if}
													<img src="{$img_cat_dir}{$subcategorie.id}-category.jpg" alt="{$subcategorie.titre}" title="{$subcategorie.titre}" class="CCCcategoryImage img-responsive" /></a>
					               			</div>
			               			 {/foreach}
			               		{/if}
						  	</div>	
						  	<div class="swiper-pagination swiper-pagination{$smarty.foreach.cats.iteration}"></div>
						</div>    
			      	{/foreach}
    			<script>
    			{foreach from=$categories item=categorie name=cats}
			    var swiper{$smarty.foreach.cats.iteration} = new Swiper('.swiper{$smarty.foreach.cats.iteration}', {
			        pagination: '.swiper-pagination{$smarty.foreach.cats.iteration}',
			        paginationClickable: true,
			    	autoplay:{if $nbrp_sub25 == 1} 2500{else} 0,{/if},
        			mousewheelControl: true
			    });
			    {/foreach}
			    </script>
		    {/if}
    {/if}
{/if}