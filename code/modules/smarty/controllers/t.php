<?php defined('SYSPATH') OR die('No direct access allowed.');

class T_Controller extends Smartyhandler_Controller {

	public $template;
	public $frame = 'index';

	public function __construct(){
		parent::__construct();
	}

	public function _after(){
		if(isset($this->template) && is_object($this->template)){

			if(!empty($this->title)) $this->add_attribute('title', $this->title);

			$this->template->uri_base = Kohana::config('cms.page_active.uri_base');

			if($this->frame == 'clear'){
				$this->template->render(TRUE);
			} else {
				$class_frame = 'Frame_'. ucfirst($this->frame) .'_Controller';
				if(!class_exists($class_frame, false)){
					require(APPPATH . 'controllers/frame/'. $this->frame .'.php');
				}

				$frame = new $class_frame();
				if(!empty($this->menu)) $frame->menu = $this->menu;
				if(!empty($this->title)) $frame->title = $this->title;
				$frame->template->content = $this->template->render(FALSE);
				$frame->render();
			}
		}
	}
}
