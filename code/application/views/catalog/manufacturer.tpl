{getconfig path="core.url_suffix" assign="url_suffix"}
<section class="page-catalog">
	<blockquote class="anons">
		<p>{$groupInfo.full_descr}</p>
	</blockquote>
	<section class="catalog">
		{foreach name="frchElements" from=$data.rows item=item}
            <article>
                <header><a href="{$uri_base}{$groups[$item.group_id].uri_base}/{$item.uri}{$url_suffix}">{$item.name|truncate:25:"..."}</a></header>
                <figure><a href="{$uri_base}{$groups[$item.group_id].uri_base}/{$item.uri}{$url_suffix}"><img src="/files/catalog/photo/{$item.files._rows.image.id}/1/{$item.uri}.{$item.files._rows.image.ext}" alt="{$item.name|truncate:25:"..."}" title="{$item.name|truncate:25:"..."}"></a></figure>
                <p>{$item.manufacturer_name}</p>
                <div class="txt-card">
                    <span class="gray">{$item.volume}</span>
                    {if $item.price > 0 && ($item.availability > 0 || $item.availability2 > 0)}
                        <p class="nal">в наличии</p>
                    {else}
                        <p class="not-nal">нет в наличии</p>
                    {/if}
                    <p class="price"><span>{$item.price}</span> руб.</p>
                </div>
            </article>
		{/foreach}
	</section>
</section>
