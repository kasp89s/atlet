{getconfig path="core.url_suffix" assign="url_suffix"}

{if count($groups)>0}
	<h3>Подразделы</h3>
	<table width="97%" align="center">
	  <tbody>
	{foreach name=frchGroups from=$groups item=group}
            <tr>
              <td valign="bottom" width="1px">
              	  {if $groupsFiles[$group.id]._rows.image.preview_2}
				  <a href="{$uri_base}{$group.uri_base}{$url_suffix}">
				  	  <img style="float:left;margin-right:5px;margin-bottom:5px;" src="/files/catalog_groups/photo/{$groupsFiles[$group.id]._rows.image.id}/2/{$group.uri}.{$groupsFiles[$group.id]._rows.image.ext}" border="0">
                  </a>
                  {/if}
              </td>
         	  <td valign="top" style="font-size:12px;">
                  <a class="alogo" href="{$uri_base}{$group.uri_base}{$url_suffix}">{$group.title}</a>
                  <br />
                  <p style="text-indent:0;">{$group.short_descr}</p>
              </td>
            </tr>

            {if not $smarty.foreach.frchGroups.last}
            <tr>
              <td colspan="3" width="100%" height="10px"></td>
            </tr>
            <tr>
              <td colspan="3" width="100%" height="2" style="background: url('/i/gray.gif') repeat-x"></td>
            </tr>
            {/if}
 	{/foreach}
	  </tbody>
	</table>
{/if}

{if count($groups)>0}
    <br /><br /><br />
{/if}

{if count($elements) >0}

<h3>Товары</h3>
<table width="95%" align="center" cellspacing=0 cellpadding=9 style="text-align: left;">
<tr>
{counter name="cntIterations" start=0 skip=1 print=false assign="cntIterations"}
{foreach name="frchElements" from=$elements item=item}
{counter name="cntIterations"}


		<td class="product_item{if $item.in_action} in_action{/if}">
			<table>
				<tr>
					<td class="img_cont">
						<a href="{$uri_base}{$plane_tree[$groupInfo.id].uri_base}/{$item.uri}{$url_suffix}">
							{if $item.files._rows.image.preview_2}
							<img src="/files/catalog/photo/{$item.files._rows.image.id}/2/{$item.uri}.{$item.files._rows.image.ext}" border="0"
							 {if $item.use_h1 > 0}alt="{if $item.concat_with_section_title}{$item.name} {$item.seo_name}{else}{$item.name}{/if}"{/if}
							 {if $item.use_h1 > 0}title="{if $item.concat_with_section_title}{$item.name} {$item.seo_name}{else}{$item.name}{/if}"{/if}
							 align="left" />
							{/if}
						</a>
					</td>
					<td class="product_desc">
						<a class="alogo" href="{$uri_base}{$plane_tree[$groupInfo.id].uri_base}/{$item.uri}{$url_suffix}">
							{$item.name}
						</a>
						<br/>
						{if $item.in_action and $item.oldprice > 0}<font class="oldprice">{$item.oldprice|show_number} руб.</font><br />{/if}
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
{/if}

{if count($elements) ==0 and count($groups)==0}
	<h3 class="centered">Данная категория пуста</h3>
{/if}

