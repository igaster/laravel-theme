<?php namespace igaster\laravelTheme;

class Asset extends Tree\Item {
    public $name;
    public $alias;
    
    private $picked = false;

    public function __construct($name, $alias = ''){
        $this->name = $name;
        $this->alias = $alias ? $alias : $name;
    }

    public function dependencies(){
        if($this->picked) return [];
        $this->picked = true;
        $data = [$this];
        foreach ($this->parents as $parent)
            $data = array_merge($data, Assets::find($parent)->dependencies());
        return $data;
    }

}


/*

class Asset {
    public $name;
    public $alias;
    public $depends;

    private $picked = false;

    public function __construct($name, $alias = null, $depends = null){
        $this->name = $name;
        $this->alias = $alias;

        if (!is_array($depends))
            $depends = [$depends];

        $this->depends = $depends;
    }

    public function dependencies(){
        if($this->picked) return [];
        $this->picked = true;
        $data = [$this];
        foreach ($this->depends as $parent)
            $data = array_merge($data, Assets::findAlias($parent)->dependencies());
        return $data;
    }

}