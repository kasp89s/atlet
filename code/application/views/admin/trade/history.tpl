{$main.footer}
<table cellspacing="0" cellpadding="0" class="table">
    <thead align="left" valign="middle">
    <tr>
        <td width="1%">№</td>
        <td>Заказ</td>
        <td>Сумма</td>
        <td>Дата</td>
    </tr>
    </thead>
    <tbody align="left" valign="middle">

    {foreach from=$items item=item}
        <tr>
            <td>{$item.id}</td>
            <td>
                <table>
                    {foreach from=$item.order item=item2}
                        <tr>
                            <td>{$item2.article}</td>
                            <td>{$item2.name}</td>
                            <td>{$item2.price}</td>
                            <td>{$item2.taste}</td>
                            <td>{$item2.volume}</td>
                            <td>{$item2.sell} шт</td>
                        </tr>
                    {/foreach}
                </table>
            </td>
            <td>{$item.price}</td>
            <td>{$item.date}</td>
        </tr>
    {/foreach}
    </tbody>
</table>

