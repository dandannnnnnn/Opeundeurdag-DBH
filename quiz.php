<?php
require_once 'config.php'; // Bevat DB connectie, session_start(), $questions, $total_questions

// Controleer of gebruiker geregistreerd is en bezig is
if (!isset($_SESSION['participant_id']) || !isset($_SESSION['current_question'])) {
    // Niet ingelogd of quiz al klaar? Stuur naar begin.
    if (isset($_SESSION['quiz_completed'])) {
        header("location: result.php");
    } else {
        header("location: index.php");
    }
    exit; // Stop script uitvoering na redirect
}

$current_q_number = $_SESSION['current_question'];

// Controleer of de vraag bestaat (zou niet mogen misgaan, maar toch)
if (!isset($questions[$current_q_number])) {
    // Fout: ongeldige vraag. Stuur naar resultaten of begin.
    // Misschien is de quiz net afgerond in een ander tabblad?

    session_destroy(); // Reset bij fout
    header("location: index.php");
    exit; // Stop script uitvoering na redirect
}

$question_data = $questions[$current_q_number];
?>