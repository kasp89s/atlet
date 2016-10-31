<?php
/**
 * Карта сайта
 *
 */
class Map_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}


	public function index() {
		$this->template = new View('map/index');

		if($tree = Cache::instance()->get('cms_plane_tree')){
			$this->template->tree = $tree;
		}

        Catalog_Controller::create_cache();
		if($catalog = Cache::instance()->get('catalog_plane_tree')){
			$this->template->catalog = $catalog;
			$this->template->catalog_uri_base = $this->get_uri_base('catalog');
		}
	}

}
?>