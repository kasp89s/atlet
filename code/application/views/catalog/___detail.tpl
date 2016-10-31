{getconfig path="core.url_suffix" assign="url_suffix"}
<div class="goods-gallery">
	<div class="goods-gallery-image-block">
		<a href="/cart/add?product_id={$data.id}&delayed=1" title="" class="add-to-favs"></a>
		<div class="goods-gallery-image">
			{if $files._rows.image.preview_2}
			<img src="/files/catalog/photo/{$files._rows.image.id}/0/{$data.uri}.{$files._rows.image.ext}" alt="" class="current" />
			{/if}
			{if count($files.photo) > 0}
          	{foreach from=$files.photo item=photoitem}
          		<img src="/files/catalog/photo/{$photoitem.id}/0/{$data.uri}.{$photoitem.ext}" alt="" />
			{/foreach}
			{/if}
		</div>
	</div>
	<ul class="goods-gallery-image-preview">
		{if $files._rows.image.preview_2}
		<li class="current"><div><img src="/files/catalog/photo/{$files._rows.image.id}/1/{$data.uri}.{$files._rows.image.ext}" alt="" /></div></li>
		{/if}
        {if count($files.photo) > 0}
      	{foreach from=$files.photo item=photoitem}
      		<li><div><img src="/files/catalog/photo/{$photoitem.id}/0/{$data.uri}.{$photoitem.ext}" alt="" /></div></li>
		{/foreach}
		{/if}
	</ul>
</div>
<div class="buy">
	<a href="/cart/add?product_id={$data.id}&delayed=1" title="" class="add-to-fav"><i></i>В избранное</a>
	<div class="buy-action">
		<span class="price">{if $data.price > 0}{$data.price|show_number} р.{else}Нет в наличии{/if}</span>
		{if $data.price > 0}<a href="/cart/add?product_id={$data.id}" title="" class="btn"><span><span>В корзину<i></i></span></span></a>{/if}
	</div>
</div>

<div class="about-goods">
	<div class="description">
		{$data.description}
	</div>
	<div class="goods-options">
		{if $data.manufacturer_name}
		<div class="manufacture">
			<div class="manufacture-logo">
				Производитель: <a class="manufacturer_name" href="/catalog/manufacturer/{$data.manufacturer_id}/">{$data.manufacturer_name}</a>
			</div>
		</div>
		{/if}
		<!--//<div class="socials">
			<img src="/content/social.jpg" alt="" />
		</div>//-->
		<ul class="options">
			<li>
				<i class="opt-freeshipping"></i>
				<p>Доставка по всей России</p>
			</li>
			<li>
				<i class="opt-nextday"></i>
				<p>Доставим быстро по Москве и области</p>
			</li>
			<li>
				<p><a href="http://intimel.ru/delivery" title="">Все способы доставки</a></p>
			</li>
			<li>
				<i class="opt-original"></i>
				<p>Только оригинальные товары.</p>
			</li>
			<li>
				<i class="opt-garantue"></i>
				<p>Гарантия защиты от подделок.</p>
			</li>
		</ul>
	</div>
</div>


{literal}
<script language="JavaScript">
	$("a[rel='lightbox']").colorbox({transition:"fade"});

	$('.addToCartLink').click(function(){
		$('#addToCartContainer').hide();
		$('#addToCartContainerWait').show();

		$.ajax({
		    url: $('.addToCartLink').attr('href')+'&ajax=true',
		    dataType: 'json',
		    beforeSend: function( xhr ) {

		    },
		    error:   function(){
		    	alert('При добавлении товара в корзину произошла ошибка связи.');
               	$('#addToCartContainer').show();
				$('#addToCartContainerWait').hide();
		    },

		    success: function( data ) {
				if(data.status=='ok'){
                	$('#addToCartContainerClicked').show();
                	$('#addToCartContainerWait').hide();

                	$('#cart_node').css('background', 'URL(/i/cart_full.jpg)');
                }else{
                	alert('При добавлении товара в корзину произошла ошибка связи.');
                	$('#addToCartContainer').show();
					$('#addToCartContainerWait').hide();
                }
		    }
		});

		return false;
	});
</script>
{/literal}

