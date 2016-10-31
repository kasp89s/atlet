{getconfig path="core.url_suffix" assign="url_suffix"}

<div class="card">
	<br />
	<div class="t-card clearfix">
		<div class="l-t-card">
			<div id="slider" class="flexslider">
				<ul class="slides">
					{if $files._rows.image.preview_2}
					<li><a href="/files/catalog/photo/{$files._rows.image.id}/0/{$data.uri}.{$files._rows.image.ext}" class="fancybox" rel="galery"><img src="/files/catalog/photo/{$files._rows.image.id}/1/{$data.uri}.{$files._rows.image.ext}" alt="{$data.name}" title="{$data.name}"/></a></li>
					{/if}
					{if count($files.photo) > 0}
		          	{foreach from=$files.photo item=photoitem}
		          		<li><a href="/files/catalog/photo/{$photoitem.id}/0/{$data.uri}.{$photoitem.ext}" class="fancybox" rel="galery"><img src="/files/catalog/photo/{$photoitem.id}/1/{$data.uri}.{$photoitem.ext}" alt="{$data.name}" title="{$data.name}"/></a></li>
					{/foreach}
					{/if}
				</ul>
			</div>
			{*<div id="carousel" class="flexslider">*}
				{*<ul class="slides">*}
					{*{if $files._rows.image.preview_2}*}
					{*<li><img src="/files/catalog/photo/{$files._rows.image.id}/0/{$data.uri}.{$files._rows.image.ext}" alt="" /></li>*}
					{*{/if}*}
					{*{if count($files.photo) > 0}*}
		          	{*{foreach from=$files.photo item=photoitem}*}
		          		{*<li><img src="/files/catalog/photo/{$photoitem.id}/0/{$data.uri}.{$photoitem.ext}" alt="" /></li>*}
					{*{/foreach}*}
					{*{/if}*}
				{*</ul>*}
			{*</div>*}
		</div>
		<div class="r-t-card">
			<div class="manufacturer clearfix">
				<div class="l-manufacturer">
					{if $data.manufacturer_name}
					<p>
						<span class="l-man">Производитель:</span>
						<span class="r-man"><a href="/catalog/manufacturer/{$data.manufacturer_id}/">{$data.manufacturer_name}</a></span>
					</p>
					{/if}
                    {if $data.volume != ""}
					<p>
						<span class="l-man">Объем:</span>
						<span class="r-man">{$data.volume}</span>
					</p>
					{/if}
                    <p>
                        <span class="l-man">Артикул:</span>
                        {*<span class="r-man">475<span>{$data.id}</span></span>*}
                        <span class="r-man">475<span id="current-article">{$tastes[0].article}</span></span>
                    </p>
					<p>
						<span class="l-man">Наличие:</span>
						{*<span class="r-man" style="color:#81b700">есть на складе</span>*}
                        {if $data.price > 0 && ($data.availability > 0 || $data.availability2 > 0)}
                        {*{if $data.price > 0}*}
                            <span class="r-man" style="color:#5a8d00"><b>есть в наличии</b></span>
                        {else}
                            <span class="r-man" style="color:#990000">нет в наличии</span>
                        {/if}
					</p>
				</div>
			</div>
			<form action="#">
                {if count($tastes) > 0}
                    {if $tastes[0].name != 'без вкуса'}
                        {if $data.availability2 > 0}
                            <div class="choice clearfix" style="width: 357px;">
                                <input type="hidden" id="hour" value="{$hour}"/>
                                {foreach from=$tastes item=item}
                                    <input type="hidden" id="{$item.trans}" value="{if $item.count2 < 1}0{else}1{/if}" data-article="{$item.article}" data-count="{if $item.count2 < 1}{$item.count}{else}0{/if}" />
                                {/foreach}
                                <p>Выберите вкус:</p>
                                <select id="tasteSelector" class="sel210">
                                    {foreach from=$tastes item=item}
                                        <option {if $item.count2 < 1}data-count="0"{else}data-count="1"{/if}  value="{$item.name}" {if $item.count2 < 1}disabled{/if}>{$item.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        {elseif $data.availability > 0}
                            <div class="choice clearfix" style="width: 357px;">
                                <input type="hidden" id="hour" value="{$hour}"/>
                                {foreach from=$tastes item=item}
                                    <input type="hidden" id="{$item.trans}" value="{if $item.count < 1}0{else}1{/if}" data-article="{$item.article}" data-count="{if $item.count < 1}{$item.count}{else}0{/if}" />
                                {/foreach}
                                <p>Выберите вкус:</p>
                                <select id="tasteSelector" class="sel210">
                                    {foreach from=$tastes item=item}
                                        <option {if $item.count < 1}data-count="0"{else}data-count="1"{/if}  value="{$item.name}" {if $item.count < 1}disabled{/if}>{$item.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        {/if}
                    {/if}
                {else}
				{if count($data.arrTastes) > 0}
				<div class="choice clearfix" style="width: 357px;">
					<p>Выберите вкус:</p>
					<select id="tasteSelector" class="sel210">
						<option value="empty">Выберите вкус</option>
						{foreach from=$data.arrTastes item=item}
						<option value="{$item}">{$item}</option>
						{/foreach}
					</select>
				</div>
				{/if}
                {/if}
				<div class="bot-card">
					<p class="price">
                        {if $data.availability2 > 0}
                            {if $data.price > 0}<span>{$data.price|show_number}</span> руб.{/if}
                        {elseif $data.availability > 0}
                            {if $data.price > 0}<span>{$data.priceSupplier|show_number}</span> руб.{/if}
                        {else}
                            {if $data.price > 0}<span>{$data.price|show_number}</span> руб.{/if}
                        {/if}
					</p>
					{if $data.price > 0 && ($data.availability > 0 || $data.availability2 > 0)}
                        <input type="submit" value="Купить" class="btn addToCartLink" href="/cart/add?product_id={$data.id}"/>
                        <p style="margin-left: 5px;" id="delivery-date" data-warehouse="{if $tastes[0].count2 > 0}2{else}1{/if}">
                        {if $tastes[0].count2 > 0}
                            {if $hour < 18}
                                {$delivery[0]}
                            {else}
                                {$delivery[1]}
                            {/if}
                        {else}
                            {if $hour < 18}
                                {$delivery[2]}
                            {else}
                                {$delivery[3]}
                            {/if}
                        {/if}
                        </p>
                    {else}
                        <a id="analog-button" class="btn">Смотреть аналоги</a>
                    {/if}
				</div>
			</form>
		</div>
	</div>
    {if count($otherFilling) > 0}
    <div class="baggers">
        <h4>Есть другие фасовки:</h4>
        <div class="jcarousel-container jcarousel-container-horizontal" style="position: relative; display: block;"><div class="jcarousel-clip jcarousel-clip-horizontal" style="position: relative;"><ul class="mycarousel jcarousel-list jcarousel-list-horizontal" style="overflow: hidden; position: relative; top: 0px; margin: 0px; padding: 0px; left: 0px; width: 680px;">
                    {foreach from=$otherFilling item=item}
                        <li class="jcarousel-item jcarousel-item-horizontal jcarousel-item-1 jcarousel-item-1-horizontal" jcarouselindex="1" style="float: left; list-style: none;">
                            <figure><a href="{$uri_base}{$plane_tree[$item.group_id].uri_base}/{$item.uri}{$url_suffix}"><img src="{$item.files._rows.image.preview_3}" alt="{$item.name}" title="{$item.name}"></a></figure>
                            <p><span class="gray">{$item.volume}</span></p>
                            {*{if $item.price > 0}*}
                            {if $item.price > 0 && ($item.availability > 0 || $item.availability2 > 0)}
                                <p class="nal">в наличии</p>
                            {else}
                                <p class="not-nal">нет в наличии</p>
                            {/if}
                        </li>
                    {/foreach}
                </ul></div><div class="jcarousel-prev jcarousel-prev-horizontal jcarousel-prev-disabled jcarousel-prev-disabled-horizontal" disabled="disabled" style="display: block;"></div><div class="jcarousel-next jcarousel-next-horizontal jcarousel-next-disabled jcarousel-next-disabled-horizontal" disabled="disabled" style="display: block;"></div></div>
    </div>
    {/if}
	<div class="main-choice">
		<ul class="tabs">
            <li id="analog-link" class="active">Aналогичные продукты</li>
			{*<li class="">Описание</li>*}
			<li class="">Отзывы ({$data.reviewsCount})</li>
		</ul>
		<div id="analog-data" class="box visible">
            {if count($seorelative.products)>0}
                <section class="catalog">
                    {foreach from=$seorelative.products item=item}
                        <article>
                            <header><a href="{$seorelative.catalog_uri_base}{$item.uri_base}{$url_suffix}">{$item.name}</a></header>
                            <figure><a href="{$seorelative.catalog_uri_base}{$item.uri_base}{$url_suffix}"><img src="/files/catalog/photo/{$item.files._rows.image.id}/1/{$item.uri}.{$item.files._rows.image.ext}" alt="{$item.name}" title="{$item.name}"></a></figure>
                            <p>{$item.manufacturer_name}</p>
                            <div class="txt-card">
                                <span class="gray">{$item.volume}</span>
                                {*{if $item.price > 0}*}
                                {if $item.price > 0 && ($item.availability > 0 || $item.availability2 > 0)}
                                    <p class="nal">в наличии</p>
                                {else}
                                    <p class="not-nal">нет в наличии</p>
                                {/if}
                                <p class="price"><span>{$item.price}</span> руб.</p>
                            </div>
                        </article>
                    {/foreach}
                </section>
            {/if}
		</div>
        {*<div class="box">*}
			{*<p>{$data.description}</p>*}
		{*</div>*}
		<div class="box">
            <!-- rewiev -->
            <div class="rewiev">
                {if !empty($data.reviews)}
                <ul>
                    {foreach from=$data.reviews item=item}
                    <li class="clearfix">
                        <div class="user">
                            <p>{$item.name}</p>
                            <figure>
                                <!-- IMG  -->
                            </figure>
                        </div>
                        <div class="txt-rew">
                            <p class="head-rew"><a href="#">Запись: {$data.name}</a></p>
                            <p>{$item.text}</p>
                        </div>
                    </li>
                    {/foreach}
                </ul>
                {/if}
                <div class="rewiev-mail clearfix">
                    <p>Ваш отзыв, вопрос или комментарий: *</p>
                    <form action="" method="post" id="captcha-form">
                        <input name="productId" type="hidden" value="{$data.id}">
                        <p><input type="text" name="name" placeholder="Ваше имя"></p>
                        <div class="l-rew">
                            <p><textarea name="text" placeholder="Напишите сообщение"></textarea></p>
                            <p><input type="submit" value="Отправить" class="btn"></p>
                        </div>
                        <div class="r-rew">
                            <p class="capcha">
                            {*<div class="cap-img">*}
                                <a  onclick="
                            document.getElementById('captcha').src='/cap/captcha.php?'+Math.random();
                            document.getElementById('captcha-form').focus();"
                                    id="change-image">
                                    <img src="/cap/captcha.php"  id="captcha" width="140" />
                                </a>
                                <a href="#" class="refr" onclick="
                            document.getElementById('captcha').src='/cap/captcha.php?'+Math.random();
                            document.getElementById('captcha-form').focus();" id="change-image"></a>
                            {*</div>*}
                            </p>
                            <p><input type="text" name="captcha" placeholder="Напишите текст с картинки"></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <p>
            {if empty($page)}
                {$data.description}
            {/if}
        </p>
	</div>
</div>
