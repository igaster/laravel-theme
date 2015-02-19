<?php namespace igaster\laravelTheme\Assets;

class js extends abastractAsset {

    public function toStr(){
		return '<script src="'.$this->url().'"></script>'."\n";
    }

}