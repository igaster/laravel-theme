<?php namespace igaster\laravelTheme;

class Assets {
    private static $items = [];

    public static function add($name, $alias = null, $depends = []){
    	return self::$items[] = new Asset($name, $alias, $depends);
    }

  //   public static function find($name){
  //   	foreach (self::$items as $asset) {
  //   		if ($asset->name === $name)
  //   			return $asset;
  //   	}
		// throw new \Exception("Asset not found : $name");
  //   }

  //   public static function dependencies($alias){
  //   	return self::find($alias)->dependencies();
  //   }

    public static function findAlias($alias){
    	foreach (self::$items as $asset) {
    		if ($asset->alias === $alias)
    			return $asset;
    	}
		throw new \Exception("Asset not found : $alias");
    }
}