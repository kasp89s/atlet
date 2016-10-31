<?php
/**
 * Главная страница
 *
 */
class Import_Controller extends T_Controller {

	public function index() {
         $dbLocal = Database::instance();
         $dbRemote = Database::instance('luxlocaloriginal');

         $arrOld2New=Array(
         	1 => 41,
         	2 => 42,
         	3 => 43,
         	4 => 44,
         	6 => 1,
         	7 => 45,
         );

         $arrContent=$dbRemote
			         	->select('*')
			         	->from('content')
			         	->get()
			         	->rows();

	     $dbLocal->delete('files',Array('item_table<>'=>'pages'));

		 foreach($arrContent as $key => $value){
		 	 $data=Array(
		 	 	 'title' => $value['name'],
		 	 	 'preview' => $value['content']
		 	 );

		 	 $dbLocal->update('pages',$data,Array('id' => $arrOld2New[$value['id']]));

		 	 $data=Array(
		 	 	 'title' => $value['name'],
		 	 	 'uri' => $value['url'],
		 	 	 'preview' => $value['content'],
		 	 	 'description' => $value['content'],
		 	 	 'seo_title' => $value['name']."(заголовок)",
		 	 	 'seo_keywords' => $value['name']."(ключевые слова)",
		 	 	 'seo_description' => $value['name']."(описание)",
		 	 	 'date_create'       => new Database_Expr('now()'),
				 'date_modified'     => new Database_Expr('now()'),

		 	 );

		 	 $dbLocal->update('page_contents',$data,Array('id' => $arrOld2New[$value['id']]));
		 }

		 $dbLocal->delete('catalog_groups',Array('id>'=>3));
		 $dbLocal->query("ALTER TABLE `ml_catalog_groups` AUTO_INCREMENT 4");
		 $dbLocal->delete('catalog_group_contents',Array('id>'=>3));
		 $dbLocal->query("ALTER TABLE `ml_catalog_group_contents` AUTO_INCREMENT 4");

         $arrGroups=$dbRemote
			         	->select('*')
			         	->from('catalog')
			         	->order_by(Array('parent_id'=>'asc','sort'=>'asc','name'=>'asc'))
			         	->get()
			         	->rows();

		 $mdlGroups = new CatalogGroups_Model();

		 $arrOld2NewGroups=Array(
		 	 0 => 3
		 );

         foreach($arrGroups as $key => $value){

             $root=$dbLocal
		         	->select('*')
		         	->from('catalog_groups')
		         	->where('id',$arrOld2NewGroups[$value['parent_id']])
		         	->get()
		         	->row();

             $result = $mdlGroups->insert_as_last_child($root);
             $id = $result->insert_id();
             $arrOld2NewGroups[$value['id']]=$id;

             $data=Array(
		 	 	 'title' => $value['name']
		 	 );

		 	 $group_content = array(
				 'group_id'          => $id,
				 'title'             => $value['name'],
				 'code'              => $value['art'],
				 'uri'   			 => (utf8::strlen($value['url'])>0?$value['url']:(url::title($value['name'],"_"))),
				 'short_descr'       => $value['short_descr'],
				 'full_descr'        => $value['descr'],
				 'seo_title'         => $value['title'],
				 'seo_keywords'      => $value['title'],
				 'seo_description'   => $value['title'],
				 'date_create'       => new Database_Expr('now()'),
				 'date_modified'     => new Database_Expr('now()'),
				 'is_vip'            => $value['isVip'],
				 'active'            => $value['active']
			 );

		 	 $dbLocal->update('catalog_groups',$data,Array('id' => $id));
		 	 $dbLocal->insert('catalog_group_contents',$group_content);

             if(!is_file("z:/tmp/images/".$value['pic'])){
	              $strImage=file_get_contents('http://luxpodarki.ru/files/'.$value['pic']);
	              $resImage=fopen("z:/tmp/images/".$value['pic'],"w");
	              fputs($resImage,$strImage);
	              fclose($resImage);
             }

		 	 $arrSource=Array(
		 	 	  'error'     => 0,
		 	 	  'name'      => $value['pic'],
		 	 	  'tmp_name'  => ('z:/tmp/images/'.$value['pic']),
		 	 	  'size'      => filesize('z:/tmp/images/'.$value['pic']),
		 	 	  'input_name'=> 'image'
		 	 );

             $strDirForWriting=DBFile::getDirForWriting();
		 	 DBFile::saveFile($arrSource,$strDirForWriting,$id,"catalog_groups",false);

         }

         $arrProducts=$dbRemote
	         	->select('*')
	         	->from('cat_items')
	         	->get()
	         	->rows();

	     $dbLocal->delete('catalog',Array('id<>'=>'0'));
	     $dbLocal->query("ALTER TABLE `ml_catalog` AUTO_INCREMENT 1");

	     foreach($arrProducts as $key => $value){

              $arrProductVars = array(
				 'group_id'          => intval($arrOld2NewGroups[$value['cat_id']]),
				 'name'              => $value['name'],
				 'code'              => $value['art'],
				 'price'             => intval(str_replace(" ","",$value['price'])),
				 'uri'   			 => (utf8::strlen($value['url'])>0?$value['url']:(url::title($value['name']."_".$value['art'],"_"))),
				 'description'       => preg_replace("#(<a[^>]+>[^<]+</a>)+#i","",$value['descr']),
				 'seo_title'         => $value['name'],
				 'seo_keywords'      => $value['name'],
				 'seo_description'   => $value['name'],
				 'date_create'       => new Database_Expr('now()'),
				 'date_modified'     => new Database_Expr('now()'),
				 'active'            => $value['active']
			 );

			 $result=$dbLocal->insert('catalog',$arrProductVars);
			 $id = $result->insert_id();

			 if(!is_file("z:/tmp/images/".$value['thumb'])){
	              $strImage=file_get_contents('http://luxpodarki.ru/files/'.$value['thumb']);
	              $resImage=fopen("z:/tmp/images/".$value['thumb'],"w");
	              fputs($resImage,$strImage);
	              fclose($resImage);
             }

             $arrSource=Array(
		 	 	  'error'     => 0,
		 	 	  'name'      => $value['thumb'],
		 	 	  'tmp_name'  => ('z:/tmp/images/'.$value['thumb']),
		 	 	  'size'      => filesize('z:/tmp/images/'.$value['thumb']),
		 	 	  'input_name'=>'image'
		 	 );

             $strDirForWriting=DBFile::getDirForWriting();
		 	 DBFile::saveFile($arrSource,$strDirForWriting,$id,"catalog",false);

			 $arrPhotos=$dbRemote
	         	->select('*')
	         	->from('item_pics')
	         	->where('item_id',$value['id'])
	         	->get()
	         	->rows();

             foreach($arrPhotos as $k => $v){
				 if(!is_file("z:/tmp/images/".$v['filename'])){
		              $strImage=file_get_contents('http://luxpodarki.ru/files/'.$v['filename']);
		              $resImage=fopen("z:/tmp/images/".$v['filename'],"w");
		              fputs($resImage,$strImage);
		              fclose($resImage);
	             }

			 	 $arrSource=Array(
			 	 	  'error'     => 0,
			 	 	  'name'      => $v['filename'],
			 	 	  'tmp_name'  => ('z:/tmp/images/'.$v['filename']),
			 	 	  'size'      => filesize('z:/tmp/images/'.$v['filename']),
			 	 	  'input_name'=>'photo_'.md5(rand().rand().microtime())
			 	 );

                 $strDirForWriting=DBFile::getDirForWriting();
			 	 DBFile::saveFile($arrSource,$strDirForWriting,$id,"catalog",false);
		 	 }

		 	 $arrGroupInfo=$dbRemote
			         	->select('*')
			         	->from('catalog')
			         	->where('art',$value['art'])
			         	->get()
			         	->row();

			 if($arrGroupInfo){
			 	 if(!is_file("z:/tmp/images/".$arrGroupInfo['pic'])){
		              $strImage=file_get_contents('http://luxpodarki.ru/files/'.$arrGroupInfo['pic']);
		              $resImage=fopen("z:/tmp/images/".$arrGroupInfo['filename'],"w");
		              fputs($resImage,$strImage);
		              fclose($resImage);
	             }

			 	 $arrSource=Array(
			 	 	  'error'     => 0,
			 	 	  'name'      => $arrGroupInfo['pic'],
			 	 	  'tmp_name'  => ('z:/tmp/images/'.$arrGroupInfo['pic']),
			 	 	  'size'      => filesize('z:/tmp/images/'.$arrGroupInfo['pic']),
			 	 	  'input_name'=> 'imageleft'
			 	 );

                 $strDirForWriting=DBFile::getDirForWriting();
			 	 DBFile::saveFile($arrSource,$strDirForWriting,$id,"catalog",false);
			 }

	     }

         $this->vipcat();


         echo("Готово(".(time()).")");

	}

	public function vipcat() {
         $dbLocal = Database::instance();
         $dbRemote = Database::instance('luxlocaloriginal');

         $arrVipCats=$dbRemote
	         	->select('*')
	         	->from('itemsinvipcats')
	         	->get()
	         	->rows();

	     $arrNewGroups=$dbLocal
	         	->select('*')
	         	->from('catalog_groups')
	         	->get()
	         	->rows();

	     $arrOldGroups=$dbRemote
	         	->select('*')
	         	->from('catalog')
	         	->get()
	         	->rows();

	     $arrTmpGroupsConvert=Array();
	     $arrGroupsConvert=Array();

	     foreach($arrOldGroups as $key => $value){
	     	 $arrTmpGroupsConvert[$value['name']]=$value['id'];
	     }

	     foreach($arrNewGroups as $key => $value){
	     	 $arrGroupsConvert[$arrTmpGroupsConvert[$value['title']]]=$value['id'];
	     }

	     $arrNewProducts=$dbLocal
	         	->select('*')
	         	->from('catalog')
	         	->get()
	         	->rows();

	     $arrOldProducts=$dbRemote
	         	->select('*')
	         	->from('cat_items')
	         	->get()
	         	->rows();

	     $arrTmpConvert=Array();
	     $arrConvert=Array();

	     foreach($arrOldProducts as $key => $value){
	     	 $arrTmpConvert[$value['art']]=$value['id'];
	     }

	     foreach($arrNewProducts as $key => $value){
	     	 $arrConvert[$arrTmpConvert[$value['code']]]=$value['id'];
	     }

         foreach($arrVipCats as $key => $value){
             $arrRowVars = array(
				 'cat_id'          => intval($arrGroupsConvert[$value['vipcatid']]),
				 'product_id'      => intval($arrConvert[$value['itemid']]),
				 'active'   	   => 1,
			 );

			 $dbLocal->insert('catalog_vipcats_content',$arrRowVars);
         }


         echo("Готово(".(time()).")");

	}

}
?>