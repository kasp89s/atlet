<?php defined('SYSPATH') OR die('No direct access allowed.');

class Frame_Controller extends Smartyhandler_Controller {

	public $template;

	function __construct($template_name) {
		parent::__construct();

		$template_name = 'frame/' . $template_name;
		$this->template = new View($template_name);

	}


	function render() {
		try {
			$this->_assign();
		} catch (Exception $e) {
			echo Kohana_Exception::handle($e), "\n";
		}

		$this->template->render(TRUE);
	}


	private function _assign(){
		$components = array();
		smartyvars::$components = (!empty(smartyvars::$components)) ? smartyvars::$components : array();
		smartyvars::$attributes = (!empty(smartyvars::$attributes)) ? smartyvars::$attributes : array();

		/**
		 * Бинд компонентов
		 */
		$components_disabled = array();
		foreach (smartyvars::$components as $v) {
			if($v['action'] == 'del'||$v['action'] == 'replace')
				$components_disabled[] = $v['place'];
		}

		foreach (smartyvars::$components as $v) {
			if($v['action'] == 'add' && !in_array($v['place'], $components_disabled)){
				$components[$v['place']] = array(
					'place'             => $v['place'],
					'tepmlate_name'     => $v['tepmlate_name'],
					'data'              => $v['data']
				);
			}
		}

		foreach (smartyvars::$components as $v) {
			if($v['action'] == 'replace'){
				$components[$v['place']] = array(
					'place'             => $v['place'],
					'tepmlate_name'     => $v['tepmlate_name'],
					'data'              => $v['data']
				);
			}
		}
		foreach ($components as $v){
			$class_component = 'Component_'. ucfirst($v['tepmlate_name']) .'_Controller';
			if(!class_exists($class_component, false)){
				require(APPPATH . 'controllers/component/'. $v['tepmlate_name'] .'.php');
			}

			$component = new $class_component($v['data']);
			$data = '';

			if (!empty($component->cache_data)){
				$data = $component->cache_data;

			} elseif (!empty($component->template)) {
				$data = $component->template->render(FALSE);

				if($component->caching)
					$component->cache($data);
			}

			$this->template->$v['place'] = $data;
		}


		/**
		 * Бинд атрибутов
		 */
// Здесь изменен стандартный вывод мета-информации, ибо выводиться должно то, что должно, без всяких добавлений
/* 		$this->template->title        = (strlen(trim(Kohana::config('cms.page_active.seo_title')))>0?Kohana::config('cms.page_active.seo_title') . '. ':'')
		                                 .
		                                (strlen(trim(Kohana::config('cms.seo.title')))>0?Kohana::config('cms.seo.title') :'');

		$this->template->keywords     = (strlen(trim(Kohana::config('cms.page_active.seo_keywords')))>0?Kohana::config('cms.page_active.seo_keywords') . '. ':'')
		                                 .
		                                (strlen(trim(Kohana::config('cms.seo.keywords')))>0?Kohana::config('cms.seo.keywords') :'');

		$this->template->description  = (strlen(trim(Kohana::config('cms.page_active.seo_description')))>0?Kohana::config('cms.page_active.seo_description') . '. ':'')
		                                 .
		                                (strlen(trim(Kohana::config('cms.seo.description')))>0?Kohana::config('cms.seo.description') :'');*/

		$this->template->title        = strlen(trim(Kohana::config('cms.page_active.seo_title')))>0?Kohana::config('cms.page_active.seo_title'):'';

		$this->template->keywords     = strlen(trim(Kohana::config('cms.page_active.seo_keywords')))>0?Kohana::config('cms.page_active.seo_keywords'):'';

		$this->template->description  = strlen(trim(Kohana::config('cms.page_active.seo_description')))>0?Kohana::config('cms.page_active.seo_description'):'';

		foreach (smartyvars::$attributes as $type => $v){
			$value="";

			switch ($type){
			    case 'title':
                    // Здесь тоже убрали лишнее из мета-информации
					/*for($i=0;$i<count($v);$i++){
						$value .= $v[count($v)-1-$i] . '. ';
					}*/
                    // Этой строки в оригинале не было ------------------
                    if(is_array($v)){
			        	$value .= $v[count($v)-1];
			        }else{
			        	$value .= $v;
			        }
			        $value .= strlen(trim($value))==0?(strlen(trim(Kohana::config('cms.seo.title')))>0?Kohana::config('cms.seo.title') :''):'';
                    // --------------------------------------------------
                    //$value=$value . $this->template->title;
					break;
				case 'keywords':
					/*for($i=0;$i<count($v);$i++){
						$value .= $v[count($v)-1-$i] . '. ';
					}*/
                    // Этой строки в оригинале не было ------------------
			        if(is_array($v)){
			        	$value .= $v[count($v)-1];
			        }else{
			        	$value .= $v;
			        }
			        $value .= strlen(trim($value))==0?(strlen(trim(Kohana::config('cms.seo.keywords')))>0?Kohana::config('cms.seo.keywords') :''):'';
                    // --------------------------------------------------
                    //$value=$value . $this->template->keywords;
					break;
				case 'description':
					/*for($i=0;$i<count($v);$i++){
						$value .= $v[count($v)-1-$i] . '. ';
					}*/
                    // Этой строки в оригинале не было ------------------
			        if(is_array($v)){
			        	$value .= $v[count($v)-1];
			        }else{
			        	$value .= $v;
			        }
			        $value .= strlen(trim($value))==0?(strlen(trim(Kohana::config('cms.seo.description')))>0?Kohana::config('cms.seo.description') :''):'';
                    // --------------------------------------------------
                    //$value=$value . $this->template->description;
					break;

				default:
					$value = $v;
					break;
			}

			$this->template->{$type} = $value;
		}
	}

}
