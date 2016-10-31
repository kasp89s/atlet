<?php
/**
 * Статусы
 *
 */
class Catcallback_Controller extends Admin_Controller {

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
	 * Список статусов
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('callback_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/catcallback/index');

		$table = new Catcallback_Model;
		$table->db
			->select(Array('id', 'name', 'active'))
			->from($table->table_name);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('id' => 'desc', 'name');

		$this->template->main = $tm->show();
	}


	/**
	 * Добавление/Редактирование статусов
	 *
	 */
	public function edit() {
		$this->template = new View('admin/catcallback/edit');

		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$post = $this->input->post();


		/**
		 * Проверка прав доступа
		 */
		if($id){
			if(!Acl::instance()->is_allowed('callback_edit'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		} else {
			if(!Acl::instance()->is_allowed('callback_add'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		}


		/**
		 * Правила валидации
		 */
		$form = new ValidateForm();
		$form->add_field('name', 'string', array('required', 'length[1,170]'));
		$form->add_field('description', 'html', 'required');
		$form->add_field('active', 'checkbox');


		/**
		 * Вспомогательные данные
		 */
		$table = new Catcallback_Model();
		if($id){
			$data = $table->db
				->select('*')
				->where('id', $id)
				->from($table->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) статуса', '/admin/catcallback');
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
				$fill['description'] = str_replace('../../', '/', $fill['description']);

				if($id) {
					if(!$result = $table->update($fill, array('id'=>$id)))
						$form->add_error('update', 1);
					else
						log::add('callback', 'Редактирование статуса id='.$id);

				} else {
					$fill['date_create'] = db::expr('now()');

					if(!$result = $table->insert($fill)) {
						$form->add_error('insert', 1);
					} else {
						$id = $result->insert_id();
						log::add('callback', 'Добавление статуса id='.$id);
					}
				}
			}

			if($form->is_ok()){
				message::info('Статус успешно сохранена', '/admin/catcallback');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$data['description_editor'] = Editor::factory("admin")->set_fieldname("description")->set_value($data['description'])->set_height(400)->set_width(650)->render(FALSE, TRUE);
		$this->template->data = $data;
	}


	/**
	 * Групповое сохранение статусов
	 *
	 */
	public function group() {
		if(!Acl::instance()->is_allowed('callback_edit'))
			message::error('Нет прав доступа для сохранения', '/admin/catcallback');


		$post = $this->input->post();
		$ids = array();
		$act = array();

		if(isset($post['ids']))
			$ids = arr::int_array($post['ids']);
		if(isset($post['act']))
			$act = arr::int_array($post['act']);


		$table = new Catcallback_Model();

		$set = array_intersect($ids, $act);
		if(count($set))
			$table->update(array('active' => 1), array('id' => $set));

		$clear = array_diff($ids, $act);
		if(count($clear))
			$table->update(array('active' => 0), array('id' => $clear));

		sort($ids);
		log::add('callback', 'Групповое сохранение статусов id=[' . implode(',', $ids) . ']');

		message::info('Данные успешно сохранены', '/admin/catcallback');
	}

	/**
	 * Удаление статуса
	 *
	 */
	public function delete() {
		if(!Acl::instance()->is_allowed('callback_del'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$id = (int) $this->input->get('id');

		$table = new Catcallback_Model();
		$table->delete(array('id' => $id));

		log::add('callback', 'Удаление статуса id='.$id);

		message::info('Статус успешно удален', '/admin/catcallback');
	}
}
?>