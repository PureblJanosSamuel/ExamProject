<?php
session_start();

include("functions.php");

$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
if ($logged_in) {
  header("Location: index.php");
  exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{
  $username = $_POST['username'];
  $password = $_POST['password'];
  
  if (!empty($username) && !empty($password) && !is_numeric($username))
  {
    
    $result = DB::GET("SELECT * FROM users WHERE Name = ? limit 1", array($username));
    if ($result)
    {
      if ($result && mysqli_num_rows($result) > 0)
      {
        $user_data = mysqli_fetch_assoc($result);
        if (password_verify($password,$user_data['Password']))
        {
          $_SESSION['userID'] = $user_data['userID'];  
          $_SESSION['logged_in'] = true;
          header("Location: index.php");
          die;
        }
      }
    }
    header("Location: login.php?hiba");
  }
  else
  {
    header("Location: login.php?hiba");
  }
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Otthon Kereső - Bejelentkezés</title>

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/def.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="icon" href="img/icons/house.png" type="image/png">

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-green" aria-label="Third navbar example">
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
    <section class="h-100 gradient-form">
      <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-xl-10">
            <div class="card rounded-3 text-black">
              <div class="card-body p-md-5 mx-md-4">
                <div class="text-center">
                  <img src="./img/icons/icon.png" style="width: 185px;" alt="logo">
                  <h4 class="mt-1 mb-5 pb-1">Bejelentkezés</h4>
                </div>
  
                <form method="post">
                  <p style="text-align:center;">Kérjük jelentkezz be!</p>
                  <?php if (isset($_GET['hiba'])) {echo "<p class='text-danger text-center bg-warning'><b>Hibás felhasználó vagy jelszó!</b></p>";}?>
                  <?php if (isset($_GET['sikeres_regisztracio'])) {echo "<br><p class='alert text-warning text-center bg-success'><b>Sikeres regisztráció!</b></p><br>";}?>
  
                  <div class="form-outline mb-4">
                    <label class="form-label" for="nev">Felhasználó név:</label><br>
                    <input type="text" id="nev" placeholder="Felhasználónév" name="username" class="form-control" required>
                  </div>
  
                  <div class="form-outline mb-4">
                    <label class="form-label" for="jelszo">Jelszó:</label><br>
                    <input type="password" id="jelszo" placeholder="Jelszavad" name="password" class="form-control" required>
                  </div>
  
                  <div class="text-center pt-1 mb-5 pb-1">
                    <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit">Belépés</button><br>
                    <a class="text-muted" href="forgotten_username.php">Elfelejtett felhasználónév?</a><br>
                    <a class="text-muted" href="password_res.php">Elfelejtett jelszó?</a>
                  </div>
  
                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Még nincs fiókod?</p>
                    <button type="button" class="btn btn-outline-danger" onclick="location.href='register.php'">Regisztrálj! </button>
                  </div>
  
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  
  <footer class="container-fluid py-3 bg-blue">
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