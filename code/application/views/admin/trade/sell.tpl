{$main.footer}
<div class="order-view"></div>
<input type="hidden" name="order" id="order" />
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
        <td>цена</td>
        <td>количество</td>
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
            <td>{$item.price}</td>
            <td>{$item.count2}</td>
            <td>{if $item.count2 > 0} <a href="javascript: void(0);" class="add_article" data-id="{$item.article}">[в заказ]</a>{/if}</td>
        </tr>
    {/foreach}
    </tbody>
</table>
{literal}
    <script type="text/javascript">
        $('.add_article').click(
                function() {
                    var element = $(this);

                    $.ajax({
                        dataType: "json",
                        url: '/admin/trade/addSell',
                        type: 'post',
                        data: {id: element.attr("data-id"), order: $('#order').val()},
                        success: function(json){
                            if (json != null && json.order != null) {
                                $('.order-view').html(json.table);
                                $('#order').val(json.order);
                            }
                            if (json.error != null) {
                                alert(json.error);
                            }
                        }
                    });
                }
        );

        function subtract(article)
        {
            $.ajax({
                dataType: "json",
                url: '/admin/trade/subtract',
                type: 'post',
                data: {id: article, order: $('#order').val()},
                success: function(json){
                    if (json != null && json.order != null) {
                        $('.order-view').html(json.table);
                        $('#order').val(json.order);
                    }
                    if (json.error != null) {
                        alert(json.error);
                    }
                }
            });
        }

        function up(article)
        {
            $.ajax({
                dataType: "json",
                url: '/admin/trade/up',
                type: 'post',
                data: {id: article, order: $('#order').val()},
                success: function(json){
                    if (json != null && json.order != null) {
                        $('.order-view').html(json.table);
                        $('#order').val(json.order);
                    }
                    if (json.error != null) {
                        alert(json.error);
                    }
                }
            });
        }

        function removePosition(article)
        {
            $.ajax({
                dataType: "json",
                url: '/admin/trade/removePosition',
                type: 'post',
                data: {id: article, order: $('#order').val()},
                success: function(json){
                    if (json != null && json.order != null) {
                        $('.order-view').html(json.table);
                        $('#order').val(json.order);
                    }
                    if (json.error != null) {
                        alert(json.error);
                    }
                }
            });
        }

        function sendOrder()
        {
            $.ajax({
                dataType: "json",
                url: '/admin/trade/sendOrder',
                type: 'post',
                data: {order: $('#order').val()},
                success: function(json){
                    if (json != null && json.order != null) {
                        alert(json.order);
                        $('.order-view').html('');
                        $('#order').val('');
                    }
                    if (json.error != null) {
                        alert(json.error);
                    }
                }
            });
        }
    </script>
{/literal}