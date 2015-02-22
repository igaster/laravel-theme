<?php namespace igaster\laravelTheme\Assets;

class css extends abastractAsset {

    public function toStr(){
		return '<link media="all" type="text/css" rel="stylesheet" href="'.$this->url().'">'."\n";
    }

}