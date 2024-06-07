<?php
  session_start();
  include("functions.php");

  $user_data = check_login();
  $is_admin = is_admin();
  $is_moderator = is_moderator();
  $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    if (!$is_admin) {
    header("Location: index.php");
    exit;
    }

  $user_id = $_GET['edit_user_ID'];
  $html = '';
  
  $result = DB::GET(
    "SELECT * FROM users WHERE userID = ?",
    array($user_id)
  );

  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $arrName = array('username','phonenumber','description','email','password');
    $arrPlace = array("Name","PhoneNumber","Description","Email","Password");
    $arrItems = [];
    $arrValues = [];

    if(isset($_POST['submit'])) {
      for ($i=0; $i < count($arrName); $i++) {
        
        if(!empty($_POST[$arrName[$i]])){
          
          if(strcmp($arrPlace[$i], "Password")==0){
            array_push($arrItems, "Password");
            array_push($arrValues, password_hash($_POST[$arrName[$i]],PASSWORD_DEFAULT));
          }
          else{
            array_push($arrItems, $arrPlace[$i]);
            array_push($arrValues, $_POST[$arrName[$i]]);
          }
        }
      }
      
      if(!empty($arrItems)&&!empty($arrValues)){
        DB::UPDATE(
          "users",
          $arrItems,
          "userID",
          $arrValues,
          $user_id
        );
      }

      header("Location: admin.php?sikeres_szerkesztes");
      
    }
    else {
      $html .= '      
      <div class="container pt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 border p-4">
            <div class="row">
                <div class="col-md-6">
                <img height="150px" width="150px" src="'.$row['ProfilePicture'].'" alt="'.$row['Name'].'" title="'.$row['Name'].'" />
                <div class="col-md-2 mt-3" style="all: initial;">
                <a class="btn btn-danger" href="admin_API.php?action=delete_profile_pic" disabled onclick="return false;">Profilkép törlése</a>
                </div>
                </div>
                <div class="col-md-6">
                <img height="150px" width="150px" src="'.$row['Thumbnail'].'" />
                <div class="col-md-2 mt-3" style="all: initial;">
                    <a class="btn btn-danger" href="admin_API.php?action=delete_thumbnail_pic" disabled onclick="return false;">Borítókép törlése</a>
                </div>
                </div>
            </div>
            </div>
        </div>
      </div>

      <div class="container pt-5">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4 border p-4">
            <form method="post">
              <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" class="form-control" name="username" placeholder="' . $row['Name'] . '">
              </div>
              <div class="form-group">
                <label for="email">E-mail cím:</label>
                <input type="email" class="form-control" name="email" placeholder="' . $row['Email'] . '">
              </div>
              <div class="form-group">
              <label for="password">Jelszó:</label>
              <input type="text" rows="4" class="form-control" name="password">
              </div>
              <div class="form-group">
              <label for="description">Leírás:</label>
              <textarea type="text" rows="4" class="form-control" name="description" placeholder="' . $row['Description'] . '"></textarea>
              </div>
              <div class="form-group">
              <label for="description">Telefon:</label>
              <input type="phonenumber" class="form-control" name="phonenumber" placeholder="' . $row['PhoneNumber'] . '">
              </div>
              <br>
              <button type="submit" name="submit" class="btn btn-primary">Mentés</button>    
              <a href="admin.php" class="btn btn-danger text-white">Kilépés<a/>
            </form>
          </div>
        </div>
      </div>';
    }
  }
  else {
    header("Location: admin.php?a_felhasznalo_nem_letezik");
    exit();
  }

  $htmlanimals = '<h4>Állatai:</h4>';
  $result = DB::GET(
    "SELECT animalID,Name,Picture,Description,Age FROM animalinfo WHERE userID = ?",
    array($user_id)
  );

  if(mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $htmlanimals .= '
        <div class="col">
                      <div class="card shadow-sm">
                      <img class="bd-placeholder-img card-img-top" width="100%" height="225" src="'.$row['Picture'].'" focusable="false">
                          <rect width="100%" height="100%" fill="#55595c"/>
                      </img>

                          <div class="card-body">
                              <p class="card-text"><b>'.$row['Name'].'</b><br>'.$row['Description'].'</p>
                              <div class="d-flex justify-content-between align-items-center">            
                                  <div class="btn-group">
                                      <a href="allat_szerkesztes_admin.php?edit_animal_ID='.$row['animalID'].'">
                                          <button type="button" class="btn btn-sm bg-primary btn-outline-warning"><b>Szerkesztés</b></button>
                                      </a>
                                  </div>                                       
                                  <p class="text-primary">Kora: '.$row['Age'].'</p>
                                  <div class="btn-group">
                                      <a>
                                          <button type="button" class="btn btn-sm bg-green btn-outline-danger"></button>
                                      </a>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>';
    }
  }
  else {
    $htmlanimals .= '<h1>Nincsenek állatai.</h1>';
  }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Otthont keresők - Admin profil szerkesztés</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
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
            <a class="nav-link txt-white" href="index.php">Főoldal</a>
          </li>
          <li class="nav-item">
            <a class="nav-link txt-white" href="search.php">Kereső</a>
          </li>
          <?php if ($logged_in): ?>
          <li class="nav-item">
            <a class="nav-link txt-white" href="hirdeteseid.php">Hirdetéseid</a>
          </li> 
          <?php endif; ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle txt-white" href="contact.php" data-bs-toggle="dropdown" aria-expanded="false">Kapcsolat</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item txt-dark" href="contact.php#team">Készítők</a></li>
              <li><a class="dropdown-item txt-dark" href="contact.php#contact">Írj nekünk</a></li>
              <li><a class="dropdown-item txt-dark" href="contact.php#bugreport">Hiba jelentése</a></li>
            </ul>
          </li>
        </ul>
       
        <?php if (!$logged_in): ?>
        <a class="nav-link text-white" href="login.php">Bejelentkezés</a>
        <?php endif; ?>
        
        <?php if ($is_admin): ?>
        <a class="nav-link text-danger active" href="admin.php">Admin felület</a>&nbsp;|&nbsp;
        <?php endif?>

        <?php if ($is_moderator || $is_admin): ?>
        <a class="nav-link text-primary" href="moderation.php">Moderátori felület</a>&nbsp;|&nbsp;
        <?php endif?>

        <?php if ($logged_in): ?>
          <?php greet_user();?><a class="nav-item" href="profile.php"><?php echo $user_data['Name'];?></a>&nbsp;<img width="16" height="16" src="<?php echo $user_data['ProfilePicture'] ?>" focusable="false"></img>&nbsp;|&nbsp;
        <a class="nav-link text-white" href="logout.php">Kijelentkezés</a>
        <?php endif; ?>
        
      </div>
    </div>
  </nav>
    <p class="centeralign bold underline bigfont">Felhasználó szerkesztése</p>

    <?php echo $html; ?>
    <br>
    <div class="container emp-profile">
      <div class="album py-5 container text-center">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
          <?php echo $htmlanimals; ?>
        </div>
      </div>
    </div>

    <footer class="container-fluid py-3 bg-blue">
      <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item"><a href="index.php" class="nav-link px-2 txt-white">Főoldal</a></li>
        <li class="nav-item"><a href="search.php" class="nav-link px-2 txt-white">Kereső</a></li>
        <li class="nav-item"><a href="aszf.php" class="nav-link px-2 txt-white">ÁSZF</a></li>
        <li class="nav-item"><a href="contact.php" class="nav-link px-2 txt-white">Kapcsolat</a></li>
      </ul>
      <p class="text-center text-muted">Otthon Kereső © 2022-2023 Company, Inc</p>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
</body>
</html>