<?php
session_start();
    
include("functions.php");

$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_data = check_login($con);
$is_admin = is_admin($con);
$is_moderator = is_moderator($con);
if (!$logged_in) {
header("Location: index.php");
exit;
}

$main_user_id = $_SESSION['userID'];
$animal_id = $_GET['animal_id'];

$arrName = array('animalname','animalspecie','gender','mozgas','ivar','szobatisztasag','chipp','oltas','leiras','befogadott','birthday','animal_pic','befogadott','vegbefogadott','befogadta');
$arrPlace = array("Name","Specie","Sex","NeedofExercise","CanBaby","HouseTrained","Chipped","Vaccines","Description","DateofBorn","Picture","TemporaryHome","FoundHome","userID");
$arrItems = [];
$arrValues = [];
$user_id = $main_user_id;

if($_SERVER['REQUEST_METHOD'] == "POST")
{
  for ($i=0; $i < count($arrName); $i++) {
        
    if(!empty($_POST[$arrName[$i]])){
      if(strcmp($arrPlace[$i], "userID")==0){
        array_push($arrItems, 'userID');
        array_push($arrValues, $_POST[$arrName[$i]]);
      }
      elseif(strcmp($arrName[$i], "birthday")==0){
        if(isset($_POST[$arrPlace[$i]]))
        $now = new DateTime();
        $birthdayDate = DateTime::createFromFormat('Y-m-d', $_POST[$arrName[$i]]);
        $ageInterval = $birthdayDate->diff($now);
        $bthdate = $birthdayDate->format('Y-m-d');
        $age = $ageInterval->format('%y');
        array_push($arrItems, $arrPlace[$i]);
        array_push($arrValues, $bthdate);
        array_push($arrItems, "Age");
        array_push($arrValues, $age);
      }
      else{
        array_push($arrItems, $arrPlace[$i]);
        array_push($arrValues, $_POST[$arrName[$i]]);
      }
    }
    elseif (strcmp($arrName[$i], "animal_pic")==0) {
      if(isset($_FILES['image']['name']) && $_FILES[$arrName[$i]]['error'] == UPLOAD_ERR_OK){
        array_push($arrItems,$arrPlace[$i]);
        array_push($arrValues,imgUpload($arrName[$i]));
      }
    }
  }

  if(!empty($arrItems)&&!empty($arrValues)){
    DB::UPDATE(
      "animalinfo",
      $arrItems,
      "userID",
      $arrValues,
      $main_user_id
    );
  }
  header("Location: hirdeteseid.php?sikeres_frissites");
}

$query = "SELECT * FROM animalspecies";
$spiecesres = mysqli_query($con, $query);
$htmlspecie = '<option value="">Válassz</option>';
while ($row = mysqli_fetch_assoc($spiecesres)) {
    $htmlspecie .= '<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
}

$sql = "SELECT userID, Name, Email FROM users";
$result = mysqli_query($con, $sql);
$users_options = '';
while ($row2 = mysqli_fetch_assoc($result)){
    $users_options .= '
    <option value="'.$row2['userID'].'">'.$row2['Name'].' - '.$row2['Email'].'</option>
    ';
}
               
$sql = "SELECT Name,Picture,Specie,Sex,DateofBorn,Picture,NeedofExercise,CanBaby,HouseTrained,Chipped,Vaccines,Description,FoundHome,TemporaryHome,Age FROM animalinfo WHERE userID = '$main_user_id' AND animalID = '$animal_id'";
$result = mysqli_query($con, $sql);
$html = '';
while ($row = mysqli_fetch_assoc($result)) {
$html .= '
<div class="container rounded bg-white mt-5 mb-5">
            <div class="row">
                <div class="col-md-3 border-right"></div>
                <div class="col-md-5 border-right">
                    <div class="p-3 py-5">
                        <div>
                            <h4 class="text-right text-center"> '.$row['Name'].' kisállat szerkesztése</h4>
                        </div>
                        <div class="row mt-2"></div>
                        <div class="row mt-3">
                            <p>Kép (a kisállatról):</p> 
                            <img src="'.$row['Picture'].'" alt="'.$row['Name'].'">
                            <input type="file" name="animal_pic">
                            <div class="col-md-12">
                                <label class="labels">Állat neve:</label>
                                <input type="text" class="form-control" name="animalname" placeholder="'.$row['Name'].'">
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Állatfaja:</label>
                            </div>
                            <select name="animalspecie">
                                '.$htmlspecie.'
                            </select> 
                            <div class="col-md-12">
                                <label class="labels">Állat neme:</label>
                            </div>
                            <select name="gender">
                                <option value="'.$row['Sex'].'">'.$row['Sex'].'</option>  
                                <option value="Hím">Hím</option>  
                                <option value="Nőstény">Nőstény</option>  
                                <option value="Egyéb">Egyéb</option>
                            </select> 
                            <div class="col-md-12">
                                <label class="labels">Születésnap:</label>
                                <input type="date" id="date-input" class="form-control" name="birthday" min="1900-01-01" max="" onchange="calculateTimeDifference()">
<script>
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("date-input").max = today;
</script>

                                <p id="output"></p>
                                <label class="labels">Korábban kora: '.$row['Age'].'</label>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Mozgásigény:</label>
                                <input type="text" class="form-control" name="mozgas" placeholder="'.$row['Description'].'">
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Ivaros-e:</label>
                            </div> 
                            <select name="ivar">
                                <option value="">Válassz</option>  
                                <option value="Igen">Igen</option>  
                                <option value="Ivartalanítva">Ivartalanítva</option>
                            </select> 
                            <div class="col-md-12">
                                <label class="labels">Szobatisztaság:</label>
                            </div>
                            <select name="szobatisztasag">
                                <option value="">Válassz</option>  
                                <option value="Igen">Igen</option>  
                                <option value="Nem">Nem</option>
                            </select> 
                            <div class="col-md-12">
                                <label class="labels">Chippeltetve ?</label>
                            </div>
                            <select name="chipp">
                                <option value="">Válassz</option>  
                                <option value="Igen">Igen</option>  
                                <option value="Nincs">Nincs</option>
                            </select> 
                            <div class="col-md-12">
                                <label class="labels">Oltások:</label>
                                <input type="text" class="form-control" name="oltas" placeholder="'.$row['Vaccines'].'">
                            </div>
                            <br>
                            <p></p>
                            <br>
                            <div>
                                <p class="text-center">Leírás (a kisállat bemutatása)</p>
                                <textarea type="text" rows="4" class="textareastyle petprof" name="leiras" maxlength="2000" placeholder="'.$row['Description'].'"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Ideiglenesen befogadták ?</label>
                            </div>
                            <select name="befogadott">
                                <option value="">Válassz</option>  
                                <option value="Igen">Igen</option>  
                                <option value="Nem">Nem</option>
                            </select>
                            <div class="col-md-12">
                            <label class="labels">Valaki véglegesen befogadta ?</label>
                            </div>
                            <select name="vegbefogadott">
                                <option value="">Válassz</option>  
                                <option value="Igen">Igen</option>  
                                <option value="">Nem</option>
                            </select>
                            <label class="labels">Kicsoda fogadta be ?</label>
                            <select name="befogadta">
                                <option value="">Válassz</option>  
                                '.$users_options.'
                            </select>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="submit">Beküldés</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
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

    <title>Otthon Kereső - Hirdetés szerkesztése</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="img/icons/house.png" type="image/png">
    
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
            <a class="nav-link text-white" aria-current="page" href="index.php">Főoldal</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="search.php">Kereső</a>
          </li>
          <?php if ($logged_in): ?>
          <li class="nav-item">
            <a class="nav-link text-white" href="hirdeteseid.php">Hirdetéseid</a>
          </li> 
          <?php endif; ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="contact.php" data-bs-toggle="dropdown" aria-expanded="false">Kapcsolat</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item text-dark" href="contact.php#team">Készítők</a></li>
              <li><a class="dropdown-item text-dark" href="contact.php#contact">Írj nekünk</a></li>
              <li><a class="dropdown-item text-dark" href="contact.php#bugreport">Hiba jelentése</a></li>
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

  <?php if (isset($_GET['hianyzoadatok'])) {echo "<p class='text-warning text-center bg-danger'><b> Hiányos adatok! Kérlek ügyelj arra, hogy minden ki legyen töltve!</b></p>";}?>

    <form method="post" enctype="multipart/form-data">
        <?php echo $html; ?>
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

    <script src="js/exactage.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>