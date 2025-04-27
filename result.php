<?php
require_once 'config.php';

//Controle quiz klaar bij de gebruiker
if (!isset($_SESSION['participant_id']) || !isset($_SESSION['quiz_completed'])) {
    header("Location: index.php");
    exit;
}

$first_name = isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : 'Deelnemer';
$final_score = isset($_SESSION['score']) ? $_SESSION['score'] : 0;

//Sessie opruimen nadat gebruiker klaar is met de quiz
session_unset();
session_destroy();