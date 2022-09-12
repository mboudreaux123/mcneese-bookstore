<?php
    session_start();
    unset($_SESSION['Loggedin']);
    unset($_SESSION['StudentID']);
    unset($_SESSION['Username']);
    header("Location:index.php");
?>