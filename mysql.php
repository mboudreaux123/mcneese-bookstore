<?php

$servername = "localhost";
$username = "bookstore";
$password = "bookstore";

$connection = new mysqli($servername, $username, $password);

if($connection -> connect_error) 
{
    die("Connection failed: " . $connection -> connect_error);
}

?> 
