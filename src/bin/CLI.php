<?php

namespace mk2\core;

class CLI{

	public function out($output){
		echo $output."\n";
	}

	public function input($output){
		echo $output." : ";
		return trim(fgets(STDIN));
	}

}