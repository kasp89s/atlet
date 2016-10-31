{getconfig path="core.url_suffix" assign="url_suffix"}
<table width="97%" align="center">
  <tbody>
    <tr>
      <td valign="top">
        <br>
        <font class="z" style="text-transform: uppercase">Каталог</font>
        <br>
        <br>
      </td>
    </tr>
{if count($groups)>0}
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
{/if}
  </tbody>
</table>



