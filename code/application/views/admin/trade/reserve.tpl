{$main.footer}
<form action="" method="POST">
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td><input type="text" name="article" placeholder="артикул" style="width: 200px" /></td>
            <td><input type="text" name="name" placeholder="название" style="width: 360px" /></td>
            <td><input type="submit" value="Найти"></td>
        </tr>
    </table>
</form>
<table cellspacing="0" cellpadding="0" class="table">
	<thead align="left" valign="middle">
		<tr>
            <td width="1%">№</td>
            <td>артикул</td>
            <td>наименование</td>
            <td>фасовка</td>
            <td>вкус</td>
            <td>склад</td>
            <td>в резерв</td>
            <td>дилительность</td>
            <td></td>
		</tr>
	</thead>
	<tbody align="left" valign="middle">

    {foreach from=$products item=item}
        <tr>
            <td></td>
            <td>{$item.article}</td>
            <td>{$item.name}</td>
            <td>{$item.volume}</td>
            <td>{$item.taste}</td>
            <td><span id="warehouse-{$item.article}">{$item.count2}</span></td>
            <td>
                {if ($item.count2 > 0 && $item.reserveCount == 0)}
                    <input type="text" id="count-{$item.article}" />
                {/if}
                {if $item.reserveCount != 0}
                    <span id="count-{$item.article}">{$item.reserveCount}</span>
                {/if}
            </td>
            <td>
                {if ($item.count2 > 0 && $item.reserveCount == 0)}
                <select id="date-{$item.article}">
                    <option value="1">1 день</option>
                    <option value="2">2 дня</option>
                </select>
                {/if}
                {if $item.reserveCount != 0}
                    <span id="date-{$item.article}">{$item.reserveDate}</span>
                {/if}
            </td>
            <td>
                {if $item.count2 > 0 && $item.reserveCount == 0}
                    <a href="javascript: void(0);" id="reserve-link-{$item.article}" onclick="reserve({$item.article})">[добавить]</a>
                {/if}
                {if $item.reserveCount != 0}
                    <a href="javascript: void(0);" id="sell-link-{$item.article}" onclick="sell({$item.article})">[продано]</a>
                {/if}
            </td>
        </tr>
    {/foreach}
	</tbody>
</table>
{literal}
    <script type="text/javascript">
        function reserve(article) {
            var count = $('#count-' + article).val();
            var date = $('#date-' + article).val();
            if (count == '') return;

            $.ajax({
                dataType: "json",
                url: '/admin/trade/reserved',
                type: 'post',
                data: {article: article, count: count, date: date},
                success: function (json) {
                    if (json != null && json.reserved != null) {
                        $('#count-' + article).replaceWith('<span id="count-' + article + '">' + json.reserveCount + '</span>');
                        $('#date-' + article).replaceWith('<span id="date-' + article + '">' + json.reserveDate + '</span>');
                        $('#reserve-link-' + article).replaceWith('<a href="javascript: void(0);" id="sell-link-' + article + '" onclick="sell(' + article + ')">[продано]</a>');
                    }
                    if (json.error != null) {
                        alert(json.error);
                    }
                }
            });
        }

        function sell(article)
        {
            $.ajax({
                dataType: "json",
                url: '/admin/trade/makesell',
                type: 'post',
                data: {article: article},
                success: function(json){
                    if (json != null && json.sell != null) {
                        $('#warehouse-' + article).text(json.count);
                        $('#count-' + article).replaceWith('<input type="text" id="count-' + article + '" />');
                        $('#date-' + article).replaceWith('<select id="date-' + article + '">' +
                        '<option value="1">1 день</option>' +
                        '<option value="2">2 дня</option>' +
                        '</select>');
                        $('#sell-link-' + article).replaceWith('<a href="javascript: void(0);" id="reserve-link-' + article + '" onclick="reserve(' + article + ')">[добавить]</a>');

                    }
                    if (json.error != null) {
                        alert(json.error);
                    }
                }
            });
        }

    </script>
{/literal}