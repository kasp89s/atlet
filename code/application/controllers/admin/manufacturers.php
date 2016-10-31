<?php
/**
 * Новости
 *
 */
class Manufacturers_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Производители';

		if(Acl::instance()->is_allowed('manufacturers_add')){
			$this->menu = array(
				array('url'=>'/admin/manufacturers', 'section'=>'Все производители'),
				array('url'=>'/admin/manufacturers/edit', 'section'=>'Добавить производителя', 'title'=>'Производитель'),
			);
		} else {
			$this->menu = array(
				array('url'=>'/admin/manufacturers', 'section'=>'Все производители'),
			);
		}

		parent::__construct();
	}


	/**
	 * Список новостей
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('manufacturers_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/manufacturers/index');


		$table = new Manufacturers_Model;
		$table->db
			->select(Array('self.id', 'self.name'))
			->from($table->table_name);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('self.id' => 'desc', 'self.name');
		$data = &$tm->show();


		$this->template->main = $data;
	}


	/**
	 * Добавление/Редактирование новостей
	 *
	 */
	public function edit() {
		$this->template = new View('admin/manufacturers/edit');

		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$post = $this->input->post();

		/**
		 * Проверка прав доступа
		 */
		if($id){
			if(!Acl::instance()->is_allowed('manufacturers_edit'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		} else {
			if(!Acl::instance()->is_allowed('manufacturers_add'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		}

		$form = new ValidateForm();
		$form->add_field('name', 'string', array('required', 'length[1,100]'));
		$form->add_field('description', 'html', 'required');


		/**
		 * Вспомогательные данные
		 */
		$table = new Manufacturers_Model();
		if($id){
			$data = $table->db
				->select('*')
				->where('id', $id)
				->from($table->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) производителя', '/admin/manufacturers');
		} else
			$data = array();


		/**
		 * Сохранение
		 */
		if(!empty($post)){
			$data = $form->get_data($post) + $data;
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();
				$fill['description'] = str_replace('../../', '/', $fill['description']);

				if($id) {
					if(!$result = $table->update($fill, array('id'=>$id))) {
						$form->add_error('update', 1);
					} else {
						log::add('manufacturers', 'Редактирование производителя id='.$id);
					}

				} else {
					if(!$result = $table->insert($fill)) {
						$form->add_error('insert', 1);
					} else {
						$id = $result->insert_id();
						log::add('manufacturers', 'Добавление производителя id='.$id);
					}
				}

			}

			if($form->is_ok()){
				Cache::instance()->delete_tag('manufacturers');

				message::info('Производитель успешно сохранен', '/admin/manufacturers');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$data['description_editor'] = Editor::factory("admin")->set_fieldname("description")->set_value($data['description'])->set_height(400)->set_width(650)->render(FALSE, TRUE);
		$this->template->data = $data;
	}


	/**
	 * Удаление записи
	 *
	 */
	public function delete() {
		if(!Acl::instance()->is_allowed('manufacturers_del'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$id = (int) $this->input->get('id');

		$table = new Manufacturers_Model();
		$table->delete(array('id' => $id));

		Cache::instance()->delete_tag('manufacturers');

		log::add('manufacturers', 'Удаление производителя id='.$id);

		message::info('Производитель успешно удален', '/admin/manufacturers');
	}

}
?>