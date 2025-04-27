<?php //registratie pagina
require_once 'config.php'; //Start sessie

//Gebruiker nog niet klaar met quiz
if (isset($_SESSION['participant_id']) && isset($_SESSION['current_question'])) {
    header("location: result.php");
    exit;
}

//Gebruiker klaar met quiz ==> resultaten weergeven
if (isset($_SESSION['quiz_completed'])) {
    header("location: result.php");
    exit;
}

$error_message = '';
if(isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']); //foutmelding verwijderd na het tonen
}