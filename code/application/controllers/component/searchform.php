<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_Searchform_Controller extends Component_Controller {

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		$this->_assign();
	}


	private function _assign(){
		$this->template = new View('component/searchform');

		$arrGet=$this->input->get();
        $arrData['cost_from']=$this->input->get('cost_from',false);
        $arrData['cost_to']=$this->input->get('cost_to',false);
        $arrData['search_words']=$this->input->get('search_words',false);

        $this->template->data = $arrData;
	}




}
?>