<?php
define ('DBHOST','localhost');
define ('DBUSER','root');
define ('DBPASS','');
define ('DBNAME','otthon_kereso');

if (!$con = mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME))
{
	die("Nem sikerült csatlakozni");
}