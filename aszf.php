<?php
  session_start();

  include("functions.php");
  include("dbconnect.php");

  $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
  $user_data = check_login($con);
  $is_admin = is_admin($con);
  $is_moderator = is_moderator($con);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otthont keresők - ÁSZF</title>
    <link rel="stylesheet" href="css/tothetop.css">
    <link rel="stylesheet" href="css/def.css">
    <link href="css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
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
            <a class="nav-link txt-white active" href="#">ÁSZF</a>
          </li>
          <li class="nav-item">  
            <a class="nav-link txt-white" href="index.php">Főoldalra</a>
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

  <br><br>
  <div class="container">
    <h1 class="text-danger">Általános Szerződési Feltételek - Otthon Kereső oldal</h1>
    <br><br>
    <ol class="text-warning text-justify">
    <li class="lead"><b>Bevezetés:</b></li>
    <ul>
      <li>
Az Otthon Kereső oldal (a továbbiakban: "Oldal") 
általános szerződési feltételei (a továbbiakban: "ÁSZF") 
meghatározzák a felhasználók (a továbbiakban: "Felhasználók") és az 
Oldal közötti jogviszony feltételeit. 
      </li>
      <li>
A felhasználó elfogadja azt, hogy a regisztráció után a profilján bárki megtekintheti az e-mail címét, hogy a kapcsolatot legalább e-mailben feltudják vele venni.
      </li>
      <li>
Az Oldal használatával a Felhasználó elfogadja az ÁSZF-ben foglaltakat.
      </li>
    </ul>

<li class="lead"><b>Regisztráció:</b></li>
      <ul>
<li>Az Oldal teljes használatához regisztráció szükséges. </li>
<li>A regisztráció során a Felhasználó köteles valós adatokat megadni.</li>
<li>
Az Oldal fenntartja a jogot a regisztráció elutasítására, illetve a Felhasználói fiók törlésére, 
ha a Felhasználó valótlan adatokat ad meg, vagy az ÁSZF-ben foglaltakat megszegi.
</li>
        </ul>

<li class="lead"><b>Szolgáltatások:</b></li>
<ul>
  <li>
Az Oldal ingyenesen elérhető szolgáltatásokat nyújt, amelyek lehetővé teszik a Felhasználók számára, 
hogy állatokat keressenek, illetve hirdessenek. 
        </li>
        <li>
Az Oldal nem vállal felelősséget az állatok állapotáért, 
illetve az állatokkal kapcsolatos tranzakciókért.
        </li>
        </ul>

<li class="lead"><b>Felhasználói tartalom:</b></li>
        <ul>
<li>A Felhasználók által az Oldalra feltöltött tartalomért a Felhasználók felelősek. </li>
<li>
Az Oldal fenntartja a jogot a Felhasználói tartalom ellenőrzésére, illetve azok törlésére, 
ha azok sértik az ÁSZF-ben foglaltakat, vagy a jogszabályokat.
        </li>
        </ul>

<li class="lead"><b>Adatvédelem:</b></li>
        <ul>
<li>Az Oldal adatvédelmi nyilatkozata megtalálható az Oldal honlapján.</li>
<li>
Az Oldal kötelezi magát, hogy a Felhasználók adatait bizalmasan kezeli, 
és azokat harmadik félnek nem adja át.
        </li>
        </ul>

<li class="lead"><b>Felelősség:</b></li>
<ul>
<li>
  Az Oldal nem vállal felelősséget az állatok állapotáért, 
illetve az állatokkal kapcsolatos tranzakciókért.
        </li>
        <li>
Az Oldal nem vállal felelősséget a Felhasználók által feltöltött tartalomért, 
illetve azok felhasználásából eredő károkért.
        </li>
        </ul>

<li class="lead"><b>Jogvita:</b></li>
<ul>
  <li>
Az ÁSZF-ben foglaltakból eredő jogviták esetén az Oldal és a Felhasználók kötelezik magukat, 
hogy azokat békés úton rendezik. 
        </li>
        <li>
Amennyiben a jogvitát nem sikerül békésen rendezni, 
az ügyben a Magyarországon illetékes bíróságok járnak el.
        </li>
</ul>

<li class="lead"><b>Módosítások:</b></li>
<ul>
<li>Az Oldal fenntartja a jogot az ÁSZF módosítására.</li>
<li>Az ÁSZF módosításairól az Oldal a Felhasználókat az Oldal honlapján tájékoztatja.</li>
</ul>

<li class="lead"><b>Kapcsolat:</b></li>
<ul>
  <li>
Az Oldal és a Felhasználók közötti kapcsolatot az Oldal honlapján található elérhetőségeken lehet felvenni.
        </li>
        <li> 
Az Oldal kötelezi magát, hogy a Felhasználók kérdéseire, észrevételeire a lehető leghamarabb válaszol.
        </li>
<ul>
    </ol>
  </div>
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
</body>
</html>
