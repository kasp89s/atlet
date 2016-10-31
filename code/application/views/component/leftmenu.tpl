{getconfig path="core.url_suffix" assign="url_suffix"}
<form action="#">
	<select id="manselect" class="sel210">
		<option>Производители</option>
		{foreach from=$data.manufacturers item=item}
            {if $item.id != 64 && $item.id != 100 && $item.id != 53 && $item.id != 105 && $item.id != 116}
		        <option value="{$item.id}">{if $item.name != ""}{$item.name}{else}NONAME{/if} ({$item.cnt})</option>
            {/if}
        {/foreach}
	</select>
</form>
<script type="text/javascript">
{literal}
	$('#manselect').change(function(){
		if($('#manselect').val() > 0){
			window.location = '/catalog/manufacturer/'+$('#manselect').val()+'/';
		}
	});
{/literal}
</script>
<nav class="left-menu">
	<ul>
		{foreach from=$data.groups item=item}
			<li>
				<a{if $intActiveGroup==$item.id} class="open"{/if} href="{if count($data.tree_groups[$item.id].rows) > 0 }#{else}{$uri_base}/{$item.uri}{$url_suffix}{/if}" title="{$item.title}">{$item.title}</a>
				{if count($data.tree_groups[$item.id].rows)>0}
				<ul{if $intActiveGroup==$item.id} style="display: block;"{/if}>
					{foreach from=$data.tree_groups[$item.id].rows item=sub_item}
			       		{if $intActiveSubGroup==$sub_item.id}
						<li><a href="#" title="">{$sub_item.title}</a></li>
						{else}
						<li><a href="{$uri_base}/{$item.uri}/{$sub_item.uri}{$url_suffix}" title="">{$sub_item.title}</a></li>
						{/if}
					{/foreach}
				</ul>
				{/if}
			</li>
		{/foreach}
	</ul>
</nav>
