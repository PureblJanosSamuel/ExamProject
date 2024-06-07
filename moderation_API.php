<?php

if (isset($_GET['user_ID'])) {
    removeUser( $_GET['user_ID']);
};

if (isset($_GET['animal_ID'])) {
    removeAnimal( $_GET['animal_ID']);
};

if (isset($_GET['remove_bugreport_id'])) {
    removeBugReport($_GET['remove_bugreport_id']);
};
?>