<?php namespace igaster\laravelTheme\Assets;

use igaster\laravelTheme\Themes;

abstract class abastractAsset extends \igaster\laravelTheme\Tree\Item {
    public $name;
    public $alias;
    
    private $picked = false;

    public function __construct($name, $alias = ''){
        $this->name = $name;
        $this->alias = $alias ? $alias : $name;
    }

    public function getParent(){
        if (!empty($this->parents))
            return $this->parents[0];
        else
            return null;
    }

    public function dependencies(){
        $asset = $this;
        $data = [];
        do {
            array_unshift($data, $asset);
        } while ($asset = $asset->getParent());

        return $data;
    }

    public function write($onlyOnce = true){
        $result = '';
        foreach ($this->dependencies() as $asset) {
            if(!$asset->picked)
                $result .= $asset->toStr();
            $asset->picked = $asset->picked || $onlyOnce;
        }
        return $result;
    }

    public function url(){
        return Themes::url($this->name);
    }

    public abstract function toStr();
}