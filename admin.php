<?php
  session_start();
  include("functions.php");

  $user_data = check_login();
  $is_admin = is_admin();
  $is_moderator = is_moderator();
  $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
if (!is_admin()) {
  header("Location: index.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Otthont keresők - Admin felület</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/tothetop.css">
    <link rel="icon" href="img/icons/house.png" type="image/png">
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

    <h1 class="centeralign bold underline biggerfont">Admin felület</h1>

    <nav class="navbar navbar-expand-lg navbar-dark bg-blue">
      <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">ADMINISZTRÁTORI NAVIGÁCIÓS FELÜLET</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto mb-2 mb-sm-0">
            <li class="nav-item">
              <a class="nav-link txt-white" href="#userviewer">Felhasználók</a>
            </li>
            <li class="nav-item">
              <a class="nav-link txt-white" href="#usercreate">Felhasználó létrehozás</a>
            </li>
            <li class="nav-item">
              <a class="nav-link txt-white" href="#animalviewer">Hirdetések</a>
            </li>
            <li class="nav-item">
              <a class="nav-link txt-white" href="#animalcreate">Hirdetés létrehozás</a>
            </li>
            <li class="nav-item">
              <a class="nav-link txt-white" href="#giverank">Rang adás</a>
            </li>
            <li class="nav-item">
              <a class="nav-link txt-white" href="#derank">Rang elvétel</a>
            </li>
            <li class="nav-item">
              <a class="nav-link txt-white" href="#bugs">Hibajelentések</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <br>
    <hr>
    <br>

    <section id="userviewer">
        <p class="centeralign bold underline bigfont">Felhasználók</p>
        <?php if (isset($_GET['felhasznalo_torolve'])) {echo "<br><p class='alert text-warning text-center bg-success'><b>Felhasználó sikeresen törölve!</b></p><br>";}?>
            <div class="album py-5 container text-center">
              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-6 g-3">

                <?php read_users(); ?>

              </div>
            </div>
            
    </section>

    <br>
    <hr>
    <br>

    <section id="usercreate">
    <p class="centeralign bold underline bigfont">Felhasználó létrehozása</p>
    <div class="album py-5 container text-center">
      
    <?php if (isset($_GET['felhasznalo_sikeresen_letrehozva'])) {echo "<p class='alert text-warning text-center bg-success'><b>Felhasználó véletlenszerű adatokkal sikeresen létrehozva!</b></p>";}?>
      <form action="admin_API.php" method="post">
        <input type="hidden" name="action" value="create_random_user">
        <button class="btn btn-secondary" type="submit">Felhasználó létrehozása véletlenszerű adatokkal</button>
      </form>
    </div>
    </section>

    <br>
    <hr>
    <br>

    <section id="animalviewer">
        <p class="centeralign bold underline bigfont">Hirdetések</p>
        <?php if (isset($_GET['allat_sikeresen_torolve'])) {echo "<p class='alert text-warning text-center bg-success'><b>Állat sikeresen törölve!</b></p>";}?>
            <div class="album py-5 container text-center">
              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-6 g-3">

                <?php read_animals(); ?>

              </div>
            </div>
            
    </section>

    <br>
    <hr>
    <br>

    <section id="animalcreate">
      <p class="centeralign bold underline bigfont">Állat létrehozása</p>
      <div class="album py-5 container text-center">

      <?php if (isset($_GET['allat_sikeresen_letrehozva'])) {echo "<br><p class='alert text-warning text-center bg-success'><b>Állat véletlenszerű adatokkal sikeresen létrehozva!</b></p><br>";}?>
        <form action="admin_API.php" method="post">
        <input type="hidden" name="action" value="create_random_animal">
        <button class="btn btn-secondary" type="submit">Állat létrehozása véletlenszerű adatokkal</button>
        </form>
      </div>
    </section>
        
    <br>
    <hr>
    <br>

    <section id="giverank">
      <p class="centeralign bold underline bigfont">Rang adás</p>
      <?php if (isset($_GET['rang_sikeresen_hozzaadva'])) {echo "<br><p class='alert text-warning text-center bg-success'><b>Rang sikeresen adva!</b></p><br>";}?>
      <?php if (isset($_GET['rang_hiba'])) {echo "<br><p class='alert text-warning text-center bg-success'><b>Hiba történt a rang adása közben!</b></p><br>";}?>
      <div class="album py-5 container text-center">

          <?php rangformhtml(); ?>
          
      </div>
    </section>    

    <br>
    <hr>
    <br>

    <section id="derank">
      <p class="centeralign bold underline bigfont">Rang elvétel</p>
      <div class="album py-5 container text-center">
      <?php if (isset($_GET['rang_sikeresen_elveve'])) {echo "<br><p class='alert text-warning text-center bg-success'><b>Rang sikeresen elvéve!</b></p><br>";}?>
          <?php ranged_usershtml(); ?>

      </div>
    </section>    

    <br>
    <hr>
    <br>

    <section id="bugs">
      <p class="centeralign bold underline bigfont">Hibajelentések</p>
      <?php if (isset($_GET['hiba_torolve'])) {echo "<br><p class='alert text-warning text-center bg-success'><b>Hiba törölve!</b></p><br>";}?>
        <div class="album py-5 container text-center">

          <?php read_bugs(); ?>

        </div>
    </section>

    <button onclick="topFunction()" id="scroll-to-top" title="Irány az oldal teteje">▲</button>

    <script src="js/tothetop.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>