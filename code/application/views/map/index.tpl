{getconfig path="core.url_suffix" assign="url_suffix"}
<font class="z">Карта сайта</font>
<br /><br />
{counter name="cntOpened" start=1 skip=1 print=false assign="opened"}
{counter name="cntClosed" start=1 skip=1 print=false assign="closed"}
{foreach from=$tree item=item key=key}
     {if $item.level<$prev.level}
	      {section name="i" loop=`$prev.level-$item.level`}
	 		   </div>
	 		   {counter name="cntClosed"}
		  {/section}
	 {/if}

     {if $item.level>$cat_prev.level}
          {counter name="cntOpened"}
	      <div class="mapcontainer">
	 {/if}

     <a class="mapitem link4" href="{$item.uri_base}{if strlen($item.uri_base) > 0}{$url_suffix}{else}/{/if}" title="{$item.short_descr}">{$item.title}</a>
     {if $item.target == "catalog"}
     <div class="mapcontainer">
            {counter name="cntCatOpened" start=0 skip=1 print=false assign="cat_opened"}
			{counter name="cntCatClosed" start=0 skip=1 print=false assign="cat_closed"}
			{foreach from=$catalog item=cat_item key=cat_key}
				 {if $cat_item.level>0}
				     {if $cat_item.level<$cat_prev.level}
					      {section name="i" loop=`$cat_prev.level-$cat_item.level`}
					 		   </div>
					 		   {counter name="cntCatClosed"}
						  {/section}
					 {/if}
					 {if $cat_item.level>$cat_prev.level and $cat_prev.level != 0}
				          {counter name="cntCatOpened"}
						  <div class="mapcontainer">
					 {/if}
				     <a class="mapitem link4" href="{$catalog_uri_base}{$cat_item.uri_base}{$url_suffix}" title="{$cat_item.short_descr}">{$cat_item.title}</a>
                 {/if}
				 {assign var="cat_prev" value=$cat_item}
			{/foreach}
			{section name="i" loop=`$cat_opened-$cat_closed`}
				</div>
			{/section}
     </div>
     {/if}

	 {assign var="prev" value=$item}
{/foreach}
{section name="i" loop=`$opened-$closed`}
	</div>
{/section}