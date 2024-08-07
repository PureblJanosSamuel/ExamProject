<?php
    session_start();

    include("functions.php");
    $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    $user_data = check_login($con);
    $user_id = $_SESSION['userID'];
    $animal_id = $_GET['animal_id'];

    $sql = "SELECT Picture FROM animalinfo WHERE userID = '$user_id' AND animalID = '$animal_id' AND Picture != 'img/animal_pics/default.png'";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
    $image_path = $row['Picture'];
    if (file_exists($image_path)) {
        unlink($image_path);
        }
    }

    $sql = "DELETE FROM animalinfo WHERE userID = '$user_id' AND animalID = '$animal_id'";
    if (mysqli_query($con, $sql)) {
        header('Location: hirdeteseid.php?allat_sikeresen_torolve');
    }
?>