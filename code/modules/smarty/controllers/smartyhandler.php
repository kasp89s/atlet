<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Класс с набором основных функций
 * для всех всех типов шаблонов (фреймы, сердцевина, компоненты)
 *
 */
class Smartyhandler_Controller extends Controller {


	/**
	 * Добавление компонента в очередь
	 *
	 * @param string $place переменная в шаблоне
	 * @param string $tepmlate_name краткой название компоненты (контроллера)
	 * @param array $data  массив данных, передаваемый в контроллер компоненты
	 */
	function add_component($place, $tepmlate_name, $data = array()){
		smartyvars::$components[] = array(
			'place'            => $place,
			'tepmlate_name'    => $tepmlate_name,
			'data'             => $data,
			'action'           => 'add'
		);
	}

	/**
	 * Замена компонента в очереди
	 *
	 * @param string $place переменная в шаблоне
	 * @param string $tepmlate_name краткой название компоненты (контроллера)
	 * @param array $data  массив данных, передаваемый в контроллер компоненты
	 */
	function replace_component($place, $tepmlate_name, $data = array()){
		smartyvars::$components[] = array(
			'place'            => $place,
			'tepmlate_name'    => $tepmlate_name,
			'data'             => $data,
			'action'           => 'replace'
		);
	}

	/**
	 * Удаление компонента из очереди
	 * Очиста по переменной в шаблоне
	 *
	 * @param string $place переменная в шаблоне
	 */
	function del_component($place){
		smartyvars::$components[] = array(
			'place'            => $place,
			'action'           => 'del'
		);
	}


	/**
	 * Добавление атрибута в очередь
	 *
	 * @param string $type  тип атрибута
	 * @param string $value  значение атрибута
	 */
	public function set_attribute($type, $value){
		if(empty($type) || empty($value))
			return false;

		/**
		 * Проверкана уникальность, чтобы не добавлять дубли
		 */
		switch ($type){
			case 'head':
				foreach (smartyvars::$attributes['head'] as $v){
					if($v == $value)
					return;
				}
				smartyvars::$attributes['head'][] = $value;
				break;
			case 'title':
				smartyvars::$attributes['title'] = $value;
				break;
			case 'keywords':
				smartyvars::$attributes['keywords']  = $value;
				break;
			case 'description':
				smartyvars::$attributes['description']  = $value;
				break;

			case 'js':
				$attr = '<script type="text/javascript" src="'.$value.'"></script>';
				if(!empty(smartyvars::$attributes['head'])) {
					foreach (smartyvars::$attributes['head'] as $v){
						if($v == $attr)
						return;
					}
				}
				smartyvars::$attributes['head'][]  = $attr;
				break;

			case 'css':
				$attr = '<link rel="stylesheet" href="'.$value.'" type="text/css" />';
				if(!empty(smartyvars::$attributes['head'])) {
					foreach (smartyvars::$attributes['head'] as $v){
						if($v == $attr)
						return;
					}
				}
				smartyvars::$attributes['head'][]  = $attr;
				break;
			case 'breadcrumbs':
				smartyvars::$attributes['breadcrumbs'][]  = $value;
				break;
		}

	}

	function add_attribute($type, $value){
		return $this->set_attribute($type, $value);
	}

	/**
	 * Получение URI запрашиваемого компонента
	 * В дереве CMS ищется первый раздел, за которым
	 * закреплен модуль(контролер) $component
	 *
	 * @param string $component
	 * @return string
	 */
	protected function get_uri_base($component = FALSE){
		if(!$component)
			return FALSE;

		if($cms = Cache::instance()->get('cms_plane_tree')){
			foreach ($cms as $v){
				if($v['type'] == 'module' && $v['target'] == $component){
					return $v['uri_base'];
				}
			}
		}

		return FALSE;
	}


	/**
	 * Получение закэшированной страницы
	 *
	 * @param string $name
	 * @param string $vars
	 * @return string
	 *
	 * @author Antuan
	 */
	protected function get_cache_data($name = FALSE, $vars = FALSE, $ignored_vars = FALSE) {
		if($name == FALSE) {
			return FALSE;
		}

		if($vars == FALSE && $ignored_vars == FALSE) {
			$vars = input::instance()->get();
		}



		if(empty($vars)) {
			$cacheKey = 'empty';
		} else {
			asort($vars);
			$queryString = http_build_query($vars);
			$cacheKey = md5($queryString);
		}

		if($cachedData = Cache::instance()->get('page_'.$name.'_'.$cacheKey)) {
			return $cachedData;
		} else {
			return FALSE;
		}
	}

	/**
	 * Кэширование страницы
	 *
	 * @param string $data
	 * @param string $name
	 * @param string $vars
	 *
	 * @author Antuan
	 */
	protected function set_cache_data($data, $name = FALSE, $tags = FALSE, $vars = FALSE, $ignored_vars = FALSE, $lifetime = FALSE) {
		if($name == FALSE || $tags == FALSE) {
			return FALSE;
		}

		if($vars == FALSE && $ignored_vars == FALSE) {
			$vars = Input::instance()->get();
		}

		if(empty($vars)) {
			$cacheKey = 'empty';
		} else {
			asort($vars);
			$cacheKey = md5(http_build_query($vars));
		}

		if(!$lifetime)
			$lifetime = NULL;

		Cache::instance()->set('page_'.$name.'_'.$cacheKey, $data, $tags, $lifetime);
	}
}
