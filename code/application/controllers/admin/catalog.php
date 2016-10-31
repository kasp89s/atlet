<?php
/**
 * Каталог товаров
 *
 */
class Catalog_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Каталог товаров';

		if(Acl::instance()->is_allowed('catalog_add')){
			$this->menu = array(
				array('url'=>'/admin/cataloggroups', 'section'=>'Все группы'),
				array('url'=>'/admin/cataloggroups/edit', 'section'=>'Добавить группу', 'title'=>'Группа'),
				array('url'=>'/admin/catalog/sgs_list', 'section'=>'SGS список товаров', 'title'=>'SGS список товаров'),
				array('url'=>'/admin/catalog', 'section'=>false, 'title'=>'Товары'),
				array('url'=>'/admin/catalog/edit', 'section'=>false, 'title'=>'Товар')
			);
		} else {
			$this->menu = array(
				array('url'=>'/admin/cataloggroups', 'section'=>'Все группы'),
				array('url'=>'/admin/catalog/sgs_list', 'section'=>'SGS список товаров', 'title'=>'SGS список товаров'),
				array('url'=>'/admin/catalog', 'section'=>false, 'title'=>'Товары'),
				array('url'=>'/admin/catalog/edit', 'section'=>false, 'title'=>'Товар')
			);
		}

		parent::__construct();
	}


	/**
	 * Список товаров
	 *
	 */
	public function index() {
		if(!Acl::instance()->is_allowed('catalog_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/catalog/index');
		$group_id = !empty($_REQUEST['group_id']) ? (int)$_REQUEST['group_id'] : false;

		if(!$group_id) message::error('Некорректный идентификатор (id) группы товара', '/admin/cataloggroups');

		$table = new Catalog_Model;
		$table->db
			->select(Array('self.id', 'self.name', 'self.code', 'self.active', 'self.group_id'))
			->where('self.group_id', $group_id)
			->from($table->table_name);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->find_files = true;
		$tm->table_of_files = 'catalog';
		$tm->orderby = array('self.id' => 'desc', 'self.name', 'self.code');
		$data = &$tm->show();

		$this->template->main = $data;
		$this->template->group_id = $group_id;
	}

	/**
	 * Список товаров
	 *
	 */
	public function sgs_list() {
		if(!Acl::instance()->is_allowed('catalog_show'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$this->template = new View('admin/catalog/index');

		$table = new Catalog_Model;
		$table->db
			->select(Array('self.id', 'self.name', 'self.code', 'self.active', 'self.group_id'))
			->where('self.in_sgs', '>', db::expr('0'))
			->from($table->table_name);

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->find_files = true;
		$tm->table_of_files = 'catalog';
		$tm->orderby = array('self.id' => 'desc', 'self.name', 'self.code');
		$data = &$tm->show();

		$this->template->main = $data;
	}


	/**
	 * Добавление/Редактирование товара
	 *
	 */
	public function edit() {
		$this->template = new View('admin/catalog/edit');

		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$group_id = !empty($_REQUEST['group_id']) ? (int)$_REQUEST['group_id'] : false;
		$post = $this->input->post();
		if(isset($post['uri'])){
			$post['uri']=url::title($post['uri'],"_");
		}


		/**
		 * Проверка прав доступа
		 */
		if($id){
			if(!Acl::instance()->is_allowed('catalog_edit'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		} else {
			if(!Acl::instance()->is_allowed('catalog_add'))
				message::error('Нет прав доступа к данному разделу', '/admin');
		}


		if(!$group_id) message::error('Некорректный идентификатор (id) группы товара', '/admin/cataloggroups');


		/**
		 * Правила валидации
		 */

		$table = new CatalogAvailability_Model();

		$table_cats = new CatalogAvailability_Model();
		$cats = $table_cats->db
				->select(Array('id', 'name'))
				->from($table_cats->table_name)
				->order_by('name')
				->get()
				->rows();

		$form = new ValidateForm();
		$form->add_field('group_id', 'int', array('required'));
		$form->add_field('name', 'string', array('required', 'length[1,100]'));
		$form->add_field('code', 'string', array('required', 'length[1,100]'));
		$form->add_field('price', 'int', array('required'));
		$form->add_field('oldprice', 'int', array());
//		$form->add_field('availability', array(new mod_list($cats)), array());
		$form->add_field('uri', 'string', array('required', 'length[3,200]'));
		$form->add_field('description', 'html');
		$form->add_field('image', 'no_fill', array('upload::valid', 'upload::type[gif,jpg,jpeg,png]', 'upload::size[1M]'));
		$form->add_field('image_left', 'no_fill', array('upload::valid', 'upload::type[gif,jpg,jpeg,png]', 'upload::size[1M]'));
		$form->add_field('sort_order', 'int');
		$form->add_field('is_show_in_left_block', 'checkbox');
		$form->add_field('is_show_on_main_page', 'checkbox');
		$form->add_field('active', 'checkbox');
		$form->add_field('use_h1', 'checkbox');
		$form->add_field('concat_with_section_title', 'checkbox');

		$form->add_field('is_show_in_relative', 'checkbox');
		$form->add_field('in_yml', 'checkbox');
		$form->add_field('in_sgs', 'checkbox');
		$form->add_field('in_action', 'checkbox');
		$form->add_field('relative_weight', 'int');
        $form->add_field('is_use_auto_tags', 'checkbox');

		$form->add_field('seo_name', 'string', array('length[1,200]'));
		$form->add_field('seo_title', 'string', array('required', 'length[1,100]'));
		$form->add_field('seo_keywords', 'string', array('required', 'length[1,200]'));
		$form->add_field('seo_description', 'string', array('required', 'length[1,200]'));


		/**
		 * Вспомогательные данные
		 */
		$table = new Catalog_Model();
		$arrTmpUnical = Array();
		if($id){
			$data = $table->db
				->select('*')
				->where('id', $id)
				->from($table->table_name)
				->get()
				->row();

			if(!$data) message::error('Некорректный идентификатор (id) товара', '/admin/catalog');
		} else {
			$data = array();
			$arrTmpUnical = $table->db
				->select('*')
				->where('uri', $post['uri'])
				->from($table->table_name)
				->get()
				->rows();
		}


		/**
		 * Сохранение
		 */
		if(!empty($post)){
			$data = $form->get_data($post) + $data;
			$form->validate();

			if(count($arrTmpUnical)>0){
				$form->set_field_err_ext("uri", 'unical');
			}

			if($form->is_ok()){
				$fill = $form->get_fill();
				$fill['description'] = str_replace('../../', '/', $fill['description']);

				if($id) {
					if(!$result = $table->update($fill, array('id'=>$id))) {
						$form->add_error('update', 1);
					} else {
						DBFile::save('catalog', $id, 'image');
						DBFile::save('catalog', $id, 'imageleft');
						unset($_FILES['image']);unset($_FILES['imageleft']);
						foreach ($_FILES as $k => $v){
							$mime = Kohana::config('mimes.'.DBFile::getFileExtention($v['name']));
							$strFileType=@explode("/",$mime[0]);
                       		$strFileType=$strFileType[0];
                       		if($strFileType=="image")
								DBFile::save('catalog', $id, $k, TRUE);
						}

						log::add('catalog', 'Редактирование товара id='.$id);
					}

				} else {
					$fill['date_create'] = db::expr('now()');

					if(!$result = $table->insert($fill)) {
						$form->add_error('insert', 1);
					} else {
						$id = $result->insert_id();
						DBFile::save('catalog', $id, 'image');
						DBFile::save('catalog', $id, 'imageleft');
						unset($_FILES['image']);unset($_FILES['imageleft']);
						foreach ($_FILES as $k => $v){
							$mime = Kohana::config('mimes.'.DBFile::getFileExtention($v['name']));
							$strFileType=@explode("/",$mime[0]);
                       		$strFileType=$strFileType[0];
                       		if($strFileType=="image")
								DBFile::save('catalog', $id, $k, TRUE);
						}

						log::add('catalog', 'Добавление товара id='.$id);
					}
				}
			}

			if($form->is_ok()){
				$arrData=Array();
				$arrData['keys']=Array('id','cat_id','product_id');
				$arrData['rows']=Array();

			    foreach($this->input->post('in_vip_cat_'.$id, Array()) as $key => $value){
			    	 $arrData['rows'][]=Array('',$key,$id);
			    }

			    $mdlCatalog=new Catalog_Model();

                $mdlCatalog->db
           			    ->delete($mdlCatalog->_vipcats_content,Array('product_id' => $id))
           			    ->get();

                if(count($arrData['rows'])>0){
			    	$mdlCatalog->db
			    		->insert($mdlCatalog->_vipcats_content)
			    		->columns('cat_id','product_id');

					foreach($arrData['rows'] as $key => $value){
						$mdlCatalog->db->values($value[1], $value[2]);
					}

			    	$mdlCatalog->db->get();
			    }

			    Cache::instance()->delete_tag('catalog');

				message::info('Товар успешно сохранен', '/admin/catalog/?group_id='.$group_id);
			} else {
				message::error('Некоторые обязательные поля не заполнены или заполнены неверно');
			}
		}


		$data = $form->get_form($data) + $data + $form->get_errors();
		$data['description_editor'] = Editor::factory("admin")->set_fieldname("description")->set_value($data['description'])->set_height(400)->set_width(650)->render(FALSE, TRUE);
		$data['media'] = DBFile::select('catalog', $id);

		$mdlInVipCats=new Catalog_Model();
		$mdlInVipCats->product_in_vip_cat($id);
		$data['in_vip_cats']=$mdlInVipCats->db->get()->rows();

		$this->template->data = $data;
		$this->template->group_id = $group_id;
	}


	/**
	 * Групповое сохранение товаров
	 *
	 */
	public function group() {
		$post = $this->input->post();
		$ids = array();
		$act = array();
		$group_id = !empty($_REQUEST['group_id']) ? (int)$_REQUEST['group_id'] : false;

		if(!$group_id) message::error('Некорректный идентификатор (id) группы товаров', '/admin/cataloggroups');

		if(!Acl::instance()->is_allowed('catalog_edit'))
			message::error('Нет прав доступа для сохранения', '/admin/catalog?group_id='.$group_id);


		if(isset($post['ids']))
			$ids = arr::int_array($post['ids']);
		if(isset($post['act']))
			$act = arr::int_array($post['act']);


		$table = new Catalog_Model();

		$set = array_intersect($ids, $act);
		if(count($set))
			$table->update(array('active' => 1), array('id' => $set));

		$clear = array_diff($ids, $act);
		if(count($clear))
			$table->update(array('active' => 0), array('id' => $clear));

		sort($ids);
		log::add('catalog', 'Групповое сохранение товаров id=[' . implode(',', $ids) . ']');

		Cache::instance()->delete_tag('catalog');

		message::info('Данные успешно сохранены', '/admin/catalog?group_id='.$group_id);
	}

	/**
	 * Удаление товара
	 *
	 */
	public function delete() {
		if(!Acl::instance()->is_allowed('catalog_del'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$id = (int) $this->input->get('id');
		$group_id = !empty($_REQUEST['group_id']) ? (int)$_REQUEST['group_id'] : false;

		if(!$group_id) message::error('Некорректный идентификатор (id) группы тоаров', '/admin/cataloggroups');

		$table = new Catalog_Model();
		$table->delete(array('id' => $id));
		DBFile::delete_items('catalog', $id);

		log::add('catalog', 'Удаление товара id='.$id);

		Cache::instance()->delete_tag('catalog');

		message::info('Товар успешно удален', '/admin/catalog?group_id='.$group_id);
	}

	public function delete_file() {
		if(!Acl::instance()->is_allowed('catalog_edit'))
			message::error('Нет прав доступа к данному разделу', '/admin');


		$id = (int) $this->input->get('id');
		$page_id = (int) $this->input->get('page');
		$group_id = (int) $this->input->get('group_id');

		if(!$group_id)
			message::error('Неверный идентификатор группы товаров', '/admin');

		if(!$page_id)
			message::error('Неверный идентификатор товара', '/admin');

		if(!$id)
			message::error('Неверный идентификатор удаляемого файла', '/admin');



		DBFile::delete_files('catalog', $id);

		log::add('catalog', 'Удаление файла ['.$id.'] у товара id='.$page_id);

		Cache::instance()->delete_tag('catalog');

		message::info('Файл успешно удален', '/admin/catalog/edit?group_id='.$group_id.'&id='.$page_id);
	}

    public function import(){
    	if(!Acl::instance()->is_allowed('catalog_edit'))
			message::error('Нет прав доступа к данному разделу', '/admin');

		$resFile = fopen("/home/atlets/domains/atlets.ru/public_html/main.csv", "r");
		$arrCsvData = fgetcsv($resFile, 3000, ";", '"');

		$intCounter = -1;
        //db::query("truncate table `ml_catalog`");
        //db::query("truncate table `ml_catalog_manufacturer`");
        //db::query("truncate table `ml_catalog_groups`");
        //db::query("truncate table `ml_catalog_group_contents`");
        //db::query("delete from `ml_files` where item_table = 'catalog'");


		db::query("update`ml_catalog` set `active` = 0");

		while(($row = fgetcsv($resFile, 3000, ";", '"')) !== FALSE) {
			//if($intCounter > 30) return;
			//$row[0] = preg_replace('/^[0-9 -]+/i', '', $row[0]);
		    //foreach($row as $key => $val){
		    //	$row[$key] = iconv('windows-1251', 'utf-8', $val);
		    //}

		    $row[8] = floor(doubleval($row[8])/100)*100 - 10;

		    //$row[0] = preg_replace('/^[0-9 -]+/i', '', $row[0]);

		    $resQuery = db::query("select * from `ml_catalog_manufacturer`
	        			               where `name` = '".mysql_real_escape_string($row[6])."'")->row();

	        if($resQuery){
	        	$intManufacturerId = $resQuery['id'];
	        } else {
	        	$resQuery = db::query("insert into `ml_catalog_manufacturer` (`name`)
	        				 values ('".mysql_real_escape_string($row[6])."')");

	        	$intManufacturerId = $resQuery->insert_id();
	        }


			$table_groups = new CatalogGroups_Model();

	        $intLastParentLft = 0;
	        $intLastParentRgt = 9999999;
	        $intTopLevelParent = 0;

	        $arrGroup = $table_groups->root(1);

			if (!$arrGroup) { //если дерево пустое
				$arrGroup['lft']   = 1;
				$arrGroup['rgt']   = 2;
				$arrGroup['level'] = 0;
				$arrGroup['scope'] = 1;

				$result = $table_groups->insert($arrGroup);

				$table_content = new CatalogGroupContents_Model();
				$group_content = array(
					'group_id'          		   => $result->insert_id(),
					'title'             		   => 'Каталог товаров',
					'uri'   					   => ''
				);

				$table_content->insert($group_content);
			}

	        for($i=1; $i < 3; $i++){

	        	if(strlen($row[3+$i]) <= 2) continue;

	        	$resCatQuery = db::query("select
	        								id, lft, rgt, level, scope
	        								from `ml_catalog_groups`
	        	                            where `title` = '".mysql_real_escape_string($row[3+$i])."' and lft>'".$arrGroup['lft']."' and rgt < '".$arrGroup['rgt']."'")->rows();

	        	if(count($resCatQuery) > 0){
	        		$arrGroup = $resCatQuery[0];
	        	}else{

					$result = $table_groups->insert_as_last_child($arrGroup);
	        		$id = $result->insert_id();

					//сохраняем title группы
					$group = array(
						'title'   => mysql_real_escape_string($row[3+$i]),
					);
					$table_groups->update($group, array('id' => $id));

					$resCatQuery = db::query("select
	        								id, lft, rgt, level, scope
	        								from `ml_catalog_groups`
	        	                            where `id` = '".$id."'")->rows();

				    $arrGroup = $resCatQuery[0];

	        		$table_content = new CatalogGroupContents_Model();
					$group_content = array(
						'group_id'          		   => $id,
						'title'             		   => mysql_real_escape_string($row[3+$i]),
						'uri'   					   => url::title(mysql_real_escape_string($row[3+$i]),"_")
					);

					$table_content->insert($group_content);
	        	}
	        }
	        $resQuery = db::query("select * from `ml_catalog` where `1c_code` = '".mysql_real_escape_string($row[1])."'")->rows();

	        $intCurrentProductId = 0;
	        if(count($resQuery)){
	        	$arrCurrentData = $resQuery[0];
	        	$intCurrentProductId = $arrCurrentData['id'];
	        	db::query("update `ml_catalog`
	        				 set `manufacturer_id` = '".intval($intManufacturerId)."',
	        				     `price` = '".doubleval($row[8])."',
	        				     `1c_code` = '".mysql_real_escape_string($row[1])."',
	        				     `name` = '".mysql_real_escape_string($row[0])."',
	        				     `description` = '".mysql_real_escape_string($row[7])."',
	        				     `tastes` = '".mysql_real_escape_string($row[14])."',
	        				     `volume` = '".mysql_real_escape_string($row[16])."',
	        				     `group_id` = '".$arrGroup['id']."',
	        				     `seo_title` = '".mysql_real_escape_string($row[0])."',
	        				     `seo_keywords` = '".mysql_real_escape_string($row[0])."',
	        				     `seo_description` = '".mysql_real_escape_string($row[0])."',
	        				     `active` = 1
	        				 where `id` = '".intval($intCurrentProductId)."'");
	        } else {
	        	$resQuery = db::query("INSERT INTO `ml_catalog` (`seo_title`, `seo_keywords`, `seo_description`,
	        									`tastes`,  `volume`,
	        									`active`, `code`, `manufacturer_id`, `price`,  `1c_code`,
	        									`name`, `description`, `group_id`, `uri`)
	        							VALUES ( '".mysql_real_escape_string($row[0])."','".mysql_real_escape_string($row[0])."','".mysql_real_escape_string($row[0])."',
	        							'".mysql_real_escape_string($row[14])."', '".mysql_real_escape_string($row[16])."',
	        							1, '".mysql_real_escape_string($row[1])."', '".intval($intManufacturerId)."', '".doubleval($row[8])."', '".mysql_real_escape_string($row[1])."',
	        							'".mysql_real_escape_string($row[0])."','".mysql_real_escape_string($row[7])."', '".$arrGroup['id']."','".url::title(crc32(mysql_real_escape_string($row[1])).' '.mysql_real_escape_string($row[0]),"_")."')");
	        	$intCurrentProductId = $resQuery->insert_id();
	        }

	        $mime = Kohana::config('mimes.'.DBFile::getFileExtention($row[9]));
			$strFileType=@explode("/",$mime[0]);
          	$strFileType=$strFileType[0];
            if($strFileType=="image" && (file_exists(DOCROOT.'/tmp/'.md5($row[9])) || copy($row[9], DOCROOT.'/tmp/'.md5($row[9])))){
            	$intFCounter++;
				$_FILES['image'] = Array(
       				'tmp_name' => DOCROOT.'/tmp/'.md5($row[9]),
       				'error' => 0,
       				'name' => md5($row[9]).".".DBFile::getFileExtention($row[9]),
       				'size' => filesize(DOCROOT.'/tmp/'.md5($row[9])),
       				'type' => $mime
       			);
       			DBFile::save('catalog', $intCurrentProductId, 'image');
       			unset($_FILES['image']);

       			$_FILES['imageleft'] = Array(
       				'tmp_name' => DOCROOT.'/tmp/'.md5($row[9]),
       				'error' => 0,
       				'name' => md5($row[9]).".".DBFile::getFileExtention($row[9]),
       				'size' => filesize(DOCROOT.'/tmp/'.md5($row[9])),
       				'type' => $mime
       			);
       			DBFile::save('catalog', $intCurrentProductId, 'imageleft');
       			unset($_FILES['imageleft']);
			}

			//usleep(110000);

	        $intCounter++;
	 	}

	 	Cache::instance()->delete_tag('catalog');

	 	//$this->import_images();
    }

    public function import_images(){
    	db::query("delete from `ml_files` where `item_table`='catalog'");

    	$resFile = fopen("/home/atlets/domains/atlets.ru/public_html/pic.csv", "r");
		$arrCsvData = fgetcsv($resFile, 3000, ";", '"');

		$intCounter = -1;
		$arrMainImageBlnMap = Array();

		$intFCounter=0;
		while(($row = fgetcsv($resFile, 3000, ";", '"')) !== FALSE) {
			//foreach($row as $key => $val){
		    //	$row[$key] = iconv('windows-1251', 'utf-8', $val);
		    //}

			if(!preg_match('/([^0-9]+)/i', $row[0])){
				$resQuery = db::query("select * from `ml_catalog` where `1c_code` = '".mysql_real_escape_string($row[0])."' or CAST(`1c_code` as SIGNED) = CAST('".intval($row[0])."' as SIGNED)")->rows();
			} else {
				$resQuery = db::query("select * from `ml_catalog` where `1c_code` = '".mysql_real_escape_string($row[0])."'")->rows();
			}


	        if(count($resQuery) > 0){
	        	$arrTmpProductImages = db::query("select id from `ml_files` where `item_table`='catalog' and `input_name`='image' and `item_id`='".$resQuery[0]['id']."'")->rows();

	        	//if(count($arrTmpProductImages) > 0)continue;

				$mime = Kohana::config('mimes.'.DBFile::getFileExtention($row[3]));
				$strFileType=@explode("/",$mime[0]);
           		$strFileType=$strFileType[0];
	            if($strFileType=="image" && (file_exists(DOCROOT.'/tmp/'.$row[3]) || copy($row[2], DOCROOT.'/tmp/'.$row[3]))){
	            	$intFCounter++;
					if(!isset($arrMainImageBlnMap[$resQuery[0]['id']])&&count($arrTmpProductImages)<=0){
						$_FILES['image'] = Array(
	           				'tmp_name' => DOCROOT.'/tmp/'.$row[3],
	           				'error' => 0,
	           				'name' => $row[3],
	           				'size' => filesize(DOCROOT.'/tmp/'.$row[3]),
	           				'type' => $mime
	           			);
	           			DBFile::save('catalog', $resQuery[0]['id'], 'image');
	           			unset($_FILES['photo_'.$intFCounter]);

	           			$_FILES['imageleft'] = Array(
	           				'tmp_name' => DOCROOT.'/tmp/'.$row[3],
	           				'error' => 0,
	           				'name' => $row[3],
	           				'size' => filesize(DOCROOT.'/tmp/'.$row[3]),
	           				'type' => $mime
	           			);
	           			DBFile::save('catalog', $resQuery[0]['id'], 'imageleft');
	           			unset($_FILES['imageleft']);
	           			$arrMainImageBlnMap[$resQuery[0]['id']] = true;
					}else{
						$_FILES['photo_'.$intFCounter] = Array(
	           				'tmp_name' => DOCROOT.'/tmp/'.$row[3],
	           				'error' => 0,
	           				'name' => $row[3],
	           				'size' => filesize(DOCROOT.'/tmp/'.$row[3]),
	           				'type' => $mime
	           			);

						DBFile::save('catalog', $resQuery[0]['id'], 'photo_'.$intFCounter, TRUE);
						unset($_FILES['photo_'.$intFCounter]);
					}
				}

				//usleep(110000);

	        }else{
				echo("Not found: ".$row[0]."<br>");
			}
	        $intCounter++;
	 	}
    }


    public function import_test(){
    	if(!Acl::instance()->is_allowed('catalog_edit'))
			message::error('Нет прав доступа к данному разделу', '/admin');

		$xmlData = simplexml_load_file("http://www.intimshop.ru/resources/export/catalog.xml");

		$intCounter = -1;
        db::query("truncate table `ml_catalog`");
        db::query("truncate table `ml_catalog_manufacturer`");
        db::query("truncate table `ml_catalog_groups`");
        db::query("truncate table `ml_catalog_group_contents`");
        db::query("truncate table `ml_catalog_cart`");
        db::query("delete from `ml_files` where `item_table`='catalog'");


		db::query("update`ml_catalog` set `active` = 0");

		$arrCat = Array();
		foreach ($xmlData->shop->categories->category as $cat){
            $arrCat[intval($cat['id'])] = Array('name' => strval($cat), 'parent' => intval($cat['parentId']));
		}

		$arrMan = Array();
		foreach ($xmlData->shop->producers->producer as $man){
            $arrMan[intval($man['id'])] = Array('name' => strval($man));
		}

		foreach ($xmlData->shop->offers->offer as $xmlItem) {

			$arrCategories = Array();
            $intMaxCntIndx = 0;
            $intMaxCntCount = 0;
            $curIteration = -1;
            $arrCategories[] = Array();
			foreach($xmlItem->categoryId as $curCatId){

				$curIteration++;
				$curCat = intval($curCatId);
                $arrCategories[$curIteration] = Array();
				$arrCategories[$curIteration][] = $arrCat[$curCat]['name'];

			    while(intval($curCat) > 0){
			    	$curCat = $arrCat[$curCat]['parent'];
			    	if($curCat > 0){
	                	$arrCategories[$curIteration][] = strval($arrCat[$curCat]['name']);
	                }
			    }

			    if(count($arrCategories[$curIteration]) > $intMaxCntCount){
			    	$intMaxCntCount = count($arrCategories[$curIteration]);
			    	$intMaxCntIndx = $curIteration;
			    }
		    }

		    $arrCategories = array_reverse($arrCategories[$intMaxCntIndx]);

		    $row[0] = strval($xmlItem->title);
		    $row[1] = 'tmp'.strval($xmlItem['id']);
		    $row[2] = 'tmp'.strval($xmlItem['id']);
		    $row[3] = $arrCategories[0];
		    $row[4] = $arrCategories[1];
		    $row[5] = $arrCategories[2];
		    $row[6] = $arrMan[intval($xmlItem->producerId)]['name'];
		    $row[7] = strval($xmlItem->description);
		    $row[8] = strval($xmlItem->price);
		    $row[9] = str_replace('.list.', '.marked.', strval($xmlItem->picture));
		    $row[10] = strval($xmlItem->categoryId);

		    $row[8] = floor(doubleval($row[8])/100)*100 - 10;

		    $resQuery = db::query("select * from `ml_catalog_manufacturer`
	        			               where `name` = '".mysql_real_escape_string($row[6])."'")->rows();
	        if(count($resQuery) > 0){
	        	$intManufacturerId = $resQuery[0]['id'];
	        } else {
	        	$resQuery = db::query("insert into `ml_catalog_manufacturer` (`name`)
	        				 values ('".mysql_real_escape_string($row[6])."')");

	        	$intManufacturerId = $resQuery->insert_id();
	        }

			$table_groups = new CatalogGroups_Model();

	        $intLastParentLft = 0;
	        $intLastParentRgt = 9999999;
	        $intTopLevelParent = 0;

	        $arrGroup = $table_groups->root(1);

			if (!$arrGroup) { //если дерево пустое
				$arrGroup['lft']   = 1;
				$arrGroup['rgt']   = 2;
				$arrGroup['level'] = 0;
				$arrGroup['scope'] = 1;

				$result = $table_groups->insert($arrGroup);

				$table_content = new CatalogGroupContents_Model();
				$group_content = array(
					'group_id'          		   => $result->insert_id(),
					'title'             		   => 'Каталог товаров',
					'uri'   					   => ''
				);

				$table_content->insert($group_content);
			}

	        for($i=0; $i < 2; $i++){

	        	if(strlen($row[3+$i]) <= 2) continue;

	        	$resCatQuery = db::query("select
	        								id, lft, rgt, level, scope
	        								from `ml_catalog_groups`
	        	                            where `title` = '".mysql_real_escape_string($row[3+$i])."' and lft>'".$arrGroup['lft']."' and rgt < '".$arrGroup['rgt']."'")->rows();

	        	if(count($resCatQuery) > 0){
	        		$arrGroup = $resCatQuery[0];
	        	}else{

					$result = $table_groups->insert_as_last_child($arrGroup);
	        		$id = $result->insert_id();

					//сохраняем title группы
					$group = array(
						'title'   => mysql_real_escape_string($row[3+$i]),
					);
					$table_groups->update($group, array('id' => $id));

					$resCatQuery = db::query("select
	        								id, lft, rgt, level, scope
	        								from `ml_catalog_groups`
	        	                            where `id` = '".$id."'")->rows();

				    $arrGroup = $resCatQuery[0];

	        		$table_content = new CatalogGroupContents_Model();
					$group_content = array(
						'group_id'          		   => $id,
						'title'             		   => mysql_real_escape_string($row[3+$i]),
						'uri'   					   => url::title(mysql_real_escape_string($row[3+$i]),"_")
					);

					$table_content->insert($group_content);
	        	}
	        }
	        $resQuery = db::query("select * from `ml_catalog` where `1c_code` = '".mysql_real_escape_string($row[2])."'")->rows();

	        $intCurrentProductId = 0;
	        if(count($resQuery)){
	        	$arrCurrentData = $resQuery[0];
	        	$intCurrentProductId = $arrCurrentData['id'];
	        	db::query("update `ml_catalog`
	        				 set `manufacturer_id` = '".intval($intManufacturerId)."',
	        				     `price` = '".doubleval($row[8])."',
	        				     `1c_code` = '".mysql_real_escape_string($row[2])."',
	        				     `name` = '".mysql_real_escape_string($row[0])."',
	        				     `description` = '".mysql_real_escape_string($row[7])."',
	        				     `group_id` = '".$arrGroup['id']."',
	        				     `seo_title` = '".mysql_real_escape_string($row[0])."',
	        				     `seo_keywords` = '".mysql_real_escape_string($row[0])."',
	        				     `seo_description` = '".mysql_real_escape_string($row[0])."',
	        				     `active` = 1
	        				 where `id` = '".intval($intCurrentProductId)."'");
	        } else {
	        	$resQuery = db::query("INSERT INTO `ml_catalog` (`seo_title`, `seo_keywords`, `seo_description`,
	        									`active`, `code`, `manufacturer_id`, `price`,  `1c_code`,
	        									`name`, `description`, `group_id`, `uri`)
	        							VALUES ( '".mysql_real_escape_string($row[0])."','".mysql_real_escape_string($row[0])."','".mysql_real_escape_string($row[0])."',
	        							1, '".mysql_real_escape_string($row[1])."', '".intval($intManufacturerId)."', '".doubleval($row[8])."', '".mysql_real_escape_string($row[2])."',
	        							'".mysql_real_escape_string($row[0])."','".mysql_real_escape_string($row[7])."', '".$arrGroup['id']."','".url::title(crc32(mysql_real_escape_string($row[2])).' '.mysql_real_escape_string($row[0]),"_")."')");
	        	$intCurrentProductId = $resQuery->insert_id();
	        }

	        $mime = Kohana::config('mimes.'.DBFile::getFileExtention($row[9]));
			$strFileType=@explode("/",$mime[0]);
          	$strFileType=$strFileType[0];
            if($strFileType=="image" && (file_exists(DOCROOT.'/tmp/'.md5($row[9])) || copy($row[9], DOCROOT.'/tmp/'.md5($row[9])))){
            	$intFCounter++;
				$_FILES['image'] = Array(
       				'tmp_name' => DOCROOT.'/tmp/'.md5($row[9]),
       				'error' => 0,
       				'name' => md5($row[9]).".".DBFile::getFileExtention($row[9]),
       				'size' => filesize(DOCROOT.'/tmp/'.md5($row[9])),
       				'type' => $mime
       			);
       			DBFile::save('catalog', $intCurrentProductId, 'image');
       			unset($_FILES['image']);

       			$_FILES['imageleft'] = Array(
       				'tmp_name' => DOCROOT.'/tmp/'.md5($row[9]),
       				'error' => 0,
       				'name' => md5($row[9]).".".DBFile::getFileExtention($row[9]),
       				'size' => filesize(DOCROOT.'/tmp/'.md5($row[9])),
       				'type' => $mime
       			);
       			DBFile::save('catalog', $intCurrentProductId, 'imageleft');
       			unset($_FILES['imageleft']);
			}

			//usleep(110000);

	        $intCounter++;
	 	}

	 	Cache::instance()->delete_tag('catalog');

	 	//$this->import_images();
    }
}
?>
