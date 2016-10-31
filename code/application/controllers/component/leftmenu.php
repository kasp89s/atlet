<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_Leftmenu_Controller extends Component_Controller {

	protected $cache = array(
		'caching'      => TRUE,
		'page'         => 'component_leftmenu',
		'tags'         => 'catalog',
		'no_vars'      => TRUE,
		'lifetime'     => 86400 //сутки
	);

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		if(!$this->cache_data)
			$this->_assign();
	}


	private function _assign(){
		$mdlCatalog = new CatalogGroups_Model;
		$table = new Catalog_Model();

		$mdlCatalog ->info_content();
		$data['groups'] = $mdlCatalog->db
			->select('self.*')
			->from($mdlCatalog->table_name)
			->where('level',1)
			->where('active',1)
			->where('is_vip',0)
			->order_by('is_vip','desc')
			->order_by('self.title')
			->get()
			->rows();


		$arrManufacturersInfo = $table->db
		 		->select(Array('m.name','m.id', 'cnt' => db::expr('count(ml_p.id)')))
			 	->from(Array('ml_m' => 'catalog_manufacturer'))
			 	->left_join(Array('ml_p' => 'catalog'), 'p.manufacturer_id', 'm.id')
                ->where('p.price', '>', db::expr('0'))
                ->where('p.active', 1)
			 	->group_by('m.id')
			 	->order_by('m.name')
			 	->get()
			 	->rows();

		$this->template = new View('component/leftmenu');
		$this->template->uri_base = $this->get_uri_base('catalog');
		$this->template->action_uri_base = $this->get_uri_base('action');
		if(isset($this->data['group_id'])){
			$this->template->intActiveGroup = $this->data['group_id'];
			if($this->data['sub_group_id']>0){
				$this->template->intActiveSubGroup = $this->data['sub_group_id'];
			}

		}

		$cntrCatalog=new Catalog_Controller();
	    $cntrCatalog->create_cache();

	    $plane_tree = Cache::instance()->get('catalog_plane_tree');

		$mdlCatalogGroups=new CatalogGroups_Model();
        $arrTreeGroups=$mdlCatalogGroups->get_grouped_array($plane_tree);

		$data['tree_groups'] = $arrTreeGroups;
		$data['manufacturers'] = $arrManufacturersInfo;
        $this->template->data = $data;
	}




}
?>
