<?php
session_start();
    
include("functions.php");

$user_data = check_login();
$is_admin = is_admin();
$is_moderator = is_moderator();
  
$logged_in =  isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
?>

<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Otthont keresők - Kereső</title>

  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/search.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/tothetop.css">
  <link rel="icon" href="img/icons/search.png" type="image/png">

</head>
<body onscroll="scrollFunction()">


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
            <a class="nav-link txt-white active" aria-current="page" href="search.php">Kereső</a>
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

  <div class="container">
    <div class="row">
      <!-- BEGIN SEARCH RESULT -->
      <div class="col-md-12">
        <div class="grid search">
          <div class="grid-body">
            <div class="row">
              <!-- BEGIN FILTERS -->
              <div class="col-md-3">
                <h2 class="grid-title"><i class="fa fa-filter"></i> Szűrők</h2>
                <hr>
                
                <!-- BEGIN FILTER BY CATEGORY -->
                <form method="GET">
                  <ul class="list-unstyled ps-0">
                    <li class="mb-1">
                      <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#home-collapse">
                        Állatfajok:
                      </button>
                      <div class="collapse show" id="home-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small" id="felsor">
                          <?php
                            $options = "SELECT * FROM animalspecies";
                            $options_run = mysqli_query($con, $options);

                            if(mysqli_num_rows($options_run) > 0){
                              
                              foreach($options_run as $animallist){
                                  $checkeds = [];
                                  if(isset($_GET['allatok'])){
                                    $checkeds = $_GET['allatok'];
                                  }

                                ?>
                                  <li class="checkbox"><label><input type="checkbox" class="icheck" name="allatok[]" value="<?= $animallist['ID']; ?>" 
                                  <?php if (in_array($animallist['ID'], $checkeds)) { echo "checked"; } ?>
                                  > <?= $animallist['Name']; ?></label></li>
                                <?php
                              }
                            }
                            else{
                              echo "No data!";
                            }
                          ?>
                        </ul>
                      </div>
                    </li>
                    <li class="mb-1">
                      <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" data-bs-toggle="collapse" data-bs-target="#dashboard-collapse">
                        Állatnem:
                      </button>
                      <div class="collapse show" id="dashboard-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                          <?php
                              $names = array('Hím','Nőstény','Egyéb');
                            foreach ($names as $n) {
                              $used = [];
                              if(isset($_GET['ivar'])){
                                $used = $_GET['ivar'];
                              }
                              ?>
                                <li class="checkbox"><label><input type="checkbox" class="icheck" name="ivar[]" value="<?= $n ?>"
                                <?php if(in_array($n, $used)) { echo "checked"; } ?>
                                ><?= $n?></label></li>
                              <?php
                            }
                          ?>
                        </ul>
                      </div>
                    </li>
                    
                  </ul>
                
                  <!-- END FILTER BY CATEGORY -->
                  
                  <div class="padding"></div>
                  
                  <!-- BEGIN FILTER BY DATE -->
                  <h4>Kor:</h4>
                  <?php
                    $age= 0;
                    if(isset($_GET['kor'])){
                      $age = $_GET['kor'];
                    }
                  ?>

                   <input type="number" name="kor" id="agebox" min="0" value="<?= $age ?>">

                  <button type="submit" class="btn btn-primary">Keress</button>
                </form>
                <!-- END FILTER BY DATE -->
                
                <div class="padding"></div>
                
                
              </div>
              <!-- END FILTERS -->
              <!-- BEGIN RESULT -->
              <div class="col-md-9">
                <h2>Keresési Eredmények:</h2>
                <hr>
                
                <div class="padding"></div>
                
                <!-- BEGIN TABLE RESULT -->
                <div class="table-responsive">
                  <table class="table table-hover">
                    <tbody id="ads">
                    <?php
                    
                      $SexSearch = [];
                      if(isset($_GET['ivar'])){
                        $SexSearch = $_GET['ivar'];
                      }

                      if(isset($_GET['allatok'])){
                        $animalsneeded = [];
                        $animalsneeded = $_GET['allatok'];
                        foreach($animalsneeded as $rowanimal){
                            $result = kiir("SELECT * FROM animalinfo WHERE Specie IN ('$rowanimal') AND FoundHome = 'Nem'", $SexSearch, $age);
                        }
                      }
                      else{
                        $result = kiir("SELECT * FROM animalinfo WHERE FoundHome = 'Nem'", $SexSearch, $age);
                      }
                    ?>
                    </tbody>
                  </table>
                </div>
                <!-- END TABLE RESULT -->
                
                <!-- BEGIN PAGINATION -->
                </ul>
                <!-- END PAGINATION -->
              </div>
              <!-- END RESULT -->
            </div>
          </div>
        </div>
      </div>
      <!-- END SEARCH RESULT -->
    </div>
    </div>
  
    <button onclick="topFunction()" id="scroll-to-top" title="Irány az oldal teteje">▲</button>

    <footer class="container-fluid py-3 bg-blue footer">
      <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item"><a href="index.php" class="nav-link px-2 txt-white">Főoldal</a></li>
        <li class="nav-item"><a href="search.php" class="nav-link px-2 txt-white">Kereső</a></li>
        <li class="nav-item"><a href="aszf.php" class="nav-link px-2 txt-white">ÁSZF</a></li>
        <li class="nav-item"><a href="contact.php" class="nav-link px-2 txt-white">Kapcsolat</a></li>
      </ul>
      <p class="text-center text-muted">Otthon Kereső © 2022-2023 Company, Inc</p>
    </footer>


  <script type="text/javascript">
    $(function() {
        $('#datepicker').datepicker();
    });

    $(function() {
        $('#datepickersecond').datepicker();
    });
  </script>
  <script src="js/tothetop.js"></script>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="js/bootstrap-datepicker.min.js"></script>
</body>
</html>