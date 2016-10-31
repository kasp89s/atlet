<?php
/**
 * Модель данных. Дерево страниц в CMS
 * 
 */
class Pages_Model extends MPTT {

	public $table_name = 'pages';
	private $_identifier;
	private $computed_uri = NULL;

	public function info_content(){
		$this->db
			->left_join('page_contents', 'page_contents.page_id', 'self.id')
			->select(array(
				'page_contents.*',
				array('page_contents_id' => 'page_contents.id')
			));
	}
	
	
	/**
	 * Удалить узел дерева с потомками и соответствующий контент
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function delete_node($id = FALSE) {
		if(!$id)
			return false;

		$node = $this->get_node($id);
		$descendants = $this->descendants($node, TRUE);
		
		$tbl_content = new PageContents_Model();
		foreach ($descendants as $descendant)
			$tbl_content->delete(array('page_id' => $descendant['id']));

		return parent::delete_node($node);
	}
	
	
	/**
	 * Поиск
	 *
	 * @param string $str поисковая строка
	 */
    public function search($str){
    	$str = utf8::strtolower($str);
    	
		$query = db::query("
			SELECT 
				contents.id, 
				contents.page_id, 
				contents.uri, 
				contents.title, 
				contents.preview, 
				contents.description
			FROM ({$this->table_prefix}pages AS self) 
			LEFT JOIN {$this->table_prefix}page_contents as contents ON (contents.page_id = self.id) 
			WHERE 
				self.scope = 1
				AND self.type = 'none'
				AND (lower(contents.title) like '%{$str}%' OR lower(contents.preview) like '%{$str}%' OR lower(contents.description) like '%{$str}%') 
			ORDER BY contents.counter DESC");
		
		
		$rows = array();
		if($cms = Cache::instance()->get('cms_plane_tree')){
			foreach ($query->rows() as $v){
				if(array_key_exists($v['page_id'], $cms)){
					$v['uri_base'] = $cms[$v['page_id']]['uri_base'] . '/';
					$rows[] = $v;
				}
			}
		}
		
		return $rows;
	}
}