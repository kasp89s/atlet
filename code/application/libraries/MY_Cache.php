<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Переопределение функций кэширования
 * Кастомизируем кэш для каждой администрации за счет соответствующего префикса
 * 
 * @author Antuan
 */
class Cache extends Cache_Core {

	/**
	 * Префикс для кэша
	 */
	protected $prefix = '';
	
	
	/**
	 * Установка префикса
	 *
	 * @param string $name
	 */
	public function set_prefix($name){
		$this->prefix = $name;
		
	}
	
	
	public function get($id) {
		$id = $this->prefix . '_' . $id;
		
		return parent::get($id);
	}
	
	
	public function get_clean($id) {
		return parent::get($id);
	}
	
	
	public function find($tag) {
		$tag = $this->prefix . '_' . $tag;
		
		return parent::find($tag);
	}

	
	public function set($id, $data, $tags = NULL, $lifetime = NULL) {
		$id = $this->prefix . '_' . $id;
		
		if(is_array($tags)) {
			foreach ($tags as $k => $v){
				$tags[$k] = $this->prefix . '_' . $v;
			}
		} else {
			$tags = array($this->prefix . '_' . $tags);
		}
		$tags[] = $this->prefix;
			
		return parent::set($id, $data, $tags, $lifetime);
	}
	
	
	public function set_clean($id, $data, $tags = NULL, $lifetime = NULL) {
		return parent::set($id, $data, $tags, $lifetime);
	}
	
	
	public function delete($id) {
		$id = $this->prefix . '_' . $id;
		
		return parent::delete($id);
	}
	
	
	public function delete_tag($tag) {
		$tag = $this->prefix . '_' . $tag;
		
		return parent::delete_tag($tag);
	}
	
	
}