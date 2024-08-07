<?php
    session_start();

    include("functions.php");

    $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    $user_data = check_login();
    $is_admin = is_admin();
    $is_moderator = is_moderator();
    if (!$logged_in) {
        header("Location: index.php");
        exit;
    }

    $user_id = $_SESSION['userID'];

    $result = DB::GET(
        "SELECT Email, Name, Description, PhoneNumber, ProfilePicture, Thumbnail FROM users WHERE userID = ?",
        array($user_id));
    $row = mysqli_fetch_assoc($result);

    $html = '
    <form>
                <div class="row">
                    
                    <div class="col-md-4">
                        <div class="profile-img" style="background-image: url('.$row['Thumbnail'].');">
                            <img src="'.$row['ProfilePicture'].'" alt="'.$row['Name'].'" />
                            <p></p>
                        </div>
                        <div class="col-md-2" style="all: initial;">
                            <br>
                            <a class="btn btn-secondary m-3" href="profil_szerkesztes.php">Szerkesztés</a>
                        </div>
                    </div>
                    <?php echo $html; ?>
                    <div class="col-md-6">
                        <p class="text-center">Profil adatok</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="tab-content profile-tab" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel"
                                        aria-labelledby="home-tab" style="color:black;background-color:white;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Felhasználónév:</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>'.$row['Name'].'</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>E-mail:</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>'.$row['Email'].'</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Telefonszám:</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>'.$row['PhoneNumber'].'</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="leiras">
                        <p class="text-center">Magamról:</p>
                        <p class="text-center">'.$row['Description'].'</p>
                    </div>
                </div>

            </form>
    ';

?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otthon Kereső - Profilod</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/def.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="img/icons/house.png" type="image/png">
    <link rel="stylesheet" href="css/profile.css">
    
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
        <div>
            <br>
            <br>
        </div>
        <div class="container emp-profile">
            <?php echo $html ?>
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