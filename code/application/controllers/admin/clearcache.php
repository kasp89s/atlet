<?php 
/**
 * Сброс кэша сайта
 *
 */
class Clearcache_Controller extends Admin_Controller {

	public function __construct(){
		$this->title = 'Сброс кэша';
		
		if(!Acl::instance()->is_allowed('admin')){
			message::error('Нет прав доступа к данному разделу', '/admin');
		}
		
		parent::__construct();
	}
	
	
	public function index() {
		$this->template = new View('admin/clearcache/index');
		
		Cache::instance()->delete_all();
		
		$smarty = new MY_Smarty;
		$smarty->clear_all_cache();
		
		message::info('Кэш сайта успешно сброшен');
	}
	
}
?>