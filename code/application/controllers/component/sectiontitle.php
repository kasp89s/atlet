<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_Sectiontitle_Controller extends Component_Controller {

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		$this->_assign();
	}


	private function _assign(){
		$this->template = new View('component/sectiontitle');
        $this->template->data = $this->data;
	}




}
?>