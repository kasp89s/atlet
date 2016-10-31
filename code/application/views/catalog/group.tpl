{getconfig path="core.url_suffix" assign="url_suffix"}
<section class="page-catalog">
	<blockquote class="anons">
		<p>{$additionalText}</p>
	</blockquote>
    <div class="sorting" {if empty($additionalText)} style="border-top: none;" {/if}>
        <form action="" name="sort" id="sort-form">
            <select name="price" class="sel210">
                <option value="false">СОРТИРОВАТЬ ПО ЦЕНЕ</option>
                <option value="0">Без сортировки</option>
                <option value="asc" {if !empty($sort.price)}{if $sort.price == 'asc'}selected{/if}{/if} >Сначала дешевые</option>
                <option value="desc" {if !empty($sort.price)}{if $sort.price == 'desc'}selected{/if}{/if}>Сначала дорогие</option>
            </select>
            {if !empty($manufacturers)}
            <select name="manufacturer" class="sel210">
                <option value="false">ПО ПРОИЗВОДИТЕЛЯМ</option>
                <option value="0">Без сортировки</option>
                {foreach from=$manufacturers item=item}
                <option value="{$item.id}" {if !empty($sort.manufacturer)}{if $sort.manufacturer == $item.id}selected{/if}{/if}>{$item.name}</option>
                {/foreach}
            </select>
            {/if}
        </form>
    </div>
	<section class="catalog">
		{foreach name="frchElements" from=$elements item=item}
		{*<article>*}
			{*<header><a href="{$uri_base}{$plane_tree[$item.group_id].uri_base}/{$item.uri}{$url_suffix}">{$item.name|truncate:25:"..."}</a></header>*}
			{*<figure><a href="{$uri_base}{$plane_tree[$item.group_id].uri_base}/{$item.uri}{$url_suffix}"><img src="/files/catalog/photo/{$item.files._rows.image.id}/2/{$item.uri}.{$item.files._rows.image.ext}" alt="" /></a></figure>*}
			{*<p class="price"><span>{$item.price|show_number}</span> руб.</p>*}
			{*<a href="{$uri_base}{$plane_tree[$item.group_id].uri_base}/{$item.uri}{$url_suffix}" class="btn">Подробнее</a>*}
		{*</article>*}
            <article>
                <header><a href="{$uri_base}{$plane_tree[$item.group_id].uri_base}/{$item.uri}{$url_suffix}">{$item.name|truncate:25:"..."}</a></header>
                <figure><a href="{$uri_base}{$plane_tree[$item.group_id].uri_base}/{$item.uri}{$url_suffix}"><img src="/files/catalog/photo/{$item.files._rows.image.id}/1/{$item.uri}.{$item.files._rows.image.ext}" alt="{$item.name|truncate:25:"..."}" title="{$item.name|truncate:25:"..."}"></a></figure>
                <p>{$item.manufacturer_name}</p>
                <div class="txt-card">
                    <span class="gray">{$item.volume}</span>
                    {*{if $item.price > 0}*}
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
