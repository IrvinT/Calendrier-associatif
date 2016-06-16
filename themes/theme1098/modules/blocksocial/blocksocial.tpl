<section id="social_block" class="footer-block">
	<h4>{l s='Follow us' mod='blocksocial'}</h4>
	<ul class="toggle-footer">
		{if $facebook_url != ''}
			<li class="facebook">
				<a target="_blank" href="{$facebook_url|escape:html:'UTF-8'}" title="{l s='Facebook' mod='blocksocial'}">
					{* <span>{l s='Facebook' mod='blocksocial'}</span> *}
					{* <i class="fa fa-facebook" aria-hidden="true"></i> *}
					{* <i class="fa fa-facebook-official" aria-hidden="true"></i> *}

					<?xml version="1.0" encoding="utf-8"?>
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
		{/if}
		{if $twitter_url != ''}
			<li class="twitter">
				<a target="_blank" href="{$twitter_url|escape:html:'UTF-8'}" title="{l s='Twitter' mod='blocksocial'}">
					<span>{l s='Twitter' mod='blocksocial'}</span>
				</a>
			</li>
		{/if}
		{if $rss_url != ''}
			<li class="rss">
				<a target="_blank" href="{$rss_url|escape:html:'UTF-8'}" title="{l s='RSS' mod='blocksocial'}">
					<span>{l s='RSS' mod='blocksocial'}</span>
				</a>
			</li>
		{/if}
        {if $youtube_url != ''}
        	<li class="youtube">
        		<a target="_blank" href="{$youtube_url|escape:html:'UTF-8'}" title="{l s='Youtube' mod='blocksocial'}">
        			<span>{l s='Youtube' mod='blocksocial'}</span>
        		</a>
        	</li>
        {/if}
        {if $google_plus_url != ''}
        	<li class="google-plus">
        		<a target="_blank" href="{$google_plus_url|escape:html:'UTF-8'}" title="{l s='Google Plus' mod='blocksocial'}">
        			<span>{l s='Google Plus' mod='blocksocial'}</span>
        		</a>
        	</li>
        {/if}
        {if $pinterest_url != ''}
        	<li class="pinterest">
        		<a target="_blank" href="{$pinterest_url|escape:html:'UTF-8'}" title="{l s='Pinterest' mod='blocksocial'}">
        			<span>{l s='Pinterest' mod='blocksocial'}</span>
        		</a>
        	</li>
        {/if}
	</ul>
</section>