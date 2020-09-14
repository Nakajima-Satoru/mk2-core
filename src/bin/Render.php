<?php

/*

mk2 | Render

A class for outputting HTML tags on the screen etc. and displaying the layout.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

class Render extends CoreBlock{

	public function rendering(){
		if($this->Template){
			return $this->Response->getTemplate();
		}
		else{
			return $this->Response->getView();
		}
	}
}