{getconfig path="core.url_suffix" assign="url_suffix"}
<div class="flexslider slide-main">
	<ul class="slides">
		<li>
			<img src="/images/s3.jpg" alt="" />
			<article class="txt-slid">
				<header></header>
				<div class="txt-s"><p><br></p>
					
				</div>
			</article>
		</li>
		<!-- li>
			<img src="/images/s1.jpg" alt="" />
			<article class="txt-slid">
				<header>Акция! До 15 ноября<br />скидка 5%<br />на всё!</header>
				<div class="txt-s"><p>Хочешь быть сильным и здоровым?<br>
Интернет магазин «Атлеты» предлагает широкий ассортимент продуктов спортивного питания и консультацию профессиональных тренеров.</p>
					<p class="txt-right"><a href="http://atlets.ru/discount" class="btn">СМОТРЕТЬ</a></p>
				</div>
			</article>
		</li -->
	</ul>
</div>
<!--<div class="bl-butt">
	<a href="#" class="button">Увеличить мышечную массу</a>
	<a href="#" class="button bg-orange">Становись сильнее</a>
	<a href="#" class="button bg-bordo">похудей и будь в форме</a>
	<a href="#" class="button light-green">энергия и восстановление</a>
	<a href="#" class="button bg-green">специально для женщин</a>
</div>   -->
<h1>ЛИДЕРЫ ПРОДАЖ</h1>
<section class="catalog">
	{counter name="cntIterations2" start=0 skip=1 print=false assign="cntIterations2"}
	{foreach from=$groups item=item}
	{counter name="cntIterations2"}
		{foreach from=$item.elements item=product}
			{if $product.price > 0}
		{*<article>*}
			{*<header><a href="{$catalog_uri_base}{$plainTree[$product.group_id].uri_base}/{$product.uri}{$url_suffix}">{$product.name|truncate:26:"..."}</a></header>*}
			{*<figure><a href="{$catalog_uri_base}{$plainTree[$product.group_id].uri_base}/{$product.uri}{$url_suffix}"><img src="{$elementsFiles[$product.id]._rows.image.preview_2}" alt="" /></a></figure>*}
			{*<p class="price"><span>{$product.price|show_number}</span> руб.</p>*}
			{*<a href="{$catalog_uri_base}{$plainTree[$product.group_id].uri_base}/{$product.uri}{$url_suffix}" class="btn">Подробнее</a>*}
		{*</article>*}
            <article>
                <header><a href="{$catalog_uri_base}{$plainTree[$product.group_id].uri_base}/{$product.uri}{$url_suffix}">{$product.name|truncate:26:"..."}</a></header>
                <figure><a href="{$catalog_uri_base}{$plainTree[$product.group_id].uri_base}/{$product.uri}{$url_suffix}"><img src="{$elementsFiles[$product.id]._rows.image.preview_1}" alt="{$product.name|truncate:26:"..."}" alt="{$product.name|truncate:26:"..."}"  title="{$product.name|truncate:26:"..."}"></a></figure>
                <p>{$product.manufacturer_name}</p>
                <div class="txt-card">
                    <span class="gray">{$product.volume}</span>
                    {if $product.price > 0 && ($product.availability > 0 || $product.availability2 > 0)}
                        <p class="nal">в наличии</p>
                        <p class="price">
                        <span>
                            {if $product.availability2 > 0}
                                {$product.price}
                            {elseif $product.availability > 0}
                                {$product.priceSupplier}
                            {/if}
                        </span> руб.</p>
                    {else}
                        <p class="not-nal">нет в наличии</p>
                        <p class="price">
                        <span>
                            {if $product.price > 0}
                                {$product.price}
                            {elseif $product.priceSupplier > 0}
                                {$product.priceSupplier}
                            {/if}
                        </span> руб.</p>
                    {/if}
                </div>
            </article>
			{/if}
		{/foreach}
	{/foreach}
</section>
<blockquote class="anons">
	<p>Хотите получать от тренировок максимальную отдачу? Увеличить показатели набора мышечной массы, сжигания жира? Питаться полноценно и с минимумом затрат? В интернет-магазине «Атлеты» вы сможете найти спортивное питание для достижения любой цели. В ассортименте широкий выбор препаратов и пищевых добавок для занятий различными видами спорта.<br><br>
С помощью специализированного питания, каждый может поддерживать оптимальный баланс питательных веществ в рационе, добиваясь максимальных результатов. Помимо решения конкретных задач, продукты, представленные в каталоге, помогают повысить общее состояние здоровья и улучшить самочувствие. Мы предлагаем спортпит от надежных брендов: гейнеры, протеины, аминокислоты, глютамин, креатин, ZMA и другие продукты для тех, кто заботится о сохранении здоровья, красоты, тонуса организма. Все товары сертифицированы. У нас вы найдете полезные аксессуары для занятий спортом и профессиональную литературу. 
Интернет-магазин специализированного питания для спортсменов  и любителей активного образа жизни «Атлеты» осуществляет оперативную доставку заказов в Москве и по всем городам России. Для каждого покупателя предусмотрены удобные условия доставки и специальные предложения. Мы заботимся обо всех клиентах, поэтому при высоком уровне качества стремимся поддерживать приятные для покупателей цены и регулярно обновляем складские запасы, чтобы вы могли получить нужный товар вовремя.<br><br> 
Подробная информация в карточке товара поможет вам выбрать функциональное спортивное питание, которое идеально впишется в ваш режим тренировок и обеспечит необходимый результат. Команда профессиональных консультантов по здоровому питанию в режиме онлайн ответит на ваши вопросы о тренировочных программах и рационе.<br><br>
Все продукты, представленные в нашем каталоге, не являются лекарственными средствами.</p>
</blockquote>
<section class="brand">
	<header>МЫ РАБОТАЕМ С ТАКИМИ БРЕНДАМИ</header>
	<img src="/images/on.jpg" alt="" />
	<img src="/images/bsn.jpg" alt="" />
	<img src="/images/universal.jpg" alt="" />
	<img src="/images/multipower.jpg" alt="" />
	<img src="/images/wieder.jpg" alt="" />
	<img src="/images/ultimate-nutrition.jpg" alt="" />
	<img src="/images/canparl-nutrition.jpg" alt="" />
	<img src="/images/nutrex-research.jpg" alt="" />
	<img src="/images/mutant.jpg" alt="" />
	<img src="/images/olimp.jpg" alt="" />
	<img src="/images/mhp.jpg" alt="" />
	<img src="/images/suntrax.jpg" alt="" />
	<img src="/images/muscletech.jpg" alt="" />
	<img src="/images/dumatyze.jpg" alt="" />
	<img src="/images/twinlab.jpg" alt="" />
</section>
