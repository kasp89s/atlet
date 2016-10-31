<?php
/**
 * Заказы
 *
 * @author Sinner
 */
class Order_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}


	/**
	 * Форма заказа
	 *
	 */
	public function index() {
		$this->template = new View('order/add');
		$uri_base = $this->get_uri_base('order');

		$post = $this->input->post();
		$data = array();

		$table = new Pages_Model();
		$table->info_content();
		$page = $table->db
			->from($table->table_name)
			->where('self.target', 'order')
			->get()
			->row();

        $this->template->page=$page;

		/**
		 * Правила валидации
		 */
		$table = new Order_Model();

		$form = new ValidateForm();
		$form->add_field('code', new mod_string2($code), array('required','length[1,50]'));
		$form->add_field('quantity',  new mod_int2(0), array('required','value[1,50]'));
		$form->add_field('author', 'string', array('required','required'));
		$form->add_field('phone', 'string', array('required','length[1,50]'));
		$form->add_field('email', 'string', array('required','valid::email', 'length[1,100]'));
		$form->add_field('description', 'string');
		$form->add_field('captcha', array('string', 'no_fill'), array('required'));


		/**
		 * Сохранение
		 */
		if(!empty($post)){
			$data = $form->get_data();
			$form->validate();

            if(!Captcha::valid($post['captcha']))
                $form->add_error('captcha');

			if($form->is_ok()){
				$fill = $form->get_fill();
				$fill['cat'] = 5;
				$fill['date_create'] = new Database_Expr('now()');

				if(!$result = $table->insert($fill)) {
					$form->add_error('insert', 1);
				} else {
					$id = $result->insert_id();

					log::add('order', 'Добавление заказа id='.$id);
					$to      = Kohana::config('cms.contacts.order_email');  // Address can also be array('to@example.com', 'Name')
					if(UTF8::strlen(Kohana::config('cms.contacts.default_email_from_title'))>0){
						$from    = Array(Kohana::config('cms.contacts.default_email_from'),Kohana::config('cms.contacts.default_email_from_title'));
					}else{
						$from    = Kohana::config('cms.contacts.default_email_from');
					}
					$subject = 'Поступил новый заказ №'.$id;
					$tplEmail= new View('order/email');
					$tplEmail->data=$fill;
					$tplEmail->data['id']=$id;
					$message = $tplEmail->render();

					email::send($to, $from, $subject, $message, TRUE);
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
	 * Сообщение о успешном результате работы
	 *
	 */
	public function success() {
		$this->template = new View('order/success');
	}
}
?>