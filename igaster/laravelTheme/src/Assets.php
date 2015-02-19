<?php namespace igaster\laravelTheme;

class Assets {

private static $items = [];

	public static function add($name, $alias = '', $depends = ''){
		$asset = new Asset($name, $alias);

		if ($depends)
			$asset->addParent(self::find($depends));

		return self::$items[] = $asset;
	}

	public static function find($alias){

		foreach (self::$items as $asset) 
			if ($asset->alias === $alias)
				return $asset;

		throw new \Exception("Asset not found : $alias");
	}

}