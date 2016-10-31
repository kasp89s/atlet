<?php 

class Index_Controller extends Admin_Controller {

	
	public function index() {
		$this->template = new View('admin/index');

	}

}
?>