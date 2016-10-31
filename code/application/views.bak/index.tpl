{getconfig path="core.url_suffix" assign="url_suffix"}
<table width="95%" align="center" cellspacing=0 cellpadding=9 style="text-align: left;">
<tr>
{counter name="cntIterations" start=0 skip=1 print=false assign="cntIterations"}
{foreach name="frchElements" from=$elements item=item}
{counter name="cntIterations"}


		<td class="product_item{if $item.in_action} in_action{/if}">
			<table>
				<tr>
					<td class="img_cont">
						<a href="{$catalog_uri_base}{$groups[$item.group_id].uri_base}/{$item.uri}{$url_suffix}">
							{if $elementsFiles[$item.id]._rows.image.preview_2}
							<img src="/files/catalog/photo/{$elementsFiles[$item.id]._rows.image.id}/2/{$item.uri}.{$elementsFiles[$item.id]._rows.image.ext}"
							 {if $item.use_h1 > 0}alt="{if $item.concat_with_section_title}{$item.name} {$item.seo_name}{else}{$item.name}{/if}"{/if}
							 {if $item.use_h1 > 0}title="{if $item.concat_with_section_title}{$item.name} {$item.seo_name}{else}{$item.name}{/if}"{/if}
							 border="0" align="left" />
							{/if}
						</a>
					</td>
					<td class="product_desc">
						<a class="alogo" href="{$catalog_uri_base}{$groups[$item.group_id].uri_base}/{$item.uri}{$url_suffix}">
							{$item.name}
						</a>
						<br/>
						{if $item.in_action > 0 and $item.oldprice > 0}<font class="oldprice">{$item.oldprice|show_number} руб.</font><br />{/if}
						<font class="art">Цена: </font><font class="pr">{$item.price|show_number}</font>
						<font class="art"> руб.</font>
						<br/>
						<font class="art">Артикул: {$item.code}</font>
					</td>
				</tr>
			</table>
		</td>


{if $cntIterations%2 == 1}
	<td width="2%" style="background: url('/i/gray.gif') repeat-y"></td>
{else}
	</tr>
	<tr><td colspan="5" width="100%" height="2" style="background: url('/i/gray.gif') repeat-x"></td></tr>
	<tr>
{/if}
{/foreach}

{if $cntIterations%2 == 1}
		<td >
			<!--Пусто-->
		</td>
		<td >
			<!--Пусто-->
		</td>
	{/if}
</tr>
</table>