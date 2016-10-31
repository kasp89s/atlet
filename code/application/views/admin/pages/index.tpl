{literal}
<script type="text/javascript">
$(document).ready(function(){

	$("ul.sortable").tree({
		sortOn: "li",
		dropOn: ".folder",
		dropHoverClass: "hover",
		handle: ".movehandle"
	});

	$('#save_sort').click(function(){
		$.post('/admin/pages/save_tree', '&tree='+$("ul.sortable").tree('serialize'), function(data, status) {
			window.location.reload();
		});
	});

});
</script>
{/literal}

<p>
Система управления содержимым.
<br />
Иерархию страниц можно менять, перемещая отдельные страницы, нажмите на [Move].
Первая страница <strong>должна</strong> содержать все остальные страницы.
Она является также главной страницей (1-й уровень). <strong>Меню</strong> на вашем сайте зависит от порядка и иерархию страниц.
Добавлять 5-й и большие уровени запрещено.
<br />
Если рядом с именем страницы отображается символом <img src="/i/admin/warning.png">,
то существуют страницы с такой же ссылкой (URI), что может привести в конфликту маршрутизации.
</p>

<div class="box">
	<h3>Page Order</h3>
	<div class="inside">
		<ul class="sortable">
		{php}
		$level = 0;
		$pages = $this->get_template_vars('pages');
		foreach ($pages as $node){
			$has_children = (($node['rgt'] - $node['lft'] - 1) > 0 );
			$id = 'page-'.$node['id'];

			$warn = '';
			if ($node['warn_duplicate'])
				$warn = '<img src="/i/admin/warning.png">';

			$edit = '';
			if (Acl::instance()->is_allowed('cms_edit'))
				$edit = "<a href='/admin/pages/edit?id=$node[id]'><span>$node[title]</span></a>";
			else
				$edit = "<span>$node[title]</span>";

			$del = '';
			if (Acl::instance()->is_allowed('cms_del'))
				$del = "<a href='/admin/pages/delete?id=$node[id]' class='confirm'><img src='/i/admin/delete.png' alt='Delete Page' title='Delete Page'></a>";
			else
				$del = "&nbsp;";

			$value = "
				<div class='folder'>
					<div style='float: left;'>
						<span class='movehandle'>[Move]</span>
						$edit
						$warn
					</div>
					<div class='delete' style='float: right;margin:0 10px 0 10px;padding-top:4px;'>
						$del
			    	</div>
				</div>";


			if($has_children) {
				if($level > $node['level']) {
					echo str_repeat("</ul></li>\n",($level - $node['level']));
					echo '<li id="'.$id.'">'.$value."\n";
					echo '<ul>'."\n";
				} else {
					echo '<li id="'.$id.'">'.$value."\n";
					echo '<ul>'."\n";
				}
			} elseif ($level > $node['level']) {
				echo str_repeat("</ul></li>\n",($level - $node['level']));
				echo '<li id="'.$id.'">'.$value.'</li>'."\n";
			} else {
				echo '<li id="'.$id.'">'.$value.'</li>'."\n";
			}
			$level = $node['level'];
		}
		echo str_repeat("</ul></li>\n",$level);
		{/php}
		</ul>
	</div>
</div>
<p>
{if 'cms_edit'|acl_is_allowed}<button id="save_sort">Сохранить</button>{/if}
</p>
