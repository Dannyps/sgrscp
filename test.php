#!/usr/bin/php
<?php

require_once("search.SS.class.php");

if(!isset($argv[1])){
  die("Missing argument!\nUsage: php test.php <up20**1234>\n");
}

$inst1 = new SS_search("up".$argv[1], 'course');

if($inst1->countResults()==0){
  echo "Sem resultados.\n";
}else{
  echo $inst1->student;
}

foreach($inst1->courses as $c){
  echo $c;
}

die();

for($i=201000041+100;$i<201010000;$i++){
  $inst1 = new SS_search("up".$i, 'name');
  
  if($inst1->countResults()==0){
    echo "Sem resultados.\n";
  }else{
    echo $inst1->student;
  }
  
  /*foreach($inst1->courses as $c){
    echo $c;
  }*/
  //echo $inst1->courses[0];
  //var_dump($inst1->courses);
  
}

exit(0);

?>
