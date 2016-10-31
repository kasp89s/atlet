{getconfig path="core.url_suffix" assign="url_suffix"}
{if count($products)>0}
<header class="head-prod">Рекомендуемые товары:</header>
<section class="catalog">
    {*{if isset($favorite)}*}
        {*<article>*}
            {*<header><a href="{$catalog_uri_base}{$favorite.uri_base}{$url_suffix}">{$favorite.name}</a></header>*}
            {*<figure><a href="{$catalog_uri_base}{$favorite.uri_base}{$url_suffix}"><img src="/files/catalog/photo/{$favorite.files._rows.image.id}/3/{$favorite.uri}.{$favorite.files._rows.image.ext}" alt="" /></a></figure>*}
            {*<p class="price"><span>{$favorite.price|show_number}</span> руб.</p>*}
        {*</article>*}
    {*{/if}*}
	{foreach from=$products item=item}
	<article>
		<header><a href="{$catalog_uri_base}{$item.uri_base}{$url_suffix}">{$item.name}</a></header>
		<figure><a href="{$catalog_uri_base}{$item.uri_base}{$url_suffix}"><img src="/files/catalog/photo/{$item.files._rows.image.id}/3/{$item.uri}.{$item.files._rows.image.ext}" alt="{$item.name}" title="{$item.name}"/></a></figure>
		<p class="price"><span>{$item.price|show_number}</span> руб.</p>
	</article>
	{/foreach}
</section>
{/if}
