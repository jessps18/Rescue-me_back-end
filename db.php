<?php

$conn = new mysqli("localhost", "root", "root", "rescueme");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}