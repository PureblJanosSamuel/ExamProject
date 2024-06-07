<?php
  session_start();

  include("functions.php");
  include("dbconnect.php");

  $user_data = check_login($con);
  $is_admin = is_admin($con);
  $is_moderator = is_moderator($con);

  $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
  $errormsg = '';
  $msg = '';

  if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $to = $email;
    $headers = "From: " . $email . "\r\n" .
               "Reply-To: " . $email . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    $email_body = "Name: " . $name . "\n" .
                  "Email: " . $email . "\n" .
                  "Subject: " . $subject . "\n" .
                  "Message: " . $message;

    if(mail($to, $subject, $email_body, $headers)){
        $msg .= '<p class="text-success">Kérlek ellenőrizd a leveleid! A üzeneted siekresen el lett küldve</p>';
        header("Location: contact.php#contact"); 
        exit();
    } else{
        $errormsg .= '<p class="text-danger">Hiba történt a levélküldés közben. Kérlek próbáld újra később.</p>';
    }
}

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
      $bugname = $_POST['bug_name'];
      $bugdesc = $_POST['bug_desc'];
      $username = $_POST['username'];
        
      if(!empty($bugname) && !empty($bugdesc) && !empty($username)) {
        if(strlen($bugname) > 30) {
          $errormsg .= '<p class="text-danger">Hiba! A hiba neve nem lehet hosszabb 30 karakternél.</p>';
        }
        else if (strlen($username) > 20) {
          $errormsg .= '<p class="text-danger">Hiba! A neved nem lehet hosszabb 20 karakternél.</p>';
        }
        else {
        $query = "INSERT INTO bugreports (Name,Description,UserName) VALUES (?,?,?)";
        $stmt = mysqli_prepare($con, $query);
          
        mysqli_stmt_bind_param($stmt, "sss", $bugname, $bugdesc, $username);
        mysqli_stmt_execute($stmt);
        $msg .= '<p class="text-success">Hiba sikeresen jelentve!</p>';
        header("Location: contact.php#bugreport");
        } 
      }
      else {
        $errormsg .= '<p class="text-danger">Hiba! Minden mezőt ki kell töltened!</p>';
      }
    }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="icon" href="img/icons/contact.png" type="image/png">

    <title>Otthont keresők - Kapcsolat</title>
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
            <a class="nav-link dropdown-toggle txt-white active" href="contact.php" aria-current="page" data-bs-toggle="dropdown" aria-expanded="false">Kapcsolat</a>
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

    <section id="team">
        <div class="album py-5 bg-light">
        <?php if (isset($_GET['hiba_sikeresen_kuldve'])) {echo "<p class='text-danger text-center bg-warning'><b>Hiba sikeresen jelentve!</b></p>";}?>
        <h1>
        <svg class="svg-icon" style="width: 47; height: 47;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
          <path d="M774.4 742.4c0-44.8-12.8-89.6-38.4-121.6C691.2 569.6 640 544 576 544c-38.4 0-76.8 0-115.2 0-44.8 0-83.2 12.8-121.6 38.4-44.8 32-70.4 76.8-76.8 128-6.4 57.6 0 121.6-6.4 185.6 6.4 0 19.2 6.4 25.6 6.4 76.8 19.2 147.2 38.4 224 38.4 44.8 0 96 0 140.8-6.4 38.4-6.4 76.8-19.2 115.2-32 6.4 0 6.4-6.4 6.4-12.8C774.4 838.4 774.4 787.2 774.4 742.4z"  />
          <path d="M358.4 531.2c12.8-6.4 19.2-6.4 32-12.8-38.4-38.4-57.6-76.8-57.6-128-44.8 0-89.6 0-134.4 0C172.8 390.4 147.2 396.8 128 403.2 57.6 428.8 0 499.2 0 576c0 51.2 0 102.4 0 160 0 6.4 0 6.4 6.4 12.8 51.2 12.8 102.4 25.6 153.6 32 19.2 0 44.8 6.4 64 6.4 0-12.8 0-25.6 0-38.4 0-19.2 0-38.4 6.4-57.6C249.6 614.4 294.4 563.2 358.4 531.2z"  />
          <path d="M1024 588.8c0-25.6-6.4-57.6-19.2-83.2-38.4-76.8-96-115.2-179.2-115.2-121.6 0 0 0-121.6 0 0 51.2-19.2 96-57.6 128 6.4 0 12.8 6.4 12.8 6.4 32 12.8 64 32 89.6 57.6 38.4 44.8 57.6 96 57.6 153.6 0 12.8 0 32 0 44.8 32-6.4 57.6-6.4 89.6-12.8 38.4-6.4 83.2-19.2 121.6-38.4 6.4 0 6.4-6.4 6.4-12.8C1024 684.8 1024 633.6 1024 588.8z"  />
          <path d="M518.4 537.6c83.2 0 153.6-70.4 147.2-153.6 0-83.2-70.4-147.2-147.2-147.2-83.2 0-153.6 64-153.6 147.2C371.2 467.2 435.2 537.6 518.4 537.6z"  />
          <path d="M704 371.2C723.2 377.6 742.4 384 768 384c83.2 0 153.6-70.4 147.2-153.6 0-83.2-70.4-147.2-147.2-147.2-83.2 0-153.6 64-153.6 147.2C665.6 256 697.6 307.2 704 371.2z"  />
          <path d="M256 384c25.6 0 57.6-6.4 76.8-19.2 6.4-51.2 32-96 70.4-121.6 0 0 0-6.4 0-6.4 0-83.2-70.4-147.2-147.2-147.2-83.2 0-153.6 64-153.6 147.2C102.4 313.6 172.8 384 256 384z"  />
        </svg>
          Csapatunk
        <svg class="svg-icon" style="width: 47; height: 47;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
          <path d="M774.4 742.4c0-44.8-12.8-89.6-38.4-121.6C691.2 569.6 640 544 576 544c-38.4 0-76.8 0-115.2 0-44.8 0-83.2 12.8-121.6 38.4-44.8 32-70.4 76.8-76.8 128-6.4 57.6 0 121.6-6.4 185.6 6.4 0 19.2 6.4 25.6 6.4 76.8 19.2 147.2 38.4 224 38.4 44.8 0 96 0 140.8-6.4 38.4-6.4 76.8-19.2 115.2-32 6.4 0 6.4-6.4 6.4-12.8C774.4 838.4 774.4 787.2 774.4 742.4z"  />
          <path d="M358.4 531.2c12.8-6.4 19.2-6.4 32-12.8-38.4-38.4-57.6-76.8-57.6-128-44.8 0-89.6 0-134.4 0C172.8 390.4 147.2 396.8 128 403.2 57.6 428.8 0 499.2 0 576c0 51.2 0 102.4 0 160 0 6.4 0 6.4 6.4 12.8 51.2 12.8 102.4 25.6 153.6 32 19.2 0 44.8 6.4 64 6.4 0-12.8 0-25.6 0-38.4 0-19.2 0-38.4 6.4-57.6C249.6 614.4 294.4 563.2 358.4 531.2z"  />
          <path d="M1024 588.8c0-25.6-6.4-57.6-19.2-83.2-38.4-76.8-96-115.2-179.2-115.2-121.6 0 0 0-121.6 0 0 51.2-19.2 96-57.6 128 6.4 0 12.8 6.4 12.8 6.4 32 12.8 64 32 89.6 57.6 38.4 44.8 57.6 96 57.6 153.6 0 12.8 0 32 0 44.8 32-6.4 57.6-6.4 89.6-12.8 38.4-6.4 83.2-19.2 121.6-38.4 6.4 0 6.4-6.4 6.4-12.8C1024 684.8 1024 633.6 1024 588.8z"  />
          <path d="M518.4 537.6c83.2 0 153.6-70.4 147.2-153.6 0-83.2-70.4-147.2-147.2-147.2-83.2 0-153.6 64-153.6 147.2C371.2 467.2 435.2 537.6 518.4 537.6z"  />
          <path d="M704 371.2C723.2 377.6 742.4 384 768 384c83.2 0 153.6-70.4 147.2-153.6 0-83.2-70.4-147.2-147.2-147.2-83.2 0-153.6 64-153.6 147.2C665.6 256 697.6 307.2 704 371.2z"  />
          <path d="M256 384c25.6 0 57.6-6.4 76.8-19.2 6.4-51.2 32-96 70.4-121.6 0 0 0-6.4 0-6.4 0-83.2-70.4-147.2-147.2-147.2-83.2 0-153.6 64-153.6 147.2C102.4 313.6 172.8 384 256 384z"  />
        </svg>
        </h1>
           <div class="container">
        
              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                <div class="col">
                  <div class="card shadow-sm">
                    <svg class="bd-placeholder-img card-img-top" width="100%" height="225" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Dömös László</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Dömös László</text></svg>
        
                    <div class="card-body">
                        <p class="card-text">
                          <svg width="16" height="16" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16">
                          <path xmlns="http://www.w3.org/2000/svg" d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5h11zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2h-11zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5z"/>
                          </svg>
                            Dömös László
                          <svg width="16" height="16" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16">
                          <path xmlns="http://www.w3.org/2000/svg" d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5h11zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2h-11zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5z"/>
                          </svg>
                          <br>
                            <span style="color: blue;">Laci</span><br>
                            Felelős a php kódokért és az adatbázisok megfelelő felépítése ért.<br>
                            Munkái:<br>
                            hirdetéseid oldal, 404 oldal, kapcsolat oldal, PHP kódok többsége.<br><br>
                        </p>
                    </div>
                  </div>
                </div>

                <div class="col">
                  <div class="card shadow-sm">
                    <svg class="bd-placeholder-img card-img-top" width="100%" height="225" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Gulyás András János</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Gulyás András János</text></svg>
        
                    <div class="card-body">
                        <p class="card-text">
                        <svg width="16" height="16" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16">
                          <path xmlns="http://www.w3.org/2000/svg" d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5h11zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2h-11zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5z"/>
                          </svg>
                            Gulyás András János
                            <svg width="16" height="16" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16">
                          <path xmlns="http://www.w3.org/2000/svg" d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5h11zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2h-11zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5z"/>
                          </svg>
                            <br>
                            Felelős a html oldalakért és azoknak CSS kódjáért valamint Badass dizájnolási képességei segíti a csapatunk.<br>
                            Munkái:<br>
                            Bejelentkezési oldal, Regisztrációs oldal, Profil oldal, kisállat lapok, javítások.
                        </p>
                    </div>
                  </div>
                </div>

                <div class="col">
                  <div class="card shadow-sm">
                    <svg class="bd-placeholder-img card-img-top" width="100%" height="225" xmlns="the_team/samu.svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Purebl János Sámuel</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Purbel János Sámuel</text></svg>
        
                    <div class="card-body">
                        <p class="card-text">
                        <svg width="16" height="16" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16">
                          <path xmlns="http://www.w3.org/2000/svg" d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5h11zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2h-11zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5z"/>
                          </svg>
                            Purebl János Sámuel
                            <svg width="16" height="16" fill="currentColor" class="bi bi-laptop" viewBox="0 0 16 16">
                          <path xmlns="http://www.w3.org/2000/svg" d="M13.5 3a.5.5 0 0 1 .5.5V11H2V3.5a.5.5 0 0 1 .5-.5h11zm-11-1A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2h-11zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5z"/>
                          </svg>
                          <br>
                            Csapatkapitány.<br>
                            Felelős a társai megfelelő munkáiért valamint a JavaScripteket is ő készítette.<br>
                            Munkái:<br>
                            Dokumentáció, JavaScript, index oldal, kereső.<br><br>
                        </p>
                    </div>
                  </div>
                </div>

              </div>
            </div>
        </div>
    </section>

    <section id="contact">
      <div class="container text-center"> 

        <form method="post">
          <h1>
          <svg width="47" height="47" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
            <path xmlns="http://www.w3.org/2000/svg" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
          </svg>
            Lépj velünk kapcsolatba
            <svg width="47" height="47" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
            <path xmlns="http://www.w3.org/2000/svg" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
          </svg>
          </h1>         
          <?php if(!empty($msg)): ?>
              <div class="alert alert-success">
                  <?php echo $msg; ?>
              </div>
          <?php endif; ?>
          <?php if(!empty($errormsg)): ?>
              <div class="alert alert-danger">
                  <?php echo $errormsg; ?>
              </div>
          <?php endif; ?>

          <p>
            <input style="width: 50%; padding: 6px; margin: 8px 0; box-sizing: border-box;" placeholder="Név amire hivatkozni szeretnél a velünk való levelezés során" name="name" type="text" tabindex="1" required>
        </p>
        <p>
            <input style="width: 50%; padding: 6px; margin: 8px 0; box-sizing: border-box;" placeholder="E-mail címed" name="email" type="email" tabindex="2" required>
        </p>
        <p>    
            <input style="width: 50%; padding: 6px; margin: 8px 0; box-sizing: border-box;" placeholder="Tárgy" type="text" name="subject" tabindex="4" required>
        </p>
        <p>
            <textarea style="width: 50%; padding: 6px; margin: 8px 0; box-sizing: border-box; resize: none;" rows="4" name="message" placeholder="Üzeneted helye..." tabindex="5" required></textarea>
        </p>    
            <button class="bg-warning" type="submit" name="submit" id="contact-submit" data-submit="...Küldés" tabindex="6">Üzenet küldése</button>
        </form> 

      </div>
    </section>

    <br>

    <section id="bugreport" class="text-center">
      <h1>
      <svg xmlns="http://www.w3.org/2000/svg" width="47" height="47" viewBox="0 0 512 512">
        <path d="M370,378c28.89,23.52,46,46.07,46,86" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M142,378c-28.89,23.52-46,46.06-46,86" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M384,208c28.89-23.52,32-56.07,32-96" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M128,206c-28.89-23.52-32-54.06-32-94" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <line x1="464" y1="288.13" x2="384" y2="288.13" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <line x1="128" y1="288.13" x2="48" y2="288.13" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <line x1="256" y1="192" x2="256" y2="448" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M256,448h0c-70.4,0-128-57.6-128-128V223.93c0-65.07,57.6-96,128-96h0c70.4,0,128,25.6,128,96V320C384,390.4,326.4,448,256,448Z" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M179.43,143.52A49.08,49.08,0,0,1,176,127.79,80,80,0,0,1,255.79,48h.42A80,80,0,0,1,336,127.79a41.91,41.91,0,0,1-3.12,14.3" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
      </svg>
        Hiba jelentése
      <svg xmlns="http://www.w3.org/2000/svg" width="47" height="47" viewBox="0 0 512 512">
        <path d="M370,378c28.89,23.52,46,46.07,46,86" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M142,378c-28.89,23.52-46,46.06-46,86" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M384,208c28.89-23.52,32-56.07,32-96" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M128,206c-28.89-23.52-32-54.06-32-94" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <line x1="464" y1="288.13" x2="384" y2="288.13" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <line x1="128" y1="288.13" x2="48" y2="288.13" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <line x1="256" y1="192" x2="256" y2="448" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M256,448h0c-70.4,0-128-57.6-128-128V223.93c0-65.07,57.6-96,128-96h0c70.4,0,128,25.6,128,96V320C384,390.4,326.4,448,256,448Z" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
        <path d="M179.43,143.52A49.08,49.08,0,0,1,176,127.79,80,80,0,0,1,255.79,48h.42A80,80,0,0,1,336,127.79a41.91,41.91,0,0,1-3.12,14.3" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/>
      </svg>
      </h1>

      <?php if(!empty($msg)): ?>
              <div class="alert alert-success">
                  <?php echo $msg; ?>
              </div>
          <?php endif; ?>
          <?php if(!empty($errormsg)): ?>
              <div class="alert alert-danger">
                  <?php echo $errormsg; ?>
              </div>
      <?php endif; ?>

      <form method="post">
      <p>
        <label>Neved:</label><br>
        <input style="width: 50%; padding: 6px; margin: 8px 0; box-sizing: border-box;" type="text" name="username" id="username" placeholder="Neved (Maximum 20 karakter)" tabindex="6" required>
      </p>
      <p>
        <label>Hiba neve:</label><br>
        <input style="width: 50%; padding: 6px; margin: 8px 0; box-sizing: border-box;" type="text" name="bug_name" id="bug_name" placeholder="Példa hibanév (Maximum 30 karakter)" tabindex="7" required>
      </p>
      <p>
        <label>Hiba leírása:</label><br>
        <textarea style="width: 50%; padding: 6px; margin: 8px 0; box-sizing: border-box; resize: none;" rows="4" name="bug_desc" placeholder="Példa leírás" tabindex="8" required></textarea>
      </p> 
        <input class="bg-warning" type="submit" value="Küldés">
      </form>

    </section>

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