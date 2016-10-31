<? if (empty($order) === false):?>
<table class="order-table">
    <tr>
        <th>артикул</th>
        <th>наименование</th>
        <th>цена</th>
        <th>вкус</th>
        <th>фасовка</th>
        <th>количество</th>
        <th></th>
    </tr>
    <? foreach($order as $product):?>
        <tr>
            <td><?= $product['article']?></td>
            <td><?= $product['name']?></td>
            <td><?= $product['price']?></td>
            <td><?= $product['taste']?></td>
            <td><?= $product['volume']?></td>
            <td> <a href="javascript: void(0);" onclick="subtract(<?= $product['article']?>)">-</a> <?= $product['sell']?> <a href="javascript: void(0);" onclick="up(<?= $product['article']?>)">+</a> </td>
            <td><a href="javascript: void(0);" onclick="removePosition(<?= $product['article']?>)">[убрать]</a></td>
        </tr>
    <? endforeach;?>
    <tr>
        <td colspan="7" style="text-align: center;"><input type="button" onclick="sendOrder()" value="Выполнить продажу" /></td>
    </tr>
</table>
<? endif;?>