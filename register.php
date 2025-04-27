<?php
require_once 'config.php'; // Bevat DB connectie en session_start()

// Controleer of het formulier is verzonden
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Haal gegevens op en trim spaties
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);

    // Basis validatie
    if (empty($first_name) || empty($last_name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Vul alle velden correct in aub.";
        header("location: index.php");
        exit;
    }

    // Beveilig tegen SQL-injectie (essentieel!)
    $safe_first_name = mysqli_real_escape_string($link, $first_name);
    $safe_last_name = mysqli_real_escape_string($link, $last_name);
    $safe_email = mysqli_real_escape_string($link, $email);

    // Bereid SQL statement voor (nog veiliger met prepared statements)
    $sql = "INSERT INTO participants (first_name, last_name, email) VALUES (?, ?, ?)";

    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variabelen aan de prepared statement als parameters
        mysqli_stmt_bind_param($stmt, "sss", $param_first_name, $param_last_name, $param_email);

        // Stel parameters in
        $param_first_name = $safe_first_name; // Gebruik hier de originele getrimde, niet de escaped versie voor de parameter
        $param_last_name = $safe_last_name;
        $param_email = $safe_email;

        // Probeer het statement uit te voeren
        if(mysqli_stmt_execute($stmt)){
            // Registratie succesvol. Haal de ID van de nieuwe deelnemer op.
            $participant_id = mysqli_insert_id($link);

            // Sla participant ID en startgegevens op in de sessie
            $_SESSION['participant_id'] = $participant_id;
            $_SESSION['first_name'] = $first_name; // Handig voor weergave
            $_SESSION['current_question'] = 1; // Start bij vraag 1
            $_SESSION['score'] = 0;            // Startscore is 0
            unset($_SESSION['quiz_completed']); // Zorg dat eventuele oude status weg is

            // Sluit statement en connectie
            mysqli_stmt_close($stmt);
            mysqli_close($link);

            // Stuur door naar de eerste vraag
            header("location: quiz.php");
            exit;
        } else{
             $_SESSION['error'] = "Oeps! Er ging iets mis bij het registreren. Probeer opnieuw.";
             error_log("MySQL Execute Error: " . mysqli_stmt_error($stmt)); // Log de fout voor jezelf
             mysqli_stmt_close($stmt);
        }
    } else {
         $_SESSION['error'] = "Oeps! Er ging iets mis. Probeer opnieuw.";
         error_log("MySQL Prepare Error: " . mysqli_error($link)); // Log de fout voor jezelf
    }

    mysqli_close($link);
    header("location: index.php"); // Terug naar registratie bij fout
    exit;

} else {
    // Als iemand direct naar register.php surft
    header("location: index.php");
    exit;
}
?>