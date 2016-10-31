<?php
/**
 * ACL. Логи
 *
 * @author Antuan
 */
class Logs_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Журнал операций';

		parent::__construct();
	}


	/**
	 * Список логов
	 *
	 */
	public function index() {
		/**
		 * Проверка прав доступа
		 */
		if(!Acl::instance()->is_allowed('admin')){
			message::error('Нет прав доступа к данному разделу', '/admin');
		}


		$this->template = new View('admin/logs/index');

		$user = (int)$this->input->get('user');
		$section = $this->input->get('section');
		$date = $this->input->get('date');


		$table = new AuthLogs_Model;
		$table->info_user();
		$table->db
			->select('self.*')
			->from($table->table_name);

		if($user) $table->db->where('self.user', $user);
		if($section) $table->db->where('self.section', $section);
		if($date) {
			$date_u = strtotime((string)$date);
			$date_db = date('Y-m-d', $date_u); // Y-m-d

			$table->db
				->where("self.date_create", '>=', "{$date_db} 00:00:00")
				->where("self.date_create", '<=', "{$date_db} 23:59:59");

		}

		$tm = new Tablemaker($table);
		$tm->session = TRUE;
		$tm->orderby = array('self.id' => 'desc', 'self.date_create', 'user_username', 'user_fio');
		$data = &$tm->show();

		foreach ($data['rows'] as $k=>$v){
			$name = $v['section'];

			switch ($v['section']){
				case 'cms':            $name = 'CMS'; break;
				case 'acl':            $name = 'ACL'; break;
				case 'callback':       $name = 'Заказ обратного звонка'; break;
				case 'order':          $name = 'Заказы'; break;
				case 'catalog':        $name = 'Каталог'; break;
				case 'news':       	   $name = 'Новости'; break;
				case 'articles':       $name = 'Статьи'; break;
				case 'links':          $name = 'Ссылки'; break;
				case 'catalogstats':   $name = 'Статистика каталога'; break;
			}

			$data['rows'][$k]['section_name'] = $name;
		}


		form_filter::fill_item('date', $date, $data);
		form_filter::fill_list('user', $user, $data, $table->get_users());
		form_filter::fill_list('section', $section, $data, array(
			0 => array('id' => 'cms', 'name' => 'CMS'),
			1 => array('id' => 'callback', 'name' => 'Заказ обратного звонка'),
			2 => array('id' => 'order', 'name' => 'Заказы'),
			3 => array('id' => 'catalog', 'name' => 'Каталог'),
			4 => array('id' => 'news', 'name' => 'Новости'),
			5 => array('id' => 'articles', 'name' => 'Статьи'),
			6 => array('id' => 'links', 'name' => 'Ссылки'),
			7 => array('id' => 'acl', 'name' => 'ACL'),
		));

		$this->template->main = $data;
	}

}
?>