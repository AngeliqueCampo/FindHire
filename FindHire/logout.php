<?php
session_start();

if (isset($_POST['logout'])) {
    // destroy session and redirect
    session_unset();
    session_destroy();
    header("Location: index.php");  // redirect to login
    exit();
}

?>