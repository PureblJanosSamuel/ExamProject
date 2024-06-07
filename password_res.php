<?php
session_start();

include("functions.php");

$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_data = check_login($con);
if ($_SERVER['REQUEST_METHOD'] === 'POST'){ 
  $email = $_POST['email'];
  $username = $_POST['username'];
  $activationcode = $_POST['activationcode'];

  $result = DB::GET(
    "SELECT * FROM users WHERE Email=? AND Name=? AND ActivationCode=?",
    array($email,$username,$activationcode)
  );

  if(mysqli_num_rows($result) == 1){
    $new_password = bin2hex(random_bytes(5));
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    
    DB::UP(
      "UPDATE users SET Password=? WHERE Email=? AND Name=?",
        array($hashed_password,$email,$username)
    );

    $to = $email;
    $subject = "Elfelejtett jelszó - Otthon Kereső";
    $message = "
    <html>
		<head>
			<title>Új jelszó generálva - Otthon Kereső</title>
		</head>
		<body>
		<div style='text-align: center; margin-left: 30%; margin-right: 30%;'>
			<h1 style='text-decoration: underline; background-color: lightgreen; color: orange;'>Üdvözlet kedves Otthon Kereső oldalt felhasználó!</h1>
		</div>

	  <div style='text-align: center; margin-left: 30%; margin-right: 30%;'>
	  
		<img width='100%' height='250pt' src='http://otthonkereso.nhely.hu/img/email_gif_attachments/dog_computer_hello.gif'>

	  	</div>

	  	<br>
	  
		<div style='text-align: center; margin-left: 30%; margin-right: 30%;'>
			<a>Sajnálattal értesültünk róla, hogy elfelejtetted a jelszavad, de nem kell aggódnod itt vagyunk és íme egy vers tőlünk önnek!</a>
		</div>
	  	<br>

	  	<div style='text-align: center; border: 4px solid gray; margin-left: 30%; margin-right: 30%;'>
			<a style='text-align: center; text-decoration: underline;'><b>Az elfelejtett jelszó</b></a>
			<br><br>
			Sajnálattal hallom, hogy elcsúfított a homály,<br>
			elfelejtette jelszavát az árnyékfalak mögött,<br>
			elfogytak a betűk, amelyeknek hívóereje volt,<br>
			mint sötétlő rózsaszín a napfényben.<br>
			<br>
			Az üres űr most kísérteties csendben áll,<br>
			és a szavak csak visszhangot adnak,<br>
			de tudom, milyen kellemetlen lehet ez,<br>
			és segíteni szeretnék, ha csak tehetem.<br>
			<br>
			Egy kattintásra volt szüksége, hogy helyreálljon,<br>
			egy e-mail, amely visszahozza az irányítást,<br>
			és én itt állok, hogy segítsek bármilyen módon,<br>
			hogy újra beléphessen fiókjába boldogan.<br>
			<br>
			Tudom, hogy elveszettnek érzi magát most,<br>
			de ne aggódjon, nincs oka a bánatára,<br>
			mi itt vagyunk, hogy segítsünk, ha szüksége van rá,<br>
			és várjuk, hogy ismét bejelentkezzen, amikor készen áll.<br>
			<br>
		</div>
		<br><br>
		
		<div style='text-align: center; margin-left: 30%; margin-right: 30%;'>
		
			<img width='100%' height='250pt' src='http://otthonkereso.nhely.hu/img/email_gif_attachments/you_forgot.gif'>
			 
		<div>
		  
		  <br>
			<h1 style='text-align: center'>Itt a jelszavad: ".$new_password."</h1>
			<p>Üdvözlettel: <br> Otthon Kereső E-mail Rendszere</p>
			<br>
			<button style='background-color: rgb(49, 176, 49);'><a href='http://otthonkereso.nhely.hu/login.php'>Belépés</a></button>
		</body>
    </html>
    ";
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: ok@otthonkereso.nhely.hu' . "\r\n";

    if(mail($to, $subject, $message, $headers)){
      header("Location: password_res.php?email_kuldve");
    } else {
      header("Location: password_res.php?hiba_email");
    }
  } else {
    header("Location: password_res.php?hiba_no_email_in_db");
  }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Otthont keresők - Elfelejtett jelszó</title>

    
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/def.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="icon" href="img/icons/house.png" type="image/png">
    
</head>
<body>
  <nav class="navbar navbar-expand-sm navbar-dark bg-green" aria-label="Third navbar example">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Otthon kereső</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample03" aria-controls="navbarsExample03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample03">
        <ul class="navbar-nav me-auto mb-2 mb-sm-0">
          <li class="nav-item">
            <a class="nav-link text-white bg-danger" aria-current="page" href="index.php">Főoldalra</a>
          </li>
        </ul>
        
      </div>
    </div>
  </nav>
  
    <div class="bg">
      <?php if (isset($_GET['email_kuldve'])) {echo "<br><p class='alert text-primary text-center bg-warning'><b>Ellenőrizd az E-mailed!</b></p><br>";}?>      
      <?php if (isset($_GET['hiba_email'])) {echo "<br><p class='alert text-primary text-center bg-danger'><b>Hiba az e-mail küldése során. Kérjük, próbáld meg később újra.</b></p><br>";}?>
      <?php if (isset($_GET['hiba_no_email_in_db'])) {echo "<br><p class='alert text-primary text-center bg-danger'><b>E-mail cím nem található az adatbázisunkban.</b></p><br>";}?>
        <section class="h-100 gradient-form" style="padding-top:50px; padding-bottom:50px;">
          <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
              <div class="col-xl-10">
                <div class="card rounded-3 text-black">
                  <div class="card-body p-md-5 mx-md-4">
                      
                    <div class="text-center">
                      <img src="img/icons/icon.png" style="width: 185px;" alt="logo">
                      <h4 class="mt-1 mb-5 pb-1">Jelszó helyreállítása</h4>
                    </div>
                    <p style="text-align:center;">Új jelszó létrehozásához szükségünk lesz az E-mailodra és a felhasználónévre amivel regisztráltál az oldalunkra!</p>
                    <form method="post">
                        <div class="form-group">
                            <label>E-mail címed:</label>
                            <input type="email" name="email" class="form-control" placeholder="Az e-mail amivel regisztráltál">
                        </div>
                        <div class="form-group">
                            <label>Otthon Kereső Felhasználóneved:</label>
                            <input type="text" name="username" class="form-control" placeholder="A felhasználónév amivel regiszráltál">
                        </div>
                        <div class="form-group">
                            <label>Aktivációs kódod:</label>
                            <input type="text" name="activationcode" class="form-control" placeholder="Aktivációs kódod amit regisztrálás után kaptál e-mailben">
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Küldés">
                            <a class="btn btn-outline-danger" href="login.php">Bejelentkezés</a>
                        </div>
      
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
      

      </div>
      <footer class="container-fluid py-3 bg-blue footer">
      <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item"><a href="index.php" class="nav-link px-2 text-white">Főoldal</a></li>
        <li class="nav-item"><a href="search.php" class="nav-link px-2 text-white">Kereső</a></li>
        <li class="nav-item"><a href="aszf.php" class="nav-link px-2 text-white">ÁSZF</a></li>
        <li class="nav-item"><a href="contact.php" class="nav-link px-2 text-white">Kapcsolat</a></li>
      </ul>
      <p class="text-center text-muted">Otthon Kereső © 2022-2023 Company, Inc</p>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>