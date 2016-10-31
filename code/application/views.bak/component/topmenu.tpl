{getconfig path="core.url_suffix" assign="url_suffix"}
{foreach from=$data item=item}
		{if $item.target != 'cart'}
        <td style="background: url('{$files[$item.id]._rows.image.src}') no-repeat; width:{$files[$item.id]._rows.image.width}px" height="129">
             <div style="padding-top: 70px;text-align:center;">
             	 <a class="alogo" href="/{$item.uri}{$url_suffix}" title="{$item.title}"
             	 	{if $item.lft<=$page_active.lft && $item.rgt>=$page_active.rgt}style="color:red"{/if}>{$item.title}</a>
             </div>
        </td>
        {else}
        	{if $cart_full}
        		<td id='cart_node' style="background: url('/i/cart_full.jpg') no-repeat; width:{$files[$item.id]._rows.image.width}px" height="129">
		             <div style="padding-top: 70px;text-align:center;">
		             	 <a class="alogo" href="/{$item.uri}{$url_suffix}" title="{$item.title}"
		             	 	{if $item.lft<=$page_active.lft && $item.rgt>=$page_active.rgt}style="color:red"{/if}>{$item.title}</a>
		             </div>
		        </td>
        	{else}
        		<td id='cart_node' style="background: url('/i/cart_empty.jpg') no-repeat; width:{$files[$item.id]._rows.image.width}px" height="129">
		             <div style="padding-top: 70px;text-align:center;">
		             	 <a class="alogo" href="/{$item.uri}{$url_suffix}" title="{$item.title}"
		             	 	{if $item.lft<=$page_active.lft && $item.rgt>=$page_active.rgt}style="color:red"{/if}>{$item.title}</a>
		             </div>
		        </td>
        	{/if}
        {/if}
{/foreach}