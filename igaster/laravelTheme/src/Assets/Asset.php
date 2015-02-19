<?php namespace igaster\laravelTheme\Assets;

class Asset extends abastractAsset {
 
    public function toStr(){
        return $this->url()."\r\n";
    }
}