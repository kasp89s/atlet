<?php
/**
 * Карта сайта
 *
 */
class Sgsapi_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}


	public function index() {
	    $action = $this->input->get('do', false);

		if($action == "artinfo"){		}elseif($action == "actual"){		}else{        	$this->getxml();
        }
	}


	public function getxml() {
		$this->frame="clear";
		header('Content-type: text/xml');

		if(!$sgsCache = Cache::instance()->get('catalog_sgs')){

			$this->template = new View('sgs/productlist');

			$objElements = new Catalog_Model();
	        $arrElements=$objElements->db
				  ->select('self.*', Array('avail_code' => 'avail.code'))
				  ->from($objElements->table_name)
				  ->left_join(Array('ml_avail' => 'catalog_availability'), 'avail.id', 'self.availability')
				  ->where('active', 1)
				  ->where('in_sgs', "1")
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

	        $sgsCache = $this->template->render(false);

	        Cache::instance()->set('catalog_sgs', $sgsCache, 'catalog', 2678400); //месяц
	    }else{	     	echo($sgsCache);
	    }
	}

}
?>