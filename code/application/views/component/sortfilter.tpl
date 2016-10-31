{getconfig path="core.url_suffix" assign="url_suffix"}

<div class="viewer round">
	<!--//<dl>
		<dt>Показать:</dt>
		<dd><a href="{$data.current_url}{$url_suffix}" class="active">8</a></dd>
		<dd><a href="{$data.current_url}{$url_suffix}" title="">50</a></dd>
		<dd><a href="{$data.current_url}{$url_suffix}" title="">100</a></dd>
		<dd class="show-all"><a href="{$data.current_url}{$url_suffix}" title="">Все <i>&#9658;</i></a></dd>
	</dl>//-->
	<div class="search">
		<form method="get" action="{$catalog_uri_base}/search{$url_suffix}">
			<input type="text" name="search_words" value="Быстрый поиск" />
			<input type="image" src="/img/search.png" />
		</form>
	</div>
</div>