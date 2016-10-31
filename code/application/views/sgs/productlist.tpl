<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="{"now"|date_format:"%Y-%m-%d %H:%M"}">
	<shop>
		<name>ЛюксПодарки.РУ</name>
	    <company>ООО Подарки Королей</company>
	    <url>http://luxpodarki.ru/</url>
		<currencies>
			<currency id="RUR" rate="1"/>
		</currencies>
	{getconfig path="core.url_suffix" assign="url_suffix"}
    <categories>
	{foreach name="frchGroups" from=$groups item=item}
         <category id="{$item.id}" {if $item.parent_id >0}parentId="{$item.parent_id}"{/if}>{$item.title}</category>
	{/foreach}
	</categories>

	{counter name="cntIterations" start=0 skip=1 print=false assign="cntIterations"}
	<offers>
	{foreach name="frchElements" from=$elements item=item}
		{if $item.group_id > 0 and $item.price > 0}
			{counter name="cntIterations"}
			<offer id="{$item.id}">

				<url>http://luxpodarki.ru{$catalog_uri_base}{$groups[$item.group_id].uri_base}/{$item.uri}{$url_suffix}</url>
				<price>{$item.price}</price>
				<currencyId>RUR</currencyId>
				<categoryId>{$item.group_id}</categoryId>
				{if $elementsFiles[$item.id]._rows.image.src}
					<picture>http://luxpodarki.ru{$elementsFiles[$item.id]._rows.image.src}</picture>
					{foreach name="frchPhotos" from=$elementsFiles[$item.id].photo item=itemphoto}
						<picture>http://luxpodarki.ru{$itemphoto.src}</picture>
					{/foreach}
		        {/if}
				<delivery>true</delivery>
				{if $item.price > 15000}
					<local_delivery_cost>0</local_delivery_cost>
				{else}
					<local_delivery_cost>300</local_delivery_cost>
				{/if}
				<name>{if $item.concat_with_section_title}{$item.name} {$item.seo_name}{else}{$item.name}{/if}</name>
				<description>
				    {$item.description|strip_tags|truncate:250:"..."}
				</description>
                <available>{$item.avail_code}</available>
			</offer>
		{/if}
	{/foreach}
    </offers>

	</shop>
	<state>1</state>
</yml_catalog>