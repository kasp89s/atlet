<?php
/**
 * Каталог товаров
 *
 */
class Trade_Controller extends Admin_Controller
{

    public function __construct()
    {
        $this->title = 'Действия продавца';

        $this->menu = array(
            array('url' => '/admin/trade/search', 'section' => 'Поиск', 'title' => 'Поиск'),
            array('url' => '/admin/trade/sell', 'section' => 'Cделать продажу', 'title' => 'Cделать продажу'),
            array('url' => '/admin/trade/history', 'section' => 'История продаж', 'title' => 'История продаж'),
            array('url' => '/admin/trade/coming', 'section' => 'Приход', 'title' => 'Приход'),
            array('url' => '/admin/trade/reserve', 'section' => 'Резерв', 'title' => 'Резерв')
        );

        parent::__construct();
    }

    public function index() {
        if (!Acl::instance()->is_allowed('trade_search')) {
            message::error('Нет прав доступа к данному разделу', '/admin');
        }

        $this->template = new View('admin/trade/index');
    }

    public function search()
    {
        $post = array_diff($this->input->post(), array(''));

        if (count($post) > 0){
            $products = $this->findProducts($post);
        }
        $this->template = new View('admin/trade/search');
        $this->template->products = $products;
    }

    public function sell()
    {
//        cookie::set('author', $fill['author'], Kohana::config('cookie.expire'));
        $post = array_diff($this->input->post(), array(''));

        if (count($post) > 0){
            $products = $this->findProducts($post);
        }

        $this->template = new View('admin/trade/sell');
        $this->template->products = $products;
    }

    public function history()
    {
        $table = new AclOrder_Model();
        $items = $table->db
            ->select(array('self.*'))
            ->from($table->table_name)
            ->order_by('self.date','desc')
            ->get()
            ->rows();
        foreach ($items as $key => $item) {
            $items[$key]['order'] = unserialize($item['order']);
        }
        $this->template = new View('admin/trade/history');
        $this->template->items = $items;
    }

    public function coming()
    {
        $post = array_diff($this->input->post(), array(''));
        if (count($post) > 0){
            $products = $this->findProducts($post);
        }

        $this->template = new View('admin/trade/coming');
        $this->template->products = $products;
    }

    public function uncoming()
    {
        $post = $this->input->post();

        $table = new Catalog_Model();
        $product = $table->db
            ->select(array('self.id', 'self.name', 'self.price', 'self.volume', 'catalog_tastes.article', 'taste' => 'catalog_tastes.name', 'catalog_tastes.count', 'catalog_tastes.count2'))
            ->from($table->table_name)
            ->left_join(array('ml_catalog_tastes' => 'catalog_tastes'), array('catalog_tastes.productId'=>'self.id'))
            ->where("catalog_tastes.article", '=', $post['article'])
            ->order_by('self.name','desc')
            ->get()
            ->row();

        if ($post['action'] == 'subtract' && $product['count2'] < $post['value']) {
            echo json_encode(array(
                'error' => 'Количества нет на складе!',
            ));
            exit;
        }

        switch($post['action']) {
            case 'subtract':
                $iteral = '-';
                $product['count2']-= $post['value'];
                break;
            case 'up':
                $iteral = '+';
                $product['count2']+= $post['value'];
                break;
            default:
                $iteral = '+';
                $product['count2']+= $post['value'];
                break;
        }
        $item = new Catalog_Model();
        $taste = new CatalogTastes_Model();
        $item->update(array('availability2' =>  db::expr('availability2 ' . $iteral . ' ' . $post['value'])), array('id' => $product['id']));
        $taste->update(array('count2' =>  db::expr('count2 ' . $iteral . ' ' . $post['value'])), array('article' => $product['article']));
        echo json_encode(array(
            'product' => $product,
        ));
        exit;
    }

    public function reserve()
    {
        $post = array_diff($this->input->post(), array(''));
        if (count($post) > 0){
            $products = $this->findProducts($post);
        }
        $this->template = new View('admin/trade/reserve');
        $this->template->products = $products;
    }

    public function reserved()
    {
        $post = $this->input->post();

        $table = new Catalog_Model();
        $product = $table->db
            ->select(array('self.id', 'self.name', 'self.price', 'self.volume', 'catalog_tastes.article', 'taste' => 'catalog_tastes.name', 'catalog_tastes.count', 'catalog_tastes.count2'))
            ->from($table->table_name)
            ->left_join(array('ml_catalog_tastes' => 'catalog_tastes'), array('catalog_tastes.productId'=>'self.id'))
            ->where("catalog_tastes.article", '=', $post['article'])
            ->order_by('self.name','desc')
            ->get()
            ->row();

        if ($product['count2'] < $post['count']) {
            echo json_encode(array(
                'error' => 'Количества нет на складе!',
            ));
            exit;
        }

        $taste = new CatalogTastes_Model();
        $taste->update(array('reserveCount' =>  $post['count'], 'reserveDate' => date("Y-m-d H:i:s", time() + $post['date'] * 86400)), array('article' => $post['article']));
        echo json_encode(array(
            'reserved' => true,
            'reserveCount' => $post['count'],
            'reserveDate' => date("Y-m-d H:i:s", time() + $post['date'] * 86400)
        ));
        exit;
    }

    public function makesell()
    {
        $post = $this->input->post();

        $table = new Catalog_Model();
        $product = $table->db
            ->select(array('self.id', 'self.name', 'self.price', 'self.volume', 'catalog_tastes.article', 'taste' => 'catalog_tastes.name', 'catalog_tastes.count', 'catalog_tastes.count2', 'catalog_tastes.reserveCount', 'catalog_tastes.reserveDate'))
            ->from($table->table_name)
            ->left_join(array('ml_catalog_tastes' => 'catalog_tastes'), array('catalog_tastes.productId'=>'self.id'))
            ->where("catalog_tastes.article", '=', $post['article'])
            ->order_by('self.name','desc')
            ->get()
            ->row();
        $taste = new CatalogTastes_Model();
        $item = new Catalog_Model();
        $item->update(array('availability2' =>  db::expr('availability2 - ' . $product['reserveCount'])), array('id' => $product['id']));
        $taste->update(array('count2' => db::expr('count2 - ' . $product['reserveCount']), 'reserveCount' =>  0, 'reserveDate' => null), array('article' => $post['article']));

        echo json_encode(array(
            'sell' => true,
            'count' => $product['count2'] - $product['reserveCount'],
        ));
        exit;
    }

    protected function findProducts($post)
    {
        if (stripos($post['article'], '475') == 0) {
            $post['article'] = str_replace('475', '', $post['article']);
        }

        $products = array();
        $table = new Catalog_Model();
        if (isset($post['name']) && $post['name'] != '' && isset($post['article']) && $post['article'] != '') {
            $products = $table->db
                ->select(array('self.name', 'self.price', 'self.volume', 'catalog_tastes.article', 'taste' => 'catalog_tastes.name', 'catalog_tastes.count', 'catalog_tastes.count2', 'catalog_tastes.reserveCount', 'catalog_tastes.reserveDate'))
                ->from($table->table_name)
                ->left_join(array('ml_catalog_tastes' => 'catalog_tastes'), array('catalog_tastes.productId'=>'self.id'))
                ->where("self.name", 'like', '%'.$post['name'].'%')
                ->or_where("catalog_tastes.article", '=', $post['article'])
                ->order_by('catalog_tastes.article','desc')
                ->get()
                ->rows();
        } elseif (isset($post['name']) && $post['name'] != '') {
            $products = $table->db
                ->select(array('self.name', 'self.price', 'self.volume', 'catalog_tastes.article', 'taste' => 'catalog_tastes.name', 'catalog_tastes.count', 'catalog_tastes.count2', 'catalog_tastes.reserveCount', 'catalog_tastes.reserveDate'))
                ->from($table->table_name)
                ->left_join(array('ml_catalog_tastes' => 'catalog_tastes'), array('catalog_tastes.productId'=>'self.id'))
                ->where("self.name", 'like', '%'.$post['name'].'%')
                ->order_by('catalog_tastes.article','desc')
                ->get()
                ->rows();
        } elseif (isset($post['article']) && $post['article'] != '') {
            $products = $table->db
                ->select(array('self.name', 'self.price', 'self.volume', 'catalog_tastes.article', 'taste' => 'catalog_tastes.name', 'catalog_tastes.count', 'catalog_tastes.count2', 'catalog_tastes.reserveCount', 'catalog_tastes.reserveDate'))
                ->from($table->table_name)
                ->left_join(array('ml_catalog_tastes' => 'catalog_tastes'), array('catalog_tastes.productId'=>'self.id'))
                ->where("catalog_tastes.article", '=', $post['article'])
                ->order_by('catalog_tastes.article','desc')
                ->get()
                ->rows();
        }

        return $products;
    }

    public function addSell()
    {
        $post = $this->input->post();

        if($post['order'] != '') {
            $order = unserialize($post['order']);
        }

        if(isset($order[$post['id']])) {
            echo json_encode(array(
                'error' => 'Товар уже в заказе!',
            ));
            exit;
        }

        $table = new Catalog_Model();
        $product = $table->db
            ->select(array('self.id', 'self.name', 'self.price', 'self.volume', 'catalog_tastes.article', 'taste' => 'catalog_tastes.name', 'catalog_tastes.count', 'catalog_tastes.count2'))
            ->from($table->table_name)
            ->left_join(array('ml_catalog_tastes' => 'catalog_tastes'), array('catalog_tastes.productId'=>'self.id'))
            ->where("catalog_tastes.article", '=', $post['id'])
            ->order_by('self.name','desc')
            ->get()
            ->row();
        $product['sell'] = 1;

        if (isset($order)) {
            $order[$post['id']] = $product;
        } else {
            $order = array(
                $post['id'] => $product
            );
        }
        $view = View::factory('admin/trade/order')->bind('order', $order);

        echo json_encode(array(
            'order' => serialize($order),
            'table' => $view->render()
        ));
        exit;
    }

    public function subtract()
    {
        $post = $this->input->post();

        if($post['order'] != '') {
            $order = unserialize($post['order']);
        }

        if (isset($order[$post['id']])) {
            if ($order[$post['id']]['sell'] > 1) {
                $order[$post['id']]['sell']-= 1;
            } else {
                unset($order[$post['id']]);
            }


            $view = View::factory('admin/trade/order')->bind('order', $order);
            echo json_encode(array(
                'order' => serialize($order),
                'table' => $view->render()
            ));
            exit;
        }

        echo json_encode(array(
            'error' => 'Товар не найден в заказе!',
        ));
        exit;
    }

    public function up()
    {
        $post = $this->input->post();

        if($post['order'] != '') {
            $order = unserialize($post['order']);
        }

        if (isset($order[$post['id']])) {
            if ($order[$post['id']]['sell'] < $order[$post['id']]['count2']) {
                $order[$post['id']]['sell']+= 1;
            } else {
                echo json_encode(array(
                    'error' => 'Не достаточно на складе!',
                ));
                exit;
            }

            $view = View::factory('admin/trade/order')->bind('order', $order);
            echo json_encode(array(
                'order' => serialize($order),
                'table' => $view->render()
            ));
            exit;
        }
        echo json_encode(array(
            'error' => 'Товар не найден в заказе!',
        ));
        exit;
    }

    public function removePosition()
    {
        $post = $this->input->post();

        if($post['order'] != '') {
            $order = unserialize($post['order']);
        }

        if (isset($order[$post['id']])) {
            unset($order[$post['id']]);

            $view = View::factory('admin/trade/order')->bind('order', $order);
            echo json_encode(array(
                'order' => serialize($order),
                'table' => $view->render()
            ));
            exit;
        }
        echo json_encode(array(
            'error' => 'Товар не найден в заказе!',
        ));
        exit;
    }

    public function sendOrder()
    {
        $post = $this->input->post();

        if($post['order'] != '') {
            $order = unserialize($post['order']);
        } else {
            echo json_encode(array(
                'error' => 'Ошибка.',
            ));
            exit;
        }

        $aclOrder = new AclOrder_Model();
        $aclOrder->insert(
            array(
                'order' => $post['order'],
                'price' => AclOrder_Model::calculateTotalPrice($order),
            )
        );

        foreach ($order as $article => $product) {
            $item = new Catalog_Model();
            $taste = new CatalogTastes_Model();
            $item->update(array('availability2' =>  db::expr('availability2 - ' . $product['sell'])), array('id' => $product['id']));
            $taste->update(array('count2' =>  db::expr('count2 - ' . $product['sell'])), array('article' => $article));
        }

        echo json_encode(array(
            'order' => 'Выполнено.',
        ));
        exit;
    }
}