<?php
/**
 * Интернет-приемная главы
 *
 */
class Callback_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Обратный звонок';

		if(Acl::instance()->is_allowed('callback_add')){
			$this->menu = array(
				array('url'=>'/admin/callback', 'section'=>'Все заказы звонка'),
				array('url'=>'/admin/catcallback', 'section'=>'Все статусы'),
				array('url'=>'/admin/catcallback/edit', 'section'=>'Добавить статус', 'title'=>'Статус')
			);
		} else {
			$this->menu = array(
				array('url'=>'/admin/callback', 'section'=>'Все заказы звонка'),
				array('url'=>'/admin/catcallback', 'section'=>'Все статусы'),
			);
		}

		parent::__construct();
	}


	/**
	 * Список заказов звонка
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('callback_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/callback/index');

		$cat          = (int)$this->input->get('cat');

		$table = new Callback_Model;
		$table->info_cat();
		$table->db
			->select(Array('self.id', 'self.author'))
			->from($table->table_name);

		if($cat) $table->db->where('self.cat', $cat);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('self.id' => 'desc', 'self.name', 'cat_name');
		$data = &$tm->show();

		form_filter::fill_list('cat', $cat, $data, $table->get_cat());

		$this->template->main = $data;
	}


	/**
	 * Редактирование заказов звонка
	 *
	 */
	public function edit() {
		if(!Acl::instance()->is_allowed('callback_edit'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/callback/edit');

		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$post = $this->input->post();

		if(!$id)
			message::error('Некорректный идентификатор (id) заказа звонка', '/admin/callback');


		/**
		 * Правила валидации
		 */
		$table = new Callback_Model();

		$table_cats = new Catcallback_Model();
		$cats = $table_cats->db
				->select(Array('id', 'name'))
				->from($table_cats->table_name)
				->order_by('name')
				->where('active',1)
				->get()
				->rows();

		$form = new ValidateForm();
		$form->add_field('cat', array(new mod_list($cats)), array('required'));
		$form->add_field('author', 'string', array('required'));
		$form->add_field('phone', 'string', array('required','length[1,50]'));
		$form->add_field('email', 'string', array('valid::email', 'length[1,100]'));
		$form->add_field('description', 'string');



		/**
		 * Вспомогательные данные
		 */
		$table = new Callback_Model();
		$table->info_cat();

		if($id){
			$data = $table->db
				->select('self.*')
				->where('self.id', $id)
				->from($table->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) заказа звонка', '/admin/callback');
		} else
			$data = array();


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
						log::add('callback', 'Редактирование заказа звонка id='.$id);
					}

				}
			}

			if($form->is_ok()){
				message::info('Заказ звонка успешно сохранен', '/admin/callback/edit?id='.$id);
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$this->template->data = $data;
	}
}
?>