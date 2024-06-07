<?php
  include("functions.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        if($_POST['action'] == 'create_random_user'){
            random_user();
        }
        if($_POST['action'] == 'create_random_animal'){
            random_animal();
        }
    }

    if (isset($_GET['user_ID'])) {
        removeUser($_GET['user_ID']);
    }

    if (isset($_GET['animal_ID'])) {
        removeAnimal($_GET['animal_ID']);
    }

    if (isset($_GET['role_user_ID'])) {
        removeUserRole($_GET['role_user_ID']);
    }

    if (isset($_GET['remove_bugreport_id'])) {
        removeBugReport($_GET['remove_bugreport_id']);
    }

    

?>