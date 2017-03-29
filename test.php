<?php

require_once("search.SS.class.php");


$inst1 = new SS_search($argv[1]);

if($inst1->countResults()==0){
  echo "Sem resultados.\n";
}

exit(0);

?>
