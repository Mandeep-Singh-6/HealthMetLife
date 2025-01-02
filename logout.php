<?php
    //Start the session 
    session_start(); 
    // Unset all session variables 
    session_unset(); 
    // Destroy the session data on the server 
    session_destroy(); 
    // Delete the session cookie 
    if (ini_get("session.use_cookies")) { 
        $params = session_get_cookie_params(); 
        setcookie(session_name(), '', time() - 42000); // $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
    }
    header("Location: /wd2/Final%20Project/HealthMetLife%20-%20Improved/index.php");
?>