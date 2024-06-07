<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "otthon_kereso";

if (!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("Nem sikerült csatlakozni");
}
mysqli_set_charset($con, "utf8");