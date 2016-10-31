<div class="popup-content">
	<form action='' method='post' id="mainCallbackForm">
		<div class="form-line {if $data.err_author_required}error{/if}" style="clear:none;">
			<label class="label">Контактное лицо:</label>
			<input type='text' name='author' size='40' value="{$data.author}">
		</div>

		<div class="form-line {if $data.err_phone_required or $data.err_phone_length}error{/if}" style="clear:none;">
			<label class="label">Телефон:</label>
			<input type='text' name='phone' size='40' value="{$data.phone}">
		</div>

		<div class="form-line submit" style="clear:none;">
			<a href="/callback" title="" class="btn-s niceButton" parentForm="mainCallbackForm"><span><span>ОК<i></i></span></span></a>
			<!--//<div class="error-messages">
				<p>Ошибка!</p>
				<p>Заполнены не все поля</p>
			</div>//-->
		</div>
	    <input type='hidden' name='act' value='send_form'>
    </form>
</div>