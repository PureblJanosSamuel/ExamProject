<?php
session_start();
  include("functions.php");
  $user_data = check_login();
  $is_admin = is_admin();
  $is_moderator = is_moderator();
  $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

$total_pages = totalPages();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$offset = pageOffset($page,$total_pages);

$result = DB::GET(
  "SELECT userID, Name, ProfilePicture, Thumbnail, Description FROM users LIMIT ?, ?",
  array($offset,getLimit())
);

$html = '<div class="container-fluid">
             <div class="row justify-content-center">
                 <div class="col-md-6">
                     <h2>Felhasználók</h2>
                     <table class="table">
                         <thead>
                             <tr>
                                 <th>Felhasználónév</th>
                                 <th>Profilkép</th>
                                 <th>Leírás</th>
                                 <th></th>
                             </tr>
                         </thead>
                         <tbody>';

while ($row = mysqli_fetch_array($result)) {
    $html .= '<tr>
                 <td>' . $row['Name'] . '</td>
                 <td><img src="' . $row['ProfilePicture'] . '" width="75" height="75" title="' . $row['Name'] .'"></td>
                 <td>' . $row['Description'] . '</td>
                 <td><a href="profil_nezo.php?user_id=' . $row['userID'] . '" class="btn btn-primary">Megtekintés</a></td>
              </tr>';
}

$html .= '</tbody>
          </table>
       </div>
   </div>
</div>';

$html_pagination = '<div class="container-fluid">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <ul class="pagination">';

for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        $html_pagination .= '<li class="page-item active"><a class="page-link">' . $i . '</a></li>';
    } else {
        $html_pagination .= '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
    }
}

$html_pagination .= '</ul>
                </div>
            </div>
        </div>';

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Otthont keresők - Profilok</title>
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

    <?php echo $html; ?>
    <?php echo $html_pagination; ?>

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
    <script src="js/jquery.min.js"></script>

</body>
</html>