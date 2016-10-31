<?php
/**
 * Модель данных. Организации
 *
 */
class AclOrder_Model extends Model {

    public $table_name = 'acl_order';

    public static function calculateTotalPrice($order)
    {
        $totalPrice = 0;
        foreach ($order as $article => $product) {
            $totalPrice+= (float)($product['sell'] * $product['price']);
        }

        return $totalPrice;
    }
}
