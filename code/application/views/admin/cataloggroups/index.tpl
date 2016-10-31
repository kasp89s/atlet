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
		$.post('/admin/cataloggroups/save_tree', '&tree='+$("ul.sortable").tree('serialize'), function(data, status) {
			window.location.reload();
		});
	});

});
</script>
{/literal}

<p>

Иерархию страниц можно менять, перемещая отдельные страницы, нажмите на [Move].
Первая страница <strong>должна</strong> содержать все остальные страницы.
Она является также главной страницей. Структура <strong>каталога товаров</strong> на вашем сайте зависит от порядка и иерархию страниц.
</p>

<div class="box">
	<h3>Группы товаров</h3>
	<div class="inside">
		<ul class="sortable">
		{php}
		$level = 0;
		$groups = $this->get_template_vars('groups');
		foreach ($groups as $node){
			$has_children = (($node['rgt'] - $node['lft'] - 1) > 0 );
			$id = 'node-'.$node['id'];

			$add = '';
			if (Acl::instance()->is_allowed('catalog_add'))
				$add = "<a href='/admin/catalog/edit?group_id=$node[id]'>[Add]</a>";
			else
				$add = "&nbsp;";

			$edit = '';
			if (Acl::instance()->is_allowed('catalog_edit'))
				$edit = "<a href='/admin/cataloggroups/edit?id=$node[id]'><span>$node[title]</span></a>";
			else
				$edit = "<span>$node[title]</span>";

			$del = '';
			if (Acl::instance()->is_allowed('catalog_del') && $node['level'] != 0)
				$del = "<a href='/admin/cataloggroups/delete?id=$node[id]' class='confirm'><img src='/i/admin/delete.png' alt='Delete Page' title='Delete Page'></a>";
			else
				$del = "&nbsp;";

			$command = "";
			if($node['level'] != 0)
				$command = "<a href='/admin/catalog?group_id=$node[id]'>[List]</a>&nbsp;&nbsp;$add&nbsp;&nbsp;";


			$value = "
				<div class='folder'>
					<div style='float: left;'>
						<span class='movehandle'>[Move]</span>
						$edit
					</div>
					<div class='delete' style='position:absolute;right:10px;'>
						$command
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
{if 'catalog_edit'|acl_is_allowed}<button id="save_sort">Сохранить</button>{/if}
</p>
