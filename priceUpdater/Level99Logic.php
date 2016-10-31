<?php

class Level99Logic {
	
	const INSERT_ACTION = 'insert';
	
	const UPDATE_ACTION = 'update';
	
	const DELETE_ACTION = 'delete';

    const availability = 'availability2';

    const price = 'price';

    const TASTE_COUNT = 'count2';

    private $db;

    public $_categoryAdapter = array(
        "Протеины" => "Протеины",
        "Аминокислоты / BCAA"=> "BCAA",
        "Гейнеры" => "Гейнеры",
        "Жиросжигатели"=> "Сжигатели жира",
        "L-карнитин"=> "Карнитин (L-карнитин)",
        "Перед тренировкой "=> "Предтренировочные комплексы",
        "После тренировки"=> "Послетренировочные комплексы",
        "Креатин"=> "Креатин моногидрат",
        "Витамины и минералы"=> "Витаминно-минеральные комплексы",
        "Omega 3"=> "Omega 3",
        "Связки и суставы"=> "Для связок и суставов",
        "Тестостерон"=> "Анаболические комплексы",
        "Специальные препараты"=> "Специальные препараты",
        "Глютамин"=> "Глютамин",
        "Оксид азота (NO2)"=> "Оксид азота (NO2)",
        "Анаболические комплексы"=> "Анаболические комплексы",
        "Энергетики"=> "Энергетики",
        "Антиоксиданты"=> "Антиоксиданты",
        "Углеводы и изотоники"=> "Изотоники",
        "Напитки"=> "Изотоники",
        "Батончики"=> "Батончики",
        "Улучшение пищеварения"=> "Специальные препараты",
        "CLA"=> "CLA",
        "Диуретики"=> "Диуретики",
        "Долголетие"=> "Специальные препараты",
        "Здоровый сон"=> "Мелатонин",
        "Шейкеры, бутылочки"=> "Шейкеры, бутылочки",
    );

	public $_manufacturerAdapter = array(
        'Betancourt' => 'Betancourt Nutrition',
        'Body First' => 'Body First',
        'Body Strong' => 'BodyStrong',
        'Easy body' => 'QNT',
        'Gat' => 'German American Technologies',
        'MRI' => 'MRI',
        'Natrol' => 'Natrol',
        'Primaforce' => 'PrimaForce',
        'Prolab' => 'Prolab',
        'QNT' => 'QNT',
        'Red Star Labs' => 'Red Star Labs',
        'Scivation' => 'Scivation',
        'Usplabs' => 'USPlabs',
	);

//> до 500 руб +100 руб
//> от 500 до 1000 +150 руб
//> от 1000 до 2000 +200 руб
//> от 2000 до 3000 +300 руб
//> от 3000 +400 руб

    public $_priceRange = array(

    );

    /**
     * Возвращает цену с наценкой
     *
     * @param $price
     * @param $manufacturerName
     *
     * @return mixed
     */
    public function getActualPrice($price, $manufacturerName)
    {
        if ($manufacturerName == 'QNT') {
            $price = $price - round($price * 0.07);
        }

        if ($price < 500) {
            return $price + 100;
        }

        if ($price > 500 && $price < 1000) {
            return $price + 150;
        }

        if ($price > 1000 && $price < 2000) {
            return $price + 200;
        }

        if ($price > 2000 && $price < 3000) {
            return $price + 300;
        }

        if ($price > 3000) {
            return $price + 400;
        }
    }

    public function __construct()
    {
        $this->db = DB::instance();
    }
	/**
	* Проверка авторизации.
	*/
	public function checkAuth($data)
	{
		preg_match('|<div class="right">(.*)</div>|isU', $data, $response);
		if(isset($response[1]) && $response[1] == '<a href="/users/logout/">Выйти</a>') {
			return true;
		}
		return false;
	}
	
	public function getCategoryList($page)
	{
		$result = array();
		preg_match('|<ul class="categories_list">(.*)</ul>|isU', $page, $response);
		
		if(empty($response[1]) === true){
			die('file category load');
		}
		preg_match_all('|<li class="categories_item"><a href="(.*)">(.*)</a></li>|isU', $response[1], $response);
		if(empty($response[1]) === true){
			die('file category load');
		}
		
		foreach ($response[1] as $key => $value) {
			$result[$response[2][$key]] = $value;
		}

		return $result;
	}
	
	public function getItemList($page)
	{
		$result = array();
		
		preg_match_all('|--><div data-id="(.*)" class="product">(.*)</div><!--|isU', $page, $response);
		if(empty($response[0]) === true){
			echo "Warning: fail load items! \n";
			return false;
		}
		
		foreach ($response[0] as $itemData) {
			preg_match('|<div class="prod_name"><a href="(.*)">(.*)</a></div>|isU', $itemData, $part);
			if(empty($part[1]) === true) {
				echo "Warning: fail pars item! \n";
				continue;
			}
			
			$result[$part[2]] = $part[1];
		}

		return $result;
	}

    /**
     * Определяем что делать с товаром.
     *
     * @param $item
     * @return string
     */
    public function getItemAction($item, $categoryId)
	{
        foreach($item['packing'] as $packing) {
            $find = false;
            // Исчим есть ли товар в базе?
            $sql = "SELECT * FROM `ml_catalog` WHERE `name` = :name;";
            $products = $this->db->select($sql, array(
                    ':name' => $item["name"],
                ))->findAll();

            //Нашли товары? Проверим если ли среди них нужная фасовка.
            if (isset($products[0]->id)) {
				echo "Обновляем продукт ({{$item['name']}}) ... \n";
                foreach($products as $dbProduct) {
                    $packingValue = preg_replace('~[^0-9]+~', '', $packing['value']);
                    $dbValue = preg_replace('~[^0-9]+~', '', $dbProduct->volume);

                    // Разница фасовок меньше 20 они одинаковы!
                    if (abs((int) $dbValue - (int) $packingValue) < 20) {
                        $find = true;
                        $this->updateProduct($dbProduct, $packing, $item);
                        break;
                    }
                }
            }

            if($find === false) {
				echo "Загрузка продукта ({{$item['name']}}) ... \n";
                $this->addProduct($item, $packing, $categoryId);
            }
        }
	}
	
	/**
     * добавляет продукты.
     *
     * @param $product
     * @param $packing
     * @param $categoryId
     */
    protected function addProduct($product, $packing, $categoryId)
    {
        $uri = $product['uri']  . '_' . preg_replace('~[^0-9]+~', '', $packing['value']);

		$check = $this->db->select("SELECT `id` FROM `ml_catalog` WHERE `uri` = :uri", array(':uri' => $uri))->find();
            if (empty($check->id) === false) {
                echo "Товар ({$product['uri']}) уже существует \n";
                return false;
            }
		// Исчим производителя!
        $manufacturerName = isset($this->_manufacturerAdapter[$product['manufacturer']]) ? $this->_manufacturerAdapter[$product['manufacturer']] : $product['manufacturer'];
        $sql = "SELECT * FROM `ml_catalog_manufacturer` WHERE `name` = :manufacturerName LIMIT 1";
        $manufacturer = $this->db->select($sql, array(':manufacturerName' => $manufacturerName))->find();

		if (empty($manufacturer->id) === true) {
        // Если нет производителя, добавим его херли...
        $sql = "INSERT INTO `ml_catalog_manufacturer` (`id`, `name`, `description`) VALUES (NULL, :manufacturerName, NULL);";
            $manufacturerId = $this->db->insert($sql, array(':manufacturerName' => $manufacturerName));
        } else {
            $manufacturerId = $manufacturer->id;
        }

		$articles = array();
		$count = 0;
		foreach($packing['taste'] as $taste) {
			$articles[] = $product['article'] . preg_replace('~[^0-9]+~', '', $packing['value']) . $taste['id'];
			$count+= $taste['count'];
		}


        $productId = $this->db->create('ml_catalog',
            array(
                'group_id' => $categoryId,
                'name' => $product['name'],
                'manufacturer_id' => $manufacturerId,
                'articles' => implode(',', $articles),
                'code' => $product['article'] . preg_replace('~[^0-9]+~', '', $packing['value']),
                '1c_code' => $product['article'] . preg_replace('~[^0-9]+~', '', $packing['value']),
                'price' => $this->getActualPrice((int) preg_replace('~[^0-9]+~', '', $packing['price']), $manufacturerName),
                'priceSupplier' => 0,
                'oldprice' => 0,
                'availability' => 0,
                'availability2' => $count,
                'description' => $product['description'],
                'uri' => $uri,
                'date_modified' => '0000-00-00 00:00:00',
                'sort_order' => '500',
                'active' => '1',
                'seo_title' => $product['name'],
                'seo_keywords' => $product['name'],
                'seo_description' => $product['name'],
                'volume' => $packing['value'],
                'is_use_auto_tags' => 1,
                'in_yml' => 1,
                )
        );
		
		if (empty($productId)) {
            echo "Товар (" . $product['name'] . ") не удалось добавить \n";
            return false;
        }
		
		$this->downloadImage($product, $productId, $uri);
		
		foreach($packing['taste'] as $packingTaste) {
				   // Вкуса нет добавим!
				   $this->db->create('ml_catalog_tastes', array(
				        'productId' => $productId,
                        'name' => $packingTaste['name'],
                        'article' => $product['article'] . preg_replace('~[^0-9]+~', '', $packing['value']) . $packingTaste['id'],
                        self::TASTE_COUNT => $packingTaste['count'],
				   ));
        }
    }
	
	public function checkCategory($groupName)
	{
		// Исчим категорию!
        $sql = "SELECT * FROM `ml_catalog_groups` WHERE `title` = :groupName LIMIT 1";

        $groupName = isset($this->_categoryAdapter[$groupName]) ? $this->_categoryAdapter[$groupName] : $groupName;
        $group = $this->db->select($sql, array(':groupName' => $this->_categoryAdapter[$groupName]))->find();
        if (empty($group->id) === true) {
            // Пока не научились добавлять категорию пропускаем ее.
            return false;
        }
		
		return $group->id;
	}
	public function getItemInfo($page)
	{
		$result = array(
			'article' => '',
			'name' => '',
			'category' => '',
			'manufacturer' => '',
			'image' => '',
			'preview' => '',
			'description' => '',
			'packing' => array(
				//'value' => '',
				//'price' => '',
				//'taste' => array(
					//0 => array(
					//	'id' => '',
					//	'name' => '',
					//	'count' => '',
				//)
			)
		);
		preg_match('|<div class="crumbs">(.*)</div>|isU', $page, $response);
			if(empty($response[1]) === true) {
				echo "Warning: fail item! \n";
				return false;
			}
		preg_match_all('|<a href="(.*)" class="crumbs_link">(.*)</a>|isU', $response[1], $response);
		
		$result['category'] = isset($response[2][3]) ? $response[2][3] : null;
		$result['manufacturer'] = isset($response[2][2]) ? $response[2][2] : null;
		
		preg_match('|<h1 class="h_title item_h_title">(.*)</h1>|isU', $page, $response);
		$result['name'] = isset($response[1]) ? $response[1] : null;

		preg_match('|data-id="(.*)"|isU', $page, $response);
		$result['article'] = isset($response[1]) ? $response[1] : null;
		
		preg_match('|<a href="(.*)" rel="lightbox" target="_blank">|i', $page, $response);
		$result['image'] = isset($response[1]) ? $response[1] : null;
		
		preg_match('|<img src="(.*)" alt="' . $result['name'] . '"></a>|i', $page, $response);
		$result['preview'] = isset($response[1]) ? $response[1] : null;
		
		preg_match('|<div class="desc_conent">(.*)</div>|isU', $page, $response);
		$result['description'] = isset($response[1]) ? $response[1] : null;
		
		preg_match_all('|<div class="weight_item">(.*)</div>
																						</div>
|isU', $page, $response);
		if(empty($response[1]) === true) {
			echo "Warning: fail parse packing! \n";
		} else{
			
			foreach($response[1] as $key => $packingInfo) {
				preg_match('|<div class="weight_name">(.*)</div>|isU', $packingInfo, $packingPart);
				if(isset($packingPart[1]) && strpos($packingPart[1], 'кг') !== false) {
					$tmp = explode(' ', $packingPart[1]);
					$packingPart[1] = $tmp[0] . '000 г';
				}
				$result['packing'][$key]['value'] = isset($packingPart[1]) ? $packingPart[1] : null;

				preg_match('|<div class="weight_price">(.*)<i class="rub"></i></div>|isU', $packingInfo, $packingPart);
				$result['packing'][$key]['price'] = isset($packingPart[1]) ? $packingPart[1] : null;
				
				preg_match_all('|<div class="taste_(.*)">(.*)<div class="weight_exist">(.*)</div>|isU', $packingInfo, $packingPart);
				if(isset($packingPart[2])) {
					foreach($packingPart[2] as $tasteKey => $tasteInfo) {
						preg_match('|<div class="taste">(.*)</div>|isU', $tasteInfo, $tastePart);
						$result['packing'][$key]['taste'][$tasteKey]['name'] = isset($tastePart[1]) ? $tastePart[1] : null;

						if ((trim($packingPart[3][$tasteKey])) == 'В ожидании') {
							$result['packing'][$key]['taste'][$tasteKey]['count'] = 0;
						} else {
							$result['packing'][$key]['taste'][$tasteKey]['count'] = substr_count($packingPart[3][$tasteKey], '<i></i>');
						}

						preg_match('|data-taste_id="(.*)"|isU', $tasteInfo, $tastePart);
						$result['packing'][$key]['taste'][$tasteKey]['id'] = isset($tastePart[1]) ? $tastePart[1] : null;
					}
				}
			}
		}
		
		return $result;
	}

	/**
     * Проверяет, обновляет продукты.
     *
     * @param $parseProduct
     * @param $packing
     * @param $dbProduct
     */
    private function updateProduct($dbProduct, $packing, $parseProduct)
    {
                $packingValue = preg_replace('~[^0-9]+~', '', $packing['value']);
                $manufacturerName = isset($this->_manufacturerAdapter[$parseProduct['manufacturer']]) ? $this->_manufacturerAdapter[$parseProduct['manufacturer']] : $parseProduct['manufacturer'];

				$count = 0;
                foreach($packing['taste'] as $packingTaste) {
					$count+= $packingTaste['count'];
                    $sql = "SELECT * FROM `ml_catalog_tastes` WHERE `productId` = :productId AND `name` = :tasteName;";
                    $dbTaste = $this->db->select($sql, array(
                            ':productId' => $dbProduct->id,
                            ':tasteName' => $packingTaste['name'],
                        ))->find();

                    if (isset($dbTaste->id)) {
                        // Вкус найден обновим!
						if($dbTaste->{self::TASTE_COUNT} == 0) {
							$this->db->update('ml_catalog_tastes', $dbTaste->id,
								array(
									self::TASTE_COUNT => $packingTaste['count'],
								)
							);
						}
                    } else {
                        // Вкуса нет добавим!
                        $sql = "INSERT INTO `ml_catalog_tastes` (`id`, `productId`, `name`, `article`, `count2`) VALUES
                        (NULL, :productId, :tasteName, :article, :tasteCount);";
                        $this->db->insert($sql,
                            array(
                                ':productId' => $dbProduct->id,
                                ':tasteName' => $packingTaste['name'],
                                ':article' => $parseProduct['article'] . $packingValue . $packingTaste['id'],
                                ':tasteCount' => $packingTaste['count'],
                            )
                        );
                    }
                }

                // Обновляем карточку.
				if($dbProduct->{self::availability} == 0) {
					$this->db->update('ml_catalog', $dbProduct->id,
						array(
							'price' => $this->getActualPrice((int) preg_replace('~[^0-9]+~', '', $packing['price']), $manufacturerName),
							'in_yml' => 1,
							self::availability => $count,
						)
					);
				}
    }

    protected function downloadImage($data, $productId, $uri)
    {
        $image = file_get_contents(DOMAIN . $data['image']);

        $preview = file_get_contents(DOMAIN . $data['image']);

        $imageName = end(explode('/', $data['image']));
        echo "\n Качаю " . $data['image'] . "... \n";
        $ext = end(explode('.', $imageName));
        $directoryId = $this->saveImageInFackingTable($productId, $imageName);

        mkdir(__DIR__ . '/../files/catalog/photo/' . $directoryId, 0777);
        mkdir(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/0', 0777);
        mkdir(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/1', 0777);
        mkdir(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/2', 0777);

        $imageData = array(
            '/files/catalog/photo/' . $directoryId . '/0/' . $productId . $directoryId . '_' . $imageName,
            '/files/catalog/photo/' . $directoryId . '/1/' . $productId . $directoryId . '_' . $imageName,
            '/files/catalog/photo/' . $directoryId . '/2/' . $productId . $directoryId . '_' . $imageName,
        );

        file_put_contents(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/0/' . $productId . $directoryId . '_' . $imageName, $image);
        file_put_contents(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/1/' . $productId . $directoryId . '_' . $imageName, $preview);
        file_put_contents(__DIR__ . '/../files/catalog/photo/' . $directoryId . '/2/' . $productId . $directoryId . '_' . $imageName, $preview);

        $this->updateImageInFackingTable($directoryId, $imageData);
    }

    protected function saveImageInFackingTable($productId, $image)
    {
        $sql = "
    INSERT INTO `ml_files`
    (`id`, `name`, `type`, `src`, `item_table`, `item_id`, `preview_1`, `preview_2`, `preview_3`, `input_name`, `file_size`, `image_width`, `image_height`, `image_orientation`)
    VALUES (NULL, '{$image}', 'image', '', 'catalog', '{$productId}', '', '', '', NULL, '0', '0', '0', 'notset');
    ";
        $imageId = $this->db->insert($sql);

        return $imageId;
    }

    protected function updateImageInFackingTable($directoryId, $imageData)
    {
        $sql = "UPDATE `ml_files` SET `src` = '" . $imageData[0] . "', `preview_1` = '{$imageData[1]}', `preview_2` = '{$imageData[2]}', `preview_3` = '{$imageData[2]}', `input_name` = 'image' WHERE `id` = '{$directoryId}'";

        $this->db->execute($sql);
    }
}
