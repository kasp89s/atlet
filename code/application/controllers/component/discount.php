<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_Discount_Controller extends Component_Controller {

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		$this->_assign();
	}


	private function _assign(){
		$this->template = new View('component/discount');
        $this->template->data = $this->data;
        $this->template->data['uri_base'] = $this->get_uri_base('discount');
	}




}
?>