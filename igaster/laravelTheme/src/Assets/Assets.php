<?php namespace igaster\laravelTheme\Assets;

class Assets {

private static $items = [];

	public static function add(abastractAsset $asset, $depends = ''){
		if ($depends)
			$asset->addParent(self::find($depends));

		return self::$items[] = $asset;
	}

	public static function script($name, $alias = '', $depends = ''){
		self::add(new js($name, $alias), $depends);
	}

	public static function style($name, $alias = '', $depends = ''){
		self::add(new css($name, $alias), $depends);
	}

	public static function file($name, $alias = '', $depends = ''){
		self::add(new Asset($name, $alias), $depends);
	}

	public static function find($alias){

		foreach (self::$items as $asset) 
			if ($asset->alias === $alias)
				return $asset;

		throw new \Exception("Asset not found : $alias");
	}

}