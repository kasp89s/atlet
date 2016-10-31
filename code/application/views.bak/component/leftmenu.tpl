{getconfig path="core.url_suffix" assign="url_suffix"}
<!--//<tr><td height="20px" colspan=2>&nbsp;</td></tr>//-->
{foreach from=$data.groups item=item}
	{if $item.active}
        {if $prev and $prev.is_vip != $item.is_vip}
        	<tr><td colspan=2><a href="{$action_uri_base}{$url_suffix}"><img src="/i/action_banner.jpg" alt="Акция!!!" border="0"></a></td></tr>
        {/if}
        <tr>
        	<td style="width:7px;padding-left:15px;{if $intActiveGroup==$item.id and not isset($intActiveSubGroup)};background: url('/i/act_menu.jpg') no-repeat;{/if}">
                {if count($data.tree_groups[$item.id].rows) > 0}
                    <a href="javascript:void" class="toggler" id="{$item.id}"><img class="minusbut{$item.id}" style="{if $intActiveGroup==$item.id}display:inline{else}display:none{/if}" src="/i/minus.png" border="0"><img class="plusbut{$item.id}" style="{if $intActiveGroup==$item.id}display:none{else}display:inline{/if}" src="/i/plus.png" border="0"></a>
                {else}
                	&nbsp;
                {/if}
        	</td>
        	<td style="padding:0 0 2px 5px;line-height:15px;
        				{if $intActiveGroup==$item.id and not isset($intActiveSubGroup)};background: url('/i/act_menu.jpg') no-repeat;height:16px;line-height:12px;{/if}">
        		<a class="menu" href="{$uri_base}/{$item.uri}{$url_suffix}" {if $item.is_custom_color and strlen($item.custom_color_value)>2}style="font-weight:bold;color: #{$item.custom_color_value}"{/if}>{$item.title}</a>
        	</td>
        </tr>

       	{foreach from=$data.tree_groups[$item.id].rows item=sub_item}
       		{if $sub_item.active}
	       		<tr class="menuitem{$item.id}" style="{if $intActiveGroup==$item.id}{else}display:none;{/if}">
	       			<td style="width:7px;{if $intActiveSubGroup==$sub_item.id};background: url('/i/act_menu.jpg') no-repeat;height:16px;{/if}">
	                    &nbsp;
	        		</td>
		        	<td style="padding:0 0 2px 20px;line-height:15px;{if $intActiveSubGroup==$sub_item.id};background: url('/i/act_menu.jpg') no-repeat;{/if}
		        				{if $intActiveSubGroup==$sub_item.id};background: url('/i/act_menu.jpg') no-repeat;height:16px;{/if}">
		        		<a class="menu" href="{$uri_base}/{$item.uri}/{$sub_item.uri}{$url_suffix}"{if $sub_item.is_custom_color and strlen($sub_item.custom_color_value)>2}style="font-weight:bold;color: #{$sub_item.custom_color_value}"{/if}>{$sub_item.title}</a>
		        	</td>
		        </tr>
	        {/if}
       	{/foreach}

        {assign var="prev" value=$item}
    {/if}
{/foreach}

{literal}
    <script language="JavaScript">
          $(".toggler").click(function(){
          	   $(".minusbut"+$(this).attr('id')).toggle();
          	   $(".plusbut"+$(this).attr('id')).toggle();
          	   $(".menuitem"+$(this).attr('id')).toggle();
          });
    </script>
{/literal}




