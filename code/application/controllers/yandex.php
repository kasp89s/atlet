<?php
/**
 * Карта сайта
 *
 */
class Yandex_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}


	public function index() {
		$this->frame="clear";
		header('Content-type: text/xml');

		if(!$ymlCache = Cache::instance()->get('catalog_yml')){

			$this->template = new View('yml/main');

			$objElements = new Catalog_Model();
	        $arrElements=$objElements->db
				  ->select('*')
				  ->from($objElements->table_name)
				  ->where('active', 1)
				  ->where('in_yml', "1")
				  ->order_by('sort_order','asc')
				  ->get()
				  ->rows();

			$objCatalog=new Catalog_Controller();
		    $objCatalog->create_cache();

	        $plane_tree = Cache::instance()->get('catalog_plane_tree');

	        foreach($arrElements as $value)
	            {
	                $arrElementsId[]=$value['id'];
	            }

	        $this->template->elementsFiles = DBFile::select_all('catalog', $arrElementsId);

	        $this->template->elements = $arrElements;
	        $this->template->groups = $plane_tree;
	        $this->template->catalog_uri_base = $this->get_uri_base('catalog');

	        $ymlCache = $this->template->render(false);

	        Cache::instance()->set('catalog_yml', $ymlCache, 'catalog', 2678400); //месяц
	    }else{	     	echo($ymlCache);
	    }
	}

}
?>