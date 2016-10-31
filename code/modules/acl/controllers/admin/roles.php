<?php
/**
 * ACL. Роли
 *
 * @author Antuan
 */
class Roles_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'ACL';
		$this->menu = array(
			array('url'=>'/admin/users', 'section'=>'Все пользователи'),
			array('url'=>'/admin/users/edit', 'section'=>'Добавить пользователя', 'title'=>'Пользователь'),
			array('url'=>'/admin/roles', 'section'=>'Все роли'),
			array('url'=>'/admin/roles/edit', 'section'=>'Добавить роль', 'title'=>'Роль'),
		);

		parent::__construct();
	}


	/**
	 * Список ролей
	 *
	 */
	public function index() {
		/**
		 * Проверка прав доступа
		 */
		if(!Acl::instance()->is_allowed('admin')){
			return $this->edit_self();
		}


		$this->template = new View('admin/roles/index');

		$table = new AuthRoles_Model;
		$table->db
			->select(Array('id', 'name', 'description'))
			->from($table->table_name);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('id' => 'desc', 'name');

		$this->template->main = $tm->show();
	}


	/**
	 * Создание/Редактирование роли
	 *
	 */
	public function edit() {
		/**
		 * Проверка прав доступа
		 */
		if(!Acl::instance()->is_allowed('admin')){
			return $this->edit_self();
		}

		$this->template = new View('admin/roles/edit');

		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$post = $this->input->post();


		/**
		 * Правила валидации
		 */
		$form = new ValidateForm();
		$form->add_field('name', 'string', array('required', 'length[1,50]'));
		$form->add_field('description', 'string', array('length[1,255]'));


		/**
		 * Загружаем данные роли
		 */
		$table = new AuthRoles_Model();
		if($id){
			$data = $table->db
				->select('*')
				->where('id', $id)
				->from($table->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) роли', '/admin/roles');
		} else
			$data = array();


		/**
		 * Загружаем права
		 */
		$table_actions = new AuthActions_Model();
		$table_acl     = new AuthACL_Model();
		if($id){
			$rows = $table_actions->db
				->select('*')
				->from($table_actions->table_name)
				->get()
				->rows();

			$role_actions_tmp = $table_acl->get_role_actions($id);
			$role_actions = array();
			foreach ($role_actions_tmp as $v){
				$role_actions[] = $v['id'];
			}

			$actions = array();
			foreach ($rows as $v){
				if($v['type'] == 'admin') continue;

				if(in_array($v['id'], $role_actions))
					$v['selected'] = true;

				$name = $v['type'];
				switch ($v['type']){
					case 'cms':            $name = 'CMS'; break;
					case 'order':          $name = 'Заказы товаров'; break;
					case 'callback':       $name = 'Заказ звонка'; break;
					case 'catalog':        $name = 'Каталог товаров'; break;
					case 'news':           $name = 'Новости'; break;
					case 'articles':       $name = 'Статьи'; break;
					case 'links':          $name = 'Ссылки'; break;
					case 'catalogstats':   $name = 'Статистика каталога'; break;
				}

				$code = str_replace($v['type'].'_', '', $v['code']);
				$actions['common'][$v['type']][$code] = $v;
				$actions['common'][$v['type']]['name'] = $name;
			}

		} else
			$actions = array();


		/**
		 * Проверка и сохранение формы
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
						$table_acl->clear_role_action($id);
						$table_acl->add_role_action($id, $post['actions']);

						log::add('acl', 'Редактирование роли id='.$id);
					}

				} else {
					if(!$result = $table->insert($fill)) {
						$form->add_error('insert', 1);
					} else {
						$id = $result->insert_id();
						log::add('acl', 'Добавление роли id='.$id);
					}
				}
			}

			if($form->is_ok()){
				message::info('Роль успешно сохранена', '/admin/roles');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$this->template->data = $data;
		$this->template->actions = $actions;
	}



	/**
	 * Удаление записи
	 *
	 * @param int $id идентификатор записи
	 */
	public function delete() {
		/**
		 * Проверка прав доступа
		 */
		if(!Acl::instance()->is_allowed('admin')){
			return $this->edit_self();
		}

		$id = (int) $this->input->get('id');

		$table = new AuthRoles_Model();
		$table->delete(array('id' => $id));

		$table = new AuthACL_Model();
		$table->clear_role_action($id);

		log::add('acl', 'Удаление роли id='.$id);

		message::info('Роль успешно удалена', '/admin/roles');
	}

}
?>