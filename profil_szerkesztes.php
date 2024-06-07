<?php
  session_start();

  include("functions.php");
  
  $user_data = check_login();
  $is_admin = is_admin();
  $is_moderator = is_moderator();
  $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

  if (!$logged_in) {
    header("Location: index.php");
    exit;
  }

  $html = '';
  $user_id = $_SESSION['userID'];
  $result = DB::GET(
    "SELECT Email, Name, Description, PhoneNumber, ProfilePicture, Thumbnail FROM users WHERE userID = ?",
    array($user_id));

  if(mysqli_num_rows($result) == 1){
    $row = mysqli_fetch_assoc($result);
    $arrName = array('username','phonenumber','description',"profile_pic",'thumbnail_pic');
    $arrPlace = array("Name","PhoneNumber","Description","ProfilePicture","Thumbnail");
    $arrItems = [];
    $arrValues = [];

    if(isset($_POST['submit']))
    {
      for ($i=0; $i < count($arrName); $i++) {
        dump($arrName[$i]);
        if(!empty($_POST[$arrName[$i]])){
          
          if(strcmp($arrItems[$i], "Password")==0){
            $arrItems[] = $arrPlace[$i];
            $arrValues[] = password_hash($_POST[$arrName[$i]],PASSWORD_DEFAULT);
          }
          else{
            $arrItems[] = $arrPlace[$i];
            $arrValues[] = $_POST[$arrName[$i]];
          }
        }
        elseif (strcmp($arrName[$i], "profile_pic")==0 || strcmp($arrName[$i], "thumbnail_pic")==0) {
          
          if(!strcmp($_FILES[$arrName[$i]]['name'], "")==0 && $_FILES[$arrName[$i]]['error'] == UPLOAD_ERR_OK){
            $arrItems[] = $arrPlace[$i];
            $arrValues[] = imgUpload($arrName[$i]);
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
      header("Location: profile.php");
    }
    else {
      $html .= '
            <div class="row">
                <div class="col-md-4">
                    <div class="profile-img" style="background-image: url('.$row['Thumbnail'].');">
                        <img src="'.$row['ProfilePicture'].'" alt="'.$row['Name'].'" />
                        <p></p>
                    </div>
                    <div class="col-md-2" style="all: initial;">
                        <br>
                        
                        Profilkép: <input type="file" name="profile_pic"><br><br>
                        Borítókép: <input type="file" name="thumbnail_pic"><br><br>
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
                                            <p><input type="text" class="form-control" name="username" placeholder="'.$row['Name'].'"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Email:</label>
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
                                        <p><input type="text" class="form-control" name="phonenumber" placeholder="'.$row['PhoneNumber'].'"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="leiras mt-2">
                    <p class="text-center">Magamról:</p>
                    <p class="text-center"><input type="text" class="form-control" name="description" placeholder="'.$row['Description'].'"></p>
                </div>
                <div class="text-center mt-2">
                    <button class="btn btn-secondary" type="submit" name="submit">Mentés</button>
                </div>
                <div class="text-center mt-3">                   
                    <a class="btn btn-primary" href="profil_torles.php">Profil Törlése</a>
                    <a class="btn btn-danger" href="password_edit.php">Jelszó szerkesztése</a>
                </div>
            </div>
    ';
    }
  }

?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otthon Kereső - Profil szerkesztés</title>
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
  <?php if (isset($_GET['hianyzoadatok'])) {echo "<p class='text-warning text-center bg-danger'><b> Hiányos adatok! Kérlek ügyelj arra, hogy minden ki legyen töltve!</b></p>";}?>
    <form method="post" enctype="multipart/form-data">
        <div class="bg">
            <div>
                <br>
                <br>
            </div>
            <div class="container emp-profile">
                <?php echo $html ?>
            </div>
        </div>
   </form>

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