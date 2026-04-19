<?php
$conn = mysqli_connect("localhost","root","","waterpark_db");

if(!$conn)
{
die("Connection failed");
}

session_start();
?>