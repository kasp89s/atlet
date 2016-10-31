<?php
/**
 * Журналирование операций пользователей из ACL
 *
 * @package ACL
 * @author Antuan
 * $create 11.10.2009
 */
class log_Core {

	/**
	 * Добавление лога
	 *
	 * @param string $section раздел
	 * @param string $action действие
	 * @return boolean
	 */
	public static function add($section, $action) {
		if (Acl::instance()->logged_in() && !empty($section) && !empty($action)) {
			$user = Acl::instance()->get_user();
			return log::add_by_user($section, $action, $user['id']);
		}

		return FALSE;
	}

	/**
	 * Добавление лога
	 *
	 * @param string $section раздел
	 * @param string $action действие
	 * @param int $user_id пользователь
	 */
	public static function add_by_user($section, $action, $user_id) {
		db::insert('auth_logs', array(
			'user'        => $user_id,
			'action'      => $action,
			'section'     => $section,
			'date_create' => db::expr('now()')
		));
	}

}