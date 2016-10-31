<?php
/**
 * Главная страница
 *
 */
class Action_Controller extends T_Controller {
	private $plane_tree;

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}

	public function index($intPage) {
		 preg_match("#page([0-9]+)#i",$intPage,$arrMatches);

		 if(count($arrMatches)>0&&intval($arrMatches[1])>0){
        	 $intPage=intval($arrMatches[1]);
         }

		 $this->template = new View('catalog/actionpage');

		 $this->create_cache();
         $plane_tree = Cache::instance()->get('catalog_plane_tree');

		 /**
		 * Тянем текст главной страницы
		 */

		 $table = new Pages_Model();
		 $table->info_content();
		 $page = $table->db
			  ->from($table->table_name)
			  ->where('self.target', 'action')
			  ->get()
			  ->row();


         $arrData=array(
        	'text'=>$page['title']
         );

         $this->add_component('sectiontitle', 'sectiontitle',$arrData);


         $arrData=Array(
        	 'order_val'=>$this->input->get('order', ''),
        	 'current_url'=>$this->get_uri_base('action')
         );

         $this->add_component('sortfilter', 'sortfilter',$arrData);
         $this->add_component('seorelative', 'seorelative',false);
         $this->add_component('socialicons', 'socialicons', Array('text' => $page['title']));

         $this->add_attribute('title', $page['seo_title'].($intPage>1?' Страница '.$intPage : ''));
		 $this->add_attribute('keywords', $page['seo_keywords']);
		 $this->add_attribute('description', $page['seo_description']);

		 /**
		 * Биндим заголовки
		 */

		 $groups = new CatalogGroups_Model();

         $arrTreeGroups=$groups->get_grouped_array($plane_tree);

		 $objElements = new Catalog_Model();


   		 $objElements->db
  			  ->select('self.*')
  			  ->from($objElements->table_name)
  			  ->where('self.active', 1)
  			  ->where('self.in_action', 1);

         $strSortOrder=$this->input->get('order', '');

         if(substr($strSortOrder,0,5)=="price"){
			  $objElements->db
			  		->order_by(Array('self.price'=>'asc','self.name'=>'asc'));
			  $strTmp=str_replace("price_","",$strSortOrder);
			  $arrValues=explode("_",$strTmp);
              $arrValues[0]=intval($arrValues[0])*1000;
              $arrValues[1]=intval($arrValues[1])*1000;

              if($arrValues[0] > 0){
                  $objElements->db
					  ->where('price', '>', $arrValues[0]);
              }

              if($arrValues[1] > 0){
                  $objElements->db
					  ->where('price', '<', $arrValues[1]);
              }

		 }elseif(substr($strSortOrder,0,5)=="articul"){
			  $objElements->db
			  		->order_by(Array('self.code'=>'asc','self.name'=>'asc'));
		 }else{
		 	  $objElements->db
		 	  		->order_by(Array('self.sort_order'=>'asc','self.price'=>'asc','self.name'=>'asc'));
		 }

         $tm = new Tablemaker($objElements);
		 if($intPage>0)$tm->current_page=$intPage;
		 $tm->default_page_size = 20;
		 $tm->pager_window = 2;

		 $tm->find_files = true;
		 $tm->table_of_files = 'catalog';

		 $arrTmData=$tm->show();

         $arrData=Array(
         	'uri_base'  => $this->get_uri_base('action'),
         	'total_pages'  => $tm->pager_info['total_pages'],
         	'current_page' => $tm->pager_info['page'],
         	'pages_array'  => $tm->pager_info['pages'],
         );

		 $this->add_component('pager', 'pager', $arrData);

         $this->template->elements=$arrTmData['rows'];

         $this->template->groups = $plane_tree;
         $this->template->additionalText = $page['description'];
         $this->template->catalog_uri_base = $this->get_uri_base('catalog');


	}


	private function create_cache() {
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
			$this->plane_tree = array();
			$tree = $this->order_tree($items);

			Cache::instance()->set('catalog_tree', $tree, array('catalog'), 2678400); //месяц
			Cache::instance()->set('catalog_plane_tree', $this->plane_tree, array('catalog'), 2678400); //месяц
		}
	}

	/**
	 * Выстраивание дерева из плоского массива
	 *
	 * @param array $items
	 * @return array
	 */
	private function order_tree($items = false, $uri_parent = '', $parent_id=0){
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
					$tree[$root_node]['rows'] = $this->order_tree($childrens, $tree[$root_node]['uri_base'], $tree[$root_node]['id']);
					$childrens = array();
				}

				if($_level == 0)
					$root_node = 'root';
				else
					$root_node = $page['uri'];

				$tree[$root_node] = $page;

				$tree[$root_node]['uri_base'] = ($root_node != 'root') ? $uri_parent . '/' . $root_node : '';

				$this->plane_tree[$tree[$root_node]['id']] = $tree[$root_node];
				$this->plane_tree[$tree[$root_node]['id']]['parent_id'] = $parent_id;

			} elseif($page['level'] > $_level) {
				$childrens[] = $page;

			} else {
				return $tree;
			}
			$i++;
		}

		if(count($childrens))
			$tree[$root_node]['rows'] = $this->order_tree($childrens, $tree[$root_node]['uri_base'], $tree[$root_node]['id']);

		return $tree;
	}

}
?>