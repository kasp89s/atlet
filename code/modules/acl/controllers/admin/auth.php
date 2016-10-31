<?php
/**
 * Модуль авторизации
 *
 * @author Antuan
 */
class Auth_Controller extends Admin_Controller {

	public function index() {
		return $this->login();
	}


	/**
	 * Авторизация
	 *
	 */
	public function login() {
		if (Acl::instance()->logged_in()){
			$url_referrer = Session::instance()->get_once('admin_referrer');
			$url_to = ($url_referrer) ? $url_referrer : '/admin';
			url::redirect($url_to);
		}


		$this->frame = 'clear';
		$this->template = new View('admin/auth');

		$post = $this->input->post();
		$errors = array();


		/**
		 * Правила валидации формы
		 */
		$form = new ValidateForm();
		$form->add_field('login', 'string', array('required', 'length[1,100]'));
		$form->add_field('password', 'string', array('required', 'length[1,100]'));


		/**
		 * Проверка введенных данных.
		 */
		if(!empty($post)){
			$data = $form->get_data();
			$form->validate();

			if($form->is_ok()){
				if ($this->check_login($post['login'], $post['password'])) {
					$url_referrer = Session::instance()->get_once('admin_referrer');
					$url_to = ($url_referrer) ? $url_referrer : '/admin';
					url::redirect($url_to);

				} else {
					$errors['err_login'] = 1;
				}
			}

		} else
			$data = array();

		$data = $form->get_form($data) + $form->get_errors() + $errors;
		$this->template->data = $data;
	}


	/**
	 * Разлогинивание
	 *
	 */
	public function logout() {

		Acl::instance()->logout(TRUE);
		url::redirect('/admin/auth');
	}


	/**
	 * Проверка логипаса + прав на конкретную администрацию
	 *
	 */
	public function check_login($login, $password) {
		if(!Acl::instance()->login($login, $password))
			return FALSE;

		return TRUE;
	}

}
?>