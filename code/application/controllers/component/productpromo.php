<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_Productpromo_Controller extends Component_Controller {

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		$this->_assign();
	}


	private function _assign(){		$arrItem=Array();

		$this->create_cache();
        $plane_tree = Cache::instance()->get('catalog_plane_tree');

		if(isset($this->data['curGroup'])&&intval($this->data['curGroup'])>0){
			$mdlCatalog = new CatalogGroups_Model;

			$mdlCatalog ->info_content();

            $arrFilesFilter=Array(
				'files.item_id' => 'products.id',
				"files.item_table" => db::expr("'catalog'"),
				'files.input_name' => db::expr("'imageleft'")
			);

			$arrItems = $mdlCatalog->db
				->select(Array('files.*', 'product_name' => 'products.name','products.code','products.uri','sect_id' => 'product_section.id'))
				->left_join(Array('ml_products'=>'catalog'), 'products.code', 'catalog_group_contents.code')
				->left_join(Array('ml_product_section'=>'catalog_group_contents'), 'product_section.group_id', 'products.group_id')
				->left_join(Array('ml_files'=>'files'), $arrFilesFilter, NULL)
				->from($mdlCatalog->table_name)
				->where('catalog_group_contents.active',1)
				->where('products.active',1)
				->where('products.is_show_in_left_block',1)
				->where('self.level>',0)
				->where("catalog_group_contents.code <>''")
				->where('self.lft<=',$this->data['curGroup']['lft'])
				->where('self.rgt>=',$this->data['curGroup']['rgt'])
				->where('files.file_size>',0)
				->order_by('self.lft','desc')
				->limit(10)
				->get()
				->rows();

	        foreach($arrItems as $key => $value){
	        	if(is_file(DOCROOT.$value['src'])){
	        		$arrItem[]=$value;
	        		$arrItem[count($arrItem)-1]['group_uri'] = $plane_tree[$value['sect_id']]['uri_base'].'/';
	        		if(count($arrItem) >=3)break;
	        	}
	        }

        }

        if(count($arrItem)<3){
            $mdlCatalog = new CatalogGroups_Model;


            $arrFilesFilter=Array(
				'files.item_id' => 'products.id',
				'files.item_table' => db::expr("'catalog'"),
				'files.input_name' => db::expr("'imageleft'")
			);

			$arrItems = $mdlCatalog->db
				->select(Array('files.*', 'product_name' => 'products.name','products.code','products.uri','sect_id' => 'product_section.id'))
				->select(db::expr('rand() as rnd'))
				->from(Array('ml_products'=>'catalog'))
				->left_join(Array('ml_product_section'=>'catalog_group_contents'), 'product_section.group_id', 'products.group_id')
				->left_join(Array('ml_files'=>'files'), $arrFilesFilter, NULL)
				->where('product_section.active',1)
				->where('products.active',1)
				->where('products.is_show_in_left_block',1)
				->where('files.file_size','>',db::expr('0'))
				->order_by('rnd')
				->limit(10)
				->get()
				->rows();

	        foreach($arrItems as $key => $value){
	        	if(is_file(DOCROOT.$value['src'])){
	        		$arrItem[]=$value;
	        		$strExt = explode(".", $value['src']);
	              	$arrItem[count($arrItem)-1]['ext'] = $strExt[count($strExt)-1];
	              	$arrItem[count($arrItem)-1]['group_uri'] = $plane_tree[$value['sect_id']]['uri_base'].'/';
	              	if(count($arrItem) >= 3)break;
	        	}
	        }
        }

        if(count($arrItem)<3){
            $mdlCatalog = new CatalogGroups_Model;


            $arrFilesFilter=Array(
				'files.item_id' => 'products.id',
				'files.item_table' => db::expr("'catalog'"),
				'files.input_name' => db::expr("'imageleft'")
			);

			$arrItems = $mdlCatalog->db
				->select(Array('files.*', 'product_name' => 'products.name', 'products.code','products.uri','sect_id' => 'product_section.id'))
				->select(db::expr('rand() as rnd'))
				->from(Array('ml_products'=>'catalog'))
				->left_join(Array('ml_product_section'=>'catalog_group_contents'), 'product_section.group_id', 'products.group_id')
				->left_join(Array('ml_files'=>'files'), $arrFilesFilter, NULL)
				->where('product_section.active',1)
				->where('products.active',1)
				->where('files.file_size','>',db::expr('0'))
				->order_by('rnd')
				->limit(10)
				->get()
				->rows();

	        foreach($arrItems as $key => $value){
	        	if(is_file(DOCROOT.$value['src'])){
	        		$arrItem[]=$value;
	        		$strExt = explode(".", $value['src']);
	              	$arrItem[count($arrItem)-1]['ext'] = $strExt[count($strExt)-1];
	              	$arrItem[count($arrItem)-1]['group_uri'] = $plane_tree[$value['sect_id']]['uri_base'].'/';
	              	if(count($arrItem) >= 3)break;
	        	}
	        }
        }

        $this->template = new View('component/productpromo');


		$this->template->data = $arrItem;
		$this->template->catalog_uri_base = $this->get_uri_base('catalog');
	}

    public static function create_cache() {
		if(( !$tree = Cache::instance()->get('catalog_tree') ) || !Cache::instance()->get('catalog_plane_tree')) {

			$table_items = new CatalogGroups_Model();
			$table_items->info_content();
			$items = $table_items->db
				->select(Array('self.*', 'seo_title', 'seo_keywords', 'seo_description'))
				->from($table_items->table_name)
				->where('self.scope', 1)
				->order_by('self.lft')
				->get()
				->rows();


			/**
			 * Строим дерево и кэшируем
			 */
			self::$plane_tree = array();
			$tree = self::order_tree($items);

			Cache::instance()->set('catalog_tree', $tree, array('catalog'), 2678400); //месяц
			Cache::instance()->set('catalog_plane_tree', self::$plane_tree, array('catalog'), 2678400); //месяц
		}
	}

	/**
	 * Выстраивание дерева из плоского массива
	 *
	 * @param array $items
	 * @return array
	 */
	public static function order_tree($items = false, $uri_parent = ''){
		if(!$items)
			return false;

		reset($items);
		$_node = current($items);
		$_level = $_node['level'];

		$tree = array();
		$childrens = array();

		$i = 0;
		foreach ($items as $k => $page){
			if($page['level'] == $_level) {
				if(count($childrens)){
					$tree[$root_node]['rows'] = self::order_tree($childrens, $tree[$root_node]['uri_base']);
					$childrens = array();
				}

				if($_level == 0)
					$root_node = 'root';
				else
					$root_node = $page['uri'];

				$tree[$root_node] = $page;

				$tree[$root_node]['uri_base'] = ($root_node != 'root') ? $uri_parent . '/' . $root_node : '';

				self::$plane_tree[$tree[$root_node]['id']] = $tree[$root_node];

			} elseif($page['level'] > $_level) {
				$childrens[] = $page;

			} else {
				return $tree;
			}
			$i++;
		}

		if(count($childrens))
			$tree[$root_node]['rows'] = self::order_tree($childrens, $tree[$root_node]['uri_base']);

		return $tree;
	}


}
?>