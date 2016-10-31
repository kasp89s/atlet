<?php
/**
 * Библиотека для табличного вывода данных
 *
 * @author Юрий Малинов aka Dok
 */
class Tablemaker{
	public $name;

	public $orderby = array();
	public $ext_order = array();
	public $arrow_asc = '/i/arrow_up.gif';
	public $arrow_desc = '/i/arrow_down.gif';
	public $sort_filter = array();
	public $simple_img = false;
	public $sort_context = 'sort';

	public $paged = true;
	public $page_sizes = array(20, 50, 100);
	public $current_page = false;
	public $default_page_size = 20;
	public $pager_template = '_footer';
	public $pager_window = 3;
	public $pager_context = 'footer';

	public $template_class = 'View';

	public $pager_info = Array();

	/**
	 * В этот массив можно заполнить callback функции, например,
	 * $tm->custom_order_by['my_date'] = 'my_order_by_date';
	 * function my_order_by_date(cls_base2 $table, $field = 'my_date', $order = 'asc'){
	 *   $table->order_by = "my asc, date $order";
	 * }
	 *
	 * @var unknown_type
	 */
	public $custom_order_by = array();

	/**
	 * Отсутствует проверка диапозона в страницах, это позволяет вызывать сначала
	 * select(), потом get_count(). Может быть полезно в некоторых случаях.
	 *
	 * @var unknown_type
	 */
	public $page_trusting = true;

	public $row_count = 0;

	public $data = array();

	public $row_context = 'rows';

	public $session = FALSE;

	//Искать ли файлы
	public $find_files = FALSE;
	public $table_of_files = FALSE;

	/**
	* @var cls_base2
	**/
	public $table;


	function __construct(&$table, $name = ''){
		$this->name = $name;
		$this->table = &$table->db;
		$this->table->push();
		$this->sort_filter = array($this->name.'_p');
	}


	/**
	 * Определние подтягивания файлов к записям
	 *
	 */
	function files($table){
		$this->find_files = TRUE;
		$this->table_of_files = $table;
	}


	/**
	 * Формирует массив order, пригдный для употребления в cls_base2::order_by
	 *
	 * @return array
	 */
	function get_order(){
		if($this->session){
			$sort_by = (int)$this->get_sgpc($this->name.'_ss');
			$sort_type = $this->get_sgpc($this->name.'_st');
		}
		else{
			$sort_by = isset($_GET[$this->name.'_ss']) ? (int)$_GET[$this->name.'_ss'] : 0;
			$sort_type = isset($_GET[$this->name.'_st']) ? $_GET[$this->name.'_st'] : null;
		}

		if($sort_by >= count($this->orderby))
			$sort_by = 0;

		$order = array();
		$i = 0;
		foreach ($this->orderby as $k => $v) {
			if($i == $sort_by){
				if(isset($sort_type)){
					if(is_int($k))
						$order[$v] = $sort_type?'asc':'desc';
					else
						$order[$k] = $sort_type?'asc':'desc';
				}
				else{
					if(is_int($k))
						$order[$v] = 'asc';
					else
						$order[$k] = $v == 'asc'?'asc':'desc';
				}
				break;
			}
			$i++;
		}

		foreach ($this->ext_order as $k => $v) {
			if(is_int($k)){
				if(empty($order[$v]))
					$order[$v] = 'asc';
			}
			else{
				if(empty($order[$k]))
					$order[$k] = $v == 'asc'?'asc':'desc';
			}
		}
		return $order;
	}

	/**
	 * Формирует массив для употребления в шаблоне, содержащий сортировки
	 *
	 * @param array $order_by результат {@link cls_table_maker2::get_order} или false
	 * @return array
	 */
	function sort($order_by = false){
		if(!$order_by)
			$order_by = $this->get_order();

		reset($order_by);
		$s_field = key($order_by);
		$s_type = current($order_by) == 'asc'?1:0;

		$sort = array();
		$i = 0;

		$qs = $this->get_qs(array(), array_merge($this->sort_filter, array($this->name.'_ss', $this->name.'_st')), NULL, TRUE);

		foreach ($this->orderby as $k => $v) {
			if(is_int($k)){
				$field = $v;
				$type = 1;
			}
			else{
				$field = $k;
				$type = $v == 'asc'?1:0;
			}

			$t_field = str_replace(array('@', '.', '#'), '_', $field);

			if($field == $s_field){
				$sort[$t_field.'_href'] = $qs.$this->name.'_ss='.$i.'&'.$this->name.'_st='.(1-$s_type);
				$sort[$t_field.'_status'] = 'active';

				$img_src = $s_type?$this->arrow_asc:$this->arrow_desc;
				if($this->simple_img)
					$sort[$t_field.'_img'] = "<img src=\"$img_src\" align=\"absmiddle\" border=0 />";
				else
					$sort[$t_field.'_img'] = array('href' => $img_src);
			}
			else{
				$sort[$t_field.'_href'] = $qs.$this->name.'_ss='.$i.'&'.$this->name.'_st='.$type;
				$sort[$t_field.'_status'] = 'passive';
			}

			$i++;
		}
		return $sort;
	}

	function get_page_information($check_range = true, $cnt = 0){
		if($this->session){
			$page = (int)$this->get_sgpc($this->name.'_p');
			$page_size = (int)$this->get_sgpc($this->name.'_ps');
		}
		else{
			if($this->current_page!==false){
				$page = $this->current_page-1;
			}else{
				$page = isset($_GET[$this->name.'_p']) ? (int)$_GET[$this->name.'_p'] : 0;
			}

			$page_size = isset($_GET[$this->name.'_ps']) ? (int)$_GET[$this->name.'_ps'] : 0;
		}

		if($page_size <= 0)
			$page_size = $this->default_page_size;


		if($check_range){
			$pages = ceil((float)$cnt / $page_size);

			if($page < 0)
				$page = 0;
			if($page >= $pages and $pages)
				$page = $pages-1;
		}
		else
			$pages = null;

		if($this->session){
			$this->set_sgpc($this->name.'_p', $page);
			$this->set_sgpc($this->name.'_ps', $page_size);
		}
		return array($page, $page_size, $pages);
	}

	function get_pager($count, $page, $page_size, $parse = false){
		$this->row_count = $count;

		$pager = array();
		$out = array();

		$pages = ceil((float)$count / $page_size);


		if($pages < $this->pager_window*2 + 3){
            for($i = 0; $i < $pages; $i++)
				$out[] = $i;
		}
        elseif($page <= $this->pager_window+1){
            for($i = 0; $i <= $this->pager_window * 2 + 1; $i++)
				$out[] = $i;

			$out[] = false;
			$out[] = $pages - 1;
		}
		elseif($page >= $pages - $this->pager_window - 2){
		 	$out[] = 0;
            $out[] = false;

            for($i = $pages - $this->pager_window * 2 - 2; $i < $pages; $i++)
				$out[] = $i;
		}
		else{
		 	$out[] = 0;
            $out[] = false;

            for($i = $page - $this->pager_window; $i <= $page + $this->pager_window; $i++)
				$out[] = $i;

			$out[] = false;
			$out[] = $pages-1;
		}
		$pager['pages'] = $out;
		$pager['total_pages'] = $pages;
		$pager['page'] = $page;
		$pager['row_count'] = $count;
		$pager['page_qs'] = $this->get_qs(array(), array($this->name.'_p'), NULL, TRUE).$this->name.'_p=';

		$qs = $this->get_qs(array(), array($this->name.'_p', $this->name.'_ps'), NULL, TRUE);
		$page_sizes = array();
		foreach ($this->page_sizes as $title => $ps) {
			if(is_int($title))
				$title = $ps;

			$page_sizes[] = array('title' => $title, 'qs' => $qs.$this->name.'_ps='.$ps.'&'.$this->name.'_p='.(int)($page*$page_size/$ps), 'page_size' => $ps);
		}

		$pager['page_size'] = $page_size;
		$pager['page_sizes'] = $page_sizes;


		$this->pager_info = $pager;


		if($this->pager_template && $parse){
			$template_class = $this->template_class;
			$templ = new $template_class($this->pager_template);

			$templ->main = $pager;
			$templ->data = $pager;

            return $templ->render(FALSE);
		}
		else{
			return $pager;
		}
	}

	function format(){
		$query = $this->table->get();
		return $query;
	}

	function show(){
		$this->table->restore_settings();

		if($this->orderby !== false){
			$order_by = $this->get_order();
			if(count($order_by) == 1 && isset($this->custom_order_by[key($order_by)])){
				call_user_func_array($this->custom_order_by[key($order_by)], array(&$this->table, key($order_by), current($order_by)));
			}
			elseif(count($order_by) == 1){
				$this->table->order_by(key($order_by), current($order_by));
			}


			$this->data[$this->sort_context] = $this->sort($order_by);
		}

		if($this->page_trusting){
			if($this->paged){
				list($page, $page_size) = $this->get_page_information(false);
				$this->table->limit($page_size);
				$this->table->offset($page*$page_size);
				$query = $this->format();
				$this->data[$this->row_context.'_offset'] = $page*$page_size;
				$this->data[$this->row_context] = $query->rows();

				$this->table->restore_settings();
				$this->table->reset_fields();
				$count = $this->table->count_records();
				$this->data[$this->pager_context] = $this->get_pager($count, $page, $page_size, $this->pager_template !== false);
			}
			else{
				$this->data[$this->row_context] = $this->format()->rows();
			}
		}
		else{
			if($this->paged){
				$this->table->reset_fields();
				$count = $this->table->count_records();
				list($page, $page_size) = $this->get_page_information(true, $count);
				$this->table->limit($page_size);
				$this->table->offset($page*$page_size);
				$this->data[$this->pager_context] = $this->get_pager($count, $page, $page_size, $this->pager_template !== false);
			}

			$this->table->restore_settings();
			$this->data[$this->row_context.'_offset'] = $page*$page_size;
			$this->data[$this->row_context] = $this->format()->rows();
		}

		if($this->find_files){
			$ids = array();
			foreach ($this->data[$this->row_context] as $row){
				$ids[] = $row['id'];
			}

			$files = DBFile::select_all($this->table_of_files, $ids);
			foreach ($this->data[$this->row_context] as $k=>$row){
				if(!empty($files[$row['id']]))
					$this->data[$this->row_context][$k]['files'] = $files[$row['id']];
			}

		}

		return $this->data;
	}


	function get_qs($add = array(), $remove = array(), $base_a = NULL, $follow = false){
		if(is_null($base_a)){
			$base_a = $_GET;
		}

		if($remove === 'all')
			$temp = $add;
		else{

			if(!empty($GLOBALS['REWRITE_VARS']))
				$remove = array_merge($remove, array_keys($GLOBALS['REWRITE_VARS']));

			$temp = $add + $base_a;

			foreach($remove as $_){
				unset($temp[$_]);
			}
		}

		$f = 1;
		$res = '';
		foreach($temp as $k => $v){
			if(is_array($v) || strlen($v)){
				if(is_array($v)){
					foreach($v as $_){
						if($f) $f = 0;  else $res .= '&';
						$res .= $k.'[]='.urlencode($_);
					}
				}
				else{
					if($f) $f = 0; else $res .= '&';
					$res .= "$k=".urlencode($v);
				}
			}
		}

		if(!$follow){
			if(strlen($res))
				return '?'.$res;
			else
				return '';
		}
		else{
			if(strlen($res))
				return '?'.$res.'&';
			else
				return '?';
		}
	}

	function get_sgpc($name, $prefix = '', $default = NULL){
		$value = $default;
		$page_name = URI::controller_path(FALSE) .'/'. URI::method(FALSE);

		$sess = Session::instance()->get($page_name.$name);
		if(isset($sess))
			$value = $sess;
		if(isset($_REQUEST[$name]))
			$value = $_REQUEST[$name];

		Session::instance()->set($page_name.$name, $value);
		return $value;
	}


	function set_sgpc($name, $value, $prefix = ''){
		$page_name = URI::controller_path(FALSE) .'/'. URI::method(FALSE);
		Session::instance()->set($page_name.$prefix.$name, $value);
	}

}


?>