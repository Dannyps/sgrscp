<?php

class SS_student{
    private $ready=false;
    private $name="";
    private $courses;
	private $number;
	
	function __construct($number, $name){
        $this->number=$number;
		$this->name=$name;
    }
	
	function __toString(){
		return "[$this->number] $this->name".PHP_EOL;
	}
}

?>
