<?php
session_start();

include("functions.php");

$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

if (!$logged_in) {
  header("Location: index.php");
  exit;
}

$user_id = $_SESSION['userID'];

$result = DB::GET(
  "SELECT Picture FROM animalinfo WHERE userID = ?",
  array($user_id)
);

unlinkpic($result,'Picture');

$result = DB::GET(
  "SELECT ProfilePicture FROM users WHERE userID = ? AND ProfilePicture != ?",
  arr($user_id,'img/profile_pics/default.png')
);
unlinkpic($result,"ProfilePicture");


$result = DB::GET(
  "SELECT * FROM users WHERE userID = ?", array($user_id)
);
$row = $result->fetch_assoc();
if ($row !== null && $row['Thumbnail'] !== 'img/thumbnail_pics/default.png') {
  $image_path = $row['Thumbnail'];
  if (file_exists($image_path)) {
    unlink($image_path);
  }
}

DEL(
  "DELETE users, animalinfo FROM users INNER JOIN animalinfo ON users.userID = animalinfo.userID WHERE users.userID = ? AND ProfilePicture != ? AND Thumbnail != ?",
  array($user_id,'img/profile_pics/default.png','img/thumbnail_pics/default.png'));

session_destroy();

header('Location: index.php?fiok_sikeresen_torolve');
exit();
?>