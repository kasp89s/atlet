<?php
/**
 * Каталог товаров
 *
 */
class CatalogStats_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Статистика каталога товаров';

		$this->menu = array(
			array('url'=>'/admin/catalogstats/products', 'section'=>'Статистика товаров'),
			array('url'=>'/admin/catalogstats/groups', 'section'=>'Статистика групп'),
		);

		parent::__construct();
	}


	/**
	 * Список товаров
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('catalogstats_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/catalogstats/index');
	}


	/**
	 * Список товаров
	 *
	 */
	public function products() {
		if(!Acl::instance()->is_allowed('catalogstats_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/catalogstats/products');

		$table = new Catalog_Model;
		$table->db
			->select(Array('self.id', 'self.name', 'self.code', 'self.active', 'self.group_id', 'self.is_show_in_relative', 'self.total_shows', 'self.relative_shows', 'self.relative_weight'))
			->from($table->table_name);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('self.total_shows' => 'desc', 'self.relative_shows' =>'desc',
							 'self.relative_weight' => 'desc', 'self.is_show_in_relative' => 'desc',
							 'self.id' => 'desc', 'self.name', 'self.code');
		$data = &$tm->show();

		$this->template->main = $data;
	}

	public function groups() {
		if(!Acl::instance()->is_allowed('catalogstats_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/catalogstats/groups');

		$table = new CatalogGroupContents_Model();
		$table->db
			->select(Array('self.id', 'self.title', 'self.code', 'self.active', 'self.group_id', 'self.is_show_in_relative', 'self.total_shows', 'self.relative_shows', 'self.relative_weight'))
			->from($table->table_name);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('self.total_shows' => 'desc', 'self.relative_shows' =>'desc',
							 'self.relative_weight' => 'desc', 'self.is_show_in_relative' => 'desc',
							 'self.id' => 'desc', 'self.title');
		$data = &$tm->show();

		$this->template->main = $data;
	}





}
?>