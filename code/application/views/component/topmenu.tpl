{getconfig path="core.url_suffix" assign="url_suffix"}
<ul class="main-menu">
	{foreach from=$data.groups item=item}
	<li>
		<div class="menu-wrapper">
			<a href="{if count($data.tree_groups[$item.id].rows) > 0}#{else}{$uri_base}/{$item.uri}{$url_suffix}{/if}" title="{$item.title}" class="bullet"><i></i>{$item.title|truncate:26:"...":true}</a>
			{if count($data.tree_groups[$item.id].rows) > 0}
			<div class="drop-menu-wrapper">
				<div class="drop-menu">
					<i class="drop-menu-arrow"></i>
					<dl>
						<dt>
							{foreach from=$data.tree_groups[$item.id].rows item=sub_item}
							<span><a href="{$uri_base}/{$item.uri}/{$sub_item.uri}{$url_suffix}" title="" class="bullet"><i></i>{$sub_item.title}</a></span>
							{/foreach}
						</dt>
						{foreach name=imgLoop from=$item.products item=prod}
						<dd><div><a href="{$uri_base}{$plainTree[$prod.sect_id].uri_base}/{$prod.uri}/"><img src="/files/catalog/photo/{$prod.id}/3/{$prod.uri}.{$prod.ext}" alt="" /></a></div></dd>
						{if $smarty.foreach.imgLoop.index%2 == 1}<br/>{/if}
						{/foreach}

					</dl>
				</div>
			</div>
			{/if}
		</div>
	</li>
	{/foreach}
</ul>