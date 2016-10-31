<?php
/**
 * Форма заказа обратного звонка
 *
 * @author Sinner
 */
class Discount_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}

	/**
	 * Форма обратной связи
	 *
	 */
	public function index() {
		$this->template = new View('discount/add');
		$uri_base = $this->get_uri_base('discount');

		$post = $this->input->post();
		$data = array();

		$table = new Pages_Model();
		$table->info_content();
		$page = $table->db
			->from($table->table_name)
			->where('self.target', 'discount')
			->get()
			->row();

        $this->template->page=$page;

		/**
		 * Правила валидации
		 */
		$table = new Discount_Model();

		$form = new ValidateForm();
		$form->add_field('author', 'string', array('required'));
		$form->add_field('email', 'string', array('valid::email', 'length[1,100]'));


		/**
		 * Сохранение
		 */


		if(!empty($post)){
			$data = $form->get_data();
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();

				$fill['date_create'] = new Database_Expr('now()');
				$fill['activation_code'] = md5(microtime(true));

				if(!$result = $table->insert($fill)) {
					$form->add_error('insert', 1);
				} else {
					$id = $result->insert_id();

					$from    = Array(Kohana::config('cms.contacts.default_email_from'),'Luxpodarki.ru');
					$subject = 'Активация сертификата на скидку';
					$tplEmail= new View('discount/email_reg');
					$tplEmail->data=$fill;
					$tplEmail->data['id']=$id;
					$tplEmail->data['uribase'] = $uri_base;
					$message = $tplEmail->render();

					email::send($fill['email'], $from, $subject, $message, TRUE);
				}

			}

			if($form->is_ok()){
				url::redirect($uri_base.'/success');
			} else {
				$data['error_form'] = 1;
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$this->template->data = $data;
	}


	/**
	 * Форма обратной связи
	 *
	 */
	public function send_json() {
		$this->frame = "empty";
		$this->template = new View('discount/empty');

		$uri_base = $this->get_uri_base('discount');

		$post = $this->input->post();
		$data = array();

		$table = new Pages_Model();
		$table->info_content();
		$page = $table->db
			->from($table->table_name)
			->where('self.target', 'discount')
			->get()
			->row();

        $this->template->page=$page;

		/**
		 * Правила валидации
		 */
		$table = new Discount_Model();

		$form = new ValidateForm();
		$form->add_field('author', 'string', array('required'));
		$form->add_field('email', 'string', array('valid::email', 'length[1,100]', 'required'));


		/**
		 * Сохранение
		 */


		if(!empty($post)){
			$data = $form->get_data();
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();

				$fill['date_create'] = new Database_Expr('now()');
				$fill['activation_code'] = md5(microtime(true));

				if(!$result = $table->insert($fill)) {
					$form->add_error('insert', 1);
				} else {
					$id = $result->insert_id();

					$from    = Array(Kohana::config('cms.contacts.default_email_from'),'Luxpodarki.ru');
					$subject = 'Активация сертификата на скидку';
					$tplEmail= new View('discount/email_reg');
					$tplEmail->data=$fill;
					$tplEmail->data['id']=$id;
					$tplEmail->data['uribase'] = $uri_base;
					$message = $tplEmail->render();

					email::send($fill['email'], $from, $subject, $message, TRUE);
				}

			}

			if(!$form->is_ok()){
				$data['error_form'] = 1;
				$data = $form->get_form($data) + $data + $form->get_errors();

				echo(json_encode($data));
			}else{				$data['error_form'] = 0;
				echo(json_encode($data));			}
		}



	}


	/**
	 * Форма обратной связи
	 *
	 */
	public function activate() {

		$uri_base = $this->get_uri_base('discount');

		$table = new Discount_Model();

		$arrResult = $table->db
						->select('*')
						->from($table->table_name)
						->where('activation_code',$this->input->get('code',''))
						->get()
						->rows();

		if(count($arrResult)<=0){			$this->template = new View('discount/activation_empty');		}elseif($arrResult['active'] > 0){            $this->template = new View('discount/activation_already');		}else{
			$arrResult = $arrResult[0];
			$this->template = new View('discount/activation_success');

			$strCouponeCode = CRC32(microtime()).$arrResult['id'];			$table->update(Array('activation_code' => $strCouponeCode, 'active' => '1'), Array('activation_code'=>$this->input->get('code','')));

			$from    = Array(Kohana::config('cms.contacts.default_email_from'),'Luxpodarki.ru');
			$subject = 'Сертификат на скидку';
			$tplEmail= new View('discount/email_coupone');
			$tplEmail->data=$arrResult;
			$tplEmail->data['uribase'] = $uri_base;
			$tplEmail->data['code'] = $strCouponeCode;
			$message = $tplEmail->render();

			email::send($arrResult['email'], $from, $subject, $message, TRUE);

			$this->template->data=$arrResult;
			$this->template->data['uribase'] = $uri_base;
			$this->template->data['code'] = $strCouponeCode;		}
	}

	/**
	 * Сообщение о успешном результате работы
	 *
	 */
	public function success() {
		$this->template = new View('discount/success');
	}
}
?>