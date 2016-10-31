<?php

class Admin_Controller extends T_Controller {

	public function __construct() {

		if (!Acl::instance()->logged_in() && url::current(false) != 'admin/auth'){
			Session::instance()->set("admin_referrer","/".url::current(true));
			url::redirect('/admin/auth');
		}

		parent::__construct();
		$this->frame = 'admin';

		//отключаем журнал доступа
		Kohana::config_set('access-log.enabled', FALSE);
	}

}
?>