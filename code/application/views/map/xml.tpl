{getconfig path="core.url_suffix" assign="url_suffix"}
<?xml version="1.0" encoding="utf8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach from=$tree item=item key=key}
     <url>
	  	 <loc>http://luxpodarki.ru{$item.uri_base}{if strlen($item.uri_base)>0}{$url_suffix}{/if}</loc>
	  	 <changefreq>daily</changefreq>
     </url>

     {if $item.target == "catalog"}
			{foreach from=$catalog item=cat_item key=cat_key}
				 {if $cat_item.level>0}
	 <url>
	  	 <loc>http://luxpodarki.ru{$catalog_uri_base}{$cat_item.uri_base}{$url_suffix}</loc>
	  	 <changefreq>daily</changefreq>
     </url>
                 {/if}
			{/foreach}
     {/if}

{/foreach}
</urlset>