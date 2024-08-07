<?php
include("dbconnect.php");

class DB{
  static function GET($sql, $arr){
    $db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
    $stmt = $db->prepare($sql);
    if(count($arr) != 0){
      $types = [];
      foreach($arr as $item){
        $types[] = substr(gettype($item),0,1);
      }
      $types = implode('', $types);
      $stmt->bind_param($types, ...$arr);
    }
    $stmt->execute();
    //$stmt->execute($arr);
    return $stmt->get_result();
  }

  static function POST($table,$items,$arr){
    $db = new mysqli('localhost', 'root', '', 'otthon_kereso');
    $sql = "INSERT INTO $table (";
    $vals = " VALUES (";
    foreach($items as $item){
      $sql .= " $item,";
      $vals .= " ?,";
    }
    $sql = rtrim($sql, ',');
    $sql = $sql . " )";
    $vals = rtrim($vals, ',');
    $vals = $vals . " )";
    $sql = $sql . $vals;

    $stmt = $db->prepare($sql);
    $stmt->execute($arr);
  }

  static function UPDATE($table,$items,$id,$arr,$uid){
    $db = new mysqli('localhost', 'root', '', 'otthon_kereso');
    $sql = "UPDATE $table SET";
    foreach($items as $item){
      $sql = $sql . " $item = ?,";
    }
    $sql = rtrim($sql, ',');
    $sql = $sql . " WHERE $id = ?";
    array_push($arr,$uid);
    $stmt = $db->prepare($sql);
    $stmt->execute($arr);
  }
  static function UP($sql, $arr){
    $db = new mysqli('localhost', 'root', '', 'otthon_kereso');
    $stmt = $db->prepare($sql);
    $stmt->execute($arr);
  }


  static function DELETE($tables, $id, $iID){
    $db = new mysqli('localhost', 'root', '', 'otthon_kereso');
    foreach($tables as $table){
      $sql = "DELETE FROM $table WHERE $id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute(array($iID));
    }
  }
  static function DEL($sql, $arr){
    $db = new mysqli('localhost', 'root', '', 'otthon_kereso');
    $stmt = $db->prepare($sql);
    $stmt->execute($arr);
  }
}

?>