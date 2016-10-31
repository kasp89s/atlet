<br /><br /><br /><br /><br /><br />
<div style="text-align: center; vertical-align: base-line;">
	<a href="/bankkarti.html"><img src="/i/visa.png" height="30px" style="margin-left: 15px; margin-right: 15px; " alt="Visa" border="0"></a>
	<a href="/alfa_bank_discount.html"><img src="/i/alfabank.png" style="margin-left: 15px; margin-right: 15px;" height="50px" alt="Альфабанк" border="0"></a>
	<a href="/russkii_standart_discount.html"><img src="/i/russky_standart_bank.png" style="margin-left: 15px; margin-right: 15px;" height="50px" alt="Русский стандарт" border="0"></a>
	<a href="/bankkarti.html"><img src="/i/master_card.png" style="margin-left: 15px; margin-right: 15px;" height="40px" alt="MasterCard" border="0"></a>
</div>
<div class="addlinks">
	<br />
	{foreach from=$data.rows item=item name=linksLoop}
	<a href="{$item.url}">{$item.name}</a>{if $smarty.foreach.linksLoop.last}. {else}, {/if}
	{/foreach}
</div>