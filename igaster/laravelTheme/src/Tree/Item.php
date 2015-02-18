<?php namespace igaster\laravelTheme\Tree;

class Item {
	public $children = [];
	public $parents  = [];

	// public function __construct($children = []){
	// 	foreach ($children as $child)
	// 		$this->addChild($child);
	// }

	public function addChild(Item $item){
		if (!in_array($item, $this->children)){
			$this->children[] = $item;
			$item->parents[] = $this;
		}
		return $item;
	}

	public function addParent(Item $item){
		$item->addChild($this);
		return $item;
	}

	public function hasChild(Item $item){
		return $this->existsInTree($item, self::DIRECTION_CHILDREN);
	}

	public function hasParent(Item $item){
		return $this->existsInTree($item, self::DIRECTION_PARENTS);
	}

	public function descendants($includeMe = true){
		return $this->flattenTree($includeMe, self::DIRECTION_CHILDREN);
	}

	public function ancenstors($includeMe = true){
		return $this->flattenTree($includeMe, self::DIRECTION_PARENTS);
	}

	public function foreachChild($callback){
		$this->foreachItem($callback, self::DIRECTION_CHILDREN);
	}

	public function foreachParent($callback, $includeMe = true){
		$this->foreachItem($callback, $includeMe, self::DIRECTION_PARENTS);
	}

	public function searchChild($callback, $includeMe = true){
		return $this->search($callback, $includeMe, self::DIRECTION_CHILDREN);
	}

	public function searchParent($callback){
		return $this->search($callback, self::DIRECTION_PARENTS);
	}

	// -----------[ Generic two way functions ]--------------

	const DIRECTION_PARENTS  = 1;
	const DIRECTION_CHILDREN = 2;

	private function relations($direction){
		if ($direction = self::DIRECTION_CHILDREN)
			return $this->children;

		if ($direction = self::DIRECTION_PARENTS)
			return $this->parents;
	}

	private function flattenTree($includeMe, $direction){
		$result = [];

		if ($includeMe) 
			$result[] = $this;

		foreach ($this->relations($direction) as $item) {
			$result = array_merge($result, $item->flattenTree(true, $direction));
		}

		return $result;
	}

	private function foreachItem($callback, $direction){
		array_walk($this->relations($direction), $callback);
	}

	private function search($callback, $includeMe, $direction){
		if ($includeMe && $result = $callback($this))
			return $result;

		foreach($this->relations($direction) as $item)
			if ($result = $item->search($callback, true, $direction))
				return $result;

		return false;
	}

	private function existsInTree(Item $search, $direction){
		if (in_array($search, $this->relations($direction)))
			return true;

		foreach ($this->relations($direction) as $item)
			if ($item->existsInTree($search, $direction))
				return true;

		return false;
	}


}