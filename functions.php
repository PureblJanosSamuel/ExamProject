<?php
include("DB.php");

function dd($var){
  echo "<pre>";
  var_dump($var);
  echo "</pre>";
  die();
}

function dump($var){
  echo "<pre>";
  var_dump($var);
  echo "</pre>";
}

function picSelection($isTh){

    $result = DB::GET(
      "SELECT animalID, Name, Picture, Age, Description FROM animalinfo WHERE FoundHome = ? AND TemporaryHome = ? ORDER BY CreatedAt LIMIT 3", 
      array("Nem",$isTh)
    );
    while ($row = mysqli_fetch_assoc($result)) {
    echo '
    <div class="p-2 col-sm-4 m-0">
      <div class="card rounded shadow-lg">
        <img height="375px" src="'.$row['Picture'].'" class="p-2 pt-3" alt="'.$row['Name'].'" title="'.$row['Name'].'">
        <div class="card-body">
          <h5 class="card-title">'.$row['Name'].'</h5>
          <p class="card-text text-truncate" style="max-width: 320px;">'.$row['Description'].'</p>
          <p class="card-text">Kora: '.$row['Age'].'</p>
          <a href="kisallat_nezo.php?animal_id='.$row['animalID'].'" class="btn btn-primary">Megtekint</a>
        </div>
      </div>
    </div>';
    }
}

function check_login()
{
	if (isSET($_SESSION['userID']))
	{ 
		$id = $_SESSION['userID'];
		$sql = "SELECT * FROM users WHERE userID= ? limit 1";
		
		$result = DB::GET($sql, array($id));
		if ($result && mysqli_num_rows($result) >0)
		{
			$user_data = mysqli_fetch_assoc($result);
			return $user_data;
		}
	}
	
}

function is_admin()
{  
    if (isSET($_SESSION['userID'])) {
    $user_id = $_SESSION['userID'];

    $sql = "SELECT Title FROM roles WHERE userID = ?";
    $result = DB::GET($sql, array($user_id));
    if (mysqli_num_rows($result) > 0) 
    {
      $row = mysqli_fetch_assoc($result);
      if ($row['Title'] == 'admin') 
      {
        return true;
      }
    }

    return false;
  }
}

function is_moderator() 
{  
  if (isSET($_SESSION['userID'])) {
    $user_id = $_SESSION['userID'];

    $sql = "SELECT Title FROM roles WHERE userID = ?";
    $result = DB::GET($sql, array($user_id));
    if (mysqli_num_rows($result) > 0) 
    {
      $row = mysqli_fetch_assoc($result);
      if ($row['Title'] == 'moderator') 
      {
        return true;
      }
    }

  return false;
  }
}

function generateActivationCode($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $code = '';
  for ($i = 0; $i < $length; $i++) {
      $code .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $code;
}

function greet_user() {
  $hour = date('H');
  $greetmsg = '';

  if ($hour >= 5 && $hour < 12) {
    $greetmsg .= '<span class="text-secondary">Jó reggelt:&nbsp;</span>';
  } elseif ($hour >= 12 && $hour < 18) {
    $greetmsg .= '<span class="text-secondary">Szép napot:&nbsp;</span>';
  } else {
    $greetmsg .= '<span class="text-secondary">Kellemes estét:&nbsp;</span>';
  }

  echo $greetmsg;
}


// Generating unic ID

function randomID($length){
  $characters = '0123456789';
  $result = '';
  srand((int) microtime(true) * 1000);
  for ($i = 0; $i < $length; $i++) {
    $result .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $result;
}

function hasIt($table, $wID, $rID){

  $result = DB::GET(
    "SELECT COUNT(*) FROM $table WHERE $wID = ?", 
    array($rID)
  );
  $row = mysqli_fetch_array($result);
  return $row[0];
}

function generateID() {
  while (true) {
      $randomString = randomID(24);
      if (hasIt("users", "userID", $randomString) == 0 &&
          hasIt("animalinfo", "animalID", $randomString) == 0){
            return $randomString;
      }
  }
}

// Table selection

function selectTable($table){
  return DB::GET(
    "SELECT * FROM $table",
    array()
  );
}

// Removing data

function removeUser($userID) {
  DB::DELETE(
    array("animalinfo","users","roles"),
    "userID",
    $userID);
  header("Location: admin.php?felhasznalo_torolve#userviewer");
}

function removeAnimal($animal_ID) {
  DB::DELETE(
    array("animalinfo"),
    "animalID",
    $animal_ID);
  header("Location: admin.php?allat_sikeresen_torolve#animalviewer");
}

function removeUserRole($userID) {
  DB::DELETE(
    array("roles"),
    "userID",
    $userID);
  header("Location: admin.php?rang_sikeresen_elveve#derank");
}

function removeBugReport($id) {
  DB::DELETE(array("bugreports"),"ID",$id);

  header("Location: admin.php?hiba_torolve#bugs");
}

// Building HTML

function kiir($sql, $sex, $age) {

  $arr = [];
  if (is_countable($sex)) {
    for ($i = 0; $i < count($sex); $i++) { 
      if ($i == 0) {
        $sql .= ' AND Sex = ?';
        array_push($arr, $sex[$i]);
      } else {
        $sql .= ' OR Sex = ?';
        array_push($arr, $sex[$i]);
      }
    }
  }

  if ($age != 0) {
    $down = $age - 1;
    $up = $age + 1;
    $sql .= ' AND Age BETWEEN ? AND ?';
    array_push($arr, strval($down), strval($up));
  }

  $animals_run = DB::GET($sql,$arr);

  if ($animals_run && mysqli_num_rows($animals_run) > 0) {
    foreach ($animals_run as $animalitems) {
      echo '
      <tr>
      <td class="product vertical-middle"><strong>
      '.$animalitems['Name'].'</strong></td>
      <td class="image vertical-middle"><img class="rounded-circle" src="'.$animalitems['Picture'].'" alt=""></td>
      <td class="product">'.$animalitems['Description'].'</td>
      <td class="text-right vertical-middle"><i><span>'.$animalitems['DateofBorn'].'</span></i></td>
      <td class="text-right vertical-middle"><span><a href="kisallat_nezo.php?animal_id='.$animalitems['animalID'].'" class="btn btn-primary">Megtekint</a></span></td>
      </tr>
      ';
    }
  } else {
    echo '<tr><td class="product vertical-middle">Éljen! Mindenki talált otthont!</td></tr>';
  }
}

function read_users() {
  $profileshtml = '            
  <table class="table table-bordered">
  <thead>
      <tr>
      <th>Profilkép</th>
      <th>Borítókép</th>
      <th>Felhasználónév</th>
      <th>Leírás</th>
      <th>ID</th>
      <th>E-mail</th>
      <th>Telefonszám</th>
      <th>Létrehozva</th>
      <th>Aktivált</th>
      <th>Törlés</th>
      <th>Szerkesztés</th>
      </tr>
  </thead>
  <tbody>';
  
  $result = selectTable("users");
  while ($row = mysqli_fetch_assoc($result)) {
      $profileshtml .= '
      <tr>
      <td><img src="' . $row['ProfilePicture'] . '" width="75" height="75"></td>
      <td><img src="' . $row['Thumbnail'] . '" width="75" height="75"></td>
      <td>' . $row['Name'] . '</td>
      <td>' . $row['Description'] . '</td>
      <td>' . $row['userID'] . '</td>
      <td>' . $row['Email'] . '</td>
      <td>' . $row['PhoneNumber'] . '</td>
      <td>' . $row['CreatedAt'] . '</td>
      <td>' . $row['Activated'] . '</td>
      <td>
      <a href="?user_ID=' . $row['userID'] . '">
      <button type="button" class="btn btn-sm bg-danger btn-outline-warning">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="25" height="25" viewBox="0 0 256 256" xml:space="preserve">

      <defs>
      </defs>
      <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
          <path d="M 76.777 2.881 H 57.333 V 2.412 C 57.333 1.08 56.253 0 54.921 0 H 35.079 c -1.332 0 -2.412 1.08 -2.412 2.412 v 0.469 H 13.223 c -1.332 0 -2.412 1.08 -2.412 2.412 v 9.526 c 0 1.332 1.08 2.412 2.412 2.412 h 63.554 c 1.332 0 2.412 -1.08 2.412 -2.412 V 5.293 C 79.189 3.961 78.109 2.881 76.777 2.881 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
          <path d="M 73.153 22.119 H 16.847 c -1.332 0 -2.412 1.08 -2.412 2.412 v 63.057 c 0 1.332 1.08 2.412 2.412 2.412 h 56.306 c 1.332 0 2.412 -1.08 2.412 -2.412 V 24.531 C 75.565 23.199 74.485 22.119 73.153 22.119 z M 33.543 81.32 c 0 1.332 -1.08 2.412 -2.412 2.412 h -2.245 c -1.332 0 -2.412 -1.08 -2.412 -2.412 V 30.799 c 0 -1.332 1.08 -2.412 2.412 -2.412 h 2.245 c 1.332 0 2.412 1.08 2.412 2.412 V 81.32 z M 48.535 81.32 c 0 1.332 -1.08 2.412 -2.412 2.412 h -2.245 c -1.332 0 -2.412 -1.08 -2.412 -2.412 V 30.799 c 0 -1.332 1.08 -2.412 2.412 -2.412 h 2.245 c 1.332 0 2.412 1.08 2.412 2.412 V 81.32 z M 63.526 81.32 c 0 1.332 -1.08 2.412 -2.412 2.412 h -2.245 c -1.332 0 -2.412 -1.08 -2.412 -2.412 V 30.799 c 0 -1.332 1.08 -2.412 2.412 -2.412 h 2.245 c 1.332 0 2.412 1.08 2.412 2.412 V 81.32 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
      </g>
      </svg>
      </button>
      </a>
      </td>
      <td>
      <a title="Szerkesztés" href="profil_szerkesztes_admin.php?edit_user_ID=' . $row['userID'] . '">
      <button type="button" class="btn btn-sm bg-warning btn-outline-danger">
      <svg fill="#000000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
      width="25" height="25" viewBox="0 0 494.936 494.936"
      xml:space="preserve">
      <g>
          <g>
              <path d="M389.844,182.85c-6.743,0-12.21,5.467-12.21,12.21v222.968c0,23.562-19.174,42.735-42.736,42.735H67.157
                  c-23.562,0-42.736-19.174-42.736-42.735V150.285c0-23.562,19.174-42.735,42.736-42.735h267.741c6.743,0,12.21-5.467,12.21-12.21
                  s-5.467-12.21-12.21-12.21H67.157C30.126,83.13,0,113.255,0,150.285v267.743c0,37.029,30.126,67.155,67.157,67.155h267.741
                  c37.03,0,67.156-30.126,67.156-67.155V195.061C402.054,188.318,396.587,182.85,389.844,182.85z"/>
              <path d="M483.876,20.791c-14.72-14.72-38.669-14.714-53.377,0L221.352,229.944c-0.28,0.28-3.434,3.559-4.251,5.396l-28.963,65.069
                  c-2.057,4.619-1.056,10.027,2.521,13.6c2.337,2.336,5.461,3.576,8.639,3.576c1.675,0,3.362-0.346,4.96-1.057l65.07-28.963
                  c1.83-0.815,5.114-3.97,5.396-4.25L483.876,74.169c7.131-7.131,11.06-16.61,11.06-26.692
                  C494.936,37.396,491.007,27.915,483.876,20.791z M466.61,56.897L257.457,266.05c-0.035,0.036-0.055,0.078-0.089,0.107
                  l-33.989,15.131L238.51,247.3c0.03-0.036,0.071-0.055,0.107-0.09L447.765,38.058c5.038-5.039,13.819-5.033,18.846,0.005
                  c2.518,2.51,3.905,5.855,3.905,9.414C470.516,51.036,469.127,54.38,466.61,56.897z"/>
          </g>
      </g>
 </svg>
 </button>
      </a>
      </td>
      </tr>';
  }
  $profileshtml .= '
  </tbody>
  </table>';
  echo $profileshtml;
}

function read_animals() {
  $animalshtml = '
  <table class="table table-bordered table-responsive-sm">
  <thead>
      <tr>
      <th>Profilkép</th>
      <th>Név</th>
      <th>Leírás</th>
      <th>ID</th>
      <th>Faj</th>
      <th>Nem</th>
      <th>Mozgásigény</th>
      <th>Oltások</th>
      <th>Létrehozva</th>
      <th>Gazdája</th>
      <th>Törlés</th>
      <th>Szerkesztés</th>
      </tr>
  </thead>
  <tbody>';
  
  $result = selectTable("animalinfo");
  while ($row = mysqli_fetch_assoc($result)) {
      $animalshtml .= '
      <tr>
      <td><img src="'.$row['Picture'].'" alt="Profilkép" width="75" height="75"></td>
      <td>'.$row['Name'].'</td>
      <td>'.$row['Description'].'</td>
      <td>'.$row['animalID'].'</td>
      <td>'.$row['Specie'].'</td>
      <td>'.$row['Sex'].'</td>
      <td>'.$row['NeedofExercise'].'</td>
      <td>'.$row['Vaccines'].'</td>
      <td>'.$row['CreatedAt'].'</td>
      <td>'.$row['userID'].'</td>
      <td>
      <a title="Állat törlése" href="?animal_ID='.$row['animalID'].'">
      <button type="button" class="btn btn-sm bg-danger btn-outline-danger">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="25" height="25" viewBox="0 0 256 256" xml:space="preserve">

      <defs>
      </defs>
      <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
          <path d="M 76.777 2.881 H 57.333 V 2.412 C 57.333 1.08 56.253 0 54.921 0 H 35.079 c -1.332 0 -2.412 1.08 -2.412 2.412 v 0.469 H 13.223 c -1.332 0 -2.412 1.08 -2.412 2.412 v 9.526 c 0 1.332 1.08 2.412 2.412 2.412 h 63.554 c 1.332 0 2.412 -1.08 2.412 -2.412 V 5.293 C 79.189 3.961 78.109 2.881 76.777 2.881 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
          <path d="M 73.153 22.119 H 16.847 c -1.332 0 -2.412 1.08 -2.412 2.412 v 63.057 c 0 1.332 1.08 2.412 2.412 2.412 h 56.306 c 1.332 0 2.412 -1.08 2.412 -2.412 V 24.531 C 75.565 23.199 74.485 22.119 73.153 22.119 z M 33.543 81.32 c 0 1.332 -1.08 2.412 -2.412 2.412 h -2.245 c -1.332 0 -2.412 -1.08 -2.412 -2.412 V 30.799 c 0 -1.332 1.08 -2.412 2.412 -2.412 h 2.245 c 1.332 0 2.412 1.08 2.412 2.412 V 81.32 z M 48.535 81.32 c 0 1.332 -1.08 2.412 -2.412 2.412 h -2.245 c -1.332 0 -2.412 -1.08 -2.412 -2.412 V 30.799 c 0 -1.332 1.08 -2.412 2.412 -2.412 h 2.245 c 1.332 0 2.412 1.08 2.412 2.412 V 81.32 z M 63.526 81.32 c 0 1.332 -1.08 2.412 -2.412 2.412 h -2.245 c -1.332 0 -2.412 -1.08 -2.412 -2.412 V 30.799 c 0 -1.332 1.08 -2.412 2.412 -2.412 h 2.245 c 1.332 0 2.412 1.08 2.412 2.412 V 81.32 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
      </g>
      </svg>
      </button>
      </a>
      </td>
      <td>
      <a title="Állat szerkesztése" href="allat_szerkesztes_admin.php?edit_animal_ID=' . $row['animalID'] . '">
      <button type="button" class="btn btn-sm bg-warning btn-outline-danger">
      <svg fill="#000000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
      width="25" height="25" viewBox="0 0 494.936 494.936"
      xml:space="preserve">
      <g>
          <g>
              <path d="M389.844,182.85c-6.743,0-12.21,5.467-12.21,12.21v222.968c0,23.562-19.174,42.735-42.736,42.735H67.157
                  c-23.562,0-42.736-19.174-42.736-42.735V150.285c0-23.562,19.174-42.735,42.736-42.735h267.741c6.743,0,12.21-5.467,12.21-12.21
                  s-5.467-12.21-12.21-12.21H67.157C30.126,83.13,0,113.255,0,150.285v267.743c0,37.029,30.126,67.155,67.157,67.155h267.741
                  c37.03,0,67.156-30.126,67.156-67.155V195.061C402.054,188.318,396.587,182.85,389.844,182.85z"/>
              <path d="M483.876,20.791c-14.72-14.72-38.669-14.714-53.377,0L221.352,229.944c-0.28,0.28-3.434,3.559-4.251,5.396l-28.963,65.069
                  c-2.057,4.619-1.056,10.027,2.521,13.6c2.337,2.336,5.461,3.576,8.639,3.576c1.675,0,3.362-0.346,4.96-1.057l65.07-28.963
                  c1.83-0.815,5.114-3.97,5.396-4.25L483.876,74.169c7.131-7.131,11.06-16.61,11.06-26.692
                  C494.936,37.396,491.007,27.915,483.876,20.791z M466.61,56.897L257.457,266.05c-0.035,0.036-0.055,0.078-0.089,0.107
                  l-33.989,15.131L238.51,247.3c0.03-0.036,0.071-0.055,0.107-0.09L447.765,38.058c5.038-5.039,13.819-5.033,18.846,0.005
                  c2.518,2.51,3.905,5.855,3.905,9.414C470.516,51.036,469.127,54.38,466.61,56.897z"/>
          </g>
      </g>
 </svg>
      </button>
      </a>
      </td>
      ';
  }
  $animalshtml .= '
  </tbody>
  </table>';
  echo $animalshtml;
}

function read_bugs() {
  $bugshtml = '
  <table class="table table-bordered table-responsive-sm">
  <thead>
      <tr>
      <th>HibaID</th>
      <th>Név</th>
      <th>Hiba név</th>
      <th>Leírás</th>
      <th>Létrehozva</th>
      <th>Hiba javítva</th>
      </tr>
  </thead>
  <tbody>';
  $result = selectTable("bugreports");
  while ($row = mysqli_fetch_assoc($result)) {
      $bugshtml .= '
      <tr>
      <td>'.$row['ID'].'</td>
      <td>'.$row['UserName'].'</td>
      <td>'.$row['Name'].'</td>
      <td>'.$row['Description'].'</td>
      <td>'.$row['CreatedAt'].'</td>
      <td>
      <a title="Hiba törlése" href="?remove_bugreport_id=' . $row['ID'] . '">
      <button type="button" class="btn btn-sm bg-warning btn-outline-danger">
      <svg fill="#000000" height="25" width="25" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xanimalpic$animalpic="http://www.w3.org/1999/xanimalpic$animalpic" 
          viewBox="0 0 512 512" xml:space="preserve">
      <g>
          <path d="M474.045,173.813c-4.201,1.371-6.494,5.888-5.123,10.088c7.571,23.199,11.411,47.457,11.411,72.1
              c0,62.014-24.149,120.315-68,164.166s-102.153,68-164.167,68s-120.316-24.149-164.167-68S16,318.014,16,256
              S40.149,135.684,84,91.833s102.153-68,164.167-68c32.889,0,64.668,6.734,94.455,20.017c28.781,12.834,54.287,31.108,75.81,54.315
              c3.004,3.239,8.066,3.431,11.306,0.425c3.24-3.004,3.43-8.065,0.426-11.306c-23-24.799-50.26-44.328-81.024-58.047
              C317.287,15.035,283.316,7.833,248.167,7.833c-66.288,0-128.608,25.813-175.48,72.687C25.814,127.392,0,189.712,0,256
              c0,66.287,25.814,128.607,72.687,175.479c46.872,46.873,109.192,72.687,175.48,72.687s128.608-25.813,175.48-72.687
              c46.873-46.872,72.687-109.192,72.687-175.479c0-26.332-4.105-52.26-12.201-77.064
              C482.762,174.736,478.245,172.445,474.045,173.813z"/>
          <path d="M504.969,83.262c-4.532-4.538-10.563-7.037-16.98-7.037s-12.448,2.499-16.978,7.034l-7.161,7.161
              c-3.124,3.124-3.124,8.189,0,11.313c3.124,3.123,8.19,3.124,11.314-0.001l7.164-7.164c1.51-1.512,3.52-2.344,5.66-2.344
              s4.15,0.832,5.664,2.348c1.514,1.514,2.348,3.524,2.348,5.663s-0.834,4.149-2.348,5.663L217.802,381.75
              c-1.51,1.512-3.52,2.344-5.66,2.344s-4.15-0.832-5.664-2.348L98.747,274.015c-1.514-1.514-2.348-3.524-2.348-5.663
              c0-2.138,0.834-4.149,2.351-5.667c1.51-1.512,3.52-2.344,5.66-2.344s4.15,0.832,5.664,2.348l96.411,96.411
              c1.5,1.5,3.535,2.343,5.657,2.343s4.157-0.843,5.657-2.343l234.849-234.849c3.125-3.125,3.125-8.189,0-11.314
              c-3.124-3.123-8.189-3.123-11.313,0L212.142,342.129l-90.75-90.751c-4.533-4.538-10.563-7.037-16.98-7.037
              s-12.448,2.499-16.978,7.034c-4.536,4.536-7.034,10.565-7.034,16.977c0,6.412,2.498,12.441,7.034,16.978l107.728,107.728
              c4.532,4.538,10.563,7.037,16.98,7.037c6.417,0,12.448-2.499,16.977-7.033l275.847-275.848c4.536-4.536,7.034-10.565,7.034-16.978
              S509.502,87.794,504.969,83.262z"/>
      </g>
      </svg>
      </button>
      </a>
      </td>
      ';
  }
  $bugshtml .= '
  </tbody>
  </table>';
  echo $bugshtml;
}

function ranged_usershtml() {
  $ranged_usershtml = '
  <table class="table table-bordered table-responsive-sm">
  <thead>
      <tr>
      <th>userID - Felhasználónév</th>
      <th>Rang</th>
      <th>Rang elvétele</th>
      </tr>
  </thead>
  <tbody>';
  $result = DB::GET(
    "SELECT roles.userID, users.Name, roles.Title FROM roles JOIN users ON roles.userID = users.userID",
    array());
  while ($row = mysqli_fetch_assoc($result)) {
      $ranged_usershtml .= '
      <tr>
      <td>'.$row['userID'].' - '.$row['Name'].'</td>
      <td>'.$row['Title'].'</td>
      <td>
      <a title="Rang elvétele" href="?role_user_ID=' . $row['userID'] . '">
      <button type="button" class="btn btn-sm bg-danger btn-outline-warning">
      V
      </button>
      </a>
      </td>
      ';
  }
  $ranged_usershtml .= '
  </tbody>
  </table>';
  echo $ranged_usershtml;
}

function rangformhtml() {
  if(isSET($_POST['username']) && isSET($_POST['rang'])) {
      $username = $_POST['username'];
      $rang = $_POST['rang'];
      $query = "INSERT INTO roles () VALUES ()";
      DB::POST(
        "roles",array("userID", "Title"),
        array("$username", "$rang")
      );
  }
  $ranghtml = '
  <form method="post">

  <div class="form-outline mb-4">
    <label class="form-label" for="nev">Felhasználónév - userID: </label><br>
    <select name="username">
    <option value="">Válassz</option>
  ';
  $result = DB::GET(
    "SELECT userID, Name FROM users",
    array()
  );
  while ($row = mysqli_fetch_assoc($result)){
      $ranghtml .= '
      <option value="'.$row['userID'].'">'.$row['Name'].' - '.$row['userID'].'</option>
      ';
  }
  $ranghtml .= '
  </select>
  </div>

  <div class="form-outline mb-4">
      <label class="form-label" for="Rang">Rang: </label>
      <select name="rang">
          <option value="">Válassz</option>
          <option value="moderator">Moderátor</option>
          <option value="admin">Adminisztrátor</option>
      </select>
  </div>
  <button class="btn btn-secondary" type="submit">Küldés</button>
  </form>';
  echo $ranghtml;
}
    
// Creating sample users and animalas
function random_user(){
  $usernames = array('Emma', 'Olivia', 'Sophia', 'Ava', 'Isabella', 'Mia', 'Charlotte', 'Amelia', 'Harper', 'Evelyn', 'Abigail', 'Emily', 'Elizabeth', 'Mila', 'Ella', 'Avery', 'Sofia', 'Camila', 'Aria', 'Scarlett', 'Victoria', 'Madison', 'Luna', 'Grace', 'Chloe', 'Penelope', 'Layla', 'Riley', 'Zoey', 'Nora', 'Lily', 'Eleanor', 'Hannah', 'Lillian', 'Addison', 'Aubrey', 'Ellie', 'Stella', 'Natalie', 'Zoe', 'Leah', 'Hazel', 'Violet', 'Aurora', 'Savannah', 'Audrey', 'Brooklyn', 'Bella', 'Claire', 'Skylar', 'Lucy', 'Paisley', 'Everly', 'Anna', 'Caroline', 'Nova', 'Genesis', 'Emilia', 'Kennedy', 'Samantha', 'Maya', 'Willow', 'Kinsley', 'Naomi', 'Aaliyah', 'Elena', 'Sarah', 'Ariana', 'Allison', 'Gabriella', 'Alice', 'Madelyn', 'Cora', 'Ruby', 'Eva', 'Serenity', 'Autumn', 'Adeline', 'Hailey', 'Gianna', 'Valentina', 'Isla', 'Eliana', 'Quinn', 'Nevaeh', 'Ivy', 'Sadie', 'Piper', 'Lydia', 'Alexa', 'Josephine', 'Emery', 'Julia', 'Delilah', 'Arianna', 'Vivian', 'Kaylee', 'Sophie', 'Brielle', 'Madeline', 'Peyton', 'Rylee', 'Clara', 'Hadley', 'Melanie', 'Mackenzie', 'Reagan', 'Adalynn', 'Liliana', 'Aubree', 'Jade', 'Katherine', 'Isabelle', 'Natalia', 'Raelynn', 'Maria', 'Athena', 'Ximena', 'Arya', 'Leilani', 'Taylor', 'Faith', 'Rose', 'Kylie', 'Alexandra', 'Mary', 'Margaret', 'Lyla', 'Ashley', 'Amaya', 'Eliza', 'Brianna', 'Bailey', 'Andrea', 'Khloe', 'Jasmine', 'Melody', 'Iris', 'Isabel', 'Norah', 'Annabelle', 'Valeria', 'Emerson', 'Adalyn', 'Rosalie', 'Ayla', 'Emersyn', 'Makayla', 'Reese', 'Malia', 'Amanda', 'Daniela', 'Gracie', 'Fatima', 'Vivienne', 'Thea', 'Adaline', 'Lola', 'Angela', 'Leila', 'Brynlee', 'Lia', 'Jordyn', 'Everleigh', 'Alaina', 'Amber', 'Kali', 'Lorelei', 'Myla', 'Jayla', 'Alina', 'Giselle', 'Haley', 'Raegan', 'Journey', 'Elaina', 'Miriam', 'Mikayla', 'Catalina', 'Slow',  'June', 'Jennifer', 'Lilah', 'Amani', 'Meredith', 'Jada', 'Leighton', 'Kiera', 'Lana', 'Hattie', 'Elina', 'Kailyn', 'Yaretzi', 'Anya', 'Jacqueline', 'Nancy', 'Saoirse', 'Carina', 'Laurel', 'Gloria', 'Charli', 'Margo', 'Mavis', 'Noemi', 'Remy', 'Rosa', 'Kamryn', 'Reina', 'Sariyah', 'Wendy', 'Karsyn', 'Rivka', 'Adele', 'Bellamy', 'Kenna', 'Belle', 'Lailah', 'Aisling', 'Madyson', 'Malani', 'Marigold', 'Vada', 'Zainab', 'Deborah', 'Estelle', 'Jovie', 'Nell', 'Kaisley', 'Kamiyah', 'Rania', 'Susan', 'Azariah', 'Jazlynn', 'Azalea', 'Itzayana', 'Monroe', 'Paulina', 'Ayana', 'Cynthia', 'Leyla', 'Reece', 'Aubriella', 'Zaylee', 'Emmalynn', 'Bexley', 'Dorothy', 'Livia', 'Maliah', 'Zariyah', 'Analia', 'Louisa', 'Honesty', 'Lilith', 'Sunny', 'Loretta', 'Ellery', 'Francine', 'Marian', 'Yara', 'Ailani', 'Aliana', 'Lizbeth', 'Ellison', 'Winter', 'Chandler', 'Journi', 'Kaylani', 'Nathalie', 'Viola', 'Huxley', 'Maddie', 'Harriet', 'Lillyana', 'Carter', 'Zora', 'Jana', 'Estella', 'Bria', 'Colette', 'Mina', 'Elyse', 'Antonella', 'Anais', 'Cordelia', 'Mariella', 'Matilda', 'Judy', 'Opal', 'Alaya', 'Ayleen', 'Oaklyn', 'Christine', 'Claudia', 'Joselyn', 'Kadence', 'Annalise', 'Louise', 'Malayah', 'Sariah', 'Taliyah', 'Della', 'Ellison', 'Jazlene', 'Addalyn', 'Kamila', 'Nalani', 'Paloma', 'Farrah', 'Micah', 'Emilee', 'Ensley', 'Jovie', 'Kimora', 'Lilyanna', 'Zella', 'Clementine', 'Gwen', 'Jaida', 'Sandra', 'Tegan', 'Chana', 'Judith', 'Marisol', 'Mikaela', 'Milan', 'Astrid', 'Davina', 'Dulce', 'Harlee', 'Alannah', 'Aurelia', 'Blaire', 'Campbell', 'Nahla', 'Jillian', 'Katalina', 'Waverly', 'Zahra', 'Carleigh', 'Dalilah', 'Eileen', 'Maliah', 'Violette', 'Addyson', 'Amia', 'Baylor', 'Jianna', 'Nataly', 'Saniyah', 'Aryanna', 'Giavanna', 'Elliot', 'Ingrid', 'Jaida', 'Sariya', 'Amani', 'Aryana', 'Avah', 'Charity', 'Kathleen', 'Mara', 'Maryjane', 'Noor', 'Roxanne', 'Sharon', 'Alyvia', 'Cambria', 'Dariana', 'Della', 'Jordynn', 'Joslynn', 'Katrina', 'Kavya', 'Marceline', 'Vida', 'Ainhoa', 'Araceli', 'Caylee', 'Dina', 'Jazlyne', 'Jenny', 'Kaleah', 'Karmen', 'Kloey', 'Larissa', 'Maiya', 'Mireya', 'Rylynn', 'Shanaya', 'Yusra', 'Abrielle', 'Anayah', 'Anja', 'Caleigh', 'Carley', 'Doris', 'Elia', 'Ester', 'Kensley', 'Kiarra', 'Leilany', 'Marely', 'Nayla', 'Perla', 'Rey', 'Sylvie', 'Vivianne', 'Yana', 'Adamaris', 'Aislyn', 'Althea', 'Amiya', 'Anabell', 'Bree', 'Coral', 'Destinee', 'Eleanora', 'Finnley', 'Glory', 'Gwenyth', 'Harlyn', 'Kamari', 'Layan', 'Leela', 'Maddyson', 'Nelly', 'Raylee', 'Rowen', 'Taliah', 'Aalayah', 'Anayeli', 'Ayvah', 'Chelsey', 'Daleyza', 'Destiney', 'Gizelle', 'Jovanna', 'Katia', 'Kelsi', 'Laela', 'Landon', 'Maddisyn', 'Safiya', 'Sammie', 'Sanai', 'Sawyer', 'Sia', 'Sofie', 'Tamera', 'Ysabelle', 'Alara', 'Ameera', 'Analee', 'Anora', 'Auri', 'Aysia', 'Brynnley', 'Camden', 'Estefany', 'Evalyn', 'Irelyn', 'Jalyn', 'Janel', 'Janelly', 'Janya', 'Jesse', 'Kenzley', 'Khloee', 'Leyah', 'LisPOSTte', 'Londynn', 'Lynn', 'Marwa', 'Nara', 'Peyton', 'Ramsey', 'Rosalind', 'Sevyn', 'Xochitl', 'Adleigh', 'Analiyah', 'Ani', 'Anisa', 'Betsy', 'Blakeley', 'Briseis', 'Cattleya', 'Daira', 'Eira', 'Esperanza', 'Gianni', 'Ivana', 'Jahzara', 'Jalayah', 'Jasleen', 'Jeannette', 'Kamya', 'Kaylene', 'Kyah', 'Leen', 'Lyndsey', 'Maelynn', 'Mai', 'Naimah', 'Raizy', 'Safiyyah', 'Sakura', 'Saray', 'Saylor', 'Sena', 'Siyona', 'Suri', 'Taylin', 'Yanely', 'Zakiya', 'Adah');
        $domains = array('gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com', 'icloud.com', 'aol.com', 'protonmail.com', 'mail.com', 'zoho.com', 'yandex.com', 'gmx.com', 'fastmail.com', 'tutanota.com', 'startmail.com', 'inbox.com', 'mail.ru', 'rediffmail.com', 'lycos.com', 'hushmail.com', 'lavabit.com', 'runbox.com', 'rocketmail.com', 'me.com', 'live.com', 'msn.com', 'comcast.net', 'cox.net', 'att.net', 'verizon.net', 'sbcglobal.net', 'bellsouth.net', 'charter.net', 'earthlink.net', 'juno.com', 'optonline.net', 'netzero.net', 'pacbell.net', 'roadrunner.com', 'shaw.ca', 'sympatico.ca', 'telus.net', 'blueyonder.co.uk', 'ntlworld.com', 'virginmedia.com', 'talktalk.net', 'btinternet.com', 'sky.com', 'yahoo.co.uk', 'mailinator.com', 'guerrillamail.com', '10minutemail.com', 'tempmail.net', 'throwawaymail.com', 'mailnesia.com', 'mailcatch.com', 'tempinbox.com', 'fakeinbox.com', 'mailinator2.com', 'getnada.com', 'tempail.com', 'yopmail.com', 'tempmail.de', 'trash-mail.com', 'dispostable.com', 'mailinator.com', 'zippymail.info', 'spamgourmet.com', 'gishpuppy.com', 'mailnesia.com', 'mailmetrash.com', 'discard.email', 'maildrop.cc', 'mailinator.net', 'mailnull.com', 'spamex.com', 'spamavert.com', 'pookmail.com', 'sharklasers.com', 'spambox.us', 'temporarily.de', 'jetable.org', 'mailexpire.com', 'spam.la', 'spambox.com', 'tempomail.fr', 'spamhole.com', 'incognitomail.com', 'temporarioemail.com.br', 'spamfree24.org', 'mailnull.com', 'guerrillamail.net', 'tempinbox.com', 'spamgourmet.net', 'spambox.org', 'fakeinbox.com', 'spambox.us', 'spambog.com', 'discard.email', 'mytemp.email', 'yopmail.fr', 'spam4.me', 'grr.la', 'trashmail.com', 'moakt.com', 'maildrop.cc', 'mailexpire.com', 'trashmailer.com', 'spamfree24.net', 'e4ward.com', 'spamday.com', 'tempmail.de', 'mailinator2.com', 'spamgourmet.org', 'sharklasers.com', 'incognitomail.org', 'tempmail.org', 'spamex.com', 'emailondeck.com', 'trbvm.com', 'spamfree.eu', 'spambox.info', 'tempmail.it', 'bouncr.com', 'temporarily.de', 'tempsky.com', 'mailtemporaire.fr', 'spamfighter.cf', '10minutemail.co.uk', 'mailnesia.com', 'spamfree24.com', 'temporarioemail.net', 'mytrashmailer.com', 'spamgourmet.info', 'emailondeck.net', 'tempo-mail.com', 'spamgourmet.com', 'trashmail.me', 'mailinator.org', 'mailinator.us');
        $profilepic = array('img/profile_pics/default.png', 'img/profile_pics/girl 1.png', 'img/profile_pics/girl 2.png', 'img/profile_pics/girl 3.png', 'img/profile_pics/girl 4.png', 'img/profile_pics/girl 5.png', 'img/profile_pics/girl 6.png', 'img/profile_pics/girl 7.png', 'img/profile_pics/girl 8.png', 'img/profile_pics/girl 9.png', 'img/profile_pics/girl 10.png', 'img/profile_pics/girl 11.png', 'img/profile_pics/girl 12.png', 'img/profile_pics/default.png', 'img/profile_pics/guy 1.png', 'img/profile_pics/guy 2.png', 'img/profile_pics/guy 3.png', 'img/profile_pics/guy 4.png', 'img/profile_pics/guy 5.png', 'img/profile_pics/guy 6.png', 'img/profile_pics/guy 7.png', 'img/profile_pics/guy 8.png', 'img/profile_pics/guy 9.png', 'img/profile_pics/guy 10.png', 'img/profile_pics/guy 11.png', 'img/profile_pics/guy 12.png', 'img/profile_pics/default.png');

        $rand_username = $usernames[array_rand($usernames)];
        $rand_email = strtolower($rand_username. '@' . $domains[array_rand($domains)]);
        $rand_profilepic = $profilepic[array_rand($profilepic)];
        $rand_password = bin2hex(random_bytes(8));
        $hash = password_hash($rand_password, PASSWORD_DEFAULT);
        $activation_code = generateActivationCode();
        $userID = generateID();

        DB::POST(
          "users",
          array("userID", "Name", "Email", "Password", "ActivationCode", "ProfilePicture", "Activated"),
          array($userID, $rand_username, $rand_email, $hash, $activation_code, $rand_profilepic, 'Aktív')
        );
        header("Location: moderation.php");
}

function random_animal() {
  $animalid = generateID();

  $animalnames = array('Luna', 'Simba', 'Daisy', 'Max', 'Charlie', 'Rocky', 'Ginger', 'Pepper', 'Apollo', 'Oliver', 'Jasper', 'Lily', 'Bella', 'Zoe', 'Zeus', 'Lucy', 'Chloe', 'Molly', 'Bear', 'Toby', 'Harley', 'Sasha', 'Bailey', 'Sunny', 'Sable', 'Tucker', 'Duke', 'Buddy', 'Sadie', 'Roxy', 'Bentley', 'Maggie', 'Cooper', 'Lola', 'Rusty', 'Riley', 'Sammy', 'Harper', 'Milo', 'Lucky', 'Boomer', 'Jax', 'Gatsby', 'Marley', 'Chase', 'Gus', 'Oscar', 'Rosie', 'Hazel', 'Stella', 'Phoebe', 'Willow', 'Coco', 'Zelda', 'Lenny', 'Gigi', 'Finn', 'Winnie', 'Nala', 'Rafiki', 'Scar', 'Mufasa', 'Pumbaa', 'Timon', 'Kiara', 'Simone', 'Zazu', 'Kion', 'Jabari', 'Shani', 'Kovu', 'Nuka', 'Vitani', 'Shenzi', 'Banzai', 'Ed', 'Ruby', 'Sapphire', 'Onyx', 'Jade', 'Opal', 'Emerald', 'Topaz', 'Garnet', 'Pearl', 'Jasper', 'Amber', 'Ivory', 'Quartz', 'Turquoise', 'Citrine', 'Peridot', 'Agate', 'Malachite', 'Diamond', 'Cooper', 'Scout', 'Riley', 'Sammy', 'Shadow', 'Rusty', 'Misty', 'Baxter', 'Bailey', 'Biscuit', 'Boots', 'Bruno', 'Buttons', 'Candy', 'Casey', 'Chester', 'Cinnamon', 'Cleo', 'Coco', 'Cody', 'Comet', 'Cookie', 'Copper', 'Cricket', 'Crystal', 'Daisy', 'Dakota', 'Daphne', 'Dexter', 'Diesel', 'Dixie', 'Dolly', 'Dusty', 'Echo', 'Eddie', 'Eli', 'Ellie', 'Emma', 'Felix', 'Finn', 'Fiona', 'Flash', 'Fluffy', 'Frankie', 'Freddie', 'Gatsby', 'George', 'Gigi', 'Ginger', 'Goldie', 'Grace', 'Gus', 'Hank', 'Harley', 'Hazel', 'Henry', 'Holly', 'Honey', 'Hope', 'Hunter', 'Ike', 'Isabella', 'Isabelle', 'Ivy', 'Jack', 'Jackson', 'Jake', 'Jasmine', 'Jasper', 'Jax', 'Jenna', 'Jesse', 'Joey', 'Johnny', 'Josie', 'Judy', 'Julie', 'June', 'Kai', 'Katie', 'Kiki', 'King', 'Kipper', 'Koda', 'Kona', 'Lacey', 'Lady', 'Layla', 'Leo', 'Lexi', 'Lila', 'Lily', 'Lincoln', 'Loki', 'Louie', 'Lucas', 'Lulu', 'Luna', 'Mabel', 'Maddie', 'Maggie', 'Maisy', 'Mango', 'Marley', 'Marty', 'Max', 'Maxwell', 'Mia', 'Mickey', 'Midnight', 'Mila', 'Milo', 'Mimi', 'Minnie', 'Misty', 'Mocha', 'Molly', 'Moose', 'Muffin', 'Murphy', 'Nala', 'Nash', 'Nellie', 'Nemo', 'Nikki', 'Nina', 'Noah', 'Noodle', 'Norman', 'Nova', 'Oakley', 'Olive', 'Oliver', 'Onyx', 'Oreo', 'Oscar', 'Otis', 'Ozzie', 'Pablo', 'Paisley', 'Parker', 'Peaches', 'Peanut', 'Pearl', 'Pebbles', 'Penny', 'Pepper', 'Petey', 'Phoebe', 'Pickle', 'Piper', 'Pippin', 'Pixie', 'Pogo', 'Poppy', 'Porter', 'Prince', 'Princess', 'Puddles', 'Puff', 'Pumpkin', 'Queenie', 'Quincy', 'Ranger', 'Rascal', 'Raven', 'Red', 'Reese', 'Remy', 'Rex', 'Ricky', 'Riley', 'Ringo', 'River', 'Roxy', 'Ruby', 'Rudy', 'Rufus', 'Rusty', 'Sadie', 'Sage', 'Sally', 'Sam', 'Samantha', 'Sammy', 'Sandy', 'Sasha', 'Sassy', 'Scooby', 'Scooter', 'Scout', 'Scrappy', 'Shadow', 'Shelby', 'Shiloh', 'Simba', 'Simon', 'Sky', 'Skye', 'Smokey', 'Snickers', 'Snoop', 'Snow', 'Snowball', 'Socks', 'Sophie', 'Spencer', 'Spike', 'Spirit', 'Spot', 'Sprinkles', 'Squirt', 'Stella', 'Storm', 'Sugar', 'Suki', 'Summer', 'Sunny', 'Sunshine', 'Susie', 'Sydney', 'Taco', 'Taz', 'Teddy', 'Tessa', 'Thor', 'Tia', 'Tiger', 'Tilly', 'Timmy', 'Toby', 'Tommy', 'Tucker', 'Tulip', 'Turbo', 'Twiggy', 'Twilight', 'Tyson', 'Vader', 'Valentine', 'Vega', 'Velvet', 'Violet', 'Wally', 'Walter', 'Whiskey', 'Whisper', 'Willow', 'Winnie', 'Winny', 'Winston', 'Winter', 'Wrigley', 'Xander', 'Yogi', 'Yoshi', 'Yukon', 'Zara', 'Zeke', 'Zelda', 'Ziggy', 'Zoe', 'Zoey', 'Zorro', 'Zuzu', 'Ace', 'Achilles', 'Ada', 'Addie', 'Admiral', 'Agatha', 'Aiden', 'Aiko', 'Aimee', 'Ajax', 'Akira', 'Albert', 'Alex', 'Alexa', 'Alfie', 'Alice', 'Alien', 'Alvin', 'Amber', 'Amelia', 'Amigo', 'Amos', 'Angel', 'Angus', 'Annie', 'Apollo', 'April', 'Archie', 'Aria', 'Ariel', 'Aries', 'Arlo', 'Arnold', 'Arrow', 'Ash', 'Aspen', 'Astro', 'Athena', 'Atlas', 'Atticus', 'Auggie', 'August', 'Aurora', 'Autumn', 'Ava', 'Avery', 'Axel', 'Bailey', 'Bambi', 'Bandit', 'Banjo', 'Barkley', 'Barney', 'Basil', 'Baxter', 'Bear', 'Beau', 'Bella', 'Belle', 'Benji', 'Benny', 'Bentley', 'Betsy', 'Betty', 'Bianca', 'Biscuit', 'Bishop', 'Bitsy', 'Blackie', 'Blaze', 'Blue', 'Bo', 'Bob', 'Bobby', 'Bolt', 'Bonnie', 'Boo', 'Boomer', 'Boone', 'Boston', 'Bowie', 'Brady', 'Brandi', 'Brandy', 'Breeze', 'Brenda', 'Brewster', 'Bridget', 'Brindle', 'Brodie', 'Bronco', 'Brooklyn', 'Brownie', 'Bruiser', 'Bruno', 'Brutus', 'Bubba', 'Bubbles', 'Buck', 'Buddy', 'Buffy', 'Bullet', 'Bullwinkle', 'Buster', 'Butch', 'Buttercup', 'Butterfly', 'Button', 'Cactus', 'Cain', 'Cali', 'Callie', 'Camille', 'Candy', 'Canela', 'Caper', 'Captain', 'Caramel', 'Carbon', 'Carley', 'Carlton', 'Carly', 'Carmen', 'Cash', 'Casper', 'Cassie', 'Catalina', 'Cato', 'Ceasar', 'Cedar', 'Champ', 'Chance', 'Chanel', 'Chaos', 'Charlie', 'Chase', 'Chata', 'Cheech', 'Chester', 'Chewie', 'Chico', 'Chief', 'Chili', 'China', 'Chip', 'Chloe', 'Chocolate', 'Chopper', 'Chubby', 'Cinnamon', 'Cisco', 'CJ', 'Clancy', 'Clark', 'Cleo', 'Clyde', 'Cocoa', 'Cocoanut', 'Cody', 'Colby', 'Comet', 'Cookie', 'Cooper', 'Copper', 'Corky', 'Cosmo', 'Cotton', 'Cougar', 'Courtney', 'Cowboy', 'Cricket', 'Crispy', 'Crockett', 'Crystal', 'Cubby', 'Cupid', 'Curtis', 'Custer', 'Cutter', 'Daisy', 'Dallas', 'Dana', 'Dancer', 'Dandelion', 'Dante', 'Darby', 'Darcy', 'Daredevil', 'Daria', 'Darla', 'Darth', 'Dash', 'Dasher', 'Dave', 'David', 'Deacon', 'Dean', 'DeeDee');
  $rand_animalname = $animalnames[array_rand($animalnames)];

  $animalspecie = array('DEG', 'EGE', 'HAL', 'HOR', 'KAM', 'KAN', 'KIG', 'KUT', 'LEG', 'MAC', 'MEN', 'MOK', 'NYU', 'PAP', 'PAT', 'POK', 'SUN', 'TEK', 'TEM', 'TOD');
  $imgs = array('img/animal_pics/degu.png', 'img/animal_pics/mouse.png', 'img/animal_pics/fish.png', 'img/animal_pics/hamster.png', 'img/animal_pics/chameleon.png', 'img/animal_pics/canary.png', 'img/animal_pics/snake.png', 'img/animal_pics/dog.png', 'img/animal_pics/iguana.png', 'img/animal_pics/cat.png', 'img/animal_pics/weasel.png', 'img/animal_pics/squirrel.png', 'img/animal_pics/rabbit.png', 'img/animal_pics/parrot.png', 'img/animal_pics/rat.png', 'img/animal_pics/spider.png', 'img/animal_pics/hedgehog.png', 'img/animal_pics/turtle.png', 'img/animal_pics/ginnypig.png', 'img/animal_pics/pygmypig.png');
  $randnum = random_int(0,count($animalspecie));
  $rand_animalspecie = $animalspecie[$randnum];
  $animalpic = $imgs[$randnum];

  $animalsex = array('Hím', 'Nőstény');
  $rand_animalsex = $animalsex[array_rand($animalsex)];
  
  $start_timestamp = strtotime("December 31st, 1999 23:59:59");     
  $end_timestamp = strtotime("December 31st, 2023 23:59:59");
  $random_timestamp = rand($start_timestamp, $end_timestamp);
  $animalbirth = date('Y-m-d', $random_timestamp);

  $yesOrNoArr = array('Igen', 'Nem');
  $yesOrIvarArr = array('Igen', 'Ivartalanítva');
  $rand_canbaby = $yesOrIvarArr[array_rand($yesOrIvarArr)];
  $rand_houPOSTrained = $yesOrNoArr[array_rand($yesOrNoArr)];
  $rand_chipped = $yesOrNoArr[array_rand($yesOrNoArr)];
  $rand_foundhome = $yesOrNoArr[array_rand($yesOrNoArr)];
  $rand_temporaryhome = $yesOrNoArr[array_rand($yesOrNoArr)];

  $result = DB::GET("SELECT userID FROM users ORDER BY RAND() LIMIT 1",array());
  while($row = mysqli_fetch_assoc($result)) {
      $randomUserID = $row["userID"];
  }

  $now = new DateTime();
  $birthdayDate = DateTime::createFromFormat('Y-m-d', $animalbirth);
  $ageInterval = $birthdayDate->diff($now);
  $age = $ageInterval->format('%y');

  DB::POST(
    "animalinfo", 
    array("animalID","Name","Specie","Sex","DateofBorn","CanBaby","HouseTrained","Chipped","userID","TemporaryHome","Picture","Age","FoundHome"),
    array($animalid, $rand_animalname, $rand_animalspecie, $rand_animalsex, $animalbirth, $rand_canbaby, $rand_houPOSTrained, $rand_chipped, $randomUserID, $rand_temporaryhome, $animalpic, $age, $rand_foundhome)
  );
  header("Location: moderation.php");
}

// Image Upload

function imgUpload($type){
  $imageName = $_FILES[$type]['name'];
  $imageTmpName = $_FILES[$type]['tmp_name'];
  $imageSize = $_FILES[$type]['size'];
  $imageType = $_FILES[$type]['type'];

  $allowedTypes = array('image/jpeg', 'image/png');
  if (in_array($imageType, $allowedTypes)) {
      if ($imageSize <= 5000000) {
          $imageNewName = uniqid('', true) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);

          $imgPath = 'img/'.$type.'s/' . $imageNewName;
          move_uploaded_file($imageTmpName, $imgPath);
          return $imgPath;
      } else {
          echo 'Hiba: A képed mérete nagyobb, mint 5000000KB.';
      }
  } else {
      echo 'Ez a fajta fájl típus nem támogatott.';
  }
}

function unlinkpic($result,$pic){
  if ($result->num_rows > 0) {
    while($result->fetch_assoc()){
      $image_path = $row[$pic];
      if (file_exists($image_path)) {
        unlink($image_path);
      }
    }
    
  }
}

//profilok.php

function getLimit(){
  return 15;
}

function pageOffset($page,$total_pages){
  if ($page < 1) {
    $page = 1;
  } elseif ($page > $total_pages) {
      $page = $total_pages;
  }
  return ($page - 1) * getLimit();
}

function totalPages(){
  $result_count = DB::GET(
    "SELECT COUNT(*) AS total FROM users",
    array()
  );
  $row_count = mysqli_fetch_assoc($result_count);
  $total_users = $row_count['total'];
  return ceil($total_users / getLimit());
}