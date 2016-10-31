<?php
/**
 * Модуль ACL (Auth + Access Control List)
 * Авторизация + права доступа
 * Основан на системной бибилотеке Kohana::Auth
 *
 * @author Antuan
 */
class Acl_Core {

	protected $config_name;
	protected $session;
	protected $config;

	/**
	 * Create an instance of Acl.
	 *
	 * @return  object
	 */
	public static function factory($config_name = 'acl') {
		return new Acl($config_name);
	}

	/**
	 * Return a static instance of Acl.
	 *
	 * @return  object
	 */
	public static function instance($config_name = 'acl') {
		static $instance;

		// Load the Acl instance
		empty($instance[$config_name]) and $instance[$config_name] = new Acl($config_name);

		return $instance[$config_name];
	}

	/**
	 * Loads Session and configuration options.
	 *
	 * @return  void
	 */
	public function __construct($config_name = 'acl') {
		$this->config_name     = $config_name;
		$this->config          = Kohana::config($config_name);
		$this->session 	       = Session::instance();

		// Clean up the salt pattern and split it into an array
		$this->config['salt_pattern'] = preg_split('/,\s*/', $this->config['salt_pattern']);
	}

	/**
	 * Returns TRUE is a user is currently logged in
	 * Проверка на авторизованность
	 *
	 * @return  boolean
	 */
	public function logged_in() {
		$user = $this->session->get($this->config['session_key']);
		return !empty($user);
	}


	/**
	 * Returns the user - if any
	 * Получить данные авторизованного пользователя
	 *
	 * @return  object / FALSE
	 */
	public function get_user() {
		if ($this->logged_in()) {
			return $this->session->get($this->config['session_key']);
		}

		return FALSE;
	}


	protected function complete_login($user, $remember = FALSE) {
		$this->session->regenerate();

		$this->session->set($this->config['session_key'], $user);
	}

	/**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 * Авторизация
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function login($username, $password, $remember = FALSE) {
		if (empty($password))
			return FALSE;

		$table_users = new AuthUsers_Model();
		$user = $table_users->db
			->select(Array('id', 'username', 'password', 'fio'))
			->where('username', $username)
			->from($table_users->table_name)
			->get()
			->row();

		$salt = $this->find_salt($user['password']);

		if($this->hash_password($password, $salt) === $user['password']) {
			$this->complete_login($user, $remember);

			return TRUE;
		}

		return FALSE;
	}


	/**
	 * Log out a user by removing the related session variables.
	 * Разлогинивание
	 *
	 * @param   boolean  completely destroy the session
	 * @return  boolean
	 */
	public function logout($destroy = FALSE) {
		if ($destroy === TRUE) {
			$this->session->destroy();
		} else {
			$this->session->delete($this->config['session_key']);

			$this->session->regenerate();
		}

		return ! $this->logged_in();
	}

	/**
	 * Creates a hashed password from a plaintext password, inserting salt
	 * based on the configured salt pattern.
	 * Получение хэша пароля
	 *
	 * @param   string  plaintext password
	 * @return  string  hashed password string
	 */
	public function hash_password($password, $salt = FALSE) {
		if ($salt === FALSE) {
			// Create a salt seed, same length as the number of offsets in the pattern
			$salt = substr($this->hash(uniqid(NULL, TRUE)), 0, count($this->config['salt_pattern']));
		}

		// Password hash that the salt will be inserted into
		$hash = $this->hash($salt.$password);

		// Change salt to an array
		$salt = str_split($salt, 1);

		// Returned password
		$password = '';

		// Used to calculate the length of splits
		$last_offset = 0;

		foreach ($this->config['salt_pattern'] as $offset) {
			// Split a new part of the hash off
			$part = substr($hash, 0, $offset - $last_offset);

			// Cut the current part out of the hash
			$hash = substr($hash, $offset - $last_offset);

			// Add the part to the password, appending the salt character
			$password .= $part.array_shift($salt);

			// Set the last offset to the current offset
			$last_offset = $offset;
		}

		// Return the password, with the remaining hash appended
		return $password.$hash;
	}

	/**
	 * Perform a hash, using the configured method.
	 *
	 * @param   string  string to hash
	 * @return  string
	 */
	public function hash($str) {
		return hash($this->config['hash_method'], $str);
	}

	/**
	 * Finds the salt from a password, based on the configured salt pattern.
	 *
	 * @param   string  hashed password
	 * @return  string
	 */
	public function find_salt($password) {
		$salt = '';

		foreach ($this->config['salt_pattern'] as $i => $offset) {
			// Find salt characters, take a good long look...
			$salt .= substr($password, $offset + $i, 1);
		}

		return $salt;
	}

	/**
	 * Загрузить и рассчитать права пользователя $user
	 *
	 * @param   string  hashed password
	 * @return  string
	 */
	public function get_acl($user) {
		$table_acl = new AuthACL_Model();
		$table_user = new AuthUsers_Model();

		$roles_ids = array();
		$roles = $table_user->get_roles($user['id']);
		foreach ($roles as $v)
			$roles_ids[] = $v['id'];

		//загружаем действия для заданного пользователя и его ролей
		$user_acl = $table_acl->get_user_actions($user['id']);
		$role_acl = $table_acl->get_role_actions($roles_ids);

		$user_acl_ids = array();
		if(!empty($user_acl))
			foreach ($user_acl as $v)
				$user_acl_ids[$v['id']] = $v;

		$role_acl_ids = array();
		if(!empty($role_acl))
			foreach ($role_acl as $v)
				$role_acl_ids[$v['id']] = $v;


		//рассчитываем права для пользователя
		$true_acl = $user_acl_ids;
		foreach ($role_acl_ids as $k=>$v){
			if(empty($true_acl[$k]))
				$true_acl[$k] = $v;
		}
		foreach ($true_acl as $k=>$v){
			if(isset($v['access']) && $v['access'] == 'deny')
				unset($true_acl[$k]);
		}

		return $true_acl;
	}

	/**
	 * Проверка прав доступа авторизованного пользователя
	 *
	 * @param string $action_name
	 * @return boolean
	 */
	public function is_allowed() {
		if ($this->logged_in()) {

			$user = $this->get_user();
			if(utf8::strtolower($user['username']) == 'admin')
				return true;

			$parameters = func_get_args();
			$parameters = empty($parameters) ? NULL : $parameters;

			$config = Kohana::config('acl.actions');
			if(!isset($config)){
				$config = $this->get_acl($user);
				Kohana::config_set('acl.actions', $config);
			}


			foreach ($parameters as $action_name) {

				$arrExplodedStr = explode('_', $action_name);
				$section = (isset($arrExplodedStr[0])?$arrExplodedStr[0]:"");
				$action = (isset($arrExplodedStr[1])?$arrExplodedStr[1]:"");

				if($action == 'show'){
//					$allowed_actions = array();
//					foreach ($this->config['allowed_actions'] as $v)
//						$allowed_actions[] = $section . '_' . $v;
//                    var_dump($config); exit;
//					foreach ($config as $v) {
//						if(in_array($v['code'], $allowed_actions))
//							return TRUE;
//					}

                    foreach ($config as $v) {
                        if($v['code'] == $action_name)
                            return TRUE;
                    }
				} else {
					foreach ($config as $v) {
						if($v['code'] == $action_name)
							return TRUE;
					}
				}
			}

		}

		return FALSE;
	}

} // End Acl