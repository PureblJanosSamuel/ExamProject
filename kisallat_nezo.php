<?php
session_start();
    
include("functions.php");

$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$is_admin = is_admin($con);
$is_moderator = is_moderator($con);
$user_data = check_login($con);

$animal_id = $_GET['animal_id'];
$result = DB::GET(
    "SELECT * FROM animalinfo WHERE animalID = ?",
    array($animal_id)
);
$result2 = DB::GET(
    "SELECT animalinfo.*, animalspecies.Name 
    FROM animalinfo 
    INNER JOIN animalspecies ON animalinfo.Specie = animalspecies.ID 
    WHERE animalinfo.animalID = ?",
    array($animal_id)
);
$speciename = '';
while ($row2 = mysqli_fetch_assoc($result2)) {
    $speciename .= '
    <p>'.$row2['Name'].'</p>
    ';
}

$result3 = DB::GET(
    "SELECT users.Name
FROM animalinfo
JOIN users ON animalinfo.userID = users.userID
WHERE animalinfo.animalID = ?;",
array($animal_id)
);
$username = '';
while ($row3 = mysqli_fetch_assoc($result3)) {
    $username .= ''.$row3['Name'].'';
}

$html = '';
while ($row = mysqli_fetch_assoc($result)) {
        $html .= '
        <form method="post" enctype="multipart/form-data">
        <div class="container rounded bg-white mt-5 mb-5">
        <br>
        <div class="row">
            <div>
                <h4 class="text-right text-center">'.$row['Name'].' kisállat profil lapja</h4>
                <br>
                <br>
            </div>
        </div>
            <div class="row mx-auto">
                <!-- KÉP A KISÁLLATRÓL -->
                <div class="col-md-4 border-right">
                    
                <p>Kép (a kisállatról)</p> 
                <img src="'.$row['Picture'].'" alt="'.$row['Name'].'" title="'.$row['Name'].'" style="max-width: 300px;" class="border">

                </div>


                    <div class="col-md-4 border-right">
                        <div class="p-3 py-5">
                            
                            <div class="row mt-3">
                            
                            <div class="tab-pane fade show active " id="home" role="tabpanel"
                                            aria-labelledby="home-tab" style="color:black;background-color:white;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Állat neve:</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>'.$row['Name'].'</p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Állat jelenlegi tulajdonosa:</label>
                                                </div>
                                                <div class="col-md-6"><br>
                                                    <p><a href="profil_nezo.php?user_id='.$row['userID'].'" title="'.$username.' profiljának megtekintése" class="text-decoration-none">'.$username.'</a></p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Állatfaja:</label>
                                                </div>
                                                <div class="col-md-6">
                                                    '.$speciename.'
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Állat neme:</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>'.$row['Sex'].'</p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Születésnap:</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>'.$row['DateofBorn'].'</p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Ivaros-e:</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>'.$row['CanBaby'].'</p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Szobatisztaság:</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>'.$row['HouseTrained'].'</p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Chippeltetve:</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>'.$row['Chipped'].'</p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Ideiglenesen befogadták: </label>
                                                </div>
                                                <div class="col-md-6"><br>
                                                    <p>'.$row['TemporaryHome'].'</p>
                                                </div>
                                            </div>


                                        </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                                <div class="leiras">
                                <p class="text-center">Kora:</p>
                                    <p class="text-center">'.$row['Age'].' év</p>
                                </div>
                                <p></p>
                                <div class="leiras">
                                <p class="text-center">Mozgásigény:</p>
                                    <p class="text-center">'.$row['NeedofExercise'].'</p>
                                </div>
                                <p></p>
                                <div class="leiras">
                                <p class="text-center">Oltások:</p>
                                    <p class="text-center">'.$row['Vaccines'].'</p>
                                </div>
                                <p></p>
                                <div class="leiras">
                                    <p class="text-center">Kisállat bemutatása:</p>
                                    <p class="text-center">'.$row['Description'].'</p>
                                </div>
                                <br>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    </form>
        ';
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Otthon Kereső - Kisállat lap</title>

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
  
  <?php echo $html; ?>

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