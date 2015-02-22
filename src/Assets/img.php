<?php namespace igaster\laravelTheme\Assets;

class img extends abastractAsset {

    public function toStr(){
        return '<img src="'.$this->url()."\">\r\n";
    }

}