<?php

class sgrscp{

	function __construct($upN){
		if(!$this->isValidUPNumber($upN)){
			throw new exception("Invalid UP number passed!");
		}

		
	}

	function isValidUPNumber($upN):bool{
		if($upN[0]!='u' || $upN[1]!='p')
			return false;
		
		if(strlen($upN)!=11)
			return false;

		$upN=substr($upN, 2);
		if(!is_numeric($upN))
			return false;

		// So far, so good.
		return true;
	}

	

}

?>