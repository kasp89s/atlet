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
        <td>количество</td>
        <td>ред.</td>
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
            <td><span id="count-{$item.article}">{$item.count2}</span></td>
            <td><input type="text" id="{$item.article}" data-id="{$item.id}" /></td>
            <td><a href="javascript: void(0);" onclick="uncoming({$item.article}, 'up')">[добавить]</a> / <a href="javascript: void(0);" onclick="uncoming({$item.article}, 'subtract')">[удалить]</a></td>
        </tr>
    {/foreach}
    </tbody>
</table>
{literal}
    <script type="text/javascript">
        function uncoming(article, action)
        {
            var input = $('#' + article);
            if (input.val() == '') return;

            $.ajax({
                dataType: "json",
                url: '/admin/trade/uncoming',
                type: 'post',
                data: {article: article, id: input.attr('data-id'), value: input.val(), action: action},
                success: function(json){
                    if (json != null && json.product != null) {
                        input.val('');
                        $('#count-' + article).text(json.product.count2);
                    }
                    if (json.error != null) {
                        alert(json.error);
                    }
                }
            });
        }

        </script>
{/literal}