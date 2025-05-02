<<?php
session_start(); // Starts a new session or resumes the current session

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Checks if the request method is POST
    $username = $_POST['username']; // Retrieves the username from the POST data
    $password = $_POST['password']; // Retrieves the password from the POST data

    $host = "localhost"; // Database host
    $db_password = ""; // Database password
    $db_username = "root"; // Database username
    $dbname = "ronohdan"; // Database name

    $conn = new mysqli($host, $db_username, $db_password, $dbname); // Creates a new connection to the database
    if ($conn->connect_error) { // Checks if there is a connection error
        die("Connection failed: " . $conn->connect_error); // If there is a connection error, terminate the script with an error message
    }

    $query = "SELECT id, password FROM dann WHERE username = ?"; // SQL query to select the user's ID and password from the database where the username matches
    $stmt = $conn->prepare($query); // Prepares the SQL query

    if ($stmt) { // Checks if the statement was successfully prepared
        $stmt->bind_param("s", $username); // Binds the username parameter to the SQL query
        $stmt->execute(); // Executes the SQL query
        $stmt->store_result(); // Stores the result of the query

        if ($stmt->num_rows == 1) { // Checks if exactly one row was returned
            $stmt->bind_result($user_id, $hashed_password); // Binds the result variables
            $stmt->fetch(); // Fetches the result

            if (password_verify($password, $hashed_password)) { // Verifies the password
                session_regenerate_id(true); // Regenerates the session ID to prevent session fixation attacks
                $_SESSION['user_id'] = $user_id; // Stores the user ID in the session

                $cookieParams = session_get_cookie_params(); // Gets the session cookie parameters
                setcookie(
                    session_name(), // The name of the session
                    session_id(), // The session ID
                    time() + 3600, // The expiration time of the cookie (1 hour from now)
                    $cookieParams["path"], // The path where the cookie is available
                    $cookieParams["domain"], // The domain where the cookie is available
                    true, // Whether the cookie should only be sent over secure connections
                    true // Whether the cookie is accessible only through the HTTP protocol
                );

                header("Location: log.php"); // Redirects the user to the log.php page
                exit; // Stops further execution of the script
            } else {
                header("Location: error.html"); // Redirects the user to the error.html page if the password is incorrect
                exit; // Stops further execution of the script
            }
        } else {
            header("Location: error.html"); // Redirects the user to the error.html page if the username is not found
            exit; // Stops further execution of the script
        }
        $stmt->close(); // Closes the statement
    } else {
        die("Prepare failed: " . $conn->error); // If the statement preparation failed, terminate the script with an error message
    }
    $conn->close(); // Closes the database connection
}
?>
