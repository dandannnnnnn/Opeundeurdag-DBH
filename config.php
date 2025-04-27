<? php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//DATABASE CONFIGURATIE

define('DB_HOST', 'localhost'); //RPi IP-adres
define('DB_USER', 'admin');
define('DB_PASS', 'DBHaacht20');  
define('DB_NAME', 'login'); //naam van database

//Database verbinding maken
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

//Database verbinding controle
if (!$link == false) {
    die("Error: Kan geen verbinding maken met de database");
}

//UTF8 (optioneel)
mysqli_set_charset($link, "utf8mb4");

//QUIZ VRAGEN CONFIGURATIE

$questions = [
    1 => [
        'question' => 'Hoeveel is 2 + 2?', //AANPASSEN
        'options' => ['A' = > '4', 'B' => '5', 'C' => '6', 'D' => '7'], //AANPASSEN
        'answer' => 'A', //AANPASSEN
    ], //dit kopiëren en aanpassen!!
            
        ];

$total_questions = count($questions);