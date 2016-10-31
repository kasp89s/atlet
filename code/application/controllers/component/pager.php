<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_Pager_Controller extends Component_Controller {

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		$this->_assign();
	}


	private function _assign(){
		$this->template = new View('component/pager');

		$this->data["query_string"]="";
		$arrGet=$this->input->get();
		foreach($arrGet as $k => $v ){
			if(strlen($this->data["query_string"])>0)$this->data["query_string"] .="&";
			$this->data["query_string"].=$k . "=" .$v;
		}

		if(utf8::strlen($this->data["query_string"])>0){
			$this->data["query_string"]= "?" . $this->data["query_string"];
		}

        $this->template->data = $this->data;
	}




}
?>