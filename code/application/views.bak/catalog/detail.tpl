{getconfig path="core.url_suffix" assign="url_suffix"}
{if $data.id}
	<td valign="top">
  <br>
  <table width="100%" align="center">
    <tbody>
      <tr>
        <td style="vertical-align: middle; ">
          {if $files._rows.image.preview_2}
          	<a title="{$data.name}" href="/files/catalog/photo/{$files._rows.image.id}/0/{$data.uri}.{$files._rows.image.ext}" rel="lightbox"><img
          	 {if $data.use_h1 > 0}alt="{if $data.concat_with_section_title}{$data.name} {$data.seo_name}{else}{$data.name}{/if}"{/if}
		     {if $data.use_h1 > 0}title="{if $data.concat_with_section_title}{$data.name} {$data.seo_name}{else}{$data.name}{/if}"{/if}
          	 src="/files/catalog/photo/{$files._rows.image.id}/1/{$data.uri}.{$files._rows.image.ext}" style="border: none; float: left;" class="photo">
          	</a>
	 	  {/if}
        </td>
        <td style="width:150px;">
        </td>
        <td style="width:100%; text-align: left;">
          <table cellspacing="5" style="margin-left: 170px;">
            <tbody>
              <tr>
              	{if count($files.photo) > 0}
              		{counter name="cntIterations" start=0 skip=1 print=false assign="cntIterations"}
		          	{foreach from=$files.photo item=photoitem}
		          		{counter name="cntIterations"}
		          		<td style="border: 1px solid black;text-align:center;width:100px" >
		                  <a title="{$data.name} - {$cntIterations}" href="/files/catalog/photo/{$photoitem.id}/0/{$data.uri}.{$photoitem.ext}" rel="lightbox">
		                  		<img style="border:none;" src="/files/catalog/photo/{$photoitem.id}/3/{$data.uri}.{$photoitem.ext}"
		                  			{if $data.use_h1 > 0}alt="{if $data.concat_with_section_title}{$data.name} {$data.seo_name}{else}{$data.name}{/if} - {$cntIterations}"{/if}
		     						{if $data.use_h1 > 0}title="{if $data.concat_with_section_title}{$data.name} {$data.seo_name}{else}{$data.name}{/if} - {$cntIterations}"{/if}>
		                  </a>
		                </td>
		                {if $cntIterations%2 == 0}
							</tr><tr>
						{/if}
		          	{/foreach}
		          	{if $cntIterations%2 == 1}
						<td>
							<!--Пусто-->
						</td>
					{/if}
			 	{/if}
			   </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
  <br />
  <table>
  	  {if $data.in_action && $data.oldprice}
  	  <tr>
  	  	  <td>
  	  	  	  &nbsp;
  	  	  </td>
  	  	  <td>
  	  	  	  &nbsp;
  	  	  </td>
  	  	  <td >
			  <font class='oldprice' style="padding-left:0;">{$data.oldprice|show_number} руб.</font>
  	  	  </td>
  	  </tr>
  	  {/if}
  	  <tr>
  	  	  <td>
  	  	  	  <font class="ctext">Артикул:</font>
			  <font color="red">
			    <b>{$data.code}</b>
			  </font>
  	  	  </td>
  	  	  <td>
  	  	  	  <font class="ctext">Цена: </font>
  	  	  </td>
  	  	  <td>
  	  	  	  <font color="red">
			    <b>{$data.price|show_number}</b>
			  </font>
			  <font class="ctext"> руб.</font>
  	  	  </td>
  	  </tr>
  </table>




  <br /><br />
  <table>
  	  <tr>
  	  	  <td>
			  <script src="http://connect.facebook.net/ru_RU/all.js#xfbml=1"></script>
			  <fb:like href="{$current_link}" show_faces="false" width="450" font="verdana"></fb:like>
			  <br /><br />
			  <div class="socialicons">
				  <noindex>
				    <a href="http://twitter.com/share?text={$current_text}&url={$current_link}" rel="nofollow" target="_blank">
				      <img src="/i/twitter-32.png" title="Поделиться в Твиттере" width="32" height="32">
				    </a>
				  </noindex>
				  <noindex>
				    <a href="http://vkontakte.ru/share.php?url={$current_link}" rel="nofollow" target="_blank">
				      <img src="/i/vkontakte-32.png" title="Поделиться ВКонтакте" width="32" height="32">
				    </a>
				  </noindex>
				  <noindex>
				    <a href="http://www.facebook.com/sharer.php?u={$current_link}" rel="nofollow" target="_blank">
				      <img src="/i/facebook-32.png" title="Поделиться в Facebook" width="32" height="32">
				    </a>
				  </noindex>
				  <noindex>
				    <a href="http://www.google.com/reader/link?url={$current_link}&title={$current_text}&srcURL=http://luxpodarki.ru" rel="nofollow" target="_blank">
				      <img src="/i/google-buzz-32.png" title="Поделиться в Google Buzz" width="32" height="32">
				    </a>
				  </noindex>
				  <noindex>
				    <a href="http://connect.mail.ru/share?share_url={$current_link}" rel="nofollow" target="_blank">
				      <img src="/i/mail-32.png" title="Поделиться во Mail" width="32" height="32">
				    </a>
				  </noindex>
				  <noindex>
				    <a href="http://www.livejournal.com/update.bml?event={$current_link}&subject={$current_text}" rel="nofollow" target="_blank">
				      <img src="/i/livejournal-32.png" title="Поделиться в Livejournal" width="32" height="32">
				    </a>
				  </noindex>
			  </div>
		  </td>
		  {if $data.in_action}
		  <td style="text-align: center; width: 200px;">
		  	  <a href="{$action_uribase}{$url_suffix}" target="_blank"><img src="/i/action_inner.png" alt="Акция!!!" border="0"></a>
		  </td>
		  {/if}
		  <td style="text-align: center;">
		  	  {if not $data.in_cart}
		 	  	  <span id="addToCartContainer">
		 	  	  	  <a class="addToCartLink" href="/cart/add?product_id={$data.id}"><img src="/i/basket.png" alt="Добавить в корзину" border="0"></a><br />
	          	  	  <a class="addToCartLink page" style="font-weight: 100;" href="/cart/add?product_id={$data.id}">Добавить в корзину</a>
	          	  </span>
	          	  <span id="addToCartContainerClicked" style='display:none;'>
	          	  	  <a href="/cart" class="page"><img src="/i/basket.png" alt="В корзину" border="0"></a><br />
	          	  	  Товар успешно добавлен <a href="/cart" style="font-weight: 100;" class="page">в корзину</a>
	          	  </span>
	          	  <span id="addToCartContainerWait" style='display:none;'>
	          	  	  Пожалуйста подождите...
	          	  </span>
	          {else}
	          	  <span id="addToCartContainerClicked">
	          	  	  <a href="/cart"><img src="/i/basket.png" alt="Добавить в корзину" border="0"></a><br />
	          	  	  Товар уже в корзине. <a href="/cart" style="font-weight: 100;" class="page">Перейти в корзину</a>
	          	  </span>
	          {/if}
		  </td>
	  </tr>
  </table>
  <br />
  <font class="btext">
    {$data.description}
    <br>
    <div align="right">
      <a href="javascript:history.go(-1);" class="page">Назад</a>
    </div>
  </font>
</td>
{else}
	<h3 class="centered">Не удалось найти запрашиваемый товар</h3>
{/if}


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
		    error:   function(){		    	alert('При добавлении товара в корзину произошла ошибка связи.');
               	$('#addToCartContainer').show();
				$('#addToCartContainerWait').hide();
		    },

		    success: function( data ) {				if(data.status=='ok'){
                	$('#addToCartContainerClicked').show();
                	$('#addToCartContainerWait').hide();

                	$('#cart_node').css('background', 'URL(/i/cart_full.jpg)');
                }else{
                	alert('При добавлении товара в корзину произошла ошибка связи.');
                	$('#addToCartContainer').show();
					$('#addToCartContainerWait').hide();                }
		    }
		});

		return false;	});
</script>
{/literal}

