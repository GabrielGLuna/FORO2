<?php

$server = 'localhost';
$user = 'root';
$pass = '';
$db = 'foro_db_v1';

$connection = new mysqli($server, $user, $pass, $db);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}else{
    echo"Conexión exitosa";
}

?>