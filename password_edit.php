<?php
session_start();

include("dbconnect.php");
include("functions.php");

$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_data = check_login($con);
$is_admin = is_admin($con);
$is_moderator = is_moderator($con);
if (!$logged_in) {
  header("Location: index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  $user_id = $_SESSION['userID'];

  $result = DB::GET(
    "SELECT Password FROM users WHERE userID = ?",
    array($user_id)
  );

  if($result) {
      $row = mysqli_fetch_assoc($result);
      $stored_password = $row['password'];

      if(password_verify($current_password, $stored_password)) {
          if($new_password === $confirm_password) {
              $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

              $result = DB::UP("UPDATE users SET Password = ? WHERE userID = ?",array($hashed_password,$user_id));

              if($result) {
                  header("Location: index.php?success=password_updated");
                  exit();
              } else {
                  header("Location: password_edit.php?error=password_update_failed");
                  exit();
              }
          } else {
              header("Location: password_edit.php?error=password_mismatch");
              exit();
          }
      } else {
          header("Location: password_edit.php?error=incorrect_current_password");
          exit();
      }
  } else {
      header("Location: index.php?error=query_failed");
      exit();
  }
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Otthont keresők - Jelszó szerkesztése</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/def.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="img/icons/house.png" type="image/png">
    <link rel="stylesheet" href="./css/profile.css">
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
            <a class="nav-link text-white bg-danger" aria-current="page" href="profile.php">Profilodra</a>
          </li>
        </ul>
        <?php if (!$logged_in): ?>
        <a class="nav-link text-white" href="login.php">Bejelentkezés</a>
        <?php endif; ?>
        
        <?php if ($is_admin): ?>
        <a class="nav-link text-danger" href="admin.php">Admin felület</a>&nbsp;|&nbsp;
        <?php endif; ?>

        <?php if ($is_moderator || $is_admin): ?>
        <a class="nav-link text-primary" href="moderation.php">Moderátori felület</a>&nbsp;|&nbsp;
        <?php endif; ?>

        <?php if ($logged_in): ?>
          <?php greet_user();?><a class="nav-item" href="profile.php"><?php echo $user_data['Name'];?></a>&nbsp;<img width="16" height="16" src="<?php echo $user_data['ProfilePicture'] ?>" focusable="false"></img>&nbsp;|&nbsp;
        <a class="nav-link text-white" href="logout.php">Kijelentkezés</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

    <div class="bg">
        <section class="h-100 gradient-form" style="padding-top:50px; padding-bottom:50px;">
          <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
              <div class="col-xl-10">
                <div class="card rounded-3 text-black">
                  <div class="card-body p-md-5 mx-md-4">
                                                
                    <div class="text-center">
                      <img src="./img/icons/icon.png" style="width: 185px;" alt="logo">
                      <h4 class="mt-1 mb-5 pb-1">Jelszó szerkesztés</h4>
                    </div>
                    <p style="text-align:center;">Új jelszó létrehozása:</p>
      
                    <form method="post">
                        <div class="form-group">
                            <label>Régi jelszó:</label>
                            <input type="password" name="current_password" class="form-control">
                            </span>
                        </div>
                        <div class="form-group">
                            <label>Új jelszó:</label>
                            <input type="password" name="new_password" class="form-control">
                            </span>
                        </div>
                        <div class="form-group">
                            <label>Új jelszó megerősítése:</label>
                            <input type="password" name="confirm_password" class="form-control">
                            </span>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Küldés">
                            <a class="btn btn-outline-danger" href="index.php">Vissza a főoldalra</a>
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