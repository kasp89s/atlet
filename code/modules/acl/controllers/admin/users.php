<?php
/**
 * ACL. Пользователи
 *
 * @author Antuan
 */
class Users_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'ACL';
		$this->menu = array(
			array('url'=>'/admin/users', 'section'=>'Все пользователи'),
			array('url'=>'/admin/users/edit', 'section'=>'Добавить пользователя', 'title'=>'Пользователь'),
			array('url'=>'/admin/roles', 'section'=>'Все роли'),
			array('url'=>'/admin/roles/edit', 'section'=>'Добавить роль', 'title'=>'Роль'),
		);

		parent::__construct();

		//Проверка прав доступа
		if(!Acl::instance()->is_allowed('admin')){
			$this->menu = array();
		}
	}


	/**
	 * Список пользователй
	 *
	 */
	public function index() {
		/**
		 * Проверка прав доступа
		 */
		if(!Acl::instance()->is_allowed('admin')){
			return $this->edit_self();
		}

		$this->template = new View('admin/users/index');

		$table = new AuthUsers_Model;
		$table->db
			->select(Array('self.id', 'self.username', 'self.fio'))
			->from($table->table_name)
			->order_by('self.fio');

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('self.id' => 'desc', 'self.username', 'self.fio');
		$data = &$tm->show();

		$this->template->main = $data;
	}


	/**
	 * Добавлени/Редактирование пользователя
	 *
	 */
	public function edit() {
		/**
		 * Проверка прав доступа
		 */
		if(!Acl::instance()->is_allowed('admin')){
			return $this->edit_self();
		}

		$this->template = new View('admin/users/edit');

		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$post = $this->input->post();


		/**
		 * Загружаем данные пользователя
		 */
		$table = new AuthUsers_Model();
		if($id){
			$data = $table->db
				->select('*')
				->where('id', $id)
				->from($table->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) пользователя', '/admin/users');

		} else
			$data = array();


		/**
		 * Загружаем роли пользвателя
		 */
		$table_roles = new AuthRoles_Model();
		if($id && $data['username'] != 'admin'){

			$all_roles = $table_roles->db
				->select('*')
				->from($table_roles->table_name)
				->order_by('description')
				->get()
				->rows();

			$roles_ids = array();
			$roles = $table->get_roles($id);
			foreach ($roles as $v){
				$roles_ids[] = $v['id'];
			}

			foreach ($all_roles as $k=>$v){
				if(in_array($v['id'], $roles_ids))
					$all_roles[$k]['selected'] = true;
			}

			$data['roles'] = $all_roles;
		}


		/**
		 * Загружаем права
		 */
		$table_actions = new AuthActions_Model();
		$table_acl     = new AuthACL_Model();
		if($id && $data['username'] != 'admin'){
			$rows = $table_actions->db
				->select('*')
				->from($table_actions->table_name)
				->get()
				->rows();

			$user_actions_tmp = $table_acl->get_user_actions($id);
			$user_actions = array();
			foreach ($user_actions_tmp as $v){
				$user_actions[$v['id']] = $v['access'];
			}

			$actions = array();
			foreach ($rows as $v){
				if($v['type'] == 'admin') continue;

				if(array_key_exists($v['id'], $user_actions))
					$v[$user_actions[$v['id']]] = true;

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
		 * Задаем правила валидации
		 */
		$user = Acl::instance()->get_user();

		$form = new ValidateForm();
		$form->add_field('username', 'string', array('required', 'length[1,100]'));
		$form->add_field('fio', 'string', array('required', 'length[1,255]'));
		$form->add_field('email', 'string', array('length[1,255]', 'email'));
		$form->add_field('phone', 'string', array('length[1,100]'));
		$form->add_field('address', 'string');
		if($id)
			$form->add_field('password', 'string', array('length[1,100]'));
		else
			$form->add_field('password', 'string', array('required', 'length[1,100]'));
		$form->add_field('password_check', array('string', 'no_fill'), array('matches[password]'));



		/**
		 * Сохранение формы
		 */
		if(!empty($post)){
			$data = $form->get_data() + $data;
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();

				//Проверка логина
				if($user['username'] == $fill['username'])
					unset($fill['username']);

				//Провкрка пароля
				if(empty($fill['password']))
					unset($fill['password']);
				else
					$fill['password'] = Acl::instance()->hash_password($fill['password']);


				if($id) {
					if(!$result = $table->update($fill, array('id'=>$id))) {
						$form->add_error('update', 1);
					} else {
						$table->clear_roles($id);
						if(isset($post['roles']))
							$table->add_role($id, $post['roles']);

						$table_acl->clear_user_action($id);
						if(isset($post['actions']))
							$table_acl->add_user_action($id, $post['actions']);

						log::add('acl', 'Редактирование пользователя id='.$id);
					}

				} else {
					$fill['date_create'] = db::expr('now()');

					if(!$result = $table->insert($fill)) {
						$form->add_error('insert', 1);

					} else {
						$id = $result->insert_id();
						//$table_acl->add_user_action($id, array($action => 'allow'));

						log::add('acl', 'Добавление пользователя id='.$id);
					}
				}
			}

			if($form->is_ok()){
				message::info('Пользователь успешно сохранен', '/admin/users');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}

		//Запрет на редактирование своего логина
		if(isset($data['username']) && $user['username'] == $data['username'])
			$data['username_no_edit'] = true;


		$data = $form->get_form($data) + $data + $form->get_errors();
		$this->template->data = $data;
		$this->template->actions = $actions;
	}


	/**
	 * Редактирование своих данных (для Неадмина)
	 *
	 */
	public function edit_self() {
		$this->template = new View('admin/users/edit_self');

		$post = $this->input->post();
		$user = Acl::instance()->get_user();
		$id = $user['id'];

		/**
		 * Загружаем данные пользователя
		 */
		$table = new AuthUsers_Model();
		$data = $table->db
			->select('*')
			->where('id', $id)
			->from($table->table_name)
			->get()
			->row();

		if(!$data) message::error('Некорректный идентификатор (id) пользователя', '/admin');


		/**
		 * Задаем правила валидации
		 */
		$form = new ValidateForm();
		$form->add_field('fio', 'string', array('required', 'length[1,255]'));
		$form->add_field('email', 'string', array('length[1,255]', 'email'));
		$form->add_field('phone', 'string', array('length[1,100]'));
		$form->add_field('address', 'string');
		$form->add_field('password', 'string', array('length[1,100]'));
		$form->add_field('password_check', array('string', 'no_fill'), array('matches[password]'));



		/**
		 * Сохранение формы
		 */
		if(!empty($post)){
			$data = $form->get_data() + $data;
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();

				//Провкрка пароля
				if(empty($fill['password']))
					unset($fill['password']);
				else
					$fill['password'] = Acl::instance()->hash_password($fill['password']);


				if(!$result = $table->update($fill, array('id'=>$id)))
					$form->add_error('update', 1);

			}

			if($form->is_ok()){
				message::info('Пользователь успешно сохранен', '/admin/users');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$this->template->data = $data;
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
		$user = Acl::instance()->get_user();
		$id = $user['id'];

		if($id == $this->input->get('id')){			message::error('Нельзя удалять свою учетную запись', '/admin/users');		}

		if(!Acl::instance()->is_allowed('admin')){
			return $this->edit_self();
		}

		$id = (int) $this->input->get('id');

		$table = new AuthUsers_Model();
		$table->delete(array('id' => $id));

		$table = new AuthACL_Model();
		$table->clear_user_action($id);

		log::add('acl', 'Удаление пользователя id='.$id);

		message::info('Пользователь успешно удален', '/admin/users');
	}
}
?>