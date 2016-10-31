<?php
/**
 * Группы организаций
 *
 * @author Antuan
 * @created 01.10.2009
 */
class CatalogGroups_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Каталог товаров';

		if(Acl::instance()->is_allowed('catalog_add')){
			$this->menu = array(
				array('url'=>'/admin/cataloggroups', 'section'=>'Все группы'),
				array('url'=>'/admin/cataloggroups/edit', 'section'=>'Добавить группу', 'title'=>'Группа'),
				array('url'=>'/admin/catalog/sgs_list', 'section'=>'SGS список товаров', 'title'=>'SGS список товаров'),
				array('url'=>'/admin/catalog', 'section'=>false, 'title'=>'Товары'),
				array('url'=>'/admin/catalog/edit', 'section'=>false, 'title'=>'Товар')
			);
		} else {
			$this->menu = array(
				array('url'=>'/admin/cataloggroups', 'section'=>'Все группы'),
				array('url'=>'/admin/catalog/sgs_list', 'section'=>'SGS список товаров', 'title'=>'SGS список товаров'),
				array('url'=>'/admin/catalog', 'section'=>false, 'title'=>'Товары'),
				array('url'=>'/admin/catalog/edit', 'section'=>false, 'title'=>'Товар')
			);
		}

		parent::__construct();
	}

	/**
	 * Дерево
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('catalog_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/cataloggroups/index');

		$groups = new CatalogGroups_Model();
		$groups->info_content();
		$groups = $groups->db
			->select('self.*')
			->from($groups->table_name)
			->order_by('self.lft')
			->get()
			->rows();

		$this->template->groups = $groups;
	}


	/**
	 * Создание/Редактирование узла дерева
	 *
	 */
	public function edit() {
		$this->template = new View('admin/cataloggroups/edit');

		$this->set_attribute('js', '/js/jquery/colorpicker.js');
		$this->set_attribute('js', '/js/jquery/eye.js');
		$this->set_attribute('js', '/js/jquery/utils.js');
        $this->set_attribute('css', '/css/colorpicker.css');

		$id = !empty($_REQUEST['id']) ? (int)$_REQUEST['id'] : false;
		$post = $this->input->post();
		if(isset($post['uri'])){
			$post['uri']=url::title($post['uri'],"_");
		}
		$data = array();

		/**
		 * Проверка прав доступа
		 */
		if($id){
			if(!Acl::instance()->is_allowed('catalog_edit'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		} else {
			if(!Acl::instance()->is_allowed('catalog_add'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		}


		/**
		 * Вспомогательные данные
		 */
		$table_groups = new CatalogGroups_Model();
		if($id){
			$table_groups->info_content();
			$data = $table_groups->db
				->select('self.*')
				->where('self.id', $id)
				->from($table_groups->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) группы', '/admin/cataloggroups');
		} else
			$data = array();


		/**
		 * Валидация данных
		 */
		$form = new ValidateForm();
		$form->add_field('title', 'string', array('required', 'length[1,200]'));
		$form->add_field('code', 'string', array('length[1,100]'));
		$form->add_field('short_descr', 'string', 'length[1,700]');
		$form->add_field('full_descr', 'html');
		$form->add_field('uri', 'string', array('required', 'length[3,200]'));
		$form->add_field('seo_name', 'string', array('length[1,200]'));
		$form->add_field('seo_title', 'string', array('required', 'length[1,100]'));
		$form->add_field('seo_keywords', 'string', array('required', 'length[1,200]'));
		$form->add_field('seo_description', 'string', array('required', 'length[1,200]'));
		$form->add_field('seo_auto_title', 'string', array('length[1,200]'));
		$form->add_field('seo_auto_keywords', 'string', array('length[1,200]'));
		$form->add_field('seo_auto_description', 'string', array('length[1,200]'));
		$form->add_field('is_vip', 'checkbox');
		$form->add_field('use_h1', 'checkbox');
		$form->add_field('concat_with_section_title', 'checkbox');
		$form->add_field('active', 'checkbox');

		$form->add_field('is_show_in_relative', 'checkbox');
		$form->add_field('relative_weight', 'int');
		$form->add_field('is_custom_color', 'checkbox');
		$form->add_field('custom_color_value', 'string', array('length[1,7]'));

		$form->add_field('image', 'no_fill', array('upload::valid', 'upload::type[gif,jpg,jpeg,png]', 'upload::size[1M]'));


		/**
		 * Сохранение
		 */
		if(!empty($post)){
			$data = $form->get_data($post) + $data;
			$form->validate();

			if($form->is_ok()){
				$fill = $form->get_fill();
				$fill['full_descr'] = str_replace('../../', '/', $fill['full_descr']);

				if($id) {
					//сохраняем title группы
					$group = array(
						'title'   => $fill['title'],
					);
					$table_groups->update($group, array('id' => $id));


					//сохраняем контент группы
					$table_content = new CatalogGroupContents_Model();
					$group_content = array(
						'title'                        => $fill['title'],
						'code'                         => $fill['code'],
						'short_descr'                  => $fill['short_descr'],
						'full_descr'                   => $fill['full_descr'],
						'uri'   			           => $fill['uri'],
						'seo_name'                     => $fill['seo_name'],
						'seo_title'                    => $fill['seo_title'],
						'seo_keywords'                 => $fill['seo_keywords'],
						'seo_description'              => $fill['seo_description'],
						'seo_auto_title'               => $fill['seo_auto_title'],
						'seo_auto_keywords'            => $fill['seo_auto_keywords'],
						'seo_auto_description'         => $fill['seo_auto_description'],
						'date_modified'                => new Database_Expr('now()'),
						'is_vip'                       => $fill['is_vip'],
						'active'                       => $fill['active'],
						'use_h1'            		   => $fill['use_h1'],
						'concat_with_section_title'    => $fill['concat_with_section_title'],
						'is_show_in_relative'          => $fill['is_show_in_relative'],
						'relative_weight'              => $fill['relative_weight'],
						'is_custom_color'   		   => $fill['is_custom_color'],
						'custom_color_value'           => $fill['custom_color_value']

					);

					if(empty($data['catalog_group_contents_id'])) {
						$group_content = array_merge(
							$group_content,
							array('group_id'=>$id, 'date_create'=>new Database_Expr('now()'))
						);
						$table_content->insert($group_content);
					} else {
						$table_content->update($group_content, array('group_id' => $id));
					}

					log::add('catalog', 'Редактирование группы товаров id='.$id);

				} else {
					//сохраняем группы без title
					$root = $table_groups->root(1);

					if (!$root) { //если дерево пустое
						$root['lft']   = 1;
						$root['rgt']   = 2;
						$root['level'] = 0;

						$result = $table_groups->insert($root);

					} else {
						$result = $table_groups->insert_as_last_child($root);
					}
					$id = $result->insert_id();

					//сохраняем title группы
					$group = array(
						'title'   => $fill['title'],
					);
					$table_groups->update($group, array('id' => $id));


					//сохраняем контент группы
					$table_content = new CatalogGroupContents_Model();
					$group_content = array(
						'group_id'          		   => $id,
						'title'             		   => $fill['title'],
						'code'              		   => $fill['code'],
						'uri'   					   => $fill['uri'],
						'short_descr'       		   => $fill['short_descr'],
						'full_descr'        		   => $fill['full_descr'],
						'seo_name'          		   => $fill['seo_name'],
						'seo_title'         		   => $fill['seo_title'],
						'seo_keywords'      		   => $fill['seo_keywords'],
						'seo_description'   		   => $fill['seo_description'],
						'seo_auto_title'         	   => $fill['seo_auto_title'],
						'seo_auto_keywords'      	   => $fill['seo_auto_keywords'],
						'seo_auto_description'   	   => $fill['seo_auto_description'],
						'date_create'       		   => new Database_Expr('now()'),
						'date_modified'     		   => new Database_Expr('now()'),
						'is_vip'            		   => $fill['is_vip'],
						'active'            		   => $fill['active'],
						'use_h1'            		   => $fill['use_h1'],
						'concat_with_section_title'    => $fill['concat_with_section_title'],
						'is_custom_color'   		   => $fill['is_custom_color'],
						'custom_color_value'           => $fill['custom_color_value']
					);
					$table_content->insert($group_content);

					log::add('catalog', 'Добавление группы товаров id='.$id);
				}
			}

			if($form->is_ok()){
				Cache::instance()->delete_tag("catalog");

				DBFile::save('catalog_groups', $id, 'image');

				message::info('Группа успешно сохранена', '/admin/cataloggroups');
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}

		$data = $form->get_form($data) + $data + $form->get_errors();
		$data['full_descr_editor'] = Editor::factory("admin")->set_fieldname("full_descr")->set_value($data['full_descr'])->set_height(400)->set_width(650)->render(FALSE, TRUE);
		$data['media'] = DBFile::select('catalog_groups', $id);
		$this->template->data = $data;
	}


	/**
	 * Удаление узла дерева
	 *
	 * @param int $id идентификатор узла
	 */
	public function delete() {
		if(!Acl::instance()->is_allowed('catalog_del'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$id = (int) $this->input->get('id');
		$table_groups = new CatalogGroups_Model();

		$count = $table_groups->db
				->where('id', $id)
				->from($table_groups->table_name)
				->count_records();

		if (!$count)
			message::error('Некорректный идентификатор (id) группы', '/admin/cataloggroups');

        DBFile::delete_items('catalog_groups', $id);
		$table_groups->delete_node($id);

		Cache::instance()->delete_tag('catalog');

		log::add('catalog', 'Удаление группы товаров id='.$id);

		message::info('Группа успешно удалена', '/admin/cataloggroups');
	}


	/**
	 * Сохраняем дерево меню
	 */
	public function save_tree(){
		if(!Acl::instance()->is_allowed('catalog_edit')) {
			message::error('Нет прав доступа для сохранения', FALSE, TRUE);
			exit;
		}


		$tree = json_decode($this->input->post('tree', NULL), TRUE);

		$this->tree = array();
		$this->counter = 0;
		$this->level_zero = 0;

		$this->_calculate_mptt($tree);

		if ($this->level_zero > 1){
			message::error('Дерево не может быть сохранено', FALSE, TRUE);
			exit;
		}

		$table = new CatalogGroups_Model();
		foreach($this->tree as $node){
			$fill = array(
				'level'	 => $node['level'],
				'lft'	 => $node['lft'],
				'rgt'	 => $node['rgt']
			);
			$table->update($fill, array('id' => $node['id']));
		}

		//очищаем кэш
		Cache::instance()->delete_tag('catalog');

		log::add('catalog', 'Сохранение дерева групп товаров');

		message::info('Дерево успешно сохранено', FALSE, TRUE);
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