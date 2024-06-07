<?php
include("functions.php");

$pre_username = $_GET['pre_username'];
$pre_email = $_GET['pre_email'];
$activationcode = $_GET['activation_code'];

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $email = $_POST['email'];
    $username = $_POST['username'];
    $activationcode = $_POST['activationcode'];
    $password = $_POST['password'];
    $pw2 = $_POST['pw2'];

    if(preg_match('/[A-Z]/',$password) &&
    preg_match('/[a-z]/',$password) &&
    preg_match('/[0-9]/',$password) &&
    strlen($password) > 8 && strlen($password) < 15)
    {

        if (!empty($username) && !empty($email)) {
            $result = DB::GET(
                "SELECT * FROM users WHERE Email=? AND Name=? AND ActivationCode=?",
                array($email,$username,$activationcode)
            );
            if (mysqli_num_rows($result) > 0) {
                $hash = password_hash($password,PASSWORD_DEFAULT);
                DB::UP(
                    "UPDATE users SET Password = ?, Activated = ? WHERE Email= ? AND Name= ? AND ActivationCode= ?",
                    array($hash,'Aktív',$email,$username,$activationcode)
                );
                header("Location: login.php?sikeres_regisztracio");
            } else {
                header("Location: activate_registration.php?nem_talalhato");
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
    <link rel="icon" href="img/icons/house.png" type="image/gif">

        
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
                                <h4 class="mt-1 mb-5 pb-1">Regisztráció akitválás</h4>
                            </div>

                            <form id="regist" method="post">
                                <p>Regisztráció akitválás</p>
                                <?php if (isset($_GET['email_kuldve'])) {echo "<br><p class='alert text-primary text-center bg-warning'><b>Ellenőrizd az E-mailed!</b></p><br>";}?>
                                <p id="hiba üzenet" style="color: red;"></p>
                                <div class="form-outline mb-4">
                                    <!-- email -->
                                    <label class="form-label" for="email">E-mail:</label><br>
                                    <input type="email" id="email" name="email" placeholder="példaandrás@gmail.com (Az e-mail címnek egyeznie kell azzal a e-mailel amit a regisztráció során megadtál)" class="form-control" value="<?php echo $pre_email; ?>" required><br>
                                    <!-- felhasználónév -->
                                    <label class="form-label" for="nev">Felhasználónév:</label><br>
                                    <input type="text" id="nev" name="username" placeholder="Felhasználónév (A felhasználónévnek egyeznie kell azzal a névvel amit regisztráció során megadtál)" class="form-control" value="<?php echo $pre_username; ?>" required><br>
                                    <!-- aktivációs kód -->
                                    <label class="form-label" for="activationcode">Aktivációs kód:</label><br>
                                    <input type="text" id="activationcode" name="activationcode" placeholder="Aktivációs kódod (A kódot egy e-mailben továbbítottuk)" class="form-control" value="<?php echo $activationcode; ?>" required>
                                </div>

                                <div class="form-outline mb-4">
                                    <!-- jelszó -->
                                    <label class="form-label" for="jelszo">Jelszó:</label><br>
                                    <input type="password" id="jelszo" name="password" class="form-control" placeholder="Jelszó  (Legalább 9 karaktert tartamazzon és legyen benne legalább egy nagy betű valamint legalább egy szám)" required>
                                    <span class="invalid-feedback"></span>
                                    <!-- jelszó megerősítése -->
                                    <label class="form-label" for="jelszo">Jelszó megerősítése:</label><br>
                                    <input type="password" id="jelszo_megerosites" name="pw2" class="form-control" placeholder="Jelszó újra" required>
                                    <span class="invalid-feedback"></span>
                                    <!-- Szerződés -->
                                    <input type="checkbox" id="terms" name="terms" value="agree">
                                    <label for="terms">Elolvastam és elfogadom az <a target="_blank" href="aszf.php">Általános Szerződési Feltételeket</a> !</label><br>
                                </div>
                                <!-- beküldés gomb / javascript futtatása -->
                                <div class="text-center pt-1 mb-5 pb-1">
                                    <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit" onclick="passcheck()" disabled="disabled" id="but">Regisztrálj</button>
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
        function passcheck() {
            var password = document.getElementById("jelszo");
            var confirm_password = document.getElementById("jelszo_megerosites");
            if (password.value == confirm_password.value) {

                if (document.getElementById("nev").value.length == 0) {

                    document.getElementById('hiba üzenet').innerText = "A név sáv kitöltése kötelező!";

                } else if (document.getElementById("nev").value.length <= 2) {

                    document.getElementById('hiba üzenet').innerText = "A név nem lehet kisebb 3 karakternél!";

                } else if (document.getElementById("jelszo").value.length == 0) {

                    document.getElementById('hiba üzenet').innerText = "A jelszó mező kitöltése kötelező!";

                } else if (document.getElementById("jelszo").value.length <= 7 || document.getElementById("jelszo").value.length >= 16) {

                    document.getElementById('hiba üzenet').innerText = "A jelszó nem lehet kisebb 8 karakternél, és nem lehet nagyobb 15 karakternél!";

                } else {

                    document.getElementById("regist").submit();
                }
            } else {

                document.getElementById('hiba üzenet').innerText = "A két jelszónak meg kell egyeznie!";

            }
        }
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
    </script>
</body>

</html>