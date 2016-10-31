<?php
/**
 * CMS. Управления статическими страницами
 *
 * @author Antuan
 * @created 23.09.2009
 */
class Pages_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'CMS';

		if(Acl::instance()->is_allowed('cms_add')){
			$this->menu = array(
				array('url'=>'/admin/pages', 'section'=>'Все страницы'),
				array('url'=>'/admin/pages/edit', 'section'=>'Добавить страницу', 'title'=>'Страница')
			);
		} else {
			$this->menu = array(
				array('url'=>'/admin/pages', 'section'=>'Все страницы'),
			);

		}

		parent::__construct();
	}

	/**
	 * Дерево
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('cms_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/pages/index');

		$pages = new Pages_Model();
		$pages->info_content();
		$pages = $pages->db
			->select('self.*')
			->from($pages->table_name)
			->where('scope', 1)
			->order_by('self.lft')
			->get()
			->rows();


		/**
		 * Проверка на конфликт URI
		 */
		$level = 0;
		$list_uri = array();
		foreach ($pages as $k=>$v){

			if($level > $v['level'])
				$list_uri[$level] = array();
			$level = $v['level'];


			if(isset($list_uri[$level]) && array_key_exists($v['uri'], $list_uri[$level])){
				$pages[$k]['warn_duplicate'] = true;
				$pages[$list_uri[$level][$v['uri']]]['warn_duplicate'] = true;
			} else {
				$list_uri[$level][$v['uri']] = $k;
			}
		}

		$this->template->pages = $pages;
	}


	/**
	 * Создание/Редактирование узла дерева
	 *
	 */
	public function edit() {
		$this->template = new View('admin/pages/edit');

		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$post = $this->input->post();
		$data = array();

		if($id){
			if(!Acl::instance()->is_allowed('cms_edit'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		} else {
			if(!Acl::instance()->is_allowed('cms_add'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		}

		$table_pages = new Pages_Model();
		if($id){
			if(!Acl::instance()->is_allowed('cms_edit'))
				return $this->edit_self();

			$table_pages->info_content();
			$data = $table_pages->db
				->select('self.*')
				->where('self.id', $id)
				->where('self.scope', 1)
				->from($table_pages->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) страницы', '/admin/pages');
		} else
			$data = array();


		/**
		 * "Настройки страницы". подгон
		 */
		if(!empty($post['type'])){
			$data['type'] = $post['type'];
			$data["type_{$post['type']}_checked"] = true;
			$post['uri'] = url::title($post['uri'],"_");

		} elseif(count($data)){
			if(empty($data['type']))
				$data['type'] = 'none';

			if($data['type'] == 'module')
				$data['module'] = $data['target'];
			elseif($data['type'] == 'redirect')
				$data['redirect'] = $data['target'];

			$data["type_{$data['type']}_checked"] = true;

		} else {
			$data["type"] = 'none';
			$data["type_none_checked"] = true;
		}


		/**
		 * Правила валидации
		 */
		$table_modules = new Modules_Model();
		$modules = $table_modules->db
				->select(Array('id' => 'code', 'name'))
				->from($table_modules->table_name)
				->order_by('name')
				->get()
				->rows();

		$form = new ValidateForm();
		switch ($data["type"]){
			case 'none':
				$form->add_field('title', 'string', array('required', 'length[1,100]'));
				$form->add_field('uri', 'string', array('required', 'length[1,200]'));
				$form->add_field('preview', 'string', 'required');
				$form->add_field('description', 'html', 'required');
				$form->add_field('type', 'string');
				$form->add_field('redirect', 'string', array('length[1,200]'));
				$form->add_field('module', array(new mod_list($modules)));
				$form->add_field('image', 'no_fill', array('upload::valid', 'upload::type[gif,jpg,jpeg,png]', 'upload::size[1M]'));
				$form->add_field('active_menu', 'checkbox');
				$form->add_field('seo_title', 'string', array('required', 'length[1,100]'));
				$form->add_field('seo_keywords', 'string', array('required', 'length[1,200]'));
				$form->add_field('seo_description', 'string', array('required', 'length[1,200]'));
				break;
			case 'module':
				$form->add_field('title', 'string', array('required', 'length[1,100]'));
				$form->add_field('uri', 'string', array('required', 'length[1,200]'));
				$form->add_field('description', 'html');
				$form->add_field('type', 'string');
				$form->add_field('module', array(new mod_list($modules)));
				$form->add_field('image', 'no_fill', array('upload::valid', 'upload::type[gif,jpg,jpeg,png]', 'upload::size[1M]'));
				$form->add_field('active_menu', 'checkbox');
				$form->add_field('seo_title', 'string', array('required', 'length[1,100]'));
				$form->add_field('seo_keywords', 'string', array('required', 'length[1,200]'));
				$form->add_field('seo_description', 'string', array('required', 'length[1,200]'));
				break;
			case 'redirect':
				$form->add_field('title', 'string', array('required', 'length[1,100]'));
				$form->add_field('uri', 'string', array('required', 'length[1,200]'));
				$form->add_field('description', 'string');
				$form->add_field('module', array(new mod_list($modules)));
				$form->add_field('image', 'no_fill', array('upload::valid', 'upload::type[gif,jpg,jpeg,png]', 'upload::size[1M]'));
				$form->add_field('type', 'string');
				$form->add_field('redirect', 'string', array('length[1,200]'));
				$form->add_field('active_menu', 'checkbox');
				break;
		}


		/**
		 * Проверка и сохранение
		 */
		if(!empty($post)){
			$data = $form->get_data($post) + $data;
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();
				$fill['target'] = ($fill['type'] == 'none') ? '' : $fill[$fill['type']];
				$fill['title'] = trim($fill['title'], '/');
				$fill['description'] = str_replace('../../', '/', $fill['description']);


				if($id) {
					//сохраняем title страницы
					$page = array(
						'title'        => $fill['title'],
						'preview'      => isset($fill['preview'])?$fill['preview']:'',
						'type'         => $fill['type'],
						'target'       => $fill['target'],
						'active_menu'  => $fill['active_menu']
					);
					$table_pages->update($page, array('id' => $id));


					//сохраняем контент страницы
					$table_content = new PageContents_Model();
					$page_content = array(
						'title'           => $fill['title'],
						'uri'             => $fill['uri'],
						'preview'         => isset($fill['preview'])?$fill['preview']:'',
						'description'     => $fill['description'],
						'seo_title'       => $fill['seo_title'],
						'seo_keywords'    => $fill['seo_keywords'],
						'seo_description' => $fill['seo_description'],
						'type'            => $fill['type'],
						'target'          => $fill['target'],
						'date_modified'   => db::expr('now()')
					);

					if(empty($data['page_contents_id'])) {
						$page_content = array_merge(
							$page_content,
							array('page_id'=>$id, 'date_create'=>db::expr('now()'))
						);
						$table_content->insert($page_content);
					} else {
						$table_content->update($page_content, array('page_id' => $id));
					}

					DBFile::save('pages', $id, 'image');

					log::add('cms', 'Редактирование страницы id='.$id);

				} else {
					//сохраняем страницу без title
					$root = $table_pages->root(1);

					if (!$root) { //если дерево пустое
						$root['lft']   = 1;
						$root['rgt']   = 2;
						$root['level'] = 0;
						$root['scope'] = 1;

						$result = $table_pages->insert($root);

					} else {
						$result = $table_pages->insert_as_last_child($root);
					}
					$id = $result->insert_id();

					//сохраняем title страницы
					$page = array(
						'title'        => $fill['title'],
						'preview'      => isset($fill['preview'])?$fill['preview']:'',
						'type'         => $fill['type'],
						'target'       => $fill['target'],
						'active_menu'  => $fill['active_menu']
					);
					$table_pages->update($page, array('id' => $id));


					//сохраняем контент страницы
					$table_content = new PageContents_Model();
					$page_content = array(
						'page_id'          => $id,
						'title'            => $fill['title'],
						'uri'              => url::title($fill['uri']),
						'preview'          => isset($fill['preview'])?$fill['preview']:'',
						'description'      => $fill['description'],
						'seo_title'        => $fill['seo_title'],
						'seo_keywords'     => $fill['seo_keywords'],
						'seo_description'  => $fill['seo_description'],
						'type'             => $fill['type'],
						'target'           => $fill['target'],
						'date_create'      => new Database_Expr('now()'),
						'date_modified'    => new Database_Expr('now()'),
					);
					$table_content->insert($page_content);

					DBFile::save('pages', $id, 'image');

					log::add('cms', 'Добавление страницы id='.$id);
				}
			}

			if($form->is_ok()){
				Cache::instance()->delete_tag('cms');

				message::info('Страница успешно сохранена', '/admin/pages');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$data['description_editor'] = Editor::factory("admin")->set_fieldname("description")->set_value($data['description'])->set_height(400)->set_width(650)->render(FALSE, TRUE);
		$data['media'] = DBFile::select('pages', $id);
		$this->template->data = $data;
	}


	/**
	 * Удаление узла дерева
	 *
	 * @param int $id идентификатор узла
	 */
	public function delete() {
		if(!Acl::instance()->is_allowed('cms_del'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$id = (int) $this->input->get('id');
		$table_pages = new Pages_Model();

		$count = $table_pages->db
				->where('id', $id)
				->where('scope', 1)
				->from($table_pages->table_name)
				->count_records();

		if (!$count)
			message::error('Некорректный идентификатор (id) страницы', '/admin/pages');


		$table_pages->delete_node($id);
		DBFile::delete_items('pages', $id);

		Cache::instance()->delete_tag('cms');

		log::add('cms', 'Удаление страницы id='.$id);

		message::info('Страница успешно удалена', '/admin/pages');

	}


	/**
	 * Сохрание структуры дерева
	 */
	public function save_tree(){
		if(!Acl::instance()->is_allowed('cms_edit')) {
			message::error('Нет прав доступа для сохранения', FALSE, TRUE);
			exit;
		}


		$tree = json_decode($this->input->post('tree', NULL), TRUE);

		$this->tree = array();
		$this->counter = 0;
		$this->level_zero = 0;
		$this->max_level = 0;

		$this->_calculate_mptt($tree);

		if ($this->level_zero > 1){
			message::error('Дерево страниц не может быть сохранено', FALSE, TRUE);
			exit;
		}

		if ($this->max_level > 3){
			message::error('Дерево не может быть сохранено. Больше четырех уровней', FALSE, TRUE);
			exit;
		}

		$table = new Pages_Model();
		foreach($this->tree as $node){
			$fill = array(
				'level'	 => $node['level'],
				'lft'	 => $node['lft'],
				'rgt'	 => $node['rgt']
			);
			$table->update($fill, array('id' => $node['id'], 'scope' => 1));
		}

		//очищаем кэш
		Cache::instance()->delete_tag('cms');

		log::add('cms', 'Сохранение дерева страниц');

		message::info('Дерево страниц успешно сохранено', FALSE, TRUE);
	}


	private function _calculate_mptt($tree, $parent = 0, $level = 0){

		foreach ($tree as $key => $children){
			$id = substr($key, 5);

			$left = ++$this->counter;

			if ( ! empty($children))
				$this->_calculate_mptt($children, $id, $level+1);

			$right = ++$this->counter;

			if ($level === 0)
				$this->level_zero++;

			if($level > $this->max_level)
				$this->max_level = $level;

			$this->tree[] = array(
				'id' => $id,
				'level' => $level,
				'lft' => $left,
				'rgt' => $right
			);
		}
	}

}
?>