<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_SeoRelative_Controller extends Component_Controller {

    static $plane_tree = Array();

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		$this->_assign();
	}


	private function _assign(){

		$this->template = new View('component/seorelative');

		$mdlProducts = new Catalog_Model();
		$mdlGroups = new CatalogGroupContents_Model();

		$this->create_cache();
        $plane_tree = Cache::instance()->get('catalog_plane_tree');


//        $favorite = $mdlProducts->db
//            ->select(Array('self.*', db::expr('(`'.$mdlProducts->table_prefix.'self`.`relative_shows`/`'.$mdlProducts->table_prefix.'self`.`relative_weight`) as `views_coef`'), db::expr('RAND() as `rnd`')))
//            ->from($mdlProducts->table_name)
//            ->left_join(Array("ml_groups" => $mdlProducts->_catgroups_contents), 'self.group_id','groups.group_id')
//
//            ->where('groups.active', '>', db::expr('0'))
//            ->where('self.active', '>', db::expr('0'))
//            ->where('self.price', '>', db::expr('0'))
//
//            ->where('self.group_id', $this->data['group_id'])
//            ->where('self.manufacturer_id', $this->data['manufacturer_id'])
//            ->order_by('rnd', 'asc');

		$mdlProducts->db
			->select(Array('self.*', db::expr('(`'.$mdlProducts->table_prefix.'self`.`relative_shows`/`'.$mdlProducts->table_prefix.'self`.`relative_weight`) as `views_coef`'), db::expr('RAND() as `rnd`')))
            ->from($mdlProducts->table_name)
			->left_join(Array("ml_groups" => $mdlProducts->_catgroups_contents), 'self.group_id','groups.group_id')
			//->where('self.relative_weight', '>', db::expr('0'))
			->where('groups.active', '>', db::expr('0'))
			->where('self.active', '>', db::expr('0'))
			->where('self.price', '>', db::expr('0'))
			//->where('self.is_show_in_relative', '1')
            ->where('self.name', '=' ,$this->data['name'])
            ->or_where('self.group_id', '>=' ,$this->data['group_id'])
            ->where('self.manufacturer_id', '>=' ,$this->data['manufacturer_id'])
            ->where('self.id', '!=' ,$this->data['id'])
			->order_by('group_id', 'asc')
			->order_by('manufacturer_id', 'asc')
            ->order_by('views_coef', 'asc')
            ->order_by('rnd', 'asc')
			->limit(12);

		$tm = new Tablemaker($mdlProducts);
		$tm->paged = false;
		$tm->find_files = true;
		$tm->table_of_files = 'catalog';
		$arrProducts = &$tm->show();

		$arrProducts = $arrProducts['rows'];
        
        $tmpArray = array();
        foreach ($arrProducts as $key => $value){
            if ($value['name'] == $this->data['name']) {
                $tmpArray[] = $arrProducts[$key];
                unset($arrProducts[$key]);
            }
        }
        $arrProducts = array_merge($tmpArray, $arrProducts);
		$arrProductsIDs=Array();
		foreach($arrProducts as $key => $value){
			$arrProductsIDs[] = $value['id'];
			@$arrProducts[$key]['uri_base'] = $plane_tree[$value['group_id']]['uri_base'].'/'.$value['uri'];
		}


        if(count($arrProductsIDs)>0){
			$mdlProducts->update(Array('relative_shows' => db::expr('`relative_shows` + 1')),Array('id' => $arrProductsIDs));
		}


		$mdlGroups->db
			->select(Array('*', db::expr('(`relative_shows`/`relative_weight`) as `views_coef`'), db::expr('RAND() as `rnd`')))
			->from($mdlGroups->table_name)
			->where('relative_weight', '>', db::expr('0'))
			->where('active', '>', db::expr('0'))
			->where('is_show_in_relative', '>', db::expr('0'));

		$tm = new Tablemaker($mdlGroups);
		$tm->default_page_size = 4;
		$tm->find_files = true;
		$tm->table_of_files = 'catalog_groups';
		$tm->orderby = array('views_coef' => 'asc', 'rnd' => 'asc');
		$arrGroups = &$tm->show();

		$arrGroups=$arrGroups['rows'];

		$arrGroupsIDs=Array();
		foreach($arrGroups as $key => $value){
			$arrGroupsIDs[] = $value['id'];
			@$arrGroups[$key]['uri_base'] = $plane_tree[$value['group_id']]['uri_base'];
		}


        if(count($arrGroupsIDs)>0){
			$mdlGroups->update(Array('relative_shows' => db::expr('`relative_shows` + 1')),Array('id' => $arrGroupsIDs));
        }




		$this->template->products = $arrProducts;
		$this->template->groups = $arrGroups;
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
