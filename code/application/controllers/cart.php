<?php
/**
 * CSM
 *
 * @author Antuan
 * $created 23.09.2009
 *
 */
class Cart_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';
		session_start();

		parent::__construct();
	}

	/**
	 * Самостоятельный вывод в шаблоне
	 *
	 * @param id $page_id
	 */
	public function add() {
		$strMode = $this->input->get('ajax');
		$isAjax = $strMode=='true'?true:false;

		$this->template = new View('cart/add');


        $strCartID = $this->get_cart_id();
        $intProductID = $this->input->get('product_id',0);
        $intQuantity = intval($this->input->get('quantity',1));
        $arrOptions = $this->input->get('options',Array());
        $intQuantity = max($intQuantity, 1);


        $mdlProducts = new Catalog_Model();
        $arrData = $mdlProducts->db
        		        ->select("*")
        		        ->from($mdlProducts->table_name)
        		        ->where('id', $intProductID)
        		        ->where('active', '>=', 1)
        		        ->get()
        		        ->row();

		if(strlen($arrData['tastes'])>0){
			if(strlen(trim($arrOptions['taste'])) <= 0 || $arrOptions['taste']=="empty"){
				if($isAjax){
					echo('{"status":"error","message":"Необходимо выбрать вкус"}');
					$this->frame='empty';
		            exit();
				}
			}
		}

        if($arrData){
             $mdlCart = new Cart_Model();

             $arrCartContent = $mdlCart->db
             		->select('*')
             		->from($mdlCart->table_name)
             		->where('product_id', $intProductID)
             		->where('options', json_encode($arrOptions))
             		->where('session_id', $strCartID)
             		->get()
             		->row();

             $arrCartStatContent = $mdlCart->db
             		->select('*')
             		->from('catalog_cart_stat')
             		->where('date', "=", db::expr('CAST(NOW() as DATE)'))
             		->get()
             		->row();

             if($arrCartStatContent){
             	 $mdlCart->db->update('catalog_cart_stat', Array('cnt' => db::expr('cnt + 1'), 'cost' => db::expr('cost + '.str_replace(',', '.', $arrData['price']))), Array('id' => $arrCartStatContent['id']))->get();
             }else{
                 $mdlCart->db->insert('catalog_cart_stat', Array('date' => db::expr('CAST(NOW() as DATE)'), 'cnt' => 1, 'cost' => $arrData['price']))->get();
             }

             if($arrCartContent){
                 $mdlCart->update(Array('quantity' => $arrCartContent['quantity'] + $intQuantity), Array('id' => $arrCartContent['id']));
             }else{
             	 $fill = Array(
	                 'product_id' => $intProductID,
	                 'session_id' => $strCartID,
	                 'options'    => json_encode($arrOptions),
	                 'quantity'   => $intQuantity,
	                 'date_add'   => db::expr('now()')
	             );

	             if($this->input->get('delayed', false)){
	             	$fill['is_delay'] = 1;
	             }

	             $mdlCart->insert($fill);
             }
        }

		if($isAjax){
			$this->frame='empty';
            echo('{"status":"ok"}');
            exit();
		}else{
			$arrCartContent = $mdlCart->db
             		->select(Array('total' => db::expr('sum(ml_p.`price` * ml_self.`quantity`)')))
             		->from($mdlCart->table_name)
             		->left_join(Array('ml_p' => 'catalog'), Array('p.id' => 'self.product_id'))
             		->where('session_id', $strCartID)
             		->get()
             		->row();

   			if($arrCartContent['total'] > 1){
   				$_SESSION['needshowfullcart'] = true;
   			}else{
   				$_SESSION['needshowshortcart'] = true;
   			}
        	url::redirect($this->input->server('HTTP_REFERER', '/'));
        }
	}


	public function delete() {
		$this->template = new View('cart/add');

        $strCartID = $this->get_cart_id();
        $intItemID = $this->input->get('item_id',0);


        $mdlCart = new Cart_Model();

        $arrCartContent = $mdlCart->db
        		->select('*')
        		->from($mdlCart->table_name)
        		->where('id', $intItemID)
        		->where('session_id', $strCartID)
        		->get()
        		->row();

        if($arrCartContent){
            $mdlCart->delete(Array('id' => $intItemID));
        }

        url::redirect($this->input->server('HTTP_REFERER', '/'));
	}


	public function index(){
		$this->template = new View('cart/index');

		$strCartID = $this->get_cart_id();
		$mdlCart = new Cart_Model();

		$uri_base = $this->get_uri_base('cart');

		$post = $this->input->post();
		$arrFormData = array();
		$code="";

		$table = new Pages_Model();
		$table->info_content();
		$page = $table->db
			->from($table->table_name)
			->where('self.target', 'cart')
			->get()
			->row();

        $this->template->page=$page;
        $this->template->clientScrollTop=$this->input->get('scrollTop', 0);

		if(!$this->input->post('processOrder', false)){
	        $arrData=array(
	      		'text'    =>  "Корзина",
	      		'use_h1'  =>  true
	       	);
       	}else{
       		$arrData=array(
	      		'text'    =>  "Оформление заказа",
	      		'use_h1'  =>  true
	       	);
       	}
        $this->add_component('sectiontitle', 'sectiontitle',$arrData);
//        $this->del_component('leftmenu', 'leftmenu');

		/**
		 * Сохранение
		 */

		$arrCartContent = $mdlCart->db
       		->select(Array(
                    'catalog.id', 'catalog.name', 'catalog.code',
       				'subtotal' => db::expr('`ml_self`.`quantity` * IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)'),
                    'price' => db::expr('IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)'),
                )
            )
       		->from($mdlCart->table_name)
       		->left_join('catalog', 'catalog.id', 'self.product_id')
       		->where('session_id', $strCartID)
       		->where('is_delay', '<=' ,db::expr('0'))
       		->get()
       		->rows();

  		$arrPayments = $mdlCart->db
       		->select("*")
       		->from("payment_types")
       		->get()
       		->rows();

    	$arrDeliveryTypes = $mdlCart->db
       		->select("*")
       		->from("delivery_types")
       		->get()
       		->rows();

     	$arrDeliveryCosts = $mdlCart->db
       		->select("*")
       		->from("delivery_costs")
       		->get()
       		->rows();

		$form = new ValidateForm();
		$form->add_field('payment', array(new mod_list($arrPayments)), array('required'));
        $form->add_field('delivery', array(new mod_list($arrDeliveryCosts)), array('required'));
		$form->add_field('author', 'string', array('required','required'));
		$form->add_field('phone', 'string', array('required','length[1,50]'));
		$form->add_field('email', 'string', array('valid::email', 'length[1,100]'));
		$form->add_field('description', 'string');

//		$form->add_field('delivery_address', 'string', array('required','required'));

      	$intPaymentType = $this->input->post("payment",0);

      	$arrDelivery = Array();
      	$arrPayment = Array();

      	if(substr($this->input->post("delivery",""), 0, 3) == "rel"){
      		$intDeliveryCosts = $this->input->post(substr($this->input->post("delivery",""), 3),0);
      	}else{
            $intDeliveryCosts = $this->input->post("delivery",0);
      	}

        foreach($arrDeliveryCosts as $delivery){
        	if($delivery['id'] == $intDeliveryCosts){
        		$arrDelivery = $delivery;
        	}
        }

        foreach($arrPayments as $payment){
        	if($payment['id'] == $intPaymentType){
        		$arrPayment = $payment;
        	}
        }


  		$mdlCoupons = new Discount_Model();
    	$arrCouponData = $mdlCoupons->db
			->select(Array('coupon_id' => 'self.id', 'self.author', 'self.email', 'self.activation_code', 'cnt' => db::expr('count(ml_orders.coupon_id)')))
			->from($mdlCoupons->table_name)
			->left_join(Array($mdlCoupons->table_prefix.'orders' => 'order'), 'self.id', 'orders.coupon_id')
			->where('self.activation_code', Session::instance()->get('cart_coupon'))
			->where('self.active', '>', db::expr('0'))
			->having('cnt', '<=', db::expr('0'))
			->group_by('self.id')
			->get()
			->rows();


		if($this->input->post('processOrder', false) && !empty($post) && isset($post['order_cart']) && count($arrCartContent) > 0){
            if($_POST['delivery'] != 3) {
                $form->add_field('delivery_address', 'string', array('required','required'));
            }

			$arrFormData = $form->get_data();
			$form->validate();

			if($form->is_ok()){
				$mdlCoupons = new Discount_Model();
				$arrCartContent = $mdlCart->db
		        		->select(Array('cart_item_id' => 'self.id', 'name' => 'catalog.name', 'product_id' => 'catalog.id',
		        					   'quantity' => 'self.quantity',
                            'subtotal' => db::expr('`ml_self`.`quantity` * IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)'),
                            'price' => db::expr('IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)'),
		        					   'product_uri' => 'catalog.uri', 'product_group_id' => 'catalog.group_id'))
		        		->from($mdlCart->table_name)
		        		->left_join('catalog', 'catalog.id', 'self.product_id')
		        		->where('session_id', $strCartID)
		        		->where('is_delay', '<=' ,db::expr('0'))
		        		->get()
		        		->rows();


				$fill = $form->get_fill();
				$fill['cat'] = 5;
				$fill['date_create'] = db::expr('now()');

				$intSubtotal = 0;
	    		$mdlOrderProducts = new OrderProducts_Model();
	    		$mdlOrder = new Order_Model();
	   			foreach($arrCartContent as $key => $value){
	   			    $intSubtotal = $intSubtotal + ($value['price'] * $value['quantity']);
	   			}

				if(count($arrCartContent) > 0 && $intSubtotal > 20000){
					$fill['coupon_id'] = $arrCouponData[0]['coupon_id'];
				}

				if(!$result = $mdlOrder->insert($fill)) {
					$form->add_error('insert', 1);
				} else {
					Session::instance()->set('cart_coupon',false);

					$id = $result->insert_id();

					$fill = $form->get_form($arrFormData) + $arrFormData;

					$arrCartContent = $mdlCart->db
		        		->select(Array('catalog.id', 'catalog.name', 'catalog.code', 'self.quantity', 'self.options',
                                'price' => db::expr('IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)'),
		        					   'subtotal' => db::expr('`ml_self`.`quantity` * IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)')))
		        		->from($mdlCart->table_name)
		        		->left_join('catalog', 'catalog.id', 'self.product_id')
		        		->where('session_id', $strCartID)
		        		->where('is_delay', '<=' ,db::expr('0'))
		        		->get()
		        		->rows();

					$intSubtotal = 0;
		    		$mdlOrderProducts = new OrderProducts_Model();
		   			foreach($arrCartContent as $key => $value){
		   			    $intSubtotal = $intSubtotal + ($value['price'] * $value['quantity']);

		   				$frchFill=Array(
		   					'productID'    => $value['id'],
		   					'productName'  => $value['name'],
		   					'productCode'  => $value['code'],
		   					'productPrice' => $value['price'],
		   					'quantity'     => $value['quantity'],
		   					'options'      => $value['options'],
		   					'orderID'      => $id
		   				);

		   				$mdlOrderProducts->insert($frchFill);
		   			}

		   			$arrCartContent = $mdlCart->db
		        		->select(Array('cart_item_id' => 'self.id', 'name' => 'catalog.name', 'product_id' => 'catalog.id',
		        					   'quantity' => 'self.quantity',
                                'subtotal' => db::expr('`ml_self`.`quantity` * IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)'),
                                'price' => db::expr('IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)'),
		        					   'product_uri' => 'catalog.uri', 'product_group_id' => 'catalog.group_id'))
		        		->from($mdlCart->table_name)
		        		->left_join('catalog', 'catalog.id', 'self.product_id')
		        		->where('session_id', $strCartID)
		        		->where('is_delay', '<=' ,db::expr('0'))
		        		->get()
		        		->rows();

		   			$mdlCart->delete(Array('is_delay' => '0', 'session_id' => $strCartID));

					$subject = 'Поступил новый заказ №'.$id;
					$tplEmail= new View('order/email');
					$tplEmail->data=$fill;
					$tplEmail->data['id']=$id;
					$tplEmail->data['products'] = $arrCartContent;
					$tplEmail->data['subtotal'] = $intSubtotal;
					$tplEmail->data['delivery_adress'] = $_POST["delivery_address"];
					$tplEmail->data['description'] = $_POST["description"];
					$tplEmail->data['phone'] = $_POST["phone"];
					$tplEmail->data['email'] = $_POST["email"];
					$tplEmail->data['author'] = $_POST["author"];
					$message = $tplEmail->render();

					//email::send($to, $from, $subject, $message, TRUE);

					DBFile::save('order', $id, 'orgpropsfile');

					$mail = new PHPMailer();

					if(utf8::strlen(Kohana::config('cms.contacts.default_email_from_title'))>0){
						$mail->From = Kohana::config('cms.contacts.default_email_from');      // от кого
			        	$mail->FromName = Kohana::config('cms.contacts.default_email_from_title');
					}else{
						$mail->From = Kohana::config('cms.contacts.default_email_from');
					}
			          // от кого
			        $mail->AddAddress(Kohana::config('cms.contacts.order_email')); // кому - адрес, Имя
			        $mail->AddAddress(Kohana::config('cms.contacts.order_copy_email')); // кому - адрес, Имя
			        $mail->AddAddress('korzinazakaza@gmail.com'); // кому - адрес, Имя
//			        $mail->AddAddress('kasp89s@gmail.com'); // кому - адрес, Имя

			        $mail->IsHTML(true);        // выставляем формат письма HTML
			        $mail->Subject = $subject;  // тема письма

			        // если был файл, то прикрепляем его к письму
			        if(isset($_FILES['orgpropsfile'])) {
		                 if($_FILES['orgpropsfile']['error'] == 0){
		                    $mail->AddAttachment($_FILES['orgpropsfile']['tmp_name'], text::transliterate_to_ascii($_FILES['orgpropsfile']['name']));
		                 }
			        }

			        $mail->Body = $message;

			        !$mail->Send();
				}

			}

			if($form->is_ok()){
				Session::instance()->set('cart_rows', $arrCartContent);

				$arrProductsId = Array();
		  		foreach($arrCartContent as $key => $value){
		  			$arrProductsId[] = $value['product_id'];
		  		}

		  		Session::instance()->set('cart_productsFiles', DBFile::select_all('catalog', $arrProductsId));
		  		Session::instance()->set('cart_groups', Cache::instance()->get('catalog_plane_tree'));
		  		Session::instance()->set('cart_catalog_uri_base', $this->get_uri_base('catalog'));

				cookie::set('author', $fill['author'], Kohana::config('cookie.expire'));

				url::redirect($uri_base.'/success');
			} else {
				$arrFormData['error_form'] = 1;
			}
		}

		if($this->input->post('processOrder', false)){
			$order_data = $form->get_form($arrFormData) + $arrFormData + $form->get_errors();

			$this->template->order_data = $order_data;
			$this->template->delivery = $arrDelivery;
			$this->template->payment = $arrPayment;
		}else{
			$this->template = new View('cart/index');
		}

		$plane_tree = Cache::instance()->get('catalog_plane_tree');

		$arrCartContent = $mdlCart->db
        		->select(Array('cart_item_id' => 'self.id', 'name' => 'catalog.name', 'product_id' => 'catalog.id',
                    'price' => db::expr('IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)'),
        					   'quantity' => 'self.quantity', 'options' => 'self.options',
                               'subtotal' => db::expr('`ml_self`.`quantity` * IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`)'),
        					   'product_uri' => 'catalog.uri', 'product_group_id' => 'catalog.group_id'))
        		->from($mdlCart->table_name)
        		->left_join('catalog', 'catalog.id', 'self.product_id')
        		->where('session_id', $strCartID)
        		->where('is_delay', '<=' ,db::expr('0'))
        		->get()
        		->rows();

    	if(count($arrCartContent) <= 0 && count($arrCartDelayedContent) > 0){
  			url::redirect($uri_base.'/delayed');
  		}

  		$arrProductsId = Array();
  		foreach($arrCartContent as $key => $value){
  			$arrProductsId[] = $value['product_id'];
  			$arrCartContent[$key]['arrOptions'] = json_decode($value['options'], true);
  		}

        $this->template->data = Array();
  		$this->template->data['rows']=$arrCartContent;
        $this->template->delivery = Settings_Model::$_delivery_date;
        $this->template->hour = date('H', time());
  		$this->template->data['delayed_rows']=$arrCartDelayedContent;
  		$this->template->data['couponData'] = $arrCouponData;

  		if(Session::instance()->get('cart_coupon') != "" && count($arrCouponData) <= 0){
            $this->template->data['couponError'] = true;
            Session::instance()->set('cart_coupon',false);
  		}

  		if(Session::instance()->get('cart_coupon') != "" && count($arrCouponData) > 0){
  			$this->template->data['couponSumm'] = 1000;
  		}

  		$this->template->productsFiles = DBFile::select_all('catalog', $arrProductsId);

  		$this->template->groups = $plane_tree;
  		$this->template->catalog_uri_base = $this->get_uri_base('catalog');
	}


	public function delayed(){
		$this->template = new View('cart/delayed');

		$this->del_component('leftmenu', 'leftmenu');

      	$arrData=array(
      		'text'    =>  "Избранное",
      		'use_h1'  =>  true
       	);
        $this->add_component('sectiontitle', 'sectiontitle',$arrData);
        $this->add_attribute('title', 'Избранное');

		$strCartID = $this->get_cart_id();
		$mdlCart = new Cart_Model();

		$uri_base = $this->get_uri_base('cart');

		$post = $this->input->post();
		$arrFormData = array();
		$code="";

		$plane_tree = Cache::instance()->get('catalog_plane_tree');

		$arrCartContent = $mdlCart->db
        		->select(Array('cart_item_id' => 'self.id', 'name' => 'catalog.name', 'price' => 'catalog.price', 'product_id' => 'catalog.id',
        					   'quantity' => 'self.quantity', 'subtotal' => db::expr('`ml_self`.`quantity` * `ml_catalog`.`price`'),
        					   'product_uri' => 'catalog.uri', 'product_group_id' => 'catalog.group_id'))
        		->from($mdlCart->table_name)
        		->left_join('catalog', 'catalog.id', 'self.product_id')
        		->where('session_id', $strCartID)
        		->where('is_delay', '<=' ,db::expr('0'))
        		->get()
        		->rows();

  		$arrCartDelayedContent = $mdlCart->db
        		->select(Array('cart_item_id' => 'self.id', 'name' => 'catalog.name', 'price' => 'catalog.price', 'product_id' => 'catalog.id',
        					   'quantity' => 'self.quantity', 'subtotal' => db::expr('`ml_self`.`quantity` * `ml_catalog`.`price`'),
        					   'product_uri' => 'catalog.uri', 'product_group_id' => 'catalog.group_id'))
        		->from($mdlCart->table_name)
        		->left_join('catalog', 'catalog.id', 'self.product_id')
        		->where('session_id', $strCartID)
        		->where('is_delay', '>' ,db::expr('0'))
        		->get()
        		->rows();

    	if(count($arrCartDelayedContent) <= 0){
  			url::redirect($uri_base);
  		}

  		$arrProductsId = Array();
  		foreach($arrCartDelayedContent as $key => $value){
  			$arrProductsId[] = $value['product_id'];
  		}

        $this->template->data = Array();
  		$this->template->data['rows']=$arrCartContent;
  		$this->template->data['delayed_rows']=$arrCartDelayedContent;

  		$this->template->productsFiles = DBFile::select_all('catalog', $arrProductsId);

  		$this->template->groups = $plane_tree;
  		$this->template->catalog_uri_base = $this->get_uri_base('catalog');
	}


	public function group() {
		$this->template = new View('cart/add');

        $strCartID = $this->get_cart_id();
        $arrItemQuantity = $this->input->post('quantity',Array());
        $arrMarkers = $this->input->post('marked',Array());

		if($this->input->post('couponNumber',false)){
        	Session::instance()->set('cart_coupon', $this->input->post('couponNumber',false));
  		}

        $mdlCart = new Cart_Model();

        foreach($arrItemQuantity as $key => $value){
            $mdlCart->update(Array('quantity' => $value), Array('id' => $key, 'session_id' => $strCartID));
        }

        $fill = Array();

        if($this->input->post('delay', false)){
        	$fill = Array('is_delay' => 1, 'quantity' => 1);
        }

        if($this->input->post('notdelay', false)){
        	$fill = Array('is_delay' => 0);
        }

		if(count($fill) > 0){
	        foreach($arrMarkers as $key => $value){
	            $mdlCart->update($fill, Array('id' => $key, 'session_id' => $strCartID));
	        }
        }

        if($this->input->post('delete', false)){
        	foreach($arrMarkers as $key => $value){
	            $mdlCart->delete(Array('id' => $key, 'session_id' => $strCartID));
	        }
        }

        $strReferrer = $this->input->server('HTTP_REFERER', false);
        $arrReferrer = parse_url($strReferrer);
        $arrQuery = Array();
        if(isset($arrReferrer['query'])){
	        parse_str($arrReferrer['query'], $arrQuery);
	        unset($arrQuery['scrollTop']);
        }

        $strReferrer = str_replace(Kohana::config('core.url_suffix'), '', $arrReferrer['path']).(count($arrQuery) > 0 ? '?'.implode('&', $arrQuery):'');

        if($strReferrer){
        	if(strpos($strReferrer, '?')!==false){
        		$strReferrer = $strReferrer . '&scrollTop='.$this->input->post('clientScrollTop', 0);
        	}else{
        		$strReferrer = $strReferrer . '?scrollTop='.$this->input->post('clientScrollTop', 0);
        	}
        }else{
        	$strReferrer = '/';
        }

        url::redirect($strReferrer);
	}


	public function item_delete() {
        $strCartID = $this->get_cart_id();
        $id = $this->input->get('id',Array());

        $mdlCart = new Cart_Model();

	    $mdlCart->delete(Array('id' => $id, 'session_id' => $strCartID));

	    $strReferrer = $this->input->server('HTTP_REFERER', false);

	    url::redirect($strReferrer);
	}

	public function item_update() {
        $strCartID = $this->get_cart_id();
        $id = $this->input->get('id',false);
        $productId = $this->input->get('productId',false);
        $taste = $this->input->get('taste', 'без вкуса');
        if (empty($taste) || $taste == '') {
            $taste = 'без вкуса';
        }
        $quantity = $this->input->get('quantity', 0);

        $catalog = new CatalogTastes_Model();
        $tastes = $catalog->db
            ->select(array('count' => db::expr('IF(`ml_self`.`count2` > 0, `ml_self`.`count2`, `ml_self`.`count`)')))
            ->from($catalog->table_name)
            ->where('self.productId', $productId)
            ->where('self.name', $taste)
            ->get()
            ->row();

        if ($tastes['count'] < $quantity) {
            echo json_encode(array('error' => 'Такого количества нет на складе!'));
            exit;
        }

        $mdlCart = new Cart_Model();

	    $mdlCart->update(Array('quantity' => $quantity), Array('id' => $id, 'session_id' => $strCartID));

	}

	public function get_ajax_cart() {
		$this->frame = 'empty';
        $strCartID = $this->get_cart_id();
        $intDeliveryId = $this->input->get('delivery',false);

        $mdlCart = new Cart_Model();

	    $arrCartContent = $mdlCart->db
        		->select(Array(
                    'count' => db::expr('count(1)'),
                    'prodCount' => db::expr('sum(ml_self.quantity)'),
                    'totalSum' => db::expr('sum(IF(`ml_catalog`.`availability2` > 0, `ml_catalog`.`price`, `ml_catalog`.`priceSupplier`) * ml_self.quantity)')
                ))
        		->from($mdlCart->table_name)
        		->left_join('catalog', 'catalog.id', 'self.product_id')
        		->where('self.session_id', $strCartID)
        		->where('self.is_delay', '<=' ,db::expr('0'))
        		->get()
        		->row();

		$deliveryCost = $mdlCart->db
       		->select(Array('total' => 'cost'))
       		->from('delivery_costs')
       		->where('id', $intDeliveryId)
       		->get()
       		->row();

       	$arrResult = Array(
       		'count'         => $arrCartContent['count'],
       		'prodCount'     => $arrCartContent['prodCount'],
       		'prodCost'      => $arrCartContent['totalSum'],
       		'deliveryCost'  => floatval($deliveryCost['total']),
       		'totalSum'      => floatval($deliveryCost['total']) + floatval($arrCartContent['totalSum'])
       	);

       	echo(json_encode($arrResult));
	}

	public function success() {
		$this->template = new View('order/success');
		$this->template->data=Array('name' => $this->input->cookie('author'));

        $arrData=array(
      		'text'    =>  "Заказ оформлен!",
      		'use_h1'  =>  true
       	);
        $this->add_component('sectiontitle', 'sectiontitle',$arrData);
        $this->del_component('leftmenu', 'leftmenu');

        $this->template->data = Array();
  		$this->template->data['rows']=Session::instance()->get('cart_rows');
  		$this->template->productsFiles = Session::instance()->get('cart_productsFiles');
  		$this->template->groups = Session::instance()->get('cart_groups');
  		$this->template->catalog_uri_base = Session::instance()->get('cart_catalog_uri_base');

  		Session::instance()->delete('cart_rows');
  		Session::instance()->delete('cart_productsFiles');
  		Session::instance()->delete('cart_groups');
  		Session::instance()->delete('cart_catalog_uri_base');
	}


	private function get_cart_id(){
		$strCartID = $this->input->cookie('cart_id', md5(microtime(true)+rand(1,10000)));
		cookie::set('cart_id', $strCartID, Kohana::config('cookie.expire'));

		return $strCartID;
	}
}
?>
