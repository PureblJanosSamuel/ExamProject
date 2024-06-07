<?php
    session_start();

    include("functions.php");

    $user_data = check_login();
    $is_admin = is_admin();
    $is_moderator = is_moderator();
    $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Az Otthon Kereső egy olyan weboldal, amely lehetőséget kínál a felhasználóknak arra, hogy megtalálják a tökéletes otthont azoknak a kedvenceiknek, amelyekről sajnos már nem tudnak gondot viselni. Az oldal egy interaktív platform, amely lehetővé teszi a felhasználók számára, hogy hirdetéseket helyezzenek el az elérhető állatikról és hogy böngésszenek a rendelkezésre álló háziállatok között. Az oldal felhasználóbarát felülete lehetővé teszi, hogy az érdeklődők kényelmesen böngésszenek a különböző kategóriákban elérhető állatok között. A felhasználók az oldalon regisztrálhatnak, és létrehozhatnak profilokat, amelyek segítségével kapcsolatba léphetnek az állatok tulajdonosaival. Az Otthon Kereső egy jószívű kezdeményezés, amelynek célja az, hogy segítsen azoknak az állatoknak, amelyeknek az eddigi gazdájuk már nem tud gondot viselni. Az oldal széles körű közösségi támogatást élvez, és az emberek egyre inkább felismerik, hogy az állatoknak is joguk van a boldog élethez. Ha Ön is szeretne segíteni, vagy egyszerűen csak szeretné megtalálni az új házi kedvencét, akkor látogasson el az Otthon Kereső weboldalra, és csatlakozzon az állatbarátok közösségéhez!">
    <meta name="keywords" content="Otthon Kereső Állatok, Otthon Kereső, Háziállatok keresése, Megunt állatok, Állatok ingyen elvihetők, Állatmentő kezdeményezés, Kedvenc új otthona, Kutyák eladók, Macskák eladók, Kisállatok eladók, Gazdi kereső, Állatbarát közösség, Állatvédelem, Örökbefogadás, Segítség a kóbor állatoknak, Házikedvencek, Kutyamentés, Állatvédő szervezetek, Eltérített állatok, Állatvédelmi programok">
    <meta name="author" content="Dömös László, Gulyás András János, Purebl János Sámuel">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otthon Kereső - Főoldal</title>

    <link href="css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="img/icons/house.png" type="image/png">
    <link rel="stylesheet" href="css/tothetop.css">
    <link rel="stylesheet" href="css/def.css">

</head>
<body onscroll="scrollFunction()">

<nav class="navbar navbar-expand-lg navbar-dark bg-green" aria-label="Third navbar example">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Otthon kereső</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample03" aria-controls="navbarsExample03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample03">
        <ul class="navbar-nav me-auto mb-2 mb-sm-0">
          <li class="nav-item">
            <a class="nav-link txt-white active" aria-current="page" href="#">Főoldal</a>
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

  <?php if (isset($_GET['fiok_sikeresen_torolve'])) {echo "<p class='text-danger text-center bg-warning'><b>Felhasználó sikeresen törölve!</b></p>";}?>
  
  <main>
    <div class="container-fluid py-5 bg-dark">
      <div class="txtcontainer">
      <img src="img/cica.jpg" alt="" class="bg-img">

      <div class="centeredtxt">
        
          <h1 class="title txt-white txt-bg-dark">Otthon kereső</h1>
          <p class="motto txt-white txt-bg-dark">"Az otthon mindenhol vár"</p>
          <h3 class="title txt-white txt-bg-dark">Üdvözlünk az oldalon</h3>

      </div>
  </div>
      <div class="container-fluid">
      </div>
    </div>
  
    <div class="album py-5 bg-green">
      <div class="container-fluid">
        <div class="row">
          <h3 class="title">
            Legrégebben jelentkezők 
          </h3>
        </div>
        
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 w-75 m-auto">

        <?php picSelection("Nem");?>

        </div>
      </div> 
    </div>
      

    <div class="album py-5 bg-dark">
      <div class="container-fluid">
        <div class="row">
          <h3 class="title txt-white">
            Legrégebben ideiglenesen befogadottak
          </h3>
        </div>
        
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 w-75 m-auto">

        <?php picSelection("Igen");?>

        </div>
      </div>
    </div>

    <div class="container-fluid py-5 bg-green">
      <div class="row">
        <div class="col-sm-6">
          <img class="float-end" src="img/csop.jpg" alt="" style="max-width: 80%;">
        </div>
        <div class="col-sm-6">
          <h2>Rólunk</h2>
          <p>
          Az Otthon Kereső egy online platform, ahol a felhasználók azokat a háziállatokat kereshetik, amelyeket gazdáik már nem tudnak tovább gondozni, és új otthonra vágynak. Célunk az állatok boldog és egészséges életének biztosítása, a környezet védelme, valamint a felelős állattartás népszerűsítése.
          Az Otthon Kereső nem állatkereskedő, hanem egy olyan közösség, ahol az állatok szerető otthonra találhatnak. A felhasználók ingyenesen adhatnak fel hirdetéseket az oldalra, amelyek segítségével az érdeklődők megtalálhatják az adott állatot, és közvetlenül a jelenlegi tulajdonosokkal léphetnek kapcsolatba.
          Az oldalunkon megtalálható összes állat az előző tulajdonosa által megunt, és új otthonra vár. Ezek az állatok sok szeretetet és figyelmet igényelnek, így fontos, hogy csak azok a felhasználók jelentkezzenek, akik valóban képesek és hajlandóak gondoskodni róluk.
          Az Otthon Kereső azt szeretné elérni, hogy minél több gazdit találjon az elhagyott megunt állatoknak, ezáltal segítse a felelős állattartást és az állatvédelmet. Ha kérdése vagy javaslata van, kérjük, vegye fel velünk a kapcsolatot az oldalon található elérhetőségeinken.
          <a class="btn btn-secondary" href="contact.php">Kapcsolat</a>
          </p>
        </div>
      </div>
    </div>
  </main>

    <footer class="container-fluid py-3 bg-blue">
      <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item"><a href="index.php" class="nav-link px-2 txt-white">Főoldal</a></li>
        <li class="nav-item"><a href="search.php" class="nav-link px-2 txt-white">Kereső</a></li>
        <li class="nav-item"><a href="aszf.php" class="nav-link px-2 txt-white">ÁSZF</a></li>
        <li class="nav-item"><a href="contact.php" class="nav-link px-2 txt-white">Kapcsolat</a></li>
      </ul>
      <p class="text-center text-muted">Otthon Kereső © 2022-2023 Company, Inc</p>
    </footer>

    <button onclick="topFunction()" id="scroll-to-top" title="Irány az oldal teteje">▲</button>

  <script src="js/tothetop.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>