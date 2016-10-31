<?php
/**
 * Главная страница
 *
 */
class Index_Controller extends T_Controller {

	public function index() {

		$this->template = new View('index');

		/**
		 * Тянем текст главной страницы
		 */
		$table = new Pages_Model();
		$table->info_content();
		$page = $table->db
			->from($table->table_name)
			->where('self.level', 0)
			->where('self.scope', 1)
			->get()
			->row();

		$groupsInfo = new CatalogGroups_Model();
		$groupsInfo->info_content();

		$arrGroups = $groupsInfo->db
					->select('self.*')
					->from($groupsInfo->table_name)
					->where('is_vip', '>=', '1')
					->where('level', '=', '1')
					->order_by('self.lft')
					->get()
					->rows();

        $arrElementsId = Array();
		foreach($arrGroups as $k => $v){
        	$objElements = new Catalog_Model();
        	$objElements->vip_cat_content($v['id']);
			$objElements->db
				->left_join($objElements->_catgroups_contents,$objElements->_catgroups_contents.'.id','self.group_id')
				->where('vipcontent.active', 1)
				->where($objElements->_catgroups_contents.'.active', 1);

	        $arrElements=$objElements->db
				  ->select('self.*', db::expr('ml_catalog_manufacturer.name as `manufacturer_name`'))
                  ->left_join('catalog_manufacturer', 'self.manufacturer_id','catalog_manufacturer.id')
				  //->left_join($objElements->_catgroups_contents,$objElements->_catgroups_contents.'.id','self.group_id')
				  //->where('vipcontent.active', 1)
				  //->where($objElements->_catgroups_contents.'.active', 1);
				  ->where('self.active', 1)
				  ->order_by('sort_order','asc')
                  ->order_by('self.availability', 'desc')
				  ->limit($this->input->get('limit',30))
				  ->get()
				  ->rows();

			$arrGroups[$k]['elements'] = $arrElements;

			foreach($arrElements as $value){
                $arrElementsId[]=$value['id'];
            }
		}

		$objCatalog=new Catalog_Controller();
	    $objCatalog->create_cache();

        $plane_tree = Cache::instance()->get('catalog_plane_tree');



        $this->template->elementsFiles = DBFile::select_all('catalog', $arrElementsId);
        $this->template->plainTree = $plane_tree;

        $this->template->groups = $arrGroups;
        $this->template->pageLimit = $this->input->get('limit',8);
        $this->template->additionalText = $page['description'];
        $this->template->catalog_uri_base = $this->get_uri_base('catalog');
	}

}
?>
