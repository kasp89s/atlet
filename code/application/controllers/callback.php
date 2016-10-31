<?php
/**
 * Форма заказа обратного звонка
 *
 * @author Sinner
 */
class Callback_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'common';

		parent::__construct();
	}

	/**
	 * Форма обратной связи
	 *
	 */
	public function index() {
		$this->template = new View('callback/add');
		$uri_base = $this->get_uri_base('callback');

        $post = !empty($_POST) ? $_POST : $_GET;
		$data = array();
		$table = new Callback_Model();

		/**
		 * Сохранение
		 */

        if (empty($post) === false) {
        $fill = $post;
        $fill['description'] = '';
        $fill['cat'] = 5;
        $fill['date_create'] = new Database_Expr('now()');

        if ($result = $table->insert($fill)) {
            $id = $result->insert_id();

            log::add('callback', 'Добавление заказа обратного звонка id=' . $id);

            $to = Kohana::config('cms.contacts.callback_email'); // Address can also be array('to@example.com', 'Name')
            if (utf8::strlen(Kohana::config('cms.contacts.default_email_from_title')) > 0) {
                $from = Array(
                    Kohana::config('cms.contacts.default_email_from'),
                    Kohana::config('cms.contacts.default_email_from_title')
                );
            } else {
                $from = Kohana::config('cms.contacts.default_email_from');
            }
            $subject = 'Поступил новый заказ на обратный звонок №' . $id;
            $tplEmail = new View('callback/email');
            $tplEmail->data = $fill;
            $tplEmail->data['id'] = $id;
            $message = $tplEmail->render();

            email::send($to, $from, $subject, $message, true);
            url::redirect($uri_base.'/success');
        }
        }

//		$data = $form->get_form($data) + $data + $form->get_errors();
//		$this->template->data = $data;
	}

	/**
	 * Сообщение о успешном результате работы
	 *
	 */
	public function success() {
		$this->template = new View('callback/success');
	}
}
?>
