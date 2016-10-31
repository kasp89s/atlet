<?php defined('SYSPATH') OR die('No direct access allowed.');

class Component_Controller extends Smartyhandler_Controller {

	public function __construct(){
		/**
		 * Отдаем кэш, если настроен для данного контроллера
		 * 
		 */
		if(!empty($this->cache['caching']) && $this->cache['caching'] === TRUE){
			$this->caching = TRUE;
			
			$page = (!empty($this->cache['page'])) ? $this->cache['page'] : FALSE;
			$tags = (!empty($this->cache['tags'])) ? $this->cache['tags'] : FALSE;
			$vars = (!empty($this->cache['vars'])) ? $this->cache['vars'] : FALSE;
			$ignored_vars = (!empty($this->cache['no_vars'])) ? $this->cache['no_vars'] : FALSE;
			
			$data = $this->get_cache_data($page, $vars, $ignored_vars);
			
			if($data){
				$this->cache_data = $data;
				return;
			} else {
				$this->cache_data = FALSE;
			}
			
		} else {
			$this->caching    = FALSE;
			$this->cache_data = FALSE;
		}
		
		parent::__construct();
	}
	
	
	/**
	 * Кэширование html-кода
	 *
	 */
	public function cache($data){
		if($this->caching && !empty($data)){
			$page = (!empty($this->cache['page'])) ? $this->cache['page'] : FALSE;
			$tags = (!empty($this->cache['tags'])) ? $this->cache['tags'] : FALSE;
			$vars = (!empty($this->cache['vars'])) ? $this->cache['vars'] : FALSE;
			$ignored_vars = (!empty($this->cache['no_vars'])) ? $this->cache['no_vars'] : FALSE;
			$lifetime = (!empty($this->cache['lifetime'])) ? $this->cache['lifetime'] : FALSE;
			
			$this->set_cache_data($data, $page, $tags, $vars, $ignored_vars, $lifetime);
			
		} 
	}
	
	
	/**
	 * Проверка прав доступа администрации к модулю
	 *
	 */
	protected function check_access($type = FALSE){
		if(!$type)
			return FALSE;
			
			
		if($tree = Cache::instance()->get('cms_plane_tree')){
			foreach ($tree as $node) {
				if($node['type'] == 'module' && $node['target'] == $type)
					return TRUE;
			}
		}
		
		return FALSE;
	}
}
