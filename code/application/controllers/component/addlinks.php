<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_AddLinks_Controller extends Component_Controller {

    /**
	 * Параметры кэширования
	 *
	 * @var array
	 */
	protected $cache = array(
		'caching'      => TRUE,
		'page'         => 'component_addlinks',
		'tags'         => 'links',
		'no_vars'      => TRUE,
		'lifetime'     => 86400 //сутки
	);

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		//if(!$this->cache_data)
			$this->_assign();
	}


	private function _assign(){
		$this->template = new View('component/addlinks_dynamic');

		$table = new Links_Model;
		$links = $table->db
			->select('self.*')
			->where(array('self.active' => 1))
			->from($table->table_name)
			->order_by('self.sort','asc')
			->get()
			->rows();

		if(empty($links))
			return false;


		$arrItemsId = Array();
		$data['rows'] = Array();
		foreach ($links as $v){
			$data['rows'][] = $v;
			$arrItemsId[]=$v['id'];
		}

		$this->template->data = $data;
	}




}
?>