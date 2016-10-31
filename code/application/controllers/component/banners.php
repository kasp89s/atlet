<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_Banners_Controller extends Component_Controller {

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		$this->_assign();
	}


	private function _assign(){
		$this->template = new View('component/banners');
        $this->template->data = $this->data;
	}




}
?>