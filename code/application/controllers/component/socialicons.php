<?php
/**
 * Компонент. Левое меню
 *
 */
class Component_SocialIcons_Controller extends Component_Controller {

	function __construct($data) {
		$this->data = $data;
		parent::__construct();

		$this->_assign();
	}


	private function _assign(){
		$this->template = new View('component/socialicons');
        $this->template->data = $this->data;

        $arrUrl = parse_url($this->input->server('REQUEST_URI'));
        $this->template->current_link = urlencode("http://luxpodarki.ru".$arrUrl['path']);
        $this->template->current_text = urlencode($this->data['text']);
	}




}
?>