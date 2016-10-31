<form method="POST">
	<div class="box">
		<h3>SEO</h3>
		<div class="inside">
			<p>
				<label for="title">Title (заголовок)*</label>
				<input type="text" id="title" name="title" class="{$data.err_title}" value="{$data.title}"/>
				<br><span class="field_comment">Максимальная длина: 100 символов</span>
				{if $data.err_title_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_title_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>
			
			<p>
				<label for="keywords">Keywords (ключевые слова)*</label>
				<input type="text" id="keywords" name="keywords" class="{$data.err_keywords}" value="{$data.keywords}"/>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_keywords_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_keywords_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>
			
			<p>
				<label for="description">Description (описание)*</label>
				<input type="text" id="description" name="description" class="{$data.err_description}" value="{$data.description}"/>
				<br><span class="field_comment">Максимальная длина: 200 символов</span>
				{if $data.err_description_required}<br><span class="error">Обязательное поле не задано</span>{/if}
				{if $data.err_description_length}<br><span class="error">Превышена максимальная длина поля</span>{/if}
			</p>
		</div>
	</div>
	
	<p>
		<input type="submit" id="submit" name="submit" value="Сохранить"/>
	</p>
</form>