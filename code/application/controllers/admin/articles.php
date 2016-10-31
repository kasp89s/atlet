<?php
/**
 * Статьи
 *
 */
class Articles_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Статьи';

		if(Acl::instance()->is_allowed('articles_add')){
			$this->menu = array(
				array('url'=>'/admin/articles', 'section'=>'Все статьи'),
				array('url'=>'/admin/articles/edit', 'section'=>'Добавить статью', 'title'=>'Статья'),
			);
		} else {
			$this->menu = array(
				array('url'=>'/admin/articles', 'section'=>'Все статьи'),
			);
		}

		parent::__construct();
	}


	/**
	 * Список статей
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('articles_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/articles/index');

		$active = $this->input->get('active');

		$table = new Articles_Model;
		$table->db
			->select(Array('self.id', 'self.name', 'self.preview', 'self.active'))
			->from($table->table_name);

		if($active) $table->db->where('self.active', ($active == 'on') ? 1 : 0);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('self.id' => 'desc', 'self.name');
		$data = &$tm->show();


		$active_statuses = array(
			'0' => array('id' => FALSE, 'name' => 'Все'),
			'1' => array('id' => 'on', 'name' => 'Активные'),
			'2' => array('id' => 'off', 'name' => 'Неактивные'),
		);
		form_filter::fill_list('active', $active, $data, $active_statuses);

		$this->template->main = $data;
	}


	/**
	 * Добавление/Редактирование статей
	 *
	 */
	public function edit() {
		$this->template = new View('admin/articles/edit');

		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$post = $this->input->post();

		if(isset($post['uri'])){
			$post['uri']=url::title($post['uri'],"_");
		}

		/**
		 * Проверка прав доступа
		 */
		if($id){
			if(!Acl::instance()->is_allowed('articles_edit'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		} else {
			if(!Acl::instance()->is_allowed('articles_add'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		}

		$form = new ValidateForm();
		$form->add_field('date_publication', array(new mod_date('d.m.Y', 'Y-m-d')), array('required', 'valid::date'));
		$form->add_field('name', 'string', array('required', 'length[1,100]'));
		$form->add_field('uri', 'string', array('required', 'length[3,200]'));
		$form->add_field('preview', 'string', 'required');
		$form->add_field('description', 'html', 'required');
		$form->add_field('active', 'checkbox');
		$form->add_field('seo_title', 'string', array('required', 'length[1,100]'));
		$form->add_field('seo_keywords', 'string', array('required', 'length[1,200]'));
		$form->add_field('seo_description', 'string', array('required', 'length[1,200]'));


		/**
		 * Вспомогательные данные
		 */
		$table = new Articles_Model();
		if($id){
			$data = $table->db
				->select('*')
				->where('id', $id)
				->from($table->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) статьи', '/admin/articles');
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

				//Публиковать могу только с правами
				if(!Acl::instance()->is_allowed('articles_publication'))
					unset($fill['active']);

				if($id) {
					if(!$result = $table->update($fill, array('id'=>$id))) {
						$form->add_error('update', 1);
					} else {
						DBFile::save('articles', $id, 'image');

						log::add('articles', 'Редактирование статьи id='.$id);
					}

				} else {
					$fill['date_create'] = db::expr('now()');

					if(!$result = $table->insert($fill)) {
						$form->add_error('insert', 1);
					} else {
						$id = $result->insert_id();
						DBFile::save('articles', $id, 'image');

						log::add('articles', 'Добавление статьи id='.$id);
					}
				}

			}

			if($form->is_ok()){
				Cache::instance()->delete_tag('articles');

				message::info('Статья успешно сохранена', '/admin/articles');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$data['description_editor'] = Editor::factory("admin")->set_fieldname("description")->set_value($data['description'])->set_height(400)->set_width(650)->render(FALSE, TRUE);
		$this->template->data = $data;
	}


	/**
	 * Групповое сохранение статей
	 *
	 */
	public function group() {
		if(!Acl::instance()->is_allowed('articles_publication'))
			message::error('Нет прав доступа для сохранения', '/admin/articles');


		$post = $this->input->post();
		$ids = array();
		$act = array();

		if(isset($post['ids']))
			$ids = arr::int_array($post['ids']);
		if(isset($post['act']))
			$act = arr::int_array($post['act']);


		$table = new Articles_Model();

		$set = array_intersect($ids, $act);
		if(count($set))
			$table->update(array('active' => 1), array('id' => $set));

		$clear = array_diff($ids, $act);
		if(count($clear))
			$table->update(array('active' => 0), array('id' => $clear));

		Cache::instance()->delete_tag('articles');

		sort($ids);
		log::add('articles', 'Групповое сохранение статей id=[' . implode(',', $ids) . ']');

		message::info('Данные успешно сохранены', '/admin/articles');
	}


	/**
	 * Удаление записи
	 *
	 */
	public function delete() {
		if(!Acl::instance()->is_allowed('articles_del'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$id = (int) $this->input->get('id');

		$table = new Articles_Model();
		$table->delete(array('id' => $id));
		DBFile::delete_items('articles', $id);

		Cache::instance()->delete_tag('articles');

		log::add('articles', 'Удаление статьи id='.$id);

		message::info('Статья успешно удалена', '/admin/articles');
	}

}
?>