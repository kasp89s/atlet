{getconfig path="core.url_suffix" assign="url_suffix"}
{if count($data) > 0}
<ul class="top-banners">
	{foreach from=$data item=item}
	<li>
		<a href="{$catalog_uri_base}{$item.group_uri}{$item.uri}{$url_suffix}" title="">
			<span class="top-banner-img"><img src="/files/catalog/photo/{$item.id}/3/{$item.uri}.{$item.ext}" alt="" /></span>
			<span class="top-banner-desc">
				<strong>{$item.product_name|truncate:40:"..."}</strong>
				<!--{$item.short_descr}-->
			</span>
		</a>
	</li>
	{/foreach}
</ul>
{/if}