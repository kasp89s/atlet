<?php

class Parser {

    private $quick = true;

    public function __construct($quick = true)
    {
        $this->quick = $quick;
    }
    public function getCategoryList($data)
    {
        preg_match('|<table class="opt_producers">(.*)</table>|isU', $data, $matches);
        if (empty($matches[0]) === true) {
            return false;
        }

        preg_match_all('|<td><a href="(.*)">(.*)</a></td>|isU', $matches[0], $matches);

        $result = array();
        foreach ($matches[1] as $key => $item){
            $result[strip_tags(trim($matches[2][$key]))] = $item;
        }

        return $result;
    }

    public function getProductsCount($data)
    {
        $connector = new Connector();
        preg_match('|<div id="user_id">(.*)</div>|isU', $data, $matches);
        $userId = isset($matches[1]) ? $matches[1] : USER_ID;

        preg_match('|<table class="gnrc opt_items">(.*)</table>|isU', $data, $matches);
        if (empty($matches[0]) === true) {
            return false;
        }
        preg_match_all('|<tr id="(.*)">(.*)</tr>|isU', $matches[0], $matches);
        if (empty($matches[1]) === true) {
            return false;
        }

        $result = array();
        $dataArray = $matches;
        foreach ($dataArray[1] as $key => $item){
            $id = str_replace('id', '', $item);

            preg_match('|<td class="st"><a href="(.*)">(.*)</a>(.*)<b>(.*)</b></td>|isU', $dataArray[2][$key], $matches);
            if (empty($matches[0]) === false) {
                $productLink = $matches[1];
                $productTitle = $matches[2];
                $productVolume = $this->trimVolume($matches[3]);
                $productTaste = $matches[4];
            } else {
                preg_match('|<td class="st"><a href="(.*)">(.*)</a>(.*)</td>|isU', $dataArray[2][$key], $matches);
                $productLink = $matches[1];
                $productTitle = $matches[2];
                $productVolume = $this->trimVolume($matches[3]);
                $productTaste = 'без вкуса';
            }

            preg_match('|<td>(.*)</td>|isU', $dataArray[2][$key], $matches);
            $dataArray[2][$key] = str_replace($matches[0], '', $dataArray[2][$key]);
            preg_match('|<td>(.*)</td>|isU', $dataArray[2][$key], $matches);
            $retailPrice = isset($matches[1]) ? (float) $matches[1] : 0;
            preg_match('|<td>(.*)</td>|isU', str_replace($matches[0], '', $dataArray[2][$key]), $matches);
            $tradePrice = isset($matches[1]) ? (float) $matches[1] : 0;

            preg_match_all('|<td>(.*)</td>|isU', $dataArray[2][$key], $matches);

            if (empty($matches[1]) === true) {
                continue;
            }

            preg_match('|<td class="qua">(.*)</td>|isU', $dataArray[2][$key], $ava);

            if (empty($ava[1]) === true) {
                continue;
            }
            if (stripos(strip_tags($ava[1]), 'уведомить') !== false) {
                $result[$id] = array(
                    'productLink' => $productLink,
                    'productTitle' => $productTitle,
                    'productVolume' => $productVolume,
                    'productTaste' => $productTaste,
                    'count' => 0,
                    'retailPrice' => $retailPrice,
                    'tradePrice' => ($retailPrice == 0) ? 0 : $tradePrice
                );
            } else {
                echo "\n Считаю товар ID " . $id;
                $count = str_replace('>', '', end($matches[1]));
//                $count = str_replace('<i>+</i>', '', $count);
                $count = (int) trim(strip_tags($count));

                $quantity = 1;
                if ($this->quick === false) {
                    while(true) {
                        switch ($count) {
                            case 20:
                                $quantity = 5;
                                break;
                            case 50:
                                $quantity = 10;
                                break;
                            case 100:
                                $quantity = 50;
                                break;
                            case 1000:
                                $quantity = 100;
                                break;
                            default: true;
                                break;
                        }
                        $response = json_decode($connector->read('http://www.5lb.ru/cgi-bin/mp/page.pl?m=shop&jsa=add_to_cart&user_id=' . $userId . '&id=' . $id . '&quantity=' . $quantity));
                        if (empty($response->stock_not_enough) === false && $response->stock_not_enough == 1) {
                            break;
                        }

                        $count = $count + $quantity;
                        echo '.';
                    }
                }
                $connector->read('http://www.5lb.ru/cgi-bin/mp/page.pl?m=shop&id=' . $id . '&user_id=' . $userId . '&jsa=delete_from_cart');

                echo "({$count})";
                $result[$id] = array(
                    'productLink' => $productLink,
                    'productTitle' => $productTitle,
                    'productVolume' => $productVolume,
                    'productTaste' => $productTaste,
                    'count' => $count,
                    'retailPrice' => $retailPrice,
                    'tradePrice' => ($retailPrice == 0) ? 0 : $tradePrice
                );
            }

        }

        return $this->sortProducts($result);
    }

    /**
     * Получает данные о фасофке.
     *
     * @param $data
     * @return array|bool
     */
    public function getProductInfo($data)
    {
        preg_match('|<div class="bread-crumbs">(.*)</div>|isU', $data, $matches);
        if (empty($matches[1]) === true) {
            return false;
        }
        $bread = strip_tags($matches[1]);
        $bread = explode('→', $bread);
        $groupName = trim($bread[count($bread) - 2]);

        preg_match('|<div class="photo">(.*)</div>|isU', $data, $matches);
        if (empty($matches[1]) === true) {
            return false;
        }
        preg_match('|<a id="thumb1" href="(.*)"(.*)"><img src="(.*)" alt="">|isU', $matches[1], $matches);
        $preview = $matches[3];
        $image = $matches[1];

        preg_match('|<div id="doc_text">(.*)</div>|isU', $data, $matches);
        if (empty($matches[1]) === true) {
            $matches[1] = '';
        }
        $description = $matches[1];
        return array(
            'groupName' => $groupName,
            'preview' => $preview,
            'image' => $image,
            'description' => $description,
        );
    }

    /**
     * Выполнит привидение фасофки.
     *
     * @param $volume
     * @return mixed
     */
    private function trimVolume($volume)
    {
        $volume = trim($volume);
        $volume = str_replace('(', '', $volume);
        $volume = str_replace(')', '', $volume);

        return $volume;
    }

    /**
     * Сортирует товары по фасофке и вкусу.
     *
     * @param $data
     */
    private function sortProducts($data)
    {
        $result = array();
        foreach ($data as $id => $productData) {
            if (isset($result[$productData['productTitle']])) {
                    if (isset($result[$productData['productTitle']]['volumes'][$productData['productVolume']])){
                        $result[$productData['productTitle']]['volumes'][$productData['productVolume']]['tastes'][$productData['productTaste']] =
                            array(
                                'article' => $id,
                                'count' => $productData['count'],
                            );
                        $result[$productData['productTitle']]['volumes'][$productData['productVolume']]['data']['articles'][] = $id;

                        if (
                        (int) $result[$productData['productTitle']]['volumes'][$productData['productVolume']]['data']['retailPrice'] == 0 ||
                        (int) $result[$productData['productTitle']]['volumes'][$productData['productVolume']]['data']['tradePrice'] == 0
                        ) {
                            $result[$productData['productTitle']]['volumes'][$productData['productVolume']]['data']['retailPrice'] = $productData['retailPrice'];
                            $result[$productData['productTitle']]['volumes'][$productData['productVolume']]['data']['tradePrice'] = $productData['tradePrice'];
                        }
                    } else {
                        $result[$productData['productTitle']]['volumes'][$productData['productVolume']] = array(
                            'tastes' => array(
                                $productData['productTaste'] => array(
                                    'article' => $id,
                                    'count' => $productData['count'],
                                )
                            ),
                            'data' => array(
                                'retailPrice' => $productData['retailPrice'],
                                'tradePrice' => $productData['tradePrice'],
                                'link' => $productData['productLink'],
                                'articles' => array($id)
                            )
                        );
                    }
            } else {
                $result[$productData['productTitle']]['volumes'][$productData['productVolume']] = array(
                    'tastes' => array(
                        $productData['productTaste'] => array(
                            'article' => $id,
                            'count' => $productData['count'],
                        ),
                     ),
                     'data' => array(
                         'retailPrice' => $productData['retailPrice'],
                         'tradePrice' => $productData['tradePrice'],
                         'link' => $productData['productLink'],
                         'articles' => array($id)
                    )
                );
            }
        }

        return $result;
    }

    /**
     * Получает список продуктов корзины.
     *
     * @param $page
     */
    public function getBasketList($data)
    {
        preg_match('|<table class="page-cart">(.*)</table>|isU', $data, $matches);

        if (empty($matches[1]) === true) {
            return false;
        }
        preg_match_all('|<div><a href="(.*)" class="del">|isU', $matches[1], $matches);

        if (empty($matches[1]) === true || count($matches[1]) == 0)
        {
            return array();
        } else {
            return $matches[1];
        }
    }

    public function addToBasket($list, $userID){
        $connector = new Connector();

        $result = array();
        foreach ($list as $code => $count)
        {
            $response = json_decode($connector->read('http://www.5lb.ru/cgi-bin/mp/page.pl?m=shop&jsa=add_to_cart&user_id=' . $userID . '&id=' . $code . '&quantity=' . $count));

            if (empty($response->stock_not_enough) === false && $response->stock_not_enough == 1 || empty($response->call_error) === false) {
                $result[$code] = false;
            } else {
                $result[$code] = true;
            }
        }

        return $result;
    }

    public function getProductById($id){
        $connector = new Connector();
        $response = $connector->read('http://www.5lb.ru/cgi-bin/mp/page.pl?m=docs&text=' . $id);

        if (stripos($response, 'Moved') !== false) {
            preg_match('|<a href="(.*)">|isU', $response, $matches);
            if (empty($matches[1]) === false) {
                $response = $connector->read($matches[1]);
            }
        }

        if (stripos($response, 'Moved') !== false) {
            preg_match('|<a href="(.*)">|isU', $response, $matches);
            if (empty($matches[1]) === false) {
                $response = $connector->read($matches[1]);
            }
        }

        preg_match('|<h1>(.*)</h1>|isU', $response, $name);
        $name = $name[1];
        preg_match('|<option(.*)value="' . $id. '">(.*)</option>|isU', $response, $taste);
        $taste = $taste[2];

        return array('name' => $name, 'taste' => $taste);
    }
    /**
     * Производит очистку корзины.
     *
     * @param $list
     */
    public function cleanBasket($list)
    {
        $connector = new Connector();

        foreach ($list as $item)
        {
            $connector->read('http://www.5lb.ru' . $item);
        }
    }

    /**
     * Проверяет авторизацию.
     *
     * @param $data
     * @return bool
     */
    public function isLogin($data)
    {
        preg_match('|<span class="lk-loggedon">(.*)</span>|isU', $data, $matches);
        $login = strip_tags($matches[1]);

        if (stripos($login, 'выход') === false) {
            return false;
        }

        return true;
    }
}
