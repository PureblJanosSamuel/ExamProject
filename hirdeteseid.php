<?php
session_start();
    
include("functions.php");
include("dbconnect.php");

$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_data = check_login($con);
if (!$logged_in) {
header("Location: index.php");
exit;
}

$user_id = $_SESSION['userID'];
$is_admin = is_admin();
$is_moderator = is_moderator();
$sql = "SELECT animalID,Name,Picture,Description,Age FROM animalinfo WHERE userID = '$user_id'";
$result = mysqli_query($con, $sql);

$html = '';
while ($row = mysqli_fetch_assoc($result)) {
    $html .= '<div class="col">
                  <div class="card shadow-sm">
                  <img class="bd-placeholder-img card-img-top" width="100%" height="225" src="'.$row['Picture'].'" focusable="false">
                      <rect width="100%" height="100%" fill="#55595c"/>
                  </img>
      
                      <div class="card-body">
                          <p class="card-text text-truncate"><b>'.$row['Name'].'</b><br>'.$row['Description'].'</p>
                          <div class="d-flex justify-content-between align-items-center">            
                              <div class="btn-group">
                                  <a href="kisallatlap_szerkesztes.php?animal_id='.$row['animalID'].'">
                                      <button type="button" class="btn btn-sm bg-primary btn-outline-warning"><b>Szerkesztés</b></button>
                                  </a>
                              </div>                                       
                              <p class="text-primary">Kora: '.$row['Age'].'</p>
                              <div class="btn-group">
                                  <a href="allat_torles.php?animal_id='.$row['animalID'].'">
                                      <button type="button" class="btn btn-sm bg-warning btn-outline-danger"><b>Törlés</b></button>
                                  </a>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>';
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Otthon kereső - Hirdetéseid</title>

    <link href="css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="img/icons/house.png" type="image/png">
    <link rel="stylesheet" href="css/tothetop.css">
    <link rel="stylesheet" href="css/def.css">
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
            <a class="nav-link txt-white" aria-current="page" href="index.php">Főoldal</a>
          </li>
          <li class="nav-item">
            <a class="nav-link txt-white" href="search.php">Kereső</a>
          </li>
          <?php if ($logged_in): ?>
          <li class="nav-item">
            <a class="nav-link txt-white active" href="hirdeteseid.php">Hirdetéseid</a>
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
        <a class="nav-link text-danger" href="admin.php">Admin felület</a>&nbsp;|&nbsp;
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

    <div class="album py-5 container text-center">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

            <?php echo $html; ?>

            <a href="kisallatlap.php" class="btn btn-info btn-lg ">
                <br>
                <br>
                <br>
                <br>
                <br>
                +<br> 
                Új hirdetés
                <br>
                <br>
                <br>
                <br>
                <br>
            </a>
        </div>
    </div>

    <?php if (isset($_GET['sikeresen_feltoltotted'])) {echo "<p class='text-warning text-center bg-success'><b>Állat sikeresen feltöltve!</b></p>";}?>
    <?php if (isset($_GET['allat_sikeresen_torolve'])) {echo "<p class='text-danger text-center bg-warning'><b>Állat sikeresen törölve!</b></p>";}?>
    <?php if (isset($_GET['sikeres_frissites'])) {echo "<p class='text-warning text-center bg-primary'><b>Állat sikeresen frissítve!</b></p>";}?>
    <?php if (isset($_GET['sikeres_fogadas'])) {echo "<p class='text-warning text-center bg-primary'><b>Állat sikeresen új gazdára talált!</b></p>";}?>

    <br><br><br>

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