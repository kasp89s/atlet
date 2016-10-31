<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <title>{$title}</title>
    <meta name="keywords" content="{$keywords}" />
    <meta name="description" content="{$description}" />
    <link rel="stylesheet" href="/css/style.css" type="text/css" />
	<link rel="stylesheet" href="/libs/fancybox/jquery.fancybox.css" type="text/css" />
	<!--[if IE]><link rel="stylesheet" type="text/css" href="/css/al-ie.css"><![endif]-->
    <!--[if lt IE 9]><link rel="stylesheet" type="text/css" href="/css/ie.css"><![endif]-->
    <!--[if lt IE 9]><script type="text/javascript" src="/js/pie.js"></script><![endif]-->
    <script src="/js/jquery-1.8.3.min.js"></script>
	<script src="/js/jquery.flexslider.min.js"></script>
	<script src="/js/jquery.selectBox.min.js"></script>
	<script src="/libs/fancybox/jquery.fancybox.js"></script>
    <script src="/js/jquery.jcarousel.min.js"></script>
    <script src="/js/main.js"></script>
    <link rel="icon" href="http://atlets.ru/favicon.ico" type="image/x-icon">
</head>
<body>
{literal}
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter26023194 = new Ya.Metrika({id:26023194,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/26023194" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
{/literal}
<div class="page">
	<div class="main-container">
	    <header class="header clearfix" role="banner">
	        <div class="logo">
				<a href="/"><img src="/images/logo.png" alt="" /><span>Интернет-магазин спортивного питания</span></a>
			</div>
			<div class="r-header">
				<address>
					<p>Наш телефон в Москве:<strong>+7 (495) 648-60-15</strong><font color=green>Ежедневно с 10:00 до 21:00</font></p>
				</address>
				<div class="consultant">
					<br>
					<a href="/callback" class="btn bel" data-reveal-id="myModal" data-animation="fade">Заказать звонок</a>
				</div>
				<div class="basket">
					<a href="/cart" class="bsk">Моя корзина</a>
					{if $cart_count > 0}
						<p id="normalCart">В корзине <span class="prodCount">{$cart_count}</span> товара(ов)</p>
						<p id="emptyCart" style="display: none">В корзине нет товаров</p>
					{else}
						<p id="normalCart" style="display: none">В корзине <span class="prodCount">{$cart_count}</span> товара(ов)</p>
						<p id="emptyCart">В корзине нет товаров</p>
					{/if}
				</div>
			</div>
	    </header><!--.header-->
		<div class="top-menu">
            <div class="c-container clearfix">
			<menu role="navigation">
				<li class="active"><a href="#">Главная</a></li>
				<li><a href="/delivery">Доставка и оплата</a></li>
				<li><a href="/discount">Скидки и акции</a></li>
				<li><a href="/faq">Вопрос-ответ</a></li>
				<li><a href="/opt">Оптовикам</a></li>
				{*<li><a href="/enc">Энциклопедия</a></li>*}
				<li><a href="/contacts">Контакты</a></li>
			</menu>
            <div class="search">
                <form action="/catalog/search">
                    <input type="text" name="search_words" placeholder="поиск">
                    <input type="submit" value="ok">
                </form>
            </div>
            </div>
		</div>
	    <div class="middle">
	        <div class="container">
	            <main class="content" role="main">
                    {$content}
	            </main><!--.content-->
	        </div><!--.container-->
	        <aside class="left-sidebar">
				{if $leftmenu}
					{$leftmenu}
				{/if}
				<div class="help">
					<header><span class="icon hlp"></span> ПОПУЛЯРНЫЕ ВОПРОСЫ</header>
					<p><a href="http://atlets.ru/faq">Существует мнение, что аргинин и орнитин улучшают секрецию гормонов роста. Так ли это?</a></p>
					<p><a href="http://atlets.ru/faq">Рекомендуете ли вы какие-либо добавки для улучшения восстановления физической активности?</a></p>
				</div>
				<div class="network">
					
				</div>
	        </aside><!--.left-sidebar -->
	    </div><!-- /middle-->
	</div>
	<span class="bg-white"></span>
</div><!-- /page -->
	<footer class="footer" role="contentinfo">
		<div class="c-footer clearfix">
			<div class="l-footer">
				<h6>Спортивное питание</h6>
				<ul>
					<li><a href="http://atlets.ru/catalog/proteiny">Протеины</a></li>
					<li><a href="http://atlets.ru/catalog/aminokislotnye_kompleksy">Аминокислоты</a></li>
					<li><a href="http://atlets.ru/catalog/kreatin_monogidrat">Креатин и предтреники</a></li>
					<li><a href="http://atlets.ru/catalog/geynery">Гейнеры</a></li>
					<li><a href="http://atlets.ru/catalog/bcaa">ВСАА</a></li>
					<li><a href="http://atlets.ru/catalog/szhigateli_zhira">Жиросжигатели</a></li>
				</ul>
			</div>
			<div class="r-footer">
				<p>Мы в соцсетях</p>
				<p><a href="#" class="icon tvt"></a> <a href="#" class="icon vk"></a> <a href="#" class="icon ff"></a> <a href="#" class="icon ok"></a></p>
				<p>Спортивное питание - АТЛЕТЫ &copy; 2014</p>
			</div>
			<div class="midlle-footer">
				<p>Мы принимаем такие платежные системы</p>
				<p>
					<img src="/images/visa.png" alt="" />
					<img src="/images/m-card.png" alt="" />
					<img src="/images/maestro.png" alt="" />
					<img src="/images/sb-bank.png" alt="" />
					<img src="/images/yd.png" alt="" />
					<img src="/images/qiwi.png" alt="" />
				</p>
			</div>
		</div>
	</footer><!--.footer -->
<div id="myModal" class="reveal-modal bell">
	<header>Заказ обратного звонка</header>
		<form action="/callback">
			<p><input type="text" name='author' placeholder="Ваше имя" /></p>
			<p><input type="text" name='phone' placeholder="Ваш телефон" /></p>
			<p class="txt-center"><input type="submit" value="Заказать звонок" class="btn bel" /></p>
		</form>
		<a href="#" class="btn-close-modal"></a>
</div>
<div id="myModal-1" class="reveal-modal pop-backet">
    <header>Товар добавлен в корзину</header>
    <p>
        <a href="/cart" class="btn bel">Перейти в корзину</a>
        <a href="#" class="btn bel btn-close-modal">Продолжить покупки</a>
    </p>
    <a href="#" class="btn-close-modal"></a>
</div>
<a href="javascript:void(0)" class="btn bel" id="open-myModal-1" data-reveal-id="myModal-1" data-animation="fade"></a>
</body>
</html>
