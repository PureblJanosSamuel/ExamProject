<?php
session_start();

include("functions.php");

$activationCode = generateActivationCode();

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $email = $_POST['email'];
    $username = $_POST['username'];

    if (!empty($username) && !empty($email)) {
        $result = DB::GET(
            "SELECT * FROM users WHERE Email= ?",
            array($email)
        );
        if (mysqli_num_rows($result) > 0) {
            header("Location register.php?email_letezik");
        } else {
            $userID = generateID();
            DB::POST(
                "users",
                array("userID", "Name", "Email", "ActivationCode", "Activated"),
                array('$userID','$username','$email','$activationCode', 'Nincs')
            );

            $to = $email;
            $subject = "Aktivációs kód - Otthon Kereső";
            $message = "
                        <html>
                <head>
                    <title>Aktivációs kód - Otthon Kereső</title>
                </head>
                <body>
                <div style='text-align: center; margin-left: 30%; margin-right: 30%;'>
                    <h1 style='text-decoration: underline; background-color: lightgreen; color: orange;'>Üdvözlet kedves  ".$username." !</h1>
                </div>

                <br>
            
                <div style='text-align: center; margin-left: 30%; margin-right: 30%;'>
                    <a><b>Örömmel értesültünk róla hogy regisztrálni szeretne az oldalunkra!</b></a>
                </div>
                <br>
                
                
                <br>
                    <h1 style='text-align: center'>Itt az aktivációs kódod amit a regisztráció aktiválása oldalon kell megadnod:<br> ".$activationCode."</h1>
                    <a href='http://otthonkereso.nhely.hu/activate_registration.php?pre_username=$username&pre_email=$email&activation_code=$activationCode'>Regisztráció aktiválása</a>
                    <p>Üdvözlettel: <br> Otthon Kereső E-mail Rendszere</p>
                    <br>
                </body>
            </html>
            ";
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $headers .= 'From: ok@otthonkereso.nhely.hu' . "\r\n";

            if (mail($to, $subject, $message, $headers)) {
                header("Location: activate_registration.php?email_kuldve&pre_username=$username&pre_email=$email");
            } else {
                header("Location: register.php?email_sikertelen");
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Otthont keresők - Regisztráció</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/def.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="img/icons/house.png" type="image/png">

        
    <style>
          #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: flex;
            justify-content: center;
            align-items: center;
            }

            #popup {
            width: 50%;
            max-width: 500px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            }
          
    </style>
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
            <a class="nav-link txt-white bg-danger" aria-current="page" href="index.php">Főoldalra</a>
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
                                <h4 class="mt-1 mb-5 pb-1">Regisztráció</h4>
                            </div>

                            <form id="regist" method="post">
                                <p>Regisztráció</p>
                                <?php if (isset($_GET['email_sikertelen'])) {echo "<br><p class='alert text-primary text-center bg-warning'><b>Nem sikerült az E-mail elküldenie a rendszernek! Kérlek próbáld újra később!</b></p><br>";}?>
                                <?php if (isset($_GET['email_letezik'])) {echo "<br><p class='alert text-primary text-center bg-warning'><b>Az E-mail már szerepel az adatbázisunkban! Sajnos valaki már regisztrált vele!</b></p><br>";}?>
                                <div class="form-outline mb-4">
                                    <!-- email -->
                                    <label class="form-label" for="email">E-mail:</label><br>
                                    <input type="email" id="email" name="email" placeholder="példaandrás@gmail.com" class="form-control" required>
                                    <!-- felhasználónév -->
                                    <label class="form-label" for="nev">Felhasználónév:</label><br>
                                    <input type="text" id="nev" name="username" placeholder="Felhasználónév" class="form-control" required>
                                </div>

                                <div class="text-center pt-1 mb-5 pb-1">
                                    <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit">Regisztrálj</button>
                                </div>

                                <div class="d-flex align-items-center justify-content-center pb-4">
                                    <!-- belépés oldal link -->
                                    <p class="mb-0 me-2">Már van fiókod?</p>
                                    <button type="button" class="btn btn-outline-danger"
                                        onclick="location.href='login.php'">Jelentkezz be!
                                    </button>
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

  <script>
        var checker = document.getElementById('terms');
        var sendbtn = document.getElementById('but');
        // amikor bepipálja, fusson le a funkció
        checker.onchange = function () {
            if (this.checked) {
                sendbtn.disabled = false;
            } else {
                sendbtn.disabled = true;
            }

        }

        const form = document.querySelector('form');
        const overlay = document.getElementById('overlay');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            overlay.style.display = 'flex';
        });
    </script>
  <script src="js/bootstrap.bundle.min.js"></script>
  
</body>

</html>