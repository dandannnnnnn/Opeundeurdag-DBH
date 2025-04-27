<?php
require_once 'config.php'; // Bevat DB connectie, session_start(), $questions, $total_questions

// Controleer of gebruiker geregistreerd is en bezig is
if (!isset($_SESSION['participant_id']) || !isset($_SESSION['current_question'])) {
     if (isset($_SESSION['quiz_completed'])) {
        header("location: result.php");
    } else {
        header("location: index.php");
    }
    exit;
}

// Controleer of er een antwoord is ingestuurd
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['answer'])) {

    $current_q_number = $_SESSION['current_question'];
    $submitted_answer = $_POST['answer'];

    // Controleer of de vraag bestaat
    if (!isset($questions[$current_q_number])) {
        // Fout
        session_destroy();
        header("location: index.php");
        exit;
    }

    // Haal het correcte antwoord op
    $correct_answer = $questions[$current_q_number]['correct'];

    // Controleer of het antwoord correct is en update de score in de sessie
    if ($submitted_answer === $correct_answer) {
        $_SESSION['score']++;
    }

    // Ga naar de volgende vraag
    $_SESSION['current_question']++;

    // Controleer of de quiz voorbij is
    if ($_SESSION['current_question'] > $total_questions) {
        // Quiz is klaar! Sla de finale score op in de database.
        $final_score = $_SESSION['score'];
        $participant_id = $_SESSION['participant_id'];

        // Update score in DB (gebruik prepared statement)
        $sql = "UPDATE participants SET score = ? WHERE id = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ii", $param_score, $param_id);

            $param_score = $final_score;
            $param_id = $participant_id;

            if(!mysqli_stmt_execute($stmt)){
                // Fout bij het opslaan van de score, log dit!
                error_log("MySQL Error updating score for ID $participant_id: " . mysqli_stmt_error($stmt));
                // De quiz gaat toch door naar de resultaten pagina
            }
             mysqli_stmt_close($stmt);
        } else {
             error_log("MySQL Prepare Error updating score: " . mysqli_error($link));
        }
        mysqli_close($link);

        // Markeer quiz als voltooid en stuur naar resultatenpagina
        $_SESSION['quiz_completed'] = true;
        unset($_SESSION['current_question']); // Huidige vraag is niet meer relevant
        header("location: result.php");
        exit;

    } else {
        // Nog niet klaar, ga naar de volgende vraag
        mysqli_close($link);
        header("location: quiz.php");
        exit;
    }

} else {
    // Geen antwoord ingediend, stuur terug naar de huidige vraag
    // Dit kan gebeuren als iemand de pagina vernieuwt of direct navigeert
    header("location: quiz.php");
    exit;
}
?>