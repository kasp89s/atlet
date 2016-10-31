<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Модуль данных
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 * 
 * @modified by Antuan
 */
class Model_Core {

	public $db = 'default';
	protected $in_transaction = false;
    
	public function __construct(){
		if (!is_object($this->db)) {
			$this->table_prefix = Kohana::config('database.'.$this->db.'.table_prefix');
		} else {
			$this->table_prefix = $this->db->table_prefix();
		}
		if(!empty($this->table_name))
			$this->table_name = array($this->table_prefix . 'self' => $this->table_name);
		
		if ( ! is_object($this->db)){
			//$this->db = Database::instance($this->db);
			$this->db = db::build($this->db);
			
		}
	}
	

	/**
	 * Lock на таблицу. Не работает
	 *
	 * @param unknown_type $alias
	 * @param unknown_type $action
	 */
	public function lock($alias, $action = 'WRITE') {
		$this->db->query('LOCK TABLE ' . $this->table_prefix . $this->get_cleartable_name() . ' '.$alias.' '.$action);

		$this->_oldtablename = $this->table_name;
		if($alias)
			$this->table_name = $alias;
	}
	
	
	/**
	 * Unlock на таблицу. Не работает
	 *
	 */
	public function unlock() {
		$this->db->query('UNLOCK TABLES');
		$this->table_name = $this->_oldtablename;
	}
	
	
	/**
	 * Получение названия таблицы используемой модели данных
	 *
	 * @return string
	 */
	public function get_table_name() {
		return current($this->table_name);
	} 
	

	/**
	 * Добавление данных
	 *
	 * @param array $set
	 * @return object
	 */
	public function insert($set = NULL){
		return $this->db->insert($this->get_table_name(), $set)->get();
	}
	
	
	/**
	 * Merge данных
	 *
	 * @param array $set
	 * @return object
	 */
	public function merge($set = NULL){
		//return merge($this->get_table_name(), $set);
	}
	
	
	/**
	 * Редактирование данных $set, 
	 * удовлетворяющих условиям $where
	 * 
	 *
	 * @param array $set
	 * @param array $where
	 * @return object
	 */
	public function update($set = NULL, $where = NULL){
		if(is_array($where)) {
			foreach ($where as $k => $v){
				if(is_array($v)){
					$this->db->where($k, 'in', $v);
					unset($where[$k]);
				}
			}
		}
		
		if(empty($where))
			$where = NULL;
			
		return $this->db->update($this->get_table_name(), $set, $where)->get();
	}
	
	
	/**
	 * Удаление данных
	 *
	 * @param array $where
	 * @return object
	 */
	public function delete($where = NULL){
		if(is_array($where)) {
			foreach ($where as $k => $v){
				if(is_array($v)){
					$this->db->where($k, 'in', $v);
					unset($where[$k]);
				}
			}
		}

		if(empty($where))
			$where = NULL;

		return $this->db->delete($this->get_table_name(), $where)->get();
	}	
	
	/**
	 * Статус транзакции
	 *
	 * @return boolean
	 */
	public function trans_status() {
		return $this->db->in_transaction;
	}

	/**
	 * Старт транзакции
	 *
	 */
    public function trans_start() {
		if ($this->in_transaction === false) {
			$this->db->query('SET AUTOCOMMIT=0');
			$this->db->query('BEGIN');
		}
		$this->in_transaction = true;
	}

	/**
	 * Конец транзакции
	 *
	 */
	public function trans_complete() {
		if ($this->in_transaction === true) {
			$this->db->query('COMMIT');
			$this->db->query('SET AUTOCOMMIT=1');
		}
		$this->in_transaction = false;
	}

	/**
	 * Откат транзакции
	 *
	 */
	public function trans_rollback() {
		if ($this->in_transaction === true) {
			$this->db->query('ROLLBACK');
			$this->db->query('SET AUTOCOMMIT=1');
		}
		$this->in_transaction = false;
	}

}