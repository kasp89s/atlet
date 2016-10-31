<?php
/**
 * Полезные ссылки
 *
 */
class Links_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Ссылки';

		if(Acl::instance()->is_allowed('links_add')){
			$this->menu = array(
				array('url'=>'/admin/links', 'section'=>'Все ссылки'),
				array('url'=>'/admin/links/edit', 'section'=>'Добавить ссылку', 'title'=>'Ссылка'),
			);
		} else {
			$this->menu = array(
				array('url'=>'/admin/links', 'section'=>'Все ссылки'),
			);
		}

		/**
		 * Проверка доступа администрации
		 */


		parent::__construct();
	}


	/**
	 * Список ссылок
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('links_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/links/index');

		$table = new Links_Model;
		$table->db
			->select('self.*')
			->from($table->table_name);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('self.id' => 'desc', 'self.name', 'self.sort');
		$data = &$tm->show();

		$this->template->main = $data;
	}


	/**
	 * Добавление/Редактирование ссылок
	 *
	 */
	public function edit() {
		$this->template = new View('admin/links/edit');

		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$post = $this->input->post();

		/**
		 * Проверка прав доступа
		 */
		if($id){
			if(!Acl::instance()->is_allowed('links_edit'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		} else {
			if(!Acl::instance()->is_allowed('links_add'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		}


		/**
		 * Правила валидации
		 */

		$form = new ValidateForm();
		$form->add_field('name', 'string', array('required', 'length[1,100]'));
		$form->add_field('url', 'string', array('required', 'length[1,250]', 'valid::url'));
		$form->add_field('active', 'checkbox');
		$form->add_field('sort', 'int', array());


		/**
		 * Вспомогательные данные
		 */
		$table = new Links_Model();
		if($id){
			$data = $table->db
				->select('*')
				->where('id', $id)
				->from($table->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) ссылки', '/admin/links');
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
						DBFile::save('links', $id, 'image');

						log::add('links', 'Редактирование ссылки id='.$id);
					}

				} else {
					$fill['date_create'] = db::expr('now()');

					if(!$result = $table->insert($fill)) {
						$form->add_error('insert', 1);
					} else {
						$id = $result->insert_id();

						log::add('links', 'Добавление ссылки id='.$id);
					}
				}
			}

			if($form->is_ok()){
				Cache::instance()->delete_tag('links');

				message::info('Ссылка успешно сохранена', '/admin/links');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$this->template->data = $data;
	}


	/**
	 * Групповое сохранение ссылок
	 *
	 */
	public function group() {
		if(!Acl::instance()->is_allowed('links_edit'))
			message::error('Нет прав доступа для сохранения', '/admin/links');


		$post = $this->input->post();
		$ids = array();
		$act = array();

		if(isset($post['ids']))
			$ids = arr::int_array($post['ids']);
		if(isset($post['act']))
			$act = arr::int_array($post['act']);


		$table = new Links_Model();

		foreach($post['sort'] as $key => $value){			$table->update(array('sort' => $value), array('id' => $ids[$key]));
		}

		$set = array_intersect($ids, $act);
		if(count($set))
			$table->update(array('active' => 1), array('id' => $set));

		$clear = array_diff($ids, $act);
		if(count($clear))
			$table->update(array('active' => 0), array('id' => $clear));

		Cache::instance()->delete_tag('links');

		sort($ids);
		log::add('links', 'Групповое сохранение ссылок id=[' . implode(',', $ids) . ']');

		message::info('Данные успешно сохранены', '/admin/links');
	}


	/**
	 * Удаление ссылки
	 *
	 */
	public function delete() {
		if(!Acl::instance()->is_allowed('links_del'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$id = (int) $this->input->get('id');

		$table = new Links_Model();
		$table->delete(array('id' => $id));

		Cache::instance()->delete_tag('links');

		log::add('links', 'Удаление ссылки id='.$id);

		message::info('Ссылка успешно удалена', '/admin/links');
	}
}
?>