<?php
/**
 * Модель данных. Организации
 *
 */
class Catalog_Model extends Model {

	public $table_name = 'catalog';
	public $_vipcats_content= 'catalog_vipcats_content';
	public $_catgroups = 'catalog_groups';
	public $_catgroups_contents = 'catalog_group_contents';


    public function vip_cat_content($intVipCatId){
		$this->db
			->select('self.*')
			->from(Array("ml_vipcontent" => $this->_vipcats_content))
			->left_join($this->table_name, 'vipcontent.product_id', 'self.id')
			->where('vipcontent.cat_id','=',$intVipCatId);
	}

	public function product_in_vip_cat($intProductId,$intActive=FALSE){

		$strExpr=db::expr("if(`ml_self`.`id`>0,'1','0') as `in_group`");

		$arrVipContentFilter=Array(
			'vipcontent.cat_id' => 'groups.id',
			'vipcontent.product_id' => db::expr(strval($intProductId))
		);

		if($intActive!==FALSE){
			$arrVipContentFilter['vipcontent.active']=$intActive;
		}

        $this->db
			->select(Array("group_id" => "groups.id","group_title" => "groups.title"))
			->select($strExpr)
			->from(Array("ml_groups" => $this->_catgroups))
			->left_join(Array('ml_groups_content' => 'catalog_group_contents'), 'groups_content.group_id','groups.id')
			->left_join(Array("ml_vipcontent" => $this->_vipcats_content), $arrVipContentFilter, NULL)
			->left_join($this->table_name,  'self.id', 'vipcontent.product_id')
			->where('groups_content.is_vip', '=', 1);
	}

}