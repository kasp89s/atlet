<?php
/**
 * Компонент. Верхнее меню
 *
 */
class Component_Topmenu_Controller extends Component_Controller {

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		$this->_assign();
	}


	private function _assign(){
		$this->template = new View('component/topmenu');
		$mdlCatalog = new CatalogGroups_Model;

		$mdlCatalog ->info_content();
		$data['groups'] = $mdlCatalog->db
			->select('self.*')
			->from($mdlCatalog->table_name)
			->where('level',1)
			->where('active',1)
			->where('is_vip', 0)
			->order_by('is_vip','desc')
			->order_by('self.title')
			->get()
			->rows();

		$this->template = new View('component/topmenu');
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

        $arrFilesFilter=Array(
			'files.item_id' => 'products.id',
			'files.item_table' => db::expr("'catalog'"),
			'files.input_name' => db::expr("'imageleft'")
		);

		foreach($data['groups'] as $k => $v){
            if($v['level'] == 1){
            	$limit = count($arrTreeGroups[$v['id']]['rows']) > 4 ? 4 : 2;
            	$data['groups'][$k]['products'] = $mdlCatalog->db
						->select(Array('files.*', 'product_name' => 'products.name','products.code','products.uri','sect_id' => 'product_section.id'))
						->select(db::expr('rand() as rnd'))
						->from(Array('ml_products'=>'catalog'))
						->left_join(Array('ml_product_section'=>'catalog_groups'), 'product_section.id', 'products.group_id')
						->left_join(Array('ml_files'=>'files'), $arrFilesFilter, NULL)
						->where('product_section.lft', '>=', db::expr($v['lft']))
						->where('product_section.rgt', '<=', db::expr($v['rgt']))
						->where('products.active',1)
						->where('files.file_size','>',db::expr('0'))
						->order_by('rnd')
						->limit($limit)
						->get()
						->rows();
            }
		}

		$data['tree_groups'] = $arrTreeGroups;
        $this->template->data = $data;
        $this->template->plainTree = $plane_tree;

	}




	private function get_cart_id(){
		$strCartID = $this->input->cookie('cart_id', md5(microtime(true)+rand(1,10000)));
		cookie::set('cart_id', $strCartID, Kohana::config('coocie.expire'));

		return $strCartID;
	}


}
?>