<?php
/**
 * Настройки CMS
 *
 */
class Settings_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Настройки';

		if(!Acl::instance()->is_allowed('admin')){
			message::error('Нет прав доступа к данному разделу', '/admin');
		}

		$this->menu = array(
			array('url'=>'/admin/settings/seo', 'section'=>'SEO'),
			array('url'=>'/admin/settings/phone_head', 'section'=>'Телефон в шапке'),
			array('url'=>'/admin/settings/contacts', 'section'=>'Контактные данные для системы'),
		);

		parent::__construct();
	}


	public function index() {
		$this->template = new View('admin/index');
	}


	/**
	 * Редактирование настроек SEO
	 *
	 */
	public function seo() {
		$this->template = new View('admin/settings/seo');

		$post = $this->input->post();
		$context = 'seo';
		/**
		 * Правила валидации
		 */
		$form = new ValidateForm();
		$form->add_field('title', 'string', array('required'));
		$form->add_field('keywords', 'string', array('required'));
		$form->add_field('description', 'string', array('required'));


		/**
		 * Вспомогательные данные
		 */
		$table = new Settings_Model();
		$data = array();
		$rows = $table->db
			->select('*')
			->where('context', $context)
			->from($table->table_name)
			->get()
			->rows();

		foreach ($rows as $v)
			$data[$v['key']] = $v['value'];



		/**
		 * Сохранение
		 */
		if(!empty($post)){
			$data = $form->get_data();
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();
				foreach ($fill as $k=>$v){
					$row = array(
						'context'       => $context,
						'key'           => $k,
						'value'         => $v,
					);

					$table->delete(array('context' => $context, 'key' => $k));
					$table->insert($row);
				}

			}

			if($form->is_ok()){
				Cache::instance()->delete_all();

				$smarty = new MY_Smarty;
				$smarty->clear_all_cache();

				message::info('Настройки успешно сохранены');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}

		$data = $form->get_form($data) + $data + $form->get_errors();
		$this->template->data = $data;
	}

	/**
	 * Редактирование настроек SEO
	 *
	 */
	public function phone_head() {
		$this->template = new View('admin/settings/phone_head');

		$post = $this->input->post();
		$context = 'phone_head';
		/**
		 * Правила валидации
		 */
		$form = new ValidateForm();
		$form->add_field('city_code', 'string', array('required', 'length[1,10]'));
		$form->add_field('phone', 'string', array('required', 'length[1,10]'));


		/**
		 * Вспомогательные данные
		 */
		$table = new Settings_Model();
		$data = array();
		$rows = $table->db
			->select('*')
			->where('context', $context)
			->from($table->table_name)
			->get()
			->rows();

		foreach ($rows as $v)
			$data[$v['key']] = $v['value'];



		/**
		 * Сохранение
		 */
		if(!empty($post)){
			$data = $form->get_data();
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();
				foreach ($fill as $k=>$v){
					$row = array(
						'context'       => $context,
						'key'           => $k,
						'value'         => $v,
					);

					$table->delete(array('context' => $context, 'key' => $k));
					$table->insert($row);
				}

			}

			if($form->is_ok()){
				Cache::instance()->delete_all();

				$smarty = new MY_Smarty;
				$smarty->clear_all_cache();

				message::info('Настройки успешно сохранены');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}

		$data = $form->get_form($data) + $data + $form->get_errors();
		$this->template->data = $data;
	}

	/**
	 * Редактирование настроек SEO
	 *
	 */
	public function contacts() {
		$this->template = new View('admin/settings/contacts');

		$post = $this->input->post();
		$context = 'contacts';
		/**
		 * Правила валидации
		 */
		$form = new ValidateForm();
		$form->add_field('order_email', 'string', array('required', 'valid::email', 'length[1,100]'));
		$form->add_field('order_copy_email', 'string', array('required', 'valid::email', 'length[1,100]'));
		$form->add_field('callback_email', 'string', array('required', 'valid::email', 'length[1,100]'));
		$form->add_field('default_email_from_title', 'string', array('length[1,100]'));
		$form->add_field('retailPercent', 'string', array('length[1,100]'));
		$form->add_field('default_email_from', 'string', array('required', 'valid::email', 'length[1,100]'));


		/**
		 * Вспомогательные данные
		 */
		$table = new Settings_Model();
		$data = array();
		$rows = $table->db
			->select('*')
			->where('context', $context)
			->from($table->table_name)
			->get()
			->rows();

		foreach ($rows as $v)
			$data[$v['key']] = $v['value'];



		/**
		 * Сохранение
		 */
		if(!empty($post)){
			$data = $form->get_data();
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();
				foreach ($fill as $k=>$v){
					$row = array(
						'context'       => $context,
						'key'           => $k,
						'value'         => $v,
					);
					$table->delete(array('context' => $context, 'key' => $k));
					$table->insert($row);
				}

			}

			if($form->is_ok()){
				Cache::instance()->delete_all();

				$smarty = new MY_Smarty;
				$smarty->clear_all_cache();

				message::info('Настройки успешно сохранены');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}

		$data = $form->get_form($data) + $data + $form->get_errors();
		$this->template->data = $data;
	}
}
?>
