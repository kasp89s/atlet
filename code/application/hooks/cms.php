<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Хук для создания CMS
 * Все запросы замыкаются на CMS, контроллеры становятся второстепенными
 */
Event::add_before('system.execute', array('Kohana', 'instance'), array('cms', 'execute'));

class cms{
	/**
	 * Вспомогательный массив для линейного
	 * отображения дерева массива
	 *
	 * @var array
	 */
	public static $plane_tree;


	/**
	 * Старт CMS
	 */
	public static function execute(){
		cms::load_settings();

		if(Router::$skip === TRUE) {
			return ;
		} else {
			cms::load_content();
		}
	}


	/**
	 * Загрузка настроек CMS
	 *
	 */
	public static function load_settings(){
		if(!$settings = Cache::instance()->get('cms_settings')) {
			$table_settings = new Settings_Model();

			$settings = $table_settings->db
				->select()
				->from($table_settings->table_name)
				->get()
				->rows();


			Cache::instance()->set('cms_settings', $settings, array('cms'), 2678400); //месяц
		}

		foreach ($settings as $v){
			Kohana::config_set('cms.'.$v['context'].'.'.$v['key'], $v['value']);
		}
	}


	/**
	 * Тянем и отдаем контент
	 * Правила роутинга CMS:
	 * 1. Ищем данные в CMS
	 *   а. нашли текстовый раздел - отдали текст
	 *   б. нашли раздел с закрепленным контроллером - отдали текст и выполненный контроллер
	 *   в. нашли раздел с редиректом - сделали редирект
	 * 2. Ничего не нашли - отдали 404
	 *
	 */
	public static function load_content() {
		$tree = cms::create_cache();

		$segments        = explode('/', Router::$current_uri);
		$segments        = array_diff($segments, array(''));
		$segments_tail   = $segments;
		$segments_head   = array();
		$type            = false;
		$uri_base        = '';
		$page            = array();


        $arrBreadCrumbs = Array();

		/**
		 * Парсим ссылку
		 */
		if(empty($segments) || $segments[0] == 'index') {
			//Главная страница
			$is_url           = true;
			$type             = 'module';
			$uri_controller   = 'index';
			$page             = $tree['root'];

		} else {
			$is_url  = true;
			$row     = &$tree['root'];
			$alias   = '';

			foreach ($segments as $k => $v) {
				if(empty($row['rows'][$v])) {
					$is_url = false;
					break;
				}

				$row                 = &$row['rows'][$v];
				$page                = $row;
				$segments_head[$k]   = $v;

				$curbreadCrumb = count($arrBreadCrumbs);
				$arrBreadCrumbs[$curbreadCrumb]['title'] =  $row['title'];
				$arrBreadCrumbs[$curbreadCrumb]['uri'] =  $row['uri_base'];

				unset($segments_tail[$k]);
			}

			unset($row);
			$type = (!empty($page['type'])) ? $page['type'] : 'none';
			if($type == 'module'){
				$is_url = true;
				$segments_tail[0]  = $page['target'];
				ksort($segments_tail);
				$uri_controller    = implode('/', $segments_tail);
				$uri_base          = '/' . implode('/', $segments_head);
			}
		}

		/**
		 * Запоминаем в глобальный конфиг текущую страницу
		 */
		if(!empty($page['rows'])) unset($page['rows']);
		Kohana::config_set('cms.page_active', $page);


		/**
		 * Увеличиваем счетчик посещений данного раздела
		 */
		if(!empty($page['id']))
			db::update('page_contents', array('counter' => db::expr('`counter` + 1')), array('page_id' => $page['id']));

		/**
		 * Запуск контроллеров
		 */
		if($is_url){
			switch ($type){
				case 'none':
					$_SERVER['PATH_INFO'] = 'cms/index/'.$page['id'];
					Router::find_uri();
					Router::setup();
					foreach($arrBreadCrumbs as $key => $value){
						Smartyhandler_Controller::set_attribute('breadcrumbs', $value);
					}
					break;

				case 'module':
					$_SERVER['PATH_INFO'] = $uri_controller;
					Router::find_uri();
					Router::setup();
					Kohana::config_set('cms.page_active.uri_base', $uri_base);
					foreach($arrBreadCrumbs as $key => $value){
						Smartyhandler_Controller::set_attribute('breadcrumbs', $value);
					}
					break;

				case 'redirect':
					url::redirect($page['target']);
					break;
			}
		} else {
			$_SERVER['PATH_INFO'] = 'catalog2/'.implode("/",$segments);
			Router::find_uri();
			Router::setup();
			Kohana::config_set('cms.page_active.uri_base', $uri_base);
		}

	}



	/**
	 * Дополнение к роутингу.
	 * Тянем и кэшируем дерево cms
	 *
	 */
	public static function create_cache() {
		if(( !$tree = Cache::instance()->get('cms_tree') ) || !Cache::instance()->get('cms_plane_tree')) {

			$table_pages = new Pages_Model();
			$pages = $table_pages->db
				->select(array('self.*', 'page_contents.uri', 'seo_title', 'seo_keywords', 'seo_description'))
				->from($table_pages->table_name)
				->left_join('page_contents', 'page_contents.page_id', 'self.id')
				->where('self.scope', '=', 1)
				->order_by('self.lft')
				->get()
				->rows();


			/**
			 * Тянем допустимые модули для администрации
			 */
			$table_modules = new Modules_Model();
			$rows = $table_modules->db
				->select()
				->from($table_modules->table_name)
				->get()
				->rows();

			$modules = array();
			foreach ($rows as $v)
				$modules[] = $v['code'];


			/**
			 * Выкидываем разделы CMS с
			 * неразрешенными модулями
			 */
			foreach ($pages as $k => $v){
				if($v['type'] == 'module' && !in_array($v['target'], $modules))
					unset($pages[$k]);
			}


			/**
			 * Строим дерево и кэшируем
			 */
			cms::$plane_tree = array();
			$tree = cms::order_tree($pages);

			Cache::instance()->set('cms_tree', $tree, array('cms'), 2678400); //месяц
			Cache::instance()->set('cms_plane_tree', cms::$plane_tree, array('cms'), 2678400); //месяц
		}

		return $tree;
	}

	/**
	 * Выстраивание дерева из плоского массива
	 *
	 * @param array $pages
	 * @return array
	 */
	public static function order_tree($pages = false, $uri_parent = ''){
		if(!$pages)
			return false;

		reset($pages);
		$_node = current($pages);
		$_level = $_node['level'];

		$tree = array();
		$childrens = array();

		$i = 0;
		foreach ($pages as $k => $page){
			if($page['level'] == $_level) {
				if(count($childrens)){
					$tree[$root_node]['rows'] = cms::order_tree($childrens, $tree[$root_node]['uri_base']);
					$childrens = array();
				}

				if($_level == 0)
					$root_node = 'root';
				else
					$root_node = $page['uri'];

				$tree[$root_node] = $page;

				$tree[$root_node]['uri_base'] = ($root_node != 'root') ? $uri_parent . '/' . $root_node : '';

				cms::$plane_tree[$tree[$root_node]['id']] = $tree[$root_node];

			} elseif($page['level'] > $_level) {
				$childrens[] = $page;

			} else {
				return $tree;
			}
			$i++;
		}

		if(count($childrens))
			$tree[$root_node]['rows'] = cms::order_tree($childrens, $tree[$root_node]['uri_base']);

		return $tree;
	}
}

?>