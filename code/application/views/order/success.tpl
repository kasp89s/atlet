{getconfig path="core.url_suffix" assign="url_suffix"}

{getconfig path="core.url_suffix" assign="url_suffix"}
{if count($data.rows) > 0}
<section class="page-basket">
	<br />
	<div class="order-product">
			<p class="orange">ОФОРМЛЕНИЕ  ЗАКАЗА</p>
			Заказ успешно оформлен. Наш менеджер свяжется с Вами в самое ближайшее время.
	</div>

</section>
<div class="clear"></div>
{else}
	{$page.description}
{/if}


