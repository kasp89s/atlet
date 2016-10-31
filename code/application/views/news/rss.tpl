{getconfig path="core.url_suffix" assign="url_suffix"}
<?xml version="1.0" encoding="UTF-8" ?>
 <rss version="2.0">
	 <channel>
		 <title>Новости интернет магазина luxpodarki.ru</title>
		 <link>http://luxpodarki.ru</link>
		 <description>
		 	  Невозможно описать абсолютно все товары, представленные в магазине элитных подарков ,
		 	  да и делать этого совершенно ни к чему, ведь как гласит древняя народная мудрость
		 	  «Лучше один раз увидеть собственными глазами, чем сто раз прочитать об этом в Интернете»!

			  Добро пожаловать в магазин LuxPodarki.ru!
		 </description>
		 <lastBuildDate>{$data.rows[0].date_publication}</lastBuildDate>
         {foreach name="frchNews" from=$data.rows item=item}
			<item>
				 <title>{$item.name}</title>
				 <link>http://luxpodarki.ru{$uri_base}/{$item.uri}{$url_suffix}</link>
				 <description>{$item.preview}</description>
				 <pubDate>{$item.date_publication}</pubDate>
		    </item>
		{/foreach}
	 </channel>
 </rss>