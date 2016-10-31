<?php
/**
 * Компонент. Полезные ссылки
 *
 */
class Component_Links_Controller extends Component_Controller {

	/**
	 * Параметры кэширования
	 *
	 * @var array
	 */
	protected $cache = array(
		'caching'      => TRUE,
		'page'         => 'component_links',
		'tags'         => 'links',
		'no_vars'      => TRUE,
		'lifetime'     => 86400 //сутки
	);


	function __construct($data) {
		if(!$this->check_access('links'))
			return FALSE;


		$this->data = $data;
		parent::__construct();

		if(!$this->cache_data)
			$this->_assign();
	}


	private function _assign(){

		$table = new Links_Model;
		$table->info_cat();
		$links = $table->db
			->select('self.*')
			->where(array('self.active' => 1, 'self.department_id' => DEPARTMENT))
			->from($table->table_name)
			->order_by('links_cat.position', 'desc')
			->get()
			->rows();

		if(empty($links))
			return false;


		$data = array(0=>Array());
		$arrItemsId = Array();
		foreach ($links as $v){
			$data[$v['cat']]['name'] = $v['cat_name'];
			$data[$v['cat']]['rows'][] = $v;
			$arrItemsId[]=$v['id'];
		}

		$this->template = new View('component/links');
		$this->template->data = $data;
		$this->template->files = DBFile::select_all('links', $arrItemsId);
	}

}
?>