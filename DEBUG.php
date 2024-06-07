<?php

class DEBUG{
  static function SEE($var){
    var_dump($var);
    die();
  }
  static function SEEALL($arr){
    foreach($arr as $var){
      var_dump($var);
    }
    die();
  }
}

?>