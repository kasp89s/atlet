<?php
/**
 * Модель данных. Дерево групп организаций
 *
 */
class CatalogGroups_Model extends MPTT {

	public $table_name = 'catalog_groups';

	public function info_content(){
		$this->db
			->left_join('catalog_group_contents', 'catalog_group_contents.group_id', 'self.id')
			->select(Array('catalog_group_contents_id'=>'catalog_group_contents.id' , 'catalog_group_contents.*'));
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

		$tbl_content = new CatalogGroupContents_Model();
		foreach ($descendants as $descendant)
			$tbl_content->delete(array('group_id' => $descendant['id']));

		return parent::delete_node($node);
	}

	public function getSubLevelsCount($intGroupId)
	{
	    $arrSubGroupsCount=$this->db
		        ->select("count(distinct ml_self.level) as cnt")
		        ->from($this->_tablename)
		        ->from(str_replace("self","parent",$this->_tablename))
		        ->where("self.lft > ml_parent.lft")
		        ->where("self.rgt < ml_parent.rgt")
		        ->where("parent.id",$intGroupId)
		        ->get()
		        ->row();

		return(intval($arrSubGroupsCount['cnt']));
	}
}