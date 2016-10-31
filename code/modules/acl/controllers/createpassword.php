<?php 
/**
 * Генерирование хэша паролей для Acl
 * вызов: http://site.com/createpassword
 * 
 * @author Antuan
 */
class Createpassword_Controller extends Controller {

	const ALLOW_PRODUCTION = FALSE;

	public function index() {
		echo('
			<form method="POST">
			<input type="text" size="20" name="password"><br>
			<input type="submit" value="Получить хэш">
			</form>'
		);
			
		if(!empty($_POST['password'])){
			$password = $_POST['password'];
			$hash_password = Acl::instance()->hash_password($password);
			
			echo("
				<br>
				Пароль: {$password}<br>
				Хэш: {$hash_password}"
			);
		} 
	}

}
?>

