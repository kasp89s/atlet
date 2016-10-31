<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package Auth
 *
 * Auth library configuration. By default, Auth will use the controller
 * database connection. If Database is not loaded, it will use use the default
 * database group.
 *
 * In order to log a user in, a user must have the `login` role. You may create
 * and assign any other role to your users.
 *
 *
 * Group Options:
 *  driver       - Driver to use for authentication. [File|Database]
 *
 *  hash_method  - Type of hash to use for passwords. Any algorithm supported by the hash function
 *                 can be used here. Note that the length of your password is determined by the
 *                 hash type + the number of salt characters.
 *                 @see http://php.net/hash
 *                 @see http://php.net/hash_algos
 *
 *  salt_pattern - Defines the hash offsets to insert the salt at. The password hash length
 *                 will be increased by the total number of offsets.
 *
 *  lifetime     - Set the auto-login (remember me) cookie lifetime, in seconds. The default
 *                 lifetime is two weeks.
 *
 *  session_key  - Set the session key that will be used to store the current user.
 *
 *  users        - Usernames (keys) and hashed passwords (values) used by the File driver.
 *                 Default admin password is "admin". You are encouraged to change this.
 *
 *  database     - Структура хранения бюджетов пользователя в БД (таблица, поле "логин", поле "пароль")
 */


$config['default'] = array
(
	'driver'       => 'File',
	'hash_method'  => 'sha1',
	'salt_pattern' => '1, 2, 3, 9, 10, 11, 20, 22, 28, 30',
	'lifetime'     => 1209600,
	'session_key'  => 'auth_user',
	'users'        => array('admin' => '9378b4074240962912642226505f664ad62d58644c23699dfa'),
	'database'     => array()
);

$config['public_users'] = array
(
	'driver'       => 'Database',
	'hash_method'  => 'sha1',
	'salt_pattern' => '1, 7, 8, 9, 10, 11, 15, 22, 24, 29',
	'lifetime'     => 1209600,
	'session_key'  => 'auth_public',
	'users'        => array(),
	'database'     => array('table' => 'public_users', 'login' => 'login', 'password' => 'password')
);