<?php
/**
 * Группы в вирт. туре
 *
 * @author Antuan
 * @created 02.10.2009
 */

class Catalog_Controller extends T_Controller {

	public static $plane_tree;
	public $current_uri;
	public $current_group_id;

    protected $intPage;
	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}

	/**
	 * Разрешение относительного адреса
	 *
	 */

	public function router($uri) {
	    $is404error=false;
	    $isIndex=false;
	    $isSearch=false;
	    $isManufacturer = false;

	    $uri=trim($uri);
	    $uri=preg_replace("#^/#i","",$uri);

	    preg_match("#page([0-9]+)#i",$uri,$arrMatches);

	    if(count($arrMatches)>0){
	    	$pagerEnabled=true;
	    }

	    if(count($arrMatches)>0&&intval($arrMatches[1])>0){
        	$intPage=intval($arrMatches[1]);
        	$this->intPage=intval($arrMatches[1]);
        	$uri=preg_replace("#([/]?page[0-9]+[/]?)#i","",$uri);
        }else{
        	$intPage=false;
        }

        $arrUriSegments=explode("/",$uri);
        $this->current_uri=$uri;

        $sqlStmtUri="";

        foreach($arrUriSegments as $key => $value){
        	if(strlen(trim($value))>0){
	        	if(strlen($sqlStmtUri)>0){
	        		$sqlStmtUri .=",";
	        	}elseif(utf8::strtolower($value)=="search"){
	        		$isSearch=true;
	        	}
	        	elseif(utf8::strtolower($value)=="manufacturer"){
	        		$isManufacturer=true;
	        	}

	        	$sqlStmtUri .="'" . $value . "'";
        	}
        }

        $arrBreadCrumbs = Array();

	    if(strlen($sqlStmtUri)>0){
	        $groupsInfo2 = new CatalogGroups_Model();
			$groupsInfo2->info_content();
			$arrRootGroup = $groupsInfo2->db
					->select('self.*')
					->from($groupsInfo2->table_name)
					->where('catalog_group_contents.uri', '=', $arrUriSegments[0])
					->where('self.level', 1)
					->get()
					->row();

	        $groupsInfo = new CatalogGroups_Model();
			$groupsInfo->info_content();
			$groupsInfo->db
					->select('self.*')
					->from($groupsInfo->table_name)
					->order_by('self.lft')
					->where('lft', '>=', $arrRootGroup['lft'])
					->where('rgt', '<=', $arrRootGroup['rgt'])
					->open();
			$intCounter = 0;

			foreach($arrUriSegments as $k => $v){
				$intCounter++;
				$arrGroups = $groupsInfo->db
						->or_open()
						->where('catalog_group_contents.uri', '=', $v)
						->where('self.level', $intCounter)
						->close();
			}
			$arrGroups = $groupsInfo->db
					->close()
					->get()
					->rows();

			$curUri=$this->get_uri_base('catalog');

	        if(isset($arrGroups[0]['level']) && $arrGroups[0]['level']>1){
	        	$is404error=true;
	        }else{
				foreach($arrGroups as $key => $value){

					if($value['uri']!=$arrUriSegments[$key]){
						$is404error=true;
						break;
					}

					if($key > 0 && ($value['lft']<$arrGroups[$key-1]['lft'] || $value['rgt']>$arrGroups[$key-1]['rgt'])){
						$is404error=true;
						break;
					}

					$curBreadCrumbs = count($arrBreadCrumbs);
					$arrBreadCrumbs[$curBreadCrumbs]['title'] = $value['title'];
					$arrBreadCrumbs[$curBreadCrumbs]['uri'] = $curUri.'/'.$value['uri'];

					$curUri=$curUri.'/'.$value['uri'];
				}
			}
		}else{
			$isIndex=true;
		}

		if($isManufacturer){
        	$this->manufacturer($arrUriSegments[1], $intPage);
        	return;
        }

        if($isSearch){
        	$this->set_attribute('breadcrumbs', Array(
        		'title'  =>   'Поиск по сайту',
        		'uri'    =>   ($this->get_uri_base('catalog').'/search')
        	));
        	$this->search($intPage);
        	return;
        }

        if($isIndex){
        	$this->index();
        	return;
        }

        if($is404error){
        	Event::run('system.404');
        	return;
        }

        $arrComponentData=Array(
	         'group_id'     =>   (isset($arrGroups[0]['id'])?$arrGroups[0]['id']:-1),
	         'sub_group_id' =>   (isset($arrGroups[1]['id'])?$arrGroups[1]['id']:-1)
	    );
	    $this->replace_component('leftmenu', 'leftmenu', $arrComponentData);


		if(count($arrGroups)<count($arrUriSegments)){//path to element

			 $objElement = new Catalog_Model();

			 if($arrGroups[count($arrGroups)-1]['is_vip']){
			     $objElement->vip_cat_content($arrGroups[(count($arrGroups)-1)]['id']);

			     $arrElement=$objElement->db
					  ->select('self.*')
					  ->where('self.active', 1)
					  ->where('self.uri', $arrUriSegments[(count($arrUriSegments)-1)])
					  ->get()
					  ->row();

				if(!$arrElement){
					$arrElement=$objElement->db
					  ->select('self.*')
					  ->from($objElement->table_name)
					  ->where('active', 1)
					  //->where('group_id', $arrGroups[(count($arrGroups)-1)]['id'])
					  ->where('uri', $arrUriSegments[(count($arrUriSegments)-1)])
					  ->get()
					  ->row();
				}
			 }else{
                 $arrElement=$objElement->db
					  ->select('self.*')
					  ->from($objElement->table_name)
					  ->where('active', 1)
					  //->where('group_id', $arrGroups[(count($arrGroups)-1)]['id'])
					  ->where('uri', $arrUriSegments[(count($arrUriSegments)-1)])
					  ->get()
					  ->row();
					  //var_dump($arrElement);
					  //var_dump($arrUriSegments);
					  //var_dump($arrGroups);
			 }

             if($arrElement['id']<=0 )Event::run('system.404');

             foreach($arrBreadCrumbs as $key => $value){
				 $this->set_attribute('breadcrumbs', $value);
			 }

			 $this->current_group_id = $arrGroups[count($arrGroups)-1]['id'];
             $this->detail($arrElement['id']);
		}elseif(count($arrGroups)==count($arrUriSegments)){//path to group

			 foreach($arrBreadCrumbs as $key => $value){
				 $this->set_attribute('breadcrumbs', $value);
			 }

             $this->group($arrGroups[(count($arrGroups)-1)]['id'],$intPage);
		}else{
			Event::run('system.404');
		}
	}

	/**
	 * Дерево
	 *
	 */
	public function index() {
		url::redirect("/");

		$groupsInfo = new CatalogGroups_Model();

		$intGroupID=$groupsInfo->root(1);
		$intGroupID=$intGroupID['id'];

		$this->create_cache();
        $plane_tree = Cache::instance()->get('catalog_plane_tree');

        foreach($plane_tree as $key => $value){
        	if($value['active']!=1)unset($plane_tree[$key]);
        }

		$arrGroupInfo = $plane_tree[$intGroupID];

		$this->template = new View('catalog/index');

        if(!$arrGroupInfo)
            {
                 $this->template->notFound = 'yes';
            }
        else
            {
				 $this->add_attribute('title', $arrGroupInfo['seo_title']);
				 $this->add_attribute('keywords', $arrGroupInfo['seo_keywords']);
				 $this->add_attribute('description', $arrGroupInfo['seo_description']);


                 $this->template->groupInfo = $arrGroupInfo;

				 $groups = new CatalogGroups_Model();

                 $arrTreeGroups=$groups->get_grouped_array($plane_tree);

                 $this->template->groups = $arrTreeGroups[$intGroupID]["rows"];
                 $arrGroupsId = Array();

            	 foreach($arrTreeGroups[$intGroupID]["rows"] as $value)
                     {
                         $arrGroupsId[]=$value['id'];
                     }

                 $this->template->groupsFiles = DBFile::select_all('catalog_groups', $arrGroupsId);
		    }
	}


	/**
	 * Дерево
	 *
	 */
	public function group($intGroupID,$intPage) {

		$groupsInfo = new CatalogGroups_Model();
		$this->create_cache();
        $plane_tree = Cache::instance()->get('catalog_plane_tree');
        $catalogTree = Cache::instance()->get('catalog_tree');
		//var_dump($plane_tree);

        foreach($plane_tree as $key => $value){
        	if($value['active']!=1)unset($plane_tree[$key]);
        }

		$arrGroupInfo = $plane_tree[$intGroupID];

        if(!$arrGroupInfo)
            {
                 Event::run('system.404');
            }
        else
            {
                 $this->template = new View('catalog/group');

				 $table = new CatalogGroupContents_Model();
                 $table->update(Array('total_shows' => db::expr('`total_shows` + 1')),Array('group_id' => $intGroupID))->get();

                 $arrData=array(
		        	'text'=>$arrGroupInfo['title']
		         );

		         $arrData=array(
	        		'text'    =>  $arrGroupInfo['title']." ".($arrGroupInfo['concat_with_section_title']>0?$arrGroupInfo['seo_name']:""),
	        		'use_h1'  =>  $arrGroupInfo['use_h1']
	         	 );

		         $this->add_component('sectiontitle', 'sectiontitle',$arrData);
		         $arrData=Array(
		        	 'order_val'=>$this->input->get('order', ''),
		        	 'current_url'=>$this->get_uri_base('catalog').$arrGroupInfo['uri_base']
		         );

		         $this->add_component('sortfilter', 'sortfilter',$arrData);
		         //$this->add_component('seorelative', 'seorelative',false);
		         $this->add_component('socialicons', 'socialicons', Array('text' => $arrGroupInfo['title']));

		         $this->add_attribute('title', $arrGroupInfo['seo_title']);
				 $this->add_attribute('keywords', $arrGroupInfo['seo_keywords']);
				 $this->add_attribute('description', $arrGroupInfo['seo_description']);

				 /**
				 * Биндим заголовки
				 */

				 $groups = new CatalogGroups_Model();

                 $arrTreeGroups=$groups->get_grouped_array($plane_tree);

				 $objElements = new Catalog_Model();
				 $objElements->db
				 		->where('self.price', '>', db::expr('0'));

                 if($arrGroupInfo['is_vip']){
                 	 $objElements->vip_cat_content($intGroupID);
                 	 $objElements->db
                          ->select(db::expr('ml_catalog_manufacturer.name as `manufacturer_name`'), db::expr('IF(ml_self.availability + ml_self.availability2 > 0, 1, 0) as `in_action`'))
                 	 	  ->left_join($objElements->_catgroups_contents,$objElements->_catgroups_contents.'.id','self.group_id')
                          ->left_join('catalog_manufacturer', 'self.manufacturer_id','catalog_manufacturer.id')
						  ->where('vipcontent.active', 1)
						  ->where($objElements->_catgroups_contents.'.active', 1);
                 } else {
		             $objElements->db
						  ->select(array(
                                 'self.*',
                                 'price' => db::expr('IF(`ml_self`.`availability2` > 0, `ml_self`.`price`, `ml_self`.`priceSupplier`)'),
                                 db::expr('ml_catalog_manufacturer.name as `manufacturer_name`'),
                                 db::expr('IF(ml_self.availability + ml_self.availability2 > 0, 1, 0) as `in_action`'
                             )))
						  ->from($objElements->table_name)
                           ->left_join('catalog_manufacturer', 'self.manufacturer_id','catalog_manufacturer.id')
						  ->where('self.active', 1)
						  ->where('self.group_id', $intGroupID);
				 }

				 $objElements->db
				 		->where('self.active', 1);

                // Все элементы группы
                $categoryElements = new Catalog_Model();
                $categoryElements = $categoryElements->db
                ->select('self.manufacturer_id')
                ->from($categoryElements->table_name)
                ->where('self.price', '>', db::expr('0'))
                ->where('self.active', 1)
                ->where('self.group_id', $intGroupID)
                ->get()
                ->rows();
                $objElements->db->order_by('in_action', 'desc');
                $sort = array();
                if (isset($_GET['price']) && ($_GET['price'] == 'asc' || $_GET['price'] == 'desc')) {
                    $objElements->db->order_by('self.price', $_GET['price']);
                    $sort['price'] = $_GET['price'];
                }

                if (isset($_GET['manufacturer']) && ((int) $_GET['manufacturer'] > 0)) {
                    $objElements->db->where('self.manufacturer_id', '=', (int) $_GET['manufacturer']);
                    $sort['manufacturer'] = (int) $_GET['manufacturer'];
                }

                if (empty($_GET['manufacturer']) && empty($_GET['price'])){
					 	  $objElements->db
					 	  		->order_by(Array('self.sort_order'=>'asc','self.price'=>'asc','self.name'=>'asc'));
                }

		         $tm = new Tablemaker($objElements);
				 if($intPage>0)$tm->current_page=$intPage;
				 $tm->default_page_size = 30;
				 $tm->pager_window = 2;

				 $tm->find_files = true;
				 $tm->table_of_files = 'catalog';

				 $arrTmData=$tm->show();

 				 if(count($arrTmData['rows']) <= 0){
                 	 $arrTreeGroups=$groups->get_grouped_array($plane_tree);

					 $objElements = new Catalog_Model();
					 $objElements->db
					 		->where('self.price', '>', db::expr('0'), db::expr('ml_catalog_manufacturer.name as `manufacturer_name`'))
						  	->select('self.*' , db::expr('IF(ml_self.availability + ml_self.availability2 > 0, 1, 0) as `in_action`'))
						  	->left_join(Array('ml_product_section'=>'catalog_groups'), 'product_section.id', 'self.group_id')
                            ->left_join('catalog_manufacturer', 'self.manufacturer_id','catalog_manufacturer.id')
						  	->from($objElements->table_name)
						  	->where('self.active', 1)
						  	->where('product_section.lft', '>=', db::expr($arrGroupInfo['lft']))
						  	->where('product_section.rgt', '<=', db::expr($arrGroupInfo['rgt']))
//						  	->order_by('total_shows', 'desc')
                     ;

		             $strSortOrder=$this->input->get('order', '');
                     $objElements->db->order_by('in_action', 'desc');
                     $sort = array();
                     if (isset($_GET['price']) && ($_GET['price'] == 'asc' || $_GET['price'] == 'desc')) {
                         $objElements->db->order_by('self.price', $_GET['price']);
                         $sort['price'] =  $_GET['price'];
                     }

                     if (isset($_GET['manufacturer']) && ((int) $_GET['manufacturer'] > 0)) {
                         $objElements->db->where('self.manufacturer_id', '=', (int) $_GET['manufacturer']);
                         $sort['manufacturer'] = (int) $_GET['manufacturer'];
                     }

                     if (empty($_GET['manufacturer']) && empty($_GET['price'])){
//					 	  $objElements->db
//					 	  		->order_by(Array('self.sort_order'=>'asc','self.price'=>'asc','self.name'=>'asc'));
                     }
//                     $categoryElements = $objElements->db->get()->rows();
			         $tm = new Tablemaker($objElements);
					 if($intPage>0)$tm->current_page=$intPage;
					 $tm->default_page_size = 30;
					 $tm->pager_window = 2;

					 $tm->find_files = true;
					 $tm->table_of_files = 'catalog';

					 $arrTmData=$tm->show();
                 }

                 $arrData=Array(
		         	'current_uri'  => $this->current_uri,
		         	'total_pages'  => $tm->pager_info['total_pages'],
		         	'current_page' => $tm->pager_info['page'] ,
		         	'pages_array'  => $tm->pager_info['pages']
		         );

				 $this->add_component('pager', 'pager', $arrData);
                 $arrTreeGroups[$intGroupID]["rows"]=(isset($arrTreeGroups[$intGroupID]["rows"])?$arrTreeGroups[$intGroupID]["rows"]:Array());
                 $arrGroupsId = Array();

            	 foreach($arrTreeGroups[$intGroupID]["rows"] as $value)
                     {
                         $arrGroupsId[]=$value['id'];
                     }

                 $this->template->elements=$arrTmData['rows'];

                 $manufacturerIds = array();
                 foreach ($categoryElements as $product) {
                     $manufacturerIds[] = $product['manufacturer_id'];
                 }
                $table_items = new Manufacturers_Model;
                $manufacturers = $table_items->db
                    ->select(Array('self.*'))
                    ->from($table_items->table_name)
                    ->where('self.id', 'IN', $manufacturerIds)
                    ->order_by('self.name', 'asc')
                    ->get()
                    ->rows();

                 $this->template->groups = $arrTreeGroups[$intGroupID]["rows"];
                 $this->template->groupInfo = $arrGroupInfo;
                 $this->template->plane_tree = $plane_tree;
                 $this->template->manufacturers = $manufacturers;
                 $this->template->sort = $sort;
                 $this->template->page = $intPage;
                 if($intPage<=1)
                 	$this->template->additionalText = $arrGroupInfo['full_descr'];
                 $this->template->groupsFiles = DBFile::select_all('catalog_groups', $arrGroupsId);
                 $this->template->catalog_uri_base = $this->get_uri_base('catalog');
		    }
	}


	/**
	 * Дерево
	 *
	 */
	public function search($intPage) {
		 $this->template = new View('catalog/search');
		 $table = new Catalog_Model();

         $table->db
			  ->select(array(
                     'self.*',
                     'price' => db::expr('IF(`ml_self`.`availability2` > 0, `ml_self`.`price`, `ml_self`.`priceSupplier`)'),
                     db::expr('ml_catalog_manufacturer.name as `manufacturer_name`')
                 ))
			  ->from($table->table_name)
              ->left_join('catalog_manufacturer', 'self.manufacturer_id','catalog_manufacturer.id')
			  ->where('self.active', 1)
			  ->where('self.price', '>', db::expr('0'));

	     $arrGet=$this->input->get();
	     $blnRequestEmpty=true;
		 if(isset($arrGet['cost_from'])&&intval($arrGet['cost_from'])>0){
		 	 $table->db
			  	->where('self.price','>=', $arrGet['cost_from'])
			  	->order_by('self.price','desc');

	         $blnRequestEmpty=false;
		 }

		 if(isset($arrGet['cost_to'])&&intval($arrGet['cost_to'])>0){
		 	 $table->db
			  	->where('self.price', '<=', $arrGet['cost_to'])
			  	->order_by('self.price','desc');

	         $blnRequestEmpty=false;
		 }

		 if(isset($arrGet['search_words'])&&UTF8::strlen($arrGet['search_words'])>0){
             if (stripos($arrGet['search_words'], '475') == 0) {
                 $arrGet['search_words'] = str_replace('475', '', $arrGet['search_words']);
             }

		 	 $arrWhereClause=Array(
		 	 	 'self.name' => $arrGet['search_words'],
		 	 	 'self.code' => $arrGet['search_words'],
		 	 	 'self.description' => $arrGet['search_words'],
		 	 );
		 	 $table->db

			  	->where("self.name", 'like', '%'.$arrGet['search_words'].'%')
			  	->or_where("self.articles", 'like', $arrGet['search_words'])
			  	->or_where("self.articles", 'like', '%,'.$arrGet['search_words'])
			  	->or_where("self.articles", 'like', '%,'.$arrGet['search_words'].',%')
			  	->or_where("self.articles", 'like', $arrGet['search_words'].',%')
			  	->or_where("self.description", 'like', '%'.$arrGet['search_words'].'%')
			  	->or_where("catalog_manufacturer.name", 'like', '%'.$arrGet['search_words'].'%')

//			  	->order_by('self.name','desc')
             ;

			 $blnRequestEmpty=false;
		 }

        $sort = array();
        if (isset($_GET['price']) && ($_GET['price'] == 'asc' || $_GET['price'] == 'desc')) {
            $table->db->order_by('self.price', $_GET['price']);
            $sort['price'] =  $_GET['price'];
        }

        if (isset($_GET['manufacturer']) && ((int) $_GET['manufacturer'] > 0)) {
            $table->db->where('self.manufacturer_id', '=', (int) $_GET['manufacturer']);
            $sort['manufacturer'] = (int) $_GET['manufacturer'];
        }

        if (empty($_GET['manufacturer']) && empty($_GET['price'])){
            $table->db
			->order_by(Array('self.sort_order'=>'asc','self.price'=>'asc','self.name'=>'asc'));
        }
         $table->db->order_by('self.availability2', 'desc');
         $table->db->order_by('self.availability', 'desc');

         $tm = new Tablemaker($table);
		 if($intPage>0)$tm->current_page=$intPage;
		 $tm->default_page_size = 15;
		 $tm->pager_window = 2;

		 $tm->find_files = true;
		 $tm->table_of_files = 'catalog';

		 $arrTmData=$tm->show();

         if($blnRequestEmpty){
         	 $arrTmData['error_text']="Поисковый запрос пуст<br /><br />";
         	 $this->template->data=$arrTmData;
         }elseif(count($arrTmData['rows'])<=0){
         	 $arrTmData['error_text']="Не удалось найти товары, удовлетворяющие поисковому запросу.<br /><br />";
         	 $this->template->data=$arrTmData;
         }else{
             $arrData=Array(
	         	'current_uri'  => $this->current_uri,
	         	'total_pages'  => $tm->pager_info['total_pages'],
	         	'current_page' => $tm->pager_info['page'] ,
	         	'pages_array'  => $tm->pager_info['pages']
	         );

			 $this->add_component('pager', 'pager', $arrData);
			 //$this->add_component('seorelative', 'seorelative',false);


             $manufacturerIds = array();
             foreach ($arrTmData['rows'] as $product) {
                 $manufacturerIds[] = $product['manufacturer_id'];
             }
             $table_items = new Manufacturers_Model;
             $manufacturers = $table_items->db
                 ->select(Array('self.*'))
                 ->from($table_items->table_name)
                 ->where('self.id', 'IN', $manufacturerIds)
                 ->order_by('self.name', 'asc')
                 ->get()
                 ->rows();

		     $this->create_cache();

	         $plane_tree = Cache::instance()->get('catalog_plane_tree');
		     //var_dump($plane_tree);
		     //var_dump($arrTmData);
             $this->template->manufacturers = $manufacturers;
             $this->template->sort = $sort;
             $this->template->search_words = $arrGet['search_words'];

	         $this->template->catalog_uri_base = $this->get_uri_base('catalog');
	         $this->template->groups = $plane_tree;

         	 $this->template->data=$arrTmData;
         }

         $arrData=array(
        	'text'=>"Поиск товаров"
         );

         $this->add_component('sectiontitle', 'sectiontitle',$arrData);



	}


	public function manufacturer($intManId, $intPage) {

		 $this->template = new View('catalog/manufacturer');
		 $table = new Catalog_Model();

		 $arrManufacturerInfo = $table->db
		 		->select('*')
			 	->from('catalog_manufacturer')
			 	->where('id', $intManId)
			 	->get()
			 	->row();

         if(!$arrManufacturerInfo){
         	 Event::run('system.404');
         }else{
         	 $table->db
				  ->select(array(
                         'self.*',
                         'price' => db::expr('IF(`ml_self`.`availability2` > 0, `ml_self`.`price`, `ml_self`.`priceSupplier`)'),
                         db::expr('ml_catalog_manufacturer.name as `manufacturer_name`')
                     ))
				  ->from($table->table_name)
                  ->left_join('catalog_manufacturer', 'self.manufacturer_id','catalog_manufacturer.id')
				  ->where('self.active', 1)
				  ->where('self.price', '>', db::expr('0'))
				  ->where('manufacturer_id', $intManId)
                  ->order_by('self.availability', 'desc')
                  ->order_by('self.availability2', 'desc')
				  ;

	         $tm = new Tablemaker($table);
			 if($intPage>0)$tm->current_page=$intPage;
			 $tm->default_page_size = 30;
			 $tm->pager_window = 2;

			 $tm->find_files = true;
			 $tm->table_of_files = 'catalog';

			 $arrTmData=$tm->show();

             $arrData=Array(
	         	'current_uri'  => $this->current_uri,
	         	'total_pages'  => $tm->pager_info['total_pages'],
	         	'current_page' => $tm->pager_info['page'] ,
	         	'pages_array'  => $tm->pager_info['pages']
	         );

			 $this->add_component('pager', 'pager', $arrData);
			 //$this->add_component('seorelative', 'seorelative',false);

		     $this->create_cache();

	         $plane_tree = Cache::instance()->get('catalog_plane_tree');
	         $this->template->catalog_uri_base = $this->get_uri_base('catalog');
	         $this->template->groups = $plane_tree;
	         $this->template->description = $arrManufacturerInfo['description'];

         	 $this->template->data=$arrTmData;
         }

         $arrData=array(
        	'text'=>"Производители: ".$arrManufacturerInfo['name']
         );

         $this->add_component('sectiontitle', 'sectiontitle',$arrData);

         $this->set_attribute('breadcrumbs', Array(
        		'title'  =>   'Производители: '.$arrManufacturerInfo['name'],
        		'uri'    =>   ($this->get_uri_base('catalog').'/manufacturer/'.$intManId.'/')
         ));

	}

    /**
     * Публикация сообщения.
     *
     * @param $name
     * @param $text
     * @param $productId
     */
    private function publicMessage($name, $text, $productId)
    {
        $name = htmlspecialchars($name);
        $text = htmlspecialchars($text);
        $date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO `ml_reviews`
               (`id`, `productId`, `name`, `text`, `date`)
               VALUES (NULL, '{$productId}', '{$name}', '{$text}', '{$date}')";
        mysql_query($sql) or die(mysql_error());
    }

    /**
     * Список отзывов к продукту.
     *
     * @param $id
     * @return array
     */
    private function getMessageList($id)
    {
        $sql = "SELECT * FROM `ml_reviews` WHERE `productId` = {$id} ORDER BY `date` DESC";
        $query = mysql_query($sql);
        $returnVars = array();
        while ($row = mysql_fetch_array($query)) {
                $returnVars[$row['id']] = $row;
        }
        return $returnVars;
    }

	public function detail($id, $intPage) {
		$this->template = new View('catalog/detail');

		$this->set_attribute('js', '/js/jquery/jquery.js');
		$this->set_attribute('js', '/js/jquery/jquery-ui.min.js');
        $this->set_attribute('js', '/js/jquery/jquery.colorbox.js');
        $this->set_attribute('css', '/css/colorbox.css');

        $this->del_component('banners', 'banners');

		$id = (int)$id;


        if (!empty($_POST['name']) && !empty($_POST['text'])){
            if (!empty($_POST['captcha']) && isset($_COOKIE['captcha'])) {
                if (empty($_COOKIE['captcha']) || trim(strtolower($_POST['captcha'])) != $_COOKIE['captcha']) {
                    $errors['captcha']= 'Не верно введена капча!';
                } else {
                    $this->publicMessage($_POST['name'], $_POST['text'], $_POST['productId']);
                }
            }
        }

		if($id) {
			$table = new Catalog_Model;
			$row = $table->db
				->select(Array('self.*', 'manufacturer_name' => 'man.name', 'in_cart' => db::expr('case when `ml_cart`.`id` is not null then 1 else 0 end')))
				->left_join(Array('ml_cart' => 'catalog_cart'), Array('cart.product_id'=>'self.id', 'cart.session_id' => db::expr("'".$this->get_cart_id()."'")))
				->left_join(Array('ml_man' => 'catalog_manufacturer'), Array('man.id' => 'self.manufacturer_id'))
				->where(array('self.id' => $id, 'self.active' => 1))
				->from($table->table_name)
				->get()
				->row();

	        if(!$row)Event::run('system.404');

	        if(strlen($row['tastes'])>0){
	        	$row['arrTastes'] = explode(',', $row['tastes']);
	        }

            // Подключаем вкусы...
            $tastes = new CatalogTastes_Model;
            $tastes = $tastes->db
                ->select(Array('self.*'))
                ->where(array('self.productId' => $id))
                ->from($tastes->table_name)
                ->order_by('self.count2', 'desc')
                ->get()
                ->rows();
            foreach ($tastes as $key => $taste) {
                $tastes[$key]['trans'] = $this->transliterate($taste['name']);
            }
//            var_dump($tastes); exit;
            $otherFilling = new Catalog_Model();
            $otherFilling->db
                ->select(Array('self.*'))
                ->from($otherFilling->table_name)
                ->where(array('self.name' => $row['name']))
                ->where('self.volume', '!=', $row['volume'])
                ->order_by('self.availability', 'desc')
//                ->order_by('self.volume', 'asc')
            ;
            $tm = new Tablemaker($otherFilling);
            $tm->current_page = 1;
            $tm->default_page_size = 30;
            $tm->find_files = true;
            $tm->table_of_files = 'catalog';

            $otherFilling = $tm->show();
            // ===================
	        $table->update(Array('total_shows' => db::expr('`total_shows` + 1')),Array('id' => $id))->get();

            $row['reviews'] = $this->getMessageList($id);
            $row['reviewsCount'] = count($row['reviews']);
			$this->template->data = $row;
			$this->template->files = DBFile::select('catalog', $id);

            $this->create_cache();
            $plane_tree = Cache::instance()->get('catalog_plane_tree');

			$arrGroupInfo = $plane_tree[$this->current_group_id];

			$this->set_attribute('breadcrumbs', Array(
				'title' => $row['name'],
				'uri'   => $this->get_uri_base('catalog')."/".$arrGroupInfo['uri_base']."/".$row['uri']
			));

			$this->template->groupInfo=$arrGroupInfo;

			$arrData=array(
        		'text'    =>  $row['name']." ".($row['concat_with_section_title']>0?$row['seo_name']:""),
        		'use_h1'  =>  $row['use_h1']
         	);

         	$this->add_component('sectiontitle', 'sectiontitle',$arrData);
//         	$this->add_component('seorelative', 'seorelative', $row);

         	$arrUri = parse_url($this->input->server("REQUEST_URI"));
         	$this->template->seorelative = $this->seorelative($row, $intPage);
         	$this->template->page = $this->intPage;
         	$this->template->tastes = $tastes;
         	$this->template->delivery = Settings_Model::$_delivery_date;
         	$this->template->hour = date('H', time());
         	$this->template->otherFilling = $otherFilling['rows'];
         	$this->template->plane_tree = $plane_tree;
         	$this->template->current_link = "http://".$this->input->server("HTTP_HOST").$arrUri['path'];
         	$this->template->current_text = urlencode($arrGroupInfo['title']." ".$row['name']);
         	$this->template->action_uribase = $this->get_uri_base('action');


			/**
			 * Биндим заголовки
			 */

			//if($row['is_use_auto_tags'] && utf8::strlen($arrGroupInfo['seo_auto_title'])>3){
				//$this->add_attribute('title', str_replace("{name}", $row['name'], $arrGroupInfo['seo_auto_title']));
			//}else{
				$this->add_attribute('title', "".$row['name']." купить по выгодной цене. Отзывы о ".$row['name']."");
			//}

			if($row['is_use_auto_tags'] && utf8::strlen($arrGroupInfo['seo_auto_keywords'])>3){
				$this->add_attribute('keywords', str_replace("{name}", $row['name'], $arrGroupInfo['seo_auto_keywords']));
			}else{
				$this->add_attribute('keywords', $row['seo_keywords']);
			}

			if($row['is_use_auto_tags'] && utf8::strlen($arrGroupInfo['seo_auto_description'])>3){
				$this->add_attribute('description', str_replace("{name}", $row['name'], $arrGroupInfo['seo_auto_description']));
			}else{
				$this->add_attribute('description', $row['seo_description']);
			}



		}else{
			Event::run('system.404');
		}
	}

    private function seorelative($data, $intPage)
    {
        $mdlProducts = new Catalog_Model();
        $mdlGroups = new CatalogGroupContents_Model();

        $this->create_cache();
        $plane_tree = Cache::instance()->get('catalog_plane_tree');

        $mdlProducts->db
            ->select(Array('self.*',
                    'price' => db::expr('IF(`ml_self`.`availability2` > 0, `ml_self`.`price`, `ml_self`.`priceSupplier`)'),
                    db::expr('(`'.$mdlProducts->table_prefix.'self`.`relative_shows`/`'.$mdlProducts->table_prefix.'self`.`relative_weight`) as `views_coef`'), db::expr('RAND() as `rnd`'), db::expr('ml_catalog_manufacturer.name as `manufacturer_name`')))
            ->from($mdlProducts->table_name)
            ->left_join(Array("ml_groups" => $mdlProducts->_catgroups_contents), 'self.group_id','groups.group_id')
            ->left_join('catalog_manufacturer', 'self.manufacturer_id','catalog_manufacturer.id')

            ->where('groups.active', '>', db::expr('0'))
            ->where('self.active', '>', db::expr('0'))
            ->where('self.price', '>', db::expr('0'))
            ->where('self.id', '!=' ,$data['id'])

            ->where('self.name', '!=' ,$data['name'])
//            ->where('self.manufacturer_id', '>=' ,$data['manufacturer_id'])
            ->where('self.group_id', '=' ,$data['group_id'])

            ->order_by('self.availability', 'desc')
            ->order_by('manufacturer_id', 'asc');
//            ->order_by('views_coef', 'asc')
//            ->order_by('rnd', 'asc')


        $tm = new Tablemaker($mdlProducts);
        if (empty($this->intPage) === false) $tm->current_page = $this->intPage;
        $tm->default_page_size = 18;
        $tm->find_files = true;
        $tm->table_of_files = 'catalog';
        $arrProducts = &$tm->show();

        $arrData=Array(
            'current_uri'  => $this->current_uri,
            'total_pages'  => $tm->pager_info['total_pages'],
            'current_page' => $tm->pager_info['page'] ,
            'pages_array'  => $tm->pager_info['pages']
        );

        $this->add_component('pager', 'pager', $arrData);

        $arrProducts = $arrProducts['rows'];

        $tmpArray = array();
        foreach ($arrProducts as $key => $value){
            if ($value['name'] == $data['name']) {
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

        return array(
            'products' => $arrProducts,
            'groups' => $arrGroups,
            'catalog_uri_base' => $this->get_uri_base('catalog'),
        );
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
	public static function order_tree($items = false, $uri_parent = '', $parent_id=0){
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
					$tree[$root_node]['rows'] = self::order_tree($childrens, $tree[$root_node]['uri_base'], $tree[$root_node]['id']);
					$childrens = array();
				}

				if($_level == 0)
					$root_node = 'root';
				else
					$root_node = $page['uri'];

				$tree[$root_node] = $page;

				$tree[$root_node]['uri_base'] = ($root_node != 'root') ? $uri_parent . '/' . $root_node : '';

				self::$plane_tree[$tree[$root_node]['id']] = $tree[$root_node];
				self::$plane_tree[$tree[$root_node]['id']]['parent_id'] = $parent_id;

			} elseif($page['level'] > $_level) {
				$childrens[] = $page;

			} else {
				return $tree;
			}
			$i++;
		}

		if(count($childrens))
			$tree[$root_node]['rows'] = self::order_tree($childrens, $tree[$root_node]['uri_base'], $tree[$root_node]['id']);

		return $tree;
	}

    private function get_cart_id(){
		$strCartID = $this->input->cookie('cart_id', md5(microtime(true)+rand(1,10000)));
		cookie::set('cart_id', $strCartID, Kohana::config('coocie.expire'));

		return $strCartID;
	}

    private function transliterate($string) {
        $roman = array("Sch","sch",'Yo','Zh','Kh','Ts','Ch','Sh','Yu','ya','yo','zh','kh','ts','ch','sh','yu','ya','A','B','V','G','D','E','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','','Y','','E','a','b','v','g','d','e','z','i','y','k','l','m','n','o','p','r','s','t','u','f','','y','','e', '_');
        $cyrillic = array("Щ","щ",'Ё','Ж','Х','Ц','Ч','Ш','Ю','я','ё','ж','х','ц','ч','ш','ю','я','А','Б','В','Г','Д','Е','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Ь','Ы','Ъ','Э','а','б','в','г','д','е','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','ь','ы','ъ','э', ' ');
        return str_replace($cyrillic, $roman, $string);
}
}
?>
