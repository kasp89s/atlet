<?php
/**
 * Новости
 *
 */
class News_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}


	public function router($uri) {
	    $uri=trim($uri);
	    $uri=preg_replace("#\.html|\.xml#i","",$uri);
	    $uri=preg_replace("#^/#i","",$uri);

	    preg_match("#page([0-9]+)#i",$uri,$arrMatches);

	    if(count($arrMatches)>0){
	    	$pagerEnabled=true;
	    }

	    if(count($arrMatches)>0&&intval($arrMatches[1])>0){
        	$intPage=intval($arrMatches[1]);
        	$uri=preg_replace("#([/]?page[0-9]+[/]?)#i","",$uri);
        }else{
        	$intPage=false;
        }

        $arrUriSegments=explode("/",$uri);
        $this->current_uri=$uri;


        foreach($arrUriSegments as $key => $value){
        	if(strlen(trim($value))<=0){        		unset($arrUriSegments[$key]);
        	}
        }

        if(count($arrUriSegments) == 0){        	$this->index($intPage);
        }else{        	if($arrUriSegments[0]=='rss'){        		$this->rss();
        	}else{        		$this->detail($arrUriSegments[1]);        	}        }

	}

	/**
	 * Список новостей
	 *
	 */
	public function index($intPage) {
		$this->template = new View('news/index');

		$date = $this->input->get('date', 0);
		$date_u = strtotime((string)$date);  // Y-m-d

		$table = new News_Model;
		$table->db
			->select(Array('self.id', 'self.name', 'self.preview', 'self.date_publication', 'self.uri'))
			->where(array('self.active' => 1))
			->where('self.date_publication', '<=', db::expr('now()'))
			->from($table->table_name);


	    if ($date_u) {
	    	$date = date('Y-m-d', $date_u); // Y-m-d
	    	$table->db
	    		->where("date_publication", '>=', "{$date} 00:00:00")
	    		->where("date_publication", '<=', "{$date} 23:59:59");
	    	$this->template->date = date('d.m.Y', $date_u);
	    }

		$tm = new Tablemaker($table);
		$tm->pager_template = '_footer2';
		if($intPage>0)$tm->current_page=$intPage;
		$tm->page_sizes = array(10 => 10);
		$tm->default_page_size = 10;
		$tm->pager_window = 2;
		$tm->orderby = array('date_publication' => 'desc');
		$tm->files('news');
        $data=$tm->show();

        $arrData=Array(
         	'current_uri'  => false,//$this->get_uri_base('news'),
         	'total_pages'  => $tm->pager_info['total_pages'],
         	'current_page' => $tm->pager_info['page'] ,
         	'pages_array'  => $tm->pager_info['pages']
        );

		$this->add_component('pager', 'pager', $arrData);

		foreach($data['rows'] as $key => $value){
             $data['rows'][$key]['uri']='detail/'.$value['uri'];
        }

		$this->template->data = $data;

	}


	/**
	 * Полный вывод новости $id
	 *
	 * @param int $id идентификатор новости
	 */
	public function detail($uri) {
		$this->template = new View('news/detail');

		if($uri) {
			$table = new News_Model;
			$row = $table->db
				->select('self.*')
				->where(array('self.uri' => $uri, 'self.active' => 1))
				->where('self.date_publication', '<=', db::expr('now()'))
				->from($table->table_name)
				->get()
				->row();

			$this->template->data = $row;
			$this->template->files = DBFile::select('news', $row['id']);

			$this->set_attribute('breadcrumbs', Array(
	       		'title'  =>   $row['name'],
	       		'uri'    =>   ($this->get_uri_base('news').'detail/'.$row['uri'].".html")
	       	));

	       	$this->add_component('socialicons', 'socialicons', Array('text' => $row['name']));


			/**
			 * Биндим заголовки
			 */
			$this->add_attribute('title', $row['seo_title']);
			$this->add_attribute('keywords', $row['seo_keywords']);
			$this->add_attribute('description', $row['seo_description']);

		}
	}

	public function rss() {
		header("content-type: application/rss+xml");

		$this->template = new View('news/rss');
		$this->frame='empty';

		$table = new News_Model;
		$table->db
			->select(Array('self.id', 'self.name', 'self.preview', 'self.date_publication', 'self.uri'))
			->where(array('self.active' => 1))
			->where('self.date_publication', '<=', db::expr('now()'))
			->from($table->table_name);


		$tm = new Tablemaker($table);
		$tm->pager_template = '_footer2';
		$tm->current_page=1;
		$tm->page_sizes = array(10 => 10);
		$tm->default_page_size = 10;
		$tm->pager_window = 2;
		$tm->orderby = array('date_publication' => 'desc');
		$tm->files('news');
        $data=$tm->show();

		foreach($data['rows'] as $key => $value){
             $data['rows'][$key]['uri']='detail/'.$value['uri'];
             $data['rows'][$key]['date_publication'] = date('r', strtotime($data['rows'][$key]['date_publication']));
        }

		$this->template->data = $data;

		echo($this->template->render());
	}
}
?>