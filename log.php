<<?php
    session_start(); // Starts a new session or resumes the current session

    if(!isset($_SESSION['username'])){ // Checks if the 'username' session variable is not set
        header("Location: log.html"); // Redirects the user to the login page (log.html)
        exit(); // Stops further execution of the script
    }
?>
