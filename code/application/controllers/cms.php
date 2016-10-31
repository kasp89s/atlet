<?php
/**
 * CSM
 *
 * @author Antuan
 * $created 23.09.2009
 *
 */
class Cms_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}

	/**
	 * Самостоятельный вывод в шаблоне
	 *
	 * @param id $page_id
	 */
	public function index($page_id) {
		$this->template = new View('cms');


		$table = new Pages_Model();
		$table->info_content();
		$page = $table->db
			->select('self.*')
			->from($table->table_name)
			->where('self.id', $page_id)
			->where('self.scope', 1)
			->get()
			->row();

		if(count($page)) {
			$this->template->main = $page;

		} else {
			$this->template->error = 1;
		}
	}
}
?>