<?php

class Frame_Admin_Controller extends Frame_Controller {

	private $template_name = 'admin';
	public $topmenu = array();
	public $menu = array();
	public $title = '';

	function __construct() {
		parent::__construct($this->template_name);
	}

	function render(){
		$this->template->menu = $this->menu;

		/**
		 * Биндинг названия раздела
		 */
		$subtitle = '';
		foreach ($this->menu as $v){
			if(trim($v['url'], '/') == url::current()){
				$subtitle = !empty($v['title']) ? $v['title'] : $v['section'];
				break;
			}
		}
		$this->template->section_subtitle = $subtitle;
		$this->template->section_title = $this->title;



		/**
		 * Генерация верхнего меню
		 */
		$start_topmenu = array(
			'order'             => array('name' => 'Заказы', 'link' => 'order'),
			'callback'          => array('name' => 'Обратный звонок', 'link' => 'callback'),
			'catalog'           => array('name' => 'Каталог', 'link' => 'cataloggroups'),
			'manufacturers'     => array('name' => 'Производители', 'link' => 'manufacturers'),
			'news'           	=> array('name' => 'Новости', 'link' => 'news'),
			'articles'          => array('name' => 'Статьи', 'link' => 'articles'),
			'links'             => array('name' => 'Ссылки', 'link' => 'links'),
			'catalogstats'      => array('name' => 'Статистика каталога', 'link' => 'catalogstats'),
		);

        $final_topmenu = array();

//        if(Acl::instance()->is_allowed('admin')) {
//            $final_topmenu = $start_topmenu;
//        }

		if(Acl::instance()->is_allowed('cms_show'))
			$final_topmenu[] = array('name' => 'CMS', 'link' => 'pages');


		$table = new Modules_Model();
		$rows = $table->db
			->select("*")
			->from($table->table_name)
			->get()
			->rows();

		foreach ($rows as $v){
			if(array_key_exists($v['code'], $start_topmenu) && Acl::instance()->is_allowed($v['code'] . '_show')){
				$final_topmenu[] = $start_topmenu[$v['code']];
			}
		}


		if(Acl::instance()->is_allowed('admin')) {
			$final_topmenu[] = array('name' => 'ACL', 'link' => 'users');
			$final_topmenu[] = array('name' => 'Журнал операций', 'link' => 'logs');
			$final_topmenu[] = array('name' => 'Настройки', 'link' => 'settings');
			$final_topmenu[] = array('name' => 'Сбросить кэш', 'link' => 'clearcache');
		} else {
            $final_topmenu[] = array('name' => 'Мои данные', 'link' => 'users');
        }

        if(Acl::instance()->is_allowed('trade_search')) {
            $final_topmenu[] = array('name' => 'Продажи', 'link' => 'trade');
        }

		$this->template->topmenu = $final_topmenu;

		parent::render();
	}

}
?>


















