<?php
/**
 * Карта сайта
 *
 */
class Xmlmap_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}


	public function index() {
		$this->frame="clear";
		$this->template = new View('map/xml');
		header('Content-type: text/xml');

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