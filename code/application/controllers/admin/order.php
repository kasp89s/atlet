<?php
/**
 * Интернет-приемная главы
 *
 */
class Order_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Заказы';

		if(Acl::instance()->is_allowed('order_add')){
			$this->menu = array(
				array('url'=>'/admin/order', 'section'=>'Все заказы'),
				array('url'=>'/admin/catorder', 'section'=>'Все статусы'),
				array('url'=>'/admin/catorder/edit', 'section'=>'Добавить статус', 'title'=>'Статус')
			);
		} else {
			$this->menu = array(
				array('url'=>'/admin/order', 'section'=>'Все заказы'),
				array('url'=>'/admin/catorder', 'section'=>'Все статусы'),
			);
		}

		parent::__construct();
	}


	/**
	 * Список заказов
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('order_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/order/index');
        if (empty($_GET['_ps']) === true) {
            $_GET['_ps'] = 100;
        }
		$cat          = (int)$this->input->get('cat');
// =========================================== //
        $table = new Order_Model;
        $table->db
            ->select(Array('self.id', db::expr('COUNT(DISTINCT date_create) as rowspan'), db::expr('LEFT(date_create, 10) as date'), db::expr('SUM(ml_order_products.productPrice * ml_order_products.quantity) + IF(ml_self.delivery = 2, 300, 100) as price')))
            ->from($table->table_name)
            ->join('order_products', 'order_products.orderID', 'self.id', 'LEFT')
            ->group_by(db::expr('LEFT(date_create, 10)'))
        ;
        if($cat) $table->db->where('self.cat', $cat);
        $monthPrice = $table->db->get()
            ->rows();

        $tmpPrice = array();
        foreach ($monthPrice as $price) {
            $tmpPrice[$price['date']] = array(
                'date' => $price['date'],
                'rowspan' => $price['rowspan'],
                'price' => $price['price']
            );
        }
// =========================================== //
		$table = new Order_Model;
		$table->info_cat();
		$table->db
            ->select(Array('self.id', 'self.author', 'self.date_create', 'self.delivery', db::expr('SUM(ml_order_products.productPrice * ml_order_products.quantity) + IF(ml_self.delivery = 2, 300, 100) as price')))
			->from($table->table_name)
            ->join('order_products', 'order_products.orderID', 'self.id', 'LEFT')
            ->select(Array('cat_id' => 'order_cat.id', 'cat_name' => 'order_cat.name'))
            ->group_by('self.id');
		if($cat) $table->db->where('self.cat', $cat);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('self.id' => 'desc', 'self.name', 'cat_name');
		$data = &$tm->show();

        foreach ($data['rows'] as $key => $row) {
            $data['rows'][$key]['monthPrice'] = $tmpPrice[substr($row['date_create'], 0, 10)];
        }

		form_filter::fill_list('cat', $cat, $data, $table->get_cat());

		$this->template->main = $data;
	}


	/**
	 * Редактирование заказа
	 *
	 */
	public function edit() {
		if(!Acl::instance()->is_allowed('order_edit'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/order/edit');

		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$post = $this->input->post();

		if(!$id)
			message::error('Некорректный идентификатор (id) заказа', '/admin/order');


		/**
		 * Правила валидации
		 */
		$table = new Order_Model();

		$table_cats = new Catorder_Model();
		$cats = $table_cats->db
				->select(Array('id', 'name'))
				->where('active', '=', '1')
				->from($table_cats->table_name)
				->order_by('name')
				->get()
				->rows();

		$form = new ValidateForm();
		$form->add_field('cat', array(new mod_list($cats)), array('required'));
		$form->add_field('author', 'string', array('required','required'));
		$form->add_field('phone', 'string', array('required','length[1,50]'));

		$form->add_field('email', 'string', array('valid::email', 'length[1,100]'));
		$form->add_field('description', 'string');

		$form->add_field('delivery_adress', 'string', array('required','required'));

        $arrPayments = $table->db
       		->select("*")
       		->from("payment_types")
       		->get()
       		->rows();

    	$arrDeliveryTypes = $table->db
       		->select("*")
       		->from("delivery_types")
       		->get()
       		->rows();

     	$arrDeliveryCosts = $table->db
       		->select("*")
       		->from("delivery_costs")
       		->get()
       		->rows();

		/**
		 * Вспомогательные данные
		 */
		$table = new Order_Model();
		$table->info_cat();

		if($id){
			$data = $table->db
				->select('self.*')
				->where('self.id', '=',$id)
				->from($table->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) заказа', '/admin/order');
		} else
			$data = array();

		foreach($arrDeliveryCosts as $delivery){
        	if($delivery['id'] == $data['delivery']){
        		$arrDelivery = $delivery;
        	}
        }

        foreach($arrPayments as $payment){
        	if($payment['id'] == $data['payment']){
        		$arrPayment = $payment;
        	}
        }

        $mdlCoupons = new Discount_Model();
    	$arrCouponData = $mdlCoupons->db
			->select(Array('coupon_id' => 'self.id', 'self.author', 'self.email', 'self.activation_code'))
			->from($mdlCoupons->table_name)
			->where('self.id', $data['coupon_id'])
			->get()
			->row();
		/**
		 * Сохранение
		 */
		if(!empty($post)){
			$data = $form->get_data() + $data;
			$form->validate();


			if($form->is_ok()){
				$fill = $form->get_fill();

				if($id) {
					if(!$result = $table->update($fill, array('id'=>$id))) {
						$form->add_error('update', 1);
					} else {
						log::add('order', 'Редактирование заказа id='.$id);
					}

				}
			}

			$mdlProducts = new OrderProducts_Model();
			$arrOrderContent = $mdlProducts->db
	       		->select(Array('self.id', 'self.productID', 'self.productName', 'self.productPrice', 'self.productCode', 'self.quantity',
	       					   'product_uri' => 'catalog.uri', 'catalog.1c_code',
	       					   'catalog.group_id', 'subtotal' => db::expr('`ml_self`.`quantity` * `ml_self`.`productPrice`')))
	       		->from($mdlProducts->table_name)
	       		->left_join('catalog', 'catalog.id', 'self.productID')
	       		->where('orderID', $id)
	       		->get()
	       		->rows();

	  		$postQuantity = $this->input->post('quantity');

	  		foreach($arrOrderContent as $tk => $tv){
	            if(isset($postQuantity[$tv['id']]) && intval($postQuantity[$tv['id']]) > 0){
	  				$mdlProducts->update(Array('quantity' => intval($postQuantity[$tv['id']])), Array('id' => $tv['id']));
	  			}
	  		}

	  		foreach($arrOrderContent as $tk => $tv){
	            if(isset($postQuantity[$tv['id']]) && intval($postQuantity[$tv['id']]) <= 0){
	  				$mdlProducts->delete(Array('id' => $tv['id']));
	  			}
	  		}

	  		if($this->input->post('newId', false) && $this->input->post('newQuantity', false)){
	  			$arrProductData = $mdlProducts->db
			       		->select('*')
			       		->from('catalog')
			       		->where('price', '>', db::expr('0'))
			       		->where('id', $this->input->post('newId', false))
			       		->get()
			       		->row();

				if($arrProductData){
					 $mdlProducts->insert(Array(
					 	  'productName'  => $arrProductData['name'],
						  'productCode'  => $arrProductData['code'],
						  'productPrice' => $arrProductData['price'],
						  'productID'    => $arrProductData['id'],
						  'quantity'     => $this->input->post('newQuantity', 1),
						  'orderID'      => $id
					 ));
				}
	  		}

			if($form->is_ok()){
				message::info('Заказ успешно сохранен', '/admin/order/edit?id='.$id);
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}

		$mdlProducts = new OrderProducts_Model();
		$arrOrderContent = $mdlProducts->db
       		->select(Array('self.id', 'self.productID', 'self.productName', 'self.productPrice', 'self.productCode', 'self.quantity', 'self.options',
       					   'product_uri' => 'catalog.uri', 'catalog.1c_code',
       					   'catalog.group_id', 'subtotal' => db::expr('`ml_self`.`quantity` * `ml_self`.`productPrice`'),
                           db::expr('ml_catalog.in_yml as level'),
                           db::expr('ml_catalog.in_isg as isg'),
                           db::expr('ml_catalog.in_5lb as 5lb'),
                           db::expr('ml_catalog.volume as volume'),
                           db::expr('ml_catalog_manufacturer.name as manufacturerName'),
                           db::expr('IF(ml_order.delivery = 2, 300, 100) as deliveryPrice'),
                           db::expr("IF(ml_order.delivery = 2, 'Доставка', 'Самовывоз') as deliveryType"),
//                           db::expr('SUM(ml_self.productPrice * ml_self.quantity) + IF(ml_order.delivery = 2, 300, 100) as totalPrice')
                ))
       		->from($mdlProducts->table_name)
       		->left_join('catalog', 'catalog.id', 'self.productID')
       		->left_join('catalog_manufacturer', 'catalog.manufacturer_id', 'catalog_manufacturer.id')
       		->left_join('order', 'order.id', 'self.orderID')
       		->where('orderID', $id)
       		->get()
       		->rows();

        foreach ($arrOrderContent as $key => $product) {
            $taste = json_decode($product['options']);
            $taste = $taste->taste;
            $id = $product['productID'];
            $taste = db::query("select * from `ml_catalog_tastes` where `productId` = '" . $id . "' AND `name` = '" . $taste . "' ")->row();
            if (isset($taste['article'])) {
                $arrOrderContent[$key]['productCode'] = $taste['article'];
            }
        }

		$fltTotalSum = 0;
		foreach($arrOrderContent as $k => $v){
			$fltTotalSum += $arrOrderContent[$k]['productPrice'] * $arrOrderContent[$k]['quantity'];
			$arrOrderContent[$k]['arrOptions'] = json_decode($v['options'], true);
		}

		$data = $form->get_form($data) + $data + $form->get_errors();

		$data['products'] = $arrOrderContent;
		$data['couponData'] = $arrCouponData;

		$plane_tree = Cache::instance()->get('catalog_plane_tree');

		$this->template->fltTotalSum = $fltTotalSum + $arrOrderContent[0]['deliveryPrice'];
		$this->template->groups = $plane_tree;
		$this->template->files = DBFile::select('order', $id);

  		$this->template->catalog_uri_base = $this->get_uri_base('catalog');
		$this->template->data = $data;
		$this->template->delivery = $arrDelivery;
		$this->template->payment = $arrPayment;
	}
}
?>
