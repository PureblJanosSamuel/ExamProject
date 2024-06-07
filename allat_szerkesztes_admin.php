<?php
  session_start();
  include("functions.php");
  include("admin_API.php");

  $user_data = check_login();
  $is_admin = is_admin();
  $is_moderator = is_moderator();
  $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    if (!is_admin()) {
    header("Location: index.php");
    exit;
    }

  $animal_ID = $_GET['edit_animal_ID'];
  $html = '';
  
  $result = DB::GET(
    "SELECT * FROM animalinfo WHERE animalID = ?",
    array($animal_ID)
  );

  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $arrName = array('animalname','description','exercise','vaccines','description');
    $arrPlace = array("Name","Description","NeedofExercise","Vaccines","Description");
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
        array_push($arrValues, $animal_ID);
        DB::UPDATE(
          "animalinfo",
          $arrItems,
          "animalID",
          $arrValues
        );
      }

      header("Location: admin.php?sikeres_szerkesztes");
      
    }
    else {
    $html .= '      
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center">
                <img height="150px" width="150px" src="'.$row['Picture'].'" alt="'.$row['Name'].'" title="'.$row['Name'].'" />
                <br><br><div class="col-md-2 mt-3" style="all: initial;">
                    <a class="btn btn-secondary" href="admin_API.php?action=delete_animal_pic" disabled onclick="return false;">Kép törlése</a>
                </div>
            </div>
        </div>
      </div>

      <div class="container pt-5">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4 border p-4">
            <form method="post">
              <div class="form-group">
                <label for="animalname">Név:</label>
                <input type="text" class="form-control" name="animalname" placeholder="' . $row['Name'] . '">
              </div>
              <div class="form-group">
                <label for="specie">Faja:</label>
                <p class="form-control" name="specie">'. $row['Specie'] . '</p>
              </div>
              <div class="form-group">
              <label for="gender">Neme:</label>
              <p class="form-control" name="gender">' . $row['Sex'] . '</p>
              </div>
              <div class="form-group">
              <label for="description">Leírás:</label>
              <textarea type="text" rows="4" class="form-control" name="description" placeholder="' . $row['Description'] . '"></textarea>
              </div>
              <div class="form-group">
              <label for="vaccines">Oltások:</label>
              <textarea type="text" rows="4" class="form-control" name="vaccines" placeholder="' . $row['Vaccines'] . '"></textarea>
              </div>
              <div class="form-group">
              <label for="exercise">Mozgás:</label>
              <textarea type="text" rows="4" class="form-control" name="exercise" placeholder="' . $row['NeedofExercise'] . '"></textarea>
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
    header("Location: admin.php?a_allat_nem_letezik#animalviewer");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Otthont keresők - Admin állat szerkesztés</title>
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
    <p class="centeralign bold underline bigfont">Állat szerkesztése</p>

    <?php echo $html; ?>
    <br>

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