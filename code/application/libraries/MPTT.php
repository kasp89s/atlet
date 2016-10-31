<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Класс дерева. MPTT библиотека
 *
 * @modified by Antuan
 */
class MPTT_Core extends Model {
	/**
	 * @access public
	 * @var string left column name.
	 */
	public $left_column = 'lft';

	/**
	 * @access public
	 * @var string right column name.
	 */
	public $right_column = 'rgt';

	/**
	 * @access public
	 * @var string level column name.
	 */
	public $level_column = 'level';

	/**
	 * @access public
	 * @var string scope column name.
	 **/
	public $scope_column = 'scope';

	/**
	 * @access public
	 * @var string id column name.
	 **/
	public $primary_key = 'id';


	/**
	 * New scope
	 * This also double as a new_root method allowing us to store multiple trees in the same table.
	 * Создать новое дерево.
	 *
	 * @param integer $scope New scope to create.
	 * @return boolean
	 **/
	public function new_scope($scope, array $additional_fields = array()) {
		// Make sure the specified scope doesn't already exist.
		$search_count = $this->db
			->select()
			->from($this->table_name)
			->where($this->scope_column, $scope)
			->count_records();

		if ($search_count > 0 )
			return FALSE;

		// Create a new root node in the new scope.
		$fill = array();
		$fill[$this->left_column]   = 1;
		$fill[$this->right_column]  = 2;
		$fill[$this->level_column]  = 0;
		$fill[$this->scope_column]  = $scope;

		// Other fields may be required.
		if (!empty($additional_fields)) {
			foreach ($additional_fields as $column => $value) {
				$fill[$column] = $value;
			}
		}

		return $this->insert($fill);
	}

	/**
	 * Does the current node have children?
	 * Есть ли дети у данного узла $node
	 *
	 * @access public
	 * @param $node array данный узел
	 * @return bool
	 */
	public function has_children($node) {
		return (($node[$this->right_column] - $node[$this->left_column]) > 1);
	}

	/**
	 * Is the current node a leaf node?
	 * Является ли данный узел $node конечным узлом (листом)
	 *
	 * @access public
	 * @param $node array данный узел
	 * @return bool
	 */
	public function is_leaf($node) {
		return ! $this->has_children($node);
	}

	/**
	 * Is the current node a descendant of the supplied node.
	 * Является ли данный узел $node потомком узлу $target
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param $target array предоставленный узел
	 * @return bool
	 */
	public function is_descendant($node, $target){
		return ($node[$this->left_column] > $target[$this->left_column] AND $node[$this->right_column] < $target[$this->right_column] AND $node[$this->scope_column] = $target[$this->scope_column]);
	}

	/**
	 * Is the current node a direct child of the supplied node?
	 * Является ли данный узел $node ребенком узлу $target
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param $target array предоставленный узел
	 * @return bool
	 */
	public function is_child($node, $target) {
		$parent = $this->parent($node);
		return ($parent[$this->primary_key] == $target[$this->primary_key]);
	}

	/**
	 * Is the current node the direct parent of the supplied node?
	 * Является ли данный узел $node родителем узлу $target
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param $target array предоставленный узел
	 * @return bool
	 */
	public function is_parent($node, $target) {
		$parent = $this->parent($target);
		return ($parent[$this->primary_key] == $node[$this->primary_key]);
	}

	/**
	 * Is the current node a sibling of the supplied node
	 * Является ли данный узел $node братом узлу $target
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param $target array предоставленный узел
	 * @return bool
	 */
	public function is_sibling($node, $target) {
		if ($node[$this->primary_key] == $target[$this->primary_key])
			return FALSE;

		$parent_node = $this->parent($node);
		$parent_target = $this->parent($target);
		return ($parent_node[$this->primary_key] == $parent_target[$this->primary_key]);
	}

	/**
	 * Is the current node a root node?
	 * Является ли данный узел $node корнем
	 *
	 * @access public
	 * @param $node array данный узел
	 * @return bool
	 */
	public function is_root($node) {
		return ($node[$this->left_column] == 1);
	}

	/**
	 * Returns the root node.
	 * Метод возвращает корень
	 *
	 * @access protected
	 * @return MPTT
	 */
	public function root($scope = FALSE) {
		if(!$scope)
			return FALSE;

		$query = $this->db
			->select()
			->from($this->table_name)
			->where(array(
				$this->left_column => 1,
				$this->scope_column => $scope
			))
			->get();

		return $query->row();
	}

	/**
	 * Метод возвращает узел с заданным id
	 *
	 * @access protected
	 * @return MPTT
	 */
	public function get_node($id = false) {
		if(!$id)
			return FALSE;

		$query = $this->db
			->select()
			->from($this->table_name)
			->where(array(
				$this->primary_key => $id
			))
			->get();

		return $query->row();
	}

	/**
	 * Returns the parent of the current node.
	 * Метод возвращает родителя данного узла $node
	 *
	 * @access public
	 * @param $node array данный узел
	 * @return MPTT
	 */
	public function parent($node) {
		$query = $this->db
			->select()
			->from($this->table_name)
			->where($this->left_column,  '<=', $node[$this->left_column])
			->where($this->right_column, '>=', $node[$this->right_column])
			->where($this->primary_key,  '<>', $node[$this->primary_key])
			->where($this->scope_column, $node[$this->scope_column])
			->where($this->level_column, $node[$this->level_column] - 1)
			->get();

		return $query->row();
	}

	/**
	 * Returns the parents of the current node.
	 * Метод возвращает родителей (предков) данного узла $node
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param bool $root include the root node? (Включать ли корень)
	 * @param string $direction direction to order the left column by.
	 * @return MPTT
	 */
	public function parents($node, $root = TRUE, $direction = 'ASC') {
		$query = $this->db
			->select()
			->from($this->table_name)
			->where($this->left_column,  '<=', $node[$this->left_column])
			->where($this->right_column, '>=', $node[$this->right_column])
			->where($this->primary_key,  '<>', $node[$this->primary_key])
			->where($this->scope_column, $node[$this->scope_column])
			->order_by($this->left_column, $direction);

		if (!$root) {
			$query->where($this->left_column, '!=', 1);
		}

		return $query->get()->rows();
	}

	/**
	 * Returns the children of the current node.
	 * Метод возвращает детей данного узла $node
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param bool $self include the current loaded node? (включать ли самого родителя $node)
	 * @param string $direction direction to order the left column by.
	 * @return MPTT
	 */
	public function children($node, $self = FALSE, $direction = 'ASC') {
		$levels         = $self ? array($node[$this->level_column] + 1, $node[$this->level_column]) : array($node[$this->level_column] + 1);
		$left_operator  = $self ? '>=' : '>';
		$right_operator = $self ? '<=' : '<';

		$query = $this->db
			->select()
			->from($this->table_name)
			->where($this->left_column,  $left_operator, $node[$this->left_column])
			->where($this->right_column, $right_operator, $node[$this->right_column])
			->where($this->scope_column, $node[$this->scope_column])
			->where($this->level_column, 'in', $levels)
			->order_by($this->left_column, $direction)
			->get();

		return $query->rows();
	}

	/**
	 * Returns the descendants of the current node.
	 * Метод возвращает потомков данного узла $node
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param bool $self include the current loaded node? (включать ли самого родителя $node)
	 * @param string $direction direction to order the left column by.
	 * @return MPTT
	 */
	public function descendants($node, $self = FALSE, $direction = 'ASC') {
		$left_operator  = $self ? '>=' : '>';
		$right_operator = $self ? '<=' : '<';

		$query = $this->db
			->select()
			->from($this->table_name)
			->where($this->left_column,  $left_operator, $node[$this->left_column])
			->where($this->right_column, $right_operator, $node[$this->right_column])
			->where($this->scope_column, $node[$this->scope_column])
			->order_by($this->left_column, $direction)
			->get();

		return $query->rows();
	}

	/**
	 * Returns the siblings of the current node
	 * Метод возвращает братьев данного узла $node
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param bool $self include the current loaded node? (включать ли самого брата $node)
	 * @param string $direction direction to order the left column by.
	 * @return MPTT
	 */
	public function siblings($node, $self = FALSE, $direction = 'ASC') {
		$parent = $this->parent($node);

		$query = $this->db
			->select()
			->from($this->table_name)
			->where($this->left_column, '>', $parent[$this->left_column])
			->where($this->right_column, '<', $parent[$this->right_column])
			->where($this->scope_column, $node[$this->scope_column])
			->where($this->level_column, $node[$this->level_column])
			->order_by($this->left_column, $direction);


		if (!$self) {
			$query->where($this->primary_key, '<>', $node[$this->primary_key]);
		}

		return $query->get()->rows();
	}

	/**
	 * Returns leaves under the current node.
	 * Метод возвращает листья от данного узла $node
	 *
	 * @access public
	 * @param $node array данный узел
	 * @return MPTT
	 */
	public function leaves($node) {
		return $this->db
			->select()
			->from($this->table_name)
			->where($this->left_column, db::expr('( '. $this->right_column. '- 1 )'))
			->where($this->left_column, '>=', $node[$this->left_column])
			->where($this->right_column, '<=', $node[$this->right_column])
			->where($this->scope_column, $node[$this->scope_column])
			->order_by($this->left_column, 'ASC')
			->get()
			->rows();
	}

	/**
	 * Get Size
	 * Метод возвращает размерность данного узла $node
	 *
	 * @access protected
	 * @param $node array данный узел
	 * @return integer
	 */
	protected function get_size($node) {
		return ($node[$this->right_column] - $node[$this->left_column]) + 1;
	}

	/**
	 * Create a gap in the tree to make room for a new node
	 * Метод создает разрыв в дереве, чтобы освободить место для нового узла $node
	 *
	 * @access private
	 * @param $node array данный узел
	 * @param integer $start start position.
	 * @param integer $size the size of the gap (default is 2).
	 */
	private function create_space($node, $start, $size = 2) {
		// Update the left values, then the right.
		db::query('UPDATE '.$this->table_prefix.$this->get_table_name().' SET `'.$this->left_column.'` = `'.$this->left_column.'` + '.$size.' WHERE `'.$this->left_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$node[$this->scope_column]);
		db::query('UPDATE '.$this->table_prefix.$this->get_table_name().' SET `'.$this->right_column.'` = `'.$this->right_column.'` + '.$size.' WHERE `'.$this->right_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$node[$this->scope_column]);
	}

	/**
	 * Closes a gap in a tree. Mainly used after a node has been removed.
	 * Метод удаляет пробел в дереве. В основном используется после удаления узла.
	 *
	 * @access private
	 * @param $node array данный узел
	 * @param integer $start start position.
	 * @param integer $size the size of the gap (default is 2).
	 */
	private function delete_space($node, $start, $size = 2) {
		// Update the left values, then the right.
		db::query('UPDATE '.$this->table_prefix.$this->get_table_name().' SET `'.$this->left_column.'` = `'.$this->left_column.'` - '.$size.' WHERE `'.$this->left_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$node[$this->scope_column]);
		db::query('UPDATE '.$this->table_prefix.$this->get_table_name().' SET `'.$this->right_column.'` = `'.$this->right_column.'` - '.$size.' WHERE `'.$this->right_column.'` >= '.$start.' AND `'.$this->scope_column.'` = '.$node[$this->scope_column]);
	}

	/**
	 * Добавление узла $node в дерево
	 *
	 * @param $node array узел, который добавляем
	 */
	protected function insert_node($node, $copy_left_from, $left_offset, $level_offset) {
		$fill = array();
		$fill[$this->left_column]  = $node[$copy_left_from] + $left_offset;
		$fill[$this->right_column] = $fill[$this->left_column] + 1;
		$fill[$this->level_column] = $node[$this->level_column] + $level_offset;
		$fill[$this->scope_column] = $node[$this->scope_column];

		$this->create_space($fill, $fill[$this->left_column]);
		$result = $this->insert($fill);

		return $result;
	}

	/**
	 * Inserts a new node to the left of the target node.
	 * Метод вставляет новый узел в качестве первого ребенка узла $node.
	 *
	 * @access public
	 * @param MPTT|integer $node target node id or MPTT object.
	 * @return MPTT
	 */
	public function insert_as_first_child($node) {
		return $this->insert_node($node, $this->left_column, 1, 1);
	}

	/**
	 * Inserts a new node to the right of the target node.
	 * Метод вставляет новый узел в качестве последнего ребенка узла $node.
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param MPTT|integer $target target node id or MPTT object.
	 * @return MPTT
	 */
	public function insert_as_last_child($node) {
		return $this->insert_node($node, $this->right_column, 0, 1);
	}

	/**
	 * Inserts a new node as a previous sibling of the target node.
	 * Метод вставляет новый узел в качестве левого брата узла $node.
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param MPTT|integer $target target node id or MPTT object.
	 * @return MPTT
	 */
	public function insert_as_prev_sibling($node) {
		return $this->insert_node($node, $this->left_column, 0, 0);
	}

	/**
	 * Inserts a new node as the next sibling of the target node.
	 * Метод вставляет новый узел в качестве правого брата узла $node.
	 *
	 * @access public
	 * @param $node array данный узел
	 * @param MPTT|integer $target target node id or MPTT object.
	 * @return MPTT
	 */
	public function insert_as_next_sibling($node) {
		return $this->insert_node($node, $this->right_column, 1, 0);
	}

	/**
	 * Removes a node and it's descendants.
	 * Метод удаляет узел $node и его потомков.
	 *
	 * $usless_param prevents a strict error that breaks PHPUnit like hell!
	 * @access public
	 * @param $node array данный узел
	 * @param bool $descendants remove the descendants?
	 */
	public function delete_node($node, $usless_param = NULL) {
		$this->delete(db::expr('`'.$this->left_column.'` BETWEEN '.$node[$this->left_column].' AND '.$node[$this->right_column].' AND `'.$this->scope_column.'` = '.$node[$this->scope_column]));
		$this->delete_space($node, $node[$this->left_column], $this->get_size($node));
	}

	/**
	 * Move to First Child
	 *
	 * Moves the current node to the first child of the target node.
	 * Метод перемещает данный узел $node, делает его первым ребенком узла $target
	 *
	 * @param $node array данный узел
	 * @param MPTT|integer $target target node id or MPTT object.
	 * @return MPTT
	 */
	public function move_to_first_child($node, $target) {
		// Stop $this being moved into a descendant
		if ($this->is_descendant($target, $node)) {
			return FALSE;
		}

		$new_left = $target[$this->left_column] + 1;
		$level_offset = $target[$this->level_column] - $node[$this->level_column] + 1;
		$result = $this->move($node, $new_left, $level_offset, $target[$this->scope_column]);

		return $result;
	}

	/**
	 * Move to Last Child
	 *
	 * Moves the current node to the last child of the target node.
	 * Метод перемещает данный узел $node, делает его последним ребенком узла $target
	 *
	 * @param $node array данный узел
	 * @param MPTT|integer $target target node id or MPTT object.
	 * @return MPTT
	 */
	public function move_to_last_child($node, $target) {
		// Stop $this being moved into a descendant
		if ($this->is_descendant($target, $node)) {
			return FALSE;
		}

		$new_left = $target[$this->right_column];
		$level_offset = $target[$this->level_column] - $node[$this->level_column] + 1;
		$result = $this->move($node, $new_left, $level_offset, $target[$this->scope_column]);

		return $result;
	}

	/**
	 * Move to Previous Sibling.
	 *
	 * Moves the current node to the previous sibling of the target node.
	 * Метод перемещает данный узел $node, делает его левым братом узла $target
	 *
	 *
	 * @param $node array данный узел
	 * @param MPTT|integer $target target node id or MPTT object.
	 * @return MPTT
	 */
	public function move_to_prev_sibling($node, $target) {
		// Stop $this being moved into a descendant
		if ($this->is_descendant($target, $node)) {
			return FALSE;
		}

		$new_left = $target[$this->left_column];
		$level_offset = $target[$this->level_column] - $node[$this->level_column];
		$result = $this->move($node, $new_left, $level_offset, $target[$this->scope_column]);

		return $result;
	}

	/**
	 * Move to Next Sibling.
	 *
	 * Moves the current node to the next sibling of the target node.
	 * Метод перемещает данный узел $node, делает его правым братом узла $target
	 *
	 * @param $node array данный узел
	 * @param MPTT|integer $target target node id or MPTT object.
	 * @return MPTT
	 */
	public function move_to_next_sibling($node, $target) {
		if ($this->is_descendant($target, $node)) {
			return FALSE;
		}

		$new_left = $target[$this->right_column] + 1;
		$level_offset = $target[$this->level_column] - $node[$this->level_column];
		$result = $this->move($node, $new_left, $level_offset, $target[$this->scope_column]);

		return $result;
	}

	/**
	 * Move
	 *
	 * @param $node array данный узел
	 * @param integer $new_left left value for the new node position.
	 * @param integer $level_offset
	 */
	protected function move($node, $new_left, $level_offset, $new_scope) {
		$size = $this->get_size($node);

		$this->create_space($node, $new_left, $size);

		$node = $this->get_node($node['id']);

		$offset = ($new_left - $node[$this->left_column]);

		// Update the values.
		$result = db::query('UPDATE '.$this->table_prefix.$this->get_table_name().' SET `'.$this->left_column.'` = `'.$this->left_column.'` + '.$offset.', `'.$this->right_column.'` = `'.$this->right_column.'` + '.$offset.'
		, `'.$this->level_column.'` = `'.$this->level_column.'` + '.$level_offset.'
		, `'.$this->scope_column.'` = '.$new_scope.'
		WHERE `'.$this->left_column.'` >= '.$node[$this->left_column].' AND `'.$this->right_column.'` <= '.$node[$this->right_column].' AND `'.$this->scope_column.'` = '.$node[$this->scope_column]);

		$this->delete_space($node, $node[$this->left_column], $size);

		return $result;
	}


	/**
	 * Verify the tree is in good order
	 *
	 * This functions speed is irrelevant - its really only for debugging and unit tests
	 * Проверка дерева на отсутствие ошибок, коллизий
	 *
	 * @todo Look for any nodes no longer contained by the root node.
	 * @todo Ensure every node has a path to the root via ->parents();
	 * @access public
	 * @return boolean
	 */
	public function verify_tree() {
		foreach ($this->get_scopes() as $scope) {
			if (!$this->verify_scope($scope['scope']))
				return FALSE;
		}
		return TRUE;
	}

	private function get_scopes() {
		return db::query('SELECT DISTINCT(`'.$this->scope_column.'`) from '.$this->table_prefix.$this->get_table_name().'')->rows();
	}


	public function verify_scope($scope) {
		$root = $this->root($scope);

		$end = $root[$this->right_column];

		// Find nodes that have slipped out of bounds.
		$result = db::query('SELECT count(*) as count FROM '.$this->table_prefix.$this->get_table_name().' WHERE `'.$this->scope_column.'` = '.$root['scope'].' AND (`'.$this->left_column.'` > '.$end.' OR `'.$this->right_column.'` > '.$end.')')->row();
		if ($result['count'] > 0)
			return FALSE;

		// Find nodes that have the same left and right value
		$result = db::query('SELECT count(*) as count FROM '.$this->table_prefix.$this->get_table_name().' WHERE `'.$this->scope_column.'` = '.$root['scope'].' AND `'.$this->left_column.'` = `'.$this->right_column.'`')->row();
		if ($result['count'] > 0)
			return FALSE;

		// Find nodes that right value is less than the left value
		$result = db::query('SELECT count(*) as count FROM '.$this->table_prefix.$this->get_table_name().' WHERE `'.$this->scope_column.'` = '.$root['scope'].' AND `'.$this->left_column.'` > `'.$this->right_column.'`')->row();
		if ($result['count'] > 0)
			return FALSE;

		// Make sure no 2 nodes share a left/right value
		$i = 1;
		while ($i <= $end) {
			$result = db::query('SELECT count(*) as count FROM '.$this->table_prefix.$this->get_table_name().' WHERE `'.$this->scope_column.'` = '.$root['scope'].' AND (`'.$this->left_column.'` = '.$i.' OR `'.$this->right_column.'` = '.$i.')')->row();

			if ($result['count'] > 1)
				return FALSE;

			$i++;
		}

		return TRUE;
	}


	/**
	 * Группировка элементов по ID родителя
	 *
	 * @param array $arrElements
	 * @return array
	 */
	public function get_grouped_array($arrElements,$arrActiveItem=Array()){

		$arrPrevious['level']=-1;
		$arrPrevious['id']=0;
		$arrLevelIds=Array('0'=>0);
		$arrResult=Array();

		if(isset($arrActiveItem['lft'])&&isset($arrActiveItem['rgt']))
			$markActive=true;
		else
			$markActive=false;

		foreach($arrElements as $key => $value){

			if($value['level']>$arrPrevious['level']){
                 $arrLevelIds[$value['level']]=$arrPrevious['id'];
                 $arrResult[$arrLevelIds[$value['level']]]['isActive']=false;
                 $arrResult[$arrLevelIds[$value['level']]]['parentGroup']=(isset($arrLevelIds[$value['level']-1])?$arrLevelIds[$value['level']-1]:-1);
                 $arrResult[$arrLevelIds[$value['level']]]['parentIndx']=(isset($arrLevelIds[$value['level']-1])?count($arrResult[$arrLevelIds[$value['level']-1]]['rows'])-1:-1);
			}
			else{
				 for($i=0;$i<($arrPrevious['level']-$value['level']);$i++){
				 	 unset($arrLevelIds[count($arrLevelIds)-1]);
				 }
			}

			if($markActive&&$value['lft']<=$arrActiveItem['lft']&&$value['rgt']>=$arrActiveItem['rgt']){

			     $arrResult[$arrLevelIds[$value['level']]]['isActive']=true;
			     $value['isActive']=true;
			}

			$arrResult[$arrLevelIds[$value['level']]]['rows'][]=$value;

			if(!isset($arrResult[$value['id']])){
				 $arrResult[$value['id']]['active']=$value['active'];
				 $arrResult[$value['id']]['parentGroup']=$arrLevelIds[$value['level']];
				 $arrResult[$value['id']]['parentIndx']=count($arrResult[$arrLevelIds[$value['level']]]['rows'])-1;
			}

			$arrPrevious=$value;
		}

		return($arrResult);

	}

}