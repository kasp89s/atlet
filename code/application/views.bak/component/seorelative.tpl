{getconfig path="core.url_suffix" assign="url_suffix"}
{if count($groups)>0 or count($products)>0}
  <table width="90%" height="98%" align="center" cellpadding="0" cellspacing="0" bgcolor="white">
   <tr>
    <td width="8" height="9" valign="top"><img src="/i/t_upleft.jpg" width="8" height="9" alt="-" /></td>
	<td style="background: url('/i/t_upcen1.gif') repeat-x" height="9" width="*"></td>
	<td width="8" height="9" valign="top"><img src="/i/t_upright.jpg" width="8" height="9" alt="-" /></td>
   </tr>
   <tr>
    <td width="8" height="*" valign="top" style="background: url('/i/t_left.jpg') repeat-y"><!--ПУсто--></td>
	<td valign="top">

	 <table width="97%" align="center">
	  <tr>
	   <td valign="top">
<font class="z">Смотрите также:</font>

{if count($groups)>0}
<div style="width:100%;">
     {foreach from=$groups item=item}
          <div class="relative_block_item">
          	   <span style="float: left; width: 110px;">
          	   {if $item.files._rows.image.preview_3}
          	   <a style="text-decoration:none;" target="_blank" href="{$catalog_uri_base}{$item.uri_base}{$url_suffix}">
          	   	   <img src="/files/catalog_groups/photo/{$item.files._rows.image.id}/3/{$item.uri}.{$item.files._rows.image.ext}" alt="{$item.title}" border="0">
          	   </a>
          	   {/if}
          	   </span>
          	   <div class="relative_block_text">
	               <a style="text-decoration:none;" target="_blank" href="{$catalog_uri_base}{$item.uri_base}{$url_suffix}"><b>{$item.title}</b></a>
	               <br />
	               <span><i>{$item.short_descr|strip_tags|truncate:70:"..."}</i></span>
               </div>
          </div>
     {/foreach}
</div>
{/if}

{if count($products)>0}
<div style="width:100%;">
     {foreach from=$products item=item}
          <div class="relative_block_item">
               <span style="float: left; width: 110px;">
          	   {if $item.files._rows.image.preview_3}
          	   <a style="text-decoration:none;" target="_blank" href="{$catalog_uri_base}{$item.uri_base}{$url_suffix}">
          	   	   <img src="/files/catalog/photo/{$item.files._rows.image.id}/3/{$item.uri}.{$item.files._rows.image.ext}" alt="{$item.name}" border="0">
          	   </a>
          	   {/if}
          	   </span>
          	   <div class="relative_block_text">
               <a style="text-decoration:none;" target="_blank" href="{$catalog_uri_base}{$item.uri_base}{$url_suffix}"><b>{$item.name}</b></a>
               <br />
	               <a style="text-decoration:none;" target="_blank" href="{$catalog_uri_base}{$item.uri_base}{$url_suffix}"><span>Артикул: {$item.code}</span></a>
	               <br />
	               <span><i>{$item.description|strip_tags|truncate:70:"..."}</i></span>
               </div>
          </div>
     {/foreach}
</div>

{/if}

<br clear="all">

</td>
	  </tr>
	 </table>
	</td>
	<td width="8" height="*" align="left" valign="top" style="background: url('/i/t_right.jpg') repeat-y"><!--ПУсто--></td>
   </tr>
   <tr>
    <td width="8" height="9" valign="top"><img src="/i/t_downleft.jpg" width="8" height="9" alt="-" /></td>
	<td style="background: url('/i/t_upcen1.gif') repeat-x" height="8" width="*"></td>
	<td width="8" height="9" valign="top"><img src="/i/t_dowright.jpg" width="8" height="9" alt="-" /></td>
   </tr>
  </table>

  <br clear="all">
  <br clear="all">

 {/if}